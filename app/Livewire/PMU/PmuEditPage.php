<?php

namespace App\Livewire\PMU;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

class PmuEditPage extends Component
{
    public $recordId;

    // Form fields
    public $name = '';
    public $description = '';
    public $status = 'active';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'name.required' => 'The name field is required.',
        'name.max' => 'The name must not exceed 255 characters.',
        'status.required' => 'The status field is required.',
    ];

    public function mount($id)
    {
        $this->recordId = $id;

        // TODO: Load the record from database
        // $record = YourModel::findOrFail($id);
        // $this->name = $record->name;
        // $this->description = $record->description;
        // $this->status = $record->status;
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // TODO: Update the record in database
            // $record = YourModel::findOrFail($this->recordId);
            // $record->update([
            //     'name' => $this->name,
            //     'description' => $this->description,
            //     'status' => $this->status,
            // ]);

            DB::commit();

            LivewireAlert::success('Success', 'Record updated successfully!');

            return redirect()->route('pmu.index');
        } catch (\Exception $e) {
            DB::rollBack();
            LivewireAlert::error('Error', 'Failed to update record: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('pmu.index');
    }

    public function render()
    {
        return view('livewire.pmu.pmu-edit-page')
            ->layout('components.layouts.app');
    }
}
