@extends('admin.layouts.app')
@section('page', 'Store')
@section('content')
<section class="store-sec ">
    <div class="row">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="card search-card">
                <div class="card-body">
                    <div class="search__filter mb-5">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-12 mb-3">
                                <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="search-filter-right">
                                    <div class="search-filter-right-el">
                                        <form action="{{ route('admin.stores.index') }}" method="GET">
                                            <div class="search-filter-right">
                                                <div class="search-filter-right-el">
                                                    <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from')}}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to')}}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="distributor" class="small text-muted">Distributor</label>
                                                    <select class="form-control form-control-sm select2" id="distributor" name="distributor_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allDistributors as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('distributor_id') == $item->name) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="ase" class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" id="ase" name="ase_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allASEs as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('ase_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="ase" class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" id="asm" name="asm_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allASMs as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('asm_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="state" class="text-muted small">State</label>
                                                    <select name="state_id" id="state" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($state as $state)
                                                            <option value="{{$state->id}}" {{ request()->input('state_id') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">Area</label>
                                                    <select class="form-control form-control-sm select2" name="area_id" disabled>
                                                        <option value="{{ $request->area_id }}">Select state first</option>
                                                    </select>
                                                </div>
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <label class="small text-muted">Area</label>-->
                                                <!--    <select class="form-control form-control-sm select2" name="area_id" disabled>-->
                                                <!--        <option value="{{ $request->area_id }}">Select state first</option>-->
                                                <!--    </select>-->
                                                <!--</div>-->
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact" value="{{app('request')->input('keyword')}}" autocomplete="off">-->
                                                <!--</div>-->
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <button type="submit" class="btn btn-outline-danger btn-sm">-->
                                                <!--        <iconify-icon icon="carbon:filter"></iconify-icon> Filter-->
                                                <!--    </button>-->
                                                <!--    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter" data-bs-toggle="tooltip" title="Clear Filter">-->
                                                <!--        <iconify-icon icon="basil:cross-outline"></iconify-icon>-->
                                                <!--    </a>-->
                                                <!--</div>-->
                                                
                                            </div>
                                            <div class="search-filter-right search-filter-right-store mt-4">
                                                
                                                <div class="search-filter-right-el">
                                                    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact" value="{{app('request')->input('keyword')}}" autocomplete="off">
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
                                        <a href="{{ route('admin.stores.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'ase'=>$request->ase_id,'asm'=>$request->asm_id,'distributor'=>$request->distributor_id,'state'=>$request->state_id,'area'=>$request->area_id,'keyword'=>$request->keyword]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                            
                                            <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                        </a>
                                    </div>
                                            </div>
                                             
                                        </form>
                                    </div>
                                    
                                    {{--<div class="search-filter-right-el">
                                        <a href="{{ route('admin.stores.inactive') }}" class="btn btn-outline-danger btn-sm">
                                            <iconify-icon icon="prime:plus-circle"></iconify-icon> Inactive Store List
                                        </a>
                                    </div>--}}
                                    
                                   
                                </div>
                            </div>
                            
							
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>#SR</th>
                                <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                <th>Uniquecode</th> 
                                <th>Store</th>
                                <th>Created by</th>
                                <th>Contact</th>
                                <th>Distributor</th>
                                <th>Address</th>
                                <th>Date</th>
								<th>ZSM Approval</th>
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
                              $distName = \App\Models\Team::select('users.name')->join('users', 'users.id', 'teams.distributor_id')->where('store_id', $item->id)->first();
                            $storename = \App\Models\Team::where('store_id', $item->id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
							//dd($storename->zsm);
                            @endphp
                            <tr>
                                <td>{{ ($data->firstItem()) + $index }}</td>
                                <td class="text-center column-thumb">
                                   @if(!empty($item->image))
                                    <img src="{{ asset($item->image) }}" style="max-width: 80px;max-height: 80px;">
                                    
                                   @endif
                                </td>
                                <td>{{ $item->unique_code }}</td>
                                <td>
                                    {{ ucwords($item->name) }}
                                    <p class="small text-muted">- {{ ucwords($item->business_name) }}</p>
                                    <div class="row__action">
                                        <form action="{{ route('admin.stores.destroy',$item->id) }}" method="POST">
                                            <a href="{{ route('admin.stores.edit', $item->id) }}">Edit</a>
                                            <a href="{{ route('admin.stores.show', $item->id) }}">View</a>
											 @if($item->zsm_approval == 1)
                                            <a href="{{ route('admin.stores.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
											
							                @elseif($storename->zsm->fname =='VACCANT')
											<a href="{{ route('admin.stores.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
											@endif
                                            @csrf
                                            @method('DELETE')
                                          {{-- <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link" style="">Delete</button>--}}
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    {{ $item ? $item->users->name : '' }}
                                    <p class="small text-muted">@if($item->users->type==5)<span>(ASM)</span>@elseif($item->users->type==6)<span>(ASE)</span>@endif</p>
                                </td>
                                <td>{{ $item->email }}<br>{{ $item->contact }}</td>
                                <td>
                                    {{ $distName ? $distName->name : '' }}
                                </td> 
                                <td>{{ ucwords($item->address) }}<br>{{ $item->areas->name ??'' }}<br>{{ $item->areas->name ??''}}<br>{{ $item->states->name ??''}}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                                </td>
								<td>
								    @if($item->zsm_approval== 1) <span class="badge bg-success">Approved by ZSM </span>
                                    
								    @else
								       <span class="badge bg-secondary">Waiting for approval </span>
                                    @endif
								    
                                </td>
                                <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="small text-muted">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
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
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
    $('select[name="state_id"]').on('change', (event) => {
        var value = $('select[name="state_id"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area_id"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area_id+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection
