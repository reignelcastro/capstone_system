<?php

use Illuminate\Support\Facades\Route;
use Codedge\Fpdf\Fpdf\Fpdf;
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
Route::get('/error404', function () {return view('error404');});
Route::get('/', function () {
    return view('auth.login');
});

/* ADDRESS ROUTES */
Route::post('address/provinces','AddressController@provinces')->name('provinces');
Route::post('address/cm','AddressController@cm')->name('cm');
Route::post('address/brgy','AddressController@brgy')->name('brgy');
/* ADDRESS ROUTES */

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('/services', 'ServicesController@index')->name('aics-services')->middleware('ifadmin');
Route::post('/services/store', 'ServicesController@store')->name('services-store');
Route::post('/services/save', 'ServicesController@save')->name('services-save');
Route::post('/services/requirements/load', 'ServicesController@load')->name('requirements-load');
Route::post('/services/delete', 'ServicesController@delete')->name('services-delete');
Route::post('/services/requirement/remove', 'ServicesController@remove')->name('requirement-remove');
Route::post('/services/flowchart/save', 'ServicesController@flowchart')->name('flowchart-save');
Route::post('/services/flowchart/upload', 'ServicesController@upload')->name('flowchart-file-upload');



Route::get('/beneficiaries', 'RequestsController@index')->name('beneficiaries')->middleware('ifadmin');
Route::post('/beneficiaries/details', 'RequestsController@details')->name('details-view');
Route::post('/beneficiaries/delete', 'RequestsController@delete')->name('beneficiary-delete');
Route::post('/beneficiaries/archive', 'RequestsController@archive')->name('beneficiary-archive');
Route::post('/application/archive', 'RequestsController@archives')->name('application-archive');
Route::post('/application/trash', 'RequestsController@trash')->name('application-trash');
Route::post('/application/update', 'RequestsController@update')->name('update-application');

Route::post('/send-sms', 'RequestsController@send')->name('send-sms');

Route::get('/archived', 'RequestsController@admin')->name('archived-admin')->middleware('ifadmin');



Route::get('/requests', 'RequestsController@index')->name('requests')->middleware('ifadmin');
Route::get('/requests-status', 'RequestsController@index')->name('requests-status')->middleware('ifadmin');
Route::get('/send-sms', 'RequestsController@index')->name('send-sms')->middleware('ifadmin');


Route::get('/beneficiaries/new', 'RequestsController@new')->name('request-new')->middleware('ifadmin');
Route::post('/requests/store', 'RequestsController@store')->name('requests-store');
Route::post('/beneficiary/save', 'RequestsController@save')->name('beneficiary-save');
Route::post('/dependents/load', 'RequestsController@load')->name('dependents-load');
Route::post('/dependent/remove', 'RequestsController@remove')->name('dependent-remove');

Route::post('/services/check', 'RequestsController@check')->name('services-check');
Route::post('/services/verify', 'RequestsController@verify')->name('services-verify');
Route::post('/services/upload', 'RequestsController@upload')->name('upload-files');
Route::post('/services/application/save', 'RequestsController@application_save')->name('application-save');


Route::get('/submit-requests', 'RequestsController@submit')->name('submit-requests')->middleware('ifadmin');
Route::post('/submit/application', 'RequestsController@submit_application')->name('submit-application');


Route::get('/print-reports', 'RequestsController@submit')->name('print-reports')->middleware('ifadmin');
Route::get('/print-requests', 'RequestsController@submit')->name('print-reports')->middleware('ifadmin');
Route::get('/archives', 'RequestsController@archived')->name('archived')->middleware('ifadmin');


Route::get('/view-details/{beneficiary_id}', 'RequestsController@view_details')->name('view-details')->middleware('ifadmin');


Route::get('/user-accounts', 'AccountsController@accounts')->name('user-accounts')->middleware('ifadmin');
Route::post('/user-accounts/save', 'AccountsController@account_save')->name('user-account-save');
Route::post('/user-accounts/update', 'AccountsController@account_update')->name('user-account-update');


Route::get('/my-account', 'AccountsController@index')->name('account-index')->middleware('ifadmin');
Route::post('/admin/account/update', 'AccountsController@update')->name('account-update');
Route::post('/admin/account/verify', 'AccountsController@verify')->name('account-verify');
Route::post('/admin/account/upload', 'AccountsController@upload')->name('account-image-upload');

Route::get('/offices', 'LocationsController@offices')->name('locations')->middleware('ifnotadmin')->middleware('ifadmin');
Route::post('/office/save', 'LocationsController@store')->name('office-save');
Route::post('/office/delete', 'LocationsController@delete')->name('office-delete');

Route::get('/admin/location', 'LocationsController@index')->name('location')->middleware('ifnotadmin');
Route::post('/admin/location/save', 'LocationsController@save')->name('location-save');

Route::get('/admin/register/user', 'RegistrationsController@index')->name('registration')->middleware('ifnotadmin');
Route::get('/admin/register/user/add', 'RegistrationsController@add')->name('registration-add');
Route::post('/admin/register/user/store', 'RegistrationsController@store')->name('registration-store');
Route::get('/admin/register/user/{id}', 'RegistrationsController@view')->name('registration-view');
Route::get('/admin/register/user/{id}/logs', 'RegistrationsController@logs')->name('registration-logs');
Route::get('/admin/register/user/{id}/logs/print', 'RegistrationsController@print')->name('registration-print');
Route::get('/admin/register/user/{id}/edit', 'RegistrationsController@edit')->name('registration-edit');

Route::post('/admin/register/user/update', 'RegistrationsController@update')->name('registration-update');
Route::post('/admin/register/user/delete', 'RegistrationsController@delete')->name('registration-delete');
Route::post('/admin/register/user/change', 'RegistrationsController@change')->name('registration-license-update');


Route::get('/admin/passenger', 'PassengersController@index')->name('passengers')->middleware('ifnotadmin');
Route::get('/admin/passenger/add', 'PassengersController@add')->name('passengers-add');
Route::post('/admin/passenger/store', 'PassengersController@store')->name('passenger-store');
Route::get('/admin/passenger/{id}', 'PassengersController@view')->name('passenger-view');
Route::get('/admin/passenger/{id}/edit', 'PassengersController@edit')->name('passenger-edit');
Route::get('/admin/passenger/{id}/logs', 'PassengersController@logs')->name('passenger-logs');
Route::post('/admin/passenger/update', 'PassengersController@update')->name('passenger-update');

Route::post('/admin/passenger/delete', 'PassengersController@delete')->name('passenger-delete');
Route::post('/admin/passenger/change', 'PassengersController@change')->name('passenger-profile-update');


Route::get('/admin/scan', 'ScannersController@index')->name('scanner');
Route::get('/admin/scan/{option}', 'ScannersController@scan')->name('scanner-options');
Route::post('/admin/scan/save', 'ScannersController@save')->name('scanner-save');
Route::post('/admin/scan/load', 'ScannersController@load')->name('scanner-load');
Route::post('/admin/scan/count', 'ScannersController@count')->name('scanner-count');

Route::get('/admin/print/passenger/{id}', 'PdfController@view')->name('passengers-to-print');
Route::get('/admin/logs/passenger/{id}', 'PdfController@logs')->name('passengers-to-logs');
