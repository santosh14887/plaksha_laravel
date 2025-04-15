@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
		<div class="col-md-12" style="text-align:center;">
			<img src="{{ asset('images/logo.png') }}" alt="logo" style="width:100px;"/>
		</div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
				<style>
				.redirect_success {
						position: relative;
						color: green;
						margin-bottom: 10px;
					}
				</style>
                <div class="card-body">
				@php
				$redirect_status = 'no';
				$base_url = URL::to('/');
				@endphp
					@if(app('request')->input('submissionGuid') && $base_url == 'https://demo.fleetelevate.com')
						@php
							$redirect_status = 'yes';
						@endphp
						<button type="button" class="btn btn-primary" id="redirect_model_btn" data-toggle="modal" data-target="#redirect_model" style="display:none;">
						  Launch demo modal
						</button>
						<div class="modal fade" id="redirect_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
						  <div class="modal-dialog" role="document">
							<div class="modal-content">
							  <div class="modal-header">
								<!--<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>-->
							  </div>
							  <div class="modal-body">
								<div class="col-md-12 redirect_success">
								Thank you for showing interest in the demo.You will auto login to the dashboard in few seconds.
							</div>
							  </div>
							  <!--<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							  </div>-->
							</div>
						  </div>
						</div>
						
					@endif
					<input type="hidden" id="login_form_redirect" value= "{{ $redirect_status }}">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!--<div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>--->

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary login_btn">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <!--<a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>-->
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

