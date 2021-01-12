<?php

namespace App\Classes;

use App\Model\Account;
use App\Model\AccountGroup;
use App\Model\AccountUpload;
use App\Model\AdjustingJournal;
use App\Model\AdjustingJournalDetail;
use App\Model\CloseMonthlyBook;
use App\Model\CloseYearlyBook;
use App\Model\Journal;
use App\Model\JournalDetail;
use App\Model\ShuConfig;
use Illuminate\Support\Facades\DB;

class AccountancyClass
{
    public $error = '', $last_journal_id = 0, $last_adjusting_journal_id = 0, $last_account_id = 0;

    public function __construct()
    {
        DB::enableQueryLog();
    }





    /*
    * ===================================================================================== START TABLE ACCOUNTS =====================================================================================
    */
    public function accountList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['code', 'asc'];
        //start query
        // $query = Account::query()->with(['group', 'jurnalTransaksi', 'jurnalPenyesuaian']);
        $query = Account::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("code", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['type']) && $data['type'] != 'all') {
            $query->where('type', $data['type']);
        }
        if (isset($data['level']) && $data['level'] != 'all') {
            $query->where('level', $data['level']);
        }
        if (isset($data['parent_id']) && $data['parent_id'] != 'all') {
            $query->where('parent_id', $data['parent_id']);
        }
        if (isset($data['group_id']) && $data['group_id'] != 'all') {
            $query->where('group_id', $data['group_id']);
        }
        if (isset($data['linked']) && $data['linked'] != 'all') {
            $query->where('linked', $data['linked']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function accountGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Account::find($data);
        } else {
            $query = Account::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function accountSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        $get_next_kode = $this->next_kodeakun($data['parent_id']);
        $data['level'] = $get_next_kode['level'];
        $data['code'] = $get_next_kode['code'];

        $account = Account::create($data);
        $this->last_account_id = $account->id;

        return true;
    }
    public function next_kodeakun($parent_id)
    {
        $akun = $this->accountGet(['id', $parent_id]);
        $last_akun = Account::where('parent_id', $akun->id)->latest('code')->first();

        if (!$last_akun) {
            $nextakun = 0;
        } else {
            $nextakun = substr($last_akun->code, -2);
        }

        $hasil = $akun->code . '.' . str_pad($nextakun + 1, 2, '0', STR_PAD_LEFT);

        return [
            'code' => $hasil,
            'level' => $akun->level + 1
        ];
    }
    public function accountUpdate($id, $data = [])
    {
        $account = $this->accountGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $account->update($data);
        return true;
    }
    /*
    * ===================================================================================== END TABLE ACCOUNTS =====================================================================================
    */





    /*
    * ===================================================================================== START TABLE ACCOUNTS GROUPS =====================================================================================
    */
    public function accountGroupList($data = [], $limit = false, $order = ['account_id', 'asc'], $paginate = true)
    {
        //start query
        $query = AccountGroup::query()->with('account');
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function accountGroupGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = AccountGroup::with(['account', 'golongan'])->find($data);
        } else {
            $query = AccountGroup::with(['account', 'golongan'])->where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function accountGroupSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        $account = AccountGroup::create($data);

        return true;
    }
    public function accountGroupUpdate($id, $data = [])
    {
        $accountGroup = $this->accountGroupGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $accountGroup->update($data);
        return true;
    }
    /*
    * ===================================================================================== END TABLE ACCOUNTS GROUPS =====================================================================================
    */





    /*
    * ===================================================================================== START TABLE ACCOUNT UPLOADS =====================================================================================
    */
    public function accountUploadList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['code', 'asc'];
        //start query
        $query = AccountUpload::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("code", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function accountUploadConfirm($confirm)
    {
        if ($confirm == 0) {
            AccountUpload::query()->truncate();
        } else {
            $accountUpload = $this->AccountUploadList();
            foreach ($accountUpload  as $value) {
                $account = $this->accountGet(['code', $value->code]);
                if ($account) {
                    if ($account->type == 0) {
                        if ($account->balance >= 0) {
                            $debit = $value->balance;
                            $kredit = 0;
                        } else {
                            $kredit = $value->balance * -1;
                            $debit = 0;
                        }
                    } else {
                        if ($account->balance >= 0) {
                            $kredit = $value->balance;
                            $debit = 0;
                        } else {
                            $debit = $value->balance * -1;
                            $kredit = 0;
                        }
                    }
                    $updateAccount = [
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id,
                        'beginning_balance' => $value->balance,
                        'ending_balance' => $value->balance,
                        'adjusting_balance' => $value->balance,
                        'debit' => $debit,
                        'kredit' => $kredit,
                    ];
                    $account->update($updateAccount);
                }
            }
            $config = "<?php \n return [\n";
            foreach (config('config_apps') as $hsl => $hasil) {
                if ($hsl == 'set_account') {
                    $hasil = 1;
                }
                $config .= "\t'{$hsl}' => '{$hasil}',\n";
            }
            $config .= " ]; ";
            $file = config_path() . '/config_apps.php';
            file_put_contents($file, $config);
            AccountUpload::query()->truncate();
        }
        return true;
    }
    /*
    * ===================================================================================== END TABLE ACCOUNT UPLOADS =====================================================================================
    */





    /*
    * ===================================================================================== START LEDGER =====================================================================================
    */
    public function ledger($data = [], $tutupbuku_bulanan = false)
    {
        $start_periode = config('config_apps.journal_periode_start');

        $account = $this->accountList(['level' => 3, 'group_id' => $data['group_id'] ?? 'all']);

        foreach ($account as $key => $value) {
            $jurnalPenyesuaian = $value->jurnalPenyesuaian->where('close_yearly_book_id', 0)->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00')->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
            if ($tutupbuku_bulanan) {
                $jurnalPenyesuaian = $jurnalPenyesuaian->where('close_monthly_book_id', 0);
            }
            $account[$key]['debit'] = $jurnalPenyesuaian->sum('debit');
            $account[$key]['kredit'] = $jurnalPenyesuaian->sum('kredit');
            $account[$key]['saldo_awal'] = $value->beginning_balance;
            if ($data['start_date'] > $start_periode) {
                $jurnal = $value->jurnalPenyesuaian->where('close_yearly_book_id', 0)->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 days', strtotime($data['start_date']))) . ' 23:59:59');
                if ($tutupbuku_bulanan) {
                    $jurnal = $jurnal->where('close_monthly_book_id', 0);
                }
                if ($value->type) {
                    $account[$key]['saldo_awal'] += $jurnal->sum('kredit') - $jurnal->sum('debit');
                } else {
                    $account[$key]['saldo_awal'] += $jurnal->sum('debit') - $jurnal->sum('kredit');
                }
            }
            $jurnalTransaksi = $value->jurnalTransaksi->where('close_yearly_book_id', 0)->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00')->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
            if ($tutupbuku_bulanan) {
                $jurnalTransaksi = $jurnalTransaksi->where('close_monthly_book_id', 0);
            }
            if ($value->type) {
                $account[$key]['saldo_akhir'] = $account[$key]['saldo_awal'] + $jurnalTransaksi->sum('kredit') - $jurnalTransaksi->sum('debit');
                $account[$key]['saldo_penyesuaian'] = $account[$key]['saldo_awal'] + $account[$key]['kredit'] - $account[$key]['debit'];
            } else {
                $account[$key]['saldo_akhir'] = $account[$key]['saldo_awal'] + $jurnalTransaksi->sum('debit') - $jurnalTransaksi->sum('kredit');
                $account[$key]['saldo_penyesuaian'] = $account[$key]['saldo_awal'] + $account[$key]['debit'] - $account[$key]['kredit'];
            }
        }
        return $account;
    }
    public function shu($data = [], $tutupbuku_bulanan = false)
    {
        $start_periode = config('config_apps.journal_periode_start');

        $account = $this->accountList(['level' => 3, 'group_id' => $data['group_id'] ?? 'all']);

        foreach ($account as $key => $value) {
            if($value->code[1] == 4 || $value->code[1] == 5 || $value->code == config('config_apps.shu_account')){
                $jurnalPenyesuaian = $value->jurnalPenyesuaian->where('close_yearly_book_id', 0)->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00')->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
                if ($tutupbuku_bulanan) {
                    $jurnalPenyesuaian = $jurnalPenyesuaian->where('close_monthly_book_id', 0);
                }
                $account[$key]['debit'] = $jurnalPenyesuaian->sum('debit');
                $account[$key]['kredit'] = $jurnalPenyesuaian->sum('kredit');
                $account[$key]['saldo_awal'] = $value->beginning_balance;
                if ($data['start_date'] > $start_periode) {
                    $jurnal = $value->jurnalPenyesuaian->where('close_yearly_book_id', 0)->where('transaction_date', '<=', date('Y-m-d', strtotime('-1 days', strtotime($data['start_date']))) . ' 23:59:59');
                    if ($tutupbuku_bulanan) {
                        $jurnal = $jurnal->where('close_monthly_book_id', 0);
                    }
                    if ($value->type) {
                        $account[$key]['saldo_awal'] += $jurnal->sum('kredit') - $jurnal->sum('debit');
                    } else {
                        $account[$key]['saldo_awal'] += $jurnal->sum('debit') - $jurnal->sum('kredit');
                    }
                }
                $jurnalTransaksi = $value->jurnalTransaksi->where('close_yearly_book_id', 0)->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00')->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
                if ($tutupbuku_bulanan) {
                    $jurnalTransaksi = $jurnalTransaksi->where('close_monthly_book_id', 0);
                }
                if ($value->type) {
                    $account[$key]['saldo_akhir'] = $account[$key]['saldo_awal'] + $jurnalTransaksi->sum('kredit') - $jurnalTransaksi->sum('debit');
                    $account[$key]['saldo_penyesuaian'] = $account[$key]['saldo_awal'] + $account[$key]['kredit'] - $account[$key]['debit'];
                } else {
                    $account[$key]['saldo_akhir'] = $account[$key]['saldo_awal'] + $jurnalTransaksi->sum('debit') - $jurnalTransaksi->sum('kredit');
                    $account[$key]['saldo_penyesuaian'] = $account[$key]['saldo_awal'] + $account[$key]['debit'] - $account[$key]['kredit'];
                }
            }
        }
        return $account;
    }

    public function cashflow($data = [])
    {
        $query1 = AdjustingJournalDetail::select('adjusting_journal_id')->where([
            ['account_code', $data['code']],
            ['transaction_date', '>=', $data['start_date']],
            ['transaction_date', '<=', $data['end_date']],
            ['close_yearly_book_id', 0]
        ])->get();

        $query2 = AdjustingJournalDetail::where([
            ['account_code', '!=', $data['code']],
            ['transaction_date', '>=', $data['start_date']],
            ['transaction_date', '<=', $data['end_date']],
            ['close_yearly_book_id', 0]
        ])
            ->whereIn('adjusting_journal_id', $query1->toArray())
            ->groupBy('account_code')
            ->get([
                'account_code',
                'type',
                DB::raw('SUM(debit) as debit'),
                DB::raw('SUM(kredit) as kredit'),
            ]);

        return $query2;
    }
    public function ekuitas($data = [])
    {
        $account = $this->accountList(['level' => 3]);
        foreach ($account as $key => $value) {
            if ($value->code[1] == 3) {
                $result[$key]['id'] = $value->id;
                $result[$key]['code'] = $value->code;
                $result[$key]['name'] = $value->name;
                $result[$key]['type'] = $value->type;
                $result[$key]['group_id'] = $value->group_id;
                $result[$key]['beginning_balance'] = $value->beginning_balance;
                $adjustingJournal = $this->adjustingJournalDetailSum([
                    'account_code' => $value->code,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                ]);
                if ($value->type == 0) {
                    $result[$key]['adjusting_balance'] = $value->beginning_balance + $adjustingJournal['debit'] - $adjustingJournal['kredit'];
                } else {
                    $result[$key]['adjusting_balance'] = $value->beginning_balance + $adjustingJournal['kredit'] - $adjustingJournal['debit'];
                }
                $result[$key]['debit'] = $adjustingJournal['debit'];
                $result[$key]['kredit'] = $adjustingJournal['kredit'];
            }
        }
        return $result;
    }
    /*
    * ===================================================================================== END LEDGER =====================================================================================
    */





    /*
    * ===================================================================================== START JOURNAL TABLE =====================================================================================
    */
    public function journalList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['transaction_date', 'desc'];
        //start query
        $query = Journal::query()->with(['detail']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("reference_number", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
        }
        if (isset($data['member_id']) && !empty($data['member_id'])) {
            $query->where('member_id', '=', $data['member_id']);
        }
        if (isset($data['warehouse_id']) && !empty($data['warehouse_id'])) {
            $query->where('warehouse_id', '=', $data['warehouse_id']);
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }

        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function journalGet($data = [])
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Journal::find($data);
        } else {
            $query = Journal::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function journalSave($data)
    {
        $detail = $data['detail'];
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['adjusting_journal_id'] = $this->last_adjusting_journal_id;

        unset($data['detail']);
        $journal = Journal::create($data);
        $this->last_journal_id = $journal->id;

        foreach ($detail as $key => $value) {
            $detail[$key]['journal_id'] = $this->last_journal_id;
            $detail[$key]['transaction_date'] = $data['transaction_date'];
            $detail[$key]['reference_number'] = $data['reference_number'];
            $detail[$key]['name'] = $data['name'];
            $detail[$key]['type'] = $data['type'];
            $detail[$key]['debit'] = $value['type'] == 'dana_from' ? $value['amount'] : 0;
            $detail[$key]['kredit'] = $value['type'] == 'dana_to' ? $value['amount'] : 0;
        }
        foreach ($detail as $key => $value) {
            unset($value['amount']);
            $this->journalDetailSave($value);
        }
        return true;
    }
    public function journalDelete($id)
    {
        $data = $this->journalGet($id);
        // $data->updated_by = $journalDetail['updated_by'] = auth()->user()->id ?? 1;
        $data->deleted_at = $journalDetail['deleted_at'] = date('Y-m-d H:i:s');
        $data->deleted_by = $journalDetail['deleted_by'] = auth()->user()->id;
        $data->update();

        JournalDetail::where('journal_id', $id)->update($journalDetail);

        return true;
    }
    public function journalUpdate($id)
    {
        $data = $this->journalGet($id);
        $data->updated_by = $journalDetail['updated_by'] = auth()->user()->id ?? 1;
        $data->updated_at = $journalDetail['updated_at'] = date('Y-m-d H:i:s');
        $data->edited = $journalDetail['edited'] = 1;
        $data->update();

        JournalDetail::where('journal_id', $id)->update($journalDetail);

        return true;
    }
    /*
    * ===================================================================================== END JOURNAL TABLE =====================================================================================
    */





    /*
    * ===================================================================================== START JOURNAL DETAIL TABLE =====================================================================================
    */
    public function journalDetailList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if ($order == false) $order = ['transaction_date', 'ASC'];
        //start query
        $query = JournalDetail::query()->with(['account', 'userEdit', 'userInput', 'journal']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_ref", "like", "%{$data['q']}%")
                    ->orWhere("keterangan", "like", "%{$data['q']}%");
            });
        }
        //search by start_date
        if (isset($data['start_date']) && $data['start_date'] != 'all') {
            $query->where('transaction_date', '>=', date('Y-m-d', strtotime($data['start_date'])) . ' 00:00:00');
        }
        //search by end_date
        if (isset($data['end_date']) && $data['end_date'] != 'all') {
            $query->where('transaction_date', '<=', date('Y-m-d', strtotime($data['end_date'])) . ' 23:59:59');
        }
        //search by kodeakun
        if (isset($data['account_code']) && $data['account_code'] != 'all') {
            $query->where('account_code', '=', $data['account_code']);
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }
        //order
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function journalDetailSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        JournalDetail::create($data);
        return true;
    }
    public function journalDetailSum($data, $all = false)
    {
        $query = JournalDetail::query();
        $query->select(DB::raw('sum(kredit) as kredit, sum(debit) as debit'));
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("reference_number", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        //account_code
        if (isset($data['account_code'])) {
            $query->where('account_code', $data['account_code']);
        }
        //search by start_date
        if (isset($data['start_date']) && $data['start_date'] != 'all') {
            $query->where('transaction_date', '>=', date('Y-m-d', strtotime($data['start_date'])) . ' 00:00:00');
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }
        //search by end_date
        if (isset($data['end_date']) && $data['end_date'] != 'all') {
            $query->where('transaction_date', '<=', date('Y-m-d', strtotime($data['end_date'])) . ' 23:59:59');
        }
        if ($all != false) {
            $query->where('edited', '!=', 0);
        }
        $hasil = $query->first();

        return $hasil;
    }
    /*
    * ===================================================================================== END JOURNAL TABLE =====================================================================================
    */





    /*
    * ===================================================================================== START ADJUSTING JOURNAL TABLE =====================================================================================
    */
    public function adjustingJournalList($data = [], $limit = false, $order = ['transaction_date', 'desc'], $paginate = true)
    {
        //start query
        $query = AdjustingJournal::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("reference_number", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('transaction_date', '>=', $data['start_date'] . ' 00:00:00');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('transaction_date', '<=', $data['end_date'] . ' 23:59:59');
        }
        if (isset($data['member_id']) && !empty($data['member_id'])) {
            $query->where('member_id', '=', $data['member_id']);
        }
        if (isset($data['warehouse_id']) && !empty($data['warehouse_id'])) {
            $query->where('warehouse_id', '=', $data['warehouse_id']);
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function adjustingJournalGet($data = [])
    {
        //if $data not array
        if (!is_array($data)) {
            $query = AdjustingJournal::find($data);
        } else {
            $query = AdjustingJournal::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function adjustingJournalSave($data, $type = 'general')
    {
        $detail = $data['detail'];
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        unset($data['detail']);
        $adjustingJournal = AdjustingJournal::create($data);
        $this->last_adjusting_journal_id = $adjustingJournal->id;
        foreach ($detail as $key => $value) {
            $detail[$key]['adjusting_journal_id'] = $this->last_adjusting_journal_id;
            $detail[$key]['transaction_date'] = $data['transaction_date'];
            $detail[$key]['reference_number'] = $data['reference_number'];
            $detail[$key]['name'] = $data['name'];
            $detail[$key]['type'] = $data['type'];
            $detail[$key]['debit'] = $value['type'] == 'dana_from' ? $value['amount'] : 0;
            $detail[$key]['kredit'] = $value['type'] == 'dana_to' ? $value['amount'] : 0;
            $detail[$key]['type_journal'] = $type;
        }

        foreach ($detail as $key => $value) {
            unset($value['amount']);
            $this->adjustingJournalDetailSave($value);
        }
        return true;
    }
    public function adjustingJournalUpdate($id, $data)
    {
        $detail = $data['detail'];
        unset($data['detail']);
        $data['updated_by'] = auth()->user()->id ?? 1;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['edited'] = 1;
        $type = $data['type_journal'] ?? '';
        unset($data['type_journal']);
        //get data jurnal
        $journal = $this->adjustingJournalGet($id);

        if (!$journal) {
            $this->error = 'Data jurnal tidak ditemukan.';
            return false;
        }

        if (date('Y-m-d', strtotime($data['transaction_date'])) == date('Y-m-d', strtotime($journal->transaction_date))) {
            unset($data['transaction_date']);
            $transaction_date = $journal->transaction_date;
        } else {
            $transaction_date = $data['transaction_date'];
        }

        foreach ($detail as $key => $value) {
            $detail[$key]['adjusting_journal_id'] = $journal->id;
            $detail[$key]['transaction_date'] = $transaction_date;
            $detail[$key]['reference_number'] = $data['reference_number'];
            $detail[$key]['name'] = $data['name'];
            $detail[$key]['type'] = $data['type'];
            $detail[$key]['edited'] = 1;
            $detail[$key]['debit'] = $value['type'] == 'dana_from' ? $value['amount'] : 0;
            $detail[$key]['kredit'] = $value['type'] == 'dana_to' ? $value['amount'] : 0;
            $detail[$key]['type_journal'] = $type;
        }
        foreach ($journal->detail as $key => $value) {
            $this->adjustingJournalDetailDelete($value->id);
        }
        $journal->update($data);

        foreach ($detail as $key => $value) {
            unset($value['amount']);
            $this->adjustingJournalDetailSave($value, 'update');
        }

        return true;
    }
    public function adjustingJournalDelete($id)
    {
        $journal = $this->adjustingJournalGet($id);
        foreach ($journal->detail as $key => $value) {
            $this->adjustingJournalDetailDelete($value->id);
        }
        $journal->delete();
        return true;
    }
    /*
    * ===================================================================================== END ADJUSTING JOURNAL TABLE =====================================================================================
    */





    /*
    * ===================================================================================== END ADJUSTING JOURNAL DETAIL TABLE =====================================================================================
    */
    public function adjustingJournalDetailList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if ($order == false) $order = ['transaction_date', 'ASC'];
        //start query
        $query = AdjustingJournalDetail::query()->with(['account', 'userEdit', 'userInput', 'journal']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("reference_number", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        //search by start_date
        if (isset($data['start_date']) && $data['start_date'] != 'all') {
            $query->where('transaction_date', '>=', date('Y-m-d', strtotime($data['start_date'])) . ' 00:00:00');
        }
        //search by end_date
        if (isset($data['end_date']) && $data['end_date'] != 'all') {
            $query->where('transaction_date', '<=', date('Y-m-d', strtotime($data['end_date'])) . ' 23:59:59');
        }
        //search by kodeakun
        if (isset($data['account_code']) && $data['account_code'] != 'all') {
            $query->where('account_code', '=', $data['account_code']);
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }
        //order
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function adjustingJournalDetailGet($data = [])
    {
        //if $data not array
        if (!is_array($data)) {
            $query = AdjustingJournalDetail::find($data);
        } else {
            $query = AdjustingJournalDetail::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function adjustingJournalDetailSave($data, $mode = 'add')
    {
        $config = config('config_apps.set_account');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $type = $data['type_journal'];
        unset($data['type_journal']);
        AdjustingJournalDetail::create($data);

        $saldoakun = [
            'code' => $data['account_code'],
            'kredit' => $data['kredit'],
            'debit' => $data['debit'],
            'type' => $data['type'],
            'type_journal' => $type
        ];

        if ($config != 0) {
            $this->saldoakunUpdate($saldoakun, $mode);
        }
        return true;
    }
    public function adjustingJournalDetailDelete($id)
    {
        $data = $this->adjustingJournalDetailGet($id);
        $saldoakun = [
            'code' => $data['account_code'],
            'kredit' => $data['debit'],
            'debit' => $data['kredit'],
            'type' => $data['type']
        ];
        $this->saldoakunUpdate($saldoakun, 'delete');
        $data->delete();
        return true;
    }
    public function adjustingJournalDetailSum($data, $all = false)
    {
        $query = AdjustingJournalDetail::query();
        $query->select(DB::raw('sum(kredit) as kredit, sum(debit) as debit'));
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("reference_number", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%");
            });
        }
        //account_code
        if (isset($data['account_code'])) {
            $query->where('account_code', $data['account_code']);
        }
        //search by start_date
        if (isset($data['start_date']) && $data['start_date'] != 'all') {
            $query->where('transaction_date', '>=', date('Y-m-d', strtotime($data['start_date'])) . ' 00:00:00');
        }
        //search by end_date
        if (isset($data['end_date']) && $data['end_date'] != 'all') {
            $query->where('transaction_date', '<=', date('Y-m-d', strtotime($data['end_date'])) . ' 23:59:59');
        }
        if (isset($data['tbb_id']) && !empty($data['tbb_id'])) {
            $query->where('close_monthly_book_id', '=', $data['tbb_id']);
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        } else {
            if (isset($data['tbt_id']) && !empty($data['tbt_id'])) {
                $query->where('close_yearly_book_id', '=', $data['tbt_id']);
            } else {
                $query->where('close_yearly_book_id', '=', 0);
            }
        }
        if ($all != false) {
            $query->where('edited', '!=', 0);
        }
        $hasil = $query->first();

        return $hasil;
    }
    /*
    * ===================================================================================== END ADJUSTING JOURNAL DETAIL TABLE =====================================================================================
    */





    /*
    * ===================================================================================== END ADJUSTING JOURNAL TABLE =====================================================================================
    */
    public function saldoakunUpdate($data, $mode = 'add')
    {
        $update_account['updated_by'] = auth()->user()->id ?? 1;
        $update_account['updated_at'] = date('Y-m-d H:i:s');

        $account = $this->accountGet(['code', $data['code']]);

        switch ($mode) {
            case 'delete':
                if ($data['type'] == 1) {
                    if ($account['type'] == 0) {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['kredit'] + $data['debit'];
                    } else {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                } else {
                    if ($account['type'] == 0) {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] + $data['debit'] - $data['kredit'];
                    } else {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                }
                $update_account['debit'] = $account['debit'] - $data['kredit'];
                $update_account['kredit'] = $account['kredit'] - $data['debit'];
                break;

            case 'update':
                if ($data['type'] == 1) {
                    if ($account['type'] == 0) {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['kredit'] + $data['debit'];
                    } else {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                } else {
                    if ($account['type'] == 0) {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] + $data['debit'] - $data['kredit'];
                    } else {
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                }
                $update_account['debit'] = $account['debit'] + $data['debit'];
                $update_account['kredit'] = $account['kredit'] + $data['kredit'];

                break;

            default:
                if ($data['type'] == 1) {
                    if ($account['type'] == 0) {
                        if ($data['type_journal'] == 'general') {
                            $update_account['ending_balance'] = $account['ending_balance'] - $data['kredit'] + $data['debit'];
                        }
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['kredit'] + $data['debit'];
                    } else {
                        if ($data['type_journal'] == 'general') {
                            $update_account['ending_balance'] = $account['ending_balance'] - $data['debit'] + $data['kredit'];
                        }
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                } else {
                    if ($account['type'] == 0) {
                        if ($data['type_journal'] == 'general') {
                            $update_account['ending_balance'] = $account['ending_balance'] + $data['debit'] - $data['kredit'];
                        }
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] + $data['debit'] - $data['kredit'];
                    } else {
                        if ($data['type_journal'] == 'general') {
                            $update_account['ending_balance'] = $account['ending_balance'] - $data['debit'] + $data['kredit'];
                        }
                        $update_account['adjusting_balance'] = $account['adjusting_balance'] - $data['debit'] + $data['kredit'];
                    }
                }
                $update_account['debit'] = $account['debit'] + $data['debit'];
                $update_account['kredit'] = $account['kredit'] + $data['kredit'];

                break;
        }
        Account::where('id', $account['id'])->update($update_account);
        return true;
    }
    public function getBeginningSaldo($code, $start_date, $tipe = 1)
    {
        $account = $this->accountGet(['code', $code]);
        $periode_start = config('config_apps.journal_periode_start');

        $filter['account_code'] = $account->code;
        $filter['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));

        $saldo = 0;
        if ($start_date > $periode_start) {
            $saldo += $account->beginning_balance;
            if ($tipe == 0) {
                $jurnal = $this->journalDetailSum($filter);
            } else {
                $jurnal = $this->adjustingJournalDetailSum($filter);
            }
            if ($account->type == 0) {
                $saldo = $saldo + $jurnal->debit - $jurnal->kredit;
            } else {
                $saldo = $saldo + $jurnal->kredit - $jurnal->debit;
            }
        }

        return $saldo;
    }
    /*
    * ===================================================================================== END ADJUSTING JOURNAL TABLE =====================================================================================
    */





    /*
    * ===================================================================================== START CLOSE MONTHLY BOOK TABLE =====================================================================================
    */
    public function closeMonthlyBookList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if ($order == false) $order = ['id', 'ASC'];
        //start query
        $query = CloseMonthlyBook::query()->with(['userInput']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['closing_date']) && !empty($data['closing_date'])) {
            $query->whereDate('closing_date', '=', date('Y-m-d', strtotime($data['closing_date'])));
        }
        //order
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function closeMonthlyBookGet($data = [])
    {
        //if $data not array
        if (!is_array($data)) {
            $query = CloseMonthlyBook::find($data);
        } else {
            $query = CloseMonthlyBook::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function closeMonthlyBookSave($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['closing_date'] = $data['closing_date'] . date(' H:i:s');
        $start_periode = config('config_apps.journal_periode_start');
        $balance = $this->ledger(['start_date' => $start_periode, 'end_date' => $data['end_periode']], true);
        $data['data'] = [];
        foreach ($balance as $key => $value) {
            $data['data'][$key] = [
                'id' => $value->id,
                'code' => $value->code,
                'name' => $value->name,
                'type' => $value->type,
                'beginning_balance' => $value->beginning_balance,
                'debit' => $value->debit,
                'kredit' => $value->kredit,
                'adjusting_balance' => $value->adjusting_balance,
                'group_id' => $value->group_id,
                'saldo_tahun_lalu' => $value->saldo_tahun_lalu,
                'saldo_awal' => $value->saldo_awal,
                'saldo_akhir' => $value->saldo_akhir,
                'saldo_penyesuaian' => $value->saldo_penyesuaian,
            ];
        }
        $data['data'] = json_encode($data['data']);
        $save = CloseMonthlyBook::create($data);
        $last_close_monthly_book_id = $save->id;
        $update_journal = [
            'updated_by' => $data['updated_by'],
            'updated_at' => $data['updated_at'],
            'close_monthly_book_id' => $last_close_monthly_book_id
        ];
        Journal::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_monthly_book_id', 0)->update($update_journal);
        JournalDetail::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_monthly_book_id', 0)->update($update_journal);
        AdjustingJournal::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_monthly_book_id', 0)->update($update_journal);
        AdjustingJournalDetail::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_monthly_book_id', 0)->update($update_journal);
        return true;
    }
    /*
    * ===================================================================================== END CLOSE MONTHLY BOOK TABLE =====================================================================================
    */





    /*
    * ===================================================================================== START CLOSE MONTHLY BOOK TABLE =====================================================================================
    */
    public function closeYearlyBookList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if ($order == false) $order = ['id', 'ASC'];
        //start query
        $query = CloseYearlyBook::query()->with(['userInput']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['closing_date']) && !empty($data['closing_date'])) {
            $query->whereDate('closing_date', '=', date('Y-m-d', strtotime($data['closing_date'])));
        }
        //order
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function closeYearlyBookGet($data = [])
    {
        //if $data not array
        if (!is_array($data)) {
            $query = CloseYearlyBook::find($data);
        } else {
            $query = CloseYearlyBook::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function closeYearlyBookSave($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['closing_date'] = $data['closing_date'] . date(' H:i:s');
        $balance = $this->ledger(['start_date' => $data['start_periode'], 'end_date' => $data['end_periode']]);
        // $data['data'] = json_encode($balance);
        $data['data'] = [];
        foreach ($balance as $key => $value) {
            $data['data'][$key] = [
                'id' => $value->id,
                'code' => $value->code,
                'name' => $value->name,
                'type' => $value->type,
                'beginning_balance' => $value->beginning_balance,
                'debit' => $value->debit,
                'kredit' => $value->kredit,
                'adjusting_balance' => $value->adjusting_balance,
                'group_id' => $value->group_id,
                'saldo_tahun_lalu' => $value->saldo_tahun_lalu,
                'saldo_awal' => $value->saldo_awal,
                'saldo_akhir' => $value->saldo_akhir,
                'saldo_penyesuaian' => $value->saldo_penyesuaian,
            ];
        }
        $data['data'] = json_encode($data['data']);

        $save = CloseYearlyBook::create($data);
        $last_close_yearly_book_id = $save->id;
        $update_journal = [
            'updated_by' => $data['updated_by'],
            'updated_at' => $data['updated_at'],
            'close_yearly_book_id' => $last_close_yearly_book_id
        ];
        Journal::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_yearly_book_id', 0)->update($update_journal);
        JournalDetail::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_yearly_book_id', 0)->update($update_journal);
        AdjustingJournal::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_yearly_book_id', 0)->update($update_journal);
        AdjustingJournalDetail::whereBetween('transaction_date', [$data['start_periode'] . ' 00:00:00', $data['end_periode'] . ' 23:59:59'])->where('close_yearly_book_id', 0)->update($update_journal);
        foreach ($balance as $key => $value) {
            $update_account = [
                'beginning_balance' => $value['saldo_penyesuaian'],
                'ending_balance' => $value['saldo_penyesuaian'],
                'adjusting_balance' => $value['saldo_penyesuaian'],
                'saldo_tahun_lalu' => $value['saldo_penyesuaian'],
                'debit' => 0,
                'kredit' => 0,
            ];
            $this->accountUpdate($value['id'], $update_account);
        }
        return true;
    }
    /*
    * ===================================================================================== END CLOSE MONTHLY BOOK TABLE =====================================================================================
    */





    /*
    * ===================================================================================== START TABLE ACCOUNTS =====================================================================================
    */
    public function shuConfigList($data = [])
    {
        //start query
        $query = ShuConfig::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("allocation", "like", "%{$data['q']}%")
                    ->orWhere("account", "like", "%{$data['q']}%");
            });
        }

        return $query->get();
    }
    public function shuConfigGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = ShuConfig::find($data);
        } else {
            $query = ShuConfig::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function shuConfigSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id ?? 1;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        ShuConfig::create($data);
        return true;
    }
    public function shuConfigUpdate($id, $data = [])
    {
        $shuConfig = $this->shuConfigGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $shuConfig->update($data);
        return true;
    }
    /*
    * ===================================================================================== END CLOSE MONTHLY BOOK TABLE =====================================================================================
    */


    public function jurnalkasbank($data = [], $limit = false, $order = false, $paginate = true)
    {
        $query = AdjustingJournalDetail::query()->with(['journal', 'account']);
        if (isset($data['date'])) {
            $query->whereDate('transaction_date', $data['date']);
        }
        if (isset($data['code'])) {
            $query->whereIn('account_code', $data['code']);
        }
        if (!$order) {
            $order = ['transaction_date', 'asc'];
        }
        //order
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }
        return $result;
    }
}