<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/desa', 'HomeController@getVillage');
Route::get('/getMember', 'HomeController@getMember');
Route::get('/getNoRek/{id}', 'HomeController@getNoRek');
Route::get('/getItem', 'HomeController@getItem');
Route::get('/getItemJual', 'HomeController@getItemJual');

Route::get('/', 'HomeController@index')->name('home');

// ===================================================================================== PROFILE =====================================================================================
Route::group(["prefix" => "profile"], function () {
    Route::get("/", "UserController@profile")->name('profile');
    Route::post("/update", "UserController@profileUpdate")->name('profileUpdate');
    Route::post('/update/member', 'UserController@profileMemberUpdate')->name('profileMemberUpdate');
});

/*
* ======================================================================================== DATA MASTER ========================================================================================
*/
// ------------------------------------------------------------------------------------- PROFILE KOPERASI -------------------------------------------------------------------------------------
Route::get('/koperasi', 'KoperasiController@index')->name('koperasi');
Route::post('/koperasi/update', 'KoperasiController@update')->name('koperasiUpdate');
// ------------------------------------------------------------------------------------- DATA ANGGOTA -------------------------------------------------------------------------------------
Route::group(["prefix" => "anggota"], function () {
    Route::get('/', 'AnggotaController@index')->name('memberList');
    Route::get('/tambah', 'AnggotaController@create')->name('memberAdd');
    Route::post('/simpan', 'AnggotaController@save')->name('memberSave');
    Route::get('/edit/{id}', 'AnggotaController@edit')->name('memberEdit');
    Route::get('/cetak', 'AnggotaController@print')->name('memberPrint');
    Route::get('/download', 'AnggotaController@download')->name('memberDownload');
    Route::get('/detail/{id}', 'AnggotaController@view')->name('memberDetail');
    Route::get('/transaksi/{id}', 'MasterController@memberTransaksi')->name('memberTransaksi');

    Route::get('/hapus/{id}', 'MasterController@memberDelete')->name('memberDelete');
    
    Route::get('/upload', 'MasterController@memberUpload')->name('memberUpload');
    Route::post('/upload/simpan', 'MasterController@memberUploadSave')->name('memberUploadSave');
    Route::get('/promosi/{id}', 'MasterController@memberPromotion')->name('memberPromotion');
    Route::post('/promosi/simpan', 'MasterController@memberPromotionSave')->name('memberPromotionSave');
    Route::get('/reset', 'MasterController@memberReset')->name('memberReset');
    Route::get('/aktivitas/{id}', 'MasterController@memberActivity')->name('memberActivity');
    Route::get('/aktivitas/{id}/print', 'MasterController@memberActivityPrint')->name('memberActivityPrint');
});
// ------------------------------------------------------------------------------------- DATA PENGURUS -------------------------------------------------------------------------------------
Route::group(["prefix" => "pengurus"], function () {
    Route::get('/', 'PengurusController@index')->name('managementList');
    Route::get('/tambah', 'PengurusController@create')->name('managementAdd');
    Route::get('/edit/{id}', 'PengurusController@edit')->name('managementEdit');
    Route::get('/hapus/{id}', 'PengurusController@delete')->name('managementDelete');
    
    Route::post('/simpan', 'MasterController@managementSave')->name('managementSave');
    // ------------------------------------------------------------------------------------- DATA JABATAN PENGURUS -------------------------------------------------------------------------------------
    Route::group(["prefix" => "/jabatan"], function () {
        Route::get('/', 'PosisiController@jabatan')->name('managementPositionList');
        Route::get('/tambah', 'PosisiController@jabatanAdd')->name('managementPositionAdd');
        Route::get('/edit/{id}', 'PosisiController@jabatanEdit')->name('managementPositionEdit');
        Route::get('/hapus/{id}', 'PosisiController@jabatanDelete')->name('managementPositionDelete');
        Route::post('/simpan', 'PosisiController@jabatanSave')->name('managementPositionSave');
    });
});
// ------------------------------------------------------------------------------------- DATA KARYAWAN -------------------------------------------------------------------------------------
Route::group(["prefix" => "karyawan"], function () {
    Route::get('/', 'KaryawanController@index')->name('employeeList');
    Route::get('/tambah', 'KaryawanController@create')->name('employeeAdd');
    Route::get('/edit/{id}', 'KaryawanController@edit')->name('employeeEdit');
    Route::get('/hapus/{id}', 'KaryawanController@delete')->name('employeeDelete');

    Route::post('/simpan', 'MasterController@employeeSave')->name('employeeSave');
    // ------------------------------------------------------------------------------------- DATA POSISI KARYAWAN -------------------------------------------------------------------------------------
    Route::group(["prefix" => "/posisi"], function () {
        Route::get('/', 'PosisiController@index')->name('employeePositionList');
        Route::get('/tambah', 'PosisiController@add')->name('employeePositionAdd');
        Route::get('/edit/{id}', 'PosisiController@edit')->name('employeePositionEdit');
        Route::post('/simpan', 'PosisiController@save')->name('employeePositionSave');
        Route::get('/hapus/{id}', 'PosisiController@delete')->name('employeePositionDelete');
    });
});
// ------------------------------------------------------------------------------------- DATA WILAYAH -------------------------------------------------------------------------------------
Route::group(['prefix' => 'wilayah'], function () {
    Route::get('/', 'WilayahController@index')->name('regionList');
    Route::get('/tambah', 'WilayahController@create')->name('regionAdd');
    Route::get('/edit/{id}', 'WilayahController@edit')->name('regionEdit');
    Route::get('/hapus/{id}', 'WilayahController@delete')->name('regionDelete');
    Route::post('/simpan', 'WilayahController@save')->name('regionSave');
});
// ------------------------------------------------------------------------------------- ASET BARANG -------------------------------------------------------------------------------------
Route::group(['prefix' => 'aset'], function () {
    Route::get('/', 'MasterController@assetList')->name('assetList');
    Route::get('/tambah', 'MasterController@assetAdd')->name('assetAdd');
    Route::get('/edit/{id}', 'MasterController@assetEdit')->name('assetEdit');
    Route::post('/simpan', 'MasterController@assetSave')->name('assetSave');
    Route::get('/hapus/{id}', 'MasterController@assetDelete')->name('assetDelete');
    // ------------------------------------------------------------------------------------- KATEGORI ASET BARANG -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'kategori'], function () {
        Route::get('/', 'MasterController@assetCategoryList')->name('assetCategoryList');
        Route::get('/tambah', 'MasterController@assetCategoryAdd')->name('assetCategoryAdd');
        Route::get('/edit/{id}', 'MasterController@assetCategoryEdit')->name('assetCategoryEdit');
        Route::post('/simpan', 'MasterController@assetCategorySave')->name('assetCategorySave');
        Route::get('/hapus/{id}', 'MasterController@assetCategoryDelete')->name('assetCategoryDelete');
    });
});
// ------------------------------------------------------------------------------------- KONFIGURASI APLIKASI -------------------------------------------------------------------------------------
Route::group(['prefix' => 'konfigurasi'], function () {
    Route::get('/', 'KonfigurasiAplikasiController@index')->name('configApps');
    Route::post('/update', 'KonfigurasiAplikasiController@update')->name('configAppsUpdate');
});
/*
* ======================================================================================== DATA MASTER ========================================================================================
*/





