<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\TokenAPIAuthenticate;
use App\Http\Middleware\TokenWebAuthenticate;

//todo: Brand List
Route::post('/BrandCreate', [BrandController::class, 'BrandCreate'])->middleware([TokenWebAuthenticate::class]);
Route::get('/BrandList', [BrandController::class, 'BrandList']);
Route::get('/BrandEdit/{id}', [BrandController::class, 'BrandEdit'])->middleware([TokenWebAuthenticate::class]);
Route::post('/BrandUpdate', [BrandController::class, 'BrandUpdate'])->middleware([TokenWebAuthenticate::class]);
Route::post('/BrandDelete', [BrandController::class, 'BrandDelete'])->middleware([TokenWebAuthenticate::class]);
//todo: Category List

Route::get('/CategoryList', [CategoryController::class, 'CategoryList']);

// Product List
Route::get('/ListProductByCategory/{id}', [ProductController::class, 'ListProductByCategory']);
Route::get('/ListProductByBrand/{id}', [ProductController::class, 'ListProductByBrand']);
Route::get('/ListProductByRemark/{remark}', [ProductController::class, 'ListProductByRemark']);
// Slider
Route::get('/ListProductSlider', [ProductController::class, 'ListProductSlider']);
// Product Details
Route::get('/ProductDetailsById/{id}', [ProductController::class, 'ProductDetailsById']);
Route::get('/ListReviewByProduct/{product_id}', [ProductController::class, 'ListReviewByProduct']);
//policy
Route::get("/PolicyByType/{type}",[PolicyController::class,'PolicyByType']);


//todo: User Auth
Route::get('/UserLogin/{UserEmail}', [UserController::class, 'UserLogin']);
Route::get('/VerifyLogin/{UserEmail}/{OTP}', [UserController::class, 'VerifyLogin']);
Route::get('/logout',[UserController::class,'UserLogout']);


//todo: User Profile
Route::post('/CreateProfile', [ProfileController::class, 'CreateProfile'])->middleware([TokenWebAuthenticate::class]);
Route::get('/ReadProfile', [ProfileController::class, 'ReadProfile'])->middleware([TokenWebAuthenticate::class]);


//todo: Product Review
Route::get('/ListReviewByProduct/{product_id}',[ProductController::class,'ListReviewByProduct']);
Route::post('/CreateProductReview', [ProductController::class, 'CreateProductReview'])->middleware([TokenWebAuthenticate::class]);


//todo: Product Wish
Route::get('/ProductWishList', [ProductController::class, 'ProductWishList'])->middleware([TokenWebAuthenticate::class]);
Route::get('/CreateWishList/{product_id}', [ProductController::class, 'CreateWishList'])->middleware([TokenWebAuthenticate::class]);
Route::get('/RemoveWishList/{product_id}', [ProductController::class, 'RemoveWishList'])->middleware([TokenWebAuthenticate::class]);


//todo: Product Cart
Route::post('/CreateCartList', [ProductController::class, 'CreateCartList'])->middleware([TokenWebAuthenticate::class]);
Route::get('/CartList', [ProductController::class, 'CartList'])->middleware([TokenWebAuthenticate::class]);
Route::get('/DeleteCartList/{product_id}', [ProductController::class, 'DeleteCartList'])->middleware([TokenWebAuthenticate::class]);


//todo Invoice and payment
Route::get("/InvoiceCreate",[InvoiceController::class,'InvoiceCreate'])->middleware([TokenWebAuthenticate::class]);
Route::get("/InvoiceList",[InvoiceController::class,'InvoiceList'])->middleware([TokenWebAuthenticate::class]);
Route::get("/InvoiceProductList/{invoice_id}",[InvoiceController::class,'InvoiceProductList'])->middleware([TokenWebAuthenticate::class]);


//todo: payment
Route::post("/PaymentSuccess",[InvoiceController::class,'PaymentSuccess']);
Route::post("/PaymentCancel",[InvoiceController::class,'PaymentCancel']);
Route::post("/PaymentFail",[InvoiceController::class,'PaymentFail']);
Route::post('/PaymentIPN',[InvoiceController::class,'PaymentIPN']);

