<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\tienda\CartController;
use App\Http\Controllers\tienda\HomeController;
use App\Http\Controllers\tienda\ReviewController;
use App\Http\Controllers\tienda\CheckoutController;
use App\Http\Controllers\admin\curso\ClaseController;
use App\Http\Controllers\admin\curso\CursoController;
use App\Http\Controllers\admin\Coupon\CouponController;
use App\Http\Controllers\admin\curso\SectionController;
use App\Http\Controllers\admin\curso\CategoryController;
use App\Http\Controllers\tienda\ProfileClientController;
use App\Http\Controllers\admin\Discount\DiscountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
 
    'middleware' => 'api',
    'prefix' => 'auth'
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login_tienda', [AuthController::class, 'login_tienda'])->name('login_tienda');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});



Route::group([
 
    'middleware' => 'api',
 
], function ($router) {
    Route::resource('/user', UserController::class );
    Route::post('/user/{id}', [UserController::class, 'update'] )->name('update');
});


Route::group([
 
    'middleware' => 'api',
 
], function ($router) {
    Route::resource('/category', CategoryController::class );
    Route::post('/category/{id}', [CategoryController::class, 'update'] )->name('update');
});

Route::group([
 
    'middleware' => 'api',
 
], function ($router) {
    Route::get('/course/config', [CursoController::class, 'config'] )->name('config');
    Route::resource('/course', CursoController::class );
    Route::post('/course/upload_video/{id}', [CursoController::class, 'upload_video'] )->name('upload_video');
    Route::post('/course/{id}', [CursoController::class, 'update'] )->name('update');
    
    //secciones
    Route::resource('/course-section', SectionController::class );
    Route::put('/course-section/{id}', [SectionController::class, 'update'] )->name('update');
    
    //clases
    Route::resource('/course-clase', ClaseController::class );
    Route::put('/course-clase/{id}', [ClaseController::class, 'update'] )->name('update');
    
    //clase files
    Route::post('/course-clase-file', [ClaseController::class, 'addFiles'] )->name('addFiles');
    Route::delete('/course-clase-file/{id}', [ClaseController::class, 'removeFiles'] )->name('removeFiles');
    Route::post('/course-clase/upload_video/{id}', [CursoController::class, 'upload_video'] )->name('upload_video');
    
    //cupones
    Route::get('/coupon/config', [CouponController::class, 'config'] )->name('config');
    Route::resource('/coupon', CouponController::class );
    Route::put('/coupon/{id}', [CouponController::class, 'update'] )->name('update');
    
    //descuentos
    
    Route::resource('/descuento', DiscountController::class );
    Route::put('/descuento/{id}', [DiscountController::class, 'update'] )->name('update');
    
    
});

Route::group([
    
    'prefix' => 'ecommerce',
    
], function ($router) {
    Route::get('home', [HomeController::class, 'home'] )->name('home');
    Route::get('config_all', [HomeController::class, 'config_all'] )->name('config_all');
    Route::post('list_courses', [HomeController::class, 'listCourses'] )->name('listCourses');
    Route::get('course-detail/{slug}', [HomeController::class, 'course_detail'] )->name('course_detail');
    
    
    Route::group([
        'middleware' => 'api',
    ], function ($router) {
        Route::get('/course_lesson/{slug}', [HomeController::class, 'course_lesson'] )->name('course_lesson');
        Route::resource('/cart', CartController::class );
        Route::post('/checkout', [CheckoutController::class, 'store'] )->name('store');
        Route::post('/apply_coupon', [CartController::class, 'apply_coupon'] )->name('apply_coupon');
        Route::post('/profile', [ProfileClientController::class, 'profile'] )->name('profile');
        Route::post('/updateclient', [ProfileClientController::class, 'updateclient'] )->name('updateclient');
        
        Route::resource('/review', ReviewController::class );
        Route::put('/review/{id}', [ReviewController::class, 'update'] )->name('update');
    });

});



Route::get('/cache', function () {
    Artisan::call('cache:clear');
    return "Limpiar Cache";
});

Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    return "Optimización de Laravel";
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return "Storage Link";
});