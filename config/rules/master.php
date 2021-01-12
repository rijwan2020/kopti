<?php
return [
    'master' => [
        'label' => 'MENU DATA MASTER',
        'indent' => 0,
        'parent' => ''
    ],
    // =================================================== RULE PROFILE KOPERASI ===================================================
    'koperasi' => [
        'label' => 'Dapat akses profile koperasi',
        'indent' => 1,
        'parent' => 'master'
    ],
    // =================================================== RULE DATA ANGGOTA ===================================================
    'memberList' => [
        'label' => 'Dapat akses data anggota',
        'indent' => 1,
        'parent' => 'master'
    ],
    'memberAdd' => [
        'label' => 'Dapat tambah data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberEdit' => [
        'label' => 'Dapat edit data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberActivity' => [
        'label' => 'Dapat akses catatan aktivitas anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberDelete' => [
        'label' => 'Dapat hapus data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberDownload' => [
        'label' => 'Dapat download data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberUpload' => [
        'label' => 'Dapat upload data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberPrint' => [
        'label' => 'Dapat print data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberDetail' => [
        'label' => 'Dapat akses detail data anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],
    'memberTransaksi' => [
        'label' => 'Dapat akses data transaksi anggota',
        'indent' => 2,
        'parent' => 'memberList'
    ],

    // =================================================== RULE DATA PENGURUS ===================================================
    'managementList' => [
        'label' => 'Dapat akses data pengurus',
        'indent' => 1,
        'parent' => 'master'
    ],
    'managementAdd' => [
        'label' => 'Dapat tambah data pengurus',
        'indent' => 2,
        'parent' => 'managementList'
    ],
    'managementEdit' => [
        'label' => 'Dapat edit data pengurus',
        'indent' => 2,
        'parent' => 'managementList'
    ],
    'managementDelete' => [
        'label' => 'Dapat hapus data pengurus',
        'indent' => 2,
        'parent' => 'managementList'
    ],
    'managementPositionList' => [
        'label' => 'Dapat akses data jabatan pengurus',
        'indent' => 2,
        'parent' => 'managementList'
    ],
    'managementPositionAdd' => [
        'label' => 'Dapat tambah data jabatan pengurus',
        'indent' => 3,
        'parent' => 'managementPositionList'
    ],
    'managementPositionEdit' => [
        'label' => 'Dapat edit data jabatan pengurus',
        'indent' => 3,
        'parent' => 'managementPositionList'
    ],
    'managementPositiondelete' => [
        'label' => 'Dapat hapus data jabatan pengurus',
        'indent' => 3,
        'parent' => 'managementPositionList'
    ],

    // =================================================== RULE DATA KARYAWAN ===================================================
    'employeeList' => [
        'label' => 'Dapat akses data karyawan',
        'indent' => 1,
        'parent' => 'master'
    ],
    'employeeAdd' => [
        'label' => 'Dapat tambah data karyawan',
        'indent' => 2,
        'parent' => 'employeeList'
    ],
    'employeeEdit' => [
        'label' => 'Dapat edit data karyawan',
        'indent' => 2,
        'parent' => 'employeeList'
    ],
    'employeeDelete' => [
        'label' => 'Dapat hapus data karyawan',
        'indent' => 2,
        'parent' => 'employeeList'
    ],
    'employeePositionList' => [
        'label' => 'Dapat akses data posisi karyawan',
        'indent' => 2,
        'parent' => 'employeeList'
    ],
    'employeePositionAdd' => [
        'label' => 'Dapat tambah data posisi karyawan',
        'indent' => 3,
        'parent' => 'employeePositionList'
    ],
    'employeePositionEdit' => [
        'label' => 'Dapat edit data posisi karyawan',
        'indent' => 3,
        'parent' => 'employeePositionList'
    ],
    'employeePositiondelete' => [
        'label' => 'Dapat hapus data posisi karyawan',
        'indent' => 3,
        'parent' => 'employeePositionList'
    ],

    // =================================================== RULE DATA WILAYAH ===================================================
    'regionList' => [
        'label' => 'Dapat akses data wilayah',
        'indent' => 1,
        'parent' => 'master'
    ],
    'regionAdd' => [
        'label' => 'Dapat tambah data wilayah',
        'indent' => 2,
        'parent' => 'regionList'
    ],
    'regionEdit' => [
        'label' => 'Dapat edit data wilayah',
        'indent' => 2,
        'parent' => 'regionList'
    ],
    'regionDelete' => [
        'label' => 'Dapat hapus data wilayah',
        'indent' => 2,
        'parent' => 'regionList'
    ],

    // =================================================== RULE ASET BARANG ===================================================
    'assetList' => [
        'label' => 'Dapat akses data aset barang',
        'indent' => 1,
        'parent' => 'master'
    ],
    'assetAdd' => [
        'label' => 'Dapat tambah data aset barang',
        'indent' => 2,
        'parent' => 'assetList'
    ],
    'assetEdit' => [
        'label' => 'Dapat edit data aset barang',
        'indent' => 2,
        'parent' => 'assetList'
    ],
    'assetDelete' => [
        'label' => 'Dapat edit data aset barang',
        'indent' => 2,
        'parent' => 'assetList'
    ],
    'assetCategoryList' => [
        'label' => 'Dapat manage kategori aset barang',
        'indent' => 2,
        'parent' => 'assetList'
    ],
    'assetCategoryAdd' => [
        'label' => 'Dapat tambah kategori aset barang',
        'indent' => 3,
        'parent' => 'assetCategoryList'
    ],
    'assetCategoryEdit' => [
        'label' => 'Dapat edit kategori aset barang',
        'indent' => 3,
        'parent' => 'assetCategoryList'
    ],
    'assetCategoryDelete' => [
        'label' => 'Dapat hapus kategori aset barang',
        'indent' => 3,
        'parent' => 'assetCategoryList'
    ],

    // =================================================== RULE CONFIG APPS ===================================================
    'configApps' => [
        'label' => 'Dapat akses konfigurasi aplikasi',
        'indent' => 1,
        'parent' => 'master'
    ],
];