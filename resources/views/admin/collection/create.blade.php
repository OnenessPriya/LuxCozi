@extends('admin.layouts.app')

@section('page', 'Collection Create')

@section('content')
<section>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.collections.store') }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Add New Collection</h4>
                        <div class="form-group mb-3">
                            <label class="label-control">Name <span class="text-danger">*</span> </label>
                            <input type="text" name="name" placeholder="" class="form-control" value="{{old('name')}}">
                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Category <span class="text-danger">*</span> </label>
                            <select class="form-control form-control-sm select2" name="cat_id" id="cat_id">
                                <option value="" selected disabled>Select</option>
                                @foreach ($cat as $index => $item)
                                <option value="{{ $item->id }}" {{ ($request->cat_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('cat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Description </label>
                            <textarea name="description" class="form-control">{{old('description')}}</textarea>
                            @error('description') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 card">
                                <div class="card-header p-0 mb-3">Icon <span class="text-danger">*</span></div>
                                <div class="card-body p-0">
                                    <div class="w-100 product__thumb">
                                        <label for="icon"><img id="iconOutput" src="{{ asset('admin/images/placeholder-image.jpg') }}" /></label>
                                    </div>
                                    <input type="file" name="icon_path" id="icon" accept="image/*" onchange="loadIcon(event)" class="d-none">
                                    <script>
                                        let loadIcon = function(event) {
                                            let iconOutput = document.getElementById('iconOutput');
                                            iconOutput.src = URL.createObjectURL(event.target.files[0]);
                                            iconOutput.onload = function() {
                                                URL.revokeObjectURL(iconOutput.src) // free memory
                                            }
                                        };
                                    </script>
                                </div>
                                @error('icon_path') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-md-6 card">
                                <div class="card-header p-0 mb-3">Sketch Icon <span class="text-danger">*</span></div>
                                <div class="card-body p-0">
                                    <div class="w-100 product__thumb">
                                        <label for="sketch_icon"><img id="sketchOutput" src="{{ asset('admin/images/placeholder-image.jpg') }}" /></label>
                                    </div>
                                    <input type="file" name="sketch_icon" id="sketch_icon" accept="image/*" onchange="loadSketch(event)" class="d-none">
                                    <script>
                                        var loadSketch = function(event) {
                                            var sketchOutput = document.getElementById('sketchOutput');
                                            sketchOutput.src = URL.createObjectURL(event.target.files[0]);
                                            sketchOutput.onload = function() {
                                                URL.revokeObjectURL(sketchOutput.src) // free memory
                                            }
                                        };
                                    </script>
                                </div>
                                @error('sketch_icon') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 card">
                                <div class="card-header p-0 mb-3">Thumbnail <span class="text-danger">*</span></div>
                                <div class="card-body p-0">
                                    <div class="w-100 product__thumb">
                                        <label for="thumbnail"><img id="output" src="{{ asset('admin/images/placeholder-image.jpg') }}" /></label>
                                    </div>
                                    <input type="file" name="image_path" id="thumbnail" accept="image/*" onchange="loadFile(event)" class="d-none">
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
                                @error('image_path') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-md-6 card">
                                <div class="card-header p-0 mb-3">Banner <span class="text-danger">*</span></div>
                                <div class="card-body p-0">
                                    <div class="w-100 product__thumb">
                                        <label for="banner"><img id="bannerOutput" src="{{ asset('admin/images/placeholder-image.jpg') }}" /></label>
                                    </div>
                                    <input type="file" name="banner_image" id="banner" accept="image/*" onchange="loadBanner(event)" class="d-none">
                                    <script>
                                        let loadBanner = function(event) {
                                            let output = document.getElementById('bannerOutput');
                                            output.src = URL.createObjectURL(event.target.files[0]);
                                            output.onload = function() {
                                                URL.revokeObjectURL(output.src) // free memory
                                            }
                                        };
                                    </script>
                                </div>
                                @error('banner_image') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Add New Collection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')


@endsection
