<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Store\PostsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestXController;
use App\Http\Controllers\NoteController;


Route::post('/create', [AuthController::class, 'createUser']);
Route::post('/login', [AuthController::class, 'loginUser']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logoutUser']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('users', [UserController::class, 'getAuthenticatedUser'])->name('users.getAuthenticatedUser'); //good
    //Articles
    Route::resource('articles', ArticleController::class);

});




























//Route::middleware('auth:sanctum')->post('/login', [AuthController::class, 'loginUser']);

/*Route::middleware('auth:sanctum')->post('/user', function (Request $request) {
    return $request->user();
});*/


//Route::post('/auth/register', [AuthController::class, 'createUser']);
/*Route::post('/auth/register',[AuthController::class, 'createUser']);
Route::get('/auth/login', [AuthController::class, 'loginUser']);
Route::middleware('auth:sanctum')->post('/note', [NoteController::class, 'store']);
*/
//Route::post('/auth/googleUser', [AuthController::class, 'googleUser']);
/*
Route::get('/auth/googleUser', [TestXController::class, 'store']);

Route::middleware('auth:sanctum')->get('/note', [NoteController::class, 'index']);
Route::middleware('auth:sanctum')->post('/note', [NoteController::class, 'store']);


//posts
Route::get('posts', [PostsController::class, 'index'])->name('posts.index');
Route::get('posts/{posts}', [PostsController::class, 'show'])->name('posts.show'); //good
Route::post('posts', [PostsController::class, 'store'])->name('posts.store'); //good
Route::put('posts/{posts}', [PostsController::class, 'update'])->name('posts.update'); //good
Route::delete('posts/{posts}', [PostsController::class, 'destroy'])->name('posts.delete');
*/
