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

    @if($success)
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card card-stats">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="numbers">
                                <h3 class="mb-2 text-uppercase font-weight-bold">Progres Editing Coding</h3>
                                <h1 class="text-info font-weight-bolder mb-2">{{$percentage_edcod}} %</h1>
                                <p class="mb-0">
                                    Kondisi pada: <span class="text-info text-sm font-weight-bolder">{{$lastUpdate}}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card card-stats">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="numbers">
                                <h3 class="mb-2 text-uppercase font-weight-bold">Progres Pencacahan</h3>
                                <h1 class="text-success font-weight-bolder mb-2">{{$percentage}} %</h1>
                                <p class="mb-0">
                                    Kondisi pada: <span class="text-success text-sm font-weight-bolder">{{$lastUpdate}}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- Custom form validation -->
                <div class="card">
                    <div class="card-body">

                        <div class="tabs mb-3">
                            <button class="btn btn-outline-primary tab-button active" data-tab="tab1">Editing Coding</button>
                            <button class="btn btn-outline-success tab-button" data-tab="tab2">Pencacahan</button>
                        </div>
                        <div class="tab-content">
                            <div class="tab-panel active" id="tab1">
                                <div>
                                    <h3 class="mb-0">Progres Editing Coding Per Tanggal</h3>
                                    <div class="table-responsive mb-4 border">
                                        <table class="table" width="100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    @foreach($dates_edcod as $date)
                                                    <th class="text-center">{{$date}}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @foreach($data_edcod as $dt)
                                                    <td class="text-center border"><strong>{{$dt}}</strong></td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4" style="width: 75%; margin: auto;">
                                        <canvas id="myLineChartEdcod"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel" id="tab2">
                                <div>
                                    <h3 class="mb-0">Progres Pencacahan Per Tanggal</h3>
                                    <div class="table-responsive mb-4 border">
                                        <table class="table" width="100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    @foreach($dates as $date)
                                                    <th class="text-center">{{$date}}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @foreach($data as $dt)
                                                    <td class="text-center border"><strong>{{$dt}}</strong></td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4" style="width: 75%; margin: auto;">
                                        <canvas id="myLineChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- Custom form validation -->
                <div class="card d-flex align-items-center justify-content-center" style="height: 400px;">
                    <h1><i class="ni ni-atom" style="font-size: 8rem;"></i></h1>
                    <h1>Data Report Sedang Diproses, Silakan Refresh dalam Beberapa Saat</h1>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/select2/dist/js/select2.min.js"></script>
<script src="/assets/vendor/datatables2/datatables.min.js"></script>
<script src="/assets/vendor/chart.js2/chart.js"></script>
<script src="/assets/vendor/chart.js2/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    var data = []
    var dates = []
    var data_edcod = []
    var dates_edcod = []

    @foreach($data as $dt)
    data.push(parseFloat('{{$dt}}'));
    @endforeach

    var dates = []
    @foreach($dates as $date)
    dates.push('{{$date}}');
    @endforeach

    @foreach($data_edcod as $dt)
    data_edcod.push(parseFloat('{{$dt}}'));
    @endforeach

    var dates = []
    @foreach($dates_edcod as $date)
    dates_edcod.push('{{$date}}');
    @endforeach
</script>

<script>
    var ctx = document.getElementById('myLineChart').getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Progres Pencacahan',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: data
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    display: true,
                    align: 'top',
                    anchor: 'end',
                    formatter: (value, context) => value,
                    font: {
                        weight: 'bold'
                    }
                },
                title: {
                    display: true,
                    text: (ctx) => 'Point Style: ' + ctx.chart.data.datasets[0].pointStyle,
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    },
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Persentase'
                    }
                }
            },
            plugins: [ChartDataLabels]
        }
    });

    var ctx = document.getElementById('myLineChartEdcod').getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates_edcod,
            datasets: [{
                label: 'Progres Pencacahan',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: data_edcod
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    display: true,
                    align: 'top',
                    anchor: 'end',
                    formatter: (value, context) => value,
                    font: {
                        weight: 'bold'
                    }
                },
                title: {
                    display: true,
                    text: (ctx) => 'Point Style: ' + ctx.chart.data.datasets[0].pointStyle,
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    },
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Persentase'
                    }
                }
            },
            plugins: [ChartDataLabels]
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabButtons = document.querySelectorAll(".tab-button");
        const tabPanels = document.querySelectorAll(".tab-panel");

        tabButtons.forEach(button => {
            button.addEventListener("click", () => {
                const targetTab = button.getAttribute("data-tab");

                // Deactivate all buttons and panels
                tabButtons.forEach(btn => btn.classList.remove("active"));
                tabPanels.forEach(panel => panel.classList.remove("active"));

                // Activate the clicked button and corresponding panel
                button.classList.add("active");
                document.getElementById(targetTab).classList.add("active");
            });
        });

        // Show the first tab by default
        tabButtons[0].classList.add("active");
        tabPanels[0].classList.add("active");
    });
</script>
@endsection