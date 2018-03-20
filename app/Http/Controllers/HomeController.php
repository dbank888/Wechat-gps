<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataset = [];
        if(in_array(Auth::user()['email'], config('app.auth_email'))) {
            $dataset['codes'] = \App\Models\Code::where('created_user_id', Auth::id())->orderBy('id', 'desc')->paginate(5, ['*'], 'c_page');
        }

        $dataset['activation_codes'] = \App\Models\Code::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10, ['*'], 'a_page');

        return view('home')->with('dataset', $dataset);
    }
}
