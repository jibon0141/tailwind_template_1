<?php


use Illuminate\Support\Facades\Route;

Route::middleware('super_admin:director,admin')->group(function () {

});
