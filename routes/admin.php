<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MainCategoryController;
use Illuminate\Support\Facades\Auth;
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

// define('PAGINATION_COUNT', 10);
define('PAGINATION_COUNT',10);

Route::group(['namespace' => 'Admin', 'middleware' => 'auth:admin'], function () {

    Route::get('/', [DashboardController::class, 'index']) ->name('admin.dashboard');

    ########################### Start Languages Group #########################

    Route::group(['prefix' => 'languages'], function () {
        Route::get('/', [LanguageController::class, 'index']) -> name('admin.languages');

        Route::get('create', [LanguageController::class, 'create']) -> name('admin.languages.create');
        Route::post('store', [LanguageController::class, 'store']) -> name('admin.languages.store');

        Route::get('edit/{id}', [LanguageController::class, 'edit']) -> name('admin.languages.edit');
        Route::post('update/{id}', [LanguageController::class, 'update']) -> name('admin.languages.update');

        Route::get('destroy/{id}', [LanguageController::class, 'destroy']) -> name('admin.languages.destroy');


    }); 

    ########################### End Languages Group #########################

    ########################### Start Main Categories Group #########################

    Route::group(['prefix' => 'main_categories'], function () {
        Route::get('/', [MainCategoryController::class, 'index']) -> name('admin.maincategories');

        Route::get('create', [MainCategoryController::class, 'create']) -> name('admin.maincategories.create');
        Route::post('store', [MainCategoryController::class, 'store']) -> name('admin.maincategories.store');

        Route::get('edit/{id}', [MainCategoryController::class, 'edit']) -> name('admin.maincategories.edit');
        Route::post('update/{id}', [MainCategoryController::class, 'update']) -> name('admin.maincategories.update');

        Route::get('destroy/{id}', [MainCategoryController::class, 'destroy']) -> name('admin.maincategories.destroy');


    }); 

    ########################### End Main Categories Group #########################

    ########################### Start vendors Group #########################

    Route::group(['prefix' => 'vendors'], function () {
        Route::get('/', [VendorsController::class, 'index']) -> name('admin.vendors');

        Route::get('create', [VendorsController::class, 'create']) -> name('admin.vendors.create');
        Route::post('store', [VendorsController::class, 'store']) -> name('admin.vendors.store');

        Route::get('edit/{id}', [VendorsController::class, 'edit']) -> name('admin.vendors.edit');
        Route::post('update/{id}', [VendorsController::class, 'update']) -> name('admin.vendors.update');

        Route::get('destroy/{id}', [VendorsController::class, 'destroy']) -> name('admin.vendors.destroy');


    }); 

    ########################### End vendors Group #########################
});

Route::group([ 'namespace' => 'Admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', [LoginController::class, 'getLogin'])->name('get.admin.login');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
}); 
