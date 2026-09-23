# TechScout Project Rules & Mandatory Workflow

Always follow this mandatory workflow for every prompt and code modification in this project:

1. **Clean `changes/` Folder First**:
   Before making any edits or code changes, delete all existing files and subdirectories inside the `changes/` folder.

2. **Review Mistakes Document & Verify All Changes**:
   Review the mistakes document (`docs/MISTAKES_LEARNED.md`) and analyze all changed files, then verify against all documented mistakes. No mistake should ever be repeated. If any mistake is detected or repeated, correct it immediately and re-verify against all mistakes before proceeding.

3. **Preserve Dual Themes (Dark & Light) & Avoid Inline Tag Styles for Web UI**:
   - Web application components, containers, modals, sidebars, headers, and editor chrome must strictly respect the platform's theme system (dark mode by default, light mode under `[data-theme="light"]` / `.theme-light`).
   - Do NOT use hardcoded inline `style="..."` colors on individual HTML tags for web UI components. Define classes in CSS files or structured stylesheet blocks with explicit rules for both dark mode and light mode.
   - For email templates and template studios: ONLY the inner email template HTML (the preview card canvas and the actual email payload) is light/white. The surrounding web page, studio toolbars, controls, sidebars, and modals must honor the platform's dark/light theme.

4. **Apply Project Modifications**:
   Implement all requested edits, feature additions, or bug fixes across the project codebase following all quality standards.

5. **Copy Only Core Project Source Files to `changes/`**:
   - Copy ONLY newly modified or created files from actual project-related folders (e.g., `app/`, `views/`, `assets/`, `database/`, `public/`, `docs/`, `index.php`, `.htaccess`, etc.).
   - DO NOT copy scratch, waste, temporary, skill, or system configuration files (e.g., `scratch/`, `waste/`, `skills/`, `.system_generated/`, etc.) into `changes/`.
   - Preserve exact relative directory structure for all copied project files.

6. **Git Commit & Push Every Change**:
   Execute a `git commit` detailing each and every change made in the commit message, and push to remote repository (`https://github.com/drmuneebaslam/techscout.git` branch `main`).

7. **Direct FTP Deployment of Changed Files to `/htdocs`**:
   - For every change, connect via FTP and upload ONLY the newly modified files directly to their corresponding destination inside `/htdocs/`:
     - **FTP Hostname**: `ftpupload.net` (Port: `21`)
     - **FTP Username**: `if0_40102772`
     - **FTP Password**: `Fp9QFWfj5fm`
     - **Target Directory**: `/htdocs/`
   - **CRITICAL PRESERVATION & SAFETY DIRECTIVE**:
     - **NEVER** delete, modify, rename, or touch any existing folders or files that are already present on the hosting account that are not part of the active change (such as `old/`, `old2/`, `oldlat/`, `tech/`, `DO NOT UPLOAD FILES HERE`, `hbdfbfj.loveslife.biz`, or any non-project folders).
     - Only create required parent subdirectories (e.g. `/htdocs/app/Core`, `/htdocs/assets/css`, `/htdocs/views/layouts`, etc.) if they do not already exist.
     - Ensure `app/Core/Config.php` uploaded to FTP contains the hosted InfinityFree credentials (`sql306.infinityfree.com`, `if0_40102772_techscoutmainafterallchanges`, `if0_40102772`, `Fp9QFWfj5fm`, `https://funwithcoding.free.nf`).

8. **EC2 Production Server Synchronization & Migration Execution (`techscout.online`)**:
   - For every change, the pushed commits on `origin/main` update the upstream repository for the Amazon EC2 instance (`/var/www/techscout`).
   - Run or trigger the deployment & migration script (`scripts/ec2_deploy_and_migrate.sh`) or call `POST /admin/system/deploy-pull` (or webhook `POST /api/v1/deploy/sync?token=...`).
   - All database migrations in `database/migrations/*.sql` must be executed sequentially on EC2 MySQL (`techscout_db`) so all new tables, columns, indexes, and seeded data are immediately active on production `https://techscout.online`.


