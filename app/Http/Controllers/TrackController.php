<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $code = \App\Models\Code::where('code', $code)->first();
        if(!$code) {
            return null;
        }
        // 判断是否激活
        if(!$code->user_id) {
            return null;
        }
        // 判断是否过期
        if($code->expired_at < \Carbon\Carbon::now()) {
            return null;
        }

        \App\Models\Visit::create([
            'user_id' => $code->user_id,
            'code_id' => $code->id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ]);
        return view('track');
    }

    public function statics()
    {
        Log::info('Track statics.' . $_SERVER['HTTP_USER_AGENT']);
    }
}
