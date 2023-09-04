@extends('admin.layouts.app')
@section('page', 'Create new Size')

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
                    <form method="POST" action="{{ route('admin.sizes.store') }}" enctype="multipart/form-data">@csrf
                        
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" placeholder="name@example.com" value="{{ old('name') }}">
                                        <label for="title">Name *</label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">Save changes</button>
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
    
</script>
@endsection
