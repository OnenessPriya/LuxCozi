@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
   <div class="fixed-row">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text"></i> {{ $pageTitle }}</h1>
            <p>{{ $subTitle }}</p>
        </div>
    </div>
    </div>
    @include('admin.partials.flash')
    <div class="alert alert-success" id="success-msg" style="display: none;">
        <span id="success-text"></span>
    </div>
    <div class="alert alert-danger" id="error-msg" style="display: none;">
        <span id="error-text"></span>
    </div>
    <div class="row section-mg row-md-body no-nav mt-0 mt-lg-5">
        <div class="col-md-12">
            <div class="row align-items-center justify-content-between">
                <div class="col">
                    <ul>
                        <li class="active"><a href="{{ route('admin.users.index') }}">All <span class="count">({{$data->count()}})</span></a></li>
                        {{-- @php
                            $activeCount = $inactiveCount = 0;
                            foreach ($data as $catKey => $catVal) {
                                if ($catVal->status == 1) $activeCount++;
                                else $inactiveCount++;
                            }
                        @endphp
                        <li><a href="{{ route('admin.directory.index', ['status' => 'active'])}}">Active <span class="count">({{$activeCount}})</span></a></li>
                        <li><a href="{{ route('admin.directory.index', ['status' => 'inactive'])}}">Inactive <span class="count">({{$inactiveCount}})</span></a></li> --}}
                    </ul>
                </div>
                <div class="col-auto">
                    <form action="{{ route('admin.users.index') }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                        <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                        </div>
                        <div class="col-auto">
                        <button type="submit" class="btn btn-outline-danger btn-sm">Search User</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="tile">
                <div class="tile-body table-responsive">
                    <table class="table table-hover custom-data-table-style table-striped" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th> Name</th>
                                <th> Email</th>
                                <th></th>
                                <th class="text-center"> Phone</th>
                                <th class="align-center"> Status</th>
                                <th class="align-center"> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key =>$user_detail)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user_detail['name'] }}</td>
                                    <td>{{ $user_detail['email'] }}</td>
                                    <td>
                                        @if ($user_detail['is_verified']==1)
                                            <span class="badge badge-verified emailV-tag">Verified</span>
                                        @else
                                            <span class="badge badge-notverified emailV-tag">Not verified</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ (empty($user_detail['mobile']))? "N/A":($user_detail['mobile']) }}</td>
                                    <td class="text-center">
                                    <div class="toggle-button-cover margin-auto">
                                        <div class="button-cover">
                                            <div class="button-togglr b2" id="button-11">
                                                <input id="toggle-block" type="checkbox" name="status" class="checkbox" data-user_id="{{ $user_detail['id'] }}" {{ $user_detail['status'] == 1 ? 'checked' : '' }}>
                                                <div class="knobs"><span>Inactive</span></div>
                                                <div class="layer"></div>
                                            </div>
                                        </div>
                                    </div>
                                    </td>
                                    <td class="align-center">
                                        <div class="btn-group" role="group" aria-label="Second group">
                                            <a href="{{ route('admin.users.details', $user_detail['id']) }}" class="btn btn-sm btn-primary edit-btn"><i class="fa fa-eye"></i></a>
                                            <a href="#" data-id="{{$user_detail['id']}}" class="sa-remove btn btn-sm btn-primary edit-btn del_btn"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $data->appends($_GET)->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css">
@endpush
@push('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.js"></script>
    {{-- <script type="text/javascript">
        $('#sampleTable').DataTable({"ordering": false});
    </script> --}}
    <script type="text/javascript">
    $('.sa-remove').on("click",function(){
        var userid = $(this).data('id');
        swal({
          title: "Are you sure?",
          text: "Your will not be able to recover the record!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(isConfirm){
          if (isConfirm) {
            window.location.href = "users/"+userid+"/delete";
            } else {
              swal("Cancelled", "Record is safe", "error");
            }
        });
    });
    </script>
    <script type="text/javascript">
        $('input[id="toggle-block"]').change(function() {
            var user_id = $(this).data('user_id');
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var check_status = 0;
          if($(this).is(":checked")){
              check_status = 1;
          }else{
            check_status = 0;
          }
          $.ajax({
                type:'POST',
                dataType:'JSON',
                url:"{{route('admin.users.updateStatus')}}",
                data:{ _token: CSRF_TOKEN, id:user_id, check_status:check_status},
                success:function(response)
                {
                  swal("Success!", response.message, "success");
                },
                error: function(response)
                {

                  swal("Error!", response.message, "error");
                }
              });
        });
    </script>
@endpush
