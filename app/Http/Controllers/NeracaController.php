<?php

namespace App\Http\Controllers;

use App\Exports\BalanceDescriptionExport;
use App\Exports\BalanceExport;
use App\Repositories\AkunGrup;
use App\Repositories\Pengurus;
use App\Repositories\TutupBukuBulanan;
use App\Repositories\TutupBukuTahunan;
use App\Services\Saldo;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class NeracaController extends Controller
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
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';
        
        $data['param'] = 'view=' . $data['view'];

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
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
                $tbt = $this->tahunan->get($_GET['tbt_id']);
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
        } else {
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
            $data['active_menu'] = 'balance';
            $data['breadcrumb'] = [
                'Neraca' => route('balance')
            ];
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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

        $data['group'] = $this->akungrup->list();
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

    public function print()
    {
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
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
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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

        $data['group'] = $this->akungrup->list();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }
        $data['assignment'] = $this->pengurus->assignment();
        return view('report.balance-print', compact('data'));
    }

    public function download()
    {
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
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
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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


        $data['group'] = $this->akungrup->list();
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

    public function penjelasan()
    {
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';
        
        $data['param'] = 'view=' . $data['view'];

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
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
                $tbt = $this->tahunan->get($_GET['tbt_id']);
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
        } else {
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
            $data['active_menu'] = 'balance';
            $data['breadcrumb'] = [
                'Neraca' => route('balance')
            ];
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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

        $data['group'] = $this->akungrup->list();
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

    public function penjelasanPrint()
    {
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
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
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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

        $data['group'] = $this->akungrup->list();
        foreach ($data['group'] as $key => $value) {
            $data['group'][$key]['saldo'] = 0;
            foreach ($data['data'] as $hsl => $hasil) {
                if ($hasil->group_id == $value->id) {
                    $data['group'][$key]['saldo'] += $hasil->saldo_penyesuaian;
                    $data['group'][$key]->saldo_tahun_lalu += $hasil->saldo_tahun_lalu;
                }
            }
        }
        $data['assignment'] = $this->pengurus->assignment();

        return view('report.balance-description-print', compact('data'));
    }
    
    public function penjelasanDownload()
    {
        $config = config('config_apps');
        $shu_account = $config['shu_account'];
        $data['view'] = $_GET['view'] ?? 'group';

        if(isset($_GET['tbb_id']) || isset($_GET['tbt_id'])){
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
            $data['start_date'] = $_GET['start_date'] ?? $config['journal_periode_start'];
            $data['end_date'] = $_GET['end_date'] ?? $config['journal_periode_end'];
            $data['data'] = $this->saldo->saldo($data);
        }

        if ($data['start_date'] > $data['end_date']) {
            return redirect()->route('balance')->with(['warning' => 'Tanggal tidak valid']);
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

        $data['group'] = $this->akungrup->list();
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
}
