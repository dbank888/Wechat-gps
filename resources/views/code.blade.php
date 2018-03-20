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
                        setTimeout(function () {
                            $(".alert").alert('close');
                        }, 2000);
                    </script>
                @endif

                <div class="card">
                    <div class="card-header"><span class="float-left">会员中心</span><a class="float-right" href="javascript:void(0)" onclick="history.back();">返回</a></div>

                    <div class="card-body">
                        欢迎您的回来，如有需要授权码，请联系代理商！
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">定位中心 > 定位链接 > 详情</div>

                    <div class="card-body">
                        <form method="POST" action="{{route('code.clear')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$code->id}}" />
                            <button class="btn btn-danger btn-block" type="submit">清空定位记录</button>
                        </form>
                        <div class="input-group mt-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text">定位链接</span>
                            </div>
                            <input type="text" class="form-control" id="link-copy-{{ $code->id }}" readonly value="{{ url('/track/' . $code->code) }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary link-copy-btn" type="button" data-clipboard-action="copy" data-clipboard-target="#link-copy-{{ $code->id }}">复制</button>
                            </div>
                        </div>
                        <script>
                            var linkClipboard = new ClipboardJS('.link-copy-btn');
                            linkClipboard.on('success', function(e) {
                                alert('复制成功');
                            });
                        </script>

                        <div class="table-responsive">
                            <table class="table mt-4">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col" width="50">#</th>
                                    <th scope="col">IP</th>
                                    <th scope="col">UserAgent</th>
                                    <th scope="col">定位时间</th>
                                    <th scope="col">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($code->visits as $visit)
                                    <tr>
                                        <th scope="row">{{$visit->id}}</th>
                                        <td>{{$visit->ip}}</td>
                                        <td>{{$visit->user_agent}}</td>
                                        <td>{{$visit->created_at}}</td>
                                        <td>
                                            <a href="{{url("/visit/$visit->id")}}" class="btn btn-danger btn-sm">查看定位</a>
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
                            {{$code->visits->links()}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