/*
* ======================================================================================== MANAGEMEN USER ========================================================================================
*/
// ------------------------------------------------------------------------------------- DATA USER -------------------------------------------------------------------------------------
Route::group(['prefix' => 'user'], function () {
    Route::get('/', 'UserController@userList')->name('userList');
    Route::get('/tambah', 'UserController@userAdd')->name('userAdd');
    Route::get('/edit/{id}', 'UserController@userEdit')->name('userEdit');
    Route::post('/simpan', 'UserController@userSave')->name('userSave');
    Route::get('/hapus/{id}', 'UserController@userDelete')->name('userDelete');
    // ------------------------------------------------------------------------------------- LEVEL USER -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'level'], function () {
        Route::get('/', 'UserController@levelList')->name('levelList');
        Route::get('/tambah', 'UserController@levelAdd')->name('levelAdd');
        Route::get('/edit/{id}', 'UserController@levelEdit')->name('levelEdit');
        Route::post('/simpan', 'UserController@levelSave')->name('levelSave');
        Route::get('/hapus/{id}', 'UserController@levelDelete')->name('levelDelete');
    });
});
/*
* ======================================================================================== MANAGEMEN USER ========================================================================================
*/





/*
* ======================================================================================== PEMBUKUAN ========================================================================================
*/
// ------------------------------------------------------------------------------------- DATA AKUN -------------------------------------------------------------------------------------
Route::group(['prefix' => 'akun'], function () {
    Route::get('/', 'AccountancyController@accountList')->name('accountList');
    Route::get('/tambah', 'AccountancyController@accountAdd')->name('accountAdd');
    Route::get('/edit/{id}', 'AccountancyController@accountEdit')->name('accountEdit');
    Route::post('/simpan', 'AccountancyController@accountSave')->name('accountSave');
    Route::get('/hapus/{id}', 'AccountancyController@accountDelete')->name('accountDelete');
    // ------------------------------------------------------------------------------------- KONFIGURASI SALDO -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'saldo'], function () {
        Route::get('/', 'AccountancyController@accountConfig')->name('accountConfig');
        Route::post('/simpan', 'AccountancyController@accountConfigSave')->name('accountConfigSave');
        Route::get('/reset', 'AccountancyController@accountConfigReset')->name('accountConfigReset');
    });
    Route::group(['prefix' => 'kelompok'], function () {
        // ------------------------------------------------------------------------------------- KELOMPOK AKUN -------------------------------------------------------------------------------------
        Route::get('/', 'AccountancyController@accountGroupList')->name('accountGroupList');
        Route::get('/tambah', 'AccountancyController@accountGroupAdd')->name('accountGroupAdd');
        Route::get('/edit/{id}', 'AccountancyController@accountGroupEdit')->name('accountGroupEdit');
        Route::post('/simpan', 'AccountancyController@accountGroupSave')->name('accountGroupSave');
        Route::get('/hapus/{id}', 'AccountancyController@accountGroupDelete')->name('accountGroupDelete');
    });
});
// ------------------------------------------------------------------------------------- JURNAL TRANSAKSI -------------------------------------------------------------------------------------
Route::group(['prefix' => 'jurnal'], function () {
    Route::get('/', 'AccountancyController@journalList')->name('journalList');
    Route::get('/tambah', 'AccountancyController@journalAdd')->name('journalAdd');
    Route::get('/edit/{id}', 'AccountancyController@journalEdit')->name('journalEdit');
    Route::post('/simpan', 'AccountancyController@journalSave')->name('journalSave');
    Route::get('/hapus/{id}', 'AccountancyController@journalDelete')->name('journalDelete');
    Route::get('/cetak', 'AccountancyController@journalPrint')->name('journalPrint');
});
// ------------------------------------------------------------------------------------- BUKU BESAR -------------------------------------------------------------------------------------
Route::group(['prefix' => 'bukubesar'], function () {
    Route::get('/', 'AccountancyController@ledger')->name('ledger');
    Route::group(['prefix' => 'detail'], function () {
        Route::get('/', 'AccountancyController@ledgerDetail')->name('ledgerDetail');
        Route::get('/download', 'AccountancyController@ledgerDetailDownload')->name('ledgerDetailDownload');
        Route::get('/cetak', 'AccountancyController@ledgerDetailPrint')->name('ledgerDetailPrint');
    });
});
// ------------------------------------------------------------------------------------- JURNAL PENYESUAIAN -------------------------------------------------------------------------------------
Route::group(['prefix' => 'jurnal/penyesuaian'], function () {
    Route::get('/', 'AccountancyController@adjustingJournalList')->name('adjustingJournalList');
    Route::get('/tambah', 'AccountancyController@adjustingJournalAdd')->name('adjustingJournalAdd');
    Route::get('/edit/{id}', 'AccountancyController@adjustingJournalEdit')->name('adjustingJournalEdit');
    Route::post('/simpan', 'AccountancyController@adjustingJournalSave')->name('adjustingJournalSave');
    Route::get('/hapus/{id}', 'AccountancyController@adjustingJournalDelete')->name('adjustingJournalDelete');
    Route::get('/cetak', 'AccountancyController@adjustingJournalPrint')->name('adjustingJournalPrint');
});
// ------------------------------------------------------------------------------------- NERACA SALDO -------------------------------------------------------------------------------------
Route::group(['prefix' => 'neracasaldo'], function () {
    Route::get('/', 'NeracaSaldoController@index')->name('trialBalance');
    Route::get('/cetak', 'NeracaSaldoController@print')->name('trialBalancePrint');
    Route::get('/download', 'NeracaSaldoController@download')->name('trialBalanceDownload');
});
// ------------------------------------------------------------------------------------- TUTUP BUKU -------------------------------------------------------------------------------------
Route::group(['prefix' => 'tutupbuku'], function () {
    Route::get('/', 'AccountancyController@closeBookList')->name('closeBookList');
    // ------------------------------------------------------------------------------------- TUTUP BUKU BULANAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'bulanan'], function () {
        Route::get('/', 'AccountancyController@closeMonthlyBookList')->name('closeMonthlyBookList');
        Route::get('/tambah', 'AccountancyController@closeMonthlyBookAdd')->name('closeMonthlyBookAdd');
        Route::post('/preview', 'AccountancyController@closeMonthlyBookPreview')->name('closeMonthlyBookPreview');
        Route::post('/confirm', 'AccountancyController@closeMonthlyBookConfirm')->name('closeMonthlyBookConfirm');
        Route::get('/detail/{id}', 'AccountancyController@closeMonthlyBookDetail')->name('closeMonthlyBookDetail');
    });
    // ------------------------------------------------------------------------------------- TUTUP BUKU TAHUNAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'tahunan'], function () {
        Route::get('/', 'AccountancyController@closeYearlyBookList')->name('closeYearlyBookList');
        Route::get('/tambah', 'AccountancyController@closeYearlyBookAdd')->name('closeYearlyBookAdd');
        Route::post('/preview', 'AccountancyController@closeYearlyBookPreview')->name('closeYearlyBookPreview');
        Route::post('/confirm', 'AccountancyController@closeYearlyBookConfirm')->name('closeYearlyBookConfirm');
        Route::get('/detail/{id}', 'AccountancyController@closeYearlyBookDetail')->name('closeYearlyBookDetail');
    });
});
/*
* ======================================================================================== PEMBUKUAN ========================================================================================
*/





