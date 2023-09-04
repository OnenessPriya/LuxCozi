@extends('admin.layouts.app')
@section('page', 'Store')
@section('content')
    <section>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="search__filter">
                            <div class="row align-items-center justify-content-between">
                                <div class="col">
                                    <ul>
                                        <li class="active"><a href="{{ route('admin.store.index') }}">All <span class="count">({{count($data)}})</span></a></li>

                                        @php
                                        $activeCount = $inactiveCount = 0;
                                        foreach ($data as $catKey => $catVal) {
                                        if ($catVal->status == 1) $activeCount++;
                                        else $inactiveCount++;
                                        }
                                        @endphp
                                        <li><a href="{{ route('admin.store.index', ['status' => 'active'])}}">Active <span class="count">({{$activeCount}})</span></a></li>
                                        <li><a href="{{ route('admin.store.index', ['status' => 'inactive'])}}">Inactive <span class="count">({{$inactiveCount}})</span></a></li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <form action="{{ route('admin.store.index') }}" method="GET">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <input type="search" name="term" class="form-control" placeholder="Search here.." id="term" value="{{app('request')->input('term')}}" autocomplete="off">
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Search Store</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <a type="button" href="{{ route('admin.store.create') }}" class="btn btn-outline-danger btn-sm float-right">Add</a>
                                    <a href="javascript:void(0)" onclick="htmlToCSV()" class="btn btn-outline-danger btn-sm float-right"><i class="fa fa-cloud-download"></i> CSV Export</a>
                                </div>
                            </div>
                        </div>
                        <table class="table" id="example5">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Store Name</th>
                                    <th>Distributor Name</th>
                                    <th>VP</th>
                                    <th>RSM</th>
                                    <th>ASM</th>
                                    <th>ASE</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th> Click </th>
                                    <th> Invoice </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
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
                                        <td>{{ $index+1 }}</td>
                                        <td>
                                            {{ $item->store_name }}
                                            <div class="row__action">
                                                <a href="{{ route('admin.store.view', $item->id) }}">Edit</a>
                                                <a href="{{ route('admin.store.view', $item->id) }}">View</a>
                                                <a
                                                    href="{{ route('admin.store.status', $item->id) }}">{{ $item->status == 1 ? 'Active' : 'Inactive' }}</a>
                                                <a href="{{ route('admin.store.delete', $item->id) }}"
                                                    class="text-danger">Delete</a>
                                            </div>
                                        </td>
                                        <td>{{ $item->bussiness_name }}</td>
                                        <td>{{ $item->vp }}</td>
                                        <td>{{ $item->rsm }}</td>
                                        <td>{{ $item->asm }}</td>
                                        <td>{{ $item->ase }}</td>
                                        <td>{{ $item->email }}<br>{{ $item->contact }}</td>
                                        <td>{{ $item->address }}<br>{{ $item->area }}<br>{{ $item->city }}<br>{{ $item->state }}
                                        </td>
                                        <td><span
                                                class="badge bg-{{ $item->status == 1 ? 'success' : 'danger' }}">{{ $item->status == 1 ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.retailer.image.index', $item->id) }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-images" viewBox="0 0 16 16">
                                                <path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                                                <path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2zM14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1zM2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1h-10z"/>
                                              </svg></a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.retailer.invoice.index', $item->id) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-receipt" viewBox="0 0 16 16">
                                                    <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z"/>
                                                    <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                                                  </svg></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="small text-muted">No data found</td>
                                    </tr>
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

@section('script')
<script>
    function htmlToCSV() {
        var data = [];
        var rows = document.querySelectorAll("#example5 tbody tr");
        @php
            if (!request()->input('page')) {
                $page = '1';
            } else {
                $page = request()->input('page');
            }
        @endphp

        var page = "{{ $page }}";

        data.push("SRNO.,StoreName,DistributorName,VP,RSM,ASM,ASE,Email,Contact,Address,Area,City,State,Status");

        for (var i = 0; i < rows.length; i++) {
            var row = [],
                cols = rows[i].querySelectorAll("td");

            for (var j = 0; j < cols.length - 1; j++) {
                var text = cols[j].innerText.split(' ');
                var new_text = text.join('-');
                if (j == 2)
                    var comtext = new_text.replace(' ', "-");
                else
                    var comtext = new_text.replace(/\n/g, ";");
                row.push(comtext);
            }
            data.push(row.join(","));
        }

        downloadCSVFile(data.join("\n"), 'Store.csv');
    }

    function downloadCSVFile(csv, filename) {
        var csv_file, download_link;

        csv_file = new Blob([csv], {
            type: "text/csv"
        });

        download_link = document.createElement("a");

        download_link.download = filename;

        download_link.href = window.URL.createObjectURL(csv_file);

        download_link.style.display = "none";

        document.body.appendChild(download_link);

        download_link.click();
    }
</script>



    @endsection
