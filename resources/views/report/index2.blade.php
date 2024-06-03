<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <div style="width: 75%; margin: auto;">
        <canvas id="myLineChart"></canvas>
    </div>

    <script src="/assets/vendor/chart.js2/chart.js"></script>
    <script src="/assets/vendor/chart.js2/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
        var data = []
        var dates = []

        @foreach($data as $dt)
        data.push(parseFloat('{{$dt}}'));
        @endforeach

        var dates = []
        @foreach($dates as $date)
        dates.push('{{$date}}');
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
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tanggal'
                        },
                        font: {
                            size: 22
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Persentase'
                        },
                        font: {
                            size: 22
                        }
                    }
                },
                plugins: [ChartDataLabels]
            }
        });
    </script>
</body>

</html>