/*
* ======================================================================================== LAPORAN ========================================================================================
*/
// ------------------------------------------------------------------------------------- NERACA -------------------------------------------------------------------------------------
Route::group(['prefix' => 'neraca'], function () {
    Route::get('/', 'NeracaController@index')->name('balance');
    Route::get('/cetak', 'NeracaController@print')->name('balancePrint');
    Route::get('/download', 'NeracaController@download')->name('balanceDownload');
    Route::get('/penjelasan', 'NeracaController@penjelasan')->name('balanceDescription');
    Route::get('/penjelasan/print', 'NeracaController@penjelasanPrint')->name('balanceDescriptionPrint');
    Route::get('/penjelasan/download', 'NeracaController@penjelasanDownload')->name('balanceDescriptionDownload');
});
// ------------------------------------------------------------------------------------- PHU -------------------------------------------------------------------------------------
Route::group(['prefix' => 'phu'], function () {
    Route::get('/', 'PhuController@index')->name('phu');
    Route::get('/cetak', 'PhuController@print')->name('phuPrint');
    Route::get('/download', 'PhuController@download')->name('phuDownload');
});
// ------------------------------------------------------------------------------------- SHU -------------------------------------------------------------------------------------
Route::group(['prefix' => 'shu'], function () {
    Route::get('/', 'ReportController@shu')->name('shu');
    Route::get('/cetak', 'ReportController@shuPrint')->name('shuPrint');
    Route::get('/download', 'ReportController@shuDownload')->name('shuDownload');
    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ReportController@shuConfig')->name('shuConfig');
        Route::get('/tambah', 'ReportController@shuConfigAdd')->name('shuConfigAdd');
        Route::get('/edit/{id}', 'ReportController@shuConfigEdit')->name('shuConfigEdit');
        Route::post('/simpan', 'ReportController@shuConfigSave')->name('shuConfigSave');
        Route::get('/hapus/{id}', 'ReportController@shuConfigDelete')->name('shuConfigDelete');
    });
    Route::group(['prefix' => 'anggota'], function () {
        Route::get('/', 'ReportController@shuAnggota')->name('shuAnggota');
        Route::get('/cetak', 'ReportController@shuAnggotaPrint')->name('shuAnggotaPrint');
        Route::get('/download', 'ReportController@shuAnggotaDownload')->name('shuAnggotaDownload');
    });
});
// ------------------------------------------------------------------------------------- ARUS KAS -------------------------------------------------------------------------------------
Route::group(['prefix' => 'aruskas'], function () {
    Route::get('/', 'ReportController@cashflow')->name('cashflow');
    Route::get('/cetak', 'ReportController@cashflowPrint')->name('cashflowPrint');
    Route::get('/download', 'ReportController@cashflowDownload')->name('cashflowDownload');
});
// ------------------------------------------------------------------------------------- ARUS KAS -------------------------------------------------------------------------------------
Route::group(['prefix' => 'perubahanmodal'], function () {
    Route::get('/', 'ReportController@ekuitas')->name('ekuitas');
    Route::get('/cetak', 'ReportController@ekuitasPrint')->name('ekuitasPrint');
    Route::get('/download', 'ReportController@ekuitasDownload')->name('ekuitasDownload');
});

