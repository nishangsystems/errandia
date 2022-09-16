@extends('helep.admin.layout.master')
@section('page_title') @lang('admin.subscriptions_add_title_msg') @endsection
@section('title') @lang('admin.subscriptions_add_title_msg') @endsection
@section('content')
    <div class="">
        <div class="p-2 shadow-sm">
            <div class=" d-flex justify-content-between">
                <div class="d-flex flex-wrap"><a href="{{route('street')}}">
                        <button type="button" class="btn helep_btn_raise">
                            <i class="fa fa-arrow-left pr-1"></i>{{trans('admin.return_back_msg')}}</button>
                    </a></div>
                <div class="d-flex flex-wrap"></div>
                <div class="d-flex flex-wrap"></div>
            </div>
            <div class="clearfix"><br/></div>
            <form enctype="multipart/form-data" method="POST" action="{{route('save_subscription')}}"
                  class="my-2 d-flex-column align-items-center px-sm-5">
                @csrf
                <div class="form-group w-100">
                    <h5 class="text-black-50 font-weight-bold  mb-2 p-2">Title</h5>
                    <div class="row">
                        <div class="col-md-8 ">
                            <div class="input-group border-with-radius mb-3">
                                <input value="" name="name" type="text" class="form-control"
                                       placeholder="Title">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    
                    <h5 class="text-black-50 font-weight-bold  mb-2 p-2">Description</h5>
                    <div class="row">
                        <div class="col-md-8 ">
                            <div class="input-group mb-3">
                                <textarea class="form-control html-editor" name="description" required placeholder="Description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    
                    <h5 class="text-black-50 font-weight-bold  mb-2 p-2">Amount </h5>
                    <div class="row">
                        <div class="col-md-8 ">
                            <div class="input-group border-with-radius mb-3">
                                <input value="" name="amount" type="text" class="form-control"
                                       placeholder="Amount">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    
                    <h5 class="text-black-50 font-weight-bold  mb-2 p-2">Duration (Days)</h5>
                    <div class="row">
                        <div class="col-md-8 ">
                            <div class="input-group border-with-radius mb-3">
                                <input value="" name="duration" type="text" class="form-control"
                                       placeholder="Duration">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    
                </div>
                <div class="align-self-start d-flex-column mb-2">
                    <button class="btn helep_btn_raise px-5 shadow w-100">{{trans('admin.add_subscriptions_btn')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('css')
<link href="{{asset('summernote/dist/summernote.css')}}" rel="stylesheet">
@stop
@section('js')
<script src="{!!asset("summernote/dist/summernote-updated.min.js")!!}"></script>
    <script>
        $(function () {
            //set link indicator
            $("#admin_manage_subscription").addClass('active');
			loadHtmlEditor();
        });
		
		function loadHtmlEditor() {
			if ($('.html-editor')[0]) {
				$('.html-editor').summernote({
					height: 300
				});
			}
			if ($('.html-editor-click')[0]) {
				//Edit
				$('body').on('click', '.hec-button', function () {
					$('.html-editor-click').summernote({
						focus: true
					});
					$('.hec-save').show();
				})
				//Save
				$('body').on('click', '.hec-save', function () {
					$('.html-editor-click').code();
					$('.html-editor-click').destroy();
					$('.hec-save').hide();
					notify('Content Saved Successfully!', 'success');
				});
			}
			//Air Mode
			if ($('.html-editor-airmod')[0]) {
				$('.html-editor-airmod').summernote({
					airMode: true
				});
			}
		}
    </script>
@endsection
