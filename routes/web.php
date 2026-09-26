<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Bookmarks;
use App\Livewire\Home;
use App\Livewire\Juz;
use App\Livewire\Landing;
use App\Livewire\Legal\Privacy;
use App\Livewire\Legal\Terms;
use App\Livewire\Profile;
use App\Livewire\Reading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)->name('landing');

Route::get('/terms', Terms::class)->name('terms');
Route::get('/privacy', Privacy::class)->name('privacy');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/surah/{number}', Reading::class)->name('surah.show');
    Route::get('/juz/{number}', Juz::class)->name('juz.show');
    Route::get('/bookmarks', Bookmarks::class)->name('bookmarks');
    Route::get('/profile', Profile::class)->name('profile');

    Route::post('/logout', function () {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
