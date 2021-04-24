<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function() {
    return redirect()->route('login');
});

//RUTAS DE LOS CONTROLADORES
                //Se puede cambiar ej admin/cat         //Nombre ruta
Route::resource('categories', 'CategoryController')->names('categories');
Route::resource('clients', 'ClientController')->names('clients');
Route::resource('products', 'ProductController')->names('products');
Route::resource('providers', 'ProviderController')->names('providers');
Route::resource('purchases', 'PurchaseController')->names('purchases')->except([
    'update', 'edit'
]);



//Reportes
Route::get('report/reports_day', 'ReportController@reports_day')->name('reports.day');
Route::get('report/reports_date', 'ReportController@reports_date')->name('reports.date');

Route::post('report/report_results','ReportController@report_results')->name('report.results');

Route::resource('sales', 'SaleController')->names('sales')->except([
    'update', 'edit'
]);

//Ruta boleta
Route::get('sales/boleta/{sale}','SaleController@pdf_boleta')->name('sales.boleta');

Auth::routes();

Route::resource('business','BusinessController')->names('business')->only([
    'index', 'update'
]);

                                                        //funcion del controlador
Route::get('purchases/pdf/{purchase}','PurchaseController@pdf')->name('purchases.pdf');
Route::get('sales/pdf/{sale}','SaleController@pdf')->name('sales.pdf');

//Agregar imagen compra
Route::get('purchases/upload/{id}','PurchaseController@upload')->name('upload.purchases');

//Cambiar estado
Route::get('change_status/products/{product}', 'ProductController@change_status')->name('change.status.products');
Route::get('change_status/purchases/{purchase}', 'PurchaseController@change_status')->name('change.status.purchases');
Route::get('change_status/sales/{sale}', 'SaleController@change_status')->name('change.status.sales');


//Users
Route::resource('users','UserController')->names('users');

//Roles
Route::resource('roles','RoleController')->names('roles');

//Product code
Route::get('get_products_by_barcode', 'ProductController@get_products_by_barcode')->name('get_products_by_barcode');
Route::get('get_products_by_id', 'ProductController@get_products_by_id')->name('get_products_by_id');

Route::get('print_barcode', 'ProductController@get_bar_code')->name('print_barcode');

Auth::routes(['register' => false]);
Route::get('/home', 'HomeController@index')->name('home');

//Logout
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');