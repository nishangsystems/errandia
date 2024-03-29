@extends('public.layout')
@section('section')
    <section class="user-dashboard-section section-b-space">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-xxl-3 col-lg-4">
                    <div class="card shadow bg-white px-2 py-3" style="border-radius: 1rem;">
                        <div class="card-body">
                            <span class="text-h6 d-block text-center mx-auto">{{ $shop->name }}</span> <span class="fas fa-verified text-info fa-2x"></span><br>
                            <img src="{{$shop->image_path != null ? asset('uploads/logos/'.$shop->image_path) : asset('assets/images/default1.jpg') }}" class="img-responsive mx-auto my-3 d-block" style="width: 12rem; height: auto;">
                            <div class="my-2 d-flex justify-content-center">
                                <span class="mr-3">
                                    <span class="fa fa-star {{ $shop->rating??0 > 0  ? 'text-warning' : 'text-secondary' }} mx-1"></span>
                                    <span class="fa fa-star {{ $shop->rating??0 > 1  ? 'text-warning' : 'text-secondary' }} mx-1"></span>
                                    <span class="fa fa-star {{ $shop->rating??0 > 2  ? 'text-warning' : 'text-secondary' }} mx-1"></span>
                                    <span class="fa fa-star {{ $shop->rating??0 > 3  ? 'text-warning' : 'text-secondary' }} mx-1"></span>
                                    <span class="fa fa-star {{ $shop->rating??0 > 4  ? 'text-warning' : 'text-secondary' }} mx-1"></span>
                                </span>
                                <b class="">{{ $shop->rating??0 }} Ratings</b>
                            </div>
                            
                            <div class="my-2 d-flex">
                                <img class="mr-3" styl="height: 2rem; width: 2rem;" src="{{ asset('assets/badmin/icon-location.svg') }}">
                                <b class="">{{ $shop->location() }}</b>
                            </div>
                            <div class="my-2 d-flex">
                                <img class="mr-3" styl="height: 2rem; width: 2rem;" src="{{ asset('assets/badmin/icon-member.svg') }}">
                                <b class="">Member since {{ \Carbon\Carbon::parse($shop->created_at)->format('M Y') }}</b>
                            </div>

                            <div class="my-2 d-flex justify-content-center">
                                <a class="button-primary" href="https://wa.me/{{ $shop->contactInfo->whatsapp??'' }}?text=`I saw your business; {{ $shop->name }} on Errandia. Can we talk more?`"><span class="fa fa-whatsapp"></span> Chat on Whatsapp</a>
                            </div>
                            <div class="my-2 d-flex justify-content-center">
                                <a class="button-secondary " href="tel:{{ $shop->contactInfo->phone ?? '' }}"><span class="fa fa-phone"></span> Call {{ $shop->contactInfo->phone ?? '???' }}</a>
                            </div>
                            <div class="my-2 d-flex justify-content-center ">
                                @if ($shop->subscribersR()->where('user_id', auth()->id())->count() == 0)
                                    <a class="button-secondary " href="{{ route('business_admin.businesses.follow', $shop->slug) }}"> Follow this Business</a>
                                @else
                                    <a class="button-tertiary " href="{{ route('business_admin.businesses.unfollow', $shop->slug) }}"> Unfollow this Business</a>                                    
                                @endif
                            </div>

                            <div class="my-2 text-body">
                                Follow us on social media <br>
                                <a href="#"><span class="fa fa-facebook fa-2x mt-2 mx-2 text-info"></span></a>
                                <a href="#"><span class="fa fa-instagram fa-2x mt-2 mx-2 text-overline"></span></a>
                                <a href="#"><span class=" fa-2x mt-2 mx-2 text-overline"><i class="fab fa-tiktok text-danger"></i></span></a>
                                <a href="#"><span class=" fa-2x mt-2 mx-2 text-overline"><i class="fa-brands fa-twitter text-primary"></i></span></a>
                            </div>

                            @if(count($branches) > 0)
                                <hr class="my-4">
                                <span class="text-h6 mb-3">Visit Our Other Branches</span><br>
                                @forelse ($branches as $branch)
                                    <a href="{{ route('public.business.show', $branch->slug) }}" class="my-2 d-flex">
                                        <span class="fa fa-angle-right fa-2x mr-3"></span>
                                        @if ($branch->image_path != null)
                                            <img src="{{ asset('uploads/logos/'.$branch->image_path) }}" class="img-responsive mx-auto" style="width: 1.5rem; height: 1.5rem;">
                                        @else
                                            <span class="fa fa-cog fa-2x text-h1 d-block text-center"></span>
                                        @endif
                                        <b class="h6 d-block"> {{ $branch->location() }}</b>
                                    </a>
                                @empty
                                    <div class="my-2 d-flex">
                                        <span class="fa fa-caret-right fa-2x"></span>
                                        @if ($shop->image_path != null)
                                            <img src="{{ asset('uploads/logos/'.$shop->image_path) }}" class="img-responsive mx-2" style="width: 1.5rem; height: 1.5rem;">
                                        @else
                                            <span class="fa fa-cog fa-2x text-h1 d-block text-center mx-2"></span>
                                        @endif
                                        <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                                            <p>No Branches</p>
                                        </div></div>
                                    </div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xxl-9 col-lg-8">
                    <div class="dashboard-right-sidebar">
                        <div>
                            <div class="tab-pane fade show active" id="pills-dashboard" role="tabpanel" aria-labelledby="pills-dashboard-tab">
                                <div class="dashboard-home">



                                    {{-- PRODUCTS START --}}
                                    <section class="breadscrumb-section pt-0 mb-5 pb-3">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="mb-5 d-flex flex-wrap justify-content-between">
                                                    <h3>Products</h3>
                                                    <a href="{{ route('public.business.show_items', ['slug'=>$shop->slug, 'type'=>1]) }}" class="button-secondary">See All</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container mx-auto row g-sm-4 g-3 product-list-section row-cols-xxl-4 row-cols-lg-3 row-cols-md-3 row-cols-2">
                                            @forelse ($products as $key => $prod)
                                                <div>
                                                    <div class="product-box-3 h-100 wow fadeInUp shadow bg-white" style="visibility: visible; animation-name: fadeInUp;">
                                                        <div class="product-header">
                                                            <div class="product-image">
                                                                <a href="{{ route('public.products.show', $prod->slug) }}">
                                                                    <img src="{{ $prod->featured_image == null ? asset('assets/images/default1.jpg') : asset('uploads/item_images/'.$prod->featured_image) }}" class="img-fluid blur-up lazyloaded" alt="">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="product-footer">
                                                            <div class="product-detail">
                                                                <a href="{{ route('public.products.show', $prod->slug) }}">
                                                                    <h5 class="name">{{ $prod->name??'' }}</h5>
                                                                </a>
                                                                <h6 class="unit"><span class="fa fa-location"></span>{{ $shop->contactInfo->location() }}</h6>
                                                                </h5>
                                                                <h5 class="sold text-content">
                                                                    <span class="theme-color price">${{$prod->unit_price}}</span>
                                                                </h5>
                                                                <div class="add-to-cart-box bg-success shadow" >
                                                                    <a  href="https://wa.me/{{ $shop->contactInfo->whatsapp??'' }}?text=I saw {{ $prod->name }} on Errandia. Can we talk more?" class="btn btn-add-cart">Chat on Whatsapp
                                                                        <span class="add-icon bg-light-gray">
                                                                            <i class="fa fa-whatsapp"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                                <div class="add-to-cart-box bg-light shadow" >
                                                                    <a  href="tel:{{ $shop->contactInfo->phone??'' }}" class="btn btn-add-cart">Call
                                                                        <span class="add-icon bg-light-gray">
                                                                            <i class="fa fa-phone"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                                                    <p>Sorry we could not find any product for this shop</p>
                                                </div></div>
                                            @endforelse
                                
                                        </div>
                                    </section>
                                    {{-- PRODUCTS END --}}

                                    {{-- SERVICES START --}}
                                    <section class="breadscrumb-section pt-0 mb-5 pb-3">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="mb-5 d-flex flex-wrap justify-content-between">
                                                    <h3>Services</h3>
                                                    <a href="{{ route('public.business.show_items', ['slug'=>$shop->slug, 'type'=>2]) }}" class="button-secondary">See All</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container mx-auto row g-sm-4 g-3 product-list-section row-cols-xxl-4 row-cols-lg-3 row-cols-md-3 row-cols-2">
                                            @forelse ($services as $key => $prod)
                                                <div>
                                                    <div class="product-box-3 h-100 wow fadeInUp shadow bg-white" style="visibility: visible; animation-name: fadeInUp;">
                                                        <div class="product-header">
                                                            <div class="product-image">
                                                                <a href="{{ route('public.products.show', $prod->slug) }}">
                                                                    <img src="{{ $prod->featured_image == null ? asset('assets/images/default1.jpg') : asset('uploads/item_images/'.$prod->featured_image) }}" class="img-fluid blur-up lazyloaded" alt="">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="product-footer">
                                                            <div class="product-detail">
                                                                <a href="{{ route('public.products.show', $prod->slug) }}">
                                                                    <h5 class="name">{{ $prod->name??'' }}</h5>
                                                                </a>
                                                                <h6 class="unit"><span class="fa fa-location"></span>{{ $shop->contactInfo->location() }}</h6>
                                                                </h5>
                                                                <h5 class="sold text-content">
                                                                    <span class="theme-color price">${{$prod->unit_price}}</span>
                                                                </h5>
                                                                <div class="add-to-cart-box bg-success shadow" >
                                                                    <a  href="https://wa.me/{{ $shop->contactInfo->whatsapp??'' }}?text=I saw {{ $prod->name }} on Errandia. Can we talk more?" class="btn btn-add-cart">Chat on Whatsapp
                                                                        <span class="add-icon bg-light-gray">
                                                                            <i class="fa fa-whatsapp"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                                <div class="add-to-cart-box bg-light shadow" >
                                                                    <a  href="tel:{{ $shop->contactInfo->phone??'' }}" class="btn btn-add-cart">Call
                                                                        <span class="add-icon bg-light-gray">
                                                                            <i class="fa fa-phone"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                                                    <p>Sorry we could not find any service for this shop</p>
                                                </div></div>
                                            @endforelse
                                
                                        </div>
                                    </section>
                                    {{-- SERVICES END --}}

                                    {{-- Tabbed content Start --}}
                                    <section>
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="product-section-box m-0">
                                                        <ul class="nav nav-tabs custom-nav no-scrollbar" id="myTab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="care-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="care" aria-selected="false">Profile</button>
                                                            </li>
                                                        

                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab" aria-controls="review" aria-selected="true">Reviews</button>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content custom-tab" id="myTabContent">
                                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="care-tab">
                                                                
                                                            </div>

                                                            <div class="tab-pane fade active show" id="review" role="tabpanel" aria-labelledby="review-tab">
                                                                <div class="review-box">
                                                                    <div class="row g-4">
                                                                        <div class="col-xl-6">
                                                                            <div class="review-title">
                                                                                <h4 class="fw-500">Customer reviews</h4>
                                                                            </div>

                                                                            <div class="d-flex">
                                                                                <div class="product-rating">
                                                                                    <ul class="rating">
                                                                                        @for($i = 1; $i <= 5; $i++)
                                                                                            <li>
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star {{ $average_rating >= $i ? 'fill' : '' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                                                            </li>
                                                                                        @endfor
                                                                                    </ul>
                                                                                </div>
                                                                                <h6 class="ms-3">{{ $average_rating }} Out Of 5</h6>
                                                                            </div>

                                                                            <div class="rating-box">
                                                                                <ul>
                                                                                    <li>
                                                                                        <div class="rating-list">
                                                                                            <h5>5 Star</h5>
                                                                                            <div class="progress">
                                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $rating5 }}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                                                                    {{ $rating5 }}%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>

                                                                                    <li>
                                                                                        <div class="rating-list">
                                                                                            <h5>4 Star</h5>
                                                                                            <div class="progress">
                                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $rating4 }}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                                                                    {{ $rating4 }}%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>

                                                                                    <li>
                                                                                        <div class="rating-list">
                                                                                            <h5>3 Star</h5>
                                                                                            <div class="progress">
                                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $rating3 }}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                                                                    {{ $rating3 }}%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>

                                                                                    <li>
                                                                                        <div class="rating-list">
                                                                                            <h5>2 Star</h5>
                                                                                            <div class="progress">
                                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $rating2 }}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                                                                    {{ $rating2 }}%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>

                                                                                    <li>
                                                                                        <div class="rating-list">
                                                                                            <h5>1 Star</h5>
                                                                                            <div class="progress">
                                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $rating1 }}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                                                                    {{ $rating1 }}%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-xl-6 h-100 d-flex flex-column justify-content-center">
                                                                            <a href="#" class="button-secondary my-5 mx-auto">Login to Add Your Review</a>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="review-title">
                                                                                <h4 class="fw-500">Customer Reviews</h4>
                                                                            </div>

                                                                            <div class="review-people">
                                                                                <ul class="review-list">
                                                                                    @forelse ($reviews as $review)
                                                                                        <li>
                                                                                            <div class="people-box">
                                                                                                <div>
                                                                                                    <div class="people-image">
                                                                                                        <img src="{{ $review->product->featured_image == null ? asset('assets/images/default1.jpg') : asset('uploads/item_images/'.$review->product->featured_image??'') }}"
                                                                                                            class="img-fluid blur-up lazyload" alt="">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="people-comment">
                                                                                                    <div class="pull-right d-flex justify-content-between">
                                                                                                        <a class="name" href="javascript:void(0)">{{ substr($review->user->name??'', 1, 3) }}*** <span class="text-content pl-4 h6">{{ $review->created_at->diffForHumans() }}</span></a> 
                                                                                                        @if($review->user->id == auth()->id())
                                                                                                            <a href="{{ route('public.delete_review', $review->id) }}" class="text-danger pull-right"><span class="fa fa-trash"></span> Delete</a>
                                                                                                        @endif
                                                                                                        @if($review->product->shop->user_id == auth()->id())
                                                                                                            <button class="text-danger border-0" data-bs-toggle="modal" data-review_id ="{{ $review->id }}" data-review_text="{{ $review->review }}" onclick="reportReview(event)" data-bs-target="#add-address"><i class="me-2 fa fa-flag"></i> report</button>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                    <div class="date-time">
                                                                                                        <p>{!! $review->review??'' !!}</p>

                                                                                                        <div class="product-rating">
                                                                                                            <ul class="rating">
                                                                                                                @for($i = 1; $i < 6; $i++)
                                                                                                                    <li>
                                                                                                                        <i data-feather="star"class="{{ $review->rating >= $i ? 'fill' : ''  }}"></i>
                                                                                                                    </li>
                                                                                                                @endfor
                                                                                                            </ul>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    

                                                                                                    <div class="d-flex flex-wrap g-2">
                                                                                                        @foreach ($review->images as $image)
                                                                                                            <img src="{{ asset('uploads/review_images/'.$image->image??'') }}" alt="" srcset="" class="img img-thumbnail m-1" style="height:5rem; width:5rem;">
                                                                                                        @endforeach
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @empty
                                                                                        <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                                                                                            <p>0 reviews found</p>
                                                                                        </div></div>
                                                                                    @endforelse
                                                                                    

                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    {{-- Tabbed content End--}}


                                    <div class="container-fluid py-4">
                                        <h2>Business Branches</h2>
                                    </div>
                                    <div class="col-custome-12">
                                        <div class="row g-sm-4 g-3 product-list-section row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-2">
                                            @forelse ($branches as $key=>$branch)
                                                <div>
                                                    <div class="product-box-3 h-100 wow fadeInUp shadow bg-white" style="visibility: visible; animation-name: fadeInUp;">
                                                        <div class="product-header">
                                                            <div class="product-image">
                                                                <a href="{{ route('public.business.show', $branch->slug) }}">
                                                                    <img src="{{ $branch->image_path == null ? asset('assets/images/default1.jpg') : asset('uploads/logos/'.$branch->image_path) }}" class="img-fluid blur-up lazyloaded" alt="">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="product-footer">
                                                            <div class="product-detail">
                                                                <a href="{{ route('public.business.show', $branch->slug) }}">
                                                                    <h5 class="name">{{ $branch->name??'' }}</h5>
                                                                </a>
                                                                <h6 class="unit"><span class="fa fa-location"></span>{{ $branch->contactInfo->location() ?? '' }}</h6>
                                                                </h5>
                                                                <div class="add-to-cart-box bg-white shadow" >
                                                                    <a  href="{{ route('public.business.show', $branch->slug) }}" class="btn btn-add-cart">Check this Business
                                                                        <span class="add-icon bg-light-gray">
                                                                            <i class="fa fa-business-time"></i>
                                                                        </span>
                                                                    </a>
                                                                    <div class="cart_qty qty-box">
                                                                        <div class="input-group bg-white">
                                                                            <button type="button" class="qty-left-minus bg-gray" data-type="minus" data-field="">
                                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                                            </button>
                                                                            <input class="form-control input-number qty-input" type="text" name="quantity" value="0">
                                                                            <button type="button" class="qty-right-plus bg-gray" data-type="plus" data-field="">
                                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                                                    <p>0 Branches found</p>
                                                </div></div>
                                            @endforelse

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-custome-12">
            </div>
        </div>
    </section>
    <section class="breadscrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadscrumb-contain">
                        <h2>Related Businesses</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mx-auto row g-sm-4 g-3 product-list-section row-cols-xl-4 row-cols-lg-3 row-cols-md-3 row-cols-2">
            @forelse ($related_shops as $key=>$shop)
                <div>
                    <div class="product-box-3 h-100 wow fadeInUp shadow bg-white" style="visibility: visible; animation-name: fadeInUp;">
                        <div class="product-header">
                            <div class="product-image">
                                <a href="{{ route('public.business.show', $shop->slug) }}">
                                    <img src="{{ $shop->image_path == null ? asset('assets/images/default1.jpg') : asset('uploads/logos/'.$shop->image_path) }}" class="img-fluid blur-up lazyloaded" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-detail">
                                <a href="{{ route('public.business.show', $shop->slug) }}">
                                    <h5 class="name">{{ $shop->name??'' }}</h5>
                                </a>
                                <h6 class="unit"><span class="fa fa-location"></span>{{ $shop->contactInfo->location() }}</h6>
                                </h5>
                                <div class="add-to-cart-box bg-white shadow" >
                                    <a  href="{{ route('public.business.show', $shop->slug) }}" class="btn btn-add-cart">Check this Business
                                        <span class="add-icon bg-light-gray">
                                            <i class="fa fa-business-time"></i>
                                        </span>
                                    </a>
                                    <div class="cart_qty qty-box">
                                        <div class="input-group bg-white">
                                            <button type="button" class="qty-left-minus bg-gray" data-type="minus" data-field="">
                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                            </button>
                                            <input class="form-control input-number qty-input" type="text" name="quantity" value="0">
                                            <button type="button" class="qty-right-plus bg-gray" data-type="plus" data-field="">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card bg-light my-2 w-100 py-1"><div class="card-body text-center px-3 py-1">
                    <p>0 related businesses found</p>
                </div></div>
            @endforelse

        </div>
    </section>
@endsection
@section('script')

@endsection
