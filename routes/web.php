<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

if (App::environment('local')) {
    Route::get('debug/bitbucket', 'Debug@bitbucket');
    Route::get('debug/github', 'Debug@github');

    Route::get('dnschecks', 'DnsChecks@run');
    Route::get('dnschecks_gist', 'DnsChecks@gist');
    Route::get('whoischecks', 'WhoIsChecks@run');
}