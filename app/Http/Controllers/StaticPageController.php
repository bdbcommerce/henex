<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }
}
