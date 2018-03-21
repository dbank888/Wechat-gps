<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function show($code)
    {
        $code = \App\Models\Code::where('code', $code)->first();
        if(!$code) {
            return null;
        }
        // 判断是否激活
        if(!$code->user_id) {
            return 'not activation';
        }
        // 判断是否过期
        if($code->expired_at < \Carbon\Carbon::now()) {
            return 'expired';
        }
        // 授权码已被禁用
        if(!$code->status) {
            return 'disabled';
        }

        $config = [
            'app_id' => 'wxcd3ec46b0ff693c2',
            'secret' => '9eb6bfe4c077846578195d1a6ad3d6f2',

            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => './wechat.log',
            ],
        ];

        $app = \EasyWeChat\Factory::officialAccount($config);

        \App\Models\Visit::create([
            'user_id' => $code->user_id,
            'code_id' => $code->id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ]);
        return view('track')->with('code', $code->code)->with('app', $app);
    }

    /**
     * 保存微信地理位置授权记录
     *
     * @param Request $request
     * @return null
     */
    public function storeWxLocation(Request $request)
    {
        $data = $request->all();
        $code = $data['code'];
        $code = \App\Models\Code::where('code', $code)->first();
        if(!$code) {
            return null;
        }
        // 判断是否激活
        if(!$code->user_id) {
            return 'not activation';
        }
        // 判断是否过期
        if($code->expired_at < \Carbon\Carbon::now()) {
            return 'expired';
        }
        // 授权码已被禁用
        if(!$code->status) {
            return 'disabled';
        }

        \App\Models\Visit::create([
            'user_id' => $code->user_id,
            'code_id' => $code->id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'auth_data' => json_encode($data, true),
        ]);
        return 'success';
    }
}
