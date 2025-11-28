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
    public $lotNotes;
    public $itemStages = [];
    public $itemRemarks = [];
    public $itemNotes = [];
    public $stages = [];
    public $divisions = [];
    public $remarks = [];
    public $showTable = true;
    public $form = [];

    // 1. ADD THIS PROPERTY to store page, search, filters, etc.
    public $queryParams = [];

    public function mount(Procurement $procurement)
    {
        $this->queryParams = request()->query();

        $this->procurement = $procurement->load([
            'pr_items.prstage.stage',
            'pr_items.currentItemRemark.remark',
            'currentPrStage.procurementStage',
            'currentLotRemark.remark',
            'division'
        ]);

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

        // Pre-select current stage and remark for perLot
        if ($this->procurement->procurement_type === 'perLot') {
            if ($this->procurement->currentPrStage) {
                $this->selectedStageId = $this->procurement->currentPrStage->pr_stage_id;
            }

            if ($this->procurement->currentLotRemark) {
                $this->remarksId = $this->procurement->currentLotRemark->remarks_id;
                $this->lotNotes = $this->procurement->currentLotRemark->notes; // Add this line
            }
        }

        // Initialize item stages and remarks with current values
        foreach ($this->procurement->pr_items as $item) {
            // Set current stage
            if ($item->prstage && $item->prstage->pr_stage_id) {
                $this->itemStages[$item->prItemID] = $item->prstage->pr_stage_id;
            }

            // Set current remark and notes
            if ($item->currentItemRemark) {
                if ($item->currentItemRemark->remarks_id) {
                    $this->itemRemarks[$item->prItemID] = $item->currentItemRemark->remarks_id;
                }
                // Add this line to load existing notes
                $this->itemNotes[$item->prItemID] = $item->currentItemRemark->notes ?? '';
            } else {
                // Initialize empty notes if no remark exists
                $this->itemNotes[$item->prItemID] = '';
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
                'lotNotes' => 'nullable|string|max:5000',
            ], [
                'selectedStageId.required' => 'Please select a procurement stage.',
                'lotNotes.max' => 'Notes cannot exceed 5000 characters.',
            ]);

            // Update stage for perLot
            PrLotPrstage::create([
                'procID' => $this->procurement->procID,
                'pr_stage_id' => $this->selectedStageId,
                'stage_history' => now(),
            ]);

            // Create remark history if remark is provided OR if notes exist
            if ($this->remarksId || !empty($this->lotNotes)) {
                PrLotRemark::create([
                    'procID' => $this->procurement->procID,
                    'remarks_id' => $this->remarksId ?: null, // Explicitly set to null if empty
                    'notes' => $this->lotNotes,
                    'remark_history' => now(),
                ]);
            }

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Saved!',
                'message' => 'Procurement status updated successfully.',
            ]);

            return redirect()->route('procurements.index', $this->queryParams);

        } else {
            // Update for perItem - process all items
            $updatedCount = 0;
            $remarksCount = 0;

            foreach ($this->form['items'] as $item) {
                $itemId = $item['prItemID'];

                // Check if stage is selected for this item
                if (!empty($this->itemStages[$itemId])) {
                    $currentStageId = $item['prstage']['pr_stage_id'] ?? null;

                    // Create new stage record if changed
                    if ($this->itemStages[$itemId] != $currentStageId) {
                        PrItemPrstage::create([
                            'procID' => $this->procurement->procID,
                            'prItemID' => $itemId,
                            'pr_stage_id' => $this->itemStages[$itemId],
                            'stage_history' => now(),
                        ]);
                        $updatedCount++;
                    }
                }

                // Create item remark history if remark is provided OR if notes exist
                if (!empty($this->itemRemarks[$itemId]) || !empty($this->itemNotes[$itemId])) {
                    PrItemRemark::create([
                        'procID' => $this->procurement->procID,
                        'prItemID' => $itemId,
                        'remarks_id' => !empty($this->itemRemarks[$itemId]) ? $this->itemRemarks[$itemId] : null,
                        'notes' => $this->itemNotes[$itemId] ?? null,
                        'remark_history' => now(),
                    ]);
                    $remarksCount++;
                }
            }

            // Check if any changes were made (stages OR remarks/notes)
            if ($updatedCount > 0 || $remarksCount > 0) {
                $messages = [];

                if ($updatedCount > 0) {
                    $messages[] = $updatedCount . ' item(s) status updated';
                }

                if ($remarksCount > 0) {
                    $messages[] = $remarksCount . ' note(s)/remark(s) added';
                }

                session()->flash('alert', [
                    'type' => 'success',
                    'title' => 'Saved!',
                    'message' => implode(' and ', $messages) . ' successfully.',
                ]);

                return redirect()->route('procurements.index', $this->queryParams);
            } else {
                LivewireAlert::info()
                    ->title('No Changes')
                    ->text('No stage, remark, or note changes were made.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
        }
    }

    public function cancel()
    {
        return redirect()->route('procurements.index', $this->queryParams);
    }

    public function render()
    {
        return view('livewire.procurements.p-r-update-status');
    }
}
