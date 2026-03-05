$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = 'c:\xampp\htdocs\cpt283coblentz'
$watcher.Filter = '*.*'
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true
$watcher.NotifyFilter = [System.IO.NotifyFilters]::LastWrite -bor [System.IO.NotifyFilters]::FileName

$action = {
    $path = $Event.SourceEventArgs.FullPath
    $ext = [System.IO.Path]::GetExtension($path)
    
    if ($ext -match '\.(php|css|js|html|json|md)$' -and 
        $path -notmatch '\\\.vscode\\' -and 
        $path -notmatch '\\\.git\\' -and 
        $path -notmatch '\\\.agent\\' -and 
        $path -notmatch '\\node_modules\\') {
        
        $basePath = 'c:\xampp\htdocs\cpt283coblentz\'
        $rel = $path.Substring($basePath.Length).Replace('\','/')
        
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Uploading: $rel"
        
        & curl -T $path "ftp://26sp-cpt283-coblentz.beausanders.net/$rel" --user "cpt283coblentz:Pinkyp!321" --ftp-pasv --ftp-create-dirs -s 2>&1 | Out-Null
        
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Done: $rel"
    }
}

Register-ObjectEvent $watcher Changed -Action $action | Out-Null
Register-ObjectEvent $watcher Created -Action $action | Out-Null

Write-Host "========================================="
Write-Host " FTP File Watcher Running"
Write-Host " Monitoring: c:\xampp\htdocs\cpt283coblentz"
Write-Host " Server: 26sp-cpt283-coblentz.beausanders.net"
Write-Host "========================================="

while ($true) { Wait-Event -Timeout 1 | Out-Null }
