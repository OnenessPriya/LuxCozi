@php
    $state = DB::select("SELECT ro.state AS state FROM `retailer_list_of_occ` AS ro GROUP BY ro.state ORDER BY ro.state");
    $allASE = \App\User::select('id', 'name')->where('user_type', 4)->orderBy('name')->get();
   // $name=$data->user->name;
 $displayASEName = '';
        foreach(explode(',',$data->user_id) as $aseKey => $aseVal) 
        {
            $catDetails = DB::table('users')->where('id', $aseVal)->get();
            //dd($distVal);
			if(count($catDetails)>0){
				$displayASEName .= $catDetails[0]->name.' ';
			}else{
				$displayASEName .= '';
			}
            
        }
    //$moreStoreDetail = \App\Models\RetailerListOfOcc::where('retailer', $data->store_name)->where('ase',$name)->where('area', $data->area)->first();
$moreStoreDetail = \App\Models\RetailerListOfOcc::where('store_id', $data->id)->first();
//dd($moreStoreDetail);
$displayName = '';
                foreach(explode(',',$moreStoreDetail->distributor_name) as $distKey => $distVal) 
                    {
                                //dd($distVal);
                            $displayName .= $distVal.',';
                           // dd($distVal);
                }
        
  // $allDistributors = \App\Models\RetailerListOfOcc::select('distributor_name')->where('distributor_name', '!=', NULL)->groupBy('distributor_name')->orderBy('distributor_name')->get();
$allDistributors = \App\Models\Distributor::select('bussiness_name')->where('bussiness_name', '!=', NULL)->groupBy('bussiness_name')->orderBy('bussiness_name')->get();
   
@endphp

@extends('admin.layouts.app')

@section('page', 'Edit Store details')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<style>
    input::file-selector-button {
        display: none;
    }
</style>

