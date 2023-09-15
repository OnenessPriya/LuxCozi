@extends('admin.layouts.app')
@section('page', 'User Activity')
@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                <div class="col-md-3">
                    <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                </div>

                <div class="col-md-9 text-end">
                    <form class="row align-items-end justify-content-end" action="" method="GET">
                        <div class="col-auto">
                            <label for="date_from" class="text-muted small">Date from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                        </div>
                        <div class="col-auto">
                            <label for="date_to" class="text-muted small">Date to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_to')) ? request()->input('date_to') : '' }}">
                        </div>
                        <div class="col-auto">
                            <label for="ase" class="small text-muted">Sales Person(ASE/ASM)</label>
                            <select class="form-select form-select-sm select2" id="ase" name="user_id">
                                <option value="" selected disabled>Select</option>
                                @foreach ($user as $item)
                                    <option value="{{$item->id}}" {{ (request()->input('user_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a href="{{ route('admin.users.activity.csv.export') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table" >
        <thead>
            <tr>
                <th>#SR</th>
                <th>User</th>
                <th>Activity</th>
                <th>Datetime</th>
                <th>Comment</th>
                <th>Location</th>
               
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ $index + $data->firstItem() }}</td>
                    <td>
                        @if($item->users ? $item->users->type ==6 : '')<span class="badge bg-secondary">ASE </span>@elseif($item->users ? $item->users->type ==5 : '') <span class="badge bg-primary">ASM </span>@endif

                        <p class="text-dark">{{$item->users ? $item->users->fname : ''}} {{ $item->users ? $item->users->lname : ''}}</p>
                    </td>
                    <td> {{$item->type}} </td>
                    <td> {{ $item->date }} {{ $item->time }} </td>
                    <td> {{ $item->comment }} </td>
                    <td> {{ $item->location }} </td>
                   
                </tr>
            @empty
                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>




   
</section>
@endsection

@section('script')

@endsection