Route::group(['prefix' => 'laporan/harian'], function () {
    Route::get('/', 'ReportController@laporanHarian')->name('laporanHarian');
    Route::get('/download', 'ReportController@laporanHarianDownload')->name('laporanHarianDownload');
    Route::get('/print', 'ReportController@laporanHarianPrint')->name('laporanHarianPrint');
    Route::get('/cash', 'ReportController@laporanKasBank')->name('laporanKasBank');
    Route::get('/cash/print', 'ReportController@laporanKasBankPrint')->name('laporanKasBankPrint');
    Route::get('/cash/download', 'ReportController@laporanKasBankDownload')->name('laporanKasBankDownload');
});
/*
* ======================================================================================== LAPORAN ========================================================================================
*/





/*
* ======================================================================================== SIMPANAN ========================================================================================
*/
Route::group(['prefix' => 'simpanan'], function () {
    // ------------------------------------------------------------------------------------- DATA SIMPANAN -------------------------------------------------------------------------------------
    Route::get('/', 'DepositController@depositList')->name('depositList');
    Route::get('/tambah', 'DepositController@depositAdd')->name('depositAdd');
    Route::post('/simpan', 'DepositController@depositSave')->name('depositSave');
    Route::get('/cetak', 'DepositController@depositPrintAll')->name('depositPrintAll');
    Route::get('/download', 'DepositController@depositDownload')->name('depositDownload');
    Route::get('/hapus/{id}', 'DepositController@depositDelete')->name('depositDelete');
    Route::post('/hapus/{id}/confirm', 'DepositController@depositDeleteConfirm')->name('depositDeleteConfirm');
    Route::get('/upload', 'DepositController@depositUpload')->name('depositUpload');
    Route::post('/upload/simpan', 'DepositController@depositUploadSave')->name('depositUploadSave');
    // ------------------------------------------------------------------------------------- DETAIL SIMPANAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'detail'], function () {
        Route::get('/{id}', 'DepositController@depositDetail')->name('depositDetail');
        Route::get('/{id}/tambah', 'DepositController@depositDetailAdd')->name('depositDetailAdd');
        Route::post('/{id}/preview', 'DepositController@depositDetailPreview')->name('depositDetailPreview');
        Route::post('/{id}/simpan', 'DepositController@depositDetailSave')->name('depositDetailSave');
        Route::get('/{id}/print', 'DepositController@depositDetailPrint')->name('depositDetailPrint');
        Route::get('/{id}/download', 'DepositController@depositDetailDownload')->name('depositDetailDownload');
    });
    // ------------------------------------------------------------------------------------- BUKU TABUNGAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'buku'], function () {
        Route::get('/{id}', 'DepositController@depositBook')->name('depositBook');
        Route::get('/{id}/reset', 'DepositController@depositBookResetAll')->name('depositBookResetAll');
        Route::get('/{deposit_id}/reset/{id}', 'DepositController@depositBookReset')->name('depositBookReset');
        Route::get('/{id}/print', 'DepositController@depositBookPrint')->name('depositBookPrint');
        Route::get('/{id}/print/confirm', 'DepositController@depositBookPrintConfirm')->name('depositBookPrintConfirm');
    });
    // ------------------------------------------------------------------------------------- JENIS SIMPANAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'jenis'], function () {
        Route::get('/', 'SimpananJenisController@index')->name('depositTypeList');
        Route::get('/tambah', 'SimpananJenisController@create')->name('depositTypeAdd');
        Route::get('/edit/{id}', 'SimpananJenisController@edit')->name('depositTypeEdit');
        Route::get('/hapus/{id}', 'SimpananJenisController@delete')->name('depositTypeDelete');
        
        Route::post('/simpan', 'DepositController@depositTypeSave')->name('depositTypeSave');
    });
    // ------------------------------------------------------------------------------------- DATA TRANSAKSI -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'transaksi'], function () {
        Route::get('/', 'DepositController@depositTransactionList')->name('depositTransactionList');
        Route::get('/download', 'DepositController@depositTransactionDownload')->name('depositTransactionDownload');
        Route::get('/cetak', 'DepositController@depositTransactionPrintAll')->name('depositTransactionPrintAll');
        Route::get('/cetak/{id}', 'DepositController@depositTransactionPrint')->name('depositTransactionPrint');
        Route::group(['prefix' => 'upload'], function () {
            Route::get('/', 'DepositController@depositTransactionUpload')->name('depositTransactionUpload');
            Route::post('save', 'DepositController@depositTransactionUploadSave')->name('depositTransactionUploadSave');
        });
    });
    // ------------------------------------------------------------------------------------- TAGIHAN -------------------------------------------------------------------------------------
    // Route::group(['prefix' => 'tagihan'], function () {
    //     Route::get('/', 'DepositController@depositBillList')->name('depositBillList');
    //     Route::get('/download', 'DepositController@depositBillDownload')->name('depositBillDownload');
    //     Route::post('/upload', 'DepositController@depositBillUpload')->name('depositBillUpload');
    // });
    // ------------------------------------------------------------------------------------- LAPORAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'laporan'], function () {
        Route::get('/', 'DepositController@laporanSimpanan')->name('laporanSimpanan');

        Route::group(['prefix' => 'simpanan'], function () {
            Route::get('/', 'DepositController@simpananAnggota')->name('simpananAnggota');
            Route::get('/download', 'DepositController@simpananAnggotaDownload')->name('simpananAnggotaDownload');
            Route::get('/cetak', 'DepositController@simpananAnggotaPrint')->name('simpananAnggotaPrint');
        });
        Route::group(['prefix' => 'simpanan/detail'], function () {
            Route::get('/', 'DepositController@simpananAnggotaDetail')->name('simpananAnggotaDetail');
            Route::get('/download', 'DepositController@simpananAnggotaDetailDownload')->name('simpananAnggotaDetailDownload');
            Route::get('/cetak', 'DepositController@simpananAnggotaDetailPrint')->name('simpananAnggotaDetailPrint');
        });
        Route::group(['prefix' => 'rekapitulasi'], function () {
            Route::get('/', 'DepositController@rekapitulasiSimpanan')->name('rekapitulasiSimpanan');
            Route::get('/download', 'DepositController@rekapitulasiSimpananDownload')->name('rekapitulasiSimpananDownload');
            Route::get('/cetak', 'DepositController@rekapitulasiSimpananPrint')->name('rekapitulasiSimpananPrint');
        });
        Route::group(['prefix' => 'rekapitulasi/detail'], function () {
            Route::get('/', 'DepositController@rekapitulasiSimpananDetail')->name('rekapitulasiSimpananDetail');
            Route::get('/download', 'DepositController@rekapitulasiSimpananDetailDownload')->name('rekapitulasiSimpananDetailDownload');
            Route::get('/cetak', 'DepositController@rekapitulasiSimpananDetailPrint')->name('rekapitulasiSimpananDetailPrint');
        });
    });
});
/*
* ======================================================================================== SIMPANAN ========================================================================================
*/





