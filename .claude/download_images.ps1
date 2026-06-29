# Download all HENEX product images from henex.cn
# Usage: .\download_images.ps1

$storeDir = "d:\OSPanel\home\Henex\.claude\store"
$products = Get-ChildItem $storeDir -Directory

Write-Host "🖼️  Starting image download..."
Write-Host "📁 Store: $storeDir"
Write-Host "📊 Products: $($products.Count)"
Write-Host "⏱️  This will take 10-20 minutes (depends on internet speed)`n"

$totalImages = 0
$downloaded = 0
$failed = 0
$startTime = Get-Date

foreach ($product in $products) {
    $metadataFile = Join-Path $product.FullName "metadata.json"

    if (Test-Path $metadataFile) {
        $metadata = Get-Content $metadataFile | ConvertFrom-Json

        foreach ($image in $metadata.images) {
            $totalImages++
            $localPath = Join-Path $product.FullName $image.filename

            # Skip if already exists
            if (Test-Path $localPath) {
                $downloaded++
                continue
            }

            try {
                $webClient = New-Object System.Net.WebClient
                $webClient.DownloadFile($image.url, $localPath)

                $fileSize = (Get-Item $localPath).Length / 1KB
                Write-Host "✅ $($product.Name)/$($image.filename) ($([Math]::Round($fileSize, 0)) KB)"
                $downloaded++
            }
            catch {
                Write-Host "❌ Failed: $($product.Name)/$($image.filename)"
                $failed++
            }
        }
    }
}

$endTime = Get-Date
$duration = $endTime - $startTime
$totalSize = (Get-ChildItem $storeDir -Recurse -File | Measure-Object -Property Length -Sum).Sum / 1MB

Write-Host "`n✅ Download Complete!"
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
Write-Host "Total images:    $totalImages"
Write-Host "Downloaded:      $downloaded ✅"
Write-Host "Failed:          $failed ❌"
Write-Host "Total size:      $([Math]::Round($totalSize, 2)) MB"
Write-Host "Time taken:      $($duration.Minutes)m $($duration.Seconds)s"
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n"

if ($failed -eq 0) {
    Write-Host "🎉 All images downloaded successfully!"
    Write-Host "`nNext: Import products into database:"
    Write-Host "  php artisan import:henex-products"
} else {
    Write-Host "⚠️  Some images failed. Check internet connection and retry."
}
