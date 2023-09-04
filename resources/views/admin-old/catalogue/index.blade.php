@extends('admin.layouts.app')
@section('page', 'Catalogue')

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
                                <form action="{{ route('admin.catalogues.index')}}">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                                    </div>
                                    <div class="col-auto">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Filter
                                            </button>
            
                                            <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </a>
                                            <div class="col-auto">
                                                <a href="{{ route('admin.catalogues.create') }}" class="btn btn-outline-danger btn-sm">
                                                    Create New Catalogue
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </form>
                            </div>
							
                            
                    </div>
                        <table class="table" id="example5">
                            <thead>
                                <tr>
                                    <th>#SR</th>
                                    <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                    <th class="text-center"> PDF</th>
                                    <th>Title</th>
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
                                        <img src="{{ asset($item->image) }}" style="max-width: 80px;max-height: 80px;">
                                    </td>
                                    <td class="text-center column-thumb">
                                        
										  <a class="btn btn-sm btn-info" href="{{ asset($item->pdf) }}"  download><i class="app-menu__icon fa fa-download"></i> Download</a>
                                    </td>
                                    <td>
                                        <h3 class="text-dark">{{$item->name}}</h3>
                                        <div class="row__action">
                                            <form action="{{ route('admin.catalogues.destroy',$item->id) }}" method="POST">
                                                <a href="{{ route('admin.catalogues.edit', $item->id) }}">Edit</a>
                                                <a href="{{ route('admin.catalogues.show', $item->id) }}">View</a>
                                                <a href="{{ route('admin.catalogues.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure ?')" class="btn btn-link" style="padding: 0;margin: 0;font-size: 14px;line-height: 1;text-decoration: none;color: #dc3545;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>{{date('d M Y', strtotime($item->start_date))}} - {{ date('d M Y', strtotime($item->end_date))}}</td>
                                    {{-- <td>Published<br/>{{date('d M Y', strtotime($item->created_at))}}</td> --}}
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
