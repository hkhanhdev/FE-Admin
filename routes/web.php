<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route("Dashboard",'dashboard')
    ->middleware(['authenticated'])
    ->name('dashboard');

Volt::route("Users-Management",'users-management')
    ->middleware(['authenticated'])
    ->name('usr-mng');

Volt::route("Products-Management",'products-management')
    ->middleware(['authenticated'])
    ->name('prd-mng');

Volt::route("Orders-Management",'orders-management')
    ->middleware(['authenticated'])
    ->name('ord-mng');

Volt::route("Manufacturers-Management",'manu-management')
    ->middleware(['authenticated'])
    ->name('mn-mng');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
