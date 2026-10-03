@echo off
setlocal EnableDelayedExpansion

echo ==========================================
echo       COMPLETE GIT HISTORY EXPORT
echo ==========================================
echo.

REM Check Git repository
git rev-parse --is-inside-work-tree >nul 2>&1

if errorlevel 1 (
    echo ERROR: Not a Git repository.
    pause
    exit /b 1
)

REM Create folders
if not exist "gitlogs" mkdir "gitlogs"
if not exist "gitlogs\commits" mkdir "gitlogs\commits"

echo.
echo Exporting ALL commits reachable from ALL references...
echo.

set count=0

REM ==========================================
REM EVERY COMMIT
REM ==========================================

for /f %%C in ('git rev-list --all') do (

    set "commit=%%C"

    set /a count+=1

    echo [!count!] !commit!

    git show --no-ext-diff --full-history --decorate --format=fuller --stat --summary !commit! > "gitlogs\commits\!commit!.txt"
)

echo.
echo ==========================================
echo Finished.
echo Total unique commits found: %count%
echo ==========================================
echo.

pause