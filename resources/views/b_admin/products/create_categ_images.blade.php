@extends('b_admin.layout')
@section('section')
    <div class="container">
        <div class="d-flex py-3 my-2 px-2">
            <span class="text-h4 d-block">Add New Product For {{ $shop->name }} <i class="text-link">({{ $shop->location() }})</i></span>
        </div>
        <form method="POST" action="{{ route('business_admin.products.create_update', ['product' =>$product]) }}" enctype="multipart/form-data">
            @csrf
            <div class="py-1 my-5 py-5 px-5 border bg-white" style="border-radius: 1rem;">
                <span class="d-block mt-4" style="font-weight: 700;">Categories *</span>
                <div class="d-flex flex-wrap my-3 border-left border-right rounded">
                    @foreach ($categories as $cat)
                        <span class="d-inlineblock rounded border bg-light py-1 px-3 my-2 mx-2">
                            <input type="checkbox" class="input mx-2" name="categories[]" value="{{ $cat->id }}">
                            <span class="text-extra">{{ $cat->name }}</span>
                        </span>
                    @endforeach
                </div>
                <span class="d-block mt-4" style="font-weight: 700;">Product image gallery*</span>
                <div class="d-block">

                </div>
                <div style="width: 0; height: 0; overflow: hidden;" class="imageFieldsContainer">
                    <input type="file"  accept="image/*" id="openFileUpload" >
                </div>
                <div class="py-3 px-3 add-btn">
                    <a title="add image"><span class="fa fa-plus fa-4x border rounded p-4 text-primary bg-light"></span></a>
                </div>
            </div>
            <span class="d-block my-4"><button class="button-primary" type="submit">NEXT</button></span>
        </form>
    </div>
@endsection
@section('script')
    <script>
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $('.add-btn').on('click', function (e){
            $('#openFileUpload').click().on("change",function (e){
                console.log(e.target.files[0])
            })
        })

        // let _id = ((Math.random()*100000000)+Date.now()+crypto.randomUUID()).replace('.', '');
        // let set_id = function(){
        //     _id = ((Math.random()*100000000)+Date.now()+crypto.randomUUID()).replace('.', '');
        // }
        // let get_id = function(){
        //     return _id;
        // }
        //
        // $(document).ready(function(){
        //     init();
        // });
        //
        let init = function(){
            $('.multipleImageUplaoder').each((index, elem)=>{
                let ___trigger = `<div class="d-flex flex-wrap multipleImageContainer py-3"></div>
                    <div style="width: 0; height: 0; overflow: hidden;" class="imageFieldsContainer">
                        <input type="file" name="gallery[]", accept="image/*" id="${get_id()}" onchange="preview('${get_id()}', '${index}')">
                    </div>
                    <div class="py-3 px-3">
                        <a title="add image" onclick="addImage(${index})"><span class="fa fa-plus fa-4x border rounded p-4 text-primary bg-light"></span></a>
                    </div>`;
                $(elem).append(___trigger);
            })
        }
        // let refresh = function(index){
        //     let field = `<input type="file" name="gallery[]", accept="image/*" id="${get_id()}" onchange="preview('${get_id()}', '${index}')">`;
        //     let container = $('.multipleImageUplaoder').get(index).children.item(1);
        //     $(container).append(field);
        // }
        //
        // let addImage = function(index){
        //     $("#"+_id).click();
        //     // let field = `<input type="file" name="gallery[]", accept="image/*" id="${get_id()}" onchange="preview('${get_id()}', '${index}')">`;
        // }
        // let saveImage = function (file) {
        //     console.log(file)
        //     $.ajax({
        //         method: 'POST',
        //         url: "badmin/products/save_images",
        //         headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
        //         data:{
        //             image:file
        //         },
        //         success: function(response){
        //             console.log(response)
        //         }
        //     })
        // }
        //
        // let preview = function(field_id, index){
            let file = document.getElementById(field_id).files[0];
            let url = URL.createObjectURL(file);
            let image = `<div>
                    <img class="mx-2 my-2" style="width: 12rem; height: 12rem; border-radius: 0.6rem;" src="${url}">
                    <span class="fa fa-close text-danger text-center d-block py-1 px-2 my-1 rounded bg-light border"></span>
                </div>`;
        //     //
        //     // let container = $('.multipleImageUplaoder').get(index).children.item(0);
        //     // $(container).append(image);
        //     saveImage(file);
        //     // set_id();
        //     // refresh(index);
        // }
        //
        // let dropImage = function(_input_id){
        //     $(document).remove('#'+_input_id);
        // }


    </script>
@endsection