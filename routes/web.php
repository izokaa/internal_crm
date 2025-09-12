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


use Livewire\Livewire;

Livewire::setScriptRoute(function ($handle) {
    return Route::get('/vendor/public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/vendor/public/livewire/update', $handle);
});
