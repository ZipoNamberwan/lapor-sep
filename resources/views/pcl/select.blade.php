@extends('main')

@section('stylesheet')
<link rel="stylesheet" href="/assets/vendor/select2update/select2.min.css">
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

<div class="modal fade" id="sampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 id="modaltitle">Modal title</h2>
                    <h3 id="modalsubtitle">Modal title</h3>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="sample_id" />
            <div class="modal-body pt-0" style="height: auto;">
                <label class="form-control-label">Status <span class="text-danger">*</span></label>
                <select id="status" name="status" class="form-control" data-toggle="select" required>
                    <option value="0" disabled selected> -- Pilih Status -- </option>
                    @foreach($statuses as $status)
                    <option value="{{$status->id}}">({{$status->code}}) {{$status->name}}</option>
                    @endforeach
                </select>
                <div id="status_error" style="display: none;" class="text-valid mt-2">
                    Belum diisi
                </div>

                <div class="mt-3">
                    <label class="form-control-label">Komoditas <span class="text-danger">*</span></label>
                </div>
                <div>
                    <div id="inputContainer">
                        <div id="tags" class="mb-1 pb-1 d-flex align-items-center flex-wrap"></div>
                        <div class="mb-1 d-flex align-items-center">
                            <select id="commodityselect" class="commodityselect" name="commodities[]">
                                <option value="0" disabled selected> -- Tambah Komoditas -- </option>
                            </select>
                        </div>
                    </div>
                    <div id="commodity_error" style="display: none;" class="text-valid mt-2">
                        Belum diisi
                    </div>
                </div>
                <div>
                    <p id="loading-save" style="visibility: hidden;" class="text-warning mt-3">Loading...</p>
                </div>
            </div>

            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button onclick="onSave()" type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/select2update/select2.min.js"></script>
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/datatables2/datatables.min.js"></script>

<script>
    var statuses = []

    @foreach($statuses as $status)
    statuses.push({
        id: '{{$status->id}}',
        name: '{{$status->name}}',
        color: '{{$status->color}}',
        code: '{{$status->code}}'
    })
    @endforeach
</script>

