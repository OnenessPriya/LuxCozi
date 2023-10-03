
@extends('admin.layouts.app')
@section('page', 'Employee Attendance')

@section('content')
<style>

    .redColor{
        padding: 0 !important;
        text-align: center;
        
    }
</style>
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
                                        <form class="row align-items-end" action="{{ route('admin.users.attendance.report') }}" method="GET">
                                            <div class="search-filter-right">
                                                <div class="search-filter-right-el">
                                                    <label for="zsm" class="text-muted small">ZSM</label>
                                                    <select name="zsm" id="zsm" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($zsmDetails as $item)
                                                            <option value="{{$item->id}}" {{ request()->input('zsm') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">RSM</label>
                                                    <select class="form-control form-control-sm select2" name="rsm" disabled>
                                                        <option value="{{ $request->rsm_id }}">Select ZSM  first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">SM</label>
                                                    <select class="form-control form-control-sm select2" name="sm" disabled>
                                                        <option value="{{ $request->sm_id }}">Select RSM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" name="asm" disabled>
                                                        <option value="{{ $request->asm_id }}">Select SM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" name="ase" disabled>
                                                        <option value="{{ $request->ase_id }}">Select ASM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="month" class="text-muted small">Month</label>
                                                    <input type="month" name="month" id="month" class="form-control form-control-sm" aria-label="Default select example" value="{{$month}}">
                                                </div>
                                                {{-- <div class="search-filter-right-el">
                                                    <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                                </div> --}}
                                            </div>
                                            <div class="search-filter-right search-filter-right-store mt-4">
                                                <div class="search-filter-right-el">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                        Filter
                                                    </button>
                                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                        <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                    </a>
                                                </div>
                                                 <div class="search-filter-right-el">
                                                    <a id="btnExport"  href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
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
                    @php
                        $my_month =  explode("-",$month);
                        $year_val = $my_month[0];
                        $month_val = $my_month[1];
                        $dates_month=dates_month($month_val,$year_val);
                        $month_names = $dates_month['month_names'];
                        $date_values = $dates_month['date_values'];
                        $totaldays=count($dates_month['date_values']);
                        
                    @endphp
                    <div class="table-responsive">
                    <div id="tableWrap">
                    <table class="table" >
                        <thead>
                            <tr>
                                <th>NSM</th>
                                <th>ZSM</th>
                                <th>RSM</th>
                                <th>SM</th>
                                <th>ASM</th> 
                                <th>Employee</th>
                                <th>Employee Id</th>
                                <th>Employee Status</th>
                                <th>Employee Designation</th>
                                <th>Employee Date of Joining</th>
								<th>Employee HQ</th>
                                <th>Employee Contact No</th>
                                {{-- <th>Total Days</th> --}}
                                @foreach ($month_names as $months)
                                <th>{{$months}}</th>
                                @endforeach
                                
                                
                            </tr>
                        </thead>
                    <tbody>
                    
                    @forelse ($data as $index => $item)

                        @php
                          $findTeamDetails= findTeamDetails($item->id, $item->type);
                        @endphp
                    
                        <tr>
                            <td> {{$findTeamDetails[0]['nsm'] ?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['zsm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['rsm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['sm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['asm']?? ''}} </td> 
                            <td> {{$item->name}} </td>
                            <td> {{$item->employee_id}} </td>
                            <td> <span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span> </td>
                            <td> {{$item->designation}} </td>
                            <td> {{$item->date_of_joining}} </td>
							<td> {{$item->headquater?? ''}} </td>
                            <td> {{$item->mobile}} </td>
                            {{-- <td> {{$totaldays}} </td> --}}

                            @foreach ($date_values as $date)
                            @php
                                $dates_attendance=dates_attendance($item->id, $date);
                                
                            @endphp
                            @if($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='A')
                            <td class="redColor" style="background-color: red;
                                color: #fff;
                                padding: 15px;
                                text-align: center;
                                border: 1px solid #fff; vertical-align: middle;">{{$dates_attendance[0][0]['date_wise_attendance'][0]['is_present']}}</td>
                            @elseif($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='P')
                            <td class="redColor" style="background-color: rgb(1, 134, 52); color:#fff;
                            padding: 15px;
                            text-align: center;
                            border: 1px solid #fff; vertical-align: middle;">{{$dates_attendance[0][0]['date_wise_attendance'][0]['is_present']}}</td>
                            @elseif($dates_attendance[0][0]['date_wise_attendance'][0]['is_present']=='W')
                             <td class="redColor"  style="background-color: rgb(241, 225, 0); color:#fff; 
                            padding: 15px;
                            text-align: center;
                            border: 1px solid #fff; vertical-align: middle;">{{$dates_attendance[0][0]['date_wise_attendance'][0]['is_present']}}</td>
                            
                            @else
                            <td class="redColor"  style="background-color: #294fa1da; color:#fff; 
                            padding: 15px;
                            text-align: center;
                            border: 1px solid #fff; vertical-align: middle;">{{$dates_attendance[0][0]['date_wise_attendance'][0]['is_present']}}</td>
                            @endif
                            @endforeach
                            
                        </tr>
                    @empty
                        <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
            <div class="d-flex justify-content-end">
                {{ $data->appends($_GET)->links() }}
            </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')

<script>
    $('select[name="zsm"]').on('change', (event) => {
        var value = $('select[name="zsm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/rsm/list/zsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="rsm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.rsm.id+'">'+value.rsm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>

<script>
    $('select[name="rsm"]').on('change', (event) => {
        var value = $('select[name="rsm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/sm/list/rsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="sm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.sm.id+'">'+value.sm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    $('select[name="sm"]').on('change', (event) => {
        var value = $('select[name="sm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/asm/list/smwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="asm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.asm.id+'">'+value.asm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    $('select[name="asm"]').on('change', (event) => {
        var value = $('select[name="asm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/ase/list/asmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="ase"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.ase.id+'">'+value.ase.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });

    
</script>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script>
    $(function() {
        $('#btnExport').click(function() {
            console.log("hello");
            //$('#tblHead').css("display","block");
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#tableWrap').html())
            location.href = url
            return false
            $('#tblHead').css("display", "none");
        });
    });
</script>
@endsection