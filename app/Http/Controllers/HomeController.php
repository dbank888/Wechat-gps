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
        $search = $request->get('search');

        $dataset = [];
        if(in_array(Auth::user()['email'], config('app.auth_email'))) {
            if(Auth::user()['email'] === '624508914@qq.com') {
                if($search) {
                    $dataset['codes'] = \App\Models\Code::where(function ($query) use ($search) {
                        if(!is_null($search['start_used_at']) && !is_null($search['end_used_at'])) {
                            $query->whereBetween('used_at', [$search['start_used_at'], $search['end_used_at']]);
                        }
                        if(isset($search['status']) && $search['status'] != '全部') {
                            $query->where('status',  $search['status']);
                        }
                    })->orderBy('id', 'desc')->paginate(20, ['*'], 'c_page');
                } else {
                    $dataset['codes'] = \App\Models\Code::orderBy('id', 'desc')->paginate(20, ['*'], 'c_page');
                }
            } else {
                if($search) {
                    $dataset['codes'] = \App\Models\Code::where(function ($query) use ($search) {
//                        if(!is_null($search['start_used_at']) && !is_null($search['end_used_at'])) {
//                            $query->whereBetween('used_at', [$search['start_used_at'], $search['end_used_at']]);
//                        }
//                        if(isset($search['status']) && $search['status'] != '全部') {
//                            $query->where('status',  $search['status']);
//                        }
                    })->where('created_user_id', Auth::id())->orderBy('id', 'desc')->paginate(20, ['*'], 'c_page');
                } else {
                    $dataset['codes'] = \App\Models\Code::where('created_user_id', Auth::id())->orderBy('id', 'desc')->paginate(20, ['*'], 'c_page');
                }
            }
            foreach($dataset['codes'] as $k => $v) {
                $v->user_info = \App\User::where('id', $v->user_id)->first();
                $v->created_user_info = \App\User::where('id', $v->created_user_id)->first();
                $dataset['code'][$k] = $v;
            }
        }

        $dataset['activation_codes'] = \App\Models\Code::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10, ['*'], 'a_page');

        return view('home')->with('dataset', $dataset);
    }
}
