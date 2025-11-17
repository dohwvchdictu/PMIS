<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\Category;
use App\Models\MopGroup;
use App\Models\PrItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\ModeOfProcurement;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\NtfBidSchedule;
use App\Models\PrSvp;
use Livewire\WithPagination;

class ModeOfProcurementUpdatePage extends Component
{
    public Procurement $procurement;
    public $form = [];
    protected ?Category $categoryCache = null;
    public $showTable = true;
    public $textareaRows = 1;
    public $page = 1;
    public $perPage = 10;
    public string $procID = '';
    public int $activeTab = 1;
    public function mount(Procurement $procurement)
    {
        $procurement->load('pr_items');
        $this->procurement = $procurement;
        $this->procID = $procurement->procID ?? '';

        $this->form = $procurement->toArray();

        $this->form['approved_ppmp'] = (bool) ($this->form['approved_ppmp'] ?? false);
        $this->form['app_updated'] = (bool) ($this->form['app_updated'] ?? false);
        $this->form['early_procurement'] = (bool) ($this->form['early_procurement'] ?? false);

        // Normalize procurement_type default
        if (!in_array($this->form['procurement_type'] ?? null, ['perItem', 'perLot'])) {
            $this->form['procurement_type'] = 'perLot';
        }

        // Load items (reverse/sort to match create visual order) and keep prItemID
        if ($this->form['procurement_type'] === 'perItem') {
            $this->form['items'] = $procurement->pr_items
                ->sortByDesc('id')
                ->map(fn($item) => [
                    'prItemID' => $item->prItemID,
                    'item_no' => $item->item_no,
                    'description' => $item->description,
                    'amount' => number_format((float) $item->amount, 2, '.', ''),
                ])
                ->values()
                ->toArray();

            if (empty($this->form['items'])) {
                $this->addItem();
            }
        } else {
            $this->form['items'] = $this->form['items'] ?? [];
        }

        if ($procurement) {
            $this->form['procurement_program_project'] = $procurement->procurement_program_project;
            $this->procID = $procurement->procID;

            // Dynamically calculate rows based on text length or line breaks
            $text = trim($procurement->procurement_program_project ?? '');

            // Count actual new lines
            $lineCount = substr_count($text, "\n") + 1;

            // Estimate wrapped lines more conservatively
            $approxExtraLines = ceil(strlen($text) / 150); // ← increased divisor from 100 → 150
            // That means: only very long text adds rows

            // Combine both counts, ensure at least 1 row
            $this->textareaRows = max($lineCount, $approxExtraLines, 1);
        } else {
            $this->form['procurement_program_project'] = '';
            $this->procID = null;
            $this->textareaRows = 1;
        }
    }
    public function removeItem(string $prItemID): void
    {
        $this->form['items'] = array_filter($this->form['items'], function ($item) use ($prItemID) {
            return $item['prItemID'] !== $prItemID;
        });

        // Reindex array
        $this->form['items'] = array_values($this->form['items']);
    }

    public function render()
    {


        return view(
            'livewire.mode-of-procurement.mode-of-procurement-update-page'
        );
    }
}
