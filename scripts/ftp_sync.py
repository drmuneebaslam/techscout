import ftplib
import os

FTP_HOST = "ftpupload.net"
FTP_USER = "if0_40102772"
FTP_PASS = "Fp9QFWfj5fm"
TARGET_DIR = "/htdocs/"

files_to_upload = [
    "app/Controllers/AdminController.php",
    "app/Controllers/ApiController.php",
    "app/Controllers/AuthController.php",
    "app/Controllers/BlogController.php",
    "app/Controllers/CategoryController.php",
    "app/Core/IncomingWebhookEngine.php",
    "app/Models/Content.php",
    "database/schema.sql",
    "index.php",
    "views/admin/incoming_webhooks.php",
    "views/auth/login.php",
    "views/author/editor.php",
    "views/public/categories.php",
    "views/public/category_single.php",
    "database/migrations/003_drop_tags.sql"
]

def upload_files():
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        print("Connected successfully.")
        
        for file_path in files_to_upload:
            if not os.path.exists(file_path):
                print(f"File {file_path} not found locally, skipping.")
                continue
                
            remote_path = TARGET_DIR + file_path.replace("\\", "/")
            remote_dir = "/".join(remote_path.split("/")[:-1])
            
            # Create directories if they don't exist
            dirs = remote_dir.split("/")
            current_dir = ""
            for d in dirs:
                if not d: continue
                current_dir += "/" + d
                try:
                    ftp.cwd(current_dir)
                except:
                    try:
                        ftp.mkd(current_dir)
                        ftp.cwd(current_dir)
                    except Exception as e:
                        print(f"Error creating directory {current_dir}: {e}")
            
            # Upload file
            ftp.cwd("/")
            print(f"Uploading {file_path} to {remote_path}...")
            with open(file_path, "rb") as f:
                ftp.storbinary(f"STOR {remote_path}", f)
            print(f"Successfully uploaded {file_path}")
            
            # Also copy to changes/ locally per rule 5
            local_change_dir = os.path.dirname(f"changes/{file_path}")
            if not os.path.exists(local_change_dir):
                os.makedirs(local_change_dir, exist_ok=True)
            import shutil
            shutil.copy2(file_path, f"changes/{file_path}")
            
        ftp.quit()
        print("Done.")
    except Exception as e:
        print(f"FTP error: {e}")

if __name__ == "__main__":
    upload_files()
