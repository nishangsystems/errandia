<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Auth\CustomForgotPasswordController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\documentation\BaseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/clear', function () {
    echo Session::get('applocale');
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

});

Route::get('reset_pass', function(){
    \App\Models\User::where('id', 1)->update(['password'=>\Illuminate\Support\Facades\Hash::make('password')]);
});
Route::get('set_local/{lang}', [Controller::class, 'set_local'])->name('lang.switch');

Route::post('login', [CustomLoginController::class, 'login'])->name('login.submit');
Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::get('register', [CustomLoginController::class, 'register'])->name('register');
Route::post('register', [CustomLoginController::class, 'signup']);
Route::post('logout', [CustomLoginController::class, 'logout'])->name('logout');

Route::post('reset_password_with_token/password/reset', [CustomForgotPasswordController::class, 'validatePasswordRequest'])->name('reset_password_without_token');
Route::get('reset_password_with_token/{token}/{email}', [CustomForgotPasswordController::class, 'resetForm'])->name('reset');
Route::post('reset_password_with_token', [CustomForgotPasswordController::class, 'resetPassword'])->name('reset_password_with_token');

Route::get('', 'WelcomeController@home');
Route::get('home', 'WelcomeController@home');


Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {

    Route::get('', 'Admin\HomeController@index')->name('home');
    Route::get('home', 'Admin\HomeController@index')->name('home');

    Route::prefix('businesses')->name('businesses.')->group(function(){
        Route::get('', [AdminHomeController::class, 'businesses'])->name('index');
        Route::get('create', [AdminHomeController::class, 'create_business'])->name('create');
        Route::post('create', [AdminHomeController::class, 'save_business']);
        Route::get('{slug}/show', [AdminHomeController::class, 'show_business'])->name('show');
        Route::get('{slug}/owner', [AdminHomeController::class, 'show_business_owner'])->name('show_owner');
        Route::get('{slug}/edit', [AdminHomeController::class, 'edit_business'])->name('edit');
        Route::post('{slug}/edit', [AdminHomeController::class, 'update_business']);
        Route::get('{slug}/delete', [AdminHomeController::class, 'delete_business'])->name('delete');
        Route::get('{slug}/suspend', [AdminHomeController::class, 'suspend_business'])->name('suspend');
        Route::get('{slug}/verify', [AdminHomeController::class, 'verify_business'])->name('verify');
        Route::get('{slug}/branches', [AdminHomeController::class, 'business_branches'])->name('branch.index');
        Route::get('{slug}/create_branch', [AdminHomeController::class, 'create_business_branch'])->name('branch.create');
        Route::post('{slug}/create_branch', [AdminHomeController::class, 'save_business_branch']);
    });

    Route::prefix('errands')->name('errands.')->group(function(){
        Route::get('', [AdminHomeController::class, 'errands'])->name('index');
        Route::get('delete/{slug}', [AdminHomeController::class, 'delete_errand'])->name('delete');
    });
    Route::prefix('services')->name('services.')->group(function(){
        Route::get('', [AdminHomeController::class, 'services'])->name('index');
        Route::get('show/{service}', [AdminHomeController::class, 'show_service'])->name('show');
        Route::get('create', [AdminHomeController::class, 'create_service'])->name('create');
    });
    Route::prefix('products')->name('products.')->group(function(){
        Route::get('', [AdminHomeController::class, 'products'])->name('index');
        Route::get('show/{product}', [AdminHomeController::class, 'show_product'])->name('show');
        Route::get('create', [AdminHomeController::class, 'create_products'])->name('create');
    });
    Route::prefix('categories')->name('categories.')->group(function(){
        Route::get('', [AdminHomeController::class, 'categories'])->name('index');
        Route::get('subcategories', [AdminHomeController::class, 'sub_categories'])->name('sub_categories');
        Route::get('subcategories/create', [AdminHomeController::class, 'create_sub_category'])->name('sub_categories.create');
        Route::get('create', [AdminHomeController::class, 'create_category'])->name('create');
    });
    Route::prefix('locations')->name('locations.')->group(function(){
        Route::get('streets', [AdminHomeController::class, 'streets'])->name('streets');
        Route::get('streets/create', [AdminHomeController::class, 'create_street'])->name('streets.create');
        Route::post('streets/create', [AdminHomeController::class, 'save_street']);
        Route::get('streets/{slug}/edit', [AdminHomeController::class, 'edit_street'])->name('streets.edit');
        Route::post('streets/{slug}/edit', [AdminHomeController::class, 'update_street']);
        Route::get('streets/{slug}/delete', [AdminHomeController::class, 'delete_street'])->name('streets.delete');
        Route::get('towns', [AdminHomeController::class, 'towns'])->name('towns');
        Route::get('towns/create', [AdminHomeController::class, 'create_town'])->name('towns.create');
        Route::post('towns/create', [AdminHomeController::class, 'save_town']);
        Route::get('towns/{slug}/edit', [AdminHomeController::class, 'edit_town'])->name('towns.edit');
        Route::post('towns/{slug}/edit', [AdminHomeController::class, 'update_town']);
        Route::get('towns/{slug}/delete', [AdminHomeController::class, 'delete_town'])->name('towns.delete');
    });
    Route::prefix('reviews')->name('reviews.')->group(function(){
        Route::get('', [AdminHomeController::class, 'reviews'])->name('index');
    });
    Route::prefix('users')->name('users.')->group(function(){
        Route::get('', [AdminHomeController::class, 'users'])->name('index');
        Route::get('create', [AdminHomeController::class, 'create_user'])->name('create');
    });
    Route::prefix('admins')->name('admins.')->group(function(){
        Route::get('', [AdminHomeController::class, 'admins'])->name('index');
        Route::get('roles', [AdminHomeController::class, 'roles'])->name('roles');
    });
    Route::prefix('plans')->name('plans.')->group(function(){
        Route::get('', [AdminHomeController::class, 'subscription_plans'])->name('index');
        Route::get('create', [AdminHomeController::class, 'create_subscription_plan'])->name('create');
    });
    Route::prefix('sms_bundles')->name('sms_bundles.')->group(function(){
        Route::get('', [AdminHomeController::class, 'sms_bundles'])->name('index');
    });
    Route::prefix('reports')->name('reports.')->group(function(){
        Route::get('sms', [AdminHomeController::class, 'sms_reports'])->name('sms');
        Route::get('subscriptions', [AdminHomeController::class, 'subscription_report'])->name('subscription');
    });
    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('profile', [AdminHomeController::class, 'my_profile'])->name('profile');
        Route::get('footer', [AdminHomeController::class, 'footer_settings'])->name('footer');
        Route::get('password', [AdminHomeController::class, 'change_password'])->name('change_password');
    });
    Route::prefix('pages')->name('pages.')->group(function(){
        Route::get('', [AdminHomeController::class, 'all_pages'])->name('index');
        Route::get('team_members', [AdminHomeController::class, 'page_team_members'])->name('team_members');
    });
    Route::prefix('abuse')->name('abuse.')->group(function(){
        Route::get('', [AdminHomeController::class, 'abuse_reports'])->name('reports');
    });

    Route::get('region/{id}/towns', [Controller::class, 'region_towns'])->name('region.towns');
    Route::get('town/{id}/streets', [Controller::class, 'town_streets'])->name('town.streets');
   
    Route::get('users/search', [Controller::class, 'search_user'])->name('users.search');


    Route::resource('users', 'Admin\UserController');


    Route::resource('roles','Admin\RolesController');
    Route::get('permissions', 'Admin\RolesController@permissions')->name('roles.permissions');
    Route::get('assign_role', 'Admin\RolesController@rolesView')->name('roles.assign');
    Route::post('assign_role', 'Admin\RolesController@rolesStore')->name('roles.assign.post');
    Route::post('roles/destroy/{id}', 'Admin\RolesController@destroy')->name('roles.destroy');

    Route::get('reset_password', 'Controller@reset_password')->name('reset_password');
    Route::post('reset_password', 'Controller@reset_password_save')->name('reset_password');

    Route::get('user/block/{user_id}', 'Admin\HomeController@block_user')->name('block_user');
    Route::get('user/activate/{user_id}', 'Admin\HomeController@activate_user')->name('activate_user');

});