/*
* ======================================================================================== TOKO ========================================================================================
*/
Route::group(['prefix' => 'toko'], function () {
    Route::get('/', 'StoreController@index')->name('store');
    // ------------------------------------------------------------------------------------- DATA BARANG -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'barang'], function () {
        Route::get('/', 'StoreController@itemList')->name('itemList');
        Route::get('/tambah', 'StoreController@itemAdd')->name('itemAdd');
        Route::get('/edit/{id}', 'StoreController@itemEdit')->name('itemEdit');
        Route::post('/simpan', 'StoreController@itemSave')->name('itemSave');
        Route::get('/hapus/{id}', 'StoreController@itemDelete')->name('itemDelete');
        Route::get('/detail/{id}', 'StoreController@itemDetail')->name('itemDetail');
        Route::get('/upload', 'StoreController@itemUpload')->name('itemUpload');
        Route::get('/upload/format', 'StoreController@itemUploadFormat')->name('itemUploadFormat');
        Route::post('/upload/simpan', 'StoreController@itemUploadSave')->name('itemUploadSave');
        Route::get('/distribution/{id}', 'StoreController@itemDistribution')->name('itemDistribution');
        Route::post('/distribution/simpan', 'StoreController@itemDistributionSave')->name('itemDistributionSave');
        Route::group(['prefix' => 'kartu'], function () {
            Route::get('/{id}', 'StoreController@itemCard')->name('itemCard');
            Route::get('/{id}/print', 'StoreController@itemCardPrint')->name('itemCardPrint');
            Route::get('/{id}/download', 'StoreController@itemCardDownload')->name('itemCardDownload');
        });
    });
    // ------------------------------------------------------------------------------------- SUPLIER -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'suplier'], function () {
        Route::get('/', 'StoreController@suplierList')->name('suplierList');
        Route::get('/tambah', 'StoreController@suplierAdd')->name('suplierAdd');
        Route::get('/edit/{id}', 'StoreController@suplierEdit')->name('suplierEdit');
        Route::post('/simpan', 'StoreController@suplierSave')->name('suplierSave');
        Route::get('/hapus/{id}', 'StoreController@suplierDelete')->name('suplierDelete');
    });
    // ------------------------------------------------------------------------------------- GUDANG -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'gudang'], function () {
        Route::get('/', 'StoreController@warehouseList')->name('warehouseList');
        Route::get('/tambah', 'StoreController@warehouseAdd')->name('warehouseAdd');
        Route::get('/edit/{id}', 'StoreController@warehouseEdit')->name('warehouseEdit');
        Route::post('/simpan', 'StoreController@warehouseSave')->name('warehouseSave');
        Route::get('/hapus/{id}', 'StoreController@warehouseDelete')->name('warehouseDelete');
        // ------------------------------------------------------------------------------------- USER GUDANG -------------------------------------------------------------------------------------
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', 'StoreController@warehouseUser')->name('warehouseUser');
            Route::get('/tambah', 'StoreController@warehouseUserAdd')->name('warehouseUserAdd');
            Route::get('/edit/{id}', 'StoreController@warehouseUserEdit')->name('warehouseUserEdit');
            Route::post('/simpan', 'StoreController@warehouseUserSave')->name('warehouseUserSave');
            Route::get('/hapus/{id}', 'StoreController@warehouseUserDelete')->name('warehouseUserDelete');
        });
    });
    // ------------------------------------------------------------------------------------- PEMBELIAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'pembelian'], function () {
        Route::get('/', 'StoreController@purchaseList')->name('purchaseList');
        Route::get('/tambah', 'StoreController@purchaseAdd')->name('purchaseAdd');
        Route::post('/confirm', 'StoreController@purchaseConfirm')->name('purchaseConfirm');
        Route::post('/simpan', 'StoreController@purchaseSave')->name('purchaseSave');
        Route::get('/detail/{id}', 'StoreController@purchaseDetail')->name('purchaseDetail');
        Route::get('/cetak/{id}', 'StoreController@purchasePrint')->name('purchasePrint');
        // ------------------------------------------------------------------------------------- PEMBELIAN UTANG -------------------------------------------------------------------------------------
        Route::group(['prefix' => 'utang'], function () {
            Route::get('/', 'StoreController@purchaseDebtList')->name('purchaseDebtList');
            Route::get('/{id}', 'StoreController@purchaseDebtPay')->name('purchaseDebtPay');
            Route::post('/confirm', 'StoreController@purchaseDebtConfirm')->name('purchaseDebtConfirm');
            Route::post('/simpan', 'StoreController@purchaseDebtSave')->name('purchaseDebtSave');
        });
        // ------------------------------------------------------------------------------------- PEMBELIAN RETUR -------------------------------------------------------------------------------------
        Route::group(['prefix' => 'retur'], function () {
            Route::get('/', 'StoreController@purchaseReturList')->name('purchaseReturList');
            Route::get('/add/{id}', 'StoreController@purchaseReturAdd')->name('purchaseReturAdd');
            Route::post('/confirm', 'StoreController@purchaseReturConfirm')->name('purchaseReturConfirm');
            Route::post('/save', 'StoreController@purchaseReturSave')->name('purchaseReturSave');
        });
    });
    // ------------------------------------------------------------------------------------- PENJUALAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'penjualan'], function () {
        Route::get('', 'StoreController@saleList')->name('saleList');
        Route::get('/tambah', 'StoreController@saleAdd')->name('saleAdd');
        Route::post('/confirm', 'StoreController@saleConfirm')->name('saleConfirm');
        Route::post('/simpan', 'StoreController@saleSave')->name('saleSave');
        Route::get('/cetak/{id}', 'StoreController@salePrint')->name('salePrint');
        Route::get('detail/{id}', 'StoreController@saleDetail')->name('saleDetail');
        // ------------------------------------------------------------------------------------- PENJUALAN PIUTANG -------------------------------------------------------------------------------------
        Route::group(['prefix' => 'piutang'], function () {
            Route::get('/', 'StoreController@saleDebtList')->name('saleDebtList');
            Route::get('/{id}', 'StoreController@saleDebtDetail')->name('saleDebtDetail');
            Route::get('/{debt_id}/print/{id}', 'StoreController@saleDebtDetailPrint')->name('saleDebtDetailPrint');
            Route::get('/{id}/pay', 'StoreController@saleDebtPay')->name('saleDebtPay');
            Route::post('/confirm', 'StoreController@saleDebtConfirm')->name('saleDebtConfirm');
            Route::post('/simpan', 'StoreController@saleDebtSave')->name('saleDebtSave');
        });
        // ------------------------------------------------------------------------------------- PENJUALAN RETUR -------------------------------------------------------------------------------------
        Route::group(['prefix' => 'retur'], function () {
            Route::get('/', 'StoreController@saleReturList')->name('saleReturList');
            Route::get('/add/{id}', 'StoreController@saleReturAdd')->name('saleReturAdd');
            Route::post('/confirm', 'StoreController@saleReturConfirm')->name('saleReturConfirm');
            Route::post('/save', 'StoreController@saleReturSave')->name('saleReturSave');
        });
    });
    // ------------------------------------------------------------------------------------- STOCK OPNAME -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'stockopname'], function () {
        Route::get('/', 'StoreController@stockOpname')->name('stockOpname');
        Route::get('/format', 'StoreController@stockOpnameFormat')->name('stockOpnameFormat');
        Route::post('/save', 'StoreController@stockOpnameSave')->name('stockOpnameSave');
    });
    // ------------------------------------------------------------------------------------- LAPORAN -------------------------------------------------------------------------------------
    Route::group(['prefix' => 'laporan'], function () {
        Route::get('/', 'StoreController@report')->name('storeReport');
        Route::group(['prefix' => 'tunai'], function () {
            Route::get('/', 'StoreController@reportSaleCash')->name('storeReportSaleCash');
            Route::get('/print', 'StoreController@reportSaleCashPrint')->name('storeReportSaleCashPrint');
            Route::get('/download', 'StoreController@reportSaleCashDownload')->name('storeReportSaleCashDownload');
        });
        Route::group(['prefix' => 'piutang'], function () {
            Route::get('/', 'StoreController@reportSaleDebt')->name('storeReportSaleDebt');
            Route::get('/print', 'StoreController@reportSaleDebtPrint')->name('storeReportSaleDebtPrint');
            Route::get('/download', 'StoreController@reportSaleDebtDownload')->name('storeReportSaleDebtDownload');
        });
        Route::group(['prefix' => 'persediaan'], function () {
            Route::get('/', 'StoreController@reportItemStock')->name('storeReportItemStock');
            Route::get('/print', 'StoreController@reportItemStockPrint')->name('storeReportItemStockPrint');
            Route::get('/download', 'StoreController@reportItemStockDownload')->name('storeReportItemStockDownload');
        });
        Route::group(['prefix' => 'wilayah'], function () {
            Route::get('/', 'StoreController@reportRegion')->name('storeReportRegion');
            Route::get('/print', 'StoreController@reportRegionPrint')->name('storeReportRegionPrint');
            Route::get('/download', 'StoreController@reportRegionDownload')->name('storeReportRegionDownload');
        });
        Route::group(['prefix' => 'anggota'], function () {
            Route::get('/', 'StoreController@reportMember')->name('storeReportMember');
            Route::get('/print', 'StoreController@reportMemberPrint')->name('storeReportMemberPrint');
            Route::get('/download', 'StoreController@reportMemberDownload')->name('storeReportMemberDownload');
        });
        Route::group(['prefix' => 'rekap/utang'], function () {
            Route::get('/', 'StoreController@reportUtang')->name('storeReportUtang');
            Route::get('/add', 'StoreController@reportUtangAdd')->name('storeReportUtangAdd');
            Route::post('/save', 'StoreController@reportUtangSave')->name('storeReportUtangSave');
            Route::get('/print', 'StoreController@reportUtangPrint')->name('storeReportUtangPrint');
            Route::get('/download', 'StoreController@reportUtangDownload')->name('storeReportUtangDownload');
            Route::get('/detail/{id}', 'StoreController@reportUtangDetail')->name('storeReportUtangDetail');
            Route::get('/detail/{id}/print', 'StoreController@reportUtangDetailPrint')->name('storeReportUtangDetailPrint');
            Route::get('/detail/{id}/download', 'StoreController@reportUtangDetailDownload')->name('storeReportUtangDetailDownload');
        });
        Route::group(['prefix' => 'rekap/piutang'], function () {
            Route::get('/', 'StoreController@reportPiutang')->name('storeReportPiutang');
            Route::get('/add', 'StoreController@reportPiutangAdd')->name('storeReportPiutangAdd');
            Route::post('/save', 'StoreController@reportPiutangSave')->name('storeReportPiutangSave');
            Route::get('/upload', 'StoreController@reportPiutangUpload')->name('storeReportPiutangUpload');
            Route::post('/upload/simpan', 'StoreController@reportPiutangUploadSave')->name('storeReportPiutangUploadSave');
            Route::get('/print', 'StoreController@reportPiutangPrint')->name('storeReportPiutangPrint');
            Route::get('/download', 'StoreController@reportPiutangDownload')->name('storeReportPiutangDownload');
            Route::get('/detail', 'StoreController@reportPiutangDetail')->name('storeReportPiutangDetail');
            Route::get('/detail/print', 'StoreController@reportPiutangDetailPrint')->name('storeReportPiutangDetailPrint');
            Route::get('/detail/download', 'StoreController@reportPiutangDetailDownload')->name('storeReportPiutangDetailDownload');
            Route::get('/detail/{id}', 'StoreController@reportPiutangDetailAnggota')->name('storeReportPiutangDetailAnggota');
            Route::get('/detail/{id}/print', 'StoreController@reportPiutangDetailAnggotaPrint')->name('storeReportPiutangDetailAnggotaPrint');
            Route::get('/detail/{id}/download', 'StoreController@reportPiutangDetailAnggotaDownload')->name('storeReportPiutangDetailAnggotaDownload');
        });
    });
});
/*
* ======================================================================================== TOKO ========================================================================================
*/





/*
* ======================================================================================== RESET APLIKASI ========================================================================================
*/
Route::group(['prefix' => 'reset'], function () {
    Route::get('/', 'ResetController@index')->name('reset');
    Route::get('/toko', 'ResetController@toko')->name('resetToko');
    Route::get('/simpanan', 'ResetController@simpanan')->name('resetSimpanan');
});
/*
* ======================================================================================== RESET APLIKASI ========================================================================================
*/

Route::get('/cache/clear', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('optimize:clear');
    return 'DONE'; //Return anything
});