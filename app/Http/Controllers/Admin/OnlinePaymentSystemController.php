<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OnlinePaymentSystemController extends Controller
{
    public function index()
    {
        return view('admin.online-payment-system.index');
    }
}
