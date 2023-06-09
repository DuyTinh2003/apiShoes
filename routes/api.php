<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\UserController;
use App\Models\Order;

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

Route::post('/login', [AuthController::class, "login"]);
Route::post('/register', [AuthController::class, "register"]);
Route::post('/refreshToken', [AuthController::class, "reFreshToken"]);
Route::put('/updateQuantityProduct', [ProductController::class, 'updateQuantityProduct']);
Route::resources([
    'product' => ProductController::class,
    'category' => CategoryController::class,
    'order' => OrderController::class,
    'user' => UserController::class
]);
Route::get('/getProductByCategory/{categoryId?}', [ProductController::class, 'getProductByCate']);
Route::get('/getOrderByIdUser/{UserId?}', [OrderController::class, 'getOrderByIdUser']);
Route::get('/getCartByIdUser/{UserId?}', [CartController::class, 'getCartByIdUser']);
Route::post('/updateCart', [CartController::class, 'updateCart']);
// Route::middleware(['auth:api', 'check_role'])->group(function () {
// });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });