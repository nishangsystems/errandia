@extends('admin.layout')
@section('section')
    <div class="py-2">
        <div class="card">
            <div class="card-body p-3">
                <h5>Edit FAQ Item :: {{ $item->title }}</h5><hr>
                <form method="POST" id="editor-form">
                    @csrf
                    <div class="my-2 d-flex justify-content-end">
                        <input type="submit" class="btn btn-primary btn-xs" value="save">
                    </div>
                    <div class="my-2">
                        <label class="text-info d-block">Title</label>
                        <input class="form-control" name="title" placeholder="Enter title here" value="{{ old($item->title, 'title') }}">
                    </div>
                    <input type="hidden" id="editor_field">
                    <div class="my-2">
                        <textarea class="form-control adv-editor" name="content"  style="height: 13rem;" id="myeditorinstance">
                            {{ old($item->content, 'content') }}
                        </textarea>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
@endsection
{{-- @section('script')
    <script>
        $('#editor-form').on('submit', function(){
            $(this).preventDefault();
            $('#editor_field').val($('#quill_editor_1'));
            $(this).submit();
        })
    </script>
@endsection --}}