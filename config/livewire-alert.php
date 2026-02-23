<?php

/*
 * For more details about the configuration, see:
 * https://sweetalert2.github.io/#configuration
 */

use Jantinnerezo\LivewireAlert\Enums\Position;

return [
    'position' => Position::Center,
    'timer' => 3000,
    'toast' => false,
    'text' => null,
    'confirmButtonText' => 'Yes',
    'cancelButtonText' => 'Cancel',
    'denyButtonText' => 'No',
    'showCancelButton' => false,
    'showConfirmButton' => false,
    'backdrop' => true,

    // Custom configuration to ensure alerts appear above modals (z-index: 99999)
    'customClass' => [
        'container' => 'swal-z-index-max',
    ],
];
