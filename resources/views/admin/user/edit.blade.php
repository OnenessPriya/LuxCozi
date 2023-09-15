
@extends('admin.layouts.app')
@section('page', 'Edit User')

@section('content')
@php
$distributorTeam=\App\Models\Team::select('nsm_id','zsm_id','rsm_id','sm_id','asm_id','ase_id')->where('distributor_id',$data->id)->groupBy('distributor_id')->first();
@endphp
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $data->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
                        <div class="row mb-2">
							<div class="col-12">
                                <p class="small text-muted mb-2">Team</p>
                            </div>
							@if($data->type == 7)
                           <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="vp" name="nsm_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allNSM as $item)
                                                <option value="{{$item->id}}" {{ ($distributorTeam->nsm_id) == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="vp">NSM *</label>
                                    </div>
                                    @error('nsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="vp" name="zsm_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allZSM as $item)
                                                <option value="{{$item->vp}}" {{ (strtoupper($distributorTeam->zsm_id) == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="vp">ZSM *</label>
                                    </div>
                                    @error('zsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="rsm" name="rsm_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allRSM as $item)
                                                <option value="{{$item->rsm}}" {{ (strtoupper($distributorTeam->rsm_id) == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="rsm">RSM *</label>
                                    </div>
                                    @error('rsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="rsm" name="sm_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allSM as $item)
                                                <option value="{{$item->rsm}}" {{ (strtoupper($distributorTeam->sm_id) == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="rsm">SM *</label>
                                    </div>
                                    @error('sm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							
							
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="asm" name="asm_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allASM as $item)
                                                <option value="{{$item->asm}}" {{ (strtoupper($distributorTeam->asm_id) == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="asm">ASM *</label>
                                    </div>
                                    @error('asm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="asm" name="ase_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allASE as $item)
                                                <option value="{{$item->asm}}" {{ (strtoupper($distributorTeam->ase_id) == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="asm">ASE *</label>
                                    </div>
                                    @error('ase_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							@endif
                       
                            <div class="col-12">
                               
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="user_type" name="type">
                                            <option value="" selected disabled>Select</option>
                                            <option value="1" {{ ($data->type == 1) ? 'selected' : '' }}>NSM</option>
                                            <option value="2" {{ ($data->type == 2) ? 'selected' : '' }}>ZSM</option>
                                            <option value="3" {{ ($data->type == 3) ? 'selected' : '' }}>RSM</option>
                                            <option value="4" {{ ($data->type == 4) ? 'selected' : '' }}>SM</option>
                                            <option value="5" {{ ($data->type == 5) ? 'selected' : '' }}>ASM</option>
                                            <option value="6" {{ ($data->type == 6) ? 'selected' : '' }}>ASE</option>
                                            <option value="7" {{ ($data->type == 7) ? 'selected' : '' }}>Distributor</option>
                                        </select>
                                        <label for="mobile">Type *</label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="designation" name="designation" placeholder="name@example.com" value="{{ old('designation') ? old('designation') : $data->designation }}">
                                        <label for="designation">Designation *</label>
                                    </div>
                                    @error('designation') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="name@example.com" value="{{ old('employee_id') ? old('employee_id') : $data->employee_id }}">
                                        <label for="employee_id">Employee ID</label>
                                    </div>
                                    @error('employee_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Name details</p>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="name@example.com" value="{{ old('name') ? old('name') : $data->name }}">
                                        <label for="name">Full name *</label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="fname" name="fname" placeholder="name@example.com" value="{{ old('fname') ? old('fname') : $data->fname }}">
                                        <label for="fname">First name *</label>
                                    </div>
                                    @error('fname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="name@example.com" value="{{ old('lname') ? old('lname') : $data->lname }}">
                                        <label for="lname">Last name *</label>
                                    </div>
                                    @error('lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="mobile" name="mobile" placeholder="name@example.com" value="{{ old('mobile') ? old('mobile') : $data->mobile }}">
                                        <label for="mobile">Mobile number *</label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') ? old('email') : $data->email }}">
                                        <label for="email">Official Email ID</label>
                                    </div>
                                    @error('email') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="password" name="password" placeholder="name@example.com" value="">
                                        <label for="password">Password</label>
                                    </div>
                                    @error('password') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Location details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="state" name="state" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->stateDetails as $index => $item)
                                                <option value="{{ $item->name }}" {{ (strtolower($data->state) == strtolower($item->name)) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="state">State *</label>
                                    </div>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="area" name="area" aria-label="Floating label select example" disabled>
                                            <option value="">Select State first</option>
                                        </select>
                                        <label for="area">City/ Area *</label><span>({{$data->city}})</span>
                                    </div>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
						
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">Update changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

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
@endsection
