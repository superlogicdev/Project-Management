@extends('layouts.app')

@section('title', __('Login'))

@section('content')
@php $gtext = gtext(); @endphp
<!-- main Section -->
<div class="loginsignup-area">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="login text-center">
					<div class="logo">
						<a href="{{ route('login') }}">
							<img src="{{ $gtext['logo'] ? asset('public/media/'.$gtext['logo']) : asset('public/assets/images/logo.png') }}">
						</a>
					</div>
					
					@if (session('message'))
						<div class="alert alert-danger">{{ session('message') }}</div>
					@endif
					
					<form id="login_form" method="POST" action="{{ route('login') }}">
						@csrf
						
						@if($errors->any())
							<ul class="errors-list">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
							</ul>
						@endif
						
						<div class="form-group">
							<input type="email" id="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
						</div>
						<div class="form-group">
							<input type="password" id="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required autocomplete="current-password">
						</div>
						<div class="tw_checkbox checkbox_group">
							<input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
							<label for="remember">{{ __('Remember me') }}</label>
							<span></span>
						</div>
						<input type="submit" class="btn login-btn" value="{{ __('Login') }}">
					</form>
					
					@if (Route::has('password.request'))
					<h3><a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a></h3>
					@endif
					
					@if (Route::has('register'))
					<h3><a href="{{ route('register') }}">{{ __('Sign up for an account') }}</a></h3>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /main Section -->
@endsection

@push('scripts')
@endpush
