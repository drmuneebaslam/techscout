$ftpServer = "ftpupload.net"
$ftpUser = "if0_40102772"
$ftpPass = "Fp9QFWfj5fm"
$baseRemoteDir = "/htdocs/"
$localBaseDir = "c:\xampp\htdocs\techscout.n\changes"

$files = Get-ChildItem -Path $localBaseDir -File -Recurse
foreach ($file in $files) {
    $relativePath = $file.FullName.Substring($localBaseDir.Length + 1).Replace('\', '/')
    $remotePath = "ftp://$ftpServer$baseRemoteDir$relativePath"
    
    Write-Host "Uploading $relativePath..."
    $uri = New-Object System.Uri($remotePath)
    $webClient = New-Object System.Net.WebClient
    $webClient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    # Try to create directories if needed
    $parentDir = $remotePath.Substring(0, $remotePath.LastIndexOf('/'))
    if ($parentDir -ne "ftp://$ftpServer/htdocs") {
        try {
            $ftpReq = [System.Net.FtpWebRequest]::Create($parentDir)
            $ftpReq.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
            $ftpReq.Credentials = $webClient.Credentials
            $ftpReq.GetResponse().Close()
        } catch {}
    }

    try {
        $webClient.UploadFile($uri, $file.FullName)
        Write-Host "Success: $relativePath"
    } catch {
        Write-Host "Error uploading $relativePath : $_"
    }
}
