<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\MasterClass;
use App\Exports\AccountExport;
use App\Exports\LedgerDetailExport;
use App\Exports\TrialBalanceExport;
use App\Http\Requests\AccountGroupRequest;
use App\Http\Requests\JournalRequest;
use App\Imports\AccountImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AccountancyController extends Controller
{
    private $accountancy, $master;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Pembukuan');

        $this->accountancy = new AccountancyClass();
        $this->master = new MasterClass();
    }





    /*
    * ========================================================================================== START DATA AKUN ==========================================================================================
    */
    public function accountList()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->accountList($data, false);
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => url()->current()
        ];
        return view('accountancy.account-list', compact('data'));
    }
    public function accountAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'account';
        $data['akun'] = $this->accountancy->accountList();
        $data['group'] = $this->accountancy->accountGroupList();
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.account-form', compact('data'));
    }
    public function accountEdit($id)
    {
        $data['data'] = $this->accountancy->accountGet($id);
        if (!$data['data']) {
            return redirect()->route('accountList')->with(['warning' => 'Data akun tidak ditemukan.']);
        }
        $data['group'] = $this->accountancy->accountGroupList();
        $data['mode'] = 'edit';
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Edit: ' . $data['data']->name => url()->current()
        ];
        return view('accountancy.account-form', compact('data'));
    }
    public function accountSave(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable',
            'name' => 'required',
            'type' => 'nullable',
            'group_id' => 'required',
        ]);
        //if mode input is add
        if ($request->mode == 'add') {
            //save account
            if (!$this->accountancy->accountSave($data)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }
            $message = 'Data akun berhasil di tambahkan';
        } else {
            //update refion
            if (!$this->accountancy->accountUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }
            $message = 'Data akun berhasil diperbaharui.';
        }
        return redirect()->route('accountList')->with(['success' => $message]);
    }
    public function accountDelete($id)
    {
        $account = $this->accountancy->accountGet($id);
        if ($account == false) {
            return redirect()->route('accountList')->with(['warning' => 'Data akun tidak ditemukan.']);
        }
        // cek saldo akhir
        if ($account->end_balance > 0 || $account->start_balance > 0) {
            return redirect()->route('accountList')->with(['warning' => 'Data akun tidak dapat dihapus.']);
        }
        // cek jurnal
        $account->delete();
        return redirect()->route('accountList')->with(['success' => 'Data akun berhasil dihapus.']);
    }
    public function accountConfig()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->accountUploadList($data);
        $data['set-account'] = config('config_apps.set_account');
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Set Saldo' => url()->current()
        ];
        if (isset($_GET['download']) && $_GET['download'] == 1) {
            return Excel::download(new AccountExport, 'FormatSetSaldo.xlsx');
        }
        if (isset($_GET['confirm'])) {
            $this->accountancy->accountUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('accountConfig')->with(['info' => 'Upload saldo awal dibatalkan.']);
            } else {
                return redirect()->route('accountConfig')->with(['success' => 'Upload saldo awal berhasil.']);
            }
        }
        return view('accountancy.account-config', compact('data'));
    }
    public function accountConfigSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('file')->storeAs('import/pembukuan', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new AccountImport, $file);
        return redirect()->route('accountConfig')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    public function accountConfigReset()
    {
        $account = DB::table('accounts');
        $update = [
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id,
            'beginning_balance' => 0,
            'ending_balance' => 0,
            'adjusting_balance' => 0,
            'debit' => 0,
            'kredit' => 0,
        ];
        $account->update($update);
        $config = "<?php \n return [\n";
        foreach (config('config_apps') as $hsl => $hasil) {
            if ($hsl == 'set_account') {
                $hasil = 0;
            }
            $config .= "\t'{$hsl}' => '{$hasil}',\n";
        }
        $config .= " ]; ";
        $file = config_path() . '/config_apps.php';
        file_put_contents($file, $config);
        DB::table('journals')->truncate();
        DB::table('journal_details')->truncate();
        DB::table('adjusting_journals')->truncate();
        DB::table('adjusting_journal_details')->truncate();
        return redirect()->route('accountConfig')->with(['success' => 'Konfigurasi Saldo awal berhasil di reset.']);
    }
    /*
    * ========================================================================================== END DATA AKUN ==========================================================================================
    */





    /*
    * ========================================================================================== START KELOMPOK AKUN ==========================================================================================
    */
    public function accountGroupList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->accountGroupList($data, $data['limit']);
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Kelompok Akun' => url()->current()
        ];
        return view('accountancy.account-group-list', compact('data'));
    }
    public function accountGroupAdd()
    {
        $data['mode'] = 'add';
        $data['golongan'] = $this->accountancy->accountList(['level' => 2]);
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Kelompok Akun' => route('accountGroupList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.account-group-form', compact('data'));
    }
    public function accountGroupEdit($id)
    {
        $data['data'] = $this->accountancy->accountGroupGet($id);
        if (!$data['data']) {
            return redirect()->route('accountGroupList')->with(['success' => 'Data tidak ditemukan']);
        }
        $data['mode'] = 'edit';
        $data['golongan'] = $this->accountancy->accountList(['level' => 2]);
        $data['active_menu'] = 'account';
        $data['breadcrumb'] = [
            'Data Akun' => route('accountList'),
            'Kelompok Akun' => route('accountGroupList'),
            'Edit ' . $data['data']->name => url()->current(),
        ];
        return view('accountancy.account-group-form', compact('data'));
    }
    public function accountGroupSave(AccountGroupRequest $request)
    {
        $data = $request->validated();
        if ($request->mode == 'add') {
            $this->accountancy->accountGroupSave($data);
            $message = 'Data kelompok akun berhasil ditambahkan.';
        } else {
            $this->accountancy->accountGroupUpdate($request->id, $data);
            $message = 'Data kelompook akun berhasil diperbaharui.';
        }
        return redirect()->route('accountGroupList')->with(['success' => $message]);
    }
    public function accountGroupDelete($id)
    {
        $data = $this->accountancy->accountGroupGet($id);
        if (!$data) {
            return redirect()->route('accountGroupList')->with(['success' => 'Data tidak ditemukan']);
        }
        if ($data->account->count() > 0) {
            return redirect()->route('accountGroupList')->with(['success' => 'Data tidak dapat dihapus karena sedang digunakan.']);
        }
        $data->delete();
        return redirect()->route('accountGroupList')->with(['success' => 'Data kelompok akun berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END KELOMPOK AKUN ==========================================================================================
    */





    /*
    * ========================================================================================== START NERACA SALDO ==========================================================================================
    */
    public function trialBalance()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if ($data['start_date'] > $data['end_date']) {
            return back()->with(['warning' => 'Tanggal tidak valid']);
        }
        $data['view'] = $_GET['view'] ?? 'all';
        $data['active_menu'] = 'trialbalance';
        $data['breadcrumb'] = [
            'Neraca Saldo' => route('trialBalance')
        ];
        $data['data'] = $this->accountancy->ledger($data);

        $data['param'] = 'view=' . $data['view'];
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Neraca Saldo' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
            $data['data'] = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Neraca Saldo' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
            $data['data'] = json_decode($tbt->data);
        }

        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo_awal'] = 0;
            $data['group'][$key]['debit'] = 0;
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['saldo_penyesuaian'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil->saldo_awal;
                    $data['group'][$key]['debit'] += $hasil->debit;
                    $data['group'][$key]['kredit'] += $hasil->kredit;
                    $data['group'][$key]['saldo_penyesuaian'] += $hasil->saldo_penyesuaian;
                }
            }
        }
        return view('accountancy.trial-balance', compact('data'));
    }
    public function trialBalancePrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'all';
        $data['data'] = $this->accountancy->ledger($data);
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['data'] = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['data'] = json_decode($tbt->data);
        }
        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo_awal'] = 0;
            $data['group'][$key]['debit'] = 0;
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['saldo_penyesuaian'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil->saldo_awal;
                    $data['group'][$key]['debit'] += $hasil->debit;
                    $data['group'][$key]['kredit'] += $hasil->kredit;
                    $data['group'][$key]['saldo_penyesuaian'] += $hasil->saldo_penyesuaian;
                }
            }
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('accountancy.trial-balance-print', compact('data'));
    }
    public function trialBalanceDownload()
    {
        $export['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $export['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $export['view'] = $_GET['view'] ?? 'all';

        $data['data'] = $this->accountancy->ledger($export);

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['data'] = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['data'] = json_decode($tbt->data);
        }

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo_awal'] = 0;
            $data['group'][$key]['debit'] = 0;
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['saldo_penyesuaian'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil->saldo_awal;
                    $data['group'][$key]['debit'] += $hasil->debit;
                    $data['group'][$key]['kredit'] += $hasil->kredit;
                    $data['group'][$key]['saldo_penyesuaian'] += $hasil->saldo_penyesuaian;
                }
            }
        }

        if ($export['view'] == 'all') {
            $export['data'] = [];
            $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
            foreach ($data['data'] as $key => $value) {
                $i++;
                if ($value->type == 0) {
                    if ($value->saldo_awal >= 0) {
                        $saldo_awal_debit = $value->saldo_awal;
                        $saldo_awal_kredit = 0;
                    } else {
                        $saldo_awal_kredit = $value->saldo_awal * -1;
                        $saldo_awal_debit = 0;
                    }

                    if ($value->saldo_penyesuaian >= 0) {
                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                        $saldo_penyesuaian_kredit = 0;
                    } else {
                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                        $saldo_penyesuaian_debit = 0;
                    }
                } else {
                    if ($value->saldo_awal >= 0) {
                        $saldo_awal_kredit = $value->saldo_awal;
                        $saldo_awal_debit = 0;
                    } else {
                        $saldo_awal_kredit = 0;
                        $saldo_awal_debit = $value->saldo_awal * -1;
                    }

                    if ($value->saldo_penyesuaian >= 0) {
                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                        $saldo_penyesuaian_debit = 0;
                    } else {
                        $saldo_penyesuaian_kredit = 0;
                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                    }
                }
                $total_saldo_awal_debit += $saldo_awal_debit;
                $total_saldo_awal_kredit += $saldo_awal_kredit;
                $total_debit += $value->debit;
                $total_kredit += $value->kredit;
                $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;

                $export['data'][] = [
                    $i,
                    $value->code,
                    $value->name,
                    number_format($saldo_awal_debit, 2, ',', '.'),
                    number_format($saldo_awal_kredit, 2, ',', '.'),
                    number_format($value->debit, 2, ',', '.'),
                    number_format($value->kredit, 2, ',', '.'),
                    number_format($saldo_penyesuaian_debit, 2, ',', '.'),
                    number_format($saldo_penyesuaian_kredit, 2, ',', '.'),
                ];
            }
        } else {
            $i = $total_debit = $total_kredit = $total_saldo_awal_debit = $total_saldo_awal_kredit = $total_saldo_penyesuaian_debit = $total_saldo_penyesuaian_kredit = 0;
            $export['data'] = [];
            foreach ($data['group'] as $key => $value) {
                $i++;
                if ($value->type == 0) {
                    if ($value->saldo_awal >= 0) {
                        $saldo_awal_debit = $value->saldo_awal;
                        $saldo_awal_kredit = 0;
                    } else {
                        $saldo_awal_kredit = $value->saldo_awal * -1;
                        $saldo_awal_debit = 0;
                    }

                    if ($value->saldo_penyesuaian >= 0) {
                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian;
                        $saldo_penyesuaian_kredit = 0;
                    } else {
                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian * -1;
                        $saldo_penyesuaian_debit = 0;
                    }
                } else {
                    if ($value->saldo_awal >= 0) {
                        $saldo_awal_kredit = $value->saldo_awal;
                        $saldo_awal_debit = 0;
                    } else {
                        $saldo_awal_kredit = 0;
                        $saldo_awal_debit = $value->saldo_awal * -1;
                    }

                    if ($value->saldo_penyesuaian >= 0) {
                        $saldo_penyesuaian_kredit = $value->saldo_penyesuaian;
                        $saldo_penyesuaian_debit = 0;
                    } else {
                        $saldo_penyesuaian_kredit = 0;
                        $saldo_penyesuaian_debit = $value->saldo_penyesuaian * -1;
                    }
                }
                $total_saldo_awal_debit += $saldo_awal_debit;
                $total_saldo_awal_kredit += $saldo_awal_kredit;
                $total_debit += $value->debit;
                $total_kredit += $value->kredit;
                $total_saldo_penyesuaian_debit += $saldo_penyesuaian_debit;
                $total_saldo_penyesuaian_kredit += $saldo_penyesuaian_kredit;

                $export['data'][] = [
                    $i,
                    $value->name,
                    number_format($saldo_awal_debit, 2, ',', '.'),
                    number_format($saldo_awal_kredit, 2, ',', '.'),
                    number_format($value->debit, 2, ',', '.'),
                    number_format($value->kredit, 2, ',', '.'),
                    number_format($saldo_penyesuaian_debit, 2, ',', '.'),
                    number_format($saldo_penyesuaian_kredit, 2, ',', '.'),
                ];
            }

            $export['data'][] = [
                '',
                'Jumlah',
                number_format($total_saldo_awal_debit, 2, ',', '.'),
                number_format($total_saldo_awal_kredit, 2, ',', '.'),
                number_format($total_debit, 2, ',', '.'),
                number_format($total_kredit, 2, ',', '.'),
                number_format($total_saldo_penyesuaian_debit, 2, ',', '.'),
                number_format($total_saldo_penyesuaian_kredit, 2, ',', '.'),
            ];
        }

        return Excel::download(new TrialBalanceExport($export), 'Neraca Saldo.xlsx');
    }
    /*
    * ========================================================================================== END NERACA SALDO ==========================================================================================
    */





    /*
    * ========================================================================================== START JURNAL TRANSAKSI ==========================================================================================
    */
    public function journalList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');

        $data['param'] = 'q=' . $data['q'];

        $data['active_menu'] = 'journal';
        $data['breadcrumb'] = [
            'Jurnal Transaksi' => url()->current()
        ];

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Jurnal Transaksi' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Jurnal Transaksi' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }

        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];

        $data['data'] = $this->accountancy->journalList($data, $data['limit']);

        $data['session'] = session('journal');
        if ($data['session'] != null) {
            if (isset($_GET['print']) && !empty($_GET['print'])) {
                $data['print'] = $this->accountancy->journalGet($_GET['print']);
                $data['print']->amount = $data['print']->detail->sum('debit');
            }
        }
        session()->forget('journal');

        $data['jumlah'] = $this->accountancy->journalDetailSum($data);
        return view('accountancy.journal-list', compact('data'));
    }
    public function journalAdd()
    {
        $data['mode'] = 'add';
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['data'] = [
            'transaction_date' => date('Y-m-d'),
            'reference_number' => 'TRX-' . date('YmdHis')
        ];
        $data['rincian_atas'] = $data['rincian_bawah'] = 2;
        $data['active_menu'] = 'journal';
        $data['breadcrumb'] = [
            'Jurnal Transaksi' => route('journalList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.journal-form', compact('data'));
    }
    public function journalEdit($id)
    {
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['data'] = $this->accountancy->journalGet($id);
        if (!$data['data']) {
            return redirect()->route('journalList')->with(['warning' => 'Data jurnal transaksi tidak ditemukan.']);
        }
        if ($data['data']->edited == 1) {
            return redirect()->route('journalList')->with(['warning' => 'Transaksi sudah pernah di edit, silakan lakukan pengeditan ulang di jurnal penyesuaian jika ingin di edit kembali.']);
        }
        if ($data['data']->deleted == 1) {
            return redirect()->route('journalList')->with(['warning' => 'Data jurnal transaksi tidak ditemukan.']);
        }
        //mode
        $data['mode'] = 'edit';

        $data['active_menu'] = 'journal';
        $data['breadcrumb'] = [
            'Jurnal Transaksi' => route('journalList'),
            'Edit: ' . $data['data']->reference_number => url()->current(),
        ];
        $data['rincian_atas'] = $data['data']->detail->where('debit', '>', 0)->count() + 1;
        $data['rincian_bawah'] = $data['data']->detail->where('kredit', '>', 0)->count() + 1;
        return view('accountancy.journal-form', compact('data'));
    }
    protected function journalSave(JournalRequest $request)
    {
        $data = $request->validated();
        $data['transaction_date'] = date('Y-m-d', strtotime($data['transaction_date'])) . ' ' . date('H:i:s');

        if ($request->mode == 'add') {
            $data['detail'] = [];
            $top_account = $top_type = $top_amount = [];
            foreach ($data['top_account'] as $key => $value) {
                $top_account[] = $value;
            }
            foreach ($data['top_type'] as $key => $value) {
                $top_type[] = $value;
            }
            foreach ($data['top_amount'] as $key => $value) {
                $top_amount[] = $value;
            }
            for ($i = 0; $i < count($top_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $top_account[$i],
                    'type' => $top_type[$i],
                    'amount' => str_replace(',', '', $top_amount[$i]),
                ];
            }
            $bottom_account = $bottom_type = $bottom_amount = [];
            foreach ($data['bottom_account'] as $key => $value) {
                $bottom_account[] = $value;
            }
            foreach ($data['bottom_type'] as $key => $value) {
                $bottom_type[] = $value;
            }
            foreach ($data['bottom_amount'] as $key => $value) {
                $bottom_amount[] = $value;
            }
            for ($i = 0; $i < count($bottom_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $bottom_account[$i],
                    'type' => $bottom_type[$i],
                    'amount' => str_replace(',', '', $bottom_amount[$i]),
                ];
            }

            unset($data['top_account'], $data['top_type'], $data['top_amount'], $data['bottom_account'], $data['bottom_type'], $data['bottom_amount']);
            if (!$this->accountancy->adjustingJournalSave($data)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }
            $this->accountancy->journalSave($data);

            $last_journal_id = $this->accountancy->last_journal_id;

            $message = 'Jurnal transaksi berhasil di tambahkan.';
            session(['journal' => $this->accountancy->last_journal_id]);
        } else {
            $top_account = $top_type = $top_amount = [];
            foreach ($data['top_account'] as $key => $value) {
                $top_account[] = $value;
            }
            foreach ($data['top_type'] as $key => $value) {
                $top_type[] = $value;
            }
            foreach ($data['top_amount'] as $key => $value) {
                $top_amount[] = $value;
            }
            for ($i = 0; $i < count($top_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $top_account[$i],
                    'type' => $top_type[$i],
                    'amount' => str_replace(',', '', $top_amount[$i]),
                ];
            }
            $bottom_account = $bottom_type = $bottom_amount = [];
            foreach ($data['bottom_account'] as $key => $value) {
                $bottom_account[] = $value;
            }
            foreach ($data['bottom_type'] as $key => $value) {
                $bottom_type[] = $value;
            }
            foreach ($data['bottom_amount'] as $key => $value) {
                $bottom_amount[] = $value;
            }
            for ($i = 0; $i < count($bottom_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $bottom_account[$i],
                    'type' => $bottom_type[$i],
                    'amount' => str_replace(',', '', $bottom_amount[$i]),
                ];
            }
            $data['type_journal'] = 'general';
            unset($data['top_account'], $data['top_type'], $data['top_amount'], $data['bottom_account'], $data['bottom_type'], $data['bottom_amount']);
            if (!$this->accountancy->adjustingJournalUpdate($request->adjusting_journal_id, $data)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }
            $this->accountancy->journalUpdate($request->id);
            $message = 'Jurnal transaksi berhasil diperbaharui.';
            $last_journal_id = '';
        }
        return redirect()->route('journalList', ['print' => $last_journal_id])->with(['success' => $message]);
    }
    public function journalDelete($id)
    {
        $journal = $this->accountancy->journalGet($id);
        if ($journal == false) {
            return redirect()->route('journalList')->with(['warning' => 'Data jurnal transaksi tidak ditemukan.']);
        }
        $this->accountancy->adjustingJournalDelete($journal->adjusting_journal_id);
        $this->accountancy->journalDelete($journal->id);
        return redirect()->route('journalList')->with(['success' => 'Data jurnal transaksi berhasil dihapus.']);
    }
    public function journalPrint()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
        }
        $data['data'] = $this->accountancy->journalList($data, false, ['transaction_date', 'asc']);
        $data['jumlah'] = $this->accountancy->journalDetailSum($data);
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('accountancy.journal-print', compact('data'));
    }
    /*
    * ========================================================================================== END JURNAL TRANSAKSI ==========================================================================================
    */



    /*
    * ========================================================================================== START BUKU BESAR ==========================================================================================
    */
    public function ledger()
    {
        $data['start_date'] = config('config_apps.journal_periode_start');
        $data['end_date'] = config('config_apps.journal_periode_end');
        $data['data'] = $this->accountancy->ledger($data);
        $data['active_menu'] = 'ledger';
        $data['breadcrumb'] = [
            'Buku Besar' => url()->current()
        ];
        $data['param'] = '';
        if (isset($_GET['tbb_id'])) {
            $data['tbb_id'] = $_GET['tbb_id'];
            $tbb = $this->accountancy->closeMonthlyBookGet($data['tbb_id']);
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['data'] = json_decode($tbb->data);
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Buku Besar' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
        }
        if (isset($_GET['tbt_id'])) {
            $data['tbt_id'] = $_GET['tbt_id'];
            $tbt = $this->accountancy->closeYearlyBookGet($data['tbt_id']);
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['data'] = json_decode($tbt->data);
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Buku Besar' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
        }
        return view('accountancy.ledger', compact('data'));
    }
    public function ledgerDetail()
    {
        $data['id'] = $_GET['id'];
        $data['account'] = $this->accountancy->accountGet($data['id']);
        if (!$data['account']) {
            return redirect()->route('ledger')->with(['danger' => 'Data akun tidak ditemukan.']);
        }
        $data['type'] = $_GET['type'] ?? 1;
        $filter['account_code'] = $data['account']->code;
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');

        if ($data['start_date'] > $data['end_date']) {
            return back()->with(['danger' => 'Tanggal tidak valid.']);
        }
        $data['beginning_balance'] = $data['account']->beginning_balance;

        if ($data['start_date'] != config('config_apps.journal_periode_start')) {
            $data['beginning_balance'] = $this->accountancy->getBeginningSaldo($data['account']->code, $data['start_date'], $data['type']);
        }

        $data['param'] = 'id=' . $data['id'] . '&type=' . $data['type'];

        $data['active_menu'] = 'ledger';
        $data['breadcrumb'] = [
            'Buku Besar' => route('ledger'),
            $data['account']->code => route('ledger', ['type' => $data['type']])
        ];

        if (isset($_GET['tbb_id'])) {
            $data['tbb_id'] = $filter['tbb_id'] = $_GET['tbb_id'];
            $tbb = $this->accountancy->closeMonthlyBookGet($data['tbb_id']);
            $data['start_date'] = $filter['start_date'] = $tbb->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbb->end_periode;
            $akun = json_decode($tbb->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
            $data['param'] .= '&tbb_id=' . $tbb->id;
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Buku Besar' => route('ledger', ['tbb' => $tbb->id]),
                $data['account']->code => route('ledger', ['type' => $data['type'], 'tbb_id' => $data['tbb_id']])
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $data['tbt_id'] = $filter['tbt_id'] = $_GET['tbt_id'];
            $tbt = $this->accountancy->closeYearlyBookGet($data['tbt_id']);
            $data['start_date'] = $filter['start_date'] = $tbt->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbt->end_periode;
            $akun = json_decode($tbt->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
            $data['param'] .= '&tbt_id=' . $tbt->id;
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Buku Besar' => route('ledger', ['tbt' => $tbt->id]),
                $data['account']->code => route('ledger', ['type' => $data['type'], 'tbt_id' => $data['tbt_id']])
            ];
        }

        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];


        if ($data['type'] == 0) {
            $data['data'] = $this->accountancy->journalDetailList($filter, $data['limit']);
        } else {
            $data['data'] = $this->accountancy->adjustingJournalDetailList($filter, $data['limit']);
        }

        if ($data['data']->currentPage() > 1) {
            $limit = ($data['data']->currentPage() - 1) * $data['data']->perPage();
            if ($data['type'] == 0) {
                $jurnal = $this->accountancy->journalDetailList($filter, $limit, false, false);
            } else {
                $jurnal = $this->accountancy->adjustingJournalDetailList($filter, $limit, false, false);
            }
            if ($data['account']->type == 0) {
                $data['beginning_balance'] += $jurnal->sum('debit');
                $data['beginning_balance'] -= $jurnal->sum('kredit');
            } else {
                $data['beginning_balance'] -= $jurnal->sum('debit');
                $data['beginning_balance'] += $jurnal->sum('kredit');
            }
        }
        $data['listakun'] = $this->accountancy->accountList(['level' => 3]);
        return view('accountancy.ledger-detail', compact('data'));
    }
    public function ledgerDetailPrint()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            return redirect()->route('ledger')->with(['danger' => 'Data akun tidak ditemukan.']);
        }
        $data['id'] = $_GET['id'];
        $data['account'] = $this->accountancy->accountGet($data['id']);
        if (!$data['account']) {
            return redirect()->route('ledger')->with(['danger' => 'Data akun tidak ditemukan.']);
        }
        if (!isset($_GET['type']) || $_GET['type'] == '') {
            return redirect()->route('ledger')->with(['danger' => 'Menu tidak tersedia']);
        }
        $data['type'] = $_GET['type'];
        $filter['account_code'] = $data['account']->code;
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('ledgerDetail', ['id' => $data['account']->id])->with(['danger' => 'Tanggal tidak valid.']);
        }
        $data['beginning_balance'] = $data['account']->beginning_balance;
        if ($data['start_date'] != config('config_apps.journal_periode_start')) {
            $data['beginning_balance'] = $this->accountancy->getBeginningSaldo($data['account']->code, $data['start_date'], $data['type']);
        }
        if (isset($_GET['tbb_id'])) {
            $data['tbb_id'] = $filter['tbb_id'] = $_GET['tbb_id'];
            $tbb = $this->accountancy->closeMonthlyBookGet($data['tbb_id']);
            $data['start_date'] = $filter['start_date'] = $tbb->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbb->end_periode;
            $akun = json_decode($tbb->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
        }
        if (isset($_GET['tbt_id'])) {
            $data['tbt_id'] = $filter['tbt_id'] = $_GET['tbt_id'];
            $tbt = $this->accountancy->closeYearlyBookGet($data['tbt_id']);
            $data['start_date'] = $filter['start_date'] = $tbt->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbt->end_periode;
            $akun = json_decode($tbt->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
        }
        if ($data['type'] == 0) {
            $data['data'] = $this->accountancy->journalDetailList($filter);
        } else {
            $data['data'] = $this->accountancy->adjustingJournalDetailList($filter);
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('accountancy.ledger-detail-print', compact('data'));
    }
    public function ledgerDetailDownload()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            return redirect()->route('ledger')->with(['danger' => 'Data akun tidak ditemukan.']);
        }
        $id = $_GET['id'];
        $data['account'] = $this->accountancy->accountGet($id);
        if (!$data['account']) {
            return redirect()->route('ledger')->with(['danger' => 'Data akun tidak ditemukan.']);
        }
        if (!isset($_GET['type']) || $_GET['type'] == '') {
            return redirect()->route('ledger')->with(['danger' => 'Menu tidak tersedia']);
        }
        $data['type'] = $_GET['type'];
        $filter['account_code'] = $data['account']->code;
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('ledgerDetail', ['id' => $data['account']->id])->with(['danger' => 'Tanggal tidak valid.']);
        }
        $data['beginning_balance'] = $data['account']->beginning_balance;
        if ($data['start_date'] != config('config_apps.journal_periode_start')) {
            $data['beginning_balance'] = $this->accountancy->getBeginningSaldo($data['account']->code, $data['start_date'], $data['type']);
        }
        if (isset($_GET['tbb_id'])) {
            $data['tbb_id'] = $filter['tbb_id'] = $_GET['tbb_id'];
            $tbb = $this->accountancy->closeMonthlyBookGet($data['tbb_id']);
            $data['start_date'] = $filter['start_date'] = $tbb->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbb->end_periode;
            $akun = json_decode($tbb->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
        }
        if (isset($_GET['tbt_id'])) {
            $data['tbt_id'] = $filter['tbt_id'] = $_GET['tbt_id'];
            $tbt = $this->accountancy->closeYearlyBookGet($data['tbt_id']);
            $data['start_date'] = $filter['start_date'] = $tbt->start_periode;
            $data['end_date'] = $filter['end_date'] = $tbt->end_periode;
            $akun = json_decode($tbt->data);
            foreach ($akun as $key => $value) {
                if ($value->code == $data['account']->code) {
                    $data['beginning_balance'] = $value->saldo_awal;
                    break;
                }
            }
        }
        if ($data['type'] == 0) {
            $data['data'] = $this->accountancy->journalDetailList($filter);
        } else {
            $data['data'] = $this->accountancy->adjustingJournalDetailList($filter);
        }
        $export['start_date'] = $data['start_date'];
        $export['end_date'] = $data['end_date'];
        $export['total_row'] = $data['data']->count();
        $export['beginning_balance'] = $data['beginning_balance'];
        $export['data'] = [];
        $i = 1;
        $export['balance'] = $data['beginning_balance'];
        foreach ($data['data'] as $key => $value) {
            if ($data['account']->type == 0) {
                $export['balance'] += $value->debit;
                $export['balance'] -= $value->kredit;
            } else {
                $export['balance'] -= $value->debit;
                $export['balance'] += $value->kredit;
            }
            $export['data'][$key]['no'] = $i++;
            $export['data'][$key]['transaction_date'] = $value->transaction_date;
            $export['data'][$key]['reference_number'] = $value->reference_number;
            $export['data'][$key]['name'] = $value->name;
            $export['data'][$key]['debit'] = number_format($value->debit, 2, ',', '.');
            $export['data'][$key]['kredit'] = number_format($value->kredit, 2, ',', '.');
            $export['data'][$key]['balance'] = number_format($export['balance'], 2, ',', '.');
        }
        $export['account'] = $data['account'];

        return Excel::download(new LedgerDetailExport($export), 'Buku Besar ' . $data['account']->code . '.xlsx');
    }
    /*
    * ========================================================================================== END BUKU BESAR ==========================================================================================
    */



    /*
    * ========================================================================================== START JURNAL TRANSAKSI ==========================================================================================
    */
    public function adjustingJournalList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');

        $data['active_menu'] = 'adjusting-journal';
        $data['breadcrumb'] = [
            'Jurnal Penyesuaian' => url()->current()
        ];

        $data['param'] = 'q=' . $data['q'];
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Jurnal Penyesuaian' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Jurnal Penyesuaian' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }

        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];

        $data['data'] = $this->accountancy->adjustingJournalList($data, $data['limit']);

        $data['session'] = session('adjusting-journal');
        if ($data['session'] != null) {
            if (isset($_GET['print']) && !empty($_GET['print'])) {
                $data['print'] = $this->accountancy->adjustingJournalGet($_GET['print']);
                $data['print']->amount = $data['print']->detail->sum('debit');
            }
        }
        session()->forget('adjusting-journal');

        $data['jumlah'] = $this->accountancy->adjustingJournalDetailSum($data);
        return view('accountancy.adjusting-journal-list', compact('data'));
    }
    public function adjustingJournalAdd()
    {
        $data['mode'] = 'add';
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['data'] = [
            'transaction_date' => date('Y-m-d'),
            'reference_number' => 'TRX-' . date('YmdHis')
        ];
        $data['rincian_atas'] = $data['rincian_bawah'] = 2;
        $data['active_menu'] = 'adjusting-journal';
        $data['breadcrumb'] = [
            'Jurnal Penyesuaian' => route('adjustingJournalList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.adjusting-journal-form', compact('data'));
    }
    public function adjustingJournalEdit($id)
    {
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['data'] = $this->accountancy->adjustingJournalGet($id);
        if (!$data['data']) {
            return redirect()->route('adjustingJournalList')->with(['warning' => 'Data jurnal penyesuaian tidak ditemukan.']);
        }
        if ($data['data']->deleted == 1) {
            return redirect()->route('adjustingJournalList')->with(['warning' => 'Data jurnal penyesuaian tidak ditemukan.']);
        }
        //mode
        $data['mode'] = 'edit';

        $data['active_menu'] = 'adjusting-journal';
        $data['breadcrumb'] = [
            'Jurnal Penyesuaian' => route('journalList'),
            'Edit: ' . $data['data']->reference_number => url()->current(),
        ];
        $data['rincian_atas'] = $data['data']->detail->where('debit', '>', 0)->count() + 1;
        $data['rincian_bawah'] = $data['data']->detail->where('kredit', '>', 0)->count() + 1;
        return view('accountancy.adjusting-journal-form', compact('data'));
    }
    protected function adjustingJournalSave(JournalRequest $request)
    {
        $data = $request->validated();
        $data['transaction_date'] = date('Y-m-d', strtotime($data['transaction_date'])) . ' ' . date('H:i:s');

        if ($request->mode == 'add') {
            $data['detail'] = [];
            $top_account = $top_type = $top_amount = [];
            foreach ($data['top_account'] as $key => $value) {
                $top_account[] = $value;
            }
            foreach ($data['top_type'] as $key => $value) {
                $top_type[] = $value;
            }
            foreach ($data['top_amount'] as $key => $value) {
                $top_amount[] = $value;
            }
            for ($i = 0; $i < count($top_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $top_account[$i],
                    'type' => $top_type[$i],
                    'amount' => str_replace(',', '', $top_amount[$i]),
                ];
            }
            $bottom_account = $bottom_type = $bottom_amount = [];
            foreach ($data['bottom_account'] as $key => $value) {
                $bottom_account[] = $value;
            }
            foreach ($data['bottom_type'] as $key => $value) {
                $bottom_type[] = $value;
            }
            foreach ($data['bottom_amount'] as $key => $value) {
                $bottom_amount[] = $value;
            }
            for ($i = 0; $i < count($bottom_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $bottom_account[$i],
                    'type' => $bottom_type[$i],
                    'amount' => str_replace(',', '', $bottom_amount[$i]),
                ];
            }

            unset($data['top_account'], $data['top_type'], $data['top_amount'], $data['bottom_account'], $data['bottom_type'], $data['bottom_amount']);
            $type = 'adjusting';

            if (!$this->accountancy->adjustingJournalSave($data, $type)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }

            $last_journal_id = $this->accountancy->last_adjusting_journal_id;

            $message = 'Jurnal penyesuaian berhasil di tambahkan.';
            session(['adjusting-journal' => $this->accountancy->last_adjusting_journal_id]);
        } else {
            $top_account = $top_type = $top_amount = [];
            foreach ($data['top_account'] as $key => $value) {
                $top_account[] = $value;
            }
            foreach ($data['top_type'] as $key => $value) {
                $top_type[] = $value;
            }
            foreach ($data['top_amount'] as $key => $value) {
                $top_amount[] = $value;
            }
            for ($i = 0; $i < count($top_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $top_account[$i],
                    'type' => $top_type[$i],
                    'amount' => str_replace(',', '', $top_amount[$i]),
                ];
            }
            $bottom_account = $bottom_type = $bottom_amount = [];
            foreach ($data['bottom_account'] as $key => $value) {
                $bottom_account[] = $value;
            }
            foreach ($data['bottom_type'] as $key => $value) {
                $bottom_type[] = $value;
            }
            foreach ($data['bottom_amount'] as $key => $value) {
                $bottom_amount[] = $value;
            }
            for ($i = 0; $i < count($bottom_account); $i++) {
                $data['detail'][] = [
                    'account_code' => $bottom_account[$i],
                    'type' => $bottom_type[$i],
                    'amount' => str_replace(',', '', $bottom_amount[$i]),
                ];
            }
            unset($data['top_account'], $data['top_type'], $data['top_amount'], $data['bottom_account'], $data['bottom_type'], $data['bottom_amount']);

            if (!$this->accountancy->adjustingJournalUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->accountancy->error]);
            }
            $journal = $this->accountancy->journalGet(['adjusting_journal_id', [$request->id]]);
            if ($journal) {
                $this->accountancy->journalUpdate($journal->id);
            }
            $message = 'Jurnal penyesuaian berhasil diperbaharui.';
            $last_journal_id = '';
        }
        return redirect()->route('adjustingJournalList', ['print' => $last_journal_id])->with(['success' => $message]);
    }
    public function adjustingJournalDelete($id)
    {
        $adjustingJournal = $this->accountancy->adjustingJournalGet($id);
        if ($adjustingJournal == false) {
            return redirect()->route('adjustingJournalList')->with(['warning' => 'Data jurnal penyesuaian tidak ditemukan.']);
        }
        $this->accountancy->adjustingJournalDelete($adjustingJournal->id);
        $journal = $this->accountancy->journalGet(['adjusting_journal_id', $id]);

        if ($journal) {
            $this->accountancy->journalDelete($journal->id);
        }
        return redirect()->route('adjustingJournalList')->with(['success' => 'Data jurnal penyesuaian berhasil dihapus.']);
    }
    public function adjustingJournalPrint()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
        }
        $data['data'] = $this->accountancy->adjustingJournalList($data);
        $data['jumlah'] = $this->accountancy->adjustingJournalDetailSum($data);
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('accountancy.adjusting-journal-print', compact('data'));
    }
    /*
    * ========================================================================================== END JURNAL TRANSAKSI ==========================================================================================
    */



    /*
    * ========================================================================================== START TUTUP BUKU BULANAN ==========================================================================================
    */
    public function closeBookList()
    {
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => url()->current()
        ];
        return view('accountancy.close-book-list', compact('data'));
    }
    public function closeMonthlyBookList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->closeMonthlyBookList($data, $data['limit']);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Bulanan' => url()->current(),
        ];
        return view('accountancy.close-monthly-book-list', compact('data'));
    }
    public function closeMonthlyBookAdd()
    {
        $data['start_periode'] = date('Y-m-01');
        $data['end_periode'] = date('Y-m-t');
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Bulanan' => route('closeMonthlyBookList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.close-monthly-book-form', compact('data'));
    }
    public function closeMonthlyBookPreview(Request $request)
    {
        $data = $request->validate([
            'closing_date' => 'required',
            'start_periode' => 'required',
            'end_periode' => 'required',
            'description' => 'nullable'
        ]);
        $start_periode = config('config_apps.journal_periode_start');
        $data['data'] = $this->accountancy->ledger(['start_date' => $start_periode, 'end_date' => $data['end_periode']], true);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Bulanan' => route('closeMonthlyBookList'),
            'Tambah' => route('closeMonthlyBookAdd'),
        ];
        return view('accountancy.close-monthly-book-preview', compact('data'));
    }
    public function closeMonthlyBookConfirm(Request $request)
    {
        $data = $request->validate([
            'closing_date' => 'required',
            'start_periode' => 'required',
            'end_periode' => 'required',
            'description' => 'nullable'
        ]);
        $this->accountancy->closeMonthlyBookSave($data);
        return redirect()->route('closeMonthlyBookList')->with(['success' => 'Data tutup buku berhasil disimpan.']);
    }
    public function closeMonthlyBookDetail($id)
    {
        $data['data'] = $this->accountancy->closeMonthlyBookGet($id);
        $data['account'] = json_decode($data['data']->data);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Bulanan' => route('closeMonthlyBookList'),
            'Detail' => url()->current(),
        ];
        return view('accountancy.close-monthly-book-detail', compact('data'));
    }
    /*
    * ========================================================================================== END TUTUP BUKU BULANAN ==========================================================================================
    */



    /*
    * ========================================================================================== START TUTUP BUKU TAHUNAN ==========================================================================================
    */
    public function closeYearlyBookList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->closeYearlyBookList($data, $data['limit']);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Tahunan' => url()->current(),
        ];
        return view('accountancy.close-yearly-book-list', compact('data'));
    }
    public function closeYearlyBookAdd()
    {
        $data['start_periode'] = config('config_apps.journal_periode_start');
        $data['end_periode'] = config('config_apps.journal_periode_end');
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Tahunan' => route('closeYearlyBookList'),
            'Tambah' => url()->current(),
        ];
        return view('accountancy.close-yearly-book-form', compact('data'));
    }
    public function closeYearlyBookPreview(Request $request)
    {
        $data = $request->validate([
            'closing_date' => 'required',
            'start_periode' => 'required',
            'end_periode' => 'required',
            'description' => 'nullable'
        ]);
        $data['data'] = $this->accountancy->ledger(['start_date' => $data['start_periode'], 'end_date' => $data['end_periode']]);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Tahunan' => route('closeYearlyBookList'),
            'Tambah' => route('closeYearlyBookAdd'),
        ];
        return view('accountancy.close-yearly-book-preview', compact('data'));
    }
    public function closeYearlyBookConfirm(Request $request)
    {
        $data = $request->validate([
            'closing_date' => 'required',
            'start_periode' => 'required',
            'end_periode' => 'required',
            'description' => 'nullable'
        ]);
        $this->accountancy->closeYearlyBookSave($data);
        return redirect()->route('closeYearlyBookList')->with(['success' => 'Data tutup buku berhasil disimpan.']);
    }
    public function closeYearlyBookDetail($id)
    {
        $data['data'] = $this->accountancy->closeYearlyBookGet($id);
        $data['account'] = json_decode($data['data']->data);
        $data['active_menu'] = 'close-book';
        $data['breadcrumb'] = [
            'Tutup Buku' => route('closeBookList'),
            'Tahunan' => route('closeYearlyBookList'),
            'Detail' => url()->current(),
        ];
        return view('accountancy.close-yearly-book-detail', compact('data'));
    }
    /*
    * ========================================================================================== END TUTUP BUKU TAHUNAN ==========================================================================================
    */
}