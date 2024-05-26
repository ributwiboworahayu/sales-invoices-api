<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{

    public function index()
    {
        $userNames = auth()->user()->name;
        return $this->successResponse('Welcome to dashboard, ' . $userNames . '!');
    }
}
