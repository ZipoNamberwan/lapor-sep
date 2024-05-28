@extends('main')

@section('stylesheet')
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
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
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lapor SEP</li>
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
                        <h3 class="mb-0">Lapor Progres</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- <form id="formupdate" autocomplete="off" method="post" action="/jadwal-panen" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf -->
                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <label class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                                <select id="subdistrict" name="subdistrict" class="form-control" data-toggle="select" name="subdistrict" required>
                                    <option value="0" disabled selected> -- Pilih Kecamatan -- </option>
                                    @foreach ($subdistricts as $subdistrict)
                                    <option value="{{ $subdistrict->id }}" {{ old('subdistrict') == $subdistrict->id ? 'selected' : '' }}>
                                        [{{ $subdistrict->short_code}}] {{ $subdistrict->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="form-control-label">Desa <span class="text-danger">*</span></label>
                                <select id="village" name="village" class="form-control" data-toggle="select" name="village"></select>
                            </div>
                            <div id="bs_div" class="col-md-4 mt-2 mb-4">
                                <label class="form-control-label">Blok Sensus <span class="text-danger">*</span></label>
                                <select id="bs" name="bs" class="form-control" data-toggle="select"></select>
                            </div>
                        </div>

                        <div id="samplelist">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h3 id="modaltitle">Modal title</h3>
                    <h4 id="modalsubtitle">Modal title</h4>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-0" style="height: auto;">
                <label class="form-control-label">Status <span class="text-danger">*</span></label>
                <select id="status" name="status" class="form-control" data-toggle="select" name="status" required>
                    <option value="0" disabled selected> -- Pilih Status -- </option>
                    <option value="Belum Dicacah">Belum Dicacah</option>
                    <option value="Sedang Dicacah">Sedang Dicacah</option>
                    <option value="Selesai">Belum Dicacah</option>
                    <option value="Tidak Ditemukan">Belum Dicacah</option>
                </select>

                <label class="mt-2 form-control-label">Komoditas <span class="text-danger">*</span></label>
                <div>
                    <div id="inputContainer">
                        <div class="mb-1 d-flex align-items-center">
                            <input type="text" name="name" class="form-control mr-1" name="komoditas[]">
                            <!-- <button class="btn btn-outline-danger btn-sm" type="button" onclick="removeInput(this)"><i class="fas fa-trash-alt"></i></button> -->
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" type="button" onclick="addInput()">Tambah Komoditas</button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/select2/dist/js/select2.min.js"></script>
<script src="/assets/vendor/datatables2/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#subdistrict').on('change', function() {
            loadVillage(null, null);
        });
        $('#village').on('change', function() {
            loadBs(null, null);
        });
        $('#bs').on('change', function() {
            loadSample(null);
        });
    });

    function loadVillage(subdistrictid = null, selectedvillage = null) {
        let id = $('#subdistrict').val();
        if (subdistrictid != null) {
            id = subdistrictid;
        }
        const resultDiv = document.getElementById('samplelist');
        resultDiv.innerHTML = '';
        $('#village').empty();
        $('#village').append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/desa/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#village').empty();
                $('#village').append(`<option value="0" disabled selected>Pilih Desa</option>`);
                $('#bs').empty();
                $('#bs').append(`<option value="0" disabled selected>Pilih Blok Sensus</option>`);
                response.forEach(element => {
                    if (selectedvillage == String(element.id)) {
                        $('#village').append('<option value=\"' + element.id + '\" selected>' +
                            '[' + element.short_code + '] ' + element.name + '</option>');
                    } else {
                        $('#village').append('<option value=\"' + element.id + '\">' + '[' +
                            element.short_code + '] ' + element.name + '</option>');
                    }
                });
            }
        });
    }

    function loadBs(villageid = null, selectedbs = null) {
        let id = $('#village').val();
        if (villageid != null) {
            id = villageid;
        }
        const resultDiv = document.getElementById('samplelist');
        resultDiv.innerHTML = '';
        $('#bs').empty();
        $('#bs').append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/bs/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#bs').empty();
                $('#bs').append(`<option value="0" disabled selected>Pilih Blok Sensus</option>`);
                response.forEach(element => {
                    if (selectedbs == String(element.id)) {
                        $('#bs').append('<option value=\"' + element.id + '\" selected>' +
                            element.name + '</option>');
                    } else {
                        $('#bs').append('<option value=\"' + element.id + '\">' +
                            element.name + '</option>');
                    }
                });
            }
        });
    }

    function loadSample(bsid = null) {
        let id = $('#bs').val();
        if (bsid != null) {
            id = bsid;
        }
        const resultDiv = document.getElementById('samplelist');
        resultDiv.innerHTML = 'Loading';

        $.ajax({
            type: 'GET',
            url: '/sample/' + id,
            success: function(response) {
                var response = JSON.parse(response);

                const resultDiv = document.getElementById('samplelist');
                resultDiv.innerHTML = '';

                const titleDiv = document.createElement('label')
                titleDiv.className = 'form-control-label'
                titleDiv.innerHTML = 'Pilih Sampel'

                resultDiv.appendChild(titleDiv)

                response.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'border p-2 bg-white rounded d-flex justify-content-between align-items-center mb-1';
                    itemDiv.style = "cursor: pointer;"

                    itemDiv.setAttribute('data-toggle', 'modal');
                    itemDiv.setAttribute('data-target', '#exampleModalCenter');

                    itemDiv.addEventListener('click', function() {
                        updateModal(item)
                    });

                    itemDiv.innerHTML = `
                        <div>
                            <h4 class="mb-1">(${item.no}) ${item.name}</h4>
                            <p class="mb-0"><span class="badge badge-primary">${item.status}</span></p>
                        </div>
                        <div>
                            <a href="" class="btn btn-outline-info btn-sm" role="button" aria-pressed="true" data-toggle="tooltip" data-original-title="Ubah Data">
                                <span class="btn-inner--icon">
                                    <i class="fas fa-edit"></i>
                                </span>
                            </a>
                        </div>
                    `;

                    resultDiv.appendChild(itemDiv);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                const resultDiv = document.getElementById('samplelist');
                resultDiv.innerHTML = `
                        <div class="d-flex">
                            <span class="mr-2">Gagal Menampilkan Sampel</span>
                            <button onclick="loadSample(null)" class="btn btn-sm btn-outline-primary">Muat Ulang</button>
                        </div>
                `;
            }
        });
    }

    function updateModal(sample) {

        document.getElementById('modaltitle').innerHTML = sample.name
        document.getElementById('modalsubtitle').innerHTML = sample.area
    }
</script>

<script>
    function addInput() {
        const container = document.getElementById('inputContainer');
        const newInput = document.createElement('div');
        newInput.className = 'mb-1 d-flex align-items-center'

        newInput.innerHTML = `
                <input type="text" name="name" class="form-control mr-1" name="komoditas[]">
                <button class="btn btn-outline-danger btn-sm" type="button" onclick="removeInput(this)"><i class="fas fa-trash-alt"></i></button>
        `;
        container.appendChild(newInput);
    }

    function removeInput(button) {
        const container = document.getElementById('inputContainer');
        const inputDiv = button.parentNode;
        container.removeChild(inputDiv);
    }
</script>

@if(@old("subdistrict"))
<script>
    loadVillage('{{@old("subdistrict")}}', '{{@old("village")}}')
</script>
@endif

@if(@old("village"))
<script>
    loadBs('{{@old("village")}}', '{{@old("bs")}}')
</script>
@endif

@endsection