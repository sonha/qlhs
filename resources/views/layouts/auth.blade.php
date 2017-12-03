<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">

    <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link rel="stylesheet" id="auth-css" href="{!! asset('assets/css/auth.css') !!}" type="text/css" media="all">
	<link href="{{ asset('css/login.css') }}" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

</head>
<body id="body_login">
<div class="navbar navbar-default navbar-static-top" style="background-color: #3B5999;height: 80px">
     <div class="container">
                <div class="navbar-header" >

                    <!-- Branding Image -->
                    <a class="navbar-brand" style="color: white;font-size: x-large;font-weight: bold;margin-top: 15px;" href="{{ url('/login') }}">
                        {{ config('app.name', 'Laravelll') }}
                    </a>
                </div>
                </div>
     </div>
	@yield('content')

</body>
</html>
