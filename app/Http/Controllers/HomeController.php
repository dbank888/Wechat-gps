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
        $codes = \App\Models\Code::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('home')->with('codes', $codes);
    }

    public function manage(Request $request)
    {
        if(!in_array(Auth::user()['email'], config('app.auth_email'))) {
            return redirect('/home')->with('tips', [
                'status' => false,
                'message' => '访问错误',
            ]);
        }
        $search = $request->get('search');

        $codes = [];
        if(Auth::user()['email'] === '624508914@qq.com') {
            if($search) {
                $codes = \App\Models\Code::where(function ($query) use ($search) {
                    if(!is_null($search['start_used_at'])) {
                        $query->where('used_at', '>=', $search['start_used_at']);
                    }
                    if(!is_null($search['end_used_at'])) {
                        $query->where('used_at', '<=', $search['end_used_at']);
                    }
                    if(!is_null($search['code'])) {
                        $query->where('code', $search['code']);
                    }
                    if(is_numeric($search['status'])) {
                        $query->where('status',  $search['status']);
                    }
                })->orderBy('id', 'desc')->paginate(10);
            } else {
                $codes = \App\Models\Code::orderBy('id', 'desc')->paginate(10);
            }
        } else {
            if($search) {
                $codes = \App\Models\Code::where(function ($query) use ($search) {
                    if(!is_null($search['start_used_at'])) {
                        $query->where('used_at', '>=', $search['start_used_at']);
                    }
                    if(!is_null($search['end_used_at'])) {
                        $query->where('used_at', '<=', $search['end_used_at']);
                    }
                    if(!is_null($search['code'])) {
                        $query->where('code', $search['code']);
                    }
                    if(is_numeric($search['status'])) {
                        $query->where('status',  $search['status']);
                    }
                })->where('created_user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
            } else {
                $codes = \App\Models\Code::where('created_user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
            }
        }
        foreach($codes as $k => $v) {
            $v->user_info = \App\User::where('id', $v->user_id)->first();
            $v->created_user_info = \App\User::where('id', $v->created_user_id)->first();
            $codes[$k] = $v;
        }

        return view('manage')->with('codes', $codes)->with('search', $search);
    }
}
