@extends('admin.layouts.app')

@section('page', 'Secondary Order report')

@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                         
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="date_to" class="text-muted small">Date to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="ase" class="small text-muted">SALES PERSON(ASE/ASM)</label>
                            <select class="form-control select2" id="ase" name="user_id">
                                <option value="" selected disabled>Select</option>
                                @foreach ($allASEs as $item)
                                    <option value="{{$item->id}}" {{ (request()->input('user_id') == $item->name) ? 'selected' : '' }}>{{$item->name}}</option>
                                @endforeach
                            </select>
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <label for="state" class="text-muted small">State</label>
                            <select name="state_id" id="state" class="form-control select2">
                                <option value="" disabled>Select</option>
                                <option value="" selected>All</option>
                                @foreach ($state as $row)
                                    <option value="{{$row->id}}" {{ request()->input('state_id') == $row->id ? 'selected' : '' }}>{{$row->name}}</option>
                                @endforeach
                            </select>
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <label class="small text-muted">Area</label>
							<select class="form-control select2" name="area_id" disabled>
								<option value="{{ $request->area_id }}">Select state first</option>
							</select>
                                              </div>
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-3">
                                              
                                             
                                              <div class="search-filter-right-el">
                                                  <label for="store_id" class="small text-muted">Store</label>
                            <select class="form-select select2" id="store_id" name="store_id">
                                <option value="" selected disabled>Select</option>
                                @foreach ($allStores as $item)
                                    <option value="{{$item->id}}" {{ (request()->input('store_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                @endforeach
                            </select>
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <label for="product" class="small text-muted">Product</label>
                            <select name="product_id" class="form-control select2" id="product">
                                <option value="" disabled>Select</option>
                                <option value="" {{request()->input('product') == 'all' ? 'selected' : ''}}>All</option>
                                @foreach ($data->products as $product)
                                    <option value="{{$product->id}}" {{request()->input('product_id') == $product->id ? 'selected' : ''}}>{{$product->name}}</option>
                                @endforeach
                            </select>
                                              </div>
                                               <div class="search-filter-right-el">
                                                  <input type="search" name="orderNo" id="orderNo" class="form-control" placeholder="Search here.." value="{{app('request')->input('orderNo')}}" autocomplete="off">
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <button type="submit" data-bs-toggle="tooltip" title="" class="btn btn-outline-danger btn-sm store-filter-btn" data-bs-original-title="Search"> <i class="fi fi-br-search"></i> </button>
                                                  <!--<button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">-->
                                                  <!--    Filter-->
                                                  <!--</button>-->
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                      <a href="{{route('admin.orders.product.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'ase'=>$request->user_id,'state'=>$request->state_id,'area'=>$request->area_id,'store_id'=>$request->store_id,'product'=>$request->product_id,'orderNo'=>$request->orderNo])}}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                          
                                          <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                      </a>
                                  </div>
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                  
                  <div class="table-responsive">
                      <table class="table table-sm admin-table">
                <thead>
                <tr>
                    <th>#SR</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Order No</th>
                    <th>Store</th>
                    <th>State</th>
                    <th>Area</th>
                    <th>Sales Person(ASE/ASM)</th>
                    <th>Date</th>
					
                </tr>
                </thead>
                <tbody>
                    @php
                        $all_orders_total_amount = 0;
                    @endphp

                    @forelse ($data->all_orders as $index => $item)
                        @php
                            $all_orders_total_amount += ($item->qty);
                        @endphp
                        <tr id="row_{{$item->id}}">
                            <td>
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <p class="text-dark mb-1">{{$item->name}}</p>
                                <p class="small text-muted mb-1">{{$item->color->name}}</p>
                                <p class="small text-muted mb-1">{{$item->size->name}}</p>

                            </td>
                            
                            <td>
                                <p class="text-dark mb-1">{{$item->qty}}</p>
                            </td>
                            <td>
                                <p class="small text-dark mb-1">#{{$item->order_no}}</p>
                            </td>
                            <td>
                                <p class="small text-dark mb-1">{{$item->orders->stores->name ?? ''}}</p>
                            </td>
							 
                            <td>
                                <p class="small text-dark mb-1">{{$item->orders->stores->states->name ?? ''}}</p>
                            </td>
                            <td>
                                <p class="small text-dark mb-1">{{$item->orders->stores->areas->name ?? ''}}</p>
                            </td>
							
                            <td>
							@if(!empty($item->orders->users))
                                <p class="small text-dark mb-1">{{$item->orders->users->fname.' '.$item->orders->users->lname ?? ''}}</p>
							@else
								 <p class="small text-danger mb-1">Self Order</p>
							@endif
                            </td>

                           
                            <td>
                                <div class="order-time">
                                    <p class="small text-muted mb-0">
                                        <span class="text-dark font-weight-bold mb-2">
                                            {{date('j M Y g:i A', strtotime($item->orders->created_at))}}
                                        </span>
                                    </p>
                                </div>
                            </td>
							
                        </tr>
                    @empty
                        <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                    @endforelse
                    <tr>
                        <td></td>
                        
                        <td>
                            <p class="small text-dark mb-1 fw-bold">TOTAL</p>
                        </td>
                        <td>
                            <p class="small text-dark mb-1 fw-bold">{{ number_format($all_orders_total_amount) }}</p>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
                  </div>

                  	<div class="d-flex justify-content-end">
               {{ $data->all_orders->appends($_GET)->links() }}
            </div>
              </div>
          </div>
      </div>
  </div>
</section>


@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
   <script>
    $('select[name="state_id"]').on('change', (event) => {
        var value = $('select[name="state_id"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area_id"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area_id+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection