<?php

namespace App\Http\Controllers;

use App\Exports\TrialBalanceExport;
use App\Repositories\AkunGrup;
use App\Repositories\Pengurus;
use App\Repositories\TutupBukuBulanan;
use App\Repositories\TutupBukuTahunan;
use App\Services\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class NeracaSaldoController extends Controller
{
    private $saldo, $akungrup, $tahunan, $bulanan, $pengurus;
    public function __construct(
        Saldo $saldo, 
        AkunGrup $akungrup,
        TutupBukuBulanan $bulanan,
        TutupBukuTahunan $tahunan,
        Pengurus $pengurus
    ){
        $this->saldo = $saldo;
        $this->akungrup = $akungrup;
        $this->bulanan = $bulanan;
        $this->tahunan = $tahunan;
        $this->pengurus = $pengurus;
        Config::set('title', 'Laporan');
    }

    public function index()
    {
        $data['view'] = $_GET['view'] ?? 'all';
        $data['param'] = 'view=' . $data['view'];
        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
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
                $tbt = $this->tahunan->get($_GET['tbt_id']);
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
        } else {
            $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
            $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
            $data['data'] = $this->saldo->saldo($data);
            $data['active_menu'] = 'trialbalance';
            $data['breadcrumb'] = [
                'Neraca Saldo' => route('trialBalance')
            ];
        }
        
        if ($data['start_date'] > $data['end_date']) {
            return back()->with(['warning' => 'Tanggal tidak valid']);
        }

        $data['param'] .= '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];

        $data['group'] = $this->akungrup->list();
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
    
    public function print()
    {
        $data['view'] = $_GET['view'] ?? 'all';
        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
                $data['tbb_id'] = $tbb->id;
                $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
                $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
                $data['data'] = json_decode($tbb->data);
            }
            if (isset($_GET['tbt_id'])) {
                $tbt = $this->tahunan->get($_GET['tbt_id']);
                $data['tbt_id'] = $tbt->id;
                $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
                $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
                $data['data'] = json_decode($tbt->data);
            }
        } else {
            $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
            $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
            $data['data'] = $this->saldo->saldo($data);
        }
        
        if ($data['start_date'] > $data['end_date']) {
            return back()->with(['warning' => 'Tanggal tidak valid']);
        }

        $data['group'] = $this->akungrup->list();
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
        $data['assignment'] = $this->pengurus->assignment();
        return view('accountancy.trial-balance-print', compact('data'));
    }

    public function download()
    {
        
        $data['view'] = $_GET['view'] ?? 'all';
        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
                $data['tbb_id'] = $tbb->id;
                $data['start_date'] = $_GET['start_date'] ?? $tbb->start_periode;
                $data['end_date'] = $_GET['end_date'] ?? $tbb->end_periode;
                $data['data'] = json_decode($tbb->data);
            }
            if (isset($_GET['tbt_id'])) {
                $tbt = $this->tahunan->get($_GET['tbt_id']);
                $data['tbt_id'] = $tbt->id;
                $data['start_date'] = $_GET['start_date'] ?? $tbt->start_periode;
                $data['end_date'] = $_GET['end_date'] ?? $tbt->end_periode;
                $data['data'] = json_decode($tbt->data);
            }
        } else {
            $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
            $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
            $data['data'] = $this->saldo->saldo($data);
        }
        
        if ($data['start_date'] > $data['end_date']) {
            return back()->with(['warning' => 'Tanggal tidak valid']);
        }

        $export['start_date'] = $data['start_date'];
        $export['end_date'] = $data['end_date'];
        $export['view'] = $data['view'];

        $data['group'] = $this->akungrup->list();
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
}
