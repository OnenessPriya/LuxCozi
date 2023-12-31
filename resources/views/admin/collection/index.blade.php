@extends('admin.layouts.app')
@section('page', 'Collection')

@section('content')
<section class="pro-sec">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="search__filter mb-5">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-3">
                                <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-md-9">
                                <div class="search-filter-right">
                                    <div class="search-filter-right-el">
                                        <form action="{{ route('admin.collections.index')}}" method="GET">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-auto">
                                                    <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"><iconify-icon icon="tabler:search"></iconify-icon> Filter</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="search-filter-right-el">
                                        <a href="{{ route('admin.collections.create') }}" class="btn btn-outline-danger btn-sm">
                                            <iconify-icon icon="prime:plus-circle"></iconify-icon>  Create New Collection
                                        </a>
                                    </div>
                                    <div class="search-filter-right-el">
                                        <a href="{{ route('admin.collections.csv.export',['term'=>$request->term]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                            <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <table class="table admin-table" id="example5">
                            <thead>
                                <tr>
                                    <th>#SR</th>
                                    <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Products</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                @php
                                if (!empty($_GET['status'])) {
                                    if ($_GET['status'] == 'active') {
                                        if ($item->status == 0) continue;
                                    } else {
                                        if ($item->status == 1) continue;
                                    }
                                }
                                @endphp
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td class="text-center column-thumb">
                                    @if(!empty($item->icon_path))
                                    <img src="{{ asset($item->icon_path) }}" style="max-width: 80px;max-height: 80px;">
                                    @else
                                    <img src="{{asset('admin/images/product-box.png')}}" style="max-width: 50px;max-height: 50px;">
                                    @endif
                                    </td>
                                   
                                    <td>
                                        <h3 class="text-dark">{{$item->name}}</h3>
                                        
                                        <div class="row__action">
                                            <form action="{{ route('admin.collections.destroy',$item->id) }}" method="POST">
                                                <a href="{{ route('admin.collections.edit', $item->id) }}">Edit</a>
                                                <a href="{{ route('admin.collections.show', $item->id) }}">View</a>
                                                <a href="{{ route('admin.collections.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>{{$item->cat->name}}</td>
                                    <td>
                                        <a href="{{ route('admin.collections.show', $item->id) }}">{{$item->ProductDetails->count()}} products</a>
                                    </td>
                                    <td>Published<br/>{{date('d M Y', strtotime($item->created_at))}}</td>
                                    <td>
                                        <span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            {{ $data->appends($_GET)->links() }}
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

@endsection
@section('script')

@endsection
