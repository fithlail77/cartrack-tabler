<!DOCTYPE html>
<html lang="en">

<head>

	<title>{{ config('app.name', 'RestAPI-Cartrack') }}</title>
	<!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 11]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!--<meta name="description" content="Flash Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />-->
	<meta name="keywords" content="admin templates, bootstrap admin templates, bootstrap 4, dashboard, dashboard templets, sass admin templets, html admin templates, responsive, bootstrap admin templates free download,premium bootstrap admin templates, RestAPI-Cartrack, RestAPI-Cartrack bootstrap admin template">
	<meta name="author" content="Codedthemes" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Favicon icon -->
	<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
	<!-- fontawesome icon -->
	<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
	<!-- animation css -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/animation/css/animate.min.css') }}">

	<!-- vendor css -->
	<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>

<!-- [ auth-signin ] start -->
<div class="auth-wrapper">
	<div class="auth-content container">
		<div class="card">
			<div class="row align-items-center">
				<div class="col-md-6">
					<div class="card-body">
						<h4 class="mb-3 f-w-400">Login to Rest API Cartrack</h4>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
						    <div class="input-group mb-2">
						    	<div class="input-group-prepend">
						    		<span class="input-group-text"><i class="feather icon-mail"></i></span>
						    	</div>
						    	<input type="email" class="form-control" placeholder="Email address" name="email" value="{{ old('email') }}" required autocomplete="email">
						    </div>
						    <div class="input-group mb-3">
						    	<div class="input-group-prepend">
						    		<span class="input-group-text"><i class="feather icon-lock"></i></span>
						    	</div>
						    	<input type="password" class="form-control" placeholder="Password" name="password" required autocomplete="current-password">
						    </div>
						    <div class="form-group text-left mt-2">
						    	<div class="checkbox checkbox-primary d-inline">
						    		<input type="checkbox" name="checkbox-fill-1" id="checkbox-fill-a1" checked="">
						    		<label for="checkbox-fill-a1" class="cr"> Save credentials</label>
						    	</div>
						    </div>
						    <button class="btn btn-primary mb-4" type="submit">Login</button>
                        </form>
						<p class="mb-2 text-muted">Lupa Sandi? <a href="#" class="f-w-400">Kontak IT Tim</a></p>
					</div>
				</div>
				<div class="col-md-6 d-none d-md-block">
					<img src="{{ asset('assets/images/RestAPI.webp') }}" alt="" class="img-fluid">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- [ auth-signin ] end -->

<!-- Required Js -->
<script src="{{ asset('assets/js/vendor-all.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>


<!--<div class="footer-fab">
    <div class="b-bg">
        <i class="fas fa-question"></i>
    </div>
    <div class="fab-hover">
        <ul class="list-unstyled">
            <li><a href="../doc/index-bc-package.html" target="_blank" data-text="UI Kit" class="btn btn-icon btn-rounded btn-info m-0"><i class="feather icon-layers"></i></a></li>
            <li><a href="../doc/index.html" target="_blank" data-text="Document" class="btn btn-icon btn-rounded btn-primary m-0"><i class="feather icon feather icon-book"></i></a></li>
        </ul>
    </div>
</div>-->


</body>

</html>
