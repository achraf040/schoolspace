<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkerController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->middleware('login.ratelimit')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration route (if needed)
Route::get('/register', function() { return redirect()->route('login'); })->name('register');

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EspaceController;
use App\Http\Controllers\Admin\AttributionController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Espaces management
    Route::resource('espaces', EspaceController::class);
    Route::post('/espaces/generate-email', [EspaceController::class, 'generateEmail'])->name('espaces.generate-email');
    
    // Attributions management
    Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
    Route::get('/attributions/{attribution}', [AttributionController::class, 'show'])->name('attributions.show');
    Route::post('/attributions', [AttributionController::class, 'store'])->name('attributions.store');
    Route::put('/attributions/{attribution}', [AttributionController::class, 'update'])->name('attributions.update');
    Route::delete('/attributions/{attribution}', [AttributionController::class, 'destroy'])->name('attributions.destroy');
    
    // API pour récupérer les utilisateurs par espace
    Route::get('/api/users/by-space/{espace}', [AttributionController::class, 'getUsersBySpace'])->name('api.users.by-space');
    
});

// Worker routes
Route::middleware(['auth', 'worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [WorkerController::class, 'myTasks'])->name('tasks');
    Route::get('/history', [WorkerController::class, 'history'])->name('history');
    Route::get('/api/stats', [WorkerController::class, 'getStats'])->name('api.stats');
    Route::post('/tasks/{attribution}/update-status', [WorkerController::class, 'updateTaskStatus'])->name('tasks.update-status');
    Route::get('/tasks/{attribution}', [WorkerController::class, 'showTask'])->name('tasks.show');
});
