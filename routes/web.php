<?php

use App\Livewire\AcceptInvite;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin/login');
});


Route::middleware('signed')
        ->get('/invitation/{invitationId}/accept', AcceptInvite::class)
        ->name('invitation.accept');
