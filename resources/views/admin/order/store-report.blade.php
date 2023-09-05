
@extends('admin.layouts.app')

@section('page', 'Secondary Order')
@section('content')
<section>
    @if (request()->input('store'))
        <p class="text-muted">{{request()->input('name')}}</p>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center justify-content-end">
                <div class="col">
                    <p class="small text-muted mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Orders</p>
                </div>
                <div class="col-auto">
                    <form action="{{ route('admin.orders.index') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label for="date_from" class="text-muted small">Date from</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                            </div>
                            <div class="col-auto">
                                <label for="date_to" class="text-muted small">Date to</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_to')) ? request()->input('date_to') : '' }}">
                            </div>
                            <div class="col-auto">
                                <label for="ase" class="small text-muted">Sales Person(ASE/ASM)</label>
                                <select class="form-select form-select-sm select2" id="ase" name="user_id">
                                    <option value="" selected disabled>Select</option>
                                    @foreach ($user as $item)
                                        <option value="{{$item->id}}" {{ (request()->input('user_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="store_id" class="small text-muted">Store</label>
                                <select class="form-select form-select-sm select2" id="store_id" name="store_id">
                                    <option value="" selected disabled>Select</option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}" {{ (request()->input('store_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-filter-right search-filter-right-store mt-4">
                                <div class="col-auto">
                                    <label for="term" class="small text-muted">Keyword</label>
                                    <input type="search" name="term" class="form-control form-control-sm" placeholder="Search order no" id="term" value="{{app('request')->input('term')}}" autocomplete="off">
                                </div>
                                <div class="search-filter-right-el">
                                    <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                        <iconify-icon icon="carbon:filter"></iconify-icon> Filter
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                        <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                    </a>
                                </div>
                                <div class="search-filter-right-el">
                                        <a href="{{ route('admin.orders.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'user_id'=>$request->user_id,'store_id'=>$request->store_id,'term'=>$request->term]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="" data-bs-original-title="Export data in CSV">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                </div>
                            </div>
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
            <th>Order No</th>
            <th>Report</th>
            <th>Store</th>
			<th>Sales Person</th>
			<th>Sales Person Mobile</th>
			<th>Sales Person WhatsApp Number</th>
			<th>Sales Person Pincode</th>
            <th>Order Type</th>
            <th>Order time</th>
            <th>Note</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
			    @php
                  $validOrder=DB::table('order_products')->where('order_id',$item->id)->get();
                @endphp
            <tr>
                <td>
                   {{($data->firstItem()) + $index}}
                </td>
                <td>
                    <p class="small text-dark mb-1">{{$item->order_no}}</p>
                   
                </td>
                
                @if(count($validOrder)==0)
                <td>
                   <p class="text-danger">Invalid Order</p>
                </td>
                @else
                <td>
                   {{-- <div class="btn-group">
                        <a href="{{ route('api.order.report', $item->id) }}" class="btn btn-sm btn-primary">PDF</a>
                        <a href="{{ route('admin.order.report.csv', $item->id) }}" class="btn btn-sm btn-primary">CSV</a>
                    </div>--}}
                </td>
                @endif
                <td>
                    <p class="small text-muted mb-1"> {{$item->stores ? $item->stores->name : ''}}</p>
                </td>
				<td>
					@if(!empty($item->users))
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->name : ''}}</p>
					@else
					 <p class="small text-danger mb-1"> No ASE,Self order</p>
					@endif
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->mobile : ''}}</p>
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->whatsapp_no : ''}}</p>
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->pin : ''}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->order_type}}</p>
                </td>
                <td>
                    <p class="small">{{date('j M Y g:i A', strtotime($item->created_at))}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->comment}}</p>
                </td>
                <td>
                    @if($item->status ==1)
                    <span class="btn btn-sm btn-primary">Wait for approval</span>
                    @elseif($item->status==2)
                    <span class="btn btn-sm btn-success">Approved</span>
                    @else
                    <span class="btn btn-sm btn-danger">Rejected</span>
                    @endif
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
</section>
@endsection

@section('script')


@endsection
