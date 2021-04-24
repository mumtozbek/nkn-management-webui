<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('providers', App\Http\Controllers\ProviderController::class)->middleware('auth');

Route::resource('accounts', App\Http\Controllers\AccountController::class)->middleware('auth');

Route::resource('nodes', App\Http\Controllers\NodeController::class)->middleware('auth');

Route::resource('ssh-keys', App\Http\Controllers\SshKeyController::class)->middleware('auth');
