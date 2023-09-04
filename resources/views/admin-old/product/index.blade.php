@extends('admin.layouts.app')
@section('page', 'Products')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                <div class="col-md-3">
                    <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                </div>
                <div class="col-md-9 text-end">
                    <form class="row align-items-end" action="{{ route('admin.products.index') }}">
                        <div class="col">
                            <select class="form-select form-select-sm select2" aria-label="Default select example"      name="cat_id" id="category">
                                <option value=""  selected>Select Category</option>
                                 @foreach ($category as $index => $item)
                                             <option value="{{$item->id}}" {{ (request()->input('cat_id') == $item->id) ? 'selected' :  '' }}>{{ $item->name }}</option>
                                 @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-sm select2" aria-label="Default select example" name="collection_id" id="collection">
                                <option value="" selected disabled>Select Collection</option>
                                <option value="{ (request()->input('collection_id')) ? 'selected' :  '' }}"></option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ style no." value="{{app('request')->input('keyword')}}" autocomplete="off">
                        </div>
                        <div class="col">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a href="{{ route('admin.products.csv.export',['collection_id'=>$request->collection_id,'cat_id'=>$request->cat_id,'term'=>$request->term]) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    CSV
                                </a>
                                
                               
                            </div>
                            
                        </div>
                        <div class="col">
                            <div class="btn-group">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-danger btn-sm">
                                    Create New Product
                                </a>
                            </div>
                            
                        </div>
                    </form>
                </div>
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
                <th>Category</th>
                <th>Range</th>
                <th>Price</th>
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
                <td>{{($data->firstItem()) + $index}}</td>
                 @if($item->image == "uploads/product/polo_tshirt_front.png" ||
                                                    !file_exists($item->image))
					 <td class="text-center column-thumb">
						<img src="{{asset('admin/images/default-placeholder-product-image.png')}}" />
					</td>
                
                @else
				   <td class="text-center column-thumb">
						<img src="{{asset($item->image)}}" />
					</td>
                @endif
                <td>
                    {{$item->name}}
                    <div class="row__action">
                        <form action="{{ route('admin.products.destroy',$item->id) }}" method="POST">
                            <a href="{{ route('admin.products.edit', $item->id) }}">Edit</a>
                            <a href="{{ route('admin.products.show', $item->id) }}">View</a>
                            <a href="{{ route('admin.products.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                            @csrf
                            @method('DELETE')
                           <button type="submit" onclick="return confirm('Are you sure ?')" class="btn btn-link" style="padding: 0;margin: 0;font-size: 14px;line-height: 1;text-decoration: none;color: #dc3545;">Delete</button> 
                    </div>
                </td>
                <td>{{$item->style_no}}</td>
                <td>
                    <a href="{{ route('admin.categories.show', $item->category->id) }}">{{$item->category ? $item->category->name : ''}}</a>
                </td>
                <td>
                    <a href="{{ route('admin.collections.show', $item->collection->id) }}">{{$item->collection ? $item->collection->name : ''}}</a>
                </td>
                
                <td>
                    
					Rs. {{$item->offer_price}}
                </td>

                 <td>Published<br/>{{date('j M Y', strtotime($item->created_at))}}</td> 
                <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
            </tr>
            @empty
            <tr><td colspan="100%" class="small text-muted text-center">No data found</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
</section>
@endsection
@section('script')
    <script>
		$('select[id="category"]').on('change', (event) => {
			var value = $('select[id="category"]').val();

			$.ajax({
				url: '{{url("/")}}/api/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="collection"]';
					var displayCollection =  "All";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data, (key, value) => {
						content += '<option value="'+value.id+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

   
@endsection
