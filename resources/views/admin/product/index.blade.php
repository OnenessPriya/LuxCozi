@extends('admin.layouts.app')
@section('page', 'Products')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                
               
            </div>
        </div>
    </div>

    <table class="table" id="example5">
        <thead>
            <tr>
                <th>#SR</th>
                <th class="text-center"><i class="fi fi-br-picture"></i></th>
                <th>Name</th>
                <th>Style No.</th>
                <th>Range</th>
                <th>Category</th>
                <th>Price</th>
                {{-- <th>Action</th>
                <th>Date</th> --}}
                <th>Status</th>
            </tr>
        </thead>
        
    </table>

   
</section>
@endsection
@section('script')

@endsection
