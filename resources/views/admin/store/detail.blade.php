@extends('admin.layouts.app')

@section('page', 'Store Detail')

@section('content')

@php
    $store_name = $data->store_name;
    $area = $data->area;
	//$name=$data->user->name;
$displayASEName = '';
        foreach(explode(',',$data->user_id) as $aseKey => $aseVal) 
        {
            //dd($distVal);
            $catDetails = DB::table('users')->where('id', $aseVal)->get();
			
			if(count($catDetails)>0){
				$displayASEName .= $catDetails[0]->name.',';
			}else{
				$displayASEName .= '';
			}
            
        }
    //$moreinformation = \App\Models\RetailerListOfOcc::where('retailer', $store_name)->where('ase',$name)->where('area', $area)->first();
$moreinformation = \App\Models\RetailerListOfOcc::where('store_id', $data->id)->first();
@endphp

<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="badge bg-primary" style="font-size: 26px;">Retailer</div>
                        </div>

                        <div class="col-md-6 text-end">
                            <a href="{{ url()->previous() }}" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                Go back
                            </a>

                            <a href="{{ route('admin.store.edit', $data->id) }}" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12"><p class="text-dark">Store information</p></div>

                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Image</p>
                                <img src="{{ asset($data->image) }}" alt="" class="w-100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Store Name</p>
                                <h5>{{ $data->store_name ? $data->store_name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Firm Name</p>
                                <h5> {{ $data->bussiness_name ? $data->bussiness_name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">GST number</p>
                                <h5> {{ $data->gst_no ? $data->gst_no : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Manager information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Distributor</p>
                                <h5> {{ $moreinformation->distributor_name ? $moreinformation->distributor_name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">VP</p>
                                <h5> {{ $moreinformation->vp ? $moreinformation->vp : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">RSM</p>
                                <h5> {{ $moreinformation->rsm ? $moreinformation->rsm : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">ASM</p>
                                <h5> {{ $moreinformation->asm ? $moreinformation->asm : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">ASE/ Created by</p>
                                <h5> {{ substr($displayASEName, 0, -1) ? substr($displayASEName,0, -1) : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Owner information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Owner First Name</p>
                                <h5> {{ $data->owner_name ? $data->owner_name : 'NA' }}</h5>
                            </div>
                        </div>
						<div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Owner Last Name</p>
                                <h5> {{ $data->owner_lname ? $data->owner_lname : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of Birth</p>
                                <h5> {{ $data->date_of_birth ? $data->date_of_birth : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of Anniversary</p>
                                <h5> {{ $data->date_of_anniversary ? $data->date_of_anniversary : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Store OCC number</p>
                                <h5> {{ $data->store_OCC_number ? $data->store_OCC_number : 'NA' }}</h5>
                            </div>
                        </div> 
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Contact information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Contact</p>
                                <h5> {{ $data->contact ? $data->contact : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Whatsapp</p>
                                <h5> {{ $data->whatsapp ? $data->whatsapp : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Email</p>
                                <h5> {{ $data->email ? $data->email : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Address information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Address</p>
                                <h5> {{ $data->address ? $data->address : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group position-relative">
                                <p class="small text-muted mb-1">Pincode</p>
                                <h5> {{ $data->pin ? $data->pin : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">State</p>
                                <h5> {{ $data->state ? $data->state : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Area</p>
                                <h5> {{ $data->area ? $data->area : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Contact person information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Full Name</p>
                                <h5> {{ $data->contact_person.' '.$data->contact_person_lname ? $data->contact_person.' '.$data->contact_person_lname : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Contact</p>
                                <h5> {{ $data->contact_person_phone ? $data->contact_person_phone : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Whatsapp</p>
                                <h5> {{ $data->contact_person_whatsapp ? $data->contact_person_whatsapp : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of birth</p>
                                <h5> {{ $data->contact_person_date_of_birth ? $data->contact_person_date_of_birth : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of anniversary</p>
                                <h5> {{ $data->contact_person_date_of_anniversary ? $data->contact_person_date_of_anniversary : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
