---
description: Upload files to remote FTP server after editing
---

// turbo-all

## FTP Upload Workflow

Use this workflow after editing files to sync them to the remote server at `26sp-cpt283-coblentz.beausanders.net`.

### Upload a single file

To upload a single file, run the following command, replacing `<LOCAL_PATH>` with the full local path and `<REMOTE_PATH>` with the path relative to the project root (e.g., `vehicle_log/projectlab03.php`):

```
curl -T "<LOCAL_PATH>" "ftp://26sp-cpt283-coblentz.beausanders.net/<REMOTE_PATH>" --user "cpt283coblentz:Pinkyp!321" --ftp-pasv --ftp-create-dirs
```

### Upload an entire folder

To upload all PHP files in a directory recursively, use this PowerShell command. Replace `<LOCAL_DIR>` with the local directory (e.g., `c:\xampp\htdocs\cpt283coblentz\vehicle_log`) and `<REMOTE_BASE>` with the remote base path (e.g., `vehicle_log`):

```
Get-ChildItem -Path "<LOCAL_DIR>" -Recurse -File | ForEach-Object { $rel = $_.FullName.Substring("<LOCAL_DIR>".Length + 1).Replace('\','/'); curl -T $_.FullName "ftp://26sp-cpt283-coblentz.beausanders.net/<REMOTE_BASE>/$rel" --user "cpt283coblentz:Pinkyp!321" --ftp-pasv --ftp-create-dirs }
```

### Notes

- The `--ftp-create-dirs` flag ensures remote directories are created if they don't exist.
- The FTP root on the server is `/www/www`.
- Always use `--ftp-pasv` for passive mode.
- After renaming or deleting folders/files locally, old files will still exist on the remote. You may need to manually delete them via an FTP client.
