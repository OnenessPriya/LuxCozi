@extends('admin.layouts.app')

@section('page', 'Edit State')

@section('content')


<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.states.update', $data->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
                        
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" value="{{ old('name') ? old('name') : $data->name }}">
                                        <label for="title">Title *</label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code') ? old('code') : $data->code }}">
                                        <label for="title">Code *</label>
                                    </div>
                                    @error('code') <p class="small text-danger">{{$message}}</p> @enderror
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