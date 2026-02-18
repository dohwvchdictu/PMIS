<?php

namespace App\Livewire\PMU;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

class PmuCreatePage extends Component
{
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

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // TODO: Replace with your actual model creation
            // YourModel::create([
            //     'name' => $this->name,
            //     'description' => $this->description,
            //     'status' => $this->status,
            // ]);

            DB::commit();

            LivewireAlert::success('Success', 'Record created successfully!');

            return redirect()->route('pmu.index');
        } catch (\Exception $e) {
            DB::rollBack();
            LivewireAlert::error('Error', 'Failed to create record: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('pmu.index');
    }

    public function render()
    {
        return view('livewire.pmu.pmu-create-page')
            ->layout('components.layouts.app');
    }
}
