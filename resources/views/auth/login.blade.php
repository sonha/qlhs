@extends('layouts.auth')

@section('title', 'Đăng nhập - Phần mềm quản lý hồ sơ')
@section('description', 'Login to the admin area')

@section('content')
	<div class="container">
        <div class="card card-container">
            <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
           <!--  <img id="profile-img" class="profile-img-card" src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" />
            <p id="profile-name" class="profile-name-card"></p> -->
            <form class="form-signin" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}
                <input type="text" id="username" name="username" class="form-control" placeholder="Tên đăng nhập" value="{{ old('username') }}" required autofocus>
				@if ($errors->has('username'))
					<span class="help-block">
						<strong>{{ $errors->first('username') }}</strong>
					</span>
				@endif
                <input type="password" id="password" name="password" class="form-control" placeholder="Mật khẩu" required>
				@if ($errors->has('password'))
					<span class="help-block">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
				@endif
                <div id="remember" class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me" name="remember"> Nhớ  đăng nhập
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Đăng nhập</button>
            </form><!-- /form -->
            <a href="{{ url('/password/reset') }}" class="forgot-password">Quên mật khẩu?</a><!--  or <a href="{{ url('/username/reminder') }}" class="forgot-password">Quên tài khoản đăng nhập?</a> -->
        </div><!-- /card-container -->
		<!-- <div class="card-container text-center">
			<a href="{{ url('/register') }}" class="new-account">Create an account</a> or <a href="{{ url('/activation/resend') }}" class="new-account">Resend activation code</a>
		</div> -->
		
    </div><!-- /container -->
@endsection