<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.store.update', $data->id) }}" enctype="multipart/form-data">@csrf
                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Manager details</p>
                            </div>
                            <div class="col-md-4">
								
                                <div class="form-group">
                                     <label for="distributor_name">Distributor *</label>
                                    <div class="form-floating mb-3">
                                        <p class="small text-danger">({{substr($displayName,0,-1)}})</p>
                                        <select class="form-select select2" id="distributor_name"   multiple="multiple" name="distributor_name[]" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($allDistributors as $item)
                                            @php
                                                $cat = explode(",", $moreStoreDetail->distributor_name);
                                                $isSelected = in_array($item->bussiness_name,$cat) ? "selected='selected'" : "";
                                            @endphp
                                                <option value="{{$item->bussiness_name}}" {{is_array($cat) && in_array($item->bussiness_name, $cat) ? 'selected' : '' }}>{{$item->bussiness_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('distributor_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="ase">ASE *</label>
                                    <div class="form-floating mb-3">
										<p class="small text-danger">({{substr($displayASEName,0,-1)}})</p>
                                        <select class="form-select select2" id="ase" name="ase[]" multiple="multiple" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($allASE as $item)
											@php
                                                $user = explode(",", $data->user_id);
                                                $isSelected = in_array($item->id,$user) ? "selected='selected'" : "";
                                            @endphp
                                                <option value="{{$item->id}}" {{is_array($user) && in_array($item->id, $user) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        
                                    </div>
                                    <p class="small text-danger">ASE depends on Distributor</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Store information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="store_name" name="store_name" placeholder="Distributor name" value="{{ old('store_name') ? old('store_name') : $data->store_name }}">
                                        <label for="store_name">Store name *</label>
                                    </div>
                                    @error('store_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="bussiness_name" name="bussiness_name" placeholder="Distributor name" value="{{ old('bussiness_name') ? old('bussiness_name') : $data->bussiness_name }}">
                                        <label for="bussiness_name">Firm name *</label>
                                    </div>
                                    @error('bussiness_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="gst_no" name="gst_no" placeholder="Distributor name" value="{{ old('gst_no') ? old('gst_no') : $data->gst_no }}">
                                        <label for="gst_no">GST number</label>
                                    </div>
                                    @error('gst_no') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex">
                                        @if (!empty($data->image) || file_exists($data->image))
                                            <img src="{{ asset($data->image) }}" alt="" class="img-thumbnail" style="height: 52px;margin-right: 10px;">
                                        @endif
                                        <div class="form-floating mb-3">
                                            <input type="file" class="form-control" id="image" name="image" placeholder="Distributor name" value="">
                                            <label for="image">Image</label>
                                        </div>
                                    </div>
                                    @error('image') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Owner information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="name" value="{{ old('owner_name') ? old('owner_name') : $data->owner_name }}">
                                        <label for="owner_name">Owner first name *</label>
                                    </div>
                                    @error('owner_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="owner_lname" name="owner_lname" placeholder="name" value="{{ old('owner_lname') ? old('owner_lname') : $data->owner_lname }}">
                                        <label for="owner_lname">Owner last name *</label>
                                    </div>
                                    @error('owner_lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="name@example.com" value="{{ old('date_of_birth') ? old('date_of_birth') : $data->date_of_birth }}">
                                        <label for="date_of_birth">Date of Birth</label>
                                    </div>
                                    @error('date_of_birth') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_anniversary" name="date_of_anniversary" placeholder="name@example.com" value="{{ old('date_of_anniversary') ? old('date_of_anniversary') : $data->date_of_anniversary }}">
                                        <label for="date_of_anniversary">Date of Anniversary</label>
                                    </div>
                                    @error('date_of_anniversary') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact" name="contact" placeholder="name@example.com" value="{{ old('contact') ? old('contact') : $data->contact }}">
                                        <label for="contact">Contact *</label>
                                    </div>
                                    @error('contact') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="whatsapp" name="whatsapp" placeholder="name@example.com" value="{{ old('whatsapp') ? old('whatsapp') : $data->whatsapp }}">
                                        <label for="whatsapp">Whatsapp</label>
                                    </div>
                                    @error('whatsapp') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') ? old('email') : $data->email }}">
                                        <label for="email">Email</label>
                                    </div>
                                    @error('email') <p class="small text-danger">{{$message}}</p> @enderror
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
                                        <input type="text" class="form-control" id="address" name="address" placeholder="name@example.com" value="{{ old('address') ? old('address') : $data->address }}">
                                        <label for="address">Address *</label>
                                    </div>
                                    @error('address') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="pin" name="pin" placeholder="name@example.com" value="{{ old('pin') ? old('pin') : $data->pin }}">
                                        <label for="pin">Pincode *</label>
                                    </div>
                                    @error('pin') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="state" name="state" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($state as $index => $item)
                                                <option value="{{ $item->state }}" {{ ($data->state == $item->state) ? 'selected' : '' }}>{{ $item->state }}</option>
                                            @endforeach
                                        </select>
                                        <label for="state">State *</label>
                                    </div>
                                    <p class="small text-danger">State depends on Distributor</p>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="area" name="area" aria-label="Floating label select example" readonly>
                                            <option value="{{$data->area}}" selected>{{$data->area}}</option>
                                        </select>
                                        <label for="area">City/ Area *</label>
                                    </div>
                                    <p class="small text-danger">Area depends on Distributor</p>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact person information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="name@example.com" value="{{ old('contact_person') ? old('contact_person') : $data->contact_person }}">
                                        <label for="contact_person">First name *</label>
                                    </div>
                                    @error('contact_person') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="contact_person_lname" name="contact_person_lname" placeholder="name" value="{{ old('contact_person_lname') ? old('contact_person_lname') : $data->contact_person_lname }}">
                                        <label for="contact_person_lname">Last name *</label>
                                    </div>
                                    @error('contact_person_lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact_person_phone" name="contact_person_phone" placeholder="name@example.com" value="{{ old('contact_person_phone') ? old('contact_person_phone') : $data->contact_person_phone }}">
                                        <label for="contact_person_phone">Contact *</label>
                                    </div>
                                    @error('contact_person_phone') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact_person_whatsapp" name="contact_person_whatsapp" placeholder="name@example.com" value="{{ old('contact_person_whatsapp') ? old('contact_person_whatsapp') : $data->contact_person_whatsapp }}">
                                        <label for="contact_person_whatsapp">Whatsapp</label>
                                    </div>
                                    @error('contact_person_whatsapp') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="contact_person_date_of_birth" name="contact_person_date_of_birth" placeholder="name@example.com" value="{{ old('contact_person_date_of_birth') ? old('contact_person_date_of_birth') : $data->contact_person_date_of_birth }}">
                                        <label for="contact_person_date_of_birth">Date of Birth</label>
                                    </div>
                                    @error('contact_person_date_of_birth') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="contact_person_date_of_anniversary" name="contact_person_date_of_anniversary" placeholder="name@example.com" value="{{ old('contact_person_date_of_anniversary') ? old('contact_person_date_of_anniversary') : $data->contact_person_date_of_anniversary }}">
                                        <label for="contact_person_date_of_anniversary">Date of Anniversary</label>
                                    </div>
                                    @error('contact_person_date_of_anniversary') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

						{{-- <div class="row">
						   <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-dark"> <span class="text-danger"><strong>Please Note, </strong></span> Default password is <strong>onninternational</strong>, user can update password later</p>
                                </div>
                           </div>
						</div> --}}

                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" name="retailer_list_of_occ_id" value="{{ $moreStoreDetail->id }}">

                                <button type="submit" class="btn btn-danger">Update changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- <section>
    <div class="row">
        <div class="col-sm-12">
       
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.store.update', $data->id) }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Edit Store</h4>
                        <div class="form-group mb-3">
                            <label class="label-control">Store Name <span class="text-danger">*</span> </label>
                            <input type="text" name="store_name" placeholder="" class="form-control" value="{{ $data->store_name }}">
                            <input type="hidden" name="id" placeholder="" class="form-control" value="{{ $data->id }}">
                            
                            @error('store_name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div class="card">
                            <div class="card-header p-0 mb-3">Image <span class="text-danger">*</span></div>
                            <div class="card-body p-0">
                                <div class="w-100 product__thumb">
                                    <label for="thumbnail"><img id="output" src="{{ asset($data->image) }}" /></label>
                                </div>
                                <input type="file" name="image" id="thumbnail" accept="image/*" onchange="loadFile(event)" class="d-none">
                                <script>
                                    var loadFile = function(event) {
                                        var output = document.getElementById('output');
                                        output.src = URL.createObjectURL(event.target.files[0]);
                                        output.onload = function() {
                                            URL.revokeObjectURL(output.src) // free memory
                                        }
                                    };
                                </script>
                            </div>
                            @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Update Store</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>
</section> --}}
@endsection

@section('script')
    <script>
        function stateWiseArea(value) {
			$.ajax({
				url: '{{url("/")}}/state-wise-area/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="area"]';
					// var displayCollection = (result.data.state == "all") ? "All Area" : "All "+" area";
					// content += '<option value="" selected>'+displayCollection+'</option>';

					let cat = "{{ app('request')->input('area') }}";

					$.each(result.data.area, (key, value) => {
						if(value.area == '') return;
						if (value.area == cat) {
                            content += '<option value="'+value.area+'" selected>'+value.area+'</option>';
                        } else {
                            content += '<option value="'+value.area+'">'+value.area+'</option>';
                        }
						//content += '<option value="'+value.area+'">'+value.area+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		}

		$('select[name="state"]').on('change', (event) => {
			var value = $('select[name="state"]').val();
			stateWiseArea(value);
		});
    </script>
    	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
@endsection