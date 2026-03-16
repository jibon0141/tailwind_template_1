<?php

use Illuminate\Support\Facades\Route;

Route::middleware('super_admin:nsm,admin')->group(function () {

});

