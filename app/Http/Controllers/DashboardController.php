<?php

namespace App\Http\Controllers;

use App\Support\BusinessContext;

class DashboardController extends Controller
{
    public function index(BusinessContext $ctx)
    {
        $biz = $ctx->get();
        return view('dashboard', compact('biz'));
    }
}