Route::get('region/{id}/towns', [Controller::class, 'region_towns'])->name('region.towns');
Route::get('town/{id}/streets', [Controller::class, 'town_streets'])->name('town.streets');


Route::prefix('badmin')->name('business_admin.')->middleware('isBusinessAdmin')->group(function () {

    Route::get('', 'BAdmin\HomeController@home')->name('home');
    Route::get('home', 'BAdmin\HomeController@home')->name('home');

    Route::prefix('businesses')->name('businesses.')->group(function(){
        Route::get('', 'BAdmin\HomeController@businesses')->name('index');
        Route::get('create', 'BAdmin\HomeController@create_business')->name('create');
        Route::post('create', 'BAdmin\HomeController@save_business');
        Route::get('{slug}/show', [AdminHomeController::class, 'show_business'])->name('show');
        Route::get('{slug}/owner', [AdminHomeController::class, 'show_business_owner'])->name('show_owner');
        Route::get('{slug}/edit', [AdminHomeController::class, 'edit_business'])->name('edit');
        Route::post('{slug}/edit', [AdminHomeController::class, 'update_business']);
        Route::get('{slug}/delete', [AdminHomeController::class, 'delete_business'])->name('delete');
        Route::get('{slug}/suspend', [AdminHomeController::class, 'suspend_business'])->name('suspend');
        Route::get('{slug}/verify', [AdminHomeController::class, 'verify_business'])->name('verify');
        Route::get('{slug}/branches', 'BAdmin\HomeController@business_branches')->name('branch.index');
        Route::get('{slug}/create_branch', 'BAdmin\HomeController@create_business_branch')->name('branch.create');
        Route::post('{slug}/create_branch', 'BAdmin\HomeController@save_business_branch');
    });

    Route::prefix('managers')->name('managers.')->group(function(){
        Route::get('', 'BAdmin\HomeController@managers')->name('index');
        Route::get('create', 'BAdmin\HomeController@create_manager')->name('create');
        Route::post('create', 'BAdmin\HomeController@save_manager');
        Route::get('{slug}/show', [AdminHomeController::class, 'show_business'])->name('show');
        Route::get('{slug}/owner', [AdminHomeController::class, 'show_business_owner'])->name('show_owner');
        Route::get('{slug}/edit', [AdminHomeController::class, 'edit_business'])->name('edit');
        Route::post('{slug}/edit', [AdminHomeController::class, 'update_business']);
        Route::get('{slug}/delete', [AdminHomeController::class, 'delete_business'])->name('delete');
        Route::get('{slug}/suspend', [AdminHomeController::class, 'suspend_business'])->name('suspend');
        Route::get('{slug}/verify', [AdminHomeController::class, 'verify_business'])->name('verify');
        Route::get('{slug}/branches', [AdminHomeController::class, 'business_branches'])->name('branch.index');
        Route::get('{slug}/create_branch', [AdminHomeController::class, 'create_business_branch'])->name('branch.create');
        Route::post('{slug}/create_branch', [AdminHomeController::class, 'save_business_branch']);
    });

    Route::prefix('errands')->name('errands.')->group(function(){
        Route::get('delete/{slug}', 'BAdmin\HomeController@delete_errand')->name('delete');
        Route::get('edit/{slug}', 'BAdmin\HomeController@edit_errand')->name('edit');
        Route::post('edit/{slug}', 'BAdmin\HomeController@update_errand');
        Route::get('show/{slug}', 'BAdmin\HomeController@show_errand')->name('show');
        Route::get('set_found/{slug}', 'BAdmin\HomeController@show_errand')->name('set_found');
        Route::get('create', 'BAdmin\HomeController@create_errand')->name('create');
        Route::post('create', 'BAdmin\HomeController@save_errand');
        Route::post('create_update', 'BAdmin\HomeController@update_save_errand')->name('create_update');
        Route::get('', 'BAdmin\HomeController@errands')->name('index');
    });
    Route::prefix('products')->name('products.')->group(function(){
        Route::get('show/{product}', 'BAdmin\HomeController@show_product')->name('show');
        Route::get('create/{shop_slug}', 'BAdmin\HomeController@create_products')->name('create');
        Route::post('create/{shop_slug}', 'BAdmin\HomeController@save_products');
        Route::post('create_update/{shop_slug}', 'BAdmin\HomeController@update_save_products')->name('create_update');
        Route::get('{shop_slug?}', 'BAdmin\HomeController@products')->name('index');
    });
    Route::prefix('services')->name('services.')->group(function(){
        Route::get('show/{product}', 'BAdmin\HomeController@show_service')->name('show');
        Route::get('create/{shop_slug}', 'BAdmin\HomeController@create_service')->name('create');
        Route::post('create/{shop_slug}', 'BAdmin\HomeController@save_service');
        Route::post('create_update/{shop_slug}', 'BAdmin\HomeController@update_save_service')->name('create_update');
        Route::get('{shop_slug?}', 'BAdmin\HomeController@services')->name('index');
    });
    Route::prefix('categories')->name('categories.')->group(function(){
        Route::get('', [AdminHomeController::class, 'categories'])->name('index');
        Route::get('subcategories', [AdminHomeController::class, 'sub_categories'])->name('sub_categories');
        Route::get('subcategories/create', [AdminHomeController::class, 'create_sub_category'])->name('sub_categories.create');
        Route::get('create', [AdminHomeController::class, 'create_category'])->name('create');
    });
    Route::prefix('reviews')->name('reviews.')->group(function(){
        Route::get('', [AdminHomeController::class, 'reviews'])->name('index');
    });

    Route::prefix('enquiries')->name('enquiries.')->group(function(){
        Route::get('', 'BAdmin\HomeController@enquiries')->name('index');
        Route::get('{slug}/show', 'BAdmin\HomeController@show_enquiry')->name('show');
        Route::get('{slug}/mail', 'BAdmin\HomeController@enquiry_sendmail')->name('mail');
        Route::get('{slug}/delete', 'BAdmin\HomeController@delete_enquiry')->name('delete');
    });
    Route::prefix('sms_bundles')->name('sms_bundles.')->group(function(){
        Route::get('', [AdminHomeController::class, 'sms_bundles'])->name('index');
    });
    Route::prefix('reports')->name('reports.')->group(function(){
        Route::get('sms', [AdminHomeController::class, 'sms_reports'])->name('sms');
        Route::get('subscriptions', [AdminHomeController::class, 'subscription_report'])->name('subscription');
    });
    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('profile', [AdminHomeController::class, 'my_profile'])->name('profile');
        Route::get('footer', [AdminHomeController::class, 'footer_settings'])->name('footer');
        Route::get('password', [AdminHomeController::class, 'change_password'])->name('change_password');
    });
    Route::prefix('pages')->name('pages.')->group(function(){
        Route::get('', [AdminHomeController::class, 'all_pages'])->name('index');
        Route::get('team_members', [AdminHomeController::class, 'page_team_members'])->name('team_members');
    });
    Route::prefix('abuse')->name('abuse.')->group(function(){
        Route::get('', [AdminHomeController::class, 'abuse_reports'])->name('reports');
    });

    Route::get('region/{id}/towns', [Controller::class, 'region_towns'])->name('region.towns');
    Route::get('town/{id}/streets', [Controller::class, 'town_streets'])->name('town.streets');
   
    Route::get('users/search', [Controller::class, 'search_user'])->name('users.search');


    Route::resource('users', 'Admin\UserController');


    Route::resource('roles','Admin\RolesController');
    Route::get('permissions', 'Admin\RolesController@permissions')->name('roles.permissions');
    Route::get('assign_role', 'Admin\RolesController@rolesView')->name('roles.assign');
    Route::post('assign_role', 'Admin\RolesController@rolesStore')->name('roles.assign.post');
    Route::post('roles/destroy/{id}', 'Admin\RolesController@destroy')->name('roles.destroy');

    Route::get('reset_password', 'Controller@reset_password')->name('reset_password');
    Route::post('reset_password', 'Controller@reset_password_save')->name('reset_password');

    Route::get('user/block/{user_id}', 'Admin\HomeController@block_user')->name('block_user');
    Route::get('user/activate/{user_id}', 'Admin\HomeController@activate_user')->name('activate_user');

});


Route::get('mode/{locale}', function ($batch) {
    session()->put('mode', $batch);

    return redirect()->back();
})->name('mode');

Route::any('{any?}', function(){return redirect(route('login'));});



