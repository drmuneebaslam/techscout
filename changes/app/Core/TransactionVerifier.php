<?php
declare(strict_types=1);

namespace App\Core;

/**
 * TransactionVerifier: Multi-Blockchain On-Chain Payment Verification Service
 *
 * Verifies transaction hashes (TXIDs) on public explorers across Bitcoin,
 * Ethereum (ERC-20 USDT), Tron (TRC-20 USDT), and Solana.
 */
class TransactionVerifier {

    /**
     * Validate the syntactic format of a TXID based on coin type.
     *
     * @param string $coin BTC, ETH, USDT, SOL
     * @param string $txid
     * @return bool
     */
    public static function validateTxidFormat(string $coin, string $txid): bool {
        $txid = trim($txid);
        $coin = strtoupper(trim($coin));

        switch ($coin) {
            case 'BTC':
                // Bitcoin TXID: 64 hexadecimal characters
                return (bool)preg_match('/^[a-fA-F0-9]{64}$/', $txid);

            case 'ETH':
            case 'USDT_ERC20':
                // Ethereum / ERC-20: 0x followed by 64 hex characters (or raw 64 hex)
                return (bool)preg_match('/^(0x)?[a-fA-F0-9]{64}$/', $txid);

            case 'USDT':
            case 'USDT_TRC20':
            case 'TRX':
                // Tron / TRC-20 or standard hex: 64 hex characters (with optional 0x)
                return (bool)preg_match('/^(0x)?[a-fA-F0-9]{64}$/', $txid);

            case 'SOL':
                // Solana signature: Base58 string, typically 86 to 88 characters
                return (bool)preg_match('/^[1-9A-HJ-NP-Za-km-z]{64,128}$/', $txid);

            default:
                // General fallback: min 32 alphanumeric chars
                return strlen($txid) >= 24 && (bool)preg_match('/^[a-zA-Z0-9_\-]+$/', $txid);
        }
    }

    /**
     * Get the public blockchain explorer URL for a given transaction.
     *
     * @param string $coin
     * @param string $txid
     * @return string
     */
    public static function getExplorerUrl(string $coin, string $txid): string {
        $coin = strtoupper(trim($coin));
        $cleanTxid = trim($txid);

        switch ($coin) {
            case 'BTC':
                return 'https://mempool.space/tx/' . urlencode($cleanTxid);

            case 'ETH':
                return 'https://etherscan.io/tx/' . (str_starts_with($cleanTxid, '0x') ? $cleanTxid : '0x' . $cleanTxid);

            case 'USDT':
                // Detect if Tron TRC-20 or Ethereum ERC-20
                if (str_starts_with($cleanTxid, '0x')) {
                    return 'https://etherscan.io/tx/' . urlencode($cleanTxid);
                }
                return 'https://tronscan.org/#/transaction/' . urlencode($cleanTxid);

            case 'SOL':
                return 'https://solscan.io/tx/' . urlencode($cleanTxid);

            default:
                return 'https://blockchair.com/search?q=' . urlencode($cleanTxid);
        }
    }

    /**
     * Verify a transaction hash live on-chain.
     *
     * @param string $coin
     * @param string $txid
     * @param float $expectedAmountUsd Optional expected payment amount
     * @return array
     */
    public static function verify(string $coin, string $txid, float $expectedAmountUsd = 0.0): array {
        $coin = strtoupper(trim($coin));
        $txid = trim($txid);

        if ($txid === '') {
            return [
                'verified' => false,
                'status' => 'missing_txid',
                'coin' => $coin,
                'txid' => '',
                'message' => 'Transaction hash (TXID) is empty.',
                'explorer_url' => '#'
            ];
        }

        $isValidFormat = self::validateTxidFormat($coin, $txid);
        $explorerUrl = self::getExplorerUrl($coin, $txid);

        if (!$isValidFormat) {
            return [
                'verified' => false,
                'status' => 'invalid_format',
                'coin' => $coin,
                'txid' => $txid,
                'message' => "The provided transaction hash does not match the standard {$coin} blockchain hash format.",
                'explorer_url' => $explorerUrl
            ];
        }

        // Execute live on-chain interrogation
        try {
            $result = match ($coin) {
                'BTC' => self::verifyBtc($txid, $expectedAmountUsd),
                'ETH' => self::verifyEth($txid, $expectedAmountUsd),
                'SOL' => self::verifySol($txid, $expectedAmountUsd),
                default => self::verifyUsdt($txid, $expectedAmountUsd),
            };
            $result['valid_format'] = true;
            return $result;
        } catch (\Throwable $e) {
            return [
                'verified' => false,
                'valid_format' => true,
                'status' => 'query_error',
                'coin' => $coin,
                'txid' => $txid,
                'message' => 'On-chain verification query could not be completed at this moment: ' . $e->getMessage(),
                'explorer_url' => $explorerUrl
            ];
        }
    }

