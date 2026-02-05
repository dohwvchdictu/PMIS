<?php

namespace App\Exports;

use App\Models\Procurement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BacPrsReceivedExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $search;
    protected $startDate;
    protected $endDate;
    protected $currentModeFilter;

    public function __construct($search, $startDate, $endDate, $currentModeFilter)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->currentModeFilter = $currentModeFilter;
    }

    public function query()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'division',
                'clusterCommittee',
                'category.bacType',
                'fundSource',
                'mopLots.modeOfProcurement'
            ])
            ->whereHas('category', function ($q) {
                $q->where('bac_type_id', 1);
            })
            ->latest('date_receipt');

        // Apply search filter
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', $searchTerm)
                    ->orWhere('procurement_program_project', 'like', $searchTerm);
            });
        }

        // Apply period covered filter
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('date_receipt', [$this->startDate, $this->endDate]);
        } elseif (!empty($this->startDate)) {
            $query->whereDate('date_receipt', '>=', $this->startDate);
        } elseif (!empty($this->endDate)) {
            $query->whereDate('date_receipt', '<=', $this->endDate);
        }

        // Current Mode filter
        if ($this->currentModeFilter) {
            $query->where(function ($q) {
                // Per-lot procurements
                $q->where('procurement_type', 'perLot')
                    ->whereHas('mopLots', function ($subQ) {
                        $subQ->where('mode_of_procurement_id', $this->currentModeFilter)
                            ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_lot WHERE procID = procurements.procID)');
                    });
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'PR Number',
            'Procurement Program / Project',
            'Date Received',
            'Unit / Cluster',
            'Category',
            'Immediate Date Needed',
            'Fund Source',
            'ABC Amount',
            'Procurement Stage',
            'Current Mode',
        ];
    }

    public function map($procurement): array
    {
        // Helper function to safely format dates
        $formatDate = function ($date) {
            if (!$date)
                return 'N/A';
            try {
                return \Carbon\Carbon::parse($date)->format('M d, Y');
            } catch (\Exception $e) {
                return $date; // Return original value if not a valid date
            }
        };

        // Get current mode
        $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
        $currentMode = $latestMop?->modeOfProcurement?->modeofprocurements ?? 'N/A';

        return [
            $procurement->pr_number,
            $procurement->procurement_program_project,
            $formatDate($procurement->date_receipt),
            $procurement->clusterCommittee?->clustercommittee ?? 'N/A',
            $procurement->category?->category ?? 'N/A',
            $formatDate($procurement->immediate_date_needed),
            $procurement->fundSource?->fundsources ?? 'N/A',
            number_format($procurement->abc ?? 0, 2),
            $procurement->currentPrStage?->procurementStage?->procurementstage ?? 'No Stage',
            $currentMode,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Style the header row
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Center align all data cells (A to J)
        $sheet->getStyle('A2:J' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Right align ABC Amount column (H)
        $sheet->getStyle('H2:H' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Wrap text for Procurement Program / Project column (B)
        $sheet->getStyle('B2:B' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Immediate Date Needed column (F)
        $sheet->getStyle('F2:F' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Procurement Stage column (I)
        $sheet->getStyle('I2:I' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Current Mode column (J)
        $sheet->getStyle('J2:J' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // PR Number
            'B' => 50, // Procurement Program / Project
            'C' => 18, // Date Received
            'D' => 25, // Unit / Cluster
            'E' => 25, // Category
            'F' => 22, // Immediate Date Needed
            'G' => 25, // Fund Source
            'H' => 18, // ABC Amount
            'I' => 30, // Procurement Stage
            'J' => 25, // Current Mode
        ];
    }
}
