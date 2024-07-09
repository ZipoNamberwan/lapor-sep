@extends('main')

@section('stylesheet')
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="/assets/vendor/@fortawesome/fontawesome-free/css/fontawesome.min.css" />
<link rel="stylesheet" href="/assets/css/container.css">
<link rel="stylesheet" href="/assets/css/text.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

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
                            <!-- <li class="breadcrumb-item active" aria-current="page">Lapor SEP</li> -->
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
                        <h3 class="mb-0">Input Progres Editing Coding</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- <form id="formupdate" autocomplete="off" method="post" action="/jadwal-panen" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf -->
                        <div class="row">
                            <div class="col-md-4 mt-2 mb-2">
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
                        </div>

                        <div class="mt-2" id="samplelist">

                        </div>

                    </div>
                </div>
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
    var kodedata = null
    var selectedCommodities = []
    var samples = []
    var selectedBS = null

    $(document).ready(function() {
        $('#subdistrict').on('change', function() {
            loadBsEdcod()
        });
    });

    function loadBsEdcod() {
        let id = $('#subdistrict').val();

        const resultDiv = document.getElementById('samplelist');
        resultDiv.innerHTML = '<p class="text-warning">Loading<p/>';

        $.ajax({
            type: 'GET',
            url: '/bsedcod/' + id,
            success: function(response) {
                var response = JSON.parse(response);

                const resultDiv = document.getElementById('samplelist');
                resultDiv.innerHTML = '';

                const titleDiv = document.createElement('h4')
                titleDiv.className = 'mt-4 mb-4'
                titleDiv.innerHTML = 'Daftar Sampel Blok Sensus'
                resultDiv.appendChild(titleDiv)

                response.forEach(bs => {

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'bg-white rounded d-flex flex-wrap align-items-center mb-1';

                    itemDiv.innerHTML = `
                        <div class="col-12 col-sm-4 col-md-6 col-lg-3 pl-0"><h5>[${bs.village_code}] ${bs.village_name} ${bs.short_code}</h5></div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4 pl-0 id="inputbs"">
                                <input id="input${bs.id}" onblur="save('${bs.id}')" type="number" class="form-control mb-1" value="${bs.edcoded}">
                                <h5 class="mx-2 invisible" id="indicator${bs.id}">sdsadas</h5>
                        </div>
                    `

                    const input = itemDiv.querySelector(`#input${bs.id}`);
                    input.addEventListener('keydown', (event) => {
                        const keysToPrevent = ['Tab', 'ArrowDown', 'ArrowUp', 'Enter'];
                        if (keysToPrevent.includes(event.key)) {
                            event.preventDefault();
                            const inputs = resultDiv.querySelectorAll('input[type="number"]');
                            const currentIndex = Array.prototype.indexOf.call(inputs, input);

                            let nextInput;
                            if (event.key === 'Tab' || event.key === 'ArrowDown' || event.key === 'Enter') {
                                nextInput = inputs[currentIndex + 1];
                            } else if (event.key === 'ArrowUp') {
                                nextInput = inputs[currentIndex - 1];
                            }

                            if (nextInput) {
                                nextInput.focus();
                                nextInput.select();
                            }
                        }
                    });

                    resultDiv.appendChild(itemDiv);

                });

            },
            error: function(jqXHR, textStatus, errorThrown) {
                const resultDiv = document.getElementById('samplelist');
                resultDiv.innerHTML = `
                        <div class="d-flex">
                            <span class="mr-2">Gagal Menampilkan Sampel</span>
                            <button onclick="loadBsEdcod()" class="btn btn-sm btn-outline-primary">Muat Ulang</button>
                        </div>
                `;
            }
        });
    }

    function save(idbs) {

        var updateData = {
            value: document.getElementById(`input${idbs}`).value,
        };

        var indicator = document.getElementById(`indicator${idbs}`)
        indicator.className = ''
        indicator.innerHTML = 'Loading...'
        var input = document.getElementById(`input${idbs}`)
        input.className = 'form-control mb-1'

        $.ajax({
            url: `/bsedcod/${idbs}`,
            type: 'POST',
            data: updateData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                indicator.className = ''
                indicator.classList.add('text-success');
                indicator.innerHTML = 'Sukses'

                input.className = 'form-control mb-1'
                input.classList.add('is-valid');

            },
            error: function(xhr, status, error) {

                var message = xhr.responseJSON != null ? xhr.responseJSON.error : "Gagal"

                indicator.className = ''
                indicator.classList.add('text-danger');
                indicator.innerHTML = message

                input.className = 'form-control mb-1'
                input.classList.add('is-invalid');
            }
        });
    }
</script>

@endsection