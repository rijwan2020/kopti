<?php

return [
    'store' => [
        'label' => 'MENU TOKO',
        'indent' => 0,
        'parent' => ''
    ],

    // =================================================== RULE DATA BARANG ===================================================
    'itemList' => [
        'label' => 'Dapat akses data barang',
        'indent' => 1,
        'parent' => 'store'
    ],
    'itemAdd' => [
        'label' => 'Dapat tambah data barang',
        'indent' => 2,
        'parent' => 'itemList'
    ],
    'itemEdit' => [
        'label' => 'Dapat edit data barang',
        'indent' => 2,
        'parent' => 'itemList'
    ],
    'itemDelete' => [
        'label' => 'Dapat delete data barang',
        'indent' => 2,
        'parent' => 'itemList'
    ],
    'itemUpload' => [
        'label' => 'Dapat upload data barang',
        'indent' => 2,
        'parent' => 'itemList'
    ],
    'itemUploadFormat' => [
        'label' => 'Dapat download format untuk upload data barang',
        'indent' => 3,
        'parent' => 'itemUpload'
    ],
    'itemDetail' => [
        'label' => 'Dapat akses detail data barang',
        'indent' => 2,
        'parent' => 'itemList'
    ],
    'itemDistribution' => [
        'label' => 'Dapat distribusi data barang',
        'indent' => 3,
        'parent' => 'itemDetail'
    ],
    'itemCard' => [
        'label' => 'Dapat akses kartu persediaan',
        'indent' => 3,
        'parent' => 'itemDetail'
    ],
    'itemCardPrint' => [
        'label' => 'Dapat print kartu persediaan',
        'indent' => 4,
        'parent' => 'itemCard'
    ],
    'itemCardDownload' => [
        'label' => 'Dapat download kartu persediaan',
        'indent' => 4,
        'parent' => 'itemCard'
    ],

    // =================================================== RULE DATA SUPLIER ===================================================
    'suplierList' => [
        'label' => 'Dapat akses data suplier',
        'indent' => 1,
        'parent' => 'store'
    ],
    'suplierAdd' => [
        'label' => 'Dapat tambah data suplier',
        'indent' => 2,
        'parent' => 'suplierList'
    ],
    'suplierEdit' => [
        'label' => 'Dapat edit data suplier',
        'indent' => 2,
        'parent' => 'suplierList'
    ],
    'suplierDelete' => [
        'label' => 'Dapat delete data suplier',
        'indent' => 2,
        'parent' => 'suplierList'
    ],

    // =================================================== RULE DATA GUDANG ===================================================
    'warehouseList' => [
        'label' => 'Dapat akses data gudang',
        'indent' => 1,
        'parent' => 'store'
    ],
    'warehouseAdd' => [
        'label' => 'Dapat tambah data gudang',
        'indent' => 2,
        'parent' => 'warehouseList'
    ],
    'warehouseEdit' => [
        'label' => 'Dapat edit data gudang',
        'indent' => 2,
        'parent' => 'warehouseList'
    ],
    'warehouseDelete' => [
        'label' => 'Dapat delete data gudang',
        'indent' => 2,
        'parent' => 'warehouseList'
    ],
    'warehouseUser' => [
        'label' => 'Dapat akses data user gudang',
        'indent' => 2,
        'parent' => 'warehouseList'
    ],
    'warehouseUserAdd' => [
        'label' => 'Dapat tambah data user gudang',
        'indent' => 3,
        'parent' => 'warehouseUser'
    ],
    'warehouseUserEdit' => [
        'label' => 'Dapat edit data user gudang',
        'indent' => 3,
        'parent' => 'warehouseUser'
    ],
    'warehouseUserDelete' => [
        'label' => 'Dapat Delete data user gudang',
        'indent' => 3,
        'parent' => 'warehouseUser'
    ],


    // =================================================== RULE DATA PEMBELIAN ===================================================
    'purchaseList' => [
        'label' => 'Dapat akses data pembelian',
        'indent' => 1,
        'parent' => 'store'
    ],
    'purchaseAdd' => [
        'label' => 'Dapat tambah data pembelian',
        'indent' => 2,
        'parent' => 'purchaseList'
    ],
    'purchaseDetail' => [
        'label' => 'Dapat akses detail data pembelian',
        'indent' => 2,
        'parent' => 'purchaseList'
    ],
    'purchasePrint' => [
        'label' => 'Dapat print faktur data pembelian',
        'indent' => 2,
        'parent' => 'purchaseList'
    ],
    'purchaseDebtList' => [
        'label' => 'Dapat akses data utang pembelian',
        'indent' => 2,
        'parent' => 'purchaseList'
    ],
    'purchaseDebtPay' => [
        'label' => 'Dapat bayar data utang pembelian',
        'indent' => 3,
        'parent' => 'purchaseDebtList'
    ],
    'purchaseReturList' => [
        'label' => 'Dapat akses retur pembelian',
        'indent' => 2,
        'parent' => 'purchaseList'
    ],
    'purchaseReturAdd' => [
        'label' => 'Dapat tambah retur pembelian',
        'indent' => 3,
        'parent' => 'purchaseReturList'
    ],


    // =================================================== RULE DATA PENJUALAN ===================================================
    'saleList' => [
        'label' => 'Dapat akses data penjualan',
        'indent' => 1,
        'parent' => 'store'
    ],
    'saleAdd' => [
        'label' => 'Dapat tambah data penjualan',
        'indent' => 2,
        'parent' => 'saleList'
    ],
    'salePrint' => [
        'label' => 'Dapat print bukti transaksi data penjualan',
        'indent' => 2,
        'parent' => 'saleList'
    ],
    'saleDetail' => [
        'label' => 'Dapat akses detail data penjualan',
        'indent' => 2,
        'parent' => 'saleList'
    ],
    'saleDebtList' => [
        'label' => 'Dapat akses data piutang penjualan',
        'indent' => 2,
        'parent' => 'saleList'
    ],
    'saleDebtDetail' => [
        'label' => 'Dapat akses detail data piutang penjualan',
        'indent' => 3,
        'parent' => 'saleDebtList'
    ],
    'saleDebtDetailPrint' => [
        'label' => 'Dapat akses print detail data piutang penjualan',
        'indent' => 4,
        'parent' => 'saleDebtDetail'
    ],
    'saleDebtPay' => [
        'label' => 'Dapat bayar data piutang penjualan',
        'indent' => 4,
        'parent' => 'saleDebtDetail'
    ],
    'saleReturList' => [
        'label' => 'Dapat akses retur penjualan',
        'indent' => 2,
        'parent' => 'saleList'
    ],
    'saleReturAdd' => [
        'label' => 'Dapat tambah retur penjualan',
        'indent' => 3,
        'parent' => 'saleReturList'
    ],


    // =================================================== RULE STOCK OPNAME ===================================================
    'stockOpname' => [
        'label' => 'Dapat akses stock opname penyusutan',
        'indent' => 1,
        'parent' => 'store'
    ],
    'stockOpnameFormat' => [
        'label' => 'Dapat donwload format stock opname penyusutan',
        'indent' => 2,
        'parent' => 'stockOpname'
    ],


    // =================================================== RULE LAPORAN ===================================================
    'storeReport' => [
        'label' => 'Dapat akses laporan toko',
        'indent' => 1,
        'parent' => 'store'
    ],
    'storeReportSaleCash' => [
        'label' => 'Dapat akses laporan penjualan tunai',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportSaleCashPrint' => [
        'label' => 'Dapat cetak laporan penjualan tunai',
        'indent' => 3,
        'parent' => 'storeReportSaleCash'
    ],
    'storeReportSaleCashDownload' => [
        'label' => 'Dapat download laporan penjualan tunai',
        'indent' => 3,
        'parent' => 'storeReportSaleCash'
    ],
    'storeReportSaleDebt' => [
        'label' => 'Dapat akses laporan penjualan piutang',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportSaleDebtPrint' => [
        'label' => 'Dapat cetak laporan penjualan piutang',
        'indent' => 3,
        'parent' => 'storeReportSaleDebt'
    ],
    'storeReportSaleDebtDownload' => [
        'label' => 'Dapat download laporan penjualan piutang',
        'indent' => 3,
        'parent' => 'storeReportSaleDebt'
    ],
    'storeReportItemStock' => [
        'label' => 'Dapat akses laporan persediaan barang',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportItemStockPrint' => [
        'label' => 'Dapat cetak laporan persediaan barang',
        'indent' => 3,
        'parent' => 'storeReportItemStock'
    ],
    'storeReportItemStockDownload' => [
        'label' => 'Dapat download laporan persediaan barang',
        'indent' => 3,
        'parent' => 'storeReportItemStock'
    ],
    'storeReportRegion' => [
        'label' => 'Dapat akses laporan penjualan wilayah',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportRegionPrint' => [
        'label' => 'Dapat cetak laporan penjualan wilayah',
        'indent' => 3,
        'parent' => 'storeReportRegion'
    ],
    'storeReportRegionDownload' => [
        'label' => 'Dapat download laporan penjualan wilayah',
        'indent' => 3,
        'parent' => 'storeReportRegion'
    ],
    'storeReportMember' => [
        'label' => 'Dapat akses laporan penjualan anggota dan Non anggota',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportMemberPrint' => [
        'label' => 'Dapat cetak laporan penjualan anggota dan Non anggota',
        'indent' => 3,
        'parent' => 'storeReportMember'
    ],
    'storeReportMemberDownload' => [
        'label' => 'Dapat download laporan penjualan anggota dan Non anggota',
        'indent' => 3,
        'parent' => 'storeReportMember'
    ],
    'storeReportUtang' => [
        'label' => 'Dapat akses laporan rekapitulasi utang',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportUtangAdd' => [
        'label' => 'Dapat tambah transaksi rekapitulasi utang',
        'indent' => 3,
        'parent' => 'storeReportUtang'
    ],
    'storeReportUtangPrint' => [
        'label' => 'Dapat print rekapitulasi utang',
        'indent' => 3,
        'parent' => 'storeReportUtang'
    ],
    'storeReportUtangDownload' => [
        'label' => 'Dapat Download rekapitulasi utang',
        'indent' => 3,
        'parent' => 'storeReportUtang'
    ],
    'storeReportUtangDetail' => [
        'label' => 'Dapat akses buku besar pembantu utang',
        'indent' => 3,
        'parent' => 'storeReportUtang'
    ],
    'storeReportUtangDetailPrint' => [
        'label' => 'Dapat print buku besar pembantu utang',
        'indent' => 4,
        'parent' => 'storeReportUtangDetail'
    ],
    'storeReportUtangDetailDownload' => [
        'label' => 'Dapat Download buku besar pembantu utang',
        'indent' => 4,
        'parent' => 'storeReportUtangDetail'
    ],
    'storeReportPiutang' => [
        'label' => 'Dapat akses laporan rekapitulasi Piutang',
        'indent' => 2,
        'parent' => 'storeReport'
    ],
    'storeReportPiutangAdd' => [
        'label' => 'Dapat tambah transaksi rekapitulasi Piutang',
        'indent' => 3,
        'parent' => 'storeReportPiutang'
    ],
    'storeReportPiutangUpload' => [
        'label' => 'Dapat upload transaksi rekapitulasi Piutang',
        'indent' => 3,
        'parent' => 'storeReportPiutang'
    ],
    'storeReportPiutangPrint' => [
        'label' => 'Dapat print rekapitulasi Piutang',
        'indent' => 3,
        'parent' => 'storeReportPiutang'
    ],
    'storeReportPiutangDownload' => [
        'label' => 'Dapat Download rekapitulasi Piutang',
        'indent' => 3,
        'parent' => 'storeReportPiutang'
    ],
    'storeReportPiutangDetail' => [
        'label' => 'Dapat akses rekapitulasi Piutang Anggota per Wilayah',
        'indent' => 3,
        'parent' => 'storeReportPiutang'
    ],
    'storeReportPiutangDetailPrint' => [
        'label' => 'Dapat print rekapitulasi Piutang Anggota per Wilayah',
        'indent' => 4,
        'parent' => 'storeReportPiutangDetail'
    ],
    'storeReportPiutangDetailDownload' => [
        'label' => 'Dapat Download rekapitulasi Piutang Anggota per Wilayah',
        'indent' => 4,
        'parent' => 'storeReportPiutangDetail'
    ],
    'storeReportPiutangDetailAnggota' => [
        'label' => 'Dapat akses buku besar pembantu piutang anggota',
        'indent' => 4,
        'parent' => 'storeReportPiutangDetail'
    ],
    'storeReportPiutangDetailAnggotaPrint' => [
        'label' => 'Dapat print buku besar pembantu piutang anggota',
        'indent' => 4,
        'parent' => 'storeReportPiutangDetailAnggota'
    ],
    'storeReportPiutangDetailAnggotaDownload' => [
        'label' => 'Dapat Download buku besar pembantu piutang anggota',
        'indent' => 4,
        'parent' => 'storeReportPiutangDetailAnggota'
    ],
];