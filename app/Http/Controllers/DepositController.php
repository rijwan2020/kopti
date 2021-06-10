<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\DepositClass;
use App\Classes\MasterClass;
use App\Exports\DepositBillExport;
use App\Exports\DepositDetailExport;
use App\Exports\DepositExport;
use App\Exports\DepositReportDetailExport;
use App\Exports\DepositReportExport;
use App\Exports\DepositReportMemberDetailExport;
use App\Exports\DepositReportMemberExport;
use App\Exports\DepositTransactionExport;
use App\Exports\DepositTransactionFormatExport;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\DepositTransactionRequest;
use App\Http\Requests\DepositTypeRequest;
use App\Imports\DepositBillImport;
use App\Imports\DepositImport;
use App\Imports\DepositTransactionImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class DepositController extends Controller
{
    private $deposit, $accountancy, $master;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Unit Simpanan');

        $this->deposit = new DepositClass();
        $this->accountancy = new AccountancyClass();
        $this->master = new MasterClass();
    }



    /*
    * ========================================================================================== START SIMPANAN ==========================================================================================
    */
    /*
    public function depositList()
    {
        $data['limit'] = $_GET['limit'] ?? 20;
        $data['q'] = $_GET['q'] ?? '';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        if (auth()->user()->isMember()) {
            $data['member_id'] = auth()->user()->member->id;
        }
        $data['data'] = $this->deposit->depositList($data, $data['limit']);
        $data['type'] = $this->deposit->depositTypeList();
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => url()->current()
        ];
        return view('deposit.deposit-list', compact('data'));
    }
    public function depositAdd()
    {
        $data['type'] = $this->deposit->depositTypeList();
        $type = $this->deposit->depositTypeGet(3);
        $data['data'] = [
            'account_number' => $type->code . '-' . str_pad($type->next_code, 9, 0, STR_PAD_LEFT),
            'deposit_type_id' => 3
        ];
        $data['account'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['mode'] = 'add';
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Tambah Data' => url()->current()
        ];
        return view('deposit.deposit-form', compact('data'));
    }
    public function depositPrintAll()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        $data['data'] = $this->deposit->depositList($data);
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.deposit-print-all', compact('data'));
    }
    public function depositDownload()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        $data['data'] = $this->deposit->depositList($data);
        $export['data'] = [];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $export['data'][$key]['no'] = $i;
            $export['data'][$key]['kode'] = $value->member->code;
            $export['data'][$key]['nama'] = $value->member->name;
            $export['data'][$key]['no_rek'] = $value->account_number;
            $export['data'][$key]['wilayah'] = $value->region->name;
            $export['data'][$key]['jenis'] = $value->type->name;
            $export['data'][$key]['tgl_reg'] = $value->registration_date;
            $export['data'][$key]['last_trx'] = $value->last_transaction;
            $export['data'][$key]['saldo'] = 'Rp' . number_format($value->balance, 2, ',', '.');
        }
        $export['total_row'] = $data['data']->count();
        $export['saldo'] = $data['data']->sum('balance');
        return Excel::download(new DepositExport($export), 'Data Simpanan.xlsx');
    }
    
    public function depositDelete($id)
    {
        $data['data'] = $this->deposit->depositGet($id);
        if ($data['data'] == false) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        if ($data['data']->deleted_by != 0) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Hapus' => url()->current()
        ];
        return view('deposit.deposit-delete', compact('data'));
    }
    
    public function depositUpload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->deposit->depositUploadList($data, $data['limit']);
        $data['type'] = $this->deposit->depositTypeList();
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Upload' => url()->current()
        ];
        if (isset($_GET['confirm'])) {
            $this->deposit->depositUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('depositUpload')->with(['info' => 'Upload data simpanan dibatalkan.']);
            } else {
                return redirect()->route('depositList')->with(['success' => 'Upload data simpanan berhasil.']);
            }
        }
        return view('deposit.deposit-upload', compact('data'));
    }
    */
    public function depositSave(DepositRequest $request)
    {
        $data = $request->validated();
        if ($request->mode == 'add') {
            $data['obligatory_balance'] = config('config_apps.besar_sw');
            $data['beginning_balance'] = str_replace(',', '', $data['beginning_balance']);
            if ($data['deposit_type_id'] == 1) {
                $data['principal_balance'] = str_replace(',', '', $data['principal_balance']);
                $data['obligatory_balance'] = 0;
            } elseif ($data['deposit_type_id'] == 2) {
                $data['obligatory_balance'] = str_replace(',', '', $data['obligatory_balance']);
                $data['principal_balance'] = 0;
            } else {
                $data['principal_balance'] = 0;
                $data['obligatory_balance'] = 0;
            }
            $bill = [
                'principal_balance' => $data['principal_balance'],
                'obligatory_balance' => $data['obligatory_balance']
            ];
            unset($data['obligatory_balance'], $data['principal_balance']);
            if (!$this->deposit->depositSave($data)) {
                return back()->with(['warning' => $this->deposit->error])->withInput();
            }

            if ($data['deposit_type_id'] == 1 || $data['deposit_type_id'] == 2) {
                $bill['deposit_id'] = $this->deposit->last_deposit_id;
                $this->deposit->depositBillSave($bill);
            }

            $message = 'Data simpanam berhasil ditambahkan.';
        }
        return redirect()->route('depositList')->with(['success' => $message]);
    }
    
    public function depositDeleteConfirm(Request $request)
    {
        $data = $request->validate([
            'deposit_id' => 'required',
            'transaction_date' => 'required',
            'type' => 'required',
            'account' => 'nullable',
            'note' => 'nullable',
        ]);
        $deposit = $this->deposit->depositGet($data['deposit_id']);
        if ($data['transaction_date'] < date('Y-m-d', strtotime($deposit->last_transaction))) {
            return back()->with(['warning' => 'Tanggal penutupan atau pengahapusan simpanan tidak boleh lebih kecil dari tanggal transaksi terkahir, yaitu ' . date('Y-m-d', strtotime($deposit->last_transaction))])->withInput();
        }
        $data['transaction_date'] = $data['transaction_date'] . date(' H:i:s');
        $transaction = [
            'deposit_id' => $data['deposit_id'],
            'transaction_date' => $data['transaction_date'],
            'type' => 2,
            'debit' => $deposit->balance,
            'kredit' => 0,
            'reference_number' => 'TRXS-' . date('YmdHis', strtotime($data['transaction_date'])),
            'account' => $data['account'],
            'note' => 'Penutupan rekening ' . $deposit->account_number . ' (' . $data['note'] . ')',
        ];
        if ($deposit->balance > 0) {
            if (!$this->deposit->depositTransactionSave($transaction, $data['type'])) {
                return back()->with(['warning' => $this->deposit->error]);
            }
        }
        $this->deposit->depositDelete($data['deposit_id'], $data['transaction_date']);
        return redirect()->route('depositList')->with(['success' => 'Data simpanan berhasil dihapus.']);
    }
    
    public function depositUploadSave(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'deposit_type_id' => 'required',
            'jurnal' => 'required',
            'account' => 'nullable'
        ]);
        unset($data['file']);
        $file = $request->file('file')->storeAs('import/deposit', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new DepositImport($data), $file);
        return redirect()->route('depositUpload')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    /*
    public function depositDetail($id)
    {
        $data['deposit'] = $this->deposit->depositGet($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, $data['limit'], ['transaction_date', 'desc']);
        $data['total_kredit'] = $this->deposit->depositTransactionList($data)->sum('kredit');
        $data['total_debit'] = $this->deposit->depositTransactionList($data)->sum('debit');
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['deposit']->account_number => url()->current()
        ];
        return view('deposit.deposit-detail-list', compact('data'));
    }
    public function depositDetailAdd($id)
    {
        $data['data'] = $this->deposit->depositGet($id);
        $data['type_transaction'][1] = 'Setoran';
        if ($data['data']->deposit_type_id != 2) {
            $data['type_transaction'][2] = 'Penarikan';
            $data['balance'] = 0;
            if ($data['data']->deposit_type_id == 1) {
                $data['balance'] = config('config_apps.besar_sp') - $data['data']->balance;
            }
        } else {
            $data['balance'] = config('config_apps.besar_sw');
        }
        $data['type_transaction'][3] = 'Jasa';
        $data['type_transaction'][4] = 'Administrasi';
        $data['type_transaction'][5] = 'Penyesuaian Setoran';
        $data['type_transaction'][6] = 'Penyesuaian Penarikan';
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['data']->account_number => route('depositDetail', ['id' => $data['data']->id]),
            'Tambah Transaksi' => url()->current(),
        ];
        return view('deposit.deposit-detail-form', compact('data'));
    }
    public function depositDetailPreview(DepositTransactionRequest $request)
    {
        $data['data'] = $request->validated();
        if (in_array($data['data']['type'], [1, 3, 5])) {
            $data['data']['kredit'] = str_replace(',', '', $request->balance);
            $data['data']['debit'] = 0;
        } else {
            $data['data']['debit'] = str_replace(',', '', $request->balance);
            $data['data']['kredit'] = 0;
        }

        $data['deposit'] = $this->deposit->depositGet($data['data']['deposit_id']);
        if ($data['deposit']->deposit_type_id == 2) {
            $data['data']['month'] = $request->month;
        }
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['data']['note'] = $data['type_transaction'][$data['data']['type']] . ' ' . $data['deposit']->account_number . ' (' . $data['data']['note'] . ')';
        $data['account'] = $this->accountancy->accountGet(['code', $data['data']['account']]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['deposit']->account_number => route('depositDetail', ['id' => $data['deposit']->id]),
            'Tambah Transaksi' => route('depositDetailAdd', ['id' => $data['deposit']->id]),
        ];
        return view('deposit.deposit-detail-preview', compact('data'));
    }
    public function depositDetailPrint($id)
    {
        $data['deposit'] = $this->deposit->depositGet($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.deposit-detail-print', compact('data'));
    }
    
    public function depositDetailDownload($id)
    {
        $data['deposit'] = $this->deposit->depositGet($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];

        $export['header'] = [
            'account_number' => $data['deposit']->account_number,
            'code' => $data['deposit']->member->code,
            'name' => $data['deposit']->member->name,
            'type' => $data['deposit']->type->name,
            'region' => $data['deposit']->region->name,
            'balance' => $data['deposit']->balance,
            'total_debit' => $data['data']->sum('debit'),
            'total_kredit' => $data['data']->sum('kredit'),
        ];
        $export['data'] = [];
        $i = 1;
        foreach ($data['data'] as $key => $value) {
            $export['data'][$key]['no'] = $i++;
            $export['data'][$key]['no_ref'] = $value->reference_number;
            $export['data'][$key]['note'] = $value->note;
            $export['data'][$key]['date'] = $value->transaction_date;
            $export['data'][$key]['type'] = '[' . str_pad($value->type, 2, 0, STR_PAD_LEFT) . '] - ' . $data['type_transaction'][$value['type']];
            $export['data'][$key]['kredit'] = number_format($value->kredit, 2, ',', '.');
            $export['data'][$key]['debit'] = number_format($value->debit, 2, ',', '.');
        }
        $export['total_row'] = $data['data']->count();
        return Excel::download(new DepositDetailExport($export), 'Data Transaksi ' . $data['deposit']->account_number . '.xlsx');
    }
    */
    
    
    public function depositDetailSave(DepositTransactionRequest $request)
    {
        $data = $request->validated();
        if (!$this->deposit->depositTransactionSave($data)) {
            return redirect()->route('depositDetailAdd', ['id' => $data['deposit_id']])->with(['warning' => $this->deposit->error]);
        }
        return redirect()->route('depositDetail', ['id' => $data['deposit_id']])->with(['success' => 'Transaksi Sukses.']);
    }
    public function depositBook($id)
    {
        $data['deposit'] = $this->deposit->depositGet($id);
        if (!$data['deposit'] || $data['deposit']->deleted_by != 0) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['data'] = $this->deposit->depositBookList(['deposit_id' => $id], 31);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Buku Tabungan  : ' . $data['deposit']->account_number => url()->current()
        ];
        return view('deposit.deposit-book-list', compact('data'));
    }
    public function depositBookResetAll($id)
    {
        $data['deposit_id'] = $id;
        $data['print'] = 0;
        $this->deposit->depositBookReset($data);
        return back()->with(['success' => 'Data cetak tabungan berhasil direset.']);
    }
    public function depositBookReset($deposit_id, $id)
    {
        $data['deposit_id'] = $deposit_id;
        $data['id'] = $id;
        $book = $this->deposit->depositBookGet($id);
        $data['print'] = $book->print == 1 ? 0 : 1;
        $this->deposit->depositBookReset($data);
        return back()->with(['success' => 'Data tabungan berhasil direset.']);
    }
    public function depositBookPrint($id)
    {
        $data['deposit'] = $this->deposit->depositGet($id);
        if (!$data['deposit'] || $data['deposit']->deleted_by != 0) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['data'] = $this->deposit->depositBookList(['deposit_id' => $id], 31);
        return view('deposit.deposit-book-print', compact('data'));
    }
    public function depositBookPrintConfirm($id)
    {
        $data['data'] = $this->deposit->depositBookList(['deposit_id' => $id], 31);
        $list_id = [];
        foreach ($data['data'] as $key => $value) {
            $list_id[] = $value->id;
        }
        $this->deposit->depositBookPrint($list_id);
        return redirect()->route('depositBook', ['id' => $id])->with(['success' => 'Print tabungan berhasil.']);
    }
    /*
    * ========================================================================================== START SIMPANAN ==========================================================================================
    */



    /*
    * ========================================================================================== START TRANSAKSI SIMPANAN ==========================================================================================
    */
    public function depositTransactionList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['data'] = $this->deposit->depositTransactionList($data, $data['limit']);
        $data['region'] = $this->master->regionList();
        $data['type'] = $this->deposit->depositTypeList();
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['param'] = 'q=' . $data['q'] . '&region_id=' . $data['region_id'] . '&type_id=' . $data['type_id'] . '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];
        $data['active_menu'] = 'deposit-transaction';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Data Transaksi' => url()->current()
        ];
        return view('deposit.deposit-transaction-list', compact('data'));
    }
    public function depositTransactionPrintAll()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $saldo_awal = $this->deposit->depositTransactionList([
            'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
            'region_id' => $data['region_id'],
            'type_id' => $data['type_id'],
            'q' => $data['q'],
        ]);
        $data['saldo_awal'] = $saldo_awal->sum('kredit') - $saldo_awal->sum('debit');
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.deposit-transaction-print', compact('data'));
    }
    public function depositTransactionDownload()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $saldo_awal = $this->deposit->depositTransactionList([
            'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
            'region_id' => $data['region_id'],
            'type_id' => $data['type_id'],
            'q' => $data['q'],
        ]);
        $saldo = $saldo_awal->sum('kredit') - $saldo_awal->sum('debit');
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $export['data'][] = [
            'Saldo Awal',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')',
        ];
        $total_debit = $data['data']->sum('debit');
        $total_kredit = $data['data']->sum('kredit');
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $saldo += $value->kredit - $value->debit;
            $export['data'][] = [
                $i,
                $value->deposit->account_number,
                $value->member->name,
                $value->member->code,
                $value->region->name,
                $value->depositType->name,
                $value->reference_number,
                $value->note,
                $value->transaction_date,
                '[' . str_pad($value->type, 2, 0, STR_PAD_LEFT) . '] - ' . $data['type_transaction'][$value->type],
                number_format($value->kredit, 2, ',', '.'),
                number_format($value->debit, 2, ',', '.'),
                $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')',
            ];
        }
        $export['data'][] = [
            'Saldo Akhir',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format($total_kredit, 2, ',', '.'),
            number_format($total_debit, 2, ',', '.'),
            $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')',
        ];
        // dd($export);
        return Excel::download(new DepositTransactionExport($export), 'Data Transaksi Simpanan.xlsx');
    }
    public function depositTransactionPrint($id)
    {
        $data['data'] = $this->deposit->depositTransactionGet($id);
        if (!$data['data']) {
            return back()->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        return view('deposit.transaction-print', compact('data'));
    }
    public function depositTransactionUpload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->deposit->depositTransactionUploadList($data, $data['limit']);
        $data['type'] = $this->deposit->depositTypeList();
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['region'] = $this->master->regionList();
        $data['jenis_transaksi'] = [
            1 => 'Setoran',
            2 => 'Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['active_menu'] = 'deposit-transaction';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Data Transaksi' => route('depositTransactionList'),
            'Upload' => url()->current()
        ];
        if (isset($_GET['download'])) {
            $simpanan = $this->deposit->depositList(['type_id' => $_GET['type_id'], 'region_id' => $_GET['region_id']]);
            $jenis = $this->deposit->depositTypeGet($_GET['type_id']);
            $export['data'] = [];
            $i = 0;
            foreach ($simpanan as $key => $value) {
                $i++;
                $export['data'][$i]['no'] = $i;
                $export['data'][$i]['kode'] = $value->member->code;
                $export['data'][$i]['nama'] = $value->member->name;
                $export['data'][$i]['wilayah'] = $value->region->name;
                $export['data'][$i]['no_rek'] = $value->account_number;
                $export['data'][$i]['jenis_transaksi'] = '';
                $export['data'][$i]['no_ref'] = '';
                $export['data'][$i]['keterangan'] = '';
                $export['data'][$i]['jumlah'] = 0;
            }
            return Excel::download(new DepositTransactionFormatExport($export), 'Format Upload Transaki ' . $jenis->name . '.xlsx');
        }
        if (isset($_GET['confirm'])) {
            $this->deposit->depositTransactionUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('depositTransactionUpload')->with(['info' => 'Upload data transaksi simpanan dibatalkan.']);
            } else {
                return redirect()->route('depositTransactionList')->with(['success' => 'Upload data transaksi simpanan berhasil.']);
            }
        }
        return view('deposit.deposit-transaction-upload', compact('data'));
    }
    public function depositTransactionUploadSave(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'tanggal_transaksi' => 'required',
            'akun' => 'required',
            'jurnal' => 'required'
        ]);
        unset($data['file']);
        $file = $request->file('file')->storeAs('import/deposit/transaksi', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new DepositTransactionImport($data), $file);
        return redirect()->route('depositTransactionUpload')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    /*
    * ========================================================================================== END TRANSAKSI SIMPANAN ==========================================================================================
    */



    /*
    * ========================================================================================== START JENIS SIMPANAN ==========================================================================================
    */
    public function depositTypeSave(DepositTypeRequest $request)
    {
        $data = $request->validated();

        if ($data['term'] != null) {
            $data['term'] = str_replace(',', '', $data['term']);
        } else {
            unset($data['term']);
        }

        if ($request->mode == 'add') {
            if (!$this->deposit->depositTypeSave($data)) {
                return back()->with(['warning' => $this->deposit->error])->withInput();
            }
            $message = 'Data jenis simpanan berhasil ditambahkan.';
        } else {
            if (!$this->deposit->depositTypeUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->deposit->error])->withInput();
            }
            $message = 'Data jenis simpanan berhasil diperbaharui.';
        }
        return redirect()->route('depositTypeList')->with(['success' => $message]);
    }
    /*
    * ========================================================================================== END JENIS SIMPANAN ==========================================================================================
    */



    /*
    * ========================================================================================== START TAGIHAN ==========================================================================================
    */
    public function depositBillList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->deposit->depositBillUploadList($data, $data['limit'], ['member_id', 'asc']);
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'deposit-bill';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Tagihan' => url()->current()
        ];
        if (isset($_GET['confirm'])) {
            $this->deposit->depositBillUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('depositBillList')->with(['info' => 'Upload data tagihan simpanan dibatalkan.']);
            } else {
                return redirect()->route('depositBillList')->with(['success' => 'Upload data tagihan simpanan berhasil.']);
            }
        }
        return view('deposit.deposit-bill', compact('data'));
    }
    public function depositBillDownload()
    {
        $data = $this->deposit->depositBillList();
        $export['data'] = [];
        $i = 1;
        $curdate = date('Y-m-d');
        foreach ($data as $key => $value) {
            if ($value->deposit_type_id == 1) {
                $bill = $value->principal_balance - $value->deposit->balance;
                if ($bill > 0) {
                    $export['data'][$key]['no'] = $i++;
                    $export['data'][$key]['kode'] = $value->member->code;
                    $export['data'][$key]['nama'] = $value->member->name;
                    $export['data'][$key]['no_rek'] = $value->deposit->account_number;
                    $export['data'][$key]['wilayah'] = $value->region->name;
                    $export['data'][$key]['tipe'] = $value->depositType->name;
                    $export['data'][$key]['tagihan'] = 'Rp' . number_format($bill, 2, ',', '.');
                    $export['data'][$key]['bayar'] = 'Rp0';
                }
            }
            if ($value->deposit_type_id == 2) {
                if ($value->next_bill <= $curdate) {
                    $timeStart = strtotime($value->next_bill);
                    $timeEnd = strtotime($curdate);
                    // Menambah bulan ini + semua bulan pada tahun sebelumnya
                    $numBulan = 1 + (date("Y", $timeEnd) - date("Y", $timeStart)) * 12;
                    // menghitung selisih bulan
                    $numBulan += date("m", $timeEnd) - date("m", $timeStart);
                    $bill = $numBulan * $value->obligatory_balance;
                    if ($bill > 0) {
                        $export['data'][$key]['no'] = $i++;
                        $export['data'][$key]['kode'] = $value->member->code;
                        $export['data'][$key]['nama'] = $value->member->name;
                        $export['data'][$key]['no_rek'] = $value->deposit->account_number;
                        $export['data'][$key]['wilayah'] = $value->region->name;
                        $export['data'][$key]['tipe'] = $value->depositType->name;
                        $export['data'][$key]['tagihan'] = 'Rp' . number_format($bill, 2, ',', '.');
                        $export['data'][$key]['bayar'] = 'Rp0';
                    }
                }
            }
        }
        $export['row'] = count($export['data']);

        return Excel::download(new DepositBillExport($export), 'Data Tagihan Simpanan ' . date('Y-m-d') . '.xlsx');
    }
    public function depositBillUpload(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'transaction_date' => 'required',
            'account' => 'required'
        ]);
        unset($data['file']);
        $file = $request->file('file')->storeAs('import/deposit/billl', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new DepositBillImport($data), $file);
        return redirect()->route('depositBillList')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    /*
    * ========================================================================================== END TAGIHAN ==========================================================================================
    */



    /*
    * ========================================================================================== START LAPORAN ==========================================================================================
    */
    public function laporanSimpanan()
    {
        $data['active_menu'] = 'deposit-report';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Laporan' => url()->current(),
        ];
        return view('deposit.report', compact('data'));
    }
    public function simpananAnggota()
    {
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $wilayah = $this->master->regionList();
        $data['jenis'] = $this->deposit->depositTypeList();
        $data['data'] = [];
        foreach ($wilayah as $key => $value) {
            $filter['region_id'] = $value->id;
            $data['data'][$key]['id'] = $value->id;
            $data['data'][$key]['nama'] = $value->name;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $data['data'][$key][$hasil->id] = $transaction['kredit'] - $transaction['debit'];
            }
        }
        $data['active_menu'] = 'deposit-report';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Laporan' => route('laporanSimpanan'),
            'Daftar Simpanan' => url()->current(),
        ];
        return view('deposit.laporan-daftar-simpanan', compact('data'));
    }
    public function simpananAnggotaPrint()
    {
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $wilayah = $this->master->regionList();
        $data['jenis'] = $this->deposit->depositTypeList();
        $data['data'] = [];
        foreach ($wilayah as $key => $value) {
            $filter['region_id'] = $value->id;
            $data['data'][$key]['id'] = $value->id;
            $data['data'][$key]['nama'] = $value->name;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $data['data'][$key][$hasil->id] = $transaction['kredit'] - $transaction['debit'];
            }
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.laporan-daftar-simpanan-print', compact('data'));
    }
    public function simpananAnggotaDownload()
    {
        $export['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $wilayah = $this->master->regionList();
        $data['jenis'] = $this->deposit->depositTypeList();
        $export['data'] = [];
        $no = 0;
        $export['header'] = ['No', 'Wilayah'];
        foreach ($data['jenis'] as $hasil) {
            $jml[$hasil->id] = 0;
            $export['header'][] = $hasil->name . ' (Rp)';
        }
        $export['header'][] = 'Total (Rp)';
        $jml_total = 0;
        foreach ($wilayah as $key => $value) {
            $no++;
            $filter['region_id'] = $value->id;
            $export['data'][$no]['id'] = $no;
            $export['data'][$no]['nama'] = $value->name;
            $total = 0;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $deposit = $transaction['kredit'] - $transaction['debit'];
                $export['data'][$no][$hasil->id] = number_format($deposit, 2, ',', '.');
                $total += $deposit;
                $jml_total += $deposit;
                $jml[$hasil->id] += $deposit;
            }
            $export['data'][$no]['total'] = number_format($total, 2, ',', '.');
        }
        $no++;
        $export['data'][$no] = [
            'no' => 'Jumlah',
            'wilayah' => ''
        ];
        foreach ($data['jenis'] as $key => $value) {
            $export['data'][$no][] = number_format($jml[$value->id], 2, ',', '.');
        }
        $export['data'][$no]['total'] = number_format($jml_total, 2, ',', '.');
        $export['total_row'] = count($export['data']);
        // dd($export);
        return Excel::download(new DepositReportExport($export), 'Daftar Simpanan Anggota per ' . $export['end_date'] . '.xlsx');
    }
    public function simpananAnggotaDetail()
    {
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $data['region_id'] = $filter['region_id'] =  $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();
        $data['data'] = $this->master->memberList($data);
        foreach ($data['data'] as $key => $value) {
            $filter['member_id'] = $value->id;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $data['data'][$key][$hasil->id] = $transaction['kredit'] - $transaction['debit'];
            }
        }
        $data['region'] = $this->master->regionList();
        $data['active_menu'] = 'deposit-report';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Laporan' => route('laporanSimpanan'),
            'Daftar Simpanan' => route('simpananAnggota'),
            'Detail' => url()->current(),
        ];
        return view('deposit.laporan-daftar-simpanan-detail', compact('data'));
    }
    public function simpananAnggotaDetailPrint()
    {
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $data['region_id'] = $filter['region_id'] =  $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();
        $data['data'] = $this->master->memberList($data);
        foreach ($data['data'] as $key => $value) {
            $filter['member_id'] = $value->id;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $data['data'][$key][$hasil->id] = $transaction['kredit'] - $transaction['debit'];
            }
        }
        $data['region'] = $this->master->regionList();
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.laporan-daftar-simpanan-detail-print', compact('data'));
    }
    public function simpananAnggotaDetailDownload()
    {
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? date('Y-m-d');
        $data['region_id'] = $filter['region_id'] =  $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();
        $anggota = $this->master->memberList($data);
        $export['header'] = ['No', 'Kode Anggota', 'Nama Anggota'];
        foreach ($data['jenis'] as $hasil) {
            $jml[$hasil->id] = 0;
            $export['header'][] = $hasil->name . ' (Rp)';
        }
        $export['header'][] = 'Total (Rp)';
        $jml_total = 0;
        $export['data'] = [];
        $no = 0;
        foreach ($anggota as $key => $value) {
            $no++;
            $export['data'][$no]['no'] = $no;
            $export['data'][$no]['code'] = $value->code;
            $export['data'][$no]['nama'] = $value->name;
            $filter['member_id'] = $value->id;
            $total = 0;
            foreach ($data['jenis'] as $hsl => $hasil) {
                $filter['type_id'] = $hasil->id;
                $transaction = $this->deposit->depositTransactionSum($filter);
                $deposit = $transaction['kredit'] - $transaction['debit'];
                $export['data'][$no][$hasil->id] = number_format($deposit, 2, ',', '.');
                $total += $deposit;
                $jml_total += $deposit;
                $jml[$hasil->id] += $deposit;
            }
            $export['data'][$no]['total'] = number_format($total, 2, ',', '.');
        }
        $no++;
        $export['data'][$no] = [
            'no' => 'Jumlah',
            'code' => '',
            'nama' => ''
        ];
        foreach ($data['jenis'] as $key => $value) {
            $export['data'][$no][] = number_format($jml[$value->id], 2, ',', '.');
        }
        $export['data'][$no]['total'] = number_format($jml_total, 2, ',', '.');
        $export['total_row'] = count($export['data']);

        $export['periode'] = 'Per ' . $data['end_date'];
        if ($data['region_id'] != 'all') {
            $region = $this->master->regionGet($data['region_id']);
            $export['periode'] .= ' - Wilayah ' . $region->name;
        }
        return Excel::download(new DepositReportMemberExport($export), 'Daftar Simpanan Anggota per ' . $data['end_date'] . '.xlsx');
    }
    public function rekapitulasiSimpanan()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';

        $wilayah = $this->master->regionList();

        $data['data'] = [];
        foreach ($wilayah as $key => $value) {
            $data['data'][$key]['id'] = $value->id;
            $data['data'][$key]['nama'] = $value->name;

            $filter['type'] = 'all';
            $filter['region_id'] = $value->id;
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);


            $data['data'][$key]['debit'] = $transaction['debit'] - $jasa['debit'];
            $data['data'][$key]['kredit'] = $transaction['kredit'] - $jasa['kredit'];
            $data['data'][$key]['jasa'] = $jasa['kredit'] - $jasa['debit'];

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $value->id,
                'type_id' => $data['type_id']
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $data['data'][$key]['saldo_awal'] = $saldo_awal['kredit'] - $saldo_awal['debit'];
        }
        $data['jenis'] = $this->deposit->depositTypeList();

        $data['active_menu'] = 'deposit-report';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Laporan' => route('laporanSimpanan'),
            'Rekapitulasi Simpanan' => url()->current(),
        ];
        return view('deposit.laporan-rekapitulasi', compact('data'));
    }
    public function rekapitulasiSimpananPrint()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';

        $wilayah = $this->master->regionList();

        $data['data'] = [];
        foreach ($wilayah as $key => $value) {
            $data['data'][$key]['id'] = $value->id;
            $data['data'][$key]['nama'] = $value->name;

            $filter['type'] = 'all';
            $filter['region_id'] = $value->id;
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);


            $data['data'][$key]['debit'] = $transaction['debit'] - $jasa['debit'];
            $data['data'][$key]['kredit'] = $transaction['kredit'] - $jasa['kredit'];
            $data['data'][$key]['jasa'] = $jasa['kredit'] - $jasa['debit'];

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $value->id,
                'type_id' => $data['type_id']
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $data['data'][$key]['saldo_awal'] = $saldo_awal['kredit'] - $saldo_awal['debit'];
        }
        $data['jenis'] = $this->deposit->depositTypeList();
        $data['assignment'] = $this->master->pengurusAssignment();

        return view('deposit.laporan-rekapitulasi-print', compact('data'));
    }
    public function rekapitulasiSimpananDownload()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';

        if ($data['type_id'] == 'all') {
            $export['title'] = 'Rekapitulasi Simpanan';
        } else {
            $type = $this->deposit->depositTypeGet($data['type_id']);
            $export['title'] = 'Rekapitulasi ' . $type->name;
        }
        $export['periode'] = 'Per ' . date('d-m-Y', strtotime($data['date']));
        $export['header'] = [
            'No',
            'Wilayah',
            'Saldo s/d ' . date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))) . ' (Rp)',
            'Saldo Masuk (Rp)',
            'Saldo Keluar (Rp)',
            'Jasa (Rp)',
            'Total Saldo (Rp)'
        ];

        $wilayah = $this->master->regionList();
        $export['data'] = [];
        $no = $total_kredit = $total_debit = $total_jasa = $total_saldo_awal = 0;
        foreach ($wilayah as $key => $value) {
            $no++;
            $export['data'][$no]['no'] = $no;
            $export['data'][$no]['nama'] = $value->name;

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $value->id,
                'type_id' => $data['type_id']
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $export['data'][$no]['saldo_awal'] = number_format($saldo_awal['kredit'] - $saldo_awal['debit'], 2, ',', '.');
            $total_saldo_awal += ($saldo_awal['kredit'] - $saldo_awal['debit']);

            $filter['type'] = 'all';
            $filter['region_id'] = $value->id;
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);


            $export['data'][$no]['kredit'] = number_format($transaction['kredit'] - $jasa['kredit'], 2, ',', '.');
            $export['data'][$no]['debit'] = number_format($transaction['debit'] - $jasa['debit'], 2, ',', '.');
            $export['data'][$no]['jasa'] = number_format($jasa['kredit'] - $jasa['debit'], 2, ',', '.');
            $export['data'][$no]['total'] = number_format($transaction['kredit'] - $transaction['debit'], 2, ',', '.');
            $total_kredit += ($transaction['kredit'] - $jasa['kredit']);
            $total_debit += ($transaction['debit'] - $jasa['debit']);
            $total_jasa += ($jasa['kredit'] - $jasa['debit']);
        }
        $saldo = $total_saldo_awal + $total_kredit - $total_debit + $total_jasa;
        $export['data'][++$no] = [
            'no' => 'Jumlah',
            'nama' => '',
            'saldo_awal' => number_format($total_saldo_awal, 2, ',', '.'),
            'kredit' => number_format($total_kredit, 2, ',', '.'),
            'debit' => number_format($total_debit, 2, ',', '.'),
            'jasa' => number_format($total_jasa, 2, ',', '.'),
            'total' => number_format($saldo, 2, ',', '.'),
        ];
        $export['total_row'] = count($export['data']);
        return Excel::download(new DepositReportDetailExport($export), $export['title'] . ' Per ' . $data['date'] . '.xlsx');
    }
    public function rekapitulasiSimpananDetail()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';
        $data['region_id'] = $filter['region_id'] = $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();

        $data['data'] = $this->master->memberList($data);

        foreach ($data['data'] as $key => $value) {
            $filter['member_id'] = $value->id;
            $filter['type'] = 'all';
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);


            $data['data'][$key]['debit'] = $transaction['debit'] - $jasa['debit'];
            $data['data'][$key]['kredit'] = $transaction['kredit'] - $jasa['kredit'];
            $data['data'][$key]['jasa'] = $jasa['kredit'] - $jasa['debit'];

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $data['region_id'],
                'type_id' => $data['type_id'],
                'member_id' => $value->id
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $data['data'][$key]['saldo_awal'] = $saldo_awal['kredit'] - $saldo_awal['debit'];
        }

        $data['param'] = 'type_id=' . $data['type_id'] . '&region_id=' . $data['region_id'] . '&start_date=' . $data['start_date'] . '&date=' . $data['date'];

        $data['region'] = $this->master->regionList();
        $data['active_menu'] = 'deposit-report';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Laporan' => route('laporanSimpanan'),
            'Rekapitulasi Simpanan' => route('rekapitulasiSimpanan'),
            'Detail' => url()->current(),
        ];
        return view('deposit.laporan-rekapitulasi-detail', compact('data'));
    }
    public function rekapitulasiSimpananDetailPrint()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';
        $data['region_id'] = $filter['region_id'] = $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();

        $data['data'] = $this->master->memberList($data);

        foreach ($data['data'] as $key => $value) {
            $filter['member_id'] = $value->id;
            $filter['type'] = 'all';
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);


            $data['data'][$key]['debit'] = $transaction['debit'] - $jasa['debit'];
            $data['data'][$key]['kredit'] = $transaction['kredit'] - $jasa['kredit'];
            $data['data'][$key]['jasa'] = $jasa['kredit'] - $jasa['debit'];

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $data['region_id'],
                'type_id' => $data['type_id'],
                'member_id' => $value->id
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $data['data'][$key]['saldo_awal'] = $saldo_awal['kredit'] - $saldo_awal['debit'];
        }
        $data['region'] = $this->master->regionList();
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('deposit.laporan-rekapitulasi-detail-print', compact('data'));
    }
    public function rekapitulasiSimpananDetailDownload()
    {
        $data['date'] = $filter['end_date'] = $_GET['date'] ?? date('Y-m-t');
        $data['start_date'] = $filter['start_date'] = date('Y-m-01', strtotime($data['date']));
        $data['type_id'] = $filter['type_id'] = $_GET['type_id'] ?? 'all';
        $data['region_id'] = $filter['region_id'] = $_GET['region_id'] ?? 'all';
        $data['jenis'] = $this->deposit->depositTypeList();

        $anggota = $this->master->memberList($data);
        $export['data'] = [];
        $no = $total_saldo_awal = $total_kredit = $total_debit = $total_jasa = 0;
        foreach ($anggota as $key => $value) {
            $no++;
            $export['data'][$no]['no'] = $no;
            $export['data'][$no]['code'] = $value->code;
            $export['data'][$no]['name'] = $value->name;

            $filter_awal = [
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))),
                'region_id' => $data['region_id'],
                'type_id' => $data['type_id'],
                'member_id' => $value->id
            ];
            $saldo_awal = $this->deposit->depositTransactionSum($filter_awal);
            $saldoawal = $saldo_awal['kredit'] - $saldo_awal['debit'];
            $export['data'][$no]['saldo_awal'] = number_format($saldoawal, 2, ',', '.');
            $total_saldo_awal += $saldoawal;

            $filter['member_id'] = $value->id;
            $filter['type'] = 'all';
            $transaction = $this->deposit->depositTransactionSum($filter);
            $filter['type'] = 3;
            $jasa = $this->deposit->depositTransactionSum($filter);

            $kredit = $transaction['kredit'] - $jasa['kredit'];
            $debit = $transaction['debit'] - $jasa['debit'];
            $jasa = $jasa['kredit'] - $jasa['debit'];
            $export['data'][$no]['kredit'] = number_format($kredit, 2, ',', '.');
            $export['data'][$no]['debit'] = number_format($debit, 2, ',', '.');
            $export['data'][$no]['jasa'] = number_format($jasa, 2, ',', '.');
            $export['data'][$no]['saldo'] = number_format($saldoawal + $kredit - $debit + $jasa, 2, ',', '.');

            $total_kredit += $kredit;
            $total_debit += $debit;
            $total_jasa += $jasa;
        }
        $total_saldo = $total_saldo_awal + $total_kredit - $total_debit + $total_jasa;
        $export['data'][++$no] = [
            'no' => 'Jumlah',
            'code' => '',
            'nama' => '',
            'saldo_awal' => number_format($total_saldo_awal, 2, ',', '.'),
            'kredit' => number_format($total_kredit, 2, ',', '.'),
            'debit' => number_format($total_debit, 2, ',', '.'),
            'jasa' => number_format($total_jasa, 2, ',', '.'),
            'saldo' => number_format($total_saldo, 2, ',', '.'),
        ];
        $data['region'] = $this->master->regionList();
        if ($data['type_id'] != 'all') {
            $type = $this->deposit->depositTypeGet($data['type_id']);
            $export['title'] = 'Rekapitulasi ' . $type->name . ' Anggota';
        } else {
            $export['title'] = 'Rekapitulasi Simpanan Anggota';
        }
        $export['periode'] = 'Per ' . date('d-m-Y', strtotime($data['date']));
        if ($data['region_id'] != 'all') {
            $region = $this->master->regionGet($data['region_id']);
            $export['periode'] .= ' - Wilayah ' . $region->name;
        }
        $export['header'] = [
            'No',
            'Kode Anggota',
            'Nama Anggota',
            'Saldo s/d ' . date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) . ' (Rp)',
            'Saldo Masuk (Rp)',
            'Saldo Keluar (Rp)',
            'Jasa (Rp)',
            'Total Saldo (Rp)'
        ];
        $export['total_row'] = count($export['data']);
        return Excel::download(new DepositReportMemberDetailExport($export), $export['title'] . ' per ' . $data['date'] . '.xlsx');
    }
    /*
    * ========================================================================================== END LAPORAN ==========================================================================================
    */
}