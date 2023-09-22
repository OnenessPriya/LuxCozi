@extends('admin.layouts.app')

@section('page', 'User list')
@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
           
            <div class="row align-items-center">
                <div class="col-md-3">
                    <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                </div>
                <div class="col-md-9 text-end">
					
                    <form class="row align-items-end justify-content-end" action="" method="GET">
                        <div class="col-auto">
                            <label class="small text-muted">User type</label>
                            <select class="form-select form-select-sm select2" name="user_type" id="type">
                                <option value="" disabled>Select</option>
								<option value="" selected>All</option>
								<option value="1" {{ ($request->user_type == 1) ? 'selected' : '' }}>NSM</option>
                                <option value="2" {{ ($request->user_type == 2) ? 'selected' : '' }}>ZSM</option>
								<option value="3" {{ ($request->user_type == 3) ? 'selected' : '' }}>RSM</option>
								<option value="4" {{ ($request->user_type == 4) ? 'selected' : '' }}>SM</option>
								<option value="5" {{ ($request->user_type == 5) ? 'selected' : '' }}>ASM</option>
								<option value="6" {{ ($request->user_type == 6) ? 'selected' : '' }}>ASE</option>
								<option value="7" {{ ($request->user_type == 7) ? 'selected' : '' }}>Distributor</option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <label for="state" class="text-muted small">State</label>
                            <select name="state" id="state" class="form-control form-control-sm select2">
                                <option value="" disabled>Select</option>
                                <option value="" selected>All</option>
                                @foreach ($state as $state)
                                    <option value="{{$state->name}}" {{ request()->input('state') == $state->name ? 'selected' : '' }}>{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <label class="small text-muted">Area</label>
							<select class="form-control form-control-sm select2" name="area" disabled>
								<option value="{{ $request->area }}">Select state first</option>
							</select>
                        </div>

                        <div class="col-auto">
                            <label for="term" class="text-muted small">Search</label>
                            <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by user name" value="{{app('request')->input('keyword')}}" autocomplete="off">
                        </div>

                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a href="{{ route('admin.users.csv.export', ['user_type'=>$request->user_type,'state'=>$request->state,'area'=>$request->area,'keyword'=>$request->keyword]) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                                <div class="search-filter-right-el">
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-danger btn-sm">
                                        <iconify-icon icon="prime:plus-circle"></iconify-icon> Create
                                    </a>
                                </div>
								
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-sm mt-3">
        <thead>
            <tr>
                <th>SR</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Mobile</th> 
                <th>HQ & State</th>
                <th style="min-width: 200px">Manager</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $index => $item)
                @php
                   
				
                @endphp
				
                <tr>
                    <td>{{$index + $data->firstItem()}}</td>
                    <td>
                        <p class="small text-dark mb-0">
                            {{$item->title}} {{$item->name}}
                        </p>
                        <p class="small text-muted">{{$item->employee_id}}</p>
                        <div class="row__action">
                            <form action="{{ route('admin.users.destroy',$item->id) }}" method="POST">
                                <a href="{{ route('admin.users.edit', $item->id) }}">Edit</a>
                                <a href="{{ route('admin.users.show', $item->id) }}">View</a>
                                <a href="{{ route('admin.users.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                
                                @csrf
                                @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button> 
                            </form>
                        </div>
                    </td>
                    <td>
                        {{ $item->designation ? $item->designation : userTypeName($item->type) }}
                    </td>
                    <td>
                        <p class="small text-dark">{{$item->mobile}}</p>
                        @if($item->alt_number1) <p class="small text-muted">{{$item->alt_number1}}</p> @endif
                        @if($item->alt_number2) <p class="small text-muted">{{$item->alt_number2}}</p> @endif
                        @if($item->alt_number3) <p class="small text-muted">{{$item->alt_number3}}</p> @endif
                    </td>
					
                    <td>
                        {{$item->city}}, {{$item->state}}

                        
                    </td>
                    <td>
                        <p class="small text-muted">{!! findManagerDetails($item->id, $item->type) !!}</p>
                    </td>
                    <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                    {{--<td>
                        {{-- <div class="btn-group">
                            <a href="{{ route('admin.users.show', $item->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="View details">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>

                            {{-- distributor edit --}}
                            {{-- @if ($item->user_type == 5)
                                @php
                                    $retailerListTableData = DB::table('retailer_list_of_occ')->select('id')->where('distributor_name', $item->name)->where('area',$item->city)->first();
                                @endphp

                                @if (!empty($retailerListTableData))
                                    <a href="{{ route('admin.distributor.edit', $retailerListTableData->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Edit details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                @endif --}}
                            {{-- store edit --}}
                            {{-- @elseif ($item->user_type == 6)
                                @php
                                    $retailerListTableData = DB::table('retailer_list_of_occ')->select('id')->where('retailer', $item->name)->first();
                                @endphp

                                @if (!empty($retailerListTableData))
                                    <a href="{{ route('admin.store.edit', $retailerListTableData->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Edit details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                @endif --}}
                            {{-- Other users Edit --}}
                            {{-- @else
                                <a href="{{ route('admin.user.edit', $item->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Edit details">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            @endif

                            @if ($item->status == 1)
                                <a href="{{ route('admin.users.status', $item->id) }}" data-bs-toggle="tooltip" title="This user is ACTIVE. Tap to make INACTIVE" class="btn btn-sm btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                                </a>
                            @else
                                <a href="{{ route('admin.user.status', $item->id) }}" data-bs-toggle="tooltip" title="This user is INACTIVE. Tap to ACTIVATE" class="btn btn-sm btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-x"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                                </a>
                            @endif --}}

                            {{-- reset password button --}}
                            {{-- @php
                                $fullName = $item->name;
                                $explodedName = explode(' ', $fullName);
                            @endphp
                            <a href="javascript: void(0)" onclick="ResetPasswordModal({{$item->id}})" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Reset Password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </a> --}}

                            {{-- Distributor only - Range setup option --}}
                            {{-- @if ($item->type == 5)
                                @php
                                    $rangeCount = DB::table('distributor_ranges')->selectRaw('COUNT(id) AS id')->where('distributor_id', $item->id)->count();
                                @endphp

                                <a href="{{ route('admin.distributor.collection', $item->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="{{$rangeCount}} Range">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </a>
                            @endif
                        </div> 
                         
                    </td>--}}
                </tr>
               
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{$data->appends($_GET)->links()}}
    </div>
</section>


{{-- reset password modal --}}
<div class="modal fade" id="resetPassword" tabindex="-1" aria-labelledby="resetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resetPassBody"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    function ResetPasswordModal(userId) {
        $.ajax({
            url: "{{route('admin.users.password.generate')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                userId: userId
            },
            success: function(resp) {
                // console.log(resp);
                var content = '';
                var url = "{{url('/')}}/admin/users/password/reset";

                if (resp.status == 200) {
                    content += `
                    <form method="post" action="${url}">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="${resp.data}">
                                <label for="password">Generated password *</label>
                            </div>
                        </div>
                        <p class="">Password generated</p>

                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" value="${userId}">
                        <button type="submit" class="btn btn-danger">Change password</button>
                    </form>
                    `;
                } else {
                    content += `
                    <p class="text-danger">${resp.message}</p>
                    <form method="post" action="${url}">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="">
                                <label for="password">Generate password *</label>
                            </div>
                        </div>
                        <p class="">Suggested password: Firstname SHORT-COUNTRY-CODE EMPLOYEE-ID</p>

                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" value="${userId}">
                        <button type="submit" class="btn btn-danger">Change password</button>
                    </form>
                    `;
                }

                $('#resetPassBody').html(content);
                var resetPassword = new bootstrap.Modal(document.getElementById('resetPassword'));
                resetPassword.show();
            }
        })
    }
</script>

@endsection
