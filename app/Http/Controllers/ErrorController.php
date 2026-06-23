<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function notFound()
    {
        return '<div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <img src="' . asset('images/notfound.jpg') . '" alt="Page Not Found"> </div>';
    }
}

