<?php

namespace App\Livewire;

use App\Models\Procurement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Mode of Procurement | PMIS')]
class ModeOfProcurementPage extends Component
{
    use WithPagination;

    public $perPage = 10;
    public function render()
    {
        $query = Procurement::query()->latest();

        return view('livewire.mode-of-procurement-page', [
            'procurements' => $query->paginate($this->perPage),
        ]);
    }
}
