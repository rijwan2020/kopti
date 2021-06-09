<?php

namespace App\Http\Controllers;

use App\Exports\PhuExport;
use App\Repositories\AkunGrup;
use App\Repositories\Pengurus;
use App\Repositories\TutupBukuBulanan;
use App\Repositories\TutupBukuTahunan;
use App\Services\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class PhuController extends Controller
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
        $this->middleware('auth');
        $this->middleware('role');
    }
    
    public function index()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['param'] = '';
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
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
                $tbt = $this->tahunan->get($_GET['tbt_id']);
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
        } else {

            $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
            $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
            $akun = $this->saldo->saldo($data);
            $data['active_menu'] = 'phu';
            $data['breadcrumb'] = [
                'Penjelasan PHU' => route('phu')
            ];
        }

        $data['param'] .= '&bulan=' . $data['bulan'] . '&tahun=' . $data['tahun'];

        $data['penjualan_anggota'] = $data['penjualan_non_anggota'] = $data['biaya_biaya_usaha'] = $data['pendapatan_lain_lain'] = $data['biaya_lain_lain'] = [];
        $data['persediaan_awal'] = $data['persediaan_akhir'] = $data['pembelian_kedelai'] = $data['susut_kedelai'] = $data['shu_bulan_lalu'] = $data['retur_pembelian'] = 0;
        
        $total_pendapatan = $total_beban = $total_shu = ['saldo_awal' => 0, 'saldo_akhir' => 0];
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
        }


        return view('report.phu', compact('data'));
    }

    public function print()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
                $data['bulan'] = date('m', strtotime($tbb->end_periode));
                $data['start_date'] = $tbb->start_periode;
                $data['end_date'] = $tbb->end_periode;
                $data['tbb_id'] = $tbb->id;
                $akun = json_decode($tbb->data);
            }
            if (isset($_GET['tbt_id'])) {
                $tbt = $this->tahunan->get($_GET['tbt_id']);
                $data['bulan'] = date('m', strtotime($tbt->end_periode));
                $data['start_date'] = $tbt->start_periode;
                $data['end_date'] = $tbt->end_periode;
                $data['tbt_id'] = $tbt->id;
                $akun = json_decode($tbt->data);
            }
        } else {

            $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
            $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
            $akun = $this->saldo->saldo($data);
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
        $data['assignment'] = $this->pengurus->assignment();

        return view('report.phu-print', compact('data'));
    }
    
    public function download()
    {
        $config = config('config_apps');
        $shu_account = config('config_apps.shu_account');
        $data['bulan'] = $_GET['bulan'] ?? date('m');
        $data['tahun'] = $_GET['tahun'] ?? date('Y');

        if (isset($_GET['tbb_id']) || isset($_GET['tbt_id'])) {
            if (isset($_GET['tbb_id'])) {
                $tbb = $this->bulanan->get($_GET['tbb_id']);
                $data['bulan'] = date('m', strtotime($tbb->end_periode));
                $data['start_date'] = $tbb->start_periode;
                $data['end_date'] = $tbb->end_periode;
                $data['tbb_id'] = $tbb->id;
                $akun = json_decode($tbb->data);
            }
            if (isset($_GET['tbt_id'])) {
                $tbt = $this->tahunan->get($_GET['tbt_id']);
                $data['bulan'] = date('m', strtotime($tbt->end_periode));
                $data['start_date'] = $tbt->start_periode;
                $data['end_date'] = $tbt->end_periode;
                $data['tbt_id'] = $tbt->id;
                $akun = json_decode($tbt->data);
            }
        } else {

            $data['start_date'] = $data['tahun'] . '-' . sprintf('%02d', $data['bulan']) . '-01';
            $data['end_date'] = date('Y-m-t', strtotime($data['start_date']));
            $akun = $this->saldo->saldo($data);
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
}
