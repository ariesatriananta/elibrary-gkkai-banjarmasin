param(
    [string]$OutputPath = ".\dist\xampp\elibrary-gkkai"
)

$ErrorActionPreference = "Stop"

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$outputRoot = [System.IO.Path]::GetFullPath((Join-Path $projectRoot $OutputPath))

$requiredPaths = @(
    (Join-Path $projectRoot "vendor\autoload.php"),
    (Join-Path $projectRoot "public\assets\css\app.css"),
    (Join-Path $projectRoot ".htaccess"),
    (Join-Path $projectRoot "deploy\xampp.env.example"),
    (Join-Path $projectRoot "docs\DEPLOY_XAMPP.md")
)

foreach ($path in $requiredPaths) {
    if (-not (Test-Path $path)) {
        throw "File wajib tidak ditemukan: $path"
    }
}

if (Test-Path $outputRoot) {
    Remove-Item -Path $outputRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $outputRoot | Out-Null

$directoriesToCopy = @("app", "public", "vendor", "writable")
foreach ($directory in $directoriesToCopy) {
    $source = Join-Path $projectRoot $directory
    $target = Join-Path $outputRoot $directory

    & robocopy $source $target /E /NFL /NDL /NJH /NJS /NC /NS | Out-Null

    if ($LASTEXITCODE -gt 7) {
        throw "Gagal menyalin folder $directory dengan robocopy. Exit code: $LASTEXITCODE"
    }
}

$filesToCopy = @(
    ".htaccess",
    "spark",
    "composer.json",
    "composer.lock",
    "LICENSE"
)

foreach ($file in $filesToCopy) {
    Copy-Item -Path (Join-Path $projectRoot $file) -Destination (Join-Path $outputRoot $file) -Force
}

Copy-Item -Path (Join-Path $projectRoot "deploy\xampp.env.example") -Destination (Join-Path $outputRoot ".env") -Force

$writableSubdirs = @(
    "cache",
    "debugbar",
    "logs",
    "session",
    "uploads"
)

foreach ($subdir in $writableSubdirs) {
    $dirPath = Join-Path $outputRoot "writable\$subdir"

    if (-not (Test-Path $dirPath)) {
        continue
    }

    Get-ChildItem -Path $dirPath -Force | Where-Object {
        $_.Name -ne "index.html"
    } | Remove-Item -Recurse -Force
}

$coverDir = Join-Path $outputRoot "public\assets\uploads\covers"
if (-not (Test-Path $coverDir)) {
    New-Item -ItemType Directory -Path $coverDir | Out-Null
}

$deployGuideTarget = Join-Path $outputRoot "DEPLOY_XAMPP.md"
Copy-Item -Path (Join-Path $projectRoot "docs\DEPLOY_XAMPP.md") -Destination $deployGuideTarget -Force

Write-Host ""
Write-Host "Paket XAMPP siap dibuat di:" -ForegroundColor Green
Write-Host $outputRoot -ForegroundColor Green
Write-Host ""
Write-Host "Langkah berikutnya:"
Write-Host "1. Copy folder ini ke C:\xampp\htdocs\elibrary-gkkai"
Write-Host "2. Sesuaikan file .env bila nama folder/database berbeda"
Write-Host "3. Buka http://localhost/elibrary-gkkai/"
