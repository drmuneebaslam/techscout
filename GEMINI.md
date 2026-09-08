# TechScout Project Rules & Mandatory Workflow

Always follow this mandatory workflow for every prompt and code modification in this project:

1. **Clean `changes/` Folder First**:
   Before making any edits or code changes, delete all existing files and subdirectories inside the `changes/` folder.

2. **Apply Project Modifications**:
   Implement all requested edits, feature additions, or bug fixes across the project codebase.

3. **Copy Only Core Project Source Files to `changes/`**:
   - Copy ONLY newly modified or created files from actual project-related folders (e.g., `app/`, `views/`, `assets/`, `database/`, `public/`, `docs/`, `index.php`, `.htaccess`, etc.).
   - DO NOT copy scratch, waste, temporary, skill, or system configuration files (e.g., `scratch/`, `waste/`, `skills/`, `.system_generated/`, etc.) into `changes/`.
   - Preserve exact relative directory structure for all copied project files.

4. **Git Commit & Push Every Change**:
   Execute a `git commit` detailing each and every change made in the commit message, and push to remote repository (`https://github.com/drmuneebaslam/techscout.git` branch `main`).
