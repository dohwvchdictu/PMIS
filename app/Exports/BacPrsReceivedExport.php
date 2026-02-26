<?php

namespace App\Exports;

use App\Models\Procurement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\BidSchedule;

class BacPrsReceivedExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $search;
    protected $startDate;
    protected $endDate;
    protected $currentModeFilter;
    protected $bacTypeId;

    public function __construct($search, $startDate, $endDate, $currentModeFilter, $bacTypeId)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->currentModeFilter = $currentModeFilter;
        $this->bacTypeId = $bacTypeId;
    }

    public function collection()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'division',
                'clusterCommittee',
                'category.bacType',
                'fundSource',
                'endUser',
                'mopLots.modeOfProcurement',
                'pr_items.mopItems.modeOfProcurement',
                'pr_items'
            ])
            ->whereHas('category', function ($q) {
                $q->where('bac_type_id', $this->bacTypeId);
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
                // For Category A, only filter per-lot procurements
                $q->where('procurement_type', 'perLot')
                    ->whereHas('mopLots', function ($subQ) {
                        $subQ->where('mode_of_procurement_id', $this->currentModeFilter)
                            ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_lot WHERE procID = procurements.procID)');
                    });
            });
        }

        $procurements = $query->get();
        $rows = collect();

        foreach ($procurements as $procurement) {
            if ($procurement->procurement_type === 'perLot') {
                $rows->push($this->mapProcurement($procurement));
            } else {
                foreach ($procurement->pr_items as $item) {
                    $rows->push($this->mapItem($procurement, $item));
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'PR Number',
            'IB No',
            'Procurement Program / Project',
            'Date Received',
            'DTrack No',
            'Division',
            'Unit / Cluster',
            'Category',
            'End-User',
            'Category / Venue',
            'Immediate Date Needed',
            'Date Needed',
            'Fund Source',
            'ABC Amount',
            'Approved PPMP',
            'EPA',
            'Procurement Stage',
            'Current Mode',
        ];
    }

    public function map($row): array
    {
        return $row;
    }

    private function mapProcurement($procurement): array
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

        // Get current mode and IB No
        $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
        $currentMode = $latestMop?->modeOfProcurement?->modeofprocurements ?? 'N/A';
        $ibNo = 'N/A';
        if ($latestMop && in_array($latestMop->mode_of_procurement_id, [2, 3, 4, 5, 6])) {
            $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                ->orderBy('created_at', 'desc')
                ->first();
            $ibNo = $bidSchedule?->ib_number ?? 'N/A';
        }

        $approvedPpmp = $procurement->approved_ppmp;
        $approvedPpmpLabel = ($approvedPpmp === null || $approvedPpmp === '')
            ? 'N/A'
            : (($approvedPpmp == '1' || strtolower((string) $approvedPpmp) === 'yes') ? 'Yes' : $approvedPpmp);

        return [
            $procurement->pr_number,
            $ibNo,
            $procurement->procurement_program_project,
            $formatDate($procurement->date_receipt),
            $procurement->dtrack_no ?? 'N/A',
            $procurement->division?->divisions ?? 'N/A',
            $procurement->clusterCommittee?->clustercommittee ?? 'N/A',
            $procurement->category?->category ?? 'N/A',
            $procurement->endUser?->endusers ?? 'N/A',
            $procurement->category_venue ?? 'N/A',
            $procurement->immediate_date_needed ?? 'N/A',
            $procurement->date_needed ?? 'N/A',
            $procurement->fundSource?->fundsources ?? 'N/A',
            number_format($procurement->abc ?? 0, 2),
            $approvedPpmpLabel,
            $procurement->early_procurement ? 'Yes' : 'No',
            $procurement->currentPrStage?->procurementStage?->procurementstage ?? 'No Stage',
            $currentMode,
        ];
    }

    private function mapItem($procurement, $item): array
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

        // Get current mode for the item
        $latestMop = $item->mopItems->sortByDesc('mode_order')->first();
        $currentMode = $latestMop?->modeOfProcurement?->modeofprocurements ?? 'N/A';

        $approvedPpmp = $procurement->approved_ppmp;
        $approvedPpmpLabel = ($approvedPpmp === null || $approvedPpmp === '')
            ? 'N/A'
            : (($approvedPpmp == '1' || strtolower((string) $approvedPpmp) === 'yes') ? 'Yes' : $approvedPpmp);

        return [
            $procurement->pr_number,
            'N/A',
            $item->description,
            $formatDate($procurement->date_receipt),
            $procurement->dtrack_no ?? 'N/A',
            $procurement->division?->divisions ?? 'N/A',
            $procurement->clusterCommittee?->clustercommittee ?? 'N/A',
            $procurement->category?->category ?? 'N/A',
            $procurement->endUser?->endusers ?? 'N/A',
            $procurement->category_venue ?? 'N/A',
            $procurement->immediate_date_needed ?? 'N/A',
            $procurement->date_needed ?? 'N/A',
            $procurement->fundSource?->fundsources ?? 'N/A',
            number_format($item->amount ?? 0, 2),
            $approvedPpmpLabel,
            $procurement->early_procurement ? 'Yes' : 'No',
            $item->prstage?->stage?->procurementstage ?? 'No Stage',
            $currentMode,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Style the header row
        $sheet->getStyle('A1:R1')->applyFromArray([
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

        // Center align all data cells
        $sheet->getStyle('A2:R' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Right align ABC Amount column (N)
        $sheet->getStyle('N2:N' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Wrap text for Procurement Program / Project column (C)
        $sheet->getStyle('C2:C' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Immediate Date Needed column (K)
        $sheet->getStyle('K2:K' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Date Needed column (L)
        $sheet->getStyle('L2:L' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Division column (F)
        $sheet->getStyle('F2:F' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Procurement Stage column (Q)
        $sheet->getStyle('Q2:Q' . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Wrap text for Current Mode column (R)
        $sheet->getStyle('R2:R' . $highestRow)->applyFromArray([
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
            'B' => 20, // IB No
            'C' => 50, // Procurement Program / Project
            'D' => 18, // Date Received
            'E' => 20, // DTrack No
            'F' => 25, // Division
            'G' => 25, // Unit / Cluster
            'H' => 25, // Category
            'I' => 30, // End-User
            'J' => 30, // Category / Venue
            'K' => 22, // Immediate Date Needed
            'L' => 22, // Date Needed
            'M' => 25, // Fund Source
            'N' => 18, // ABC Amount
            'O' => 18, // Approved PPMP
            'P' => 10, // EPA
            'Q' => 30, // Procurement Stage
            'R' => 25, // Current Mode
        ];
    }
}
