@extends('layouts.auth')

@section('title', 'Quên mật khẩu')
@section('description', 'Quên mật khẩu đăng nhập phần mềm.')

@section('content')
	<div class="container">
		<div class="card-container text-center">
			@if (session('status'))
				<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					{{ session('status') }}
				</div>
			@endif
			@if ($errors->has('email'))
				<div class="alert alert-danger fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					{{ $errors->first('email') }}
				</div>
			@endif
		</div>
        <div class="card card-container">
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" method="POST" action="{{ url('/password/email') }}">
                {{ csrf_field() }}
                <input type="email" id="email" name="email" class="form-control" placeholder="Địa chỉ email" value="{{ old('email') }}" required autofocus>
				@if ($errors->has('email'))
					<span class="help-block">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif

                <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Gửi</button>
                 <button class="btn btn-lg btn-primary btn-block btn-signin" type="button" onclick="document.location.replace('/login');">Đăng nhập
                 </button>
                
        
            </form><!-- /form -->
           
        </div><!-- /card-container -->
		
    </div><!-- /container -->
@endsection