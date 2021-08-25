<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {
     Route::post('login', [AuthController::class, 'login'])->name('login');
     Route::post('registerAdmin', [AuthController::class, 'registerAdmin']);
     Route::post('registerUser', [AuthController::class, 'registerUser']);
     Route::post('loginUser', [AuthController::class, 'loginUser'])->name('loginUser');
     Route::group([
        'middleware' => 'auth:api'
      ], function() {
          Route::get('logout', [AuthController::class,'logout']);
          Route::get('user', [AuthController::class, 'user']);
   });
});

Route::get('view', [ManageController::class, 'adminView'])->name('all_view');;
Route::post('add', [ManageController::class, 'adminAdd'])->name('admin_add');;
Route::post('delete', [ManageController::class, 'adminDelete'])->name('admin_delete');;

//not used
Route::post('view/teacher', [UserController::class, 'teacherView'])->name('teacherView');
Route::post('view/student', [UserController::class, 'studentView'])->name('studentView');


// Route::get('chat', [ChatController::class, 'chat']);
// Route::post('send', [ChatController::class, 'send']);
