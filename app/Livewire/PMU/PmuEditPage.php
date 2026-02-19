<?php

namespace App\Livewire\PMU;

use App\Models\Pmu;
use App\Models\Procurement;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

class PmuEditPage extends Component
{
    public $noticeOfAwardNumber;
    public $pmuRecord = null;
    public $procurements = null;
    public $itemRows = null;

    // Form fields
    public $date_forwarded = '';
    public $contract_amount = '';
    public $po_contract_number = '';
    public $po_contract_number_link = '';
    public $contract_signing_date = '';
    public $notice_to_proceed_date = '';
    public $remarks = '';

    protected $rules = [
        'date_forwarded' => 'nullable|date',
        'contract_amount' => 'required|numeric|min:0',
        'po_contract_number' => 'required|string|max:255',
        'po_contract_number_link' => 'nullable|url|max:2048',
        'contract_signing_date' => 'required|date',
        'notice_to_proceed_date' => 'nullable|date',
        'remarks' => 'nullable|string',
    ];

    protected $messages = [
        'contract_amount.required' => 'Contract amount is required.',
        'contract_amount.numeric' => 'Contract amount must be a valid number.',
        'contract_amount.min' => 'Contract amount must be 0 or greater.',
        'po_contract_number.required' => 'PO / Contract number is required.',
        'contract_signing_date.required' => 'Contract signing date is required.',
        'date_forwarded.date' => 'Date forwarded must be a valid date.',
        'contract_signing_date.date' => 'Contract signing date must be a valid date.',
        'notice_to_proceed_date.date' => 'Notice to proceed date must be a valid date.',
    ];

    public function mount($id)
    {
        $this->noticeOfAwardNumber = $id;

        $record = Pmu::where('notice_of_award_number', $id)->firstOrFail();
        $this->pmuRecord = $record;

        $this->date_forwarded = $record->date_forwarded ? $record->date_forwarded->format('Y-m-d') : '';
        $this->contract_amount = $record->contract_amount ?? '';
        $this->po_contract_number = $record->po_contract_number ?? '';
        $this->po_contract_number_link = $record->po_contract_number_link ?? '';
        $this->contract_signing_date = $record->contract_signing_date ? $record->contract_signing_date->format('Y-m-d') : '';
        $this->notice_to_proceed_date = $record->notice_to_proceed_date ? $record->notice_to_proceed_date->format('Y-m-d') : '';
        $this->remarks = $record->remarks ?? '';

        // Per-lot procurements (stage 7)
        $this->procurements = Procurement::query()
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->where('post_procurements.notice_of_award_number', $id)
            ->whereNull('pmus.deleted_at')
            ->whereHas('prLotPrstages', fn($q) => $q->where('pr_stage_id', 7))
            ->select('procurements.*')
            ->get();

        // Per-item rows (stage 7)
        $this->itemRows = DB::table('pr_items')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pr_item_prstage')
                    ->whereColumn('pr_item_prstage.prItemID', 'pr_items.prItemID')
                    ->where('pr_item_prstage.pr_stage_id', 7);
            })
            ->where('post_procurements.notice_of_award_number', $id)
            ->whereNull('pmus.deleted_at')
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'pr_items.prItemID',
                'pr_items.item_no',
                'pr_items.description',
                'pr_items.amount'
            )
            ->orderBy('procurements.pr_number')
            ->orderBy('pr_items.item_no')
            ->get();
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $record = Pmu::where('notice_of_award_number', $this->noticeOfAwardNumber)->firstOrFail();

            $record->update([
                'date_forwarded' => $this->date_forwarded ?: null,
                'contract_amount' => $this->contract_amount !== '' ? $this->contract_amount : null,
                'po_contract_number' => $this->po_contract_number ?: null,
                'po_contract_number_link' => $this->po_contract_number_link ?: null,
                'contract_signing_date' => $this->contract_signing_date ?: null,
                'notice_to_proceed_date' => $this->notice_to_proceed_date ?: null,
                'remarks' => $this->remarks ?: null,
            ]);

            DB::commit();

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Updated!',
                'message' => 'PMU record updated successfully.',
            ]);

            return redirect()->route('pmu.index');
        } catch (\Exception $e) {
            DB::rollBack();
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to update record: ' . $e->getMessage())
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancel()
    {
        return redirect()->route('pmu.index');
    }

    public function render()
    {
        return view('livewire.pmu.pmu-edit-page', [
            'noticeOfAwardNumber' => $this->noticeOfAwardNumber,
            'pmuRecord' => $this->pmuRecord,
            'procurements' => $this->procurements,
            'itemRows' => $this->itemRows,
        ])->layout('components.layouts.app');
    }
}
