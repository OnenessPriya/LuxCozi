

@extends('admin.layouts.app')

@section('page', 'User detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="badge bg-primary" style="font-size: 26px;">{{$data->user->designation}}</div>

                            @if ($data->user->status == 1)
                                <a href="{{ route('admin.users.status', $data->user->id) }}" data-bs-toggle="tooltip" title="This user is ACTIVE. Tap to make INACTIVE" class="badge bg-success">Active</a>
                            @else
                                <a href="{{ route('admin.users.status', $data->user->id) }}" data-bs-toggle="tooltip" title="This user is INACTIVE. Tap to ACTIVATE" class="badge bg-danger">Inactive</a>
                            @endif
                        </div>

                        <div class="col-md-6 text-end">
                            <a href="{{ url()->previous() }}" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                Go back
                            </a>

                            <a href="{{ route('admin.users.edit', $data->user->id) }}" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Primary information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Name</p>
                            <h5>{{$data->user->name}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Mobile number</p>
                            <h5>{{$data->user->mobile}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Email address</p>
                            <h5>{{$data->user->email}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Code</p>
                            <h5>{{$data->user->employee_id}}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Location information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">State</p>
                            <h5>{{$data->user->state}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Area</p>
                            <h5>{{$data->user->city}}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Optional Contact information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Alt. Mobile number 1</p>
                            <h5>{{$data->user->alt_number1 ? $data->user->alt_number1 : 'NA'}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Alt. Mobile number 2</p>
                            <h5>{{$data->user->alt_number2 ? $data->user->alt_number2 : 'NA'}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Alt. Mobile number 3</p>
                            <h5>{{$data->user->alt_number3 ? $data->user->alt_number3 : 'NA'}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Personal email</p>
                            <h5>{{$data->user->personal_mail ? $data->user->personal_mail : 'NA'}}</h5>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
    
@endsection