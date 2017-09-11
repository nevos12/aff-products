<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageHomeController extends Controller
{
    /**
     * The main manage method
     *
     * @return View 
     */
    public function index()
    {
        return view('manage.home');
    }
}
