<?php
namespace App\Services;

use App\Model\AdjustingJournal as Jurnal;
use App\Model\AdjustingJournalDetail as PenyesuaianDetail;
use App\Model\JournalDetail as TransaksiDetail;
use App\Repositories\Akun;
use Illuminate\Support\Facades\DB;

class Saldo
{
    private $akun ;

    public function __construct(Akun $akun) {
        $this->akun = $akun;
    }

    public function saldo($data = [], $tutupbuku_bulanan = false)
    {
        $start_periode = config('config_apps.journal_periode_start');

        $filterAkun['level'] = 3;
        if(isset($data['group_id']) && $data['group_id'] != 'all'){
            $filterAkun['group_id'] = $data['group_id'];
        }

        $akun = $this->akun->list($filterAkun);
        
        // get data jurnal
        $filter = [
            'tanggal_awal' => $data['start_date'],
            'tanggal_akhir' => $data['end_date']
        ];
        if($tutupbuku_bulanan){
            $filter['tutupbuku_bulanan'] = true;
        }
        
        $saldoJurnalPenyesuaian = $this->saldoJurnalPenyesuaian($filter);
        $saldoJurnalTransaksi = $this->saldoJurnalTransaksi($filter);

        $saldoAwal = [];
        if($data['start_date'] > $start_periode){
            $filter = [
                'tanggal_akhir' => date('Y-m-d', strtotime('-1 days', strtotime($data['start_date'])))
            ];
            if($tutupbuku_bulanan){
                $filter['tutupbuku_bulanan'] = true;
            }
            $saldoAwal = $this->saldoJurnalPenyesuaian($filter);
        }

        foreach ($akun as $key => $value) {
            $akun[$key]['saldo_awal'] = $value->beginning_balance;
            if(isset($saldoJurnalPenyesuaian[$value->code])){
                $akun[$key]['debit'] = $saldoJurnalPenyesuaian[$value->code]['debit'];
                $akun[$key]['kredit'] = $saldoJurnalPenyesuaian[$value->code]['kredit'];
            }
            if ($data['start_date'] > $start_periode) {
                if(isset($saldoAwal[$value->code])){
                    if($value->type){
                        $akun[$key]['saldo_awal'] += $saldoAwal[$value->code]['kredit'] - $saldoAwal[$value->code]['debit'];
                    } else {
                        $akun[$key]['saldo_awal'] += $saldoAwal[$value->code]['debit'] - $saldoAwal[$value->code]['kredit'];
                    }
                }
            }
            if ($value->type) {
                $akun[$key]['saldo_akhir'] = $akun[$key]['saldo_awal'];
                if(isset($saldoJurnalTransaksi[$value->code])){
                    $akun[$key]['saldo_akhir'] += $saldoJurnalTransaksi[$value->code]['kredit'] - $saldoJurnalTransaksi[$value->code]['debit'];
                }
                $akun[$key]['saldo_penyesuaian'] = $akun[$key]['saldo_awal'] + $akun[$key]['kredit'] - $akun[$key]['debit'];
            } else {
                $akun[$key]['saldo_akhir'] = $akun[$key]['saldo_awal'];
                if(isset($saldoJurnalTransaksi[$value->code])){
                    $akun[$key]['saldo_akhir'] += $saldoJurnalTransaksi[$value->code]['debit'] - $saldoJurnalTransaksi[$value->code]['kredit'];
                }
                $akun[$key]['saldo_penyesuaian'] = $akun[$key]['saldo_awal'] + $akun[$key]['debit'] - $akun[$key]['kredit'];
            }
        }

        return $akun;
    }

    private function saldoJurnalPenyesuaian($data = [])
    {
        $filter[] = ['close_yearly_book_id', 0];
        if (isset($data['tanggal_awal'])) {
            $filter[] = ['transaction_date', '>=', $data['tanggal_awal'] . ' 00:00:00'];
        }
        if (isset($data['tanggal_akhir'])) {
            $filter[] = ['transaction_date', '<=', $data['tanggal_akhir'] . ' 23:59:59'];
        }
        if (isset($data['tutupbuku_bulanan'])) {
            $filter[] = ['close_monthly_book_id', 0];
        }
        $jurnal_list = PenyesuaianDetail::select(DB::raw("sum(`debit`) as debit, sum(`kredit`) as kredit, account_code as kode"))->where($filter)->groupBy('account_code')->get();

        $data = [];
        foreach($jurnal_list as $jurnal){
            $data[$jurnal->kode] = [
                'debit' => $jurnal->debit,
                'kredit' => $jurnal->kredit
            ];
        }
        return $data;
    }

    private function saldoJurnalTransaksi($data = [])
    {
        $filter[] = ['close_yearly_book_id', 0];
        if (isset($data['tanggal_awal'])) {
            $filter[] = ['transaction_date', '>=', $data['tanggal_awal'] . ' 00:00:00'];
        }
        if (isset($data['tanggal_akhir'])) {
            $filter[] = ['transaction_date', '<=', $data['tanggal_akhir'] . ' 23:59:59'];
        }
        if (isset($data['tutupbuku_bulanan'])) {
            $filter[] = ['close_monthly_book_id', 0];
        }
        $jurnal_list = TransaksiDetail::select(DB::raw("sum(`debit`) as debit, sum(`kredit`) as kredit, account_code as kode"))->where($filter)->groupBy('account_code')->get();

        $data = [];
        foreach($jurnal_list as $jurnal){
            $data[$jurnal->kode] = [
                'debit' => $jurnal->debit,
                'kredit' => $jurnal->kredit
            ];
        }
        return $data;
    }

}
