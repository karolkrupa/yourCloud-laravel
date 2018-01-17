<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    function index(Request $request) {
        return view('folder');
    }
}
