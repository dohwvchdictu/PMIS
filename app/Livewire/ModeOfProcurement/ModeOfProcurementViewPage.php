<?php

namespace App\Livewire\ModeOfProcurement;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('View Mode of Procurement | PMIS')]
class ModeOfProcurementViewPage extends Component
{
    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-view-page');
    }
}
