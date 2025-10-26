param(
  [Parameter(Mandatory=$true)]
  [string]$LegacyRoot,   # ex: C:\xampp\htdocs\LEGADO\b i z l i s t e r (raiz do legado)

  [string]$NewRoot = (Get-Location).Path,  # raiz do projeto novo (padrão = pasta atual)

  [switch]$CopyMissing,   # copia views que não existem no novo
  [switch]$WriteDiffs,    # grava diffs .txt para arquivos diferentes
  [switch]$AutoFix        # aplica pequenos ajustes de compatibilidade Laravel 8
)

function RelPath($base, $path) {
  $u = Resolve-Path $path
  $b = Resolve-Path $base
  return $u.Path.Substring($b.Path.Length).TrimStart('\','/')
}

function HashFile($p) {
  if (!(Test-Path $p)) { return $null }
  return (Get-FileHash $p -Algorithm SHA1).Hash
}

$legacyViews = Join-Path $LegacyRoot "resources\views"
$newViews    = Join-Path $NewRoot    "resources\views"

if (!(Test-Path $legacyViews)) { throw "ERRO: não achei $legacyViews" }
if (!(Test-Path $newViews))    { throw "ERRO: não achei $newViews" }

Write-Host "Legado : $legacyViews"
Write-Host "Novo   : $newViews"

# coleções (chave = caminho relativo normalizado)
$legacyMap = @{}
$newMap    = @{}

Get-ChildItem $legacyViews -Recurse -File -Filter *.blade.php | ForEach-Object {
  $rel = (RelPath $legacyViews $_.FullName) -replace '\\','/'
  $legacyMap[$rel] = @{
    Full = $_.FullName
    Hash = HashFile $_.FullName
  }
}

Get-ChildItem $newViews -Recurse -File -Filter *.blade.php | ForEach-Object {
  $rel = (RelPath $newViews $_.FullName) -replace '\\','/'
  $newMap[$rel] = @{
    Full = $_.FullName
    Hash = HashFile $_.FullName
  }
}

$missing = @()
$changed = @()
$same    = @()

foreach ($k in $legacyMap.Keys) {
  if (-not $newMap.ContainsKey($k)) {
    $missing += $k
  } else {
    if ($legacyMap[$k].Hash -ne $newMap[$k].Hash) {
      $changed += $k
    } else {
      $same += $k
    }
  }
}

Write-Host ""
Write-Host "=== RELATÓRIO DE VIEWS (LEGADO → NOVO) ===" -ForegroundColor Cyan
Write-Host ("Faltando no novo : {0}" -f $missing.Count)
Write-Host ("Diferentes        : {0}" -f $changed.Count)
Write-Host ("Iguais            : {0}" -f $same.Count)

# pasta de saída para diffs
$diffDir = Join-Path $NewRoot "tools\view-diffs"
if ($WriteDiffs) { New-Item -ItemType Directory -Force -Path $diffDir | Out-Null }

# 1) Copia as que não existem
if ($CopyMissing -and $missing.Count -gt 0) {
  foreach ($rel in $missing) {
    $src = $legacyMap[$rel].Full
    $dst = Join-Path $newViews $rel
    $dstDir = Split-Path $dst -Parent
    New-Item -ItemType Directory -Force -Path $dstDir | Out-Null
    Copy-Item $src $dst -Force
    Write-Host "Copiado (novo): $rel" -ForegroundColor Green
  }
}

# 2) Gera diffs simples para revisar os diferentes
# usa 'fc' do Windows (side-by-side); também salva o arquivo legado caso queira abrir
if ($WriteDiffs -and $changed.Count -gt 0) {
  foreach ($rel in $changed) {
    $src = $legacyMap[$rel].Full
    $dst = $newMap[$rel].Full
    $out = Join-Path $diffDir ($rel -replace '/','__' -replace '\.blade\.php$','.diff.txt')
    New-Item -ItemType Directory -Force -Path (Split-Path $out -Parent) | Out-Null
    # 'fc' retorna código de saída != 0 quando há diferenças, por isso não trate como erro
    cmd /c "fc `"$src`" `"$dst`" > `"$out`"" | Out-Null
    Write-Host "Diff gerado: $rel → $(RelPath $NewRoot $out)" -ForegroundColor Yellow
  }
}

# 3) Ajustes opcionais p/ Laravel 8 (safe)
if ($AutoFix) {
  # 3.1 – garantir @csrf em forms que tenham method="post" e não tenham @csrf
  Get-ChildItem $newViews -Recurse -File -Filter *.blade.php | ForEach-Object {
    $txt = Get-Content $_.FullName -Raw
    if ($txt -match '<form[^>]*method\s*=\s*["'']?post["'']?' -and $txt -notmatch '@csrf') {
      # injeta @csrf logo após a tag <form ...>
      $new = $txt -replace '(<form[^>]*>)', '$1' + "`r`n    @csrf"
      if ($new -ne $txt) {
        Set-Content $_.FullName -Value $new -Encoding UTF8
        Write-Host "AutoFix @csrf: $(RelPath $newViews $_.FullName)" -ForegroundColor DarkCyan
      }
    }
  }

  # 3.2 – converte <?= ?> curtas para Blade {{ }} (com escape)
  Get-ChildItem $newViews -Recurse -File -Filter *.blade.php | ForEach-Object {
    $txt = Get-Content $_.FullName -Raw
    $new = $txt -replace '<\?=\s*(.+?)\s*\?>', '{{ $1 }}'
    if ($new -ne $txt) {
      Set-Content $_.FullName -Value $new -Encoding UTF8
      Write-Host "AutoFix echo curto → Blade: $(RelPath $newViews $_.FullName)" -ForegroundColor DarkCyan
    }
  }

  # 3.3 – ajusta includes antigos para @include('partials.x') quando detecta include('partials/x.blade.php')
  Get-ChildItem $newViews -Recurse -File -Filter *.blade.php | ForEach-Object {
    $txt = Get-Content $_.FullName -Raw
    $new = $txt -replace "@include\(['""]partials\/([A-Za-z0-9_\-\/]+)\.blade\.php['""]\)", "@include('partials.$1')"
    if ($new -ne $txt) {
      Set-Content $_.FullName -Value $new -Encoding UTF8
      Write-Host "AutoFix includes: $(RelPath $newViews $_.FullName)" -ForegroundColor DarkCyan
    }
  }
}

# 4) Saída final “máquina legível” (se quiser consumir em CI)
$report = [PSCustomObject]@{
  Missing  = $missing
  Changed  = $changed
  Same     = $same
  Legacy   = $legacyViews
  New      = $newViews
  DiffDir  = if ($WriteDiffs) { $diffDir } else { $null }
}
$report | ConvertTo-Json -Depth 5