    // ------------------------------------------------------------------------
    // NETWORK QUERY WORKERS
    // ------------------------------------------------------------------------

    private static function verifyBtc(string $txid, float $expectedAmountUsd): array {
        $explorerUrl = 'https://mempool.space/tx/' . urlencode($txid);
        $apiUrl = 'https://mempool.space/api/tx/' . urlencode($txid);

        $json = self::httpGet($apiUrl, 6);
        if (!$json) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'BTC',
                'txid' => $txid,
                'message' => 'Transaction not found in Bitcoin Mempool or blocks. Please ensure the transfer was broadcasted.',
                'explorer_url' => $explorerUrl
            ];
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return [
                'verified' => false,
                'status' => 'unparseable',
                'coin' => 'BTC',
                'txid' => $txid,
                'message' => 'Unexpected payload from Bitcoin node.',
                'explorer_url' => $explorerUrl
            ];
        }

        $confirmed = !empty($data['status']['confirmed']);
        $blockHeight = (int)($data['status']['block_height'] ?? 0);
        $blockTime = !empty($data['status']['block_time']) ? date('Y-m-d H:i:s', $data['status']['block_time']) : null;
        $feeSat = (int)($data['fee'] ?? 0);

        // Check outputs for platform treasury wallet
        $platformBtcWallet = strtolower(trim((string)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'platform_wallet_btc'")['key_value'] ?? '')));
        $matchedWallet = false;
        $receivedSat = 0;

        if (!empty($data['vout']) && is_array($data['vout'])) {
            foreach ($data['vout'] as $out) {
                $addr = strtolower((string)($out['scriptpubkey_address'] ?? ''));
                if (!empty($platformBtcWallet) && $addr === $platformBtcWallet) {
                    $matchedWallet = true;
                    $receivedSat = (int)($out['value'] ?? 0);
                    break;
                }
            }
        }

        $receivedBtc = $receivedSat / 100000000;

        return [
            'verified' => $confirmed,
            'status' => $confirmed ? 'confirmed' : 'pending_mempool',
            'coin' => 'BTC',
            'txid' => $txid,
            'block_height' => $blockHeight,
            'block_time' => $blockTime,
            'fee_sat' => $feeSat,
            'matched_treasury_wallet' => $matchedWallet,
            'received_amount' => $receivedBtc > 0 ? "{$receivedBtc} BTC" : null,
            'message' => $confirmed 
                ? "Transaction confirmed on Bitcoin blockchain at block #{$blockHeight}."
                : "Transaction found in Bitcoin Mempool (0 confirmations — pending mining).",
            'explorer_url' => $explorerUrl
        ];
    }

    private static function verifyEth(string $txid, float $expectedAmountUsd): array {
        $cleanTxid = str_starts_with($txid, '0x') ? $txid : '0x' . $txid;
        $explorerUrl = 'https://etherscan.io/tx/' . urlencode($cleanTxid);

        // Query Blockchair Ethereum API
        $apiUrl = 'https://api.blockchair.com/ethereum/dashboards/transaction/' . urlencode($cleanTxid);
        $json = self::httpGet($apiUrl, 6);

        if (!$json) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'ETH',
                'txid' => $cleanTxid,
                'message' => 'Transaction not found on Ethereum network. Check TXID or wait for block propagation.',
                'explorer_url' => $explorerUrl
            ];
        }

        $data = json_decode($json, true);
        $txData = $data['data'][$cleanTxid]['transaction'] ?? null;

        if (!$txData) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'ETH',
                'txid' => $cleanTxid,
                'message' => 'Ethereum transaction hash not indexed yet.',
                'explorer_url' => $explorerUrl
            ];
        }

        $blockId = (int)($txData['block_id'] ?? 0);
        $confirmed = ($blockId > 0);
        $time = !empty($txData['time']) ? $txData['time'] : date('Y-m-d H:i:s');
        $failed = !empty($txData['failed']);

        return [
            'verified' => $confirmed && !$failed,
            'status' => $failed ? 'failed' : ($confirmed ? 'confirmed' : 'pending'),
            'coin' => 'ETH',
            'txid' => $cleanTxid,
            'block_height' => $blockId,
            'block_time' => $time,
            'message' => $failed ? 'Transaction failed on Ethereum blockchain.' : ($confirmed ? "Confirmed on Ethereum (Block #{$blockId})." : "Transaction pending in Ethereum mempool."),
            'explorer_url' => $explorerUrl
        ];
    }

    private static function verifyUsdt(string $txid, float $expectedAmountUsd): array {
        $cleanTxid = trim($txid);
        $isEthHex = str_starts_with($cleanTxid, '0x');

        if ($isEthHex) {
            return self::verifyEth($cleanTxid, $expectedAmountUsd);
        }

        // Tron TRC-20 Verification via Tronscan API
        $explorerUrl = 'https://tronscan.org/#/transaction/' . urlencode($cleanTxid);
        $apiUrl = 'https://apilist.tronscanapi.com/api/transaction-info?hash=' . urlencode($cleanTxid);

        $json = self::httpGet($apiUrl, 6);
        if (!$json) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'USDT',
                'txid' => $cleanTxid,
                'message' => 'Transaction not found on Tron network. Ensure correct TRC-20 hash.',
                'explorer_url' => $explorerUrl
            ];
        }

        $data = json_decode($json, true);
        $confirmed = !empty($data['confirmed']);
        $contractRet = $data['contractRet'] ?? '';
        $isSuccess = ($contractRet === 'SUCCESS');
        $block = (int)($data['block'] ?? 0);
        $timestamp = !empty($data['timestamp']) ? date('Y-m-d H:i:s', (int)($data['timestamp'] / 1000)) : date('Y-m-d H:i:s');

        // Check transfer details if available
        $amount = null;
        if (!empty($data['trc20TransferInfo'][0]['amount_str'])) {
            $rawAmt = (float)$data['trc20TransferInfo'][0]['amount_str'];
            $decimals = (int)($data['trc20TransferInfo'][0]['decimals'] ?? 6);
            $amount = ($rawAmt / pow(10, $decimals)) . ' USDT';
        }

        $isVerified = $confirmed && $isSuccess;

        return [
            'verified' => $isVerified,
            'status' => $isVerified ? 'confirmed' : ($confirmed ? 'failed_contract' : 'pending'),
            'coin' => 'USDT (TRC-20)',
            'txid' => $cleanTxid,
            'block_height' => $block,
            'block_time' => $timestamp,
            'received_amount' => $amount,
            'message' => $isVerified 
                ? "Transaction confirmed on Tron blockchain (Block #{$block})." 
                : ($isSuccess ? "Transaction pending on Tron network." : "Transaction contract execution failed."),
            'explorer_url' => $explorerUrl
        ];
    }

    private static function verifySol(string $txid, float $expectedAmountUsd): array {
        $cleanTxid = trim($txid);
        $explorerUrl = 'https://solscan.io/tx/' . urlencode($cleanTxid);

        // Query public Solana RPC endpoint
        $rpcUrl = 'https://api.mainnet-beta.solana.com';
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'getSignatureStatuses',
            'params' => [[$cleanTxid], ['searchTransactionHistory' => true]]
        ]);

        $ch = curl_init($rpcUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        if (!$res) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'SOL',
                'txid' => $cleanTxid,
                'message' => 'Transaction not found on Solana blockchain.',
                'explorer_url' => $explorerUrl
            ];
        }

        $data = json_decode($res, true);
        $statusObj = $data['result']['value'][0] ?? null;

        if (!$statusObj) {
            return [
                'verified' => false,
                'status' => 'not_found',
                'coin' => 'SOL',
                'txid' => $cleanTxid,
                'message' => 'Solana transaction signature not found or unconfirmed.',
                'explorer_url' => $explorerUrl
            ];
        }

        $confirmations = $statusObj['confirmations'] ?? 0;
        $confirmationStatus = $statusObj['confirmationStatus'] ?? 'unknown';
        $err = $statusObj['err'] ?? null;
        $slot = (int)($statusObj['slot'] ?? 0);

        $isFinalized = ($confirmationStatus === 'finalized' || $confirmationStatus === 'confirmed') && empty($err);

        return [
            'verified' => $isFinalized,
            'status' => !empty($err) ? 'failed' : ($isFinalized ? 'confirmed' : 'pending'),
            'coin' => 'SOL',
            'txid' => $cleanTxid,
            'block_height' => $slot,
            'confirmations' => $confirmations,
            'confirmation_status' => $confirmationStatus,
            'message' => $isFinalized 
                ? "Transaction finalized on Solana blockchain (Slot #{$slot})."
                : (!empty($err) ? "Solana transaction failed." : "Transaction status: {$confirmationStatus}."),
            'explorer_url' => $explorerUrl
        ];
    }

    private static function httpGet(string $url, int $timeout = 5): ?string {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'User-Agent: TechScout-Tx-Verifier/2.0'
            ]);
            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code >= 200 && $code < 300 && is_string($response) && strlen($response) > 0) {
                return $response;
            }
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeout,
                'header' => "Accept: application/json\r\nUser-Agent: TechScout-Tx-Verifier/2.0\r\n"
            ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);

        $res = @file_get_contents($url, false, $ctx);
        return is_string($res) && strlen($res) > 0 ? $res : null;
    }
}
