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
                        <h3 class="mb-0">[{{$bs->long_code}}] {{$bs->village->name}} {{$bs->short_code}}</h3>
                    </div>
                    <!-- Card body -->
                    <div class="row">
                        <div class="col-12" id="row-table">
                            <div class="table-responsive">
                                <table class="table" id="datatable-id" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Sampel</th>
                                            <th>Petugas</th>
                                            <th>Status</th>
                                            <th style="max-width: 400px;">Komoditas</th>
                                            <th>Pengganti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($samples as $sample)
                                        <tr>
                                            <td>[{{$sample->no}}] {{$sample->name}}</td>
                                            <td>
                                                <p class="mb-0">{{$sample->user != null ? $sample->user->name : ''}}</p>
                                                <p class="mb-0 text-muted">{{$sample->user != null ? $sample->user->pml : ''}}</p>
                                            </td>
                                            <td>
                                                <p class="mb-1"><span class="badge badge-{{$sample->status->color}}">{{$sample->status->name}}</span></p>
                                            </td>
                                            <td>
                                                @foreach($sample->commodities as $com)
                                                <button class="mb-1 btn btn-sm btn-primary">{{$com->name}}</button>
                                                @endforeach
                                            </td>
                                            <td>{{$sample->replacement != null ? $sample->replacement->name : '-'}}</td>
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

@endsection