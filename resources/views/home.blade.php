@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('tips'))
                @if(session('tips')['status'])
                    <div class="alert alert-success">
                @else
                    <div class="alert alert-danger">
                @endif
                        {{ session('tips')['message'] }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <script>
                    setTimeout(function() {
                        $(".alert").alert('close');
                    }, 2000);
                </script>
            @endif

            <div class="card">
                <div class="card-header">会员中心</div>

                <div class="card-body">
                    欢迎您的回来，技术支持QQ: <strong class="text-danger">624508914</strong>
                </div>
            </div>

            @if(Auth::user()['email'] === '624508914@qq.com')
                <div class="card mt-4">
                    <div class="card-header">生成授权码</div>

                    <div class="card-body">
                        <script>
                            var loading = false;
                            function handleLoading() {
                                if(loading) {
                                    return false;
                                }
                                loading = true;
                                return true;
                            }
                        </script>
                        <form method="POST" action="{{ route('code.store') }}" onsubmit="return handleLoading()">
                            @csrf
                            <div class="input-group">
                                <select class="custom-select" name="type">
                                    <option selected>请选择类型</option>
                                    <option value="day">天卡</option>
                                    <option value="week">周卡</option>
                                    <option value="month">月卡</option>
                                    <option value="year">年卡</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">立即生成</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table mt-4">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" width="50">#</th>
                                        <th scope="col">类型</th>
                                        <th scope="col">授权码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataset['codes'] as $code)
                                    <tr>
                                        <th scope="row">{{$code->id}}</th>
                                        <td>{{$code->type_name}}</td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary code-copy-btn" type="button" data-clipboard-action="copy" data-clipboard-target="#code-copy-{{ $code->id }}">复制</button>
                                                </div>
                                                <input type="text" class="form-control" id="code-copy-{{ $code->id }}" readonly value="{{ $code->code }}">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <script>
                                var codeClipboard = new ClipboardJS('.code-copy-btn');
                                codeClipboard.on('success', function(e) {
                                    alert('复制成功');
                                });
                            </script>
                        </div>

                        <nav class="float-right">
                            @if(Request::get('a_page'))
                                {{$dataset['codes']->appends(['a_page' => Request::get('a_page')])->links()}}
                            @else
                                {{$dataset['codes']->links()}}
                            @endif
                        </nav>
                    </div>
                </div>
            @endif

            <div class="card mt-4">
                <div class="card-header">定位中心</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('code.activation') }}" onsubmit="return handleLoading()">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" placeholder="输入您的授权码">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">立即激活</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" width="50">#</th>
                                    <th scope="col">类型</th>
                                    <th scope="col">状态</th>
                                    <th scope="col">定位链接</th>
                                    <th scope="col">使用时间</th>
                                    <th scope="col">过期时间</th>
                                    <th scope="col">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataset['activation_codes'] as $code)
                                    <tr>
                                        <th scope="row">{{$code->id}}</th>
                                        <td>{{$code->type_name}}</td>
                                        <td>
                                            @if($code->expired_at < \Carbon\Carbon::now())
                                                <span class="badge badge-danger">过期</span>
                                            @else
                                                <span class="badge badge-primary">正常</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary link-copy-btn" type="button" data-clipboard-action="copy" data-clipboard-target="#link-copy-{{ $code->id }}">复制</button>
                                                </div>
                                                <input type="text" class="form-control" id="link-copy-{{ $code->id }}" readonly value="{{ url("/track/{$code->code}") }}">
                                            </div>
                                        </td>
                                        <td>{{$code->used_at}}</td>
                                        <td>{{$code->expired_at}}</td>
                                        <td>
                                            <a href="{{url("/code/$code->id")}}" class="btn btn-primary btn-sm">查看</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <script>
                            var linkClipboard = new ClipboardJS('.link-copy-btn');
                            linkClipboard.on('success', function(e) {
                                alert('复制成功');
                            });
                        </script>
                    </div>

                    <nav class="float-right">
                        @if(Request::get('c_page'))
                            {{$dataset['activation_codes']->appends(['c_page' => Request::get('c_page')])->links()}}
                        @else
                            {{$dataset['activation_codes']->links()}}
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
