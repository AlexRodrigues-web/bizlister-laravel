<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\City;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totCities'     => City::count(),
            'totCategories' => Category::count(),
            'totBusinesses' => Business::count(),
            'totUsers'      => User::count(),
        ]);
    }
}