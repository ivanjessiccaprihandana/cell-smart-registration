<?php

namespace App\Http\Controllers;

use App\Models\HomeClass;

class HomeClassController extends Controller
{
    public function index()
    {
        return HomeClass::active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }
}
