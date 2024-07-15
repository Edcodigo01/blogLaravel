<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
// $this->middleware('auth:api', ['except' => ['login','register','validateToken']]);
Route::middleware(['cors'])->group(function () {

    Route::prefix('admin')->group(function () {
        // UPLOAD IMAGE TinyMCE
        Route::middleware(['auth:api'])->group(function () {
            Route::resource('user', 'userController');

            Route::post('refresh-token', 'AuthController@refresh');
            Route::post('validate-token', 'AuthController@validateToken');

            Route::get('articles', 'Admin\ArticleController@index');
            Route::get('article/create', 'Admin\ArticleController@create');
            Route::post('article/store', 'Admin\ArticleController@store');
            Route::get('article/{slug}/edit', 'Admin\ArticleController@edit');
            Route::put('article/{slug}/update', 'Admin\ArticleController@update');
            Route::put('article/{id}/disable', 'Admin\ArticleController@disable');
            Route::put('article/{id}/enable', 'Admin\ArticleController@enable');
            Route::delete('article/{id}/delete', 'Admin\ArticleController@delete');

            Route::post('upload-image-tinymce', 'Admin\ArticleController@uploadImageEditor');

            Route::get('videos', 'Admin\VideoController@index');
            Route::get('video/create', 'Admin\VideoController@create');

            Route::post('video/store', 'Admin\VideoController@store');
            Route::get('video/{id}/edit', 'Admin\VideoController@edit');

            Route::put('video/{id}/update', 'Admin\VideoController@update');
            Route::delete('video/{id}/delete', 'Admin\VideoController@delete');



            Route::get('categories', 'Admin\CategoryController@index');
            Route::get('categories/create', 'Admin\CategoryController@create');
            Route::post('categories/store', 'Admin\CategoryController@store');
            Route::get('categories/{slug}/edit', 'Admin\CategoryController@edit');
            Route::put('categories/{slug}/update', 'Admin\CategoryController@update');
            Route::delete('categories/{id}/delete', 'Admin\CategoryController@delete');
        });

        Route::post('login', 'AuthController@login')->name('login');
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/',function(){
            if(Auth::check()){
                return 'Logueado';
            }else{
                return 'No logueado';
            }
        })->name('home');

    });

    Route::get('blog', 'BlogController@index');
    Route::get('videos', 'VideoController@index');

    Route::get('blog/{category}/{article}', 'BlogController@article')->name('article');

    Route::get('means-navbar', 'navbarController@index');
    Route::get('articles/last', 'blogController@lastArticles');

    Route::post('mails/contact', 'contactController@mail_contact');





});




