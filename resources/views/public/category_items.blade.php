@extends('public.layout')
@section('section')
    <section class="breadscrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadscrumb-contain">
                        <h2>{{$region->name??''}}</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">
                                        <i class="fa-solid fa-house"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $data['title'] ?? 'Items' }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="section-b-space shop-section">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-custome-3">
                    <div class="left-box wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                        <div class="shop-left-sidebar">
                            <div class="location-list nav-link">
                                <form method="GET" action="{{ route('public.businesses', ['region_id' => ""]) }}">
                                    <div class="search-input my-3">
                                        <select class="form-control" oninput="loadTowns(event)" name="region_id">
                                            <option>Region</option>
                                            @foreach($regions as $key => $region)
                                                <option value="{{$region->id}}" >
                                                    <a href="{{ route('public.errands', ['region' =>$region->name]) }}">{{$region->name}}</a>
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="search-input my-3">
                                        <select class="form-control" oninput="loadStreets(event)" id="town_selection" name="town_id">
                                            <option>Town</option>
                                        </select>
                                    </div>
                                    <div class="search-input my-3">
                                        <select class="form-control" name="street_id">
                                            <option>Street</option>
                                        </select>
                                    </div>
                                    <button class="button-primary w-100" type="submit">
                                        Apply filter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-custome-9">
                    <div class="show-button">
                        <div class="filter-button d-inline-block d-lg-none">
                            <a><i class="fa-solid fa-filter"></i> Filter Menu</a>
                        </div>
                        <div class="top-filter-menu">
                            <div class="category-dropdown">
                                <h5 class="text-content">Sort By :</h5>
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown">
                                        <span>Most Popular</span> <i class="fa-solid fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li>
                                            <a class="dropdown-item" id="aToz" href="{{route('public.businesses', ['orderBy' => "DESC"])}}">A - Z Order</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" id="zToa" href="{{route('public.businesses', ['orderBy' => "ASC"])}}">Z - A Order</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="grid-option d-none d-md-block">
                                <ul>
                                    <li class="three-grid active">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('assets/public/assets/svg/grid-3.svg') }}" class="blur-up lazyloaded" alt="">
                                        </a>
                                    </li>
                                    <li class="grid-btn d-xxl-inline-block d-none">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('assets/public/assets/svg/grid-4.svg') }}" class="blur-up lazyload d-lg-inline-block d-none" alt="">
                                            <img src="{{ asset('assets/public/assets/svg/grid.svg') }}" class="blur-up lazyload img-fluid d-lg-none d-inline-block" alt="">
                                        </a>
                                    </li>
                                    <li class="list-btn">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('assets/public/assets/svg/list.svg') }}" class="blur-up lazyloaded" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row g-sm-4 g-3 product-list-section row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-2">
                        @foreach($items as $key => $item)
                            <div>
                                <div class="product-box-3 h-100 wow fadeInUp shadow-md border" style="visibility: visible; animation-name: fadeInUp;">
                                    <div class="product-header">
                                        <div class="product-image">
                                            <a href="{{ route('public.products.show', ['slug' => $item->slug]) }}">
                                                <img src="{{ $item->featured_image == null ? asset('assets/images/default1.jpg') : asset('uploads/item_images/'.$item->featured_image) }}" class="img-fluid blur-up lazyloaded" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="product-footer">
                                        <div class="product-detail">
                                            <a href="{{ route('public.products.show', ['slug' => $item->slug]) }}">
                                                <h5 class="name">{{$item->name}}</h5>
                                            </a>
                                            <h6 class="unit">{{ $item->description ?? '' }}</h6>
                                            <a href="{{ route('public.business.show', ['slug' => $item->slug]) }}">
                                                <h5 class="name">{{$item->shop->name}}</h5>
                                            </a>
                                            <h6 class="unit"><span class="fa fa-location"></span>{{$item->shop->contactInfo->location()}}</h6>
                                            </h5>
                                            <div class="add-to-cart-box bg-white shadow" >
                                                <a  href="{{ route('public.business.show', ['slug' => $item->shop->slug]) }}" class="btn btn-add-cart">Check this Business
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
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        let loadTowns = function (evt) {
            let regionId = evt.target.value;
            if (regionId != null) {
                let route = "{{ route('region.towns', '__ID__') }}".replace('__ID__', regionId);
                $.ajax({
                    method: 'get', url: route, success: function (response) {
                        if (response.data != null) {
                            let html = `<option>Town</option>`;
                            response.data.forEach(element => {
                                html += `<option value="${element.id}">${element.name}</option>`;
                            })
                            $('#town_selection').html(html);
                        }
                    }
                })
            }
        }

        let loadStreets = function (event) {
            let town = event.target.value;
            if (town != null) {
                let route = "{{ route('town.streets', '__ID__') }}".replace('__ID__', town);
                $.ajax({
                    method: 'get', url: route, success: function (response) {
                        if (response.data != null) {
                            let html = `<option>Street</option>`;
                            response.data.forEach(element => {
                                html += `<option value="${element.id}">${element.name}</option>`;
                            })
                            $('#street_selection').html(html);
                        }
                    }
                })
            }
        }


    </script>
@endsection