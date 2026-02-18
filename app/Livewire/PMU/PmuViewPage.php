<?php

namespace App\Livewire\PMU;

use App\Models\Procurement;
use App\Models\PostProcurement;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class PmuViewPage extends Component
{
    public $noticeOfAwardNumber;
    public $procurements;
    public $postProcurement;
    public $totalAbc = 0;

    public function mount($id)
    {
        $this->noticeOfAwardNumber = $id;

        // Get all procurements with this notice_of_award_number and stage 7
        $this->procurements = Procurement::query()
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->where('post_procurements.notice_of_award_number', $this->noticeOfAwardNumber)
            ->where(function ($q) {
                $q->whereHas('prLotPrstages', function ($query) {
                    $query->where('pr_stage_id', 7);
                })
                    ->orWhereHas('prItemPrstages', function ($query) {
                        $query->where('pr_stage_id', 7);
                    });
            })
            ->with([
                'division',
                'category',
                'fundSource',
                'endUser',
                'postProcurement',
                'prLotPrstages.procurementStage',
                'prItemPrstages.stage'
            ])
            ->select('procurements.*')
            ->get();

        // Get post procurement info (from first record)
        if ($this->procurements->isNotEmpty()) {
            $this->postProcurement = $this->procurements->first()->postProcurement;
            $this->totalAbc = $this->procurements->sum('abc');
        }

        if ($this->procurements->isEmpty()) {
            LivewireAlert::warning('Not Found', 'No procurements found with this Notice of Award Number.');
            return redirect()->route('pmu.index');
        }
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
