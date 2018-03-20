<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class CodeController extends Controller
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

    public function show($id) {
        $code = \App\Models\Code::where('id', $id)->first();
        if(!$code) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '定位链接不存在',
            ]);
        }
        $code->visits = \App\Models\Visit::where('code_id', $id)->where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);

        return view('code')->with('code', $code);
    }

    /**
     * 生成激活码
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        if(Auth::user()['email'] !== '624508914@qq.com') {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '您没有权限生成授权码',
            ]);
        }
        $data = $request->all();
        if(!in_array($data['type'], ['day', 'week', 'month', 'year'])) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '请选择正确类型',
            ]);
        }
        $result = \App\Models\Code::create([
            'type' => $data['type'],
            'code' => md5(time() . Str::random(32) . Str::random(32) . Str::random(32) . time())
        ]);

        if(!$result) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '生成授权码失败',
            ]);
        }

        return Redirect::back()->with('tips', [
            'status' => true,
            'message' => '生成授权码成功',
        ]);
    }

    /**
     * 激活授权码
     *
     * @param Request $request
     * @return mixed
     */
    public function activation(Request $request)
    {
        $data = $request->all();
        $result = \App\Models\Code::where('code', $data['code'])->first();
        if(!$data['code'] || !$result) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '授权码不存在',
            ]);
        }
        if($result->user_id) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '授权码已被激活',
            ]);
        }
        $result->user_id = Auth::id();
        $result->used_at = \Carbon\Carbon::now();
        switch ($result->type)
        {
            case 'day':
                $result->expired_at = \Carbon\Carbon::now()->modify('+1 days');
                break;
            case 'week':
                $result->expired_at = \Carbon\Carbon::now()->modify('+1 weeks');
                break;
            case 'month':
                $result->expired_at = \Carbon\Carbon::now()->modify('+1 months');
                break;
            case 'year':
                $result->expired_at = \Carbon\Carbon::now()->modify('+1 year');
                break;
        }
        $res = $result->save();
        if($res === false) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '激活失败',
            ]);
        }

        return Redirect::back()->with('tips', [
            'status' => true,
            'message' => '激活成功',
        ]);
    }

    /**
     * 清空定位记录
     */
    public function clear(Request $request)
    {
        $id = $request->get('id');
        $result = \App\Models\Code::where('id', $id)->first();
        if(!$result) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '定位链接不存在',
            ]);
        }
        \App\Models\Visit::where('code_id', $id)->where('user_id', Auth::id())->delete();

        return Redirect::back()->with('tips', [
            'status' => true,
            'message' => '成功',
        ]);
    }
}
