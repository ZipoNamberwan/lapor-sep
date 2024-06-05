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
                        <h3 class="mb-0">Input Progres</h3>
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
            <div class="modal-header pb-1">
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
                    <label class="form-control-label">Komoditas <span class="text-danger">*</span> <small>Isikan jika berhasil cacah</small></label>
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

<div class="modal fade" id="changeSampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ganti Sampel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: auto;">
                <input type="hidden" id="toreplace" />
                <label class="form-control-label">Sampel yang akan Diganti:</label>
                <div id="sampletochange">

                </div>
                <label class="mt-4 form-control-label">Pilih Sampel Pengganti <span class="text-danger">*</span></label>

                <div class="d-flex flex-wrap align-items-center mb-2">
                    <p class="mb-1 text-muted mr-2" style="font-size: 0.8rem;">Apakah pengganti dalam BS yang sama?</p>
                    <label class="mb-1 custom-toggle">
                        <input id="samebs" type="checkbox" checked name="samebs">
                        <span class="custom-toggle-slider rounded-circle" data-label-off="Tidak" data-label-on="Ya"></span>
                    </label>
                </div>

                <div id="changebssection" class="row" style="display: none;">
                    <div class="col-md-12 mt-2">
                        <label class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                        <select id="subdistrictsample" name="subdistrictsample" class="form-control" data-toggle="select" required>
                            <option value="0" disabled selected> -- Pilih Kecamatan -- </option>
                            @foreach ($subdistricts as $subdistrict)
                            <option value="{{ $subdistrict->id }}" {{ old('subdistrict') == $subdistrict->id ? 'selected' : '' }}>
                                [{{ $subdistrict->short_code}}] {{ $subdistrict->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-control-label">Desa <span class="text-danger">*</span></label>
                        <select id="villagesample" name="villagesample" class="form-control" data-toggle="select"></select>
                    </div>
                    <div id="bs_div" class="col-md-12 mt-2 mb-4">
                        <label class="form-control-label">Blok Sensus <span class="text-danger">*</span></label>
                        <select id="bssample" name="bssample" class="form-control" data-toggle="select"></select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-control-label">Pilih Sample Pengganti dari BS Lain <span class="text-danger">*</span></label>
                        <select id="sampleChangeListsample" class="form-control" data-toggle="select"></select>
                        <div id="sampleChangeErrorsample" style="display: none;" class="text-valid mt-2">
                            Belum diisi
                        </div>
                    </div>
                </div>

                <div id="samebssection">
                    <select id="sampleChangeList" class="form-control mt-2" data-toggle="select"></select>
                    <div id="sampleChangeError" style="display: none;" class="text-valid mt-2">
                        Belum diisi
                    </div>
                </div>
                <div>
                    <p id="loading-change" style="visibility: hidden;" class="text-warning mt-3">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button onclick="onChange()" type="button" class="btn btn-primary">Ganti</button>
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
    var samples = []
    var selectedBS = null

    $(document).ready(function() {
        $('#subdistrict').on('change', function() {
            loadVillage(false, null, null);
        });
        $('#village').on('change', function() {
            loadBs(false, null, null);
        });
        $('#bs').on('change', function() {
            loadSample(null);
        });


        $('#samebs').on('change', function() {
            onChangeBs(document.getElementById('samebs').checked)
        });
        $('#subdistrictsample').on('change', function() {
            loadVillage(true, null, null);
        });
        $('#villagesample').on('change', function() {
            loadBs(true, null, null);
        });
        $('#bssample').on('change', function() {
            loadChangeSample();
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

    function onChangeBs(checked) {
        if (checked) {
            document.getElementById('changebssection').style.display = 'none'
            document.getElementById('samebssection').style.display = 'block'
        } else {
            document.getElementById('changebssection').style.display = 'block'
            document.getElementById('samebssection').style.display = 'none'
        }
    }

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

    function validateChange() {

        issamebs = document.getElementById('samebs').checked;
        var replacement_valid = true
        if (issamebs) {
            if (document.getElementById('sampleChangeList').value == 0 || document.getElementById('sampleChangeList').value == null) {
                replacement_valid = false
                document.getElementById('sampleChangeError').style.display = 'block'
            } else {
                document.getElementById('sampleChangeError').style.display = 'none'
            }
        } else {
            if (document.getElementById('sampleChangeListsample').value == 0 || document.getElementById('sampleChangeListsample').value == null) {
                replacement_valid = false
                document.getElementById('sampleChangeErrorsample').style.display = 'block'
            } else {
                document.getElementById('sampleChangeErrorsample').style.display = 'none'
            }
        }

        return replacement_valid
    }

    function onChange() {
        document.getElementById('sampleChangeError').style.display = 'none'
        document.getElementById('sampleChangeErrorsample').style.display = 'none'
        if (validateChange()) {
            document.getElementById('loading-change').style.visibility = 'visible'

            id = document.getElementById('toreplace').value
            issamebs = document.getElementById('samebs').checked;
            if (issamebs) {
                var updateData = {
                    replacement: document.getElementById('sampleChangeList').value,
                };
            } else {
                var updateData = {
                    replacement: document.getElementById('sampleChangeListsample').value,
                };
            }

            $.ajax({
                url: `/petugas/edit/sample/${id}`,
                type: 'PATCH',
                data: updateData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    loadSample(null)
                    $('#changeSampleModal').modal('hide');
                    document.getElementById('sampleChangeError').style.display = 'none'
                    document.getElementById('loading-change').style.visibility = 'hidden'
                },
                error: function(xhr, status, error) {
                    document.getElementById('sampleChangeError').style.display = 'none'
                    document.getElementById('loading-change').style.visibility = 'hidden'
                }
            });
        }
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
            },
            language: {
                inputTooShort: function(args) {
                    const remainingChars = args.minimum - args.input.length;
                    return `Ketik minimal ${remainingChars} huruf untuk melakukan pencarian`;
                }
            }
        });
    }

    function loadVillage(isSample = false, subdistrictid = null, selectedvillage = null) {
        let id = $('#subdistrict' + (isSample ? 'sample' : '')).val();
        if (subdistrictid != null) {
            id = subdistrictid;
        }
        if (!isSample) {
            const resultDiv = document.getElementById('samplelist');
            resultDiv.innerHTML = '';
        } else {
            $('#sampleChangeListsample').empty();
            $('#sampleChangeListsample').append(`<option value="0" disabled selected> -- Pilih Sampel Pengganti -- </option>`);
        }
        $('#village' + (isSample ? 'sample' : '')).empty();
        $('#village' + (isSample ? 'sample' : '')).append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/desa/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#village' + (isSample ? 'sample' : '')).empty();
                $('#village' + (isSample ? 'sample' : '')).append(`<option value="0" disabled selected> -- Pilih Desa -- </option>`);
                $('#bs' + (isSample ? 'sample' : '')).empty();
                $('#bs' + (isSample ? 'sample' : '')).append(`<option value="0" disabled selected> -- Pilih Blok Sensus -- </option>`);
                response.forEach(element => {
                    if (selectedvillage == String(element.id)) {
                        $('#village' + (isSample ? 'sample' : '')).append('<option value=\"' + element.id + '\" selected>' +
                            '[' + element.short_code + '] ' + element.name + '</option>');
                    } else {
                        $('#village' + (isSample ? 'sample' : '')).append('<option value=\"' + element.id + '\">' + '[' +
                            element.short_code + '] ' + element.name + '</option>');
                    }
                });
            }
        });
    }

    function loadBs(isSample = false, villageid = null, selectedbs = null) {
        let id = $('#village' + (isSample ? 'sample' : '')).val();
        if (villageid != null) {
            id = villageid;
        }
        if (!isSample) {
            const resultDiv = document.getElementById('samplelist');
            resultDiv.innerHTML = '';
        } else {
            $('#sampleChangeListsample').empty();
            $('#sampleChangeListsample').append(`<option value="0" disabled selected> -- Pilih Sampel Pengganti -- </option>`);
        }
        $('#bs' + (isSample ? 'sample' : '')).empty();
        $('#bs' + (isSample ? 'sample' : '')).append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/bs/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#bs' + (isSample ? 'sample' : '')).empty();
                $('#bs' + (isSample ? 'sample' : '')).append(`<option value="0" disabled selected> -- Pilih Blok Sensus -- </option>`);
                response.forEach(element => {
                    if (selectedbs == String(element.id)) {
                        $('#bs' + (isSample ? 'sample' : '')).append('<option value=\"' + element.id + '\" selected>' +
                            element.name + '</option>');
                    } else {
                        $('#bs' + (isSample ? 'sample' : '')).append('<option value=\"' + element.id + '\">' +
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

                selectedBS = id;

                samples = []
                var response = JSON.parse(response);

                const resultDiv = document.getElementById('samplelist');
                resultDiv.innerHTML = '';

                const titleDiv = document.createElement('label')
                titleDiv.className = 'form-control-label'
                titleDiv.innerHTML = 'Pilih Sampel'

                resultDiv.appendChild(titleDiv)

                response.forEach(item => {
                    samples.push(item)

                    if (item.type == 'Utama' || item.is_selected == 1) {
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
                            <button onclick="showChangeSampleModal(${JSON.stringify(item).replace(/"/g, '&quot;')});"
                                class="btn btn-outline-success btn-sm">
                                <span class="btn-inner--icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                            </button>
                        ` :
                            '';

                        var bs = item.replacement != null ? item.replacement.bs.id != selectedBS ? (' dari BS lain: ** ' + item.replacement.area + ' (' + item.replacement.bs.long_code + ') **') : '' : '';
                        var replacementicon = false ? '<i class="fas fa-dot-circle text-danger"></i> ' : '';
                        var replacement = item.sample_id != null ?
                            `<span style="font-size: 0.9rem">${replacementicon}Digantikan oleh: <strong>(${item.sample_no}) ${item.sample_name} ${bs}</strong></span>` : ''

                        var bsreplacing = item.replacing.length > 0 ? item.replacing[0].bs.id != selectedBS ? (' dari BS lain: ** ' + item.replacing[0].area + ' (' + item.replacing[0].bs.long_code + ') **') : '' : '';
                        var replacingicon = false ? '<i class="fas fa-dot-circle text-success"></i> ' : '';
                        var replacing = item.replacing.length > 0 ? `<span style="font-size: 0.9rem">${replacingicon}Pengganti dari: <strong>(${item.replacing[0].no}) ${item.replacing[0].name} ${bsreplacing}</strong></span>` : ''

                        var replacementicon = item.replacement != null ? '<i class="fas fa-arrow-alt-circle-down text-danger"></i>' : ''
                        var replacingicon = item.replacing.length > 0 ? '<i class="fas fa-arrow-alt-circle-up text-success"></i>' : ''

                        itemDiv.innerHTML = `
                        <div class="mb-1">
                            <h4 class="mb-1">(${item.no}) ${item.name} ${replacementicon} ${replacingicon}</h4>
                            <span class="mb-1" style="font-size: 0.9rem">${item.type}</span>
                            <p class="mb-1"><span class="badge badge-${item.color}">${item.status_name}</span></p>
                            ${replacement}
                            ${replacing}
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
                    }
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

    function loadChangeSample(bsid = null) {
        let id = $('#bssample').val();
        if (bsid != null) {
            id = bsid;
        }

        $.ajax({
            type: 'GET',
            url: '/sample/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#sampleChangeListsample').empty()
                $('#sampleChangeListsample').append(`<option value="0" disabled selected> -- Pilih Sampel Pengganti -- </option>`);
                response.forEach((sample) => {
                    if (sample.type == 'Cadangan' && sample.is_selected == 0) {
                        $('#sampleChangeListsample').append(`<option value="${sample.id}">(${sample.no}) ${sample.name}</option>`);
                    }
                })
            },
            error: function(jqXHR, textStatus, errorThrown) {}
        });
    }

    function showChangeSampleModal(item) {
        event.stopPropagation()
        $('#changeSampleModal').modal('show');

        const itemDiv = document.getElementById('sampletochange');
        itemDiv.className = 'border p-2 bg-white rounded d-flex flex-wrap justify-content-between align-items-center mb-1';
        itemDiv.innerHTML = `
                <div class="mb-1">
                    <h4 class="mb-1">(${item.no}) ${item.name}</h4>
                    <p class="mb-0"><span class="badge badge-${item.color}">${item.status_name}</span></p>
                </div>
            `;

        document.getElementById('subdistrictsample').value = 0
        const options = document.getElementById('subdistrictsample').options;
        var opt = []
        for (let i = 0; i < options.length; i++) {
            if (i == 0) {
                options[i].selected = true;
            } else {
                options[i].selected = false;
            }
            opt.push(options[i])
        }
        $('#subdistrictsample').empty()
        for (let i = 0; i < opt.length; i++) {
            $('#subdistrictsample').append(opt[i]);
        }

        $('#villagesample').empty()
        $('#villagesample').append(`<option value="0" disabled selected> -- Pilih Desa -- </option>`);

        $('#bssample').empty()
        $('#bssample').append(`<option value="0" disabled selected> -- Pilih Blok Sensus -- </option>`);

        $('#sampleChangeListsample').empty()
        $('#sampleChangeListsample').append(`<option value="0" disabled selected> -- Pilih Sampel Pengganti -- </option>`);

        if (item.replacement != null) {
            if (item.replacement.bs_id == item.bs_id) {
                document.getElementById('samebs').checked = true
                onChangeBs(true)
            } else {
                document.getElementById('samebs').checked = false
                onChangeBs(false)

                const options = document.getElementById('subdistrictsample').options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value == item.replacement.subdistrict_id) {
                        options[i].selected = true;
                    }
                }
                loadVillage(true, item.replacement.subdistrict_id, item.replacement.village_id);
                loadBs(true, item.replacement.village_id, item.replacement.bs_id);
                loadChangeSample(item.replacement.bs_id);
            }
        } else {
            document.getElementById('samebs').checked = true
            onChangeBs(true)
        }

        $('#sampleChangeList').empty()
        $('#sampleChangeList').append(`<option value="0" disabled selected> -- Pilih Sampel Pengganti -- </option>`);
        samples.forEach((sample) => {
            if (sample.type == 'Cadangan' && sample.is_selected == 0) {
                var sel = item.sample_id == sample.id ? 'selected' : ''
                $('#sampleChangeList').append(`<option ${sel} value="${sample.id}">(${sample.no}) ${sample.name}</option>`);
            }
        })

        document.getElementById('toreplace').value = item.id
    }

    function updateModal(sample) {
        // console.log(sample)

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
        $('#status').append(`<option value="0" disabled> -- Pilih Status -- </option>`);
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