<?php

namespace App\Livewire\BacApprovedPr;

use App\Models\BacApprovedPr;
use App\Models\Procurement;
use Illuminate\Support\Facades\Validator;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create BAC Approved PR | PMIS')]
class BacApprovedPrCreatePage extends Component
{
    public $form = [];
    public $textareaRows = 1;
    public $procID;
    protected $layout = 'components.layouts.app';

    public function mount()
    {
        $this->resetForm();
    }

    private function defaultForm(): array
    {
        return [
            'pr_number' => '',
            'procurement_program_project' => '',
            'remarks' => '',
            'filepath' => '',
        ];
    }

    private function resetForm(): void
    {
        $this->form = $this->defaultForm();
        $this->resetErrorBag();
    }

    public function save()
    {
        // 1. Define validation rules
        $rules = [
            'form.pr_number' => 'required',
            'form.filepath' => 'required|url|max:255',
            'form.remarks' => 'nullable|string',
        ];
        $attributes = [
            'form.pr_number' => 'PR Number',
            'form.filepath' => 'Approved PR Document URL',
        ];

        // 2. Validate the data
        $this->validate($rules, [], $attributes);

        // 3. Check for duplicates
        $isAlreadySaved = BacApprovedPr::where('procID', $this->procID)->exists();
        if ($isAlreadySaved) {
            LivewireAlert::title('Error!')
                ->error()
                ->text('This PR has already been saved and cannot be added again.')
                ->toast()
                ->position('top-end')
                ->show();
            $this->addError('form.pr_number', 'This PR has already been recorded.');
            return;
        }

        // 4. Create the record in the database
        BacApprovedPr::create([
            'procID' => $this->procID,
            'filepath' => $this->form['filepath'],
            'remarks' => $this->form['remarks'],
        ]);

        // 5. Show success message
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Saved!',
            'message' => 'Your BAC Approved Procurement has been created successfully.',
        ]);

        return redirect()->route('bac-approved-pr.index');
    }

    public function updatedFormPrNumber($value)
    {
        // Clear the fields first
        $this->form['procurement_program_project'] = '';
        $this->procID = null;
        $this->textareaRows = 1;

        // If no value selected, return early
        if (empty($value)) {
            return;
        }

        // Find the procurement by procID
        $procurement = Procurement::where('procID', $value)->first();

        if ($procurement) {
            $this->form['procurement_program_project'] = $procurement->procurement_program_project;
            $this->procID = $procurement->procID;

            // Calculate textarea rows
            $text = trim($procurement->procurement_program_project ?? '');
            $lineCount = substr_count($text, "\n") + 1;
            $approxExtraLines = ceil(strlen($text) / 150);
            $this->textareaRows = max($lineCount, $approxExtraLines, 1);
        }
    }

    public function render()
    {
        $procurements = Procurement::whereDoesntHave('bacApprovedPr')
            ->orderBy('pr_number', 'desc')
            ->get();

        return view('livewire.bac-approved-pr.bac-approved-pr-create-page', [
            'procurements' => $procurements,
        ]);
    }
}
