@extends('layouts.app')

@section('content')
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
                    欢迎您的回来，如有需要授权码，请联系代理商！
                </div>
            </div>

            @if(in_array(Auth::user()['email'], config('app.auth_email')))
                <div class="card mt-4">
                    <div class="card-header">生成授权码</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('code.store') }}" onsubmit="return handleLoading()">
                            @csrf
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="custom-select" style="border-top-right-radius: 0;border-bottom-right-radius: 0;" name="type" required>
                                        <option selected>类型</option>
                                        <option value="day">天卡</option>
                                        <option value="week">周卡</option>
                                        <option value="month">月卡</option>
                                        <option value="year">年卡</option>
                                    </select>
                                </div>
                                <input type="number" class="form-control" name="number" placeholder="数量" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">生成</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">授权码列表</div>

                    <div class="card-body">
                        <form class="mb-4" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label>激活开始时间</label>
                                    <input type="text" class="form-control" placeholder="例：2018-03-21 00:00:00" name="search[start_used_at]">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>激活结束时间</label>
                                    <input type="text" class="form-control" placeholder="例：2018-03-21 23:59:59" name="search[end_used_at]">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>激活状态</label>
                                    <select class="custom-select" name="search[status]">
                                        <option value="all">全部</option>
                                        <option value="0">待激活</option>
                                        <option value="1">已激活</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary btn-block disabled" type="button">暂时禁用搜索</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table mt-4">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" width="50">#</th>
                                        <th scope="col">类型</th>
                                        <th scope="col">状态</th>
                                        <th scope="col">激活用户</th>
                                        <th scope="col">激活时间</th>
                                        <th scope="col">生成用户</th>
                                        <th scope="col">授权码</th>
                                        <th scope="col">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataset['codes'] as $code)
                                    <tr>
                                        <th scope="row">{{$code->id}}</th>
                                        <td>{{$code->type_name}}</td>
                                        <td>
                                            @if($code->status)
                                                <span class="badge badge-primary">正常</span>
                                            @else
                                                <span class="badge badge-danger">禁用</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($code->user_id)
                                                <span class="badge badge-danger">{{$code->user_info->name}}</span>
                                            @else
                                                <span class="badge badge-primary">待激活</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($code->used_at)
                                                {{$code->used_at}}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            @if($code->created_user_id)
                                                <span class="badge badge-danger">{{$code->created_user_info->name}}</span>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary code-copy-btn" type="button" data-clipboard-action="copy" data-clipboard-target="#code-copy-{{ $code->id }}">复制</button>
                                                </div>
                                                <input type="text" class="form-control" id="code-copy-{{ $code->id }}" readonly value="{{ $code->code }}">
                                            </div>
                                        </td>
                                        <td>
                                            @if($code->status)
                                                <a href="{{url("/code/setStatus/$code->id")}}" class="btn btn-danger btn-sm">禁用</a>
                                            @else
                                                <a href="{{url("/code/setStatus/$code->id")}}" class="btn btn-primary btn-sm">启用</a>
                                            @endif
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
                <div class="card-header">授权激活</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('code.activation') }}" onsubmit="return handleLoading()">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" placeholder="输入您的授权码" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">激活</button>
                            </div>
                        </div>
                    </form>

                    @if(session('activation'))
                        @if(session('activation')['status'])
                            <div class="alert alert-success activation-alert mt-4">
                        @else
                            <div class="alert alert-danger activation-alert mt-4">
                        @endif
                                {{ session('activation')['message'] }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <script>
                                setTimeout(function() {
                                    $(".activation-alert").alert('close');
                                }, 10000);
                            </script>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">授权查询</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('code.search') }}" onsubmit="return handleLoading()">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" placeholder="输入您的授权码" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">查询</button>
                            </div>
                        </div>
                    </form>

                    @if(session('search'))
                        @if(session('search')['status'])
                            <div class="alert alert-success search-alert mt-4">
                        @else
                            <div class="alert alert-danger search-alert mt-4">
                        @endif
                                {{ session('search')['message'] }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <script>
                                setTimeout(function() {
                                    $(".search-alert").alert('close');
                                }, 10000);
                            </script>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">定位中心</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
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
                            @if ($dataset['activation_codes']->count())
                                @foreach ($dataset['activation_codes'] as $code)
                                    <tr>
                                        <th scope="row">{{$code->id}}</th>
                                        <td>{{$code->type_name}}</td>
                                        <td>
                                            @if($code->status)
                                                @if($code->expired_at < \Carbon\Carbon::now())
                                                    <span class="badge badge-danger">过期</span>
                                                @else
                                                    <span class="badge badge-primary">正常</span>
                                                @endif
                                            @else
                                                <span class="badge badge-danger">已禁用</span>
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
                                            @if($code->status)
                                                <a href="{{url("/code/$code->id")}}" class="btn btn-primary btn-sm">查看</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm disabled">查看</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7"><span class="text-danger" style="text-align: center; display:block;">还未激活过授权码，请联系代理商购买授权码！</span></td>
                                </tr>
                            @endif
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
