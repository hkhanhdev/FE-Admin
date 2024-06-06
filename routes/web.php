<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route("Dashboard",'dashboard')
//    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route("Users-Management",'users-management')
//    ->middleware(['auth', 'verified'])
    ->name('usr-mng');

Volt::route("Products-Management",'products-management')
//    ->middleware(['auth', 'verified'])
    ->name('prd-mng');

Volt::route("Orders-Management",'orders-management')
//    ->middleware(['auth', 'verified'])
    ->name('ord-mng');

Volt::route("Manufacturers-Management",'manu-management')
//    ->middleware(['auth', 'verified'])
    ->name('mn-mng');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
