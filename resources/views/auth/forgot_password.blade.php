@extends('public.layout')
@section('section')
	    <!-- Breadcrumb Section Start -->
		<section class="breadscrumb-section pt-0">
			<div class="container-fluid-lg">
				<div class="row">
					<div class="col-12">
						<div class="breadscrumb-contain">
							<h2 class="mb-2">Forgot Password</h2>
							<nav>
								<ol class="breadcrumb mb-0">
									<li class="breadcrumb-item">
										<a href="index.html">
											<i class="fa-solid fa-house"></i>
										</a>
									</li>
									<li class="breadcrumb-item active">Forgot Password</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Breadcrumb Section End -->
	
		<section class="log-in-section section-b-space forgot-section">
			<div class="container-fluid-lg w-100">
				<div class="row">
					<div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
						<div class="image-contain">
							<img src="{{ asset('assets/images/default1.jpg') }}" class="img-fluid" alt="">
						</div>
					</div>
	
					<div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
						<div class="d-flex align-items-center justify-content-center h-100">
							<div class="log-in-box">
								<div class="log-in-title">
									<h3>Welcome To Errandia</h3>
									<h4>Forgot your password?</h4>
								</div>
	
								<div class="input-box">
									<form class="row g-4" method="POST">
										@csrf
										<div class="col-12">
											<div class="form-floating theme-form-floating log-in-form">
												<input type="email" class="form-control" id="email" name="email" placeholder="Email Address" value="{{old('email')}}">
												<label for="email">Email Address</label>
											</div>
										</div>
	
										<div class="col-12">
											<button class="btn btn-animation w-100" type="submit">Send Password Reset Link</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	
@endsection
@section('script')

@endsection