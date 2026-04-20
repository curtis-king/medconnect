cd C:\laragon\www\zer

Write-Host "`n`n🔐 FINAL API LOGIN TEST`n" -ForegroundColor Cyan

$body = @{
    email = "patient@test.com"
    password = "password"
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest `
        -Uri "http://localhost:8000/api/v1/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $body

    $json = $response.Content | ConvertFrom-Json

    Write-Host "✅ LOGIN SUCCESS" -ForegroundColor Green
    Write-Host ""
    Write-Host "User: $($json.data.user.name)"
    Write-Host "Email: $($json.data.user.email)"
    Write-Host "Token: $($json.data.token.Substring(0, 30))..."
    Write-Host "Expires: $($json.data.expires_at)"
    Write-Host ""
    Write-Host "✅ API AUTHENTICATION IS WORKING!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}
