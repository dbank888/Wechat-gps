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

    /**
     * 查看授权码详情
     * @param $id
     * @return $this
     */
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
     * 设置授权码状态
     * @param $id
     * @return mixed
     */
    public function setStatus($id) {
        $code = \App\Models\Code::where('id', $id)->first();
        if(!$code) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '定位链接不存在',
            ]);
        }
        $code->status = intval(!$code->status);
        $code->save();

        return Redirect::back()->with('tips', [
            'status' => true,
            'message' => '操作成功',
        ]);
    }

    /**
     * 生成激活码
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        if(!in_array(Auth::user()['email'], config('app.auth_email'))) {
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
        if($data['number'] < 1) {
            return Redirect::back()->with('tips', [
                'status' => false,
                'message' => '数量必须大于 0',
            ]);
        }
        for($i = 0; $i < $data['number']; $i++) {
            $result = \App\Models\Code::create([
                'type' => $data['type'],
                'remark' => $data['remark'],
                'code' => md5(time() . Str::random(32) . Str::random(32) . Str::random(32) . time()),
                'created_user_id' => Auth::id(),
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
            return Redirect::back()->with('activation', [
                'status' => false,
                'message' => '授权码不存在',
            ]);
        }
        if($result->user_id) {
            return Redirect::back()->with('activation', [
                'status' => false,
                'message' => '授权码已被激活',
            ]);
        }
        if(!$result->status) {
            return Redirect::back()->with('activation', [
                'status' => false,
                'message' => '授权码已被禁用',
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
            return Redirect::back()->with('activation', [
                'status' => false,
                'message' => '激活失败',
            ]);
        }

        return Redirect::back()->with('activation', [
            'status' => true,
            'message' => '激活成功',
        ]);
    }

    /**
     * 查询授权码
     *
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $data = $request->all();
        $result = \App\Models\Code::where('code', $data['code'])->first();
        if(!$data['code'] || !$result) {
            return Redirect::back()->with('search', [
                'status' => false,
                'message' => '授权码: ' . $data['code'] . ' 不存在',
            ]);
        }
        if($result->user_id) {
            return Redirect::back()->with('search', [
                'status' => false,
                'message' => '授权码: ' . $data['code'] . ' 已被激活',
            ]);
        }

        if(!$result->status) {
            return Redirect::back()->with('search', [
                'status' => false,
                'message' => '授权码: ' . $data['code'] . ' 已被禁用',
            ]);
        }

        return Redirect::back()->with('search', [
            'status' => true,
            'message' => '授权码: ' . $data['code'] . ' 还未激活，可正常使用',
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

    /**
     * 编辑备注
     */
    public function editRemark(Request $request)
    {
        $id = $request->get('id');
        $result = \App\Models\Code::where('id', $id)->first();
        if(!$result) {
            return json_encode([
                'status' => false,
                'message' => '定位链接不存在',
            ]);
        }
        $result->remark = $request->get('remark');
        if(!$result->save()) {
            return json_encode([
                'status' => false,
                'message' => '失败',
            ]);
        }

        return json_encode([
            'status' => true,
            'message' => '成功',
        ]);
    }
}
