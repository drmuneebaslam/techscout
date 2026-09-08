# TechScout Project Rules & Mandatory Workflow

Always follow this mandatory workflow for every prompt and code modification in this project:

1. **Clean `changes/` Folder First**:
   Before making any edits or code changes, delete all existing files and subdirectories inside the `changes/` folder.

2. **Apply Project Modifications**:
   Implement all requested edits, feature additions, or bug fixes across the codebase.

3. **Copy Only Newly Edited Files to `changes/`**:
   Copy ONLY the newly modified or created files into the `changes/` directory, preserving their exact relative path structure (e.g., `changes/views/...`, `changes/index.php`, etc.).

4. **Git Commit Every Change**:
   Execute a `git commit` detailing each and every change made in the commit message.
