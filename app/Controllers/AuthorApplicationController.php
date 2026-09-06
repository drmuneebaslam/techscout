<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Sanitizer;

class AuthorApplicationController {

    public function showForm(Request $request): void {
        if (!Auth::check()) {
            Session::flash('info', 'Please sign in or create an account to submit an Author application.');
            Response::redirect('/auth/login?redirect=' . urlencode('/author/apply'));
            return;
        }

        $userId = (int)Auth::id();
        $user = Auth::user();
        
        // Fetch existing application if any
        $application = Database::fetch("SELECT * FROM author_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1", [$userId]);
        $authorRecord = Database::fetch("SELECT * FROM authors WHERE user_id = ?", [$userId]);
        $topics = Database::query("SELECT * FROM topics ORDER BY sort_order ASC, name ASC");

        Response::view('public/author_apply', [
            'title' => 'Apply to Become a Verified Author & Creator | TechScout',
            'user' => $user,
            'application' => $application,
            'authorRecord' => $authorRecord,
            'topics' => $topics
        ]);
    }

    public function submit(Request $request): void {
        if (!Auth::check()) {
            Response::redirect('/auth/login');
            return;
        }

        $userId = (int)Auth::id();
        $displayName = trim((string)$request->post('display_name', ''));
        $headline = trim((string)$request->post('headline', ''));
        $bio = trim((string)$request->post('bio', ''));
        $portfolioUrl = trim((string)$request->post('portfolio_url', ''));
        $githubHandle = trim((string)$request->post('github_handle', ''));
        $twitterHandle = trim((string)$request->post('twitter_handle', ''));
        $linkedinUrl = trim((string)$request->post('linkedin_url', ''));
        $specializations = $request->post('specializations');
        $specializationsStr = is_array($specializations) ? implode(', ', $specializations) : trim((string)$specializations);
        
        $topicInterests = $request->post('topic_interests');
        $topicInterestsJson = is_array($topicInterests) ? json_encode($topicInterests) : null;

        $samplePitch = trim((string)$request->post('sample_pitch', ''));

        $requestedPrompts = $request->post('requested_prompts') ? 1 : 0;
        $requestedGpts = $request->post('requested_gpts') ? 1 : 0;
        $requestedTools = $request->post('requested_tools') ? 1 : 0;

        // Check for optional sample docx file upload
        $sampleFileUrl = null;
        if (!empty($_FILES['sample_file']) && $_FILES['sample_file']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['sample_file'];
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['docx', 'doc', 'pdf', 'md', 'txt'], true)) {
                $targetDir = dirname(__DIR__, 2) . '/assets/uploads/applications';
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $cleanFilename = 'app_' . $userId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $targetPath = $targetDir . '/' . $cleanFilename;
                if (move_uploaded_file($f['tmp_name'], $targetPath)) {
                    $sampleFileUrl = \App\Core\Config::url('/assets/uploads/applications/' . $cleanFilename);
                }
            }
        }

        if (empty($displayName) || empty($headline) || empty($bio) || (empty($samplePitch) && empty($sampleFileUrl))) {
            Session::flash('error', 'Please fill in all required fields (Display Name, Headline, Bio, and Sample Pitch/Document).');
            Response::redirect('/author/apply');
            return;
        }

        // Check if an application already exists
        $existing = Database::fetch("SELECT id, sample_file_url FROM author_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1", [$userId]);
        if (!$sampleFileUrl && $existing && !empty($existing['sample_file_url'])) {
            $sampleFileUrl = $existing['sample_file_url'];
        }

        if ($existing) {
            Database::execute("UPDATE author_applications SET 
                display_name = ?, headline = ?, bio = ?, portfolio_url = ?, github_handle = ?, twitter_handle = ?, linkedin_url = ?, specializations = ?, topic_interests = ?, sample_pitch = ?, sample_file_url = ?, requested_prompts = ?, requested_gpts = ?, requested_tools = ?, status = 'pending', updated_at = NOW()
                WHERE id = ?", [
                $displayName, $headline, $bio, $portfolioUrl, $githubHandle, $twitterHandle, $linkedinUrl, $specializationsStr, $topicInterestsJson, $samplePitch, $sampleFileUrl, $requestedPrompts, $requestedGpts, $requestedTools, $existing['id']
            ]);
        } else {
            Database::execute("INSERT INTO author_applications 
                (user_id, display_name, headline, bio, portfolio_url, github_handle, twitter_handle, linkedin_url, specializations, topic_interests, sample_pitch, sample_file_url, requested_prompts, requested_gpts, requested_tools, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')", [
                $userId, $displayName, $headline, $bio, $portfolioUrl, $githubHandle, $twitterHandle, $linkedinUrl, $specializationsStr, $topicInterestsJson, $samplePitch, $sampleFileUrl, $requestedPrompts, $requestedGpts, $requestedTools
            ]);
        }

        // Log audit event
        Database::execute("INSERT INTO audit_logs (user_id, action, target_type, target_id, details) VALUES (?, 'AUTHOR_APPLICATION_SUBMITTED', 'author_application', ?, ?)", [
            $userId, $userId, json_encode(['display_name' => $displayName, 'headline' => $headline])
        ]);

        Session::flash('success', 'Your Author Application has been submitted to the TechScout Editorial Board! We will review your portfolio and sample document within 24 hours. 🚀');
        Response::redirect('/author/apply');
    }
}
