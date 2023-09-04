@extends('admin.layouts.app')

@section('page', 'Store Detail')

@section('content')
<section>
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
                        {{-- <div class="form-group mb-3">
                            <label class="label-control">Full Name of Distributor  <span class="text-danger">*</span> </label>
                            <input type="text" name="bussiness_name" placeholder="" class="form-control" value="{{ $data->bussiness_name }}">
                            @error('bussiness_name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Store OCC Number  <span class="text-danger">*</span> </label>
                            <input type="text" name="store_OCC_number" placeholder="" class="form-control" value="{{ $data->store_OCC_number }}">
                            @error('store_OCC_number') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Email <span class="text-danger">*</span> </label>
                            <input type="text" name="email" placeholder="" class="form-control" value="{{ $data->email }}">
                            @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Contact Information <span class="text-danger">*</span> </label>
                            <input type="text" name="contact" placeholder="" class="form-control" value="{{ $data->contact }}">
                            @error('contact') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">WhatsApp Number <span class="text-danger">*</span> </label>
                            <input type="text" name="whatsapp" placeholder="" class="form-control" value="{{ $data->whatsapp }}">
                            @error('whatsapp') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Address <span class="text-danger">*</span> </label>
                            <input type="text" name="address" placeholder="" class="form-control" value="{{ $data->address }}">
                            @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Area <span class="text-danger">*</span> </label>
                            <input type="text" name="area" placeholder="" class="form-control" value="{{ $data->area }}">
                            @error('area') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">State<span class="text-danger">*</span> </label>
                            <input type="text" name="state" placeholder="" class="form-control" value="{{ $data->state }}">
                            @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">City<span class="text-danger">*</span> </label>
                            <input type="text" name="city" placeholder="" class="form-control" value="{{ $data->city }}">
                            @error('city') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Pincode <span class="text-danger">*</span> </label>
                            <input type="text" name="pin" placeholder="" class="form-control" value="{{ $data->pin }}">
                            @error('pin') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div> --}}

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



                         {{-- <div class="form-group mb-3">
                            <label class="label-control">Distributor Name <span class="text-danger">*</span> </label>
                            <select class="form-control" name="cat_id">
                                <option hidden selected>Select ...</option>
                                @foreach ($users as $index => $item)
                                    <option value="{{$item->id}}" {{ ($data->user_id == $item->id) ? 'selected' : '' }}> {{$item->fname.' '.$item->lname}}<br>({{ $item->type }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>  --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Update Store</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>
</section>
@endsection
