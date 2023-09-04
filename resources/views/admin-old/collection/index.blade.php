@extends('admin.layouts.app')
@section('page', 'Collection')

@section('content')
<section>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="search__filter">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-3">
                                <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-auto">
                                <form action="{{ route('admin.collections.index')}}" method="GET">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
							<div class="col-auto">
                                <a href="{{ route('admin.collections.create') }}" class="btn btn-outline-danger btn-sm">
                                    Create New Collection
                                </a>
						    </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.collections.csv.export',['term'=>$request->term]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    CSV
                                </a>
                        </div>
                    </div>
                        <table class="table" id="example5">
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
                                                <button type="submit" onclick="return confirm('Are you sure ?')" class="btn btn-link" style="padding: 0;margin: 0;font-size: 14px;line-height: 1;text-decoration: none;color: #dc3545;">Delete</button>
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
@endsection
@section('script')

@endsection
