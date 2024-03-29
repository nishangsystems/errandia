@extends('admin.layout')
@section('section')
    <div class="py-2">
        <div class="d-flex justify-content-end py-3 px-2">
            <a href="{{ route('admin.locations.streets.create') }}" class=" btn btn-primary bg-sm py-2 px-4 text-white text-capitalize rounded"><span class="text-white fa fa-plus mx-2"></span>Add new street</a>
        </div>
        <div class="py-1 px-2 d-flex">


            <table class="table">
                <thead class="text-capitalize">
                    <th></th>
                    <th>name</th>
                    <th>town</th>
                    <th>region</th>
                    <th>action</th>
                </thead>
                <tbody>
                    @php $k = 1;
                    @endphp
                    @foreach($streets as $key => $st)
                        <tr class="shadow-sm border-bottom">
                            <td>{{ $k++}}</td>
                            <td>{{ $st->name??'' }}</td>
                            <td>{{ $st->town->name??'' }}</td>
                            <td>{{ $st->town->region->name??'' }}</td>
                            <td>
                                <a href="{{ route('admin.locations.streets.edit', $st->id) }}" class="text-secondary"><img src="{{ asset('assets/admin/icons/icon-edit.svg') }}" style="height: 1.3rem; width: 1.3rem; margin-right: 1rem;"></a>
                                <a href="#" onclick="_prompt(`{{ route('admin.locations.streets.delete', $st->id) }}`, 'Are you sure you intend to delete this item? This process cannot be undone.')" class="text-danger"><img src="{{ asset('assets/admin/icons/icon-trash.svg') }}" style="height: 1.3rem; width: 1.3rem; margin-right: 1rem;"></a>
                            </td>
                        </tr>
                    @endforeach()
                </tbody>
            </table>
        </div>
    </div>
@endsection