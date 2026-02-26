$file = 'c:\Users\DOH\Herd\BACPMIS\resources\views\livewire\pmu\partials\expanded-table.blade.php'
$lines = Get-Content $file -Encoding UTF8
Write-Host "Before: $($lines.Length) lines"
Write-Host "First line: $($lines[0].Trim())"
Write-Host "Last 5 lines:"
$lines[($lines.Length-5)..($lines.Length-1)] | ForEach-Object { Write-Host "  [$_]" }
# Remove first line (the overflow-x-auto opening div) and last 3 lines (</div></div></td>)
$trimmed = $lines[1..($lines.Length - 4)]
$trimmed | Set-Content $file -Encoding UTF8
Write-Host "After: $((Get-Content $file).Length) lines"
