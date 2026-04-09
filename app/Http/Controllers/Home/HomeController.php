<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home()
    {
        return view('home.accueil');
    }

    public function services()
    {
        return view('home.pages.services');
    }

    public function comment()
    {
        return view('home.pages.comment');
    }

    public function securite()
    {
        return view('home.pages.securite');
    }

    public function contact()
    {
        return view('home.pages.contact');
    }
}
