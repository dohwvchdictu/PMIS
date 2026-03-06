<?php

namespace App\Livewire\ModeOfProcurement;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mode of Procurement | PMIS')]
class ModeOfProcurementEditPage extends Component
{
    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-edit-page');
    }
}
