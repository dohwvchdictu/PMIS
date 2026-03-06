<?php

namespace App\Livewire\PMU;

use App\Models\Pmu;
use App\Models\Procurement;
use App\Models\PostProcurement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

#[Title('View PMU | PMIS')]
class PmuViewPage extends Component
{
    public $noticeOfAwardNumber;
    public $pmuRecord = null;
    public $procurements = null;
    public $itemRows = null;
    public $totalAbc = 0;

    public function mount($id)
    {
        $this->noticeOfAwardNumber = $id;

        $record = Pmu::where('notice_of_award_number', $id)->firstOrFail();
        $this->pmuRecord = $record;

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

        if ($this->procurements->isEmpty() && $this->itemRows->isEmpty()) {
            LivewireAlert::warning('Not Found', 'No procurements found with this Notice of Award Number.');
            return redirect()->route('pmu.index');
        }

        $this->totalAbc = $this->procurements->sum('abc');
    }

    public function back()
    {
        return redirect()->route('pmu.index');
    }

    public function render()
    {
        return view('livewire.pmu.pmu-view-page')
            ->layout('components.layouts.app');
    }
}
