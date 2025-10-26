<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totals = [
            'business'   => DB::table('business')->count(),
            'categories' => DB::table('category')->count(),
            'cities'     => DB::table('city')->count(),
            'users'      => DB::table('app_users')->count(),
        ];
        return view('admin.index', compact('totals'));
    }
}