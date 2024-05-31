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
                        <h3 class="mb-0">Progres Lapor {{$user->name}}</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        @hasrole('pcl')
                        <a href="{{url('/petugas/create')}}" class="mb-2 btn btn-primary btn-round btn-icon" data-toggle="tooltip" data-original-title="Tambah Jadwal Panen">
                            <span class="btn-inner--icon"><i class="fas fa-plus-circle"></i></span>
                            <span class="btn-inner--text">Input Progres</span>
                        </a>
                        @endrole

                        <div>
                            <p class="mb-0"><small>- Table bisa di scroll ke kanan</small></p>
                            <p class="mb-0"><small>- Kotak pencarian bisa digunakan untuk pencarian by nama/kode wilayah, nama responden</small></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="row-table">
                            <div class="table-responsive">
                                <table class="table" id="datatable-id" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Identitas Sampel</th>
                                            <th>Responden</th>
                                            <th>Status</th>
                                            <th>Pencacah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
        "serverSide": true,
        "processing": true,
        "ajax": {
            "url": '/petugas/data/{{$id_petugas}}',
            "type": 'GET',
        },
        "columns": [{
                "responsivePriority": 8,
                "width": "10%",
                "data": "area_code",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<p class="mb-1"><span class="badge badge-primary">' + data + '</span></p>' +
                            row.subdistrict_name + ', ' + row.village_name + ', ' + row.bs_code;
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '(' + row.no + ') ' + data + '<br/>' +
                            row.type;
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "10%",
                "data": "status_name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<p class="mb-1"><span class="badge badge-' + row.status_color + '">' + data + '</span></p>';
                    }
                    return data;
                }
            }, {
                "responsivePriority": 1,
                "width": "5%",
                "data": "user_name",
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