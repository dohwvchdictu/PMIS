<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Services\ApiService;

class Navbar extends Component
{
    public $user;
    public $userPhoto;

    public function mount()
    {
        $this->user = session('user');

        if (is_array($this->user)) {
            $apiService = new ApiService();
            $this->userPhoto = $apiService->fetchUserPhoto($this->user)
                ?? asset('storage/employees/default.png');
        } else {
            $this->userPhoto = asset('storage/employees/default.png');
        }

        session(['user_photo' => $this->userPhoto]);
    }

    public function render()
    {
        return view('livewire.partials.navbar');
    }
}
