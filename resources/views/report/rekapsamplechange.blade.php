@extends('main')

@section('stylesheet')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="/assets/vendor/datatables2/datatables.min.css" />
<link rel="stylesheet" href="/assets/vendor/@fortawesome/fontawesome-free/css/fontawesome.min.css" />
<link rel="stylesheet" href="/assets/css/container.css">
<link rel="stylesheet" href="/assets/css/text.css">
@endsection

@section('container')

<div class="header bg-success pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <nav aria-label="breadcrumb" class="d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- Custom form validation -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <h3 class="mb-0">Rekap Pergantian Sampel</h3>
                    </div>
                    <!-- Card body -->
                    <div class="row my-2">
                        <div class="col-12" id="row-table">
                            <div class="table-responsive">
                                <table class="table" id="datatable-id" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Sampel</th>
                                            <th>Wilayah</th>
                                            <th>Sampel Pengganti</th>
                                            <th>Wilayah Sample Pengganti</th>
                                            <th>Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($samples as $sample)
                                        <tr>
                                            <td>{{$sample->name}}</td>
                                            <td>{{$sample->replacement->name}}</td>
                                            <td>{{$sample->user != null ? $sample->user->name : ''}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/select2/dist/js/select2.min.js"></script>
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/datatables2/datatables.min.js"></script>
<script src="/assets/vendor/momentjs/moment-with-locales.js"></script>

<script>
    var table = $('#datatable-id').DataTable({
        "order": [],
        "data": @json($samples),
        "columns": [{
                "responsivePriority": 8,
                "width": "10%",
                "data": "name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<i class="fas fa-arrow-alt-circle-down text-danger"></i> ' + data + '<br/>';
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "area_name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return data + '</br>' + row.bs.long_code
                    }
                    return data + ' ' + row.bs.long_code;
                }
            },
            {
                "responsivePriority": 1,
                "width": "10%",
                "data": "replacement.name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<i class="fas fa-arrow-alt-circle-up text-success"></i> ' + data + '<br/>';
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "replacement.area_name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return data + '</br>' + row.replacement.bs.long_code
                    }
                    return data + ' ' + row.replacement.bs.long_code;
                }
            },
            {
                "responsivePriority": 1,
                "width": "10%",
                "data": "user.name",
            },
        ],
        "language": {
            'paginate': {
                'previous': '<i class="fas fa-angle-left"></i>',
                'next': '<i class="fas fa-angle-right"></i>'
            }
        }
    });
</script>
@endsection