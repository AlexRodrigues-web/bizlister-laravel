Param(
  [string]$Db="flippy_bizlister_v2",
  [string]$Mysql="C:\xampp\mysql\bin\mysql.exe"
)

$ErrorActionPreference="SilentlyContinue"
$PASS=0;$FAIL=0
function P($m){$global:PASS++; Write-Host "[OK] " -f Green -NoNewline; Write-Host $m}
function F($m){$global:FAIL++; Write-Host "[FALHA] " -f Red -NoNewline; Write-Host $m}
function H($t){Write-Host "`n=== $t ===" -f Cyan}
function Sh($cmd){try{Invoke-Expression $cmd}catch{}}

H "Ambiente"
Sh 'php -v | Select-Object -First 1'
Sh 'composer -V'
Sh 'php artisan --version'

if(Test-Path .\.env){ P ".env presente" } else { F ".env ausente (copie de .env.example e ajuste credenciais)" }
$envContent = if(Test-Path .\.env){ Get-Content .\.env -Raw } else { "" }
if($envContent -match "APP_KEY=base64"){ P "APP_KEY configurada" } else { F "APP_KEY ausente (rode: php artisan key:generate)" }

if(Test-Path .\vendor){ P "vendor/ presente" } else { F "vendor/ ausente (rode: composer install)" }

H "Laravel (rotas/migrações/seeds)"
Sh 'php artisan route:list --columns=Method,URI,Name,Action,Middleware | findstr /i "search pages.show admin settings sitemap contact"'
Sh 'php artisan migrate:status'
Sh 'php artisan db:seed --class=__DoesNotRun 2>$null' # só para ver se o artisan carrega

H "Banco (páginas estáticas)"
$hasMysql = Test-Path $Mysql
if($hasMysql){
  & $Mysql -u root -D $Db -e "SHOW TABLES LIKE 'pages';" | Out-Null
  if($LASTEXITCODE -eq 0){ P "Conexão MySQL OK ($Db)" } else { F "Falha ao conectar MySQL ($Db)" }

  & $Mysql -u root -D $Db -e "SELECT slug,title,is_active,COALESCE(LENGTH(content),0) len FROM pages;" 2>$null
  if($LASTEXITCODE -eq 0){ P "Tabela pages lida com sucesso" } else { F "Tabela pages não encontrada" }
} else { F "MySQL CLI não encontrado em $Mysql" }

H "Build do front (se aplicável)"
if(Test-Path .\package.json){
  P "package.json presente (documentar: npm ci && npm run build/dev)"
}else{
  Write-Host "Sem package.json" -f DarkYellow
}

H "Resumo"
Write-Host ("PASS: {0}   FAIL: {1}" -f $PASS,$FAIL) -f Cyan
if($FAIL -gt 0){
  Write-Host "Ajuste as falhas acima e rode novamente." -f Yellow
}else{
  Write-Host "Tudo pronto para documentar/rodar instação." -f Green
}
