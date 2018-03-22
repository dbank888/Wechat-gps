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
                    @if(in_array(Auth::user()['email'], config('app.auth_email')))
                        <p>【<strong class="text-danger">{{Auth::user()['name']}}</strong>】 代理商你好，欢迎您的回来!</p>
                        <a href="{{url('/manage')}}" class="btn btn-primary">进入 管理中心</a>
                    @else
                        欢迎您的回来，如有需要授权码，请联系代理商！
                    @endif
                </div>
            </div>

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
                                <th scope="col">激活时间</th>
                                <th scope="col">过期时间</th>
                                <th scope="col">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($codes->count())
                                @foreach ($codes as $code)
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
                        {{$codes->links()}}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
