@extends('layouts.application')

@section('content')
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                Selamat datang <b>{{ auth()->user()->name }}</b>, Waktu login anda pada {{ auth()->user()->last_access }}
            </div>
        </div>
    </div>
</div>
@if (!auth()->user()->isMember())
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fa fa-user display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>Jumlah Anggota Aktif</b></div>
                        <div class="text-large">{{ $data['member']->where('status', 1)->count() }} Orang</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fa fa-user display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>Jumlah Non Anggota</b></div>
                        <div class="text-large">{{ $data['member']->where('status', 0)->count() }} Orang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div id="region"></div>
    </div>
</div>
@endif
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="ion ion-ios-wallet display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>Total Simpanan</b></div>
                        <div class="text-large">Rp{{ number_format($data['total_simpanan'], 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="fa fa-credit-card display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>Total Rekening</b></div>
                        <div class="text-large">{{ number_format($data['total_rekening']) }} Rekening</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Simpanan Masuk Hari ini : <b>Rp{{ number_format($data['simpanan_hari_ini']->kredit, 2, ',', '.') }}</b></div>
            <div class="card-header">Simpanan Keluar Hari ini : <b>Rp{{ number_format($data['simpanan_hari_ini']->debit, 2, ',', '.') }}</b></div>
        </div>
    </div>
    <div class="col-md-8">
        <div id="simpanan"></div>
    </div>
</div>
@if (!auth()->user()->isMember())
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="large"><b>Jumlah stok sampai hari ini</b></div>
                <div class="text-large">{{ number_format($data['stok'], 2, ',', '.') }} kg</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="large"><b>Pembelian di periode ini</b></div>
                <div class="text-large">{{ number_format($data['penambahan'], 2, ',', '.') }} kg</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="large"><b>Penjualan di periode ini</b></div>
                <div class="text-large">{{ number_format($data['pengurangan'], 2, ',', '.') }} kg</div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div id="grafik-stok"></div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="ion ion-md-cash display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>SHU Bersih Tahun Lalu</b></div>
                        <div class="text-large">Rp{{ number_format($data['shu_lalu'], 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="ion ion-md-cash display-4"></div>
                    <div class="ml-3">
                        <div class="large"><b>SHU Bersih Tahun Sekarang</b></div>
                        <div class="text-large">Rp{{ number_format($data['shu'], 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div id="shu"></div>
    </div>
</div>
@endif
@endsection



@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    @if (!auth()->user()->isMember())
        <script>
            $(document).ready(function(){
                Highcharts.chart('region', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Grafik Penyebaran Anggota dan Non Anggota'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: [
                            <?php
                            foreach($data['region'] as $value){
                                echo "'".$value->name."',";
                            }
                            ?>
                        ],
                        title: {
                            text: null
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah',
                            align: 'middle'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' orang'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    exporting: false,
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Jumlah',
                        data: [
                            <?php
                            foreach($data['region'] as $value){
                                echo ($value->member->count()).",";
                            }
                            ?>
                        ]
                    }]

                });
                Highcharts.chart('shu', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: 0,
                        plotShadow: false,
                    },
                    title: {
                        text: 'Total<br>SHU',
                        align: 'center',
                        verticalAlign: 'middle',
                        y: 60
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>Rp{point.y}</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            dataLabels: {
                                enabled: true,
                                distance: -50,
                                style: {
                                    fontWeight: 'bold',
                                    color: 'white'
                                }
                            },
                            startAngle: -90,
                            endAngle: 90,
                            center: ['50%', '75%'],
                            size: '110%'
                        }
                    },
                    exporting: false,
                    credits: {
                        enabled: false
                    },
                    series: [{
                        type: 'pie',
                        name: 'Total SHU',
                        innerSize: '50%',
                        data: [
                            ['2019', {{ $data["shu_lalu"] }}],
                            ['2020', {{ $data["shu"] }}]
                        ]
                    }]
                });
                Highcharts.chart('grafik-stok', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Grafik Penjualan dan Pembelian Barang'
                    },
                    subtitle: {
                        text: 'Periode Jan {{ date("Y") }} s/d {{ date("M Y") }}'
                    },
                    xAxis: {
                        categories: [
                            <?php
                            foreach($data['grafik_stok'] as $value){
                                echo "'".$value['bulan']."',";
                            }
                            ?>
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah (Kg)'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.2f} kg</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    exporting: false,
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Pembelian',
                        data: [
                            <?php
                            foreach($data['grafik_stok'] as $value){
                                echo $value['penambahan'].",";
                            }
                            ?>
                        ]

                    }, {
                        name: 'Penjualan',
                        data: [
                            <?php
                            foreach($data['grafik_stok'] as $value){
                                echo $value['pengurangan'].",";
                            }
                            ?>
                        ]

                    }]
                });
            });
        </script>
    @endif
    <script>
        $(document).ready(function(){
            Highcharts.chart('simpanan', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Data Simpanan Tahun {{ date("Y") }}'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        <?php 
                        foreach($data['simpanan_tahunan'] as $value){
                            echo "'".$value['bulan']."',";
                        }
                        ?>
                    ],
                    tickmarkPlacement: 'on',
                    title: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    },
                    labels: {
                        formatter: function () {
                            return this.value / 1000;
                        }
                    }
                },
                exporting: false,
                credits: {
                    enabled: false
                },
                tooltip: {
                    split: true,
                    valueSuffix: ''
                },
                plotOptions: {
                    area: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#666666'
                        }
                    }
                },
                series: [{
                    name: 'Jumlah Simpanan',
                    data: [
                        <?php 
                        foreach($data['simpanan_tahunan'] as $value){
                            echo "".$value['total'].",";
                        }
                        ?>
                    ]
                }]
            });
        });
    </script>
@endsection
