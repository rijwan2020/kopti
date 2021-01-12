<?php

namespace App\Http\Controllers;

use App\Model\AdjustingJournal;
use App\Model\AdjustingJournalDetail;
use App\Model\Deposit;
use App\Model\DepositBill;
use App\Model\DepositBillUpload;
use App\Model\DepositBook;
use App\Model\DepositTransaction;
use App\Model\DepositTransactionUpload;
use App\Model\DepositType;
use App\Model\DepositUpload;
use App\Model\Journal;
use App\Model\JournalDetail;
use App\Model\StoreItem;
use App\Model\StoreItemCard;
use App\Model\StoreItemDetail;
use App\Model\StoreItemUpload;
use App\Model\StorePurchase;
use App\Model\StorePurchaseDebt;
use App\Model\StorePurchaseDebtHistory;
use App\Model\StorePurchaseDetail;
use App\Model\StorePurchaseRetur;
use App\Model\StorePurchaseTransaction;
use App\Model\StoreSale;
use App\Model\StoreSaleDebt;
use App\Model\StoreSaleDebtHistori;
use App\Model\StoreSaleDebtHistoryUpload;
use App\Model\StoreSaleDetail;
use App\Model\StoreSaleRetur;
use App\Model\StoreSaleTransaction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ResetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Reset Aplikasi');
    }

    public function index()
    {
        $data['active_menu'] = 'reset';
        $data['breadcrumb'] = [
            'Reset Aplikasi' => url()->current(),
        ];
        return view('reset', compact('data'));
    }
    public function toko()
    {
        $jurnaltransaksi = Journal::where('unit', 2);
        JournalDetail::whereIn('journal_id', $jurnaltransaksi->get('id')->toArray())->delete();
        $jurnaltransaksi->delete();
        $jurnalpenyesuaian = AdjustingJournal::where('unit', 2);
        AdjustingJournalDetail::whereIn('adjusting_journal_id', $jurnalpenyesuaian->get('id')->toArray())->delete();
        $jurnalpenyesuaian->delete();
        StoreItem::truncate();
        StoreItemCard::truncate();
        StoreItemDetail::truncate();
        StoreItemUpload::truncate();

        StorePurchase::truncate();
        StorePurchaseDetail::truncate();
        StorePurchaseDebt::truncate();
        StorePurchaseDebtHistory::truncate();
        StorePurchaseRetur::truncate();
        StorePurchaseTransaction::truncate();

        StoreSale::truncate();
        StoreSaleDebt::truncate();
        StoreSaleDebtHistori::truncate();
        StoreSaleDebtHistoryUpload::truncate();
        StoreSaleDetail::truncate();
        StoreSaleRetur::truncate();
        StoreSaleTransaction::truncate();

        return back()->with(['success' => 'Data Toko berhasil direset.']);
    }

    public function simpanan()
    {
        $jurnaltransaksi = Journal::where('unit', 1);
        JournalDetail::whereIn('journal_id', $jurnaltransaksi->get('id')->toArray())->delete();
        $jurnaltransaksi->delete();
        $jurnalpenyesuaian = AdjustingJournal::where('unit', 1);
        AdjustingJournalDetail::whereIn('adjusting_journal_id', $jurnalpenyesuaian->get('id')->toArray())->delete();
        $jurnalpenyesuaian->delete();
        Deposit::truncate();
        DepositBill::truncate();
        DepositBillUpload::truncate();
        DepositBook::truncate();
        DepositTransaction::truncate();
        DepositTransactionUpload::truncate();
        DepositUpload::truncate();
        DB::table('deposit_types')->update(['next_code' => 1]);
        return back()->with(['success' => 'Data Simpanan berhasil direset.']);
    }
}