<?php
return [
    'accountancy' => [
        'label' => 'MENU PEMBUKUAN',
        'indent' => 0,
        'parent' => ''
    ],

    // =================================================== RULE DATA AKUN ===================================================
    'accountList' => [
        'label' => 'Dapat akses data akun',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'accountAdd' => [
        'label' => 'Dapat tambah data akun',
        'indent' => 2,
        'parent' => 'accountList'
    ],
    'accountEdit' => [
        'label' => 'Dapat edit data akun',
        'indent' => 2,
        'parent' => 'accountList'
    ],
    'accountDelete' => [
        'label' => 'Dapat hapus data akun',
        'indent' => 2,
        'parent' => 'accountList'
    ],
    'accountConfig' => [
        'label' => 'Dapat set konfigurasi saldo awal',
        'indent' => 2,
        'parent' => 'accountList'
    ],
    'accountConfigReset' => [
        'label' => 'Dapat reset konfigurasi saldo awal',
        'indent' => 3,
        'parent' => 'accountConfig'
    ],
    'accountGroupList' => [
        'label' => 'Dapat akses data kelompok akun',
        'indent' => 2,
        'parent' => 'accountList'
    ],
    'accountGroupAdd' => [
        'label' => 'Dapat tambah data kelompok akun',
        'indent' => 3,
        'parent' => 'accountGroupList'
    ],
    'accountGroupEdit' => [
        'label' => 'Dapat edit data kelompok akun',
        'indent' => 3,
        'parent' => 'accountGroupList'
    ],
    'accountGroupDelete' => [
        'label' => 'Dapat hapus data kelompok akun',
        'indent' => 3,
        'parent' => 'accountGroupList'
    ],

    // =================================================== RULE JURNAL TRANSAKSI ===================================================
    'journalList' => [
        'label' => 'Dapat akses data jurnal transaksi',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'journalAdd' => [
        'label' => 'Dapat tambah data jurnal transaksi',
        'indent' => 2,
        'parent' => 'journalList'
    ],
    'journalEdit' => [
        'label' => 'Dapat edit data jurnal transaksi',
        'indent' => 2,
        'parent' => 'journalList'
    ],
    'journalDelete' => [
        'label' => 'Dapat delete data jurnal transaksi',
        'indent' => 2,
        'parent' => 'journalList'
    ],
    'journalPrint' => [
        'label' => 'Dapat print data jurnal transaksi',
        'indent' => 2,
        'parent' => 'journalList'
    ],

    // =================================================== RULE BUKU BESAR ===================================================
    'ledger' => [
        'label' => 'Dapat akses data buku besar',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'ledgerDetail' => [
        'label' => 'Dapat akses detail buku besar',
        'indent' => 2,
        'parent' => 'ledger'
    ],
    'ledgerDetailDownload' => [
        'label' => 'Dapat download detail buku besar',
        'indent' => 2,
        'parent' => 'ledger'
    ],
    'ledgerDetailPrint' => [
        'label' => 'Dapat print detail buku besar',
        'indent' => 2,
        'parent' => 'ledger'
    ],

    // =================================================== RULE JURNAL PENYESUAIAN ===================================================
    'adjustingJournalList' => [
        'label' => 'Dapat akses data jurnal penyesuaian',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'adjustingJournalAdd' => [
        'label' => 'Dapat tambah data jurnal penyesuaian',
        'indent' => 2,
        'parent' => 'adjustingJournalList'
    ],
    'adjustingJournalEdit' => [
        'label' => 'Dapat edit data jurnal penyesuaian',
        'indent' => 2,
        'parent' => 'adjustingJournalList'
    ],
    'adjustingJournalDelete' => [
        'label' => 'Dapat delete data jurnal penyesuaian',
        'indent' => 2,
        'parent' => 'adjustingJournalList'
    ],
    'adjustingJournalPrint' => [
        'label' => 'Dapat print data jurnal penyesuaian',
        'indent' => 2,
        'parent' => 'adjustingJournalList'
    ],

    // =================================================== RULE NERACA SALDO ===================================================
    'trialBalance' => [
        'label' => 'Dapat akses neraca saldo',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'trialBalanceDownload' => [
        'label' => 'Dapat download neraca saldo',
        'indent' => 2,
        'parent' => 'trialBalance'
    ],
    'trialBalancePrint' => [
        'label' => 'Dapat print neraca saldo',
        'indent' => 2,
        'parent' => 'trialBalance'
    ],

    // =================================================== RULE TUTUP BUKU ===================================================
    'closeBookList' => [
        'label' => 'Dapat akses data tutup buku',
        'indent' => 1,
        'parent' => 'accountancy'
    ],
    'closeMonthlyBookList' => [
        'label' => 'Dapat akses data tutup buku bulanan',
        'indent' => 2,
        'parent' => 'closeBookList'
    ],
    'closeMonthlyBookAdd' => [
        'label' => 'Dapat tambah data tutup buku bulanan',
        'indent' => 3,
        'parent' => 'closeMonthlyBookList'
    ],
    'closeMonthlyBookDetail' => [
        'label' => 'Dapat akses detail tutup buku bulanan',
        'indent' => 3,
        'parent' => 'closeMonthlyBookList'
    ],
    'closeYearlyBookList' => [
        'label' => 'Dapat akses data tutup buku tahunan',
        'indent' => 2,
        'parent' => 'closeBookList'
    ],
    'closeYearlyBookAdd' => [
        'label' => 'Dapat tambah data tutup buku tahunan',
        'indent' => 3,
        'parent' => 'closeYearlyBookList'
    ],
    'closeYearlyBookDetail' => [
        'label' => 'Dapat akses detail tutup buku tahunan',
        'indent' => 3,
        'parent' => 'closeYearlyBookList'
    ],
];