<?php

return [
    'deposit' => [
        'label' => 'MENU SIMPANAN',
        'indent' => 0,
        'parent' => ''
    ],

    // =================================================== RULE DATA SIMPANAN ===================================================
    'depositList' => [
        'label' => 'Dapat akses data simpanan',
        'indent' => 1,
        'parent' => 'deposit'
    ],
    'depositAdd' => [
        'label' => 'Dapat tambah data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositDelete' => [
        'label' => 'Dapat hapus data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositPrint' => [
        'label' => 'Dapat print data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositDownload' => [
        'label' => 'Dapat download data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositUpload' => [
        'label' => 'Dapat Upload data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositDetail' => [
        'label' => 'Dapat akses detail data simpanan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositDetailAdd' => [
        'label' => 'Dapat tambah transaksi simpanan',
        'indent' => 3,
        'parent' => 'depositDetail'
    ],
    'depositDetailPrint' => [
        'label' => 'Dapat print transaksi simpanan',
        'indent' => 3,
        'parent' => 'depositDetail'
    ],
    'depositDetailDownload' => [
        'label' => 'Dapat download transaksi simpanan',
        'indent' => 3,
        'parent' => 'depositDetail'
    ],
    'depositBook' => [
        'label' => 'Dapat akses buku tabungan',
        'indent' => 2,
        'parent' => 'depositList'
    ],
    'depositBookResetAll' => [
        'label' => 'Dapat reset print semua buku tabungan',
        'indent' => 2,
        'parent' => 'depositBook'
    ],
    'depositBookReset' => [
        'label' => 'Dapat reset print satuan buku tabungan',
        'indent' => 2,
        'parent' => 'depositBook'
    ],
    'depositBookPrint' => [
        'label' => 'Dapat print buku tabungan',
        'indent' => 2,
        'parent' => 'depositBook'
    ],
    'depositBookPrintConfirm' => [
        'label' => 'Dapat update print buku tabungan',
        'indent' => 3,
        'parent' => 'depositBookPrint'
    ],

    // =================================================== RULE JENIS SIMPANAN ===================================================
    'depositTypeList' => [
        'label' => 'Dapat akses data jenis simpanan',
        'indent' => 1,
        'parent' => 'deposit'
    ],
    'depositTypeAdd' => [
        'label' => 'Dapat tambah data jenis simpanan',
        'indent' => 2,
        'parent' => 'depositTypeList'
    ],
    'depositTypeEdit' => [
        'label' => 'Dapat edit jenis simpanan',
        'indent' => 2,
        'parent' => 'depositTypeList'
    ],
    'depositTypeDelete' => [
        'label' => 'Dapat hapus jenis simpanan',
        'indent' => 2,
        'parent' => 'depositTypeList'
    ],

    // =================================================== RULE DATA TRANSAKSI ===================================================
    'depositTransactionList' => [
        'label' => 'Dapat akses data transaksi simpanan',
        'indent' => 1,
        'parent' => 'deposit'
    ],
    'depositTransactionDownload' => [
        'label' => 'Dapat download data transaksi simpanan',
        'indent' => 2,
        'parent' => 'depositTransactionList'
    ],
    'depositTransactionPrintAll' => [
        'label' => 'Dapat print semua data transaksi simpanan',
        'indent' => 2,
        'parent' => 'depositTransactionList'
    ],
    'depositTransactionPrint' => [
        'label' => 'Dapat print satuan data transaksi simpanan',
        'indent' => 2,
        'parent' => 'depositTransactionList'
    ],
    'depositTransactionUpload' => [
        'label' => 'Dapat Upload data transaksi simpanan',
        'indent' => 2,
        'parent' => 'depositTransactionList'
    ],

    // =================================================== RULE TAGIHAN ===================================================
    // 'depositBillList' => [
    //     'label' => 'Dapat akses data tagihan simpanan',
    //     'indent' => 1,
    //     'parent' => 'deposit'
    // ],
    // 'depositBillDownload' => [
    //     'label' => 'Dapat download data tagihan simpanan',
    //     'indent' => 2,
    //     'parent' => 'depositBillList'
    // ],

    // =================================================== RULE LAPORAN ===================================================
    'laporanSimpanan' => [
        'label' => 'Dapat akses data laporan simpanan',
        'indent' => 1,
        'parent' => 'deposit'
    ],
    'simpananAnggota' => [
        'label' => 'Dapat akses laporan daftar simpanan anggota',
        'indent' => 2,
        'parent' => 'laporanSimpanan'
    ],
    'simpananAnggotaPrint' => [
        'label' => 'Dapat print laporan daftar simpanan anggota',
        'indent' => 3,
        'parent' => 'simpananAnggota'
    ],
    'simpananAnggotaDownload' => [
        'label' => 'Dapat Download laporan daftar simpanan anggota',
        'indent' => 3,
        'parent' => 'simpananAnggota'
    ],
    'simpananAnggotaDetail' => [
        'label' => 'Dapat akses detail laporan daftar simpanan anggota',
        'indent' => 3,
        'parent' => 'simpananAnggota'
    ],
    'simpananAnggotaDetailPrint' => [
        'label' => 'Dapat print detail laporan daftar simpanan anggota',
        'indent' => 4,
        'parent' => 'simpananAnggotaDetail'
    ],
    'simpananAnggotaDetailDownload' => [
        'label' => 'Dapat Download detail laporan daftar simpanan anggota',
        'indent' => 4,
        'parent' => 'simpananAnggotaDetail'
    ],
    'rekapitulasiSimpanan' => [
        'label' => 'Dapat akses laporan rekapitulasi simpanan anggota',
        'indent' => 2,
        'parent' => 'laporanSimpanan'
    ],
    'rekapitulasiSimpananPrint' => [
        'label' => 'Dapat print laporan rekapitulasi simpanan anggota',
        'indent' => 3,
        'parent' => 'rekapitulasiSimpanan'
    ],
    'rekapitulasiSimpananDownload' => [
        'label' => 'Dapat Download laporan rekapitulasi simpanan anggota',
        'indent' => 3,
        'parent' => 'rekapitulasiSimpanan'
    ],
    'rekapitulasiSimpananDetail' => [
        'label' => 'Dapat akses detail laporan rekapitulasi simpanan anggota',
        'indent' => 3,
        'parent' => 'rekapitulasiSimpanan'
    ],
    'rekapitulasiSimpananDetailPrint' => [
        'label' => 'Dapat print detail laporan rekapitulasi simpanan anggota',
        'indent' => 4,
        'parent' => 'rekapitulasiSimpananDetail'
    ],
    'rekapitulasiSimpananDetailDownload' => [
        'label' => 'Dapat Download detail laporan rekapitulasi simpanan anggota',
        'indent' => 4,
        'parent' => 'rekapitulasiSimpananDetail'
    ],
];