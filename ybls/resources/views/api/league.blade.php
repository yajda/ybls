<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta name="apple-themes-web-app-capable" content="yes">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <title>{{$data->title}}</title>
    <link rel="stylesheet" href="/public/css/api/main.css">
    <script type="text/javascript" src="/public/js/api/main.js"></script>
</head>
<body>
<div id="web_view">
    <img class="banner_image" src="{{$data->image}}">
    <div>{{$data->content}}</div>
</div>
</body>
</html>