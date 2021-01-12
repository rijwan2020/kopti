<?php
return [
    'report' => [
        'label' => 'MENU LAPORAN',
        'indent' => 0,
        'parent' => ''
    ],

    // =================================================== NERACA ===================================================
    'balance' => [
        'label' => 'Dapat akses neraca',
        'indent' => 1,
        'parent' => 'report'
    ],
    'balancePrint' => [
        'label' => 'Dapat print neraca',
        'indent' => 2,
        'parent' => 'balance'
    ],
    'balanceDownload' => [
        'label' => 'Dapat download neraca',
        'indent' => 2,
        'parent' => 'balance'
    ],
    'balanceDescription' => [
        'label' => 'Dapat akses penjelasan neraca',
        'indent' => 2,
        'parent' => 'balance'
    ],
    'balanceDescriptionPrint' => [
        'label' => 'Dapat print penjelasan neraca',
        'indent' => 3,
        'parent' => 'balanceDescription'
    ],
    'balanceDescriptionDownload' => [
        'label' => 'Dapat Download penjelasan neraca',
        'indent' => 3,
        'parent' => 'balanceDescription'
    ],

    // =================================================== PHU ===================================================
    'phu' => [
        'label' => 'Dapat akses PHU',
        'indent' => 1,
        'parent' => 'report'
    ],
    'phuPrint' => [
        'label' => 'Dapat print PHU',
        'indent' => 2,
        'parent' => 'phu'
    ],
    'phuDownload' => [
        'label' => 'Dapat download PHU',
        'indent' => 2,
        'parent' => 'phu'
    ],

    // =================================================== SHU ===================================================
    'shu' => [
        'label' => 'Dapat akses shu',
        'indent' => 1,
        'parent' => 'report'
    ],
    'shuPrint' => [
        'label' => 'Dapat print shu',
        'indent' => 2,
        'parent' => 'shu'
    ],
    'shuDownload' => [
        'label' => 'Dapat download shu',
        'indent' => 2,
        'parent' => 'shu'
    ],
    'shuConfig' => [
        'label' => 'Dapat akses set alokasi shu',
        'indent' => 2,
        'parent' => 'shu'
    ],
    'shuConfigAdd' => [
        'label' => 'Dapat tambah alokasi shu',
        'indent' => 3,
        'parent' => 'shuConfig'
    ],
    'shuConfigEdit' => [
        'label' => 'Dapat edit alokasi shu',
        'indent' => 3,
        'parent' => 'shuConfig'
    ],
    'shuConfigDelete' => [
        'label' => 'Dapat hapus alokasi shu',
        'indent' => 3,
        'parent' => 'shuConfig'
    ],
    'shuAnggota' => [
        'label' => 'Dapat akses data shu anggota',
        'indent' => 2,
        'parent' => 'shu'
    ],
    'shuAnggotaPrint' => [
        'label' => 'Dapat print data shu anggota',
        'indent' => 3,
        'parent' => 'shuAnggota'
    ],
    'shuAnggotaDownload' => [
        'label' => 'Dapat Download data shu anggota',
        'indent' => 3,
        'parent' => 'shuAnggota'
    ],


    // =================================================== LAPORAN HARIAN ===================================================
    'laporanHarian' => [
        'label' => 'Dapat akses Laporan harian',
        'indent' => 1,
        'parent' => 'report'
    ],
    'laporanHarianPrint' => [
        'label' => 'Dapat print laporan harian',
        'indent' => 2,
        'parent' => 'laporanHarian'
    ],
    'laporanHarianDownload' => [
        'label' => 'Dapat Download laporan harian',
        'indent' => 2,
        'parent' => 'laporanHarian'
    ],
    'laporanKasBank' => [
        'label' => 'Dapat akses laporan kas dan bank',
        'indent' => 2,
        'parent' => 'laporanHarian'
    ],
    'laporanKasBankPrint' => [
        'label' => 'Dapat print laporan kas dan bank',
        'indent' => 3,
        'parent' => 'laporanKasBank'
    ],
    'laporanKasBankDownload' => [
        'label' => 'Dapat Download laporan kas dan bank',
        'indent' => 3,
        'parent' => 'laporanKasBank'
    ],

    // =================================================== ARUS KAS ===================================================
    // 'cashflow' => [
    //     'label' => 'Dapat akses arus kas',
    //     'indent' => 1,
    //     'parent' => 'report'
    // ],
    // 'cashflowPrint' => [
    //     'label' => 'Dapat print arus kas',
    //     'indent' => 2,
    //     'parent' => 'cashflow'
    // ],
    // 'cashflowDownload' => [
    //     'label' => 'Dapat download arus kas',
    //     'indent' => 2,
    //     'parent' => 'cashflow'
    // ],

    // =================================================== PERUBAHAN MODAL ===================================================
    // 'ekuitas' => [
    //     'label' => 'Dapat akses perubahan modal',
    //     'indent' => 1,
    //     'parent' => 'report'
    // ],
    // 'ekuitasPrint' => [
    //     'label' => 'Dapat print perubahan modal',
    //     'indent' => 2,
    //     'parent' => 'ekuitas'
    // ],
    // 'ekuitasDownload' => [
    //     'label' => 'Dapat download perubahan modal',
    //     'indent' => 2,
    //     'parent' => 'ekuitas'
    // ],
];