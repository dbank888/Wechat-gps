<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        * {
            padding:0;
            margin:0;
        }
    </style>
</head>
<body>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.0&ak=yogxH1g0VzghVO38jG0jF1CEFuNpjyiR&services=&t=20170517145936"></script>
<!--百度地图容器-->
<div style="width:100%;box-sizing:border-box;height:500px;border:#ccc solid 1px;font-size:12px" id="allmap"></div>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    map.centerAndZoom(new BMap.Point({{$location['x']}}, {{$location['y']}}),15);
    map.enableScrollWheelZoom(true);
    var point = new BMap.Point({{$location['x']}}, {{$location['y']}});
    var marker = new BMap.Marker(point);  // 创建标注
    map.addOverlay(marker);              // 将标注添加到地图中
    map.panTo(point);
</script>
</body>
</html>