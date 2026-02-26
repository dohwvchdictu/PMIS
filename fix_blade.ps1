$file = 'c:\Users\DOH\Herd\BACPMIS\resources\views\livewire\pmu\pmu-index-page.blade.php'
$lines = Get-Content $file -Encoding UTF8
Write-Host "Total lines before: $($lines.Length)"
$keep = $lines[0..404] + $lines[1048..($lines.Length - 1)]
$keep | Set-Content $file -Encoding UTF8 -NoNewline:$false
Write-Host "Total lines after: $((Get-Content $file).Length)"
