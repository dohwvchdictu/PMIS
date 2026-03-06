<?php

namespace App\Livewire\ScheduleForPr;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Schedule for PR | PMIS')]
class ScheduleForPrViewPage extends Component
{
    public function render()
    {
        return view('livewire.schedule-for-pr.schedule-for-pr-view-page');
    }
}
