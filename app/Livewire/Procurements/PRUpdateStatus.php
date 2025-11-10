<?php

namespace App\Livewire\Procurements;

use App\Models\PrItemRemark;
use App\Models\PrLotRemark;
use App\Models\Procurement;
use App\Models\ProcurementStage;
use App\Models\PrLotPrstage;
use App\Models\PrItemPrstage;
use App\Models\Division;
use App\Models\Remarks;
use App\Models\PrItem;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class PRUpdateStatus extends Component
{
    public Procurement $procurement;
    public $selectedStageId;
    public $remarksId;
    public $itemStages = [];
    public $itemRemarks = [];
    public $stages = [];
    public $divisions = [];
    public $remarks = [];
    public $showTable = true;
    public $form = [];

    public function mount(Procurement $procurement)
    {
        $this->procurement = $procurement->load('pr_items.prstage.stage', 'currentPrStage.procurementStage', 'division');
        $this->stages = ProcurementStage::where('is_active', true)->orderBy('procurementstage')->get();
        $this->divisions = Division::all();
        $this->remarks = Remarks::where('is_active', true)->orderBy('remarks')->get();

        // Initialize form array with procurement data
        $this->form = [
            'pr_number' => $this->procurement->pr_number,
            'procurement_program_project' => $this->procurement->procurement_program_project,
            'procurement_type' => $this->procurement->procurement_type,
            'divisions_id' => $this->procurement->divisions_id,
            'items' => $this->procurement->pr_items->toArray(),
        ];

        // Pre-select current stage for perLot
        if ($this->procurement->procurement_type === 'perLot' && $this->procurement->currentPrStage) {
            $this->selectedStageId = $this->procurement->currentPrStage->pr_stage_id;
        }

        // Initialize item stages with current values
        foreach ($this->procurement->pr_items as $item) {
            if ($item->prstage && $item->prstage->pr_stage_id) {
                $this->itemStages[$item->prItemID] = $item->prstage->pr_stage_id;
            }
        }
    }

    public function save()
    {
        if ($this->procurement->procurement_type === 'perLot') {
            // Validation for perLot
            $this->validate([
                'selectedStageId' => 'required|exists:procurement_stages,id',
                'remarksId' => 'nullable|exists:remarks,id',
            ], [
                'selectedStageId.required' => 'Please select a procurement stage.',
            ]);

            // Update stage for perLot
            PrLotPrstage::create([
                'procID' => $this->procurement->procID,
                'pr_stage_id' => $this->selectedStageId,
                'stage_history' => now(),
            ]);

            // Create remark history if provided
            if ($this->remarksId) {
                PrLotRemark::create([
                    'procID' => $this->procurement->procID,
                    'remarks_id' => $this->remarksId,
                    'remark_history' => now(),
                ]);
            }


            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Saved!',
                'message' => 'Procurement status updated successfully.',
            ]);

            return redirect()->route('procurements.index');

        } else {
            // Update for perItem - process all items
            $updatedCount = 0;

            foreach ($this->form['items'] as $item) {
                $itemId = $item['prItemID'];

                // Check if stage is selected for this item
                if (!empty($this->itemStages[$itemId])) {
                    $currentStageId = $item['prstage']['pr_stage_id'] ?? null;

                    // Create new stage record if changed
                    if ($this->itemStages[$itemId] != $currentStageId) {
                        PrItemPrstage::create([
                            'procID' => $this->procurement->procID, // Added this line
                            'prItemID' => $itemId,
                            'pr_stage_id' => $this->itemStages[$itemId],
                            'stage_history' => now(),
                        ]);
                        $updatedCount++;
                    }
                }

                // Create item remark history if provided
                if (!empty($this->itemRemarks[$itemId])) {
                    PrItemRemark::create([
                        'procID' => $this->procurement->procID,
                        'prItemID' => $itemId,
                        'remarks_id' => $this->itemRemarks[$itemId],
                        'remark_history' => now(),
                    ]);
                }
            }

            if ($updatedCount > 0) {

                session()->flash('alert', [
                    'type' => 'success',
                    'title' => 'Saved!',
                    'message' => $updatedCount . ' item(s) status updated successfully.',
                ]);

                return redirect()->route('procurements.index');
            } else {
                LivewireAlert::info()
                    ->title('No Changes')
                    ->text('No stage changes were made.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
        }
    }

    public function cancel()
    {
        return redirect()->route('procurements.index');
    }

    public function render()
    {
        return view('livewire.procurements.p-r-update-status');
    }
}
