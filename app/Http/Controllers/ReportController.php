<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\DepositClass;
use App\Classes\MasterClass;
use App\Classes\StoreClass;
use App\Exports\BalanceDescriptionExport;
use App\Exports\BalanceExport;
use App\Exports\CashflowExport;
use App\Exports\LaporanHarianExport;
use App\Exports\LaporanKasBankExport;
use App\Exports\PerubahanModalExport;
use App\Exports\PhuExport;
use App\Exports\ShuAnggotaExport;
use App\Exports\ShuExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private $accountancy, $master, $deposit, $store;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Laporan');

        $this->accountancy = new AccountancyClass();
        $this->master = new MasterClass();
        $this->deposit = new DepositClass();
        $this->store = new StoreClass();
        
        ini_set('memory_limit','5524M');
        set_time_limit(10000);
    }



    /*
    * ========================================================================================== START NERACA ==========================================================================================
    */
    public function balance()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'group';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
        $data['data'] = $this->accountancy->ledger($data);
        $data['active_menu'] = 'balance';
        $data['breadcrumb'] = [
            'Neraca' => route('balance')
        ];
        $data['param'] = 'view=' . $data['view'];

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['data'] = json_decode($tbb->data);
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Neraca' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['data'] = json_decode($tbt->data);
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Neraca' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }
        $data['param'] .= '&end_date=' . $data['end_date'];
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]['saldo_tahun_lalu'] += $hasil->saldo_tahun_lalu;
                }
            }
        }

        return view('report.balance', compact('data'));
    }
    public function balancePrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'group';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
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
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('report.balance-print', compact('data'));
    }
    public function balanceDownload()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'group';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
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
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;


        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }


        if ($data['view'] == 'all') {
            // Aktiva Lancar
            $no_aktiva = 0;
            $row_aktiva_lancar = 0;
            $export[$no_aktiva] = [
                'I',
                'Aktiva Lancar',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_aktiva_lancar = $total_aktiva_lancar_lalu = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 1 && $value->code[4] == 1 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_aktiva++;
                    $row_aktiva_lancar++;
                    if ($value->type == 0) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_aktiva_lancar += $saldo;
                    $total_aktiva_lancar_lalu += $saldo_lalu;

                    $export[$no_aktiva] = [
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Aktiva Lancar',
                '',
                '',
                ($total_aktiva_lancar >= 0 ? number_format($total_aktiva_lancar, 2, ',', '.') : '(' . number_format($total_aktiva_lancar * -1, 2, ',', '.') . ')'),
                ($total_aktiva_lancar_lalu >= 0 ? number_format($total_aktiva_lancar_lalu, 2, ',', '.') : '(' . number_format($total_aktiva_lancar_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                '',
                ''
            ];
            // Investasi
            $no_aktiva++;
            $export[$no_aktiva] = [
                'II',
                'Investasi',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_investasi = $total_investasi_lalu = 0;
            $row_investasi = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 1 && $value->code[4] == 2 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_aktiva++;
                    $row_investasi++;
                    if ($value->type == 0) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_investasi += $saldo;
                    $total_investasi_lalu += $saldo_lalu;

                    $export[$no_aktiva] = [
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Investasi',
                '',
                '',
                ($total_investasi >= 0 ? number_format($total_investasi, 2, ',', '.') : '(' . number_format($total_investasi * -1, 2, ',', '.') . ')'),
                ($total_investasi_lalu >= 0 ? number_format($total_investasi_lalu, 2, ',', '.') : '(' . number_format($total_investasi_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                '',
                ''
            ];
            // Aktiva Tetap
            $no_aktiva++;
            $export[$no_aktiva] = [
                'III',
                'Aktiva Tetap',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_aktiva_tetap = $total_aktiva_tetap_lalu = 0;
            $row_aktiva_tetap = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 1 && $value->code[4] == 3 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_aktiva++;
                    $row_aktiva_tetap++;
                    if ($value->type == 0) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_aktiva_tetap += $saldo;
                    $total_aktiva_tetap_lalu += $saldo_lalu;

                    $export[$no_aktiva] = [
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Aktiva Tetap',
                '',
                '',
                ($total_aktiva_tetap >= 0 ? number_format($total_aktiva_tetap, 2, ',', '.') : '(' . number_format($total_aktiva_tetap * -1, 2, ',', '.') . ')'),
                ($total_aktiva_tetap_lalu >= 0 ? number_format($total_aktiva_tetap_lalu, 2, ',', '.') : '(' . number_format($total_aktiva_tetap_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                '',
                ''
            ];
            // Kewajiban Jangka Pendek
            $no_pasiva = 0;
            $export[$no_pasiva] = [
                $export[$no_pasiva][0],
                $export[$no_pasiva][1],
                $export[$no_pasiva][2],
                $export[$no_pasiva][3],
                $export[$no_pasiva][4],
                'IV',
                'Kewajiban Jangka Pendek',
                '',
                '',
                ''
            ];
            $total_kewajiban_jk_pendek = $total_kewajiban_jk_pendek_lalu = 0;
            $row_kewajiban_jk_pendek = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 2 && $value->code[4] == 1 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_pasiva++;
                    $row_kewajiban_jk_pendek++;
                    if ($value->type == 1) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_kewajiban_jk_pendek += $saldo;
                    $total_kewajiban_jk_pendek_lalu += $saldo_lalu;

                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'Total Kewajiban Jangka Pendek',
                '',
                '',
                ($total_kewajiban_jk_pendek >= 0 ? number_format($total_kewajiban_jk_pendek, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_pendek * -1, 2, ',', '.') . ')'),
                ($total_kewajiban_jk_pendek_lalu >= 0 ? number_format($total_kewajiban_jk_pendek_lalu, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_pendek_lalu * -1, 2, ',', '.') . ')'),
            ];
            // Kewajiban Jangka Panjang
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'V',
                'Kewajiban Jangka Panjang',
                '',
                '',
                ''
            ];
            $total_kewajiban_jk_panjang = $total_kewajiban_jk_panjang_lalu = 0;
            $row_kewajiban_jk_panjang = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 2 && $value->code[4] == 2 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_pasiva++;
                    $row_kewajiban_jk_panjang++;
                    if ($value->type == 1) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_kewajiban_jk_panjang += $saldo;
                    $total_kewajiban_jk_panjang_lalu += $saldo_lalu;

                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'Total Kewajiban Jangka Panjang',
                '',
                '',
                ($total_kewajiban_jk_panjang >= 0 ? number_format($total_kewajiban_jk_panjang, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_panjang * -1, 2, ',', '.') . ')'),
                ($total_kewajiban_jk_panjang_lalu >= 0 ? number_format($total_kewajiban_jk_panjang_lalu, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_panjang_lalu * -1, 2, ',', '.') . ')'),
            ];
            // Modal
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'VI',
                'Modal',
                '',
                '',
                ''
            ];
            $total_modal = $total_modal_lalu = 0;
            $row_modal = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 3 && $value->code[4] != 4 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_pasiva++;
                    $row_modal++;
                    if ($value->type == 1) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_modal += $saldo;
                    $total_modal_lalu += $saldo_lalu;

                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'Total Modal',
                '',
                '',
                ($total_modal >= 0 ? number_format($total_modal, 2, ',', '.') : '(' . number_format($total_modal * -1, 2, ',', '.') . ')'),
                ($total_modal_lalu >= 0 ? number_format($total_modal_lalu, 2, ',', '.') : '(' . number_format($total_modal_lalu * -1, 2, ',', '.') . ')'),
            ];

            // PHU
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'VII',
                'PHU',
                '',
                '',
                ''
            ];
            $total_phu = $total_phu_lalu = 0;
            $row_phu = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->code[1] == 3 && $value->code[4] == 4 && ($value->saldo_penyesuaian != 0 || $value->saldo_tahun_lalu != 0)) {
                    $no_pasiva++;
                    $row_phu++;
                    if ($value->type == 1) {
                        $saldo = $value->saldo_penyesuaian;
                        $saldo_lalu = $value->saldo_tahun_lalu;
                    } else {
                        $saldo = $value->saldo_penyesuaian * -1;
                        $saldo_lalu = $value->saldo_tahun_lalu * -1;
                    }
                    $total_phu += $saldo;
                    $total_phu_lalu += $saldo_lalu;

                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                        '',
                        $value->code,
                        $value->name,
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][4] : '',
                'Total PHU',
                '',
                '',
                ($total_phu >= 0 ? number_format($total_phu, 2, ',', '.') : '(' . number_format($total_phu * -1, 2, ',', '.') . ')'),
                ($total_phu_lalu >= 0 ? number_format($total_phu_lalu, 2, ',', '.') : '(' . number_format($total_phu_lalu * -1, 2, ',', '.') . ')'),
            ];

            // total aktiva
            $total_aktiva = $total_aktiva_lancar + $total_aktiva_tetap + $total_investasi;
            if ($total_aktiva >= 0) {
                $aktiva = number_format($total_aktiva, 2, ',', '.');
            } else {
                $aktiva = '(' . number_format($total_aktiva * -1, 2, ',', '.') . ')';
            }
            $total_aktiva_lalu = $total_aktiva_lancar_lalu + $total_aktiva_tetap_lalu + $total_investasi_lalu;
            if ($total_aktiva_lalu >= 0) {
                $aktiva_lalu = number_format($total_aktiva_lalu, 2, ',', '.');
            } else {
                $aktiva_lalu = '(' . number_format($total_aktiva_lalu * -1, 2, ',', '.') . ')';
            }
            // total pasiva
            $total_pasiva = $total_kewajiban_jk_panjang + $total_kewajiban_jk_pendek + $total_modal + $total_phu;
            if ($total_pasiva >= 0) {
                $pasiva = number_format($total_pasiva, 2, ',', '.');
            } else {
                $pasiva = '(' . number_format($total_pasiva * -1, 2, ',', '.') . ')';
            }
            $total_pasiva_lalu = $total_kewajiban_jk_panjang_lalu + $total_kewajiban_jk_pendek_lalu + $total_modal_lalu + $total_phu_lalu;
            if ($total_pasiva_lalu >= 0) {
                $pasiva_lalu = number_format($total_pasiva_lalu, 2, ',', '.');
            } else {
                $pasiva_lalu = '(' . number_format($total_pasiva_lalu * -1, 2, ',', '.') . ')';
            }
            $no_aktiva++;
            $no_pasiva++;
            if ($no_pasiva >= $no_aktiva) {
                $export[$no_pasiva] = [
                    'Total Aktiva',
                    '',
                    '',
                    $aktiva,
                    $aktiva_lalu,
                    'Total Pasiva',
                    '',
                    '',
                    $pasiva,
                    $pasiva_lalu,
                ];
            } else {
                $export[$no_aktiva] = [
                    'Total Aktiva',
                    '',
                    '',
                    $aktiva,
                    $aktiva_lalu,
                    'Total Pasiva',
                    '',
                    '',
                    $pasiva,
                    $pasiva_lalu,
                ];
            }
        } else {
            // Aktiva Lancar
            $no_aktiva = 0;
            $row_aktiva_lancar = 0;
            $export[$no_aktiva] = [
                'I',
                'Aktiva Lancar',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_aktiva_lancar = $total_aktiva_lancar_lalu = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 6 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_aktiva++;
                    $row_aktiva_lancar++;
                    if ($value['type'] == 0) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_aktiva_lancar += $saldo;
                    $total_aktiva_lancar_lalu += $saldo_lalu;

                    $export[$no_aktiva] = [
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Aktiva Lancar',
                '',
                ($total_aktiva_lancar >= 0 ? number_format($total_aktiva_lancar, 2, ',', '.') : '(' . number_format($total_aktiva_lancar * -1, 2, ',', '.') . ')'),
                ($total_aktiva_lancar_lalu >= 0 ? number_format($total_aktiva_lancar_lalu, 2, ',', '.') : '(' . number_format($total_aktiva_lancar_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                ''
            ];
            // Investasi
            $no_aktiva++;
            $export[$no_aktiva] = [
                'II',
                'Investasi',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_investasi = $total_investasi_lalu = 0;
            $row_investasi = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 7 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_aktiva++;
                    $row_investasi++;
                    if ($value['type'] == 0) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_investasi += $saldo;
                    $total_investasi_lalu += $saldo_lalu;

                    $export[$no_aktiva] = [
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Investasi',
                '',
                ($total_investasi >= 0 ? number_format($total_investasi, 2, ',', '.') : '(' . number_format($total_investasi * -1, 2, ',', '.') . ')'),
                ($total_investasi_lalu >= 0 ? number_format($total_investasi_lalu, 2, ',', '.') : '(' . number_format($total_investasi_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                ''
            ];
            // Aktiva Tetap
            $no_aktiva++;
            $export[$no_aktiva] = [
                'III',
                'Aktiva Tetap',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $total_aktiva_tetap = $total_aktiva_tetap_lalu = 0;
            $row_aktiva_tetap = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 8 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_aktiva++;
                    $row_aktiva_tetap++;
                    if ($value['type'] == 0) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_aktiva_tetap += $saldo;
                    $total_aktiva_tetap_lalu += $saldo_lalu;
                    $export[$no_aktiva] = [
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
            $no_aktiva++;
            $export[$no_aktiva] = [
                'Total Aktiva Tetap',
                '',
                ($total_aktiva_tetap >= 0 ? number_format($total_aktiva_tetap, 2, ',', '.') : '(' . number_format($total_aktiva_tetap * -1, 2, ',', '.') . ')'),
                ($total_aktiva_tetap_lalu >= 0 ? number_format($total_aktiva_tetap_lalu, 2, ',', '.') : '(' . number_format($total_aktiva_tetap_lalu * -1, 2, ',', '.') . ')'),
                '',
                '',
                '',
                ''
            ];
            // Kewajiban Jangka Pendek
            $no_pasiva = 0;
            $export[$no_pasiva] = [
                $export[$no_pasiva][0],
                $export[$no_pasiva][1],
                $export[$no_pasiva][2],
                $export[$no_pasiva][3],
                'IV',
                'Kewajiban Jangka Pendek',
                '',
                '',
            ];
            $total_kewajiban_jk_pendek = $total_kewajiban_jk_pendek_lalu = 0;
            $row_kewajiban_jk_pendek = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 9 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_pasiva++;
                    $row_kewajiban_jk_pendek++;
                    if ($value['type'] == 1) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_kewajiban_jk_pendek += $saldo;
                    $total_kewajiban_jk_pendek_lalu += $saldo_lalu;
                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'Total Kewajiban Jangka Pendek',
                '',
                ($total_kewajiban_jk_pendek >= 0 ? number_format($total_kewajiban_jk_pendek, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_pendek * -1, 2, ',', '.') . ')'),
                ($total_kewajiban_jk_pendek_lalu >= 0 ? number_format($total_kewajiban_jk_pendek_lalu, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_pendek_lalu * -1, 2, ',', '.') . ')'),
            ];
            // Kewajiban Jangka Panjang
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'V',
                'Kewajiban Jangka Panjang',
                '',
                ''
            ];
            $total_kewajiban_jk_panjang = $total_kewajiban_jk_panjang_lalu = 0;
            $row_kewajiban_jk_panjang = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 10 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_pasiva++;
                    $row_kewajiban_jk_panjang++;
                    if ($value['type'] == 1) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_kewajiban_jk_panjang += $saldo;
                    $total_kewajiban_jk_panjang_lalu += $saldo_lalu;
                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'Total Kewajiban Jangka Panjang',
                '',
                ($total_kewajiban_jk_panjang >= 0 ? number_format($total_kewajiban_jk_panjang, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_panjang * -1, 2, ',', '.') . ')'),
                ($total_kewajiban_jk_panjang_lalu >= 0 ? number_format($total_kewajiban_jk_panjang_lalu, 2, ',', '.') : '(' . number_format($total_kewajiban_jk_panjang_lalu * -1, 2, ',', '.') . ')'),
            ];
            // Modal
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'VI',
                'Modal',
                '',
                '',
            ];
            $total_modal = $total_modal_lalu = 0;
            $row_modal = 0;
            foreach ($data['group'] as $key => $value) {
                if (in_array($value->account_id, [11, 12, 13]) && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_pasiva++;
                    $row_modal++;
                    if ($value['type'] == 1) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_modal += $saldo;
                    $total_modal_lalu += $saldo_lalu;
                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'Total Modal',
                '',
                ($total_modal >= 0 ? number_format($total_modal, 2, ',', '.') : '(' . number_format($total_modal * -1, 2, ',', '.') . ')'),
                ($total_modal_lalu >= 0 ? number_format($total_modal_lalu, 2, ',', '.') : '(' . number_format($total_modal_lalu * -1, 2, ',', '.') . ')'),
            ];
            // PHU
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'VII',
                'PHU',
                '',
                '',
            ];
            $total_phu = $total_phu_lalu = 0;
            $row_phu = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value['account_id'] == 14 && ($value['saldo'] != 0 || $value['saldo_tahun_lalu'] != 0)) {
                    $no_pasiva++;
                    $row_phu++;
                    if ($value['type'] == 1) {
                        $saldo = $value['saldo'];
                        $saldo_lalu = $value['saldo_tahun_lalu'];
                    } else {
                        $saldo = $value['saldo'] * -1;
                        $saldo_lalu = $value['saldo_tahun_lalu'] * -1;
                    }
                    $total_phu += $saldo;
                    $total_phu_lalu += $saldo_lalu;
                    $export[$no_pasiva] = [
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                        isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                        '',
                        $value['name'],
                        ($saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'),
                        ($saldo_lalu >= 0 ? number_format($saldo_lalu, 2, ',', '.') : '(' . number_format($saldo_lalu * -1, 2, ',', '.') . ')'),
                    ];
                }
            }
            $no_pasiva++;
            $export[$no_pasiva] = [
                isset($export[$no_pasiva]) ? $export[$no_pasiva][0] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][1] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][2] : '',
                isset($export[$no_pasiva]) ? $export[$no_pasiva][3] : '',
                'Total PHU',
                '',
                ($total_phu >= 0 ? number_format($total_phu, 2, ',', '.') : '(' . number_format($total_phu * -1, 2, ',', '.') . ')'),
                ($total_phu_lalu >= 0 ? number_format($total_phu_lalu, 2, ',', '.') : '(' . number_format($total_phu_lalu * -1, 2, ',', '.') . ')'),
            ];
            // total aktiva
            $total_aktiva = $total_aktiva_lancar + $total_aktiva_tetap + $total_investasi;
            if ($total_aktiva >= 0) {
                $aktiva = number_format($total_aktiva, 2, ',', '.');
            } else {
                $aktiva = '(' . number_format($total_aktiva * -1, 2, ',', '.') . ')';
            }
            $total_aktiva_lalu = $total_aktiva_lancar_lalu + $total_aktiva_tetap_lalu + $total_investasi_lalu;
            if ($total_aktiva_lalu >= 0) {
                $aktiva_lalu = number_format($total_aktiva_lalu, 2, ',', '.');
            } else {
                $aktiva_lalu = '(' . number_format($total_aktiva_lalu * -1, 2, ',', '.') . ')';
            }
            // total pasiva
            $total_pasiva = $total_kewajiban_jk_panjang + $total_kewajiban_jk_pendek + $total_modal + $total_phu;
            if ($total_pasiva >= 0) {
                $pasiva = number_format($total_pasiva, 2, ',', '.');
            } else {
                $pasiva = '(' . number_format($total_pasiva * -1, 2, ',', '.') . ')';
            }
            $total_pasiva_lalu = $total_kewajiban_jk_panjang_lalu + $total_kewajiban_jk_pendek_lalu + $total_modal_lalu + $total_phu_lalu;
            if ($total_pasiva_lalu >= 0) {
                $pasiva_lalu = number_format($total_pasiva_lalu, 2, ',', '.');
            } else {
                $pasiva_lalu = '(' . number_format($total_pasiva_lalu * -1, 2, ',', '.') . ')';
            }
            $no_aktiva++;
            $no_pasiva++;
            if ($no_pasiva >= $no_aktiva) {
                $export[$no_pasiva] = [
                    'Total Aktiva',
                    '',
                    $aktiva,
                    $aktiva_lalu,
                    'Total Pasiva',
                    '',
                    $pasiva,
                    $pasiva_lalu,
                ];
            } else {
                $export[$no_aktiva] = [
                    'Total Aktiva',
                    '',
                    $aktiva,
                    $aktiva_lalu,
                    'Total Pasiva',
                    '',
                    $pasiva,
                    $pasiva_lalu,
                ];
            }
        }


        $download['end_date'] = $data['end_date'];
        $download['start_date'] = $data['start_date'];
        $download['total_row'] = count($export);
        $download['data'] = $export;
        $download['row_aktiva_lancar'] = $row_aktiva_lancar;
        $download['row_investasi'] = $row_investasi;
        $download['row_aktiva_tetap'] = $row_aktiva_tetap;
        $download['row_kewajiban_jk_pendek'] = $row_kewajiban_jk_pendek;
        $download['row_kewajiban_jk_panjang'] = $row_kewajiban_jk_panjang;
        $download['row_kewajiban_jk_panjang'] = $row_kewajiban_jk_panjang;
        $download['row_modal'] = $row_modal;
        $download['row_phu'] = $row_phu;
        $download['view'] = $data['view'];

        return Excel::download(new BalanceExport($download), 'Neraca ' . date('d M Y', strtotime($data['end_date'])) . '.xlsx');
    }
    public function balanceDescription()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
        $data['data'] = $this->accountancy->ledger($data);

        $data['active_menu'] = 'balance';
        $data['breadcrumb'] = [
            'Neraca' => route('balance'),
            'Penjelasan' => url()->current(),
        ];

        $data['param'] = '';

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['tbb_id'] = $tbb->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
            $data['data'] = json_decode($tbb->data);
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Neraca' => route('balance', ['tbb_id' => $data['tbb_id']]),
                'Penjelasan' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['tbt_id'] = $tbt->id;
            $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
            $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
            $data['data'] = json_decode($tbt->data);
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Neraca' => route('balance', ['tbt_id' => $data['tbt_id']]),
                'Penjelasan' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }
        $data['param'] .= '&end_date=' . $data['end_date'];
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]->saldo = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]->saldo += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }
        return view('report.balance-description', compact('data'));
    }
    public function balanceDescriptionPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = 'group';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
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
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }
        $data['assignment'] = $this->master->pengurusAssignment();

        return view('report.balance-description-print', compact('data'));
    }
    public function balanceDescriptionDownload()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = 'group';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
        }
        $shu_account = config('config_apps.shu_account');
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
        $shu = $shu_lalu = 0;
        $index = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                } else {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $shu -= $value->saldo_penyesuaian;
                    $shu_lalu -= $value->saldo_tahun_lalu;
                } else {
                    $shu += $value->saldo_penyesuaian;
                    $shu_lalu += $value->saldo_tahun_lalu;
                }
            }
            if ($value->code == $shu_account) {
                $index = $key;
            }
        }
        $data['data'][$index]->saldo_penyesuaian += $shu;
        $data['data'][$index]->saldo_tahun_lalu += $shu_lalu;

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }

        $export['end_date'] = $data['end_date'];
        // Aktiva Lancar
        $export['data'][] = [
            'I.',
            'Aktiva Lancar',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 6 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 0) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '1.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // Investasi
        $export['data'][] = [
            'II.',
            'Investasi',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 7 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 0) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '2.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // Aktiva Tetap
        $export['data'][] = [
            'III.',
            'Aktiva Tetap',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 8 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 0) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '3.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // Kewajiban Jangka Pendek
        $export['data'][] = [
            'IV.',
            'Kewajiban Jangka Pendek',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 9 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 1) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '4.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // Kewajiban Jangka Panjang
        $export['data'][] = [
            'V.',
            'Kewajiban Jangka Panjang',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 10 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 1) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '5.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // Modal
        $export['data'][] = [
            'VI.',
            'Modal',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if (in_array($value->account_id, [11, 12, 13]) && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 1) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '6.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        // PHU
        $export['data'][] = [
            'VII.',
            'PHU',
            '',
            '',
        ];
        $i = 0;
        foreach ($data['group'] as $key => $value) {
            if ($value->account_id == 14 && $value->saldo != 0) {
                $deskripsi = str_replace('this.date', date('d M Y', strtotime($data['end_date'])), $value->description);
                $i++;
                if ($value->type == 1) {
                    $saldo = $value->saldo;
                } else {
                    $saldo = $value->saldo * -1;
                }
                $export['data'][] = [
                    '7.' . $i,
                    $value->name . (!empty($deskripsi) ? "\n" . $deskripsi : ''),
                    '',
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp' . number_format($saldo * -1, 2, ',', '.') . ')',
                ];
                foreach ($data['data'] as $hsl => $item) {
                    if ($item->group_id == $value->id && $item->saldo_penyesuaian != 0) {
                        $export['data'][] = [
                            '',
                            '- ' . $item->name,
                            $item->saldo_penyesuaian >= 0 ? 'Rp' . number_format($item->saldo_penyesuaian, 2, ',', '.') : '(Rp' . number_format($item->saldo_penyesuaian * -1, 2, ',', '.') . ')',
                            '',
                        ];
                    }
                }
            }
        }
        return Excel::download(new BalanceDescriptionExport($export), 'Penjelasan Neraca per ' . date('d M Y', strtotime($data['end_date'])) . '.xlsx');
    }
    /*
    * ========================================================================================== START NERACA ==========================================================================================
    */



    /*
    * ========================================================================================== START PHU ==========================================================================================
    */
    public function phu()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
        $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
        $akun = $this->accountancy->ledger($data);
        $data['active_menu'] = 'phu';
        $data['breadcrumb'] = [
            'Penjelasan PHU' => route('phu')
        ];
        $data['param'] = '';

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Penjelasan PHU' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Penjelasan PHU' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }

        $data['param'] .= '&bulan=' . $data['bulan'] . '&tahun=' . $data['tahun'];

        $data['penjualan_anggota'] = $data['penjualan_non_anggota'] = $data['biaya_biaya_usaha'] = $data['pendapatan_lain_lain'] = $data['biaya_lain_lain'] = [];
        $data['persediaan_awal'] = $data['persediaan_akhir'] = $data['pembelian_kedelai'] = $data['susut_kedelai'] = $data['shu_bulan_lalu'] = $data['retur_pembelian'] = 0;
        
        $total_pendapatan = $total_beban = $total_shu = ['saldo_awal' => 0, 'saldo_akhir' => 0];
        if(auth()->user()->id == 1){
            // dd($akun);
        }
        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
            foreach ($akun as $key => $value) {
                if ($value->code == $config['akun_persediaan']) {
                    $data['persediaan_awal'] = $value->beginning_balance;
                    $data['persediaan_akhir'] = $value->adjusting_balance;
                }
                if ($value->code == $config['akun_penjualan_anggota']) {
                    $data['penjualan_anggota'][] = $value;
                }
                if ($value->code == $config['akun_retur_penjualan_anggota']) {
                    $data['penjualan_anggota'][] = $value;
                }
                if ($value->code == $config['akun_penjualan_non_anggota']) {
                    $data['penjualan_non_anggota'][] = $value;
                }
                if ($value->code == $config['akun_retur_penjualan_non_anggota']) {
                    $data['penjualan_non_anggota'][] = $value;
                }
                if ($value->code == $config['akun_pembelian']) {
                    if ($value->type == 0) {
                        $data['pembelian_kedelai'] = $value->adjusting_balance;
                    } else {
                        $data['pembelian_kedelai'] = $value->adjusting_balance * -1;
                    }
                }
                /*if ($value->code == $config['akun_susut_pembelian']) {
                    if ($value->type == 0) {
                        $data['susut_kedelai'] = $value->saldo_penyesuaian;
                    } else {
                        $data['susut_kedelai'] = $value->saldo_penyesuaian * -1;
                    }
                }*/
                if ($value->code == $config['akun_retur_pembelian']) {
                    if ($value->type == 1) {
                        $data['retur_pembelian'] = $value->adjusting_balance;
                    } else {
                        $data['retur_pembelian'] = $value->adjusting_balance * -1;
                    }
                }
                if ($value->code[1] == 5 && ($value->code[4] == 2 || $value->code[4] == 3)) {
                    $data['biaya_biaya_usaha'][] = $value;
                }
                if ($value->code[1] == 4 && $value->code[4] == 5) {
                    $data['pendapatan_lain_lain'][] = $value;
                }
                if ($value->code[1] == 5 && $value->code[4] == 4) {
                    $data['biaya_lain_lain'][] = $value;
                }
                if ($value->code == $shu_account) {
                    $data['shu_bulan_lalu'] += $value->beginning_balance;
                    $total_shu['saldo_awal'] = $value->beginning_balance;
                    $total_shu['saldo_akhir'] = $value->adjusting_balance;
                }
                if ($value->code[1] == 4) {
                    if ($value->type == 1) {
                        $data['shu_bulan_lalu'] += $value->beginning_balance;
                        $total_pendapatan['saldo_awal'] += $value->beginning_balance;
                        $total_pendapatan['saldo_akhir'] += $value->adjusting_balance;
                    } else {
                        $data['shu_bulan_lalu'] -= $value->beginning_balance;
                        $total_pendapatan['saldo_awal'] -= $value->beginning_balance;
                        $total_pendapatan['saldo_akhir'] -= $value->adjusting_balance;
                    }
                }
                if ($value->code[1] == 5) {
                    if ($value->type == 0) {
                        $data['shu_bulan_lalu'] += $value->beginning_balance;
                        $total_beban['saldo_awal'] += $value->beginning_balance;
                        $total_beban['saldo_akhir'] += $value->adjusting_balance;
                    } else {
                        $data['shu_bulan_lalu'] -= $value->beginning_balance;
                        $total_beban['saldo_awal'] -= $value->beginning_balance;
                        $total_beban['saldo_akhir'] -= $value->adjusting_balance;
                    }
                }
            }
        }else{
            foreach ($akun as $key => $value) {
                if ($value->code == $config['akun_persediaan']) {
                    $data['persediaan_awal'] = $value->beginning_balance;
                    $data['persediaan_akhir'] = $value['saldo_penyesuaian'];
                }
                if ($value->code == $config['akun_penjualan_anggota']) {
                    $data['penjualan_anggota'][] = $value;
                }
                if ($value->code == $config['akun_retur_penjualan_anggota']) {
                    $data['penjualan_anggota'][] = $value;
                }
                if ($value->code == $config['akun_penjualan_non_anggota']) {
                    $data['penjualan_non_anggota'][] = $value;
                }
                if ($value->code == $config['akun_retur_penjualan_non_anggota']) {
                    $data['penjualan_non_anggota'][] = $value;
                }
                if ($value->code == $config['akun_pembelian']) {
                    if ($value->type == 0) {
                        $data['pembelian_kedelai'] = $value->saldo_penyesuaian;
                    } else {
                        $data['pembelian_kedelai'] = $value->saldo_penyesuaian * -1;
                    }
                }
                /*if ($value->code == $config['akun_susut_pembelian']) {
                    if ($value->type == 0) {
                        $data['susut_kedelai'] = $value->saldo_penyesuaian;
                    } else {
                        $data['susut_kedelai'] = $value->saldo_penyesuaian * -1;
                    }
                }*/
                if ($value->code == $config['akun_retur_pembelian']) {
                    if ($value->type == 1) {
                        $data['retur_pembelian'] = $value->saldo_penyesuaian;
                    } else {
                        $data['retur_pembelian'] = $value->saldo_penyesuaian * -1;
                    }
                }
                if ($value->code[1] == 5 && ($value->code[4] == 2 || $value->code[4] == 3)) {
                    $data['biaya_biaya_usaha'][] = $value;
                }
                if ($value->code[1] == 4 && $value->code[4] == 5) {
                    $data['pendapatan_lain_lain'][] = $value;
                }
                if ($value->code[1] == 5 && $value->code[4] == 4) {
                    $data['biaya_lain_lain'][] = $value;
                }
                if ($value->code == $shu_account) {
                    $data['shu_bulan_lalu'] += $value->saldo_awal;
                    $total_shu['saldo_awal'] = $value->saldo_awal;
                    $total_shu['saldo_akhir'] = $value->saldo_penyesuaian;
                }
                if ($value->code[1] == 4) {
                    if ($value->type == 1) {
                        $data['shu_bulan_lalu'] += $value->saldo_awal;
                        $total_pendapatan['saldo_awal'] += $value->saldo_awal;
                        $total_pendapatan['saldo_akhir'] += $value->saldo_penyesuaian;
                    } else {
                        $data['shu_bulan_lalu'] -= $value->saldo_awal;
                        $total_pendapatan['saldo_awal'] -= $value->saldo_awal;
                        $total_pendapatan['saldo_akhir'] -= $value->saldo_penyesuaian;
                    }
                }
                if ($value->code[1] == 5) {
                    if ($value->type == 0) {
                        $data['shu_bulan_lalu'] += $value->saldo_awal;
                        $total_beban['saldo_awal'] += $value->saldo_awal;
                        $total_beban['saldo_akhir'] += $value->saldo_penyesuaian;
                    } else {
                        $data['shu_bulan_lalu'] -= $value->saldo_awal;
                        $total_beban['saldo_awal'] -= $value->saldo_awal;
                        $total_beban['saldo_akhir'] -= $value->saldo_penyesuaian;
                    }
                }
            }
        }
        
        
        $data['shu_bulan_lalu'] = $total_shu['saldo_awal'] + $total_pendapatan['saldo_awal'] - $total_beban['saldo_awal'];
          
        if(auth()->user()->id == 1){
            $data['shu_bulan_sekarang'] = $total_shu['saldo_akhir'] + $total_pendapatan['saldo_akhir'] - $total_beban['saldo_akhir'];
            $shu['awal'] = $total_shu['saldo_awal'] + $total_pendapatan['saldo_awal'] - $total_beban['saldo_awal'];
            $shu['akhir'] = $total_shu['saldo_akhir'] + $total_pendapatan['saldo_akhir'] - $total_beban['saldo_akhir'];
            // dd($data['shu_bulan_lalu'], $data['shu_bulan_sekarang']);
        }


        return view('report.phu', compact('data'));
    }
    public function phuPrint()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
        $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
        $akun = $this->accountancy->ledger($data);


        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }

        $data['penjualan_anggota'] = $data['penjualan_non_anggota'] = $data['biaya_biaya_usaha'] = $data['pendapatan_lain_lain'] = $data['biaya_lain_lain'] = [];
        $data['persediaan_awal'] = $data['persediaan_akhir'] = $data['pembelian_kedelai'] = $data['susut_kedelai'] = $data['shu_bulan_lalu'] = $data['retur_pembelian'] = 0;
        
        $total_pendapatan = $total_beban = $total_shu = ['saldo_awal' => 0, 'saldo_akhir' => 0];
        foreach ($akun as $key => $value) {
            if ($value->code == $config['akun_persediaan']) {
                $data['persediaan_awal'] = $value->beginning_balance;
                $data['persediaan_akhir'] = $value->saldo_penyesuaian;
            }
            if ($value->code == $config['akun_penjualan_anggota']) {
                $data['penjualan_anggota'][] = $value;
            }
            if ($value->code == $config['akun_retur_penjualan_anggota']) {
                $data['penjualan_anggota'][] = $value;
            }
            if ($value->code == $config['akun_penjualan_non_anggota']) {
                $data['penjualan_non_anggota'][] = $value;
            }
            if ($value->code == $config['akun_retur_penjualan_non_anggota']) {
                $data['penjualan_non_anggota'][] = $value;
            }
            if ($value->code == $config['akun_pembelian']) {
                if ($value->type == 0) {
                    $data['pembelian_kedelai'] = $value->saldo_penyesuaian;
                } else {
                    $data['pembelian_kedelai'] = $value->saldo_penyesuaian * -1;
                }
            }
            /*if ($value->code == $config['akun_susut_pembelian']) {
                if ($value->type == 0) {
                    $data['susut_kedelai'] = $value->saldo_penyesuaian;
                } else {
                    $data['susut_kedelai'] = $value->saldo_penyesuaian * -1;
                }
            }*/
            if ($value->code == $config['akun_retur_pembelian']) {
                if ($value->type == 1) {
                    $data['retur_pembelian'] = $value->saldo_penyesuaian;
                } else {
                    $data['retur_pembelian'] = $value->saldo_penyesuaian * -1;
                }
            }
            if ($value->code[1] == 5 && ($value->code[4] == 2 || $value->code[4] == 3)) {
                $data['biaya_biaya_usaha'][] = $value;
            }
            if ($value->code[1] == 4 && $value->code[4] == 5) {
                $data['pendapatan_lain_lain'][] = $value;
            }
            if ($value->code[1] == 5 && $value->code[4] == 4) {
                $data['biaya_lain_lain'][] = $value;
            }
            if ($value->code == $shu_account) {
                $data['shu_bulan_lalu'] += $value->saldo_awal;
                $total_shu['saldo_awal'] = $value->saldo_awal;
                $total_shu['saldo_akhir'] = $value->saldo_penyesuaian;
            }
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $data['shu_bulan_lalu'] += $value->saldo_awal;
                    $total_pendapatan['saldo_awal'] += $value->saldo_awal;
                    $total_pendapatan['saldo_akhir'] += $value->saldo_penyesuaian;
                } else {
                    $data['shu_bulan_lalu'] -= $value->saldo_awal;
                    $total_pendapatan['saldo_awal'] -= $value->saldo_awal;
                    $total_pendapatan['saldo_akhir'] -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $data['shu_bulan_lalu'] += $value->saldo_awal;
                    $total_beban['saldo_awal'] += $value->saldo_awal;
                    $total_beban['saldo_akhir'] += $value->saldo_penyesuaian;
                } else {
                    $data['shu_bulan_lalu'] -= $value->saldo_awal;
                    $total_beban['saldo_awal'] -= $value->saldo_awal;
                    $total_beban['saldo_akhir'] -= $value->saldo_penyesuaian;
                }
            }
        }
        $data['shu_bulan_lalu'] = $total_shu['saldo_awal'] + $total_pendapatan['saldo_awal'] - $total_beban['saldo_awal'];
        $data['assignment'] = $this->master->pengurusAssignment();

        return view('report.phu-print', compact('data'));
    }
    public function phuDownload()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
        $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
        $akun = $this->accountancy->ledger($data);

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }

        $data['penjualan_anggota'] = $data['penjualan_non_anggota'] = $data['biaya_biaya_usaha'] = $data['pendapatan_lain_lain'] = $data['biaya_lain_lain'] = [];
        $data['persediaan_awal'] = $data['persediaan_akhir'] = $data['pembelian_kedelai'] = $data['susut_kedelai'] = $data['shu_bulan_lalu'] = $data['retur_pembelian'] = 0;
        $total_pendapatan = $total_beban = $total_shu = ['saldo_awal' => 0, 'saldo_akhir' => 0];
        foreach ($akun as $key => $value) {
            if ($value->code == $config['akun_persediaan']) {
                $data['persediaan_awal'] = $value->beginning_balance;
                $data['persediaan_akhir'] = $value->saldo_penyesuaian;
            }
            if ($value->code == $config['akun_penjualan_anggota']) {
                $data['penjualan_anggota'][] = $value;
            }
            if ($value->code == $config['akun_retur_penjualan_anggota']) {
                $data['penjualan_anggota'][] = $value;
            }
            if ($value->code == $config['akun_penjualan_non_anggota']) {
                $data['penjualan_non_anggota'][] = $value;
            }
            if ($value->code == $config['akun_retur_penjualan_non_anggota']) {
                $data['penjualan_non_anggota'][] = $value;
            }
            if ($value->code == $config['akun_pembelian']) {
                if ($value->type == 0) {
                    $data['pembelian_kedelai'] = $value->saldo_penyesuaian;
                } else {
                    $data['pembelian_kedelai'] = $value->saldo_penyesuaian * -1;
                }
            }
            /*if ($value->code == $config['akun_susut_pembelian']) {
                if ($value->type == 0) {
                    $data['susut_kedelai'] = $value->saldo_penyesuaian;
                } else {
                    $data['susut_kedelai'] = $value->saldo_penyesuaian * -1;
                }
            }*/
            if ($value->code == $config['akun_retur_pembelian']) {
                if ($value->type == 1) {
                    $data['retur_pembelian'] = $value->saldo_penyesuaian;
                } else {
                    $data['retur_pembelian'] = $value->saldo_penyesuaian * -1;
                }
            }
            if ($value->code[1] == 5 && ($value->code[4] == 2 || $value->code[4] == 3)) {
                $data['biaya_biaya_usaha'][] = $value;
            }
            if ($value->code[1] == 4 && $value->code[4] == 5) {
                $data['pendapatan_lain_lain'][] = $value;
            }
            if ($value->code[1] == 5 && $value->code[4] == 4) {
                $data['biaya_lain_lain'][] = $value;
            }
            
            if ($value->code == $shu_account) {
                $data['shu_bulan_lalu'] += $value->saldo_awal;
                $total_shu['saldo_awal'] = $value->saldo_awal;
                $total_shu['saldo_akhir'] = $value->saldo_penyesuaian;
            }
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $data['shu_bulan_lalu'] += $value->saldo_awal;
                    $total_pendapatan['saldo_awal'] += $value->saldo_awal;
                    $total_pendapatan['saldo_akhir'] += $value->saldo_penyesuaian;
                } else {
                    $data['shu_bulan_lalu'] -= $value->saldo_awal;
                    $total_pendapatan['saldo_awal'] -= $value->saldo_awal;
                    $total_pendapatan['saldo_akhir'] -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $data['shu_bulan_lalu'] += $value->saldo_awal;
                    $total_beban['saldo_awal'] += $value->saldo_awal;
                    $total_beban['saldo_akhir'] += $value->saldo_penyesuaian;
                } else {
                    $data['shu_bulan_lalu'] -= $value->saldo_awal;
                    $total_beban['saldo_awal'] -= $value->saldo_awal;
                    $total_beban['saldo_akhir'] -= $value->saldo_penyesuaian;
                }
            }
        }
        $data['shu_bulan_lalu'] = $total_shu['saldo_awal'] + $total_pendapatan['saldo_awal'] - $total_beban['saldo_awal'];

        // start initialize
        $export['data'][] = [
            'I.',
            'Penjualan Barang',
            '',
            '',
            '',
        ];
        $export['data'][] = [
            '',
            '# Penjualan Pada Anggota',
            '',
            '',
            '',
        ];
        $total_penjualan_anggota = 0;
        foreach ($data['penjualan_anggota'] as $key => $value) {
            if ($value->saldo_penyesuaian != 0) {
                if ($value->type == 1) {
                    $total_penjualan_anggota += $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian;
                } else {
                    $total_penjualan_anggota -= $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian * -1;
                }
                $export['data'][] = [
                    '',
                    '- ' . $value->name,
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp.' . number_format($saldo * -1, 2, ',', '.') . ')',
                    '',
                    ''
                ];
            }
        }
        $export['data'][] = [
            '',
            'Total Penjualan Anggota',
            '',
            $total_penjualan_anggota >= 0 ? 'Rp' . number_format($total_penjualan_anggota, 2, ',', '.') : '(Rp.' . number_format($total_penjualan_anggota * -1, 2, ',', '.') . ')',
            '',
        ];
        $export['data'][] = [
            '',
            '# Penjualan Pada Non Anggota',
            '',
            '',
            '',
        ];
        $total_penjualan_non_anggota = 0;
        foreach ($data['penjualan_non_anggota'] as $key => $value) {
            if ($value->saldo_penyesuaian != 0) {
                if ($value->type == 1) {
                    $total_penjualan_non_anggota += $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian;
                } else {
                    $total_penjualan_non_anggota -= $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian * -1;
                }
                $export['data'][] = [
                    '',
                    '- ' . $value->name,
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp.' . number_format($saldo * -1, 2, ',', '.') . ')',
                    '',
                    '',
                ];
            }
        }
        $export['data'][] = [
            '',
            'Total Penjualan Non Anggota',
            '',
            $total_penjualan_non_anggota >= 0 ? 'Rp' . number_format($total_penjualan_non_anggota, 2, ',', '.') : '(Rp.' . number_format($total_penjualan_non_anggota * -1, 2, ',', '.') . ')',
            '',
        ];

        $total_penjualan = $total_penjualan_anggota + $total_penjualan_non_anggota;
        $export['data'][] = [
            '',
            '# Jumlah Penjualan',
            '',
            '',
            $total_penjualan >= 0 ? 'Rp' . number_format($total_penjualan, 2, ',', '.') : '(Rp.' . number_format($total_penjualan * -1, 2, ',', '.') . ')',
        ];
        $export['data'][] = [
            'II.',
            'Harga Pokok',
            '',
            '',
            '',
        ];
        $export['data'][] = [
            '',
            '# Persediaan Awal',
            $data['persediaan_awal'] >= 0 ? 'Rp' . number_format($data['persediaan_awal'], 2, ',', '.') : '(Rp.' . number_format($data['persediaan_awal'] * -1, 2, ',', '.') . ')',
            '',
            '',
        ];
        $export['data'][] = [
            '',
            '# Harga Pokok Penjualan :',
            '',
            '',
            '',
        ];
        if ($data['pembelian_kedelai'] != 0) {
            $export['data'][] = [
                '',
                '- Pembelian Kedele',
                $data['pembelian_kedelai'] >= 0 ? 'Rp' . number_format($data['pembelian_kedelai'], 2, ',', '.') : '(Rp.' . number_format($data['pembelian_kedelai'] * -1, 2, ',', '.') . ')',
                '',
                '',
            ];
        }
        if ($data['susut_kedelai'] != 0) {
            $export['data'][] = [
                '',
                '- Susut Kedele',
                $data['susut_kedelai'] >= 0 ? 'Rp' . number_format($data['susut_kedelai'], 2, ',', '.') : '(Rp.' . number_format($data['susut_kedelai'] * -1, 2, ',', '.') . ')',
                '',
                '',
            ];
        }
        if ($data['retur_pembelian'] != 0) {
            $export['data'][] = [
                '',
                '- Retur Pembelian',
                $data['retur_pembelian'] >= 0 ? 'Rp' . number_format($data['retur_pembelian'], 2, ',', '.') : '(Rp.' . number_format($data['retur_pembelian'] * -1, 2, ',', '.') . ')',
                '',
                '',
            ];
        }
        $barang_tersedia = $data['persediaan_awal'] + $data['pembelian_kedelai'] - $data['susut_kedelai'] - $data['retur_pembelian'];
        $export['data'][] = [
            '',
            '# Barang Tersedia',
            '',
            $barang_tersedia >= 0 ? 'Rp' . number_format($barang_tersedia, 2, ',', '.') : '(Rp.' . number_format($barang_tersedia * -1, 2, ',', '.') . ')',
            '',
        ];
        $export['data'][] = [
            '',
            '# Persediaan Akhir',
            '',
            $data['persediaan_akhir'] >= 0 ? 'Rp' . number_format($data['persediaan_akhir'], 2, ',', '.') : '(Rp.' . number_format($data['persediaan_akhir'] * -1, 2, ',', '.') . ')',
            '',
        ];
        $hpp = $barang_tersedia - $data['persediaan_akhir'];
        $export['data'][] = [
            '',
            '# Harga Pokok Penjualan',
            '',
            '',
            $hpp >= 0 ? 'Rp' . number_format($hpp, 2, ',', '.') : '(Rp.' . number_format($hpp * -1, 2, ',', '.') . ')',
        ];
        $laba_bruto = $total_penjualan - $hpp;
        $export['data'][] = [
            'III.',
            'Laba Bruto',
            '',
            '',
            $laba_bruto >= 0 ? 'Rp' . number_format($laba_bruto, 2, ',', '.') : '(Rp.' . number_format($laba_bruto * -1, 2, ',', '.') . ')',
        ];
        $export['data'][] = [
            'IV.',
            'Biaya Biaya Usaha',
            '',
            '',
            '',
        ];
        $total_biaya_usaha = 0;
        foreach ($data['biaya_biaya_usaha'] as $key => $value) {
            if ($value->saldo_penyesuaian != 0) {
                if ($value->type == 0) {
                    $total_biaya_usaha += $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian;
                } else {
                    $total_biaya_usaha -= $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian * -1;
                }
                $export['data'][] = [
                    '',
                    '- ' . $value->name,
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp.' . number_format($saldo * -1, 2, ',', '.') . ')',
                    '',
                    '',
                ];
            }
        }
        $export['data'][] = [
            '',
            '# Total Biaya Usaha',
            '',
            $total_biaya_usaha >= 0 ? 'Rp' . number_format($total_biaya_usaha, 2, ',', '.') : '(Rp.' . number_format($total_biaya_usaha * -1, 2, ',', '.') . ')',
            '',
        ];
        $laba_usaha = $laba_bruto - $total_biaya_usaha;
        $export['data'][] = [
            '',
            '# Laba Usaha',
            '',
            '',
            $laba_usaha >= 0 ? 'Rp' . number_format($laba_usaha, 2, ',', '.') : '(Rp.' . number_format($laba_usaha * -1, 2, ',', '.') . ')',
        ];
        $export['data'][] = [
            'V.',
            'Pendapatan Lain Lain',
            '',
            '',
            '',
        ];
        $total_pendapatan_lain_lain = 0;
        foreach ($data['pendapatan_lain_lain'] as $key => $value) {
            if ($value->saldo_penyesuaian != 0) {
                if ($value->type == 1) {
                    $total_pendapatan_lain_lain += $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian;
                } else {
                    $total_pendapatan_lain_lain -= $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian * -1;
                }
                $export['data'][] = [
                    '',
                    '- ' . $value->name,
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp.' . number_format($saldo * -1, 2, ',', '.') . ')',
                    '',
                    '',
                ];
            }
        }
        $export['data'][] = [
            '',
            '# Total Pendapatan Lain Lain',
            '',
            $total_pendapatan_lain_lain >= 0 ? 'Rp' . number_format($total_pendapatan_lain_lain, 2, ',', '.') : '(Rp.' . number_format($total_pendapatan_lain_lain * -1, 2, ',', '.') . ')',
            '',
        ];
        $laba_usaha += $total_pendapatan_lain_lain;
        $export['data'][] = [
            '',
            '',
            '',
            '',
            $laba_usaha >= 0 ? 'Rp' . number_format($laba_usaha, 2, ',', '.') : '(Rp.' . number_format($laba_usaha * -1, 2, ',', '.') . ')',
        ];

        $export['data'][] = [
            'VI.',
            'Biaya Lain Lain',
            '',
            '',
            '',
        ];
        $total_biaya_lain_lain = 0;
        foreach ($data['biaya_lain_lain'] as $key => $value) {
            if ($value->saldo_penyesuaian != 0) {
                if ($value->type == 0) {
                    $total_biaya_lain_lain += $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian;
                } else {
                    $total_biaya_lain_lain -= $value->saldo_penyesuaian;
                    $saldo = $value->saldo_penyesuaian * -1;
                }
                $export['data'][] = [
                    '',
                    '- ' . $value->name,
                    $saldo >= 0 ? 'Rp' . number_format($saldo, 2, ',', '.') : '(Rp.' . number_format($saldo * -1, 2, ',', '.') . ')',
                    '',
                    '',
                ];
            }
        }
        $export['data'][] = [
            '',
            '# Total Biaya Lain Lain',
            '',
            $total_biaya_lain_lain >= 0 ? 'Rp' . number_format($total_biaya_lain_lain, 2, ',', '.') : '(Rp.' . number_format($total_biaya_lain_lain * -1, 2, ',', '.') . ')',
            '',
        ];
        $laba_usaha -= $total_biaya_lain_lain;
        $export['data'][] = [
            '',
            'Perhitungan Hasil Usaha s/d ' . date('d M Y', strtotime($data['end_date'])),
            '',
            '',
            $laba_usaha >= 0 ? 'Rp' . number_format($laba_usaha, 2, ',', '.') : '(Rp.' . number_format($laba_usaha * -1, 2, ',', '.') . ')',
        ];
        // $export['data'][] = [
        //     '',
        //     'Perhitungan Hasil Usaha s/d ' . date('d M Y', strtotime('-1 day', strtotime($data['start_date']))),
        //     '',
        //     '',
        //     $data['shu_bulan_lalu'] >= 0 ? 'Rp' . number_format($data['shu_bulan_lalu'], 2, ',', '.') : '(Rp.' . number_format($data['shu_bulan_lalu'] * -1, 2, ',', '.') . ')',
        // ];
        $shu = $data['shu_bulan_lalu'] + $laba_usaha;
        // $export['data'][] = [
        //     '',
        //     'Perhitungan Hasil Usaha Tahun ' . date('Y'),
        //     '',
        //     '',
        //     $shu >= 0 ? 'Rp' . number_format($shu, 2, ',', '.') : '(Rp.' . number_format($shu * -1, 2, ',', '.') . ')',
        // ];



        $export['end_date'] = $data['end_date'];
        // dd($export);
        return Excel::download(new PhuExport($export), 'Perhitungan Hasil Usaha per ' . date('d M Y', strtotime($data['end_date'])) . '.xlsx');
    }
    /*
    * ========================================================================================== END PHU ==========================================================================================
    */



    /*
    * ========================================================================================== START SHU ==========================================================================================
    */
    public function shu()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->ledger($data);
        $data['param'] = '';
        $data['active_menu'] = 'shu';
        $data['breadcrumb'] = [
            'Sisa Hasil Usaha' => route('shu')
        ];

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Sisa Hasil Usaha' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Sisa Hasil Usaha' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }
        $data['param'] .= '&end_date=' . $data['end_date'];
        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }
        $data['shu'] = $shu + $pendapatan - $beban;
        $data['zakat'] = $data['shu'] * 2.5 / 100;
        $data['shu'] = $data['shu'] - $data['zakat'];
        $data['data'] = $this->accountancy->shuConfigList();
        $data['percent'] = $data['data']->sum('percent');

        return view('report.shu', compact('data'));
    }
    public function shuPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->ledger($data);
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }
        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }
        $data['shu'] = $shu + $pendapatan - $beban;
        $data['zakat'] = $data['shu'] * 2.5 / 100;
        $data['shu'] = $data['shu'] - $data['zakat'];
        $data['data'] = $this->accountancy->shuConfigList();
        $data['percent'] = $data['data']->sum('percent');
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('report.shu-print', compact('data'));
    }
    public function shuDownload()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->ledger($data);
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }
        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }
        $data['shu'] = $shu + $pendapatan - $beban;
        $data['zakat'] = $data['shu'] * 2.5 / 100;
        $data['shu'] = $data['shu'] - $data['zakat'];
        $data['data'] = $this->accountancy->shuConfigList();
        $data['percent'] = $data['data']->sum('percent');

        $download = [];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $total = $data['shu'] * $value->percent / 100;
            $download[] = [
                $i,
                $value->allocation,
                number_format($value->percent, 2, ',', '.') . '%',
                ($data['shu'] >= 0 ? 'Rp' . number_format($total, 2, ',', '.') : 'Rp0'),
            ];
        }
        $export['end_date'] = $data['end_date'];
        $export['shu'] = $data['shu'];
        $export['zakat'] = $data['zakat'];
        $export['data'] = $download;
        $export['total_row'] = count($download);
        return Excel::download(new ShuExport($export), 'Sisa Hasil Usaha ' . date('d M Y', strtotime($data['end_date'])) . '.xlsx');
    }
    public function shuConfig()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->accountancy->shuConfigList($data);
        $data['percent'] = $data['data']->sum('percent');
        $data['active_menu'] = 'shu';
        $data['breadcrumb'] = [
            'Sisa Hasil Usaha' => route('shu'),
            'Set Alokasi SHU' => url()->current(),
        ];
        return view('report.shu-config', compact('data'));
    }
    public function shuConfigAdd()
    {
        $data['mode'] = 'add';
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['active_menu'] = 'shu';
        $data['breadcrumb'] = [
            'Sisa Hasil Usaha' => route('shu'),
            'Set Alokasi SHU' => route('shuConfig'),
            'Tambah' => url()->current(),
        ];
        return view('report.shu-config-form', compact('data'));
    }
    public function shuConfigEdit($id)
    {
        $data['data'] = $this->accountancy->shuConfigGet($id);
        if (!$data['data']) {
            return redirect()->route('shuConfig')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['mode'] = 'edit';
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['active_menu'] = 'shu';
        $data['breadcrumb'] = [
            'Sisa Hasil Usaha' => route('shu'),
            'Set Alokasi SHU' => route('shuConfig'),
            'Edit: ' . $data['data']->allocation => url()->current(),
        ];
        return view('report.shu-config-form', compact('data'));
    }
    public function shuConfigSave(Request $request)
    {
        $data = $request->validate([
            'allocation' => 'required',
            'account' => 'required',
            'percent' => 'required',
        ]);
        if ($request->mode == 'add') {
            $this->accountancy->shuConfigSave($data);
            $message = 'Data alokasi shu berhasil ditambahkan.';
        } else {
            $this->accountancy->shuConfigUpdate($request->id, $data);
            $message = 'Data alokasi shu berhasil diperbaharui.';
        }
        return redirect()->route('shuConfig')->with(['success' => $message]);
    }
    public function shuConfigDelete($id)
    {
        $data['data'] = $this->accountancy->shuConfigGet($id);
        if (!$data['data']) {
            return redirect()->route('shuConfig')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['data']->delete();
        return redirect()->route('shuConfig')->with(['success' => 'Data alokasi berhasil dihapus']);
    }
    public function shuAnggota()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        // $data['status'] = 1;
        $data['data'] = $this->master->memberList($data, $data['limit']);
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->shu($data);

        $data['active_menu'] = 'shu';
        $data['breadcrumb'] = [
            'Sisa Hasil Usaha' => route('shu'),
            'SHU Anggota' => url()->current()
        ];

        $data['param'] = '';

        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
            $data['param'] .= '&tbb_id=' . $data['tbb_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Bulanan' => route('closeMonthlyBookList'),
                date('d-m-Y', strtotime($tbb->closing_date)) => route('closeMonthlyBookDetail', ['id' => $tbb->id]),
                'Sisa Hasil Usaha' => route('shu', ['tbb_id' => $data['tbb_id']]),
                'SHU Anggota' => url()->current() . '?tbb_id=' . $tbb->id,
            ];
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeYearlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
            $data['param'] .= '&tbt_id=' . $data['tbt_id'];
            $data['active_menu'] = 'close-book';
            $data['breadcrumb'] = [
                'Tutup Buku' => route('closeBookList'),
                'Tahunan' => route('closeYearlyBookList'),
                date('d-m-Y', strtotime($tbt->closing_date)) => route('closeYearlyBookDetail', ['id' => $tbt->id]),
                'Sisa Hasil Usaha' => route('shu', ['tbt_id' => $data['tbt_id']]),
                'SHU Anggota' => url()->current() . '?tbt_id=' . $tbt->id,
            ];
        }
        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];

        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }
        $total_shu = $shu + $pendapatan - $beban;
        $zakat = $total_shu * 2.5 / 100;
        $shu = $total_shu - $zakat;

        $shu_simpanan = $total_shu >= 0 ? $this->accountancy->shuConfigGet(2)->percent * $shu / 100 : 0;
        $shu_toko = $total_shu >= 0 ? $this->accountancy->shuConfigGet(3)->percent * $shu / 100 : 0;

        $config = config('config_apps');
        
        $anggota = $this->master->memberList(['status' => 1]);
        $total_simpanan = 0;
        foreach($anggota as $key => $value){
            $total_simpanan += $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
        }

        
        $filter_toko = [
            // 'shu' => 1,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $total_toko = $this->store->saleList($filter_toko)->sum('total_belanja');
        

        foreach ($data['data'] as $key => $value) {
            $simpanan = $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
            $toko = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59')->sum('total_belanja');
            $data['data'][$key]['shu_simpanan'] = 0;
            if($value->status == 1){
                $data['data'][$key]['shu_simpanan'] = $total_simpanan > 0 ? $simpanan / $total_simpanan * $shu_simpanan : 0;
            }
            $data['data'][$key]['shu_toko'] = $total_toko > 0 ? $toko / $total_toko * $shu_toko : 0;
        }
        return view('report.shu-anggota', compact('data'));
    }
    public function shuAnggotaDownload()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['q'] = $_GET['q'] ?? '';
        // $data['status'] = 1;
        $data['data'] = $this->master->memberList($data);
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->shu($data);
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeMonthlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }
        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }

        $total_shu = $shu + $pendapatan - $beban;
        $zakat = $total_shu * 2.5 / 100;
        $shu = $total_shu - $zakat;

        $shu_simpanan = $total_shu >= 0 ? $this->accountancy->shuConfigGet(2)->percent * $shu / 100 : 0;
        $shu_toko = $total_shu >= 0 ? $this->accountancy->shuConfigGet(3)->percent * $shu / 100 : 0;

        $anggota = $this->master->memberList(['status' => 1]);
        $total_simpanan = 0;
        foreach($anggota as $key => $value){
            $total_simpanan += $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
        }
        
        $filter_toko = [
            // 'shu' => 1,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $total_toko = $this->store->saleList($filter_toko)->sum('total_belanja');

        foreach ($data['data'] as $key => $value) {
            $simpanan = $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
            $toko = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59')->sum('total_belanja');
            $data['data'][$key]['shu_simpanan'] = 0;
            if($value->status == 1){
                $data['data'][$key]['shu_simpanan'] = $total_simpanan > 0 ? $simpanan / $total_simpanan * $shu_simpanan : 0;
            }
            $data['data'][$key]['shu_toko'] = $total_toko > 0 ? $toko / $total_toko * $shu_toko : 0;
        }


        $download['data'] = [];
        $i = 1;
        $status = [
            0 => 'Non Anggota',
            1 => 'Anggota Aktif',
            2 => 'Anggota Keluar'
        ];
        foreach ($data['data'] as $key => $value) {
            $shu = $value->shu_toko + $value->shu_simpanan;
            $download['data'][$key] = [
                $i++,
                $value->code,
                $value->name,
                $value->region->name,
                $status[$value->status],
                number_format($value->shu_simpanan, 2, ',', '.'),
                number_format($value->shu_toko, 2, ',', '.'),
                number_format($shu, 2, ',', '.'),
            ];
        }
        $download['total_row'] = $data['data']->count();
        return Excel::download(new ShuAnggotaExport($download), 'Sisa Hasil Usaha Anggota.xlsx');
    }
    public function shuAnggotaPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['q'] = $_GET['q'] ?? '';
        // $data['status'] = 1;
        $data['data'] = $this->master->memberList($data);
        $shu_account = config('config_apps.shu_account');
        $akun = $this->accountancy->shu($data);
        if (isset($_GET['tbb_id'])) {
            $tbb = $this->accountancy->closeMonthlyBookGet($_GET['tbb_id']);
            $data['bulan'] = date('m', strtotime($tbb->end_periode));
            $data['start_date'] = $tbb->start_periode;
            $data['end_date'] = $tbb->end_periode;
            $data['tbb_id'] = $tbb->id;
            $akun = json_decode($tbb->data);
        }
        if (isset($_GET['tbt_id'])) {
            $tbt = $this->accountancy->closeMonthlyBookGet($_GET['tbt_id']);
            $data['bulan'] = date('m', strtotime($tbt->end_periode));
            $data['start_date'] = $tbt->start_periode;
            $data['end_date'] = $tbt->end_periode;
            $data['tbt_id'] = $tbt->id;
            $akun = json_decode($tbt->data);
        }
        $pendapatan = $beban = $shu = 0;
        foreach ($akun as $key => $value) {
            if ($value->code[1] == 4) {
                if ($value->type == 1) {
                    $pendapatan += $value->saldo_penyesuaian;
                } else {
                    $pendapatan -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code[1] == 5) {
                if ($value->type == 0) {
                    $beban += $value->saldo_penyesuaian;
                } else {
                    $beban -= $value->saldo_penyesuaian;
                }
            }
            if ($value->code == $shu_account) {
                $shu = $value->saldo_penyesuaian;
            }
        }
        $total_shu = $shu + $pendapatan - $beban;
        $zakat = $total_shu * 2.5 / 100;
        $shu = $total_shu - $zakat;

        $shu_simpanan = $total_shu >= 0 ? $this->accountancy->shuConfigGet(2)->percent * $shu / 100 : 0;
        $shu_toko = $total_shu >= 0 ? $this->accountancy->shuConfigGet(3)->percent * $shu / 100 : 0;

        $config = config('config_apps');

        $anggota = $this->master->memberList(['status' => 1]);
        $total_simpanan = 0;
        foreach($anggota as $key => $value){
            $total_simpanan += $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
        }
        
        $filter_toko = [
            // 'shu' => 1,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $total_toko = $this->store->saleList($filter_toko)->sum('total_belanja');

        foreach ($data['data'] as $key => $value) {
            $simpanan = $value->deposit->whereIn('deposit_type_id', [1, 2, 3, 4, 5, 6, 8, 9])->sum('balance');
            $toko = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59')->sum('total_belanja');
            $data['data'][$key]['shu_simpanan'] = 0;
            if($value->status == 1){
                $data['data'][$key]['shu_simpanan'] = $total_simpanan > 0 ? $simpanan / $total_simpanan * $shu_simpanan : 0;
            }
            $data['data'][$key]['shu_toko'] = $total_toko > 0 ? $toko / $total_toko * $shu_toko : 0;
        }

        $data['assignment'] = $this->master->pengurusAssignment();
        return view('report.shu-anggota-print', compact('data'));
    }
    /*
    * ========================================================================================== END SHU ==========================================================================================
    */



    /*
    * ========================================================================================== START ARUS KAS ==========================================================================================
    */
    public function cashflow()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'all';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('cashflow')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $data['start_date']]);
        }
        $data['code'] = $_GET['code'] ?? '01.01.01';
        $data['data'] = $this->accountancy->cashflow($data);
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['account'] = $this->accountancy->accountGet(['code', $data['code']]);

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['debit'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->account->group_id == $value->id) {
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                }
            }
        }
        $data['active_menu'] = 'cashflow';
        $data['breadcrumb'] = [
            'Arus Kas' => route('cashflow')
        ];
        return view('report.cashflow', compact('data'));
    }
    public function cashflowPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'all';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('cashflow')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $data['start_date']]);
        }
        $data['code'] = $_GET['code'] ?? '01.01.01';
        $data['data'] = $this->accountancy->cashflow($data);
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['account'] = $this->accountancy->accountGet(['code', $data['code']]);

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['debit'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->account->group_id == $value->id) {
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                }
            }
        }
        return view('report.cashflow-print', compact('data'));
    }
    public function cashflowDownload()
    {
        $export['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $export['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $export['view'] = $_GET['view'] ?? 'all';
        if ($export['start_date'] > $export['end_date']) {
            return redirect()->route('cashflow')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $export['start_date']]);
        }
        $export['code'] = $_GET['code'] ?? '01.01.01';
        $data['data'] = $this->accountancy->cashflow($export);
        $export['account'] = $this->accountancy->accountGet(['code', $export['code']]);

        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['kredit'] = 0;
            $data['group'][$key]['debit'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->account->group_id == $value->id) {
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                }
            }
        }
        $export['data'] = [];
        if ($export['view'] == 'all') {
            // Aktivitas Operasional
            $i = 0;
            $export['data'][$i] = ['Aktivitas Operasional', '', '', '', '', ''];
            $total_penambahan_opr = $total_pengurangan_opr = $no = $row_aktivitas_opr = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->account_code[1] != 3 || ($value->account_code[1] == 1 && $value->account_code[4] != 2)) {
                    $no++;
                    $i++;
                    $row_aktivitas_opr++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_opr += $penambahan;
                    $total_pengurangan_opr += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->account_code,
                        $value->account->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                '',
                number_format($total_penambahan_opr, 2, ',', '.'),
                number_format($total_pengurangan_opr, 2, ',', '.'),
                ''
            ];
            $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Operasional :',
                '',
                '',
                '',
                '',
                $total_opr < 0 ? '(' . number_format($total_opr * -1, 2, ',', '.') . ')' : number_format($total_opr, 2, ',', '.')
            ];
            // Aktivitas Investasi
            $i++;
            $export['data'][$i] = ['Aktivitas Investasi', '', '', '', '', ''];
            $total_penambahan_inv = $total_pengurangan_inv = $no = $row_aktivitas_inv = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->account_code[1] == 1 && $value->account_code[4] == 2) {
                    $no++;
                    $i++;
                    $row_aktivitas_inv++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_inv += $penambahan;
                    $total_pengurangan_inv += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->account_code,
                        $value->account->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                '',
                number_format($total_penambahan_inv, 2, ',', '.'),
                number_format($total_pengurangan_inv, 2, ',', '.'),
                ''
            ];
            $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Investasi :',
                '',
                '',
                '',
                '',
                $total_inv < 0 ? '(' . number_format($total_inv * -1, 2, ',', '.') . ')' : number_format($total_inv, 2, ',', '.')
            ];
            // Aktivitas Pendanaan
            $i++;
            $export['data'][$i] = ['Aktivitas Pendanaan', '', '', '', '', ''];
            $total_penambahan_pend = $total_pengurangan_pend = $no = $row_aktivitas_pend = 0;
            foreach ($data['data'] as $key => $value) {
                if ($value->account_code[1] == 3) {
                    $no++;
                    $i++;
                    $row_aktivitas_pend++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_pend += $penambahan;
                    $total_pengurangan_pend += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->account_code,
                        $value->account->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                '',
                number_format($total_penambahan_pend, 2, ',', '.'),
                number_format($total_pengurangan_pend, 2, ',', '.'),
                ''
            ];
            $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Pendanaan :',
                '',
                '',
                '',
                '',
                $total_pend < 0 ? '(' . number_format($total_pend * -1, 2, ',', '.') . ')' : number_format($total_pend, 2, ',', '.')
            ];
            // Saldo Awal
            $i++;
            $export['data'][$i] = [
                'Saldo Awal :',
                '',
                '',
                '',
                '',
                $export['account']->beginning_balance < 0 ? '(' . number_format($export['account']->beginning_balance * -1, 2, ',', '.') . ')' : number_format($export['account']->beginning_balance, 2, ',', '.')
            ];
            // Saldo Akhir
            $saldo = $export['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
            $i++;
            $export['data'][$i] = [
                'Saldo Akhir :',
                '',
                '',
                '',
                '',
                $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.')
            ];
        } else {
            // Aktivitas Operasional
            $i = 0;
            $export['data'][$i] = ['Aktivitas Operasional', '', '', '', ''];
            $total_penambahan_opr = $total_pengurangan_opr = $no = $row_aktivitas_opr = 0;
            foreach ($data['group'] as $key => $value) {
                if (!in_array($value->account_id, [7, 11, 12, 13, 14]) && ($value->kredit != 0 || $value->debit != 0)) {
                    $no++;
                    $i++;
                    $row_aktivitas_opr++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_opr += $penambahan;
                    $total_pengurangan_opr += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                number_format($total_penambahan_opr, 2, ',', '.'),
                number_format($total_pengurangan_opr, 2, ',', '.'),
                ''
            ];
            $total_opr = $total_penambahan_opr - $total_pengurangan_opr;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Operasional :',
                '',
                '',
                '',
                $total_opr < 0 ? '(' . number_format($total_opr * -1, 2, ',', '.') . ')' : number_format($total_opr, 2, ',', '.')
            ];
            // Aktivitas Investasi
            $i++;
            $export['data'][$i] = ['Aktivitas Investasi', '', '', '', ''];
            $total_penambahan_inv = $total_pengurangan_inv = $no = $row_aktivitas_inv = 0;
            foreach ($data['group'] as $key => $value) {
                if ($value->account_id == 7 && ($value->kredit != 0 || $value->debit != 0)) {
                    $no++;
                    $i++;
                    $row_aktivitas_inv++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_inv += $penambahan;
                    $total_pengurangan_inv += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                number_format($total_penambahan_inv, 2, ',', '.'),
                number_format($total_pengurangan_inv, 2, ',', '.'),
                ''
            ];
            $total_inv = $total_penambahan_inv - $total_pengurangan_inv;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Investasi :',
                '',
                '',
                '',
                $total_inv < 0 ? '(' . number_format($total_inv * -1, 2, ',', '.') . ')' : number_format($total_inv, 2, ',', '.')
            ];
            // Aktivitas Pendanaan
            $i++;
            $export['data'][$i] = ['Aktivitas Pendanaan', '', '', '', ''];
            $total_penambahan_pend = $total_pengurangan_pend = $no = $row_aktivitas_pend = 0;
            foreach ($data['group'] as $key => $value) {
                if (in_array($value->account_id, [11, 12, 13, 14]) && ($value->kredit != 0 || $value->debit != 0)) {
                    $no++;
                    $i++;
                    $row_aktivitas_pend++;
                    if ($export['account']->type == 0) {
                        $penambahan = $value->kredit;
                        $pengurangan = $value->debit;
                    } else {
                        $penambahan = $value->debit;
                        $pengurangan = $value->kredit;
                    }
                    $total_penambahan_pend += $penambahan;
                    $total_pengurangan_pend += $pengurangan;
                    $export['data'][$i] = [
                        $no,
                        $value->name,
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        ''
                    ];
                }
            }
            $i++;
            $export['data'][$i] = [
                'Jumlah',
                '',
                number_format($total_penambahan_pend, 2, ',', '.'),
                number_format($total_pengurangan_pend, 2, ',', '.'),
                ''
            ];
            $total_pend = $total_penambahan_pend - $total_pengurangan_pend;
            $i++;
            $export['data'][$i] = [
                'Total Aktivitas Pendanaan :',
                '',
                '',
                '',
                $total_pend < 0 ? '(' . number_format($total_pend * -1, 2, ',', '.') . ')' : number_format($total_pend, 2, ',', '.')
            ];
            // Saldo Awal
            $i++;
            $export['data'][$i] = [
                'Saldo Awal :',
                '',
                '',
                '',
                $export['account']->beginning_balance < 0 ? '(' . number_format($export['account']->beginning_balance * -1, 2, ',', '.') . ')' : number_format($export['account']->beginning_balance, 2, ',', '.')
            ];
            // Saldo Akhir
            $saldo = $export['account']->beginning_balance + $total_opr + $total_inv + $total_pend;
            $i++;
            $export['data'][$i] = [
                'Saldo Akhir :',
                '',
                '',
                '',
                $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.')
            ];
        }
        $export['total_row'] = count($export['data']);
        $export['row_aktivitas_opr'] = $row_aktivitas_opr;
        $export['row_aktivitas_inv'] = $row_aktivitas_inv;
        $export['row_aktivitas_pend'] = $row_aktivitas_pend;
        return Excel::download(new CashflowExport($export), 'Arus ' . $export['account']->name . ' ' . date('d M Y', strtotime($export['end_date'])) . '.xlsx');
    }
    /*
    * ========================================================================================== END ARUS KAS ==========================================================================================
    */



    /*
    * ========================================================================================== START PERUBAHAN MODAL ==========================================================================================
    */
    public function ekuitas()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'all';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('ekuitas')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $data['start_date']]);
        }
        $data['data'] = $this->accountancy->ekuitas($data);
        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil['group_id'] == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil['beginning_balance'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['saldo'] += $hasil['adjusting_balance'];
                }
            }
        }
        $data['active_menu'] = 'ekuitas';
        $data['breadcrumb'] = [
            'Arus Kas' => route('ekuitas')
        ];
        return view('report.ekuitas', compact('data'));
    }
    public function ekuitasPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['view'] = $_GET['view'] ?? 'all';
        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('ekuitas')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $data['start_date']]);
        }
        $data['data'] = $this->accountancy->ekuitas($data);
        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil['group_id'] == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil['beginning_balance'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['saldo'] += $hasil['adjusting_balance'];
                }
            }
        }
        return view('report.ekuitas-print', compact('data'));
    }
    public function ekuitasDownload()
    {
        $export['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $export['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $export['view'] = $_GET['view'] ?? 'all';
        if ($export['start_date'] > $export['end_date']) {
            return redirect()->route('ekuitas')->with(['warning' => 'Tanggal tidak boleh kurang dari' . $export['start_date']]);
        }
        $data['data'] = $this->accountancy->ekuitas($export);
        $data['group'] = $this->accountancy->accountGroupList();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil['group_id'] == $value->id) {
                    $data['group'][$key]['saldo_awal'] += $hasil['beginning_balance'];
                    $data['group'][$key]['debit'] += $hasil['debit'];
                    $data['group'][$key]['kredit'] += $hasil['kredit'];
                    $data['group'][$key]['saldo'] += $hasil['adjusting_balance'];
                }
            }
        }
        if ($export['view'] == 'all') {
            $export['data'] = [];
            $i = $saldo_awal = $total_penambahan = $total_pengurangan = $total_saldo = 0;
            foreach ($data['data'] as $key => $value) {
                $i++;
                $saldo_awal += $value['beginning_balance'];
                if ($value['type'] == 1) {
                    $penambahan = $value['kredit'];
                    $pengurangan = $value['debit'];
                } else {
                    $penambahan = $value['debit'];
                    $pengurangan = $value['kredit'];
                }
                $saldo = $value['beginning_balance'] + $penambahan - $pengurangan;
                $total_penambahan += $penambahan;
                $total_pengurangan += $pengurangan;
                $total_saldo += $saldo;
                $export['data'][] = [
                    $i,
                    $value['code'],
                    $value['name'],
                    number_format($value['beginning_balance'], 2, ',', '.'),
                    number_format($penambahan, 2, ',', '.'),
                    number_format($pengurangan, 2, ',', '.'),
                    $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.')
                ];
            }
            $export['data'][] = [
                'Jumlah :',
                '',
                '',
                number_format($saldo_awal, 2, ',', '.'),
                number_format($total_penambahan, 2, ',', '.'),
                number_format($total_pengurangan, 2, ',', '.'),
                $total_saldo < 0 ? '(' . number_format($total_saldo * -1, 2, ',', '.') . ')' : number_format($total_saldo, 2, ',', '.')
            ];
        } else {
            $export['data'] = [];
            $i = $saldo_awal = $total_penambahan = $total_pengurangan = $total_saldo = 0;
            foreach ($data['group'] as $key => $value) {
                if (in_array($value->account_id, [11, 12, 13, 14])) {
                    $i++;
                    $saldo_awal += $value['beginning_balance'];
                    if ($value['type'] == 1) {
                        $penambahan = $value['kredit'];
                        $pengurangan = $value['debit'];
                    } else {
                        $penambahan = $value['debit'];
                        $pengurangan = $value['kredit'];
                    }
                    $saldo = $penambahan - $pengurangan;
                    $total_penambahan += $penambahan;
                    $total_pengurangan += $pengurangan;
                    $total_saldo += $saldo;
                    $export['data'][] = [
                        $i,
                        $value['name'],
                        number_format($value['beginning_balance'], 2, ',', '.'),
                        number_format($penambahan, 2, ',', '.'),
                        number_format($pengurangan, 2, ',', '.'),
                        $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.')
                    ];
                }
            }
            $export['data'][] = [
                'Jumlah :',
                '',
                number_format($saldo_awal, 2, ',', '.'),
                number_format($total_penambahan, 2, ',', '.'),
                number_format($total_pengurangan, 2, ',', '.'),
                $total_saldo < 0 ? '(' . number_format($total_saldo * -1, 2, ',', '.') . ')' : number_format($total_saldo, 2, ',', '.')
            ];
        }
        $export['total_row'] = count($export['data']);
        return Excel::download(new PerubahanModalExport($export), 'Perubahan Modal ' . date('d M Y', strtotime($export['end_date'])) . '.xlsx');
    }
    /*
    * ========================================================================================== END PERUBAHAN MODAL ==========================================================================================
    */



    /*
    * ========================================================================================== START LAPORAN HARIAN ==========================================================================================
    */
    public function laporanHarian()
    {
        $config = config('config_apps');
        $data['date'] = $_GET['date'] ?? date('Y-m-d');
        $bukubesar = $this->accountancy->ledger([
            'start_date' => $data['date'],
            'end_date' => $data['date'],
        ]);

        // Adm Keuangan
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $data['adm_keuangan'] = [];
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($data['date']))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            $jurnalPenyesuaian = $value->jurnalPenyesuaian->where('transaction_date', '>=', $data['date'] . ' 00:00:00')->where('transaction_date', '<=', $data['date'] . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldo_lalu = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('kredit');
                $pengurangan = $jurnalPenyesuaian->sum('debit');
            } else {
                $saldo_lalu = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('debit');
                $pengurangan = $jurnalPenyesuaian->sum('kredit');
            }
            $data['adm_keuangan'][] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_lalu,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_lalu + $penambahan - $pengurangan,
            ];
        }
        $data['adm_keuangan'] = collect($data['adm_keuangan']);

        
        // Kewajiban titipan
        $kewajiban_titipan = [];
        foreach ($bukubesar as $key => $value) {
            if ($value->code == $config['piutang_penjualan_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
            }
            if ($value->code == $config['piutang_penjualan_non_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Non Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
            }
        }
        $data['kewajiban_titipan'] = collect($kewajiban_titipan);

        // Aktiva titipan
        $data['jenis_simpanan'] = $this->deposit->depositTypeList();
        $aktiva_titipan = [];
        foreach ($data['jenis_simpanan'] as $key => $value) {
            $get_saldo_awal = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['date'])))
            ]);
            $saldo_awal = $get_saldo_awal['kredit'] - $get_saldo_awal['debit'];
            $transaction = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'start_date' => $data['date'],
                'end_date' => $data['date'],
            ]);
            $penambahan = $transaction['kredit'] ?? 0;
            $pengurangan = $transaction['debit'] ?? 0;
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $aktiva_titipan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
        }
        $data['aktiva_titipan'] = collect($aktiva_titipan);

        // Persediaan Barang Jenis Kedele
        $barang = $this->store->itemList();
        $persediaan = [];
        foreach ($barang as $key => $value) {
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['date']))), 'item_id' => $value->id]);
            $saldo_awal = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $item = $this->store->itemCardList([
                'item_id' => $value->id,
                'start_date' => $data['date'],
                'end_date' => $data['date'],
            ]);
            $penambahan = $item->where('tipe', 0)->sum('qty');
            $pengurangan = $item->where('tipe', 1)->sum('qty');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $persediaan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
        }
        $data['persediaan'] = collect($persediaan);

        $data['active_menu'] = 'laporanHarian';
        $data['breadcrumb'] = [
            'Laporan Harian' => route('laporanHarian')
        ];
        return view('report.harian', compact('data'));
    }
    public function laporanHarianPrint()
    {
        $config = config('config_apps');
        $data['date'] = $_GET['date'] ?? date('Y-m-d');
        $bukubesar = $this->accountancy->ledger([
            'start_date' => $data['date'],
            'end_date' => $data['date'],
        ]);

        // Adm Keuangan
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $data['adm_keuangan'] = [];
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($data['date']))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            $jurnalPenyesuaian = $value->jurnalPenyesuaian->where('transaction_date', '>=', $data['date'] . ' 00:00:00')->where('transaction_date', '<=', $data['date'] . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldo_lalu = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('kredit');
                $pengurangan = $jurnalPenyesuaian->sum('debit');
            } else {
                $saldo_lalu = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('debit');
                $pengurangan = $jurnalPenyesuaian->sum('kredit');
            }
            $data['adm_keuangan'][] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_lalu,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_lalu + $penambahan - $pengurangan,
            ];
        }
        $data['adm_keuangan'] = collect($data['adm_keuangan']);

        // Kewajiban titipan
        $kewajiban_titipan = [];
        foreach ($bukubesar as $key => $value) {
            if ($value->code == $config['piutang_penjualan_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
            }
            if ($value->code == $config['piutang_penjualan_non_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Non Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
            }
        }
        $data['kewajiban_titipan'] = collect($kewajiban_titipan);

        // Aktiva titipan
        $data['jenis_simpanan'] = $this->deposit->depositTypeList();
        $aktiva_titipan = [];
        foreach ($data['jenis_simpanan'] as $key => $value) {
            $get_saldo_awal = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['date'])))
            ]);
            $saldo_awal = $get_saldo_awal['kredit'] - $get_saldo_awal['debit'];
            $transaction = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'start_date' => $data['date'],
                'end_date' => $data['date'],
            ]);
            $penambahan = $transaction['kredit'] ?? 0;
            $pengurangan = $transaction['debit'] ?? 0;
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $aktiva_titipan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
        }
        $data['aktiva_titipan'] = collect($aktiva_titipan);

        // Persediaan Barang Jenis Kedele
        $barang = $this->store->itemList();
        $persediaan = [];
        foreach ($barang as $key => $value) {
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['date']))), 'item_id' => $value->id]);
            $saldo_awal = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $item = $this->store->itemCardList([
                'item_id' => $value->id,
                'start_date' => $data['date'],
                'end_date' => $data['date'],
            ]);
            $penambahan = $item->where('tipe', 0)->sum('qty');
            $pengurangan = $item->where('tipe', 1)->sum('qty');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $persediaan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
        }
        $data['persediaan'] = collect($persediaan);
        $data['assignment'] = $this->master->pengurusAssignment();

        return view('report.harian-print', compact('data'));
    }
    public function laporanHarianDownload()
    {
        $config = config('config_apps');
        $date = $_GET['date'] ?? date('Y-m-d');
        $bukubesar = $this->accountancy->ledger([
            'start_date' => $date,
            'end_date' => $date,
        ]);

        // Adm Keuangan
        $export[] = [
            'I',
            'Adm Keuangan',
            '',
            '',
            '',
            '',
        ];
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $adm_keuangan = [];
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($date))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            $jurnalPenyesuaian = $value->jurnalPenyesuaian->where('transaction_date', '>=', $date . ' 00:00:00')->where('transaction_date', '<=', $date . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldo_lalu = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('kredit');
                $pengurangan = $jurnalPenyesuaian->sum('debit');
            } else {
                $saldo_lalu = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
                $penambahan = $jurnalPenyesuaian->sum('debit');
                $pengurangan = $jurnalPenyesuaian->sum('kredit');
            }
            $saldo_akhir = $saldo_lalu + $penambahan - $pengurangan;
            $export[] = [
                '-',
                $value->name,
                number_format($saldo_lalu, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.'),
            ];
            $adm_keuangan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_lalu,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
        }
        $export[] = [
            '',
            'Jumlah',
            number_format(collect($adm_keuangan)->sum('saldo_awal'), 2, ',', '.'),
            number_format(collect($adm_keuangan)->sum('penambahan'), 2, ',', '.'),
            number_format(collect($adm_keuangan)->sum('pengurangan'), 2, ',', '.'),
            number_format(collect($adm_keuangan)->sum('saldo_akhir'), 2, ',', '.'),
        ];

        // Kewajiban titipan
        $export[] = [
            'II',
            'Kewajiban Titipan',
            '',
            '',
            '',
            '',
        ];
        $kewajiban_titipan = [];
        foreach ($bukubesar as $key => $value) {
            if ($value->code == $config['piutang_penjualan_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
                $export[] = [
                    '-',
                    'Piutang Penjualan Kedele Anggota',
                    number_format($value->saldo_awal, 2, ',', '.'),
                    number_format($value->debit, 2, ',', '.'),
                    number_format($value->kredit, 2, ',', '.'),
                    number_format($value->saldo_penyesuaian, 2, ',', '.'),
                ];
            }
            if ($value->code == $config['piutang_penjualan_non_anggota']) {
                $kewajiban_titipan[] = [
                    'name' => 'Piutang Penjualan Kedele Non Anggota',
                    'saldo_awal' => $value->saldo_awal,
                    'penambahan' => $value->debit,
                    'pengurangan' => $value->kredit,
                    'saldo_akhir' => $value->saldo_penyesuaian,
                ];
                $export[] = [
                    '-',
                    'Piutang Penjualan Kedele Non Anggota',
                    number_format($value->saldo_awal, 2, ',', '.'),
                    number_format($value->debit, 2, ',', '.'),
                    number_format($value->kredit, 2, ',', '.'),
                    number_format($value->saldo_penyesuaian, 2, ',', '.'),
                ];
            }
        }
        $export[] = [
            '',
            'Jumlah',
            number_format(collect($kewajiban_titipan)->sum('saldo_awal'), 2, ',', '.'),
            number_format(collect($kewajiban_titipan)->sum('penambahan'), 2, ',', '.'),
            number_format(collect($kewajiban_titipan)->sum('pengurangan'), 2, ',', '.'),
            number_format(collect($kewajiban_titipan)->sum('saldo_akhir'), 2, ',', '.'),
        ];

        // Aktiva titipan
        $export[] = [
            'III',
            'Aktiva Titipan',
            '',
            '',
            '',
            '',
        ];
        $jenis_simpanan = $this->deposit->depositTypeList();
        $aktiva_titipan = [];
        foreach ($jenis_simpanan as $key => $value) {
            $get_saldo_awal = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'end_date' => date('Y-m-d', strtotime('-1 day', strtotime($date)))
            ]);
            $saldo_awal = $get_saldo_awal['kredit'] - $get_saldo_awal['debit'];
            $transaction = $this->deposit->depositTransactionSum([
                'type_id' => $value->id,
                'start_date' => $date,
                'end_date' => $date,
            ]);
            $penambahan = $transaction['kredit'] ?? 0;
            $pengurangan = $transaction['debit'] ?? 0;
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $aktiva_titipan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
            $export[] = [
                '-',
                $value->name,
                number_format($saldo_awal, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.'),
            ];
        }
        // dd($export);
        $export[] = [
            '',
            'Jumlah',
            number_format(collect($aktiva_titipan)->sum('saldo_awal'), 2, ',', '.'),
            number_format(collect($aktiva_titipan)->sum('penambahan'), 2, ',', '.'),
            number_format(collect($aktiva_titipan)->sum('pengurangan'), 2, ',', '.'),
            number_format(collect($aktiva_titipan)->sum('saldo_akhir'), 2, ',', '.'),
        ];

        // Persediaan Barang Jenis Kedele
        $export[] = [
            'IV',
            'Persediaan Barang Jenis Kedele',
            '',
            '',
            '',
            '',
        ];
        $barang = $this->store->itemList();
        $persediaan = [];
        foreach ($barang as $key => $value) {
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($date))), 'item_id' => $value->id]);
            $saldo_awal = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $item = $this->store->itemCardList([
                'item_id' => $value->id,
                'start_date' => $date,
                'end_date' => $date,
            ]);
            $penambahan = $item->where('tipe', 0)->sum('qty');
            $pengurangan = $item->where('tipe', 1)->sum('qty');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $persediaan[] = [
                'name' => $value->name,
                'saldo_awal' => $saldo_awal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldo_akhir,
            ];
            $export[] = [
                '-',
                $value->name,
                number_format($saldo_awal, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.'),
            ];
        }
        $export[] = [
            '',
            'Jumlah',
            number_format(collect($persediaan)->sum('saldo_awal'), 2, ',', '.') . ' Kg',
            number_format(collect($persediaan)->sum('penambahan'), 2, ',', '.') . ' Kg',
            number_format(collect($persediaan)->sum('pengurangan'), 2, ',', '.') . ' Kg',
            number_format(collect($persediaan)->sum('saldo_akhir'), 2, ',', '.') . ' Kg',
        ];

        // Sekretariat
        $export[] = [
            'IV',
            'Sekretariat',
            '',
            '',
            '',
            '',
        ];
        $export[] = [
            '-',
            'Surat Masuk',
            'Bh',
            'Bh',
            'Bh',
            'Bh',
        ];
        $export[] = [
            '-',
            'Surat Keluar',
            'Bh',
            'Bh',
            'Bh',
            'Bh',
        ];
        $export[] = [
            '-',
            'Anggota Penuh',
            'Org',
            'Org',
            'Org',
            'Org',
        ];

        return Excel::download(new LaporanHarianExport($export, $date), 'Laporan Harian ' . date('d-m-Y', strtotime($date)) . '.xlsx');
    }
    public function laporanKasBank()
    {
        $data['date'] = $filter['date'] = $_GET['date'] ?? date('Y-m-d');
        $data['limit'] = $_GET['limit'] ?? 2;
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $data['saldo_awal'] = 0;
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($data['date']))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldo_awal = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
            } else {
                $saldo_awal = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
            }
            $data['saldo_awal'] += $saldo_awal;
            $filter['code'][] = $value->code;
        }
        $data['data'] = $this->accountancy->jurnalkasbank($filter, $data['limit']);

        $all_trx = $this->accountancy->jurnalkasbank($filter);
        $data['total_debit'] = $all_trx->sum('debit');
        $data['total_kredit'] = $all_trx->sum('kredit');

        $data['active_menu'] = 'laporanHarian';
        $data['breadcrumb'] = [
            'Laporan Harian' => route('laporanHarian'),
            'Pemasukan/Pengeluaran Kas & Bank' => route('laporanKasBank'),
        ];
        return view('report.kas-bank', compact('data'));
    }
    public function laporanKasBankPrint()
    {
        $data['date'] = $filter['date'] = $_GET['date'] ?? date('Y-m-d');
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $data['saldo_awal'] = 0;
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($data['date']))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldo_awal = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
            } else {
                $saldo_awal = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
            }
            $data['saldo_awal'] += $saldo_awal;
            $filter['code'][] = $value->code;
        }
        $data['data'] = $this->accountancy->jurnalkasbank($filter);
        $data['total_debit'] = $data['data']->sum('debit');
        $data['total_kredit'] = $data['data']->sum('kredit');

        $data['assignment'] = $this->master->pengurusAssignment();
        return view('report.kas-bank-print', compact('data'));
    }
    public function laporanKasBankDownload()
    {
        $date = $filter['date'] = $_GET['date'] ?? date('Y-m-d');
        $grup_kas = $this->accountancy->accountGroupGet(1);
        $saldo_awal = 0;
        foreach ($grup_kas->account as $key => $value) {
            $jurnal = $value->jurnalPenyesuaian->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 day', strtotime($date))) . ' 23:59:59')->where('close_yearly_book_id', 0);
            if ($value->type) {
                $saldoawal = $value->beginning_balance - $jurnal->sum('debit') + $jurnal->sum('kredit');
            } else {
                $saldoawal = $value->beginning_balance + $jurnal->sum('debit') - $jurnal->sum('kredit');
            }
            $saldo_awal += $saldoawal;
            $filter['code'][] = $value->code;
        }
        $data = $this->accountancy->jurnalkasbank($filter);
        $i = 0;
        foreach ($data as $value) {
            $i++;
            $export[] = [
                $i,
                date('d M Y, H:i:s', strtotime($value->transaction_date)),
                $value->account->code,
                $value->account->name,
                $value->reference_number,
                $value->name,
                number_format($value->debit, 2, ',', '.'),
                number_format($value->kredit, 2, ',', '.'),
            ];
        }
        $export[] = [
            'Saldo s/d ' . date('d-m-Y', strtotime('-1 day', strtotime($date))),
            '',
            '',
            '',
            '',
            '',
            '',
            $saldo_awal >= 0 ? number_format($saldo_awal, 2, ',', '.') : '(' . number_format($saldo_awal * -1, 2, ',', '.') . ')'
        ];
        $total_debit = $data->sum('debit');
        $export[] = [
            'Total Mutasi Debit',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format($total_debit, 2, ',', '.')
        ];
        $total_kredit = $data->sum('kredit');
        $export[] = [
            'Total Mutasi Kredit',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format($total_kredit, 2, ',', '.')
        ];
        $saldo = $saldo_awal + $total_debit - $total_kredit;
        $export[] = [
            'Saldo s/d ' . date('d-m-Y', strtotime($date)),
            '',
            '',
            '',
            '',
            '',
            '',
            $saldo >= 0 ? number_format($saldo, 2, ',', '.') : '(' . number_format($saldo * -1, 2, ',', '.') . ')'
        ];
        return Excel::download(new LaporanKasBankExport($export, $date), 'Laporan Kas & Bank ' . date('d-m-Y', strtotime($date)) . '.xlsx');
    }
    /*
    * ========================================================================================== END LAPORAN HARIAN ==========================================================================================
    */
}