<?php

namespace App\Livewire\BacApprovedPr;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('View BAC Approved PR | PMIS')]
class BacApprovedPrViewPage extends Component
{
    public function render()
    {
        return view('livewire.bac-approved-pr.bac-approved-pr-view-page');
    }
}
