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
                        <h3 class="mb-0">Unduh Progres</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <form id="formupdate" autocomplete="off" method="post" action="/download" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @method('post')
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-control-label">Pilih Level <span class="text-danger">*</span></label>
                                    <select id="level" name="level" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Level -- </option>
                                        <option value="kab">Progres Pencacahan Menurut Kabupaten</option>
                                        <option value="bs">Progres Pencacahan Menurut BS</option>
                                        @hasrole('adminkab')
                                        <option value="sample">Progres Pencacahan Menurut Sampel Ruta</option>
                                        @endhasrole
                                        <option value="petugas">Progres Pencacahan Petugas</option>
                                        <option value="kab_edcod">Progres Edcod Menurut Kabupaten</option>
                                        <option value="bs_edcod">Progres Edcod Menurut BS</option>
                                    </select>
                                    @error('level')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3" id="submit" type="submit">Download</button>
                        </form>
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

@endsection