<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->is_admin, 403);

        return view('orders.index', [
            'orders' => $request->user()->orders()->latest()->get(),
        ]);
    }
}