<script>
    var kodedata = null
    var selectedCommodities = []

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

        $.ajax({
            url: '/data/kode.json',
            dataType: 'json',
            success: function(rsp) {
                kodedata = rsp
                initializeSelect(kodedata)
            }
        });
    });

    $('.commodityselect').on('change', function(e) {

        var selectedValue = $(this).val();
        var selectedText = $(this).find(':selected').text();

        var isExist = false
        selectedCommodities.forEach((comm) => {
            if (comm.id == selectedValue) {
                isExist = true
            }
        })
        if (!isExist) {
            selectedCommodities.push({
                id: selectedValue,
                text: selectedText
            })
        }

        createTags()
    });

    function createTags() {

        const tagsContainer = document.getElementById('tags');
        tagsContainer.innerHTML = ''

        selectedCommodities.forEach(tagData => {
            const tagElement = document.createElement('div');
            tagElement.className = 'btn btn-primary btn-sm mt-1';
            tagElement.innerHTML = `
                <span class="btn-inner--text">${tagData.text}</span>
                <span class="ml-1 btn-inner--icon"><i class="fas fa-window-close"></i></span>
            `
            tagsContainer.appendChild(tagElement);

            tagElement.addEventListener('click', function() {
                selectedCommodities = removeCommodities(tagData.id)
                tagElement.remove()
            });
        });
    }

    function validate() {
        var commodity_valid = true
        if (selectedCommodities.length == 0) {
            if (document.getElementById('status').value == 9) {
                commodity_valid = false
                document.getElementById('commodity_error').style.display = 'block'
            }
        } else {
            document.getElementById('commodity_error').style.display = 'none'
        }

        var status_valid = true
        if (document.getElementById('status').value == 0 || document.getElementById('status').value == null) {
            status_valid = false
            document.getElementById('status_error').style.display = 'block'
        } else {
            document.getElementById('status_error').style.display = 'none'
        }

        return commodity_valid && status_valid
    }

    function onSave() {
        document.getElementById('commodity_error').style.display = 'none'
        document.getElementById('status_error').style.display = 'none'

        if (validate()) {
            document.getElementById('loading-save').style.visibility = 'visible'

            id = document.getElementById('sample_id').value
            var updateData = {
                status: document.getElementById('status').value,
                commodities: selectedCommodities
            };

            $.ajax({
                url: `/petugas/edit/${id}`,
                type: 'PATCH',
                data: updateData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    loadSample(null)
                    $('#sampleModal').modal('hide');
                    document.getElementById('loading-save').style.visibility = 'hidden'
                },
                error: function(xhr, status, error) {
                    document.getElementById('loading-save').style.visibility = 'hidden'
                }
            });
        }
    }

    function removeCommodities(id) {
        return selectedCommodities.filter(item => item.id !== id);
    }

    function initializeSelect(rsp) {
        $('.commodityselect').select2({
            data: Object.keys(rsp).map(key => ({
                id: key,
                text: rsp[key]
            })),
            minimumInputLength: 3,
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                if (typeof data.text === 'undefined' || typeof data.id === 'undefined') {
                    return null;
                }

                const searchTerm = params.term.toLowerCase();
                if (data.text.toLowerCase().indexOf(searchTerm) > -1 || data.id.toLowerCase().indexOf(searchTerm) > -1) {
                    return data;
                }

                return null;
            },
            templateResult: function(data) {
                if (data.id == 0) {
                    return data.text
                }
                return $('<span>').text(`(${data.id}) ${data.text}`);
            },
            templateSelection: function(data) {
                return ' -- Tambah Komoditas -- '
                // if (data.id == 0) {
                //     return data.text
                // }

                // return `(${data.id}) ${data.text}`;
            },
            language: {
                inputTooShort: function(args) {
                    const remainingChars = args.minimum - args.input.length;
                    return `Ketik minimal ${remainingChars} huruf untuk melakukan pencarian`;
                }
            }
        });
    }

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
        resultDiv.innerHTML = '<p class="text-warning">Loading<p/>';

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
                    itemDiv.className = 'border p-2 bg-white rounded d-flex flex-wrap justify-content-between align-items-center mb-1';
                    itemDiv.style = "cursor: pointer;"

                    itemDiv.setAttribute('data-toggle', 'modal');
                    itemDiv.setAttribute('data-target', '#sampleModal');

                    itemDiv.addEventListener('click', function() {
                        updateModal(item)
                    });

                    var changeSample = item.status_id != 9 && item.status_id != 1 && item.status_id != 2 ?
                        `
                            <button onclick="showChangeSampleModal(${JSON.stringify(item)})" class="btn btn-outline-success btn-sm">
                                <span class="btn-inner--icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                            </button>
                        ` :
                        ''

                    itemDiv.innerHTML = `
                        <div class="mb-1">
                            <h4 class="mb-1">(${item.no}) ${item.name}</h4>
                            <p class="mb-0"><span class="badge badge-${item.color}">${item.status_name}</span></p>
                        </div>
                        <div class="d-flex mb-1">
                            ${changeSample}
                            <button class="btn btn-outline-info btn-sm">
                                <span class="btn-inner--icon">
                                    <i class="fas fa-edit"></i>
                                </span>
                            </button>
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

    function showChangeSampleModal(item) {
        event.stopPropagation()
        console.log(item)
    }

    function updateModal(sample) {

        document.getElementById('modaltitle').innerHTML = sample.name
        document.getElementById('modalsubtitle').innerHTML = sample.area
        document.getElementById('sample_id').value = sample.id

        document.getElementById('tags').innerHTML = ''
        selectedCommodities = []
        sample.commodities.forEach((spl) => {
            selectedCommodities.push({
                id: spl.code,
                text: spl.name
            })
        })

        createTags()

        document.getElementById('commodity_error').style.display = 'none'
        document.getElementById('status_error').style.display = 'none'
        document.getElementById('commodityselect').value = '0'

        $('#status').empty();
        $('#status').append(`<option value="0" disabled> --- Pilih Status --- </option>`);
        statuses.forEach((st) => {
            var sel = st.id == sample.status_id ? 'selected' : ''
            $('#status').append(`<option ${sel} value="${st.id}">(${st.code}) ${st.name}</option>`);
        })
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