@extends('admin.layouts.app')

@section('page', 'Category detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{ $data->name }}</h3>
                            <p class="small">{{ $data->description }}</p>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-muted">Icon</p>
                            <img src="{{ asset($data->icon_path) }}" alt="" style="height: 50px">
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted">Sketch</p>
                            <img src="{{ asset($data->sketch_icon) }}" alt="" style="height: 50px">
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted">Thumbnail</p>
                            <img src="{{ asset($data->image_path) }}" alt="" style="height: 50px">
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted">Banner</p>
                            <img src="{{ asset($data->banner_image) }}" alt="" style="height: 50px">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-muted">Products</h3>
                            <p>{{$data->ProductDetails->count()}} products total</p>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                    <th>Name</th>
                                    <th>Style No.</th>
                                    <th>Collection</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data->ProductDetails as $index => $item)
                                    @php
                                        if (!empty($_GET['status'])) {
                                            if ($_GET['status'] == 'active') {
                                                if ($item->status == 0) continue;
                                            } else {
                                                if ($item->status == 1) continue;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center column-thumb">
                                            <img src="{{asset('img/product-box.png')}}" />
                                        </td>
                                        <td>
                                            {{$item->name}}
                                            <div class="row__action">
                                                <a href="{{ route('admin.products.edit', $item->id) }}">Edit</a>
                                                <a href="{{ route('admin.products.show', $item->id) }}">View</a>
                                            </div>
                                        </td>
                                        <td>{{$item->style_no}}</td>
                                        <td>{{$item->collection ? $item->collection->name : ''}}</td>
                                        <td>
                                            <small> <del>{{$item->price}}</del> </small> Rs. {{$item->offer_price}}
                                        </td>
                                        <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
