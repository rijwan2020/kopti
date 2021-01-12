<?php

namespace App\Classes;

use App\Model\Deposit;
use App\Model\DepositBill;
use App\Model\DepositBillUpload;
use App\Model\DepositBook;
use App\Model\DepositTransaction;
use App\Model\DepositTransactionUpload;
use App\Model\DepositType;
use App\Model\DepositUpload;
use Illuminate\Support\Facades\DB;

class DepositClass
{
    public $error = '', $last_deposit_id = 0;
    public function __construct()
    {
        DB::enableQueryLog();
        $this->accountancy = new AccountancyClass();
        $this->master = new MasterClass();
    }

    public function calculateSimpanan($deposit_id)
    {
        $transaction = $this->depositTransactionSum(['deposit_id' => $deposit_id]);
        $saldo = $transaction->kredit - $transaction->debit;

        Deposit::find($deposit_id)->update(['balance' => $saldo]);

        return true;
    }



    /*
    * =============================================================================================== START DEPOSIT ===============================================================================================
    */
    public function depositList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'asc'];
        //start query
        $query = Deposit::query()->with(['member', 'region', 'type']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("account_number", "like", "%{$data['q']}%");
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', $data['type_id']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }
        $query->where('deleted_by', 0);
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
    public function depositGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Deposit::with(['member', 'type', 'region'])->find($data);
        } else {
            $query = Deposit::where($data[0], $data[1])->with(['member', 'type', 'region'])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function depositSave($data = [], $jurnal = 1)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $member = $this->master->memberGet($data['member_id']);
        $data['region_id'] = $member->region_id;
        $account = $data['account'];
        unset($data['account']);

        if ($this->depositGet(['account_number', $data['account_number']])) {
            $this->error = 'No rekening sudah digunakan.';
            return false;
        }

        if ($data['deposit_type_id'] == 1) {
            if ($this->depositList(['type_id' => 1, 'member_id' => $data['member_id']])->count() > 0) {
                $this->error = 'Anggota sudah memiliki simpanan pokok.';
                return false;
            }
        }
        if ($data['deposit_type_id'] == 2) {
            if ($this->depositList(['type_id' => 2, 'member_id' => $data['member_id']])->count() > 0) {
                $this->error = 'Anggota sudah memiliki simpanan wajib.';
                return false;
            }
        }
        if ($member->status == 2 and ($data['deposit_type_id'] == 1 || $data['deposit_type_id'] == 2)) {
            $this->error = 'Simpanan pokok dan wajib tidak tersedia untuk non anggota.';
            return false;
        }

        $deposit = Deposit::create($data);
        $this->last_deposit_id = $deposit->id;
        // Save transaction
        $transaction = [
            'deposit_id' => $this->last_deposit_id,
            'account' => $account,
            'transaction_date' => $data['registration_date'] . date(' H:i:s'),
            'kredit' => $data['beginning_balance'],
            'debit' => 0,
            'type' => 1,
            'reference_number' => 'TRXS-' . date('YmdHis'),
            'note' => 'Saldo Awal ' . $data['account_number']
        ];
        // if ($data['deposit_type_id'] == 2) {
        //     $transaction['month'] = 1;
        // }
        $this->depositTransactionSave($transaction, $jurnal, 1);

        // update deposit type
        $type = $this->depositTypeGet($data['deposit_type_id']);
        $type->next_code += 1;
        $type->updated_at = date('Y-m-d H:i:s');
        $type->updated_by = auth()->user()->id;
        $type->update();
        return true;
    }
    public function depositUpdate($id, $data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
    }
    public function depositDelete($id, $date)
    {
        $data['deleted_at'] = $date ?? date('Y-m-d H:i:s');
        $data['deleted_by'] = auth()->user()->id;

        Deposit::where('id', $id)->update($data);
        DepositTransaction::where('deposit_id', $id)->update($data);
        DepositBill::where('deposit_id', $id)->update($data);
        DepositBook::where('deposit_id', $id)->update($data);
        return true;
    }
    public function depositSum($data = [], $limit = false)
    {
        //start query
        $query = Deposit::query()->with(['member', 'region', 'type']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("account_number", "like", "%{$data['q']}%");
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', $data['type_id']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }
        $query->where('deleted_by', 0);
        //limit
        if ($limit != false) {
            $result = $query->limit($limit)->sum('balance');
        } else {
            $result = $query->sum('balance');
        }

        return $result;
    }
    public function depositCount($data = [], $limit = false)
    {
        //start query
        $query = Deposit::query()->with(['member', 'region', 'type']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("account_number", "like", "%{$data['q']}%");
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', $data['type_id']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }
        $query->where('deleted_by', 0);
        //limit
        if ($limit != false) {
            $result = $query->limit($limit)->count();
        } else {
            $result = $query->count();
        }

        return $result;
    }
    /*
    * =============================================================================================== END DEPOSIT ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT TRANSACTION ===============================================================================================
    */
    public function depositTransactionList($data = [], $limit = false, $order = ['transaction_date', 'desc'], $paginate = true)
    {
        //start query
        $query = DepositTransaction::query()->with(['member', 'region', 'deposit', 'depositType']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("note", "like", "%{$data['q']}%")
                    ->orWhere('reference_number', 'like', "%{$data['q']}%");

                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("deposit", function ($q) use ($data) {
                    $q->where('account_number', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("region", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%");
                });
            });
        }
        $query->where('deleted_by', 0);

        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->whereDate('transaction_date', '>=', $data['start_date']);
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->whereDate('transaction_date', '<=', $data['end_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['deposit_id']) && $data['deposit_id'] != 'all') {
            $query->where('deposit_id', '=', $data['deposit_id']);
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', '=', $data['type_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
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
    public function depositTransactionGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = DepositTransaction::with(['member', 'depositType', 'region', 'deposit'])->find($data);
        } else {
            $query = DepositTransaction::where($data[0], $data[1])->with(['member', 'depositType', 'region', 'deposit'])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function depositTransactionSave($data, $jurnal = 1, $new_data = 0)
    {
        $data['created_by'] = $data['updated_by'] = $update_deposit['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = $update_deposit['updated_at'] = date('Y-m-d H:i:s');
        $deposit = $this->depositGet($data['deposit_id']);
        // if ($data['type'] == 1 && $deposit->deposit_type_id == 2) {
        //     $month = $data['month'];
        //     unset($data['month']);
        //     $data['kredit'] = $data['kredit'] * $month;
        // }
        $type = in_array($data['type'], [2, 4, 6]) ? 1 : 0;
        $journal = [
            'transaction_date' => $data['transaction_date'],
            'reference_number' => $data['reference_number'],
            'name' => $data['note'],
            'type' => $type,
            'unit' => 1,
            'member_id' => $deposit->member_id,
            'warehouse_id' => $data['warehouse_id'] ?? 0
        ];
        if ($type == 0) {
            $journal['detail'] = [
                [
                    'account_code' => $data['account'],
                    'type' => 'dana_from',
                    'amount' => $data['kredit'] > 0 ? $data['kredit'] : $data['debit']
                ],
                [
                    'account_code' => $deposit->type->account_code,
                    'type' => 'dana_to',
                    'amount' => $data['kredit'] > 0 ? $data['kredit'] : $data['debit']
                ]
            ];
        } else {
            $journal['detail'] = [
                [
                    'account_code' => $deposit->type->account_code,
                    'type' => 'dana_from',
                    'amount' => $data['kredit'] > 0 ? $data['kredit'] : $data['debit']
                ],
                [
                    'account_code' => $data['account'],
                    'type' => 'dana_to',
                    'amount' => $data['kredit'] > 0 ? $data['kredit'] : $data['debit']
                ]
            ];
        }
        $update_deposit['balance'] = $deposit->balance + $data['kredit'] - $data['debit'];
        $update_deposit['last_transaction'] = $data['transaction_date'];
        if ($jurnal == 1) {
            $this->accountancy->adjustingJournalSave($journal);
            $this->accountancy->journalSave($journal);
            $data['journal_id'] = $this->accountancy->last_journal_id;
        }
        $data['member_id'] = $deposit->member_id;
        $data['region_id'] = $deposit->region_id;
        $data['deposit_type_id'] = $deposit->deposit_type_id;
        unset($data['account'], $data['warehouse_id']);
        DepositTransaction::create($data);
        $deposit->update($update_deposit);

        $this->calculateSimpanan($deposit->id);

        $book = [
            'deposit_id' => $data['deposit_id'],
            'transaction_date' => $data['transaction_date'],
            'debit' => $data['debit'],
            'kredit' => $data['kredit'],
            'type_transaction' => $data['type']
        ];
        $this->depositBookSave($book);
        if ($new_data == 0) {
            // if ($deposit->deposit_type_id == 2 && $data['type'] == 1) {
            //     $bill = [
            //         'jml_bulan' => $month,
            //         'deposit_id' => $deposit->id,
            //         'last_transaction' => $data['transaction_date']
            //     ];
            //     $this->depositBillUpdate($bill);
            // }
        }
        return true;
    }
    public function depositTransactionSum($data = [])
    {
        //start query
        $query = DepositTransaction::query();
        $query->select(DB::raw('sum(kredit) as kredit, sum(debit) as debit'));
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("note", "like", "%{$data['q']}%")
                    ->orWhere('reference_number', 'like', "%{$data['q']}%");

                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("deposit", function ($q) use ($data) {
                    $q->where('account_number', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("region", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%");
                });
            });
        }
        $query->where('deleted_by', 0);

        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->whereDate('transaction_date', '>=', $data['start_date']);
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->whereDate('transaction_date', '<=', $data['end_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['deposit_id']) && $data['deposit_id'] != 'all') {
            $query->where('deposit_id', '=', $data['deposit_id']);
        }
        if (isset($data['type']) && $data['type'] != 'all') {
            $query->where('type', '=', $data['type']);
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', '=', $data['type_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }
        $hasil = $query->first();

        return $hasil;
    }
    /*
    * =============================================================================================== END DEPOSIT TRANSACTION ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT TYPE ===============================================================================================
    */
    public function depositTypeList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'asc'];
        //start query
        $query = DepositType::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("code", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['type']) && $data['type'] != 'all') {
            $query->where('type', $data['type']);
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
    public function depositTypeGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = DepositType::find($data);
        } else {
            $query = DepositType::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function depositTypeSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $induk_account = $this->accountancy->accountGet(['code', $data['account']]);
        unset($data['account']);
        if ($this->depositTypeGet(['code', $data['code']])) {
            $this->error = 'Kode sudah digunakan.';
            return false;
        }
        $account = [
            'parent_id' => $induk_account->id,
            'name' => $data['name'],
            'type' => $induk_account->type,
            'group_id' => $data['group_id'],
        ];
        $this->accountancy->accountSave($account);
        $account_id = $this->accountancy->last_account_id;
        $getAccount = $this->accountancy->accountGet($account_id);
        $data['account_code'] = $getAccount->code;
        unset($data['group_id']);
        DepositType::create($data);
        return true;
    }
    public function depositTypeUpdate($id, $data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;

        $depositType = $this->depositTypeGet($id);
        if ($data['code'] != $depositType->code) {
            if ($this->depositTypeGet(['code', $data['code']])) {
                $this->error = 'Kode sudah digunakan.';
                return false;
            }
        }
        $depositType->update($data);
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT TYPE ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT BILL ===============================================================================================
    */
    public function depositBillList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['id', 'asc'];
        //start query
        $query = DepositBill::query()->with(['member', 'region', 'deposit', 'depositType']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("note", "like", "%{$data['q']}%")
                    ->orWhere('reference_number', 'like', "%{$data['q']}%");

                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("deposit", function ($q) use ($data) {
                    $q->where('account_number', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("region", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->whereDate('transaction_date', '>=', $data['start_date']);
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->whereDate('transaction_date', '<=', $data['end_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['deposit_id']) && $data['deposit_id'] != 'all') {
            $query->where('deposit_id', $data['deposit_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }

        $query->where('deleted_by', 0);

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
    public function depositBillGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = DepositBill::find($data);
        } else {
            $query = DepositBill::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function depositBillSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        $deposit = $this->depositGet($data['deposit_id']);
        $data['member_id'] = $deposit->member_id;
        $data['region_id'] = $deposit->region_id;
        $data['deposit_type_id'] = $deposit->deposit_type_id;
        $data['last_transaction'] = $deposit->last_transaction;
        $bill_date = date('d', strtotime($deposit->registration_date));
        $data['billing_date'] = $bill_date < 28 ? $bill_date : 28;
        $data['next_bill'] = date('Y-m-', strtotime('+1 month', strtotime($deposit->registration_date))) . str_pad($bill_date, 2, 0, STR_PAD_LEFT);

        DepositBill::create($data);
        return true;
    }
    public function depositBillUpdate($data = [])
    {
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $bill = $this->depositBillGet(['deposit_id', $data['deposit_id']]);
        if ($bill->deposit_type_id == 2) {
            $data['next_bill'] = date('Y-m-d', strtotime('+' . $data['jml_bulan'] . 'month', strtotime($bill->next_bill)));
            unset($data['jml_bulan']);
        }
        $bill->update($data);
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT BILL ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT BOOK ===============================================================================================
    */
    public function depositBookList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['id', 'asc'];
        //start query
        $query = DepositBook::query()->with(['member', 'region', 'deposit', 'depositType', 'userInput']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("note", "like", "%{$data['q']}%")
                    ->orWhere('reference_number', 'like', "%{$data['q']}%");

                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("deposit", function ($q) use ($data) {
                    $q->where('account_number', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("region", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->whereDate('transaction_date', '>=', $data['start_date']);
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->whereDate('transaction_date', '<=', $data['end_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['deposit_id']) && $data['deposit_id'] != 'all') {
            $query->where('deposit_id', $data['deposit_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
        }

        $query->where('deleted_by', 0);

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
    public function depositBookGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = DepositBook::find($data);
        } else {
            $query = DepositBook::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function depositBookSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        $deposit = $this->depositGet($data['deposit_id']);
        $data['member_id'] = $deposit->member_id;
        $data['deposit_type_id'] = $deposit->deposit_type_id;
        $data['region_id'] = $deposit->region_id;
        $data['balance'] = $deposit->balance;
        DepositBook::create($data);
        return true;
    }
    public function depositBookReset($data)
    {
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $query = DepositBook::query();
        if (isset($data['id'])) {
            $query->where('id', $data['id']);
            unset($data['id']);
        }
        if (isset($data['deposit_id'])) {
            $query->where('deposit_id', $data['deposit_id']);
            unset($data['deposit_id']);
        }
        $query->update($data);
        return true;
    }
    public function depositBookPrint($id = [])
    {
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['print'] = 1;
        DepositBook::whereIn('id', $id)->update($data);
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT BOOK ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT UPLOAD ===============================================================================================
    */
    public function depositUploadList($data = [], $limit = false, $order = ['account_number', 'asc'], $paginate = true)
    {
        //start query
        $query = DepositUpload::query()->with(['member', 'region', 'type']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("account_number", "like", "%{$data['q']}%");
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['type_id']) && $data['type_id'] != 'all') {
            $query->where('deposit_type_id', $data['type_id']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
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
    public function depositUploadConfirm($confirm)
    {
        if ($confirm == 0) {
            DepositUpload::truncate();
        } else {
            $data = $this->depositUploadList();
            foreach ($data as $key => $value) {
                $upload['member_id'] = $value->member_id;
                $upload['deposit_type_id'] = $value->deposit_type_id;
                $upload['account_number'] = $value->account_number;
                $upload['beginning_balance'] = $value->beginning_balance;
                $upload['registration_date'] = $value->registration_date;
                $upload['account'] = $value->account_code;
                $ok = true;
                if ($value->member->status == 2) {
                    $ok = false;
                }
                if ($value->member->status == 0) {
                    if ($value->deposit_type_id == 1 || $value->deposit_type_id == 2) {
                        $ok = false;
                    }
                }
                if ($ok) {
                    $this->depositSave($upload, $value->jurnal);
                    /*if ($value->deposit_type_id == 1 || $value->deposit_type_id == 2) {
                        $bill = [
                            'deposit_id' => $this->last_deposit_id,
                            'principal_balance' => 0,
                            'obligatory_balance' => 0
                        ];
                        if ($value->deposit_type_id == 1) {
                            $bill['principal_balance'] = config('config_apps.besar_sp');
                        }
                        if ($value->deposit_type_id == 2) {
                            $bill['obligatory_balance'] = config('config_apps.besar_sw');
                        }
                        $this->depositBillSave($bill);
                    }*/
                }
            }
            DepositUpload::truncate();
        }
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT UPLOAD ===============================================================================================
    */



    /*
    * =============================================================================================== START DEPOSIT BILL UPLOAD ===============================================================================================
    */
    public function depositBillUploadList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['id', 'asc'];
        //start query
        $query = DepositBillUpload::query()->with(['member', 'deposit']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {

                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });

                $q->orWhereHas("deposit", function ($q) use ($data) {
                    $q->where('account_number', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['deposit_id']) && $data['deposit_id'] != 'all') {
            $query->where('deposit_id', $data['deposit_id']);
        }
        if (isset($data['member_id']) && $data['member_id'] != 'all') {
            $query->where('member_id', $data['member_id']);
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
    public function depositBillUploadConfirm($confirm)
    {
        if ($confirm == 0) {
            DepositBillUpload::truncate();
        } else {
            $data = $this->depositBillUploadList();
            foreach ($data as $key => $value) {
                $upload['deposit_id'] = $value->deposit_id;
                $upload['transaction_date'] = $value->transaction_date . date(' H:i:s');
                $upload['type'] = 1;
                $upload['debit'] = 0;
                $upload['reference_number'] = 'TRXS-' . date('YmdHis', strtotime($value->created_at));
                $upload['account'] = $value->account;
                $upload['note'] = 'Setoran ' . $value->deposit->account_number;
                $upload['kredit'] = $value->bayar;
                if ($value->deposit->deposit_type_id == 2) {
                    $upload['kredit'] = $value->deposit->bill->obligatory_balance;
                    $bulan = $value->bayar / $upload['kredit'];
                    $upload['month'] = floor($bulan);
                }
                $this->depositTransactionSave($upload);
            }
            DepositBillUpload::truncate();
        }
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT BILL UPLOAD ===============================================================================================
    */



    /*
    * =============================================================================================== END DEPOSIT TRANSACTION UPLOAD ===============================================================================================
    */
    public function depositTransactionUploadList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'asc'];
        //start query
        $query = DepositTransactionUpload::query()->with(['member']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_rekening", "like", "%{$data['q']}%")
                    ->orWhere('no_ref', "like", "%{$data['q']}%")
                    ->orWhere('keterangan', "like", "%{$data['q']}%");
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
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
    public function depositTransactionUploadConfirm($confirm)
    {
        if ($confirm == 0) {
            DepositTransactionUpload::truncate();
        } else {
            $data = $this->depositTransactionUploadList();
            foreach ($data as $key => $value) {
                $upload['deposit_id'] = $value->deposit_id;
                $upload['transaction_date'] = $value->tanggal_transaksi;
                $upload['type'] = $value->jenis_transaksi;
                if (in_array($upload['type'], [1, 3, 5])) {
                    $upload['kredit'] = $value->jumlah;
                    $upload['debit'] = 0;
                } else {
                    $upload['debit'] = $value->jumlah;
                    $upload['kredit'] = 0;
                }
                $upload['reference_number'] = $value->no_ref;
                $upload['account'] = $value->akun;
                $upload['note'] = $value->keterangan;
                $this->depositTransactionSave($upload, $value->jurnal);
            }
            DepositTransactionUpload::truncate();
        }
        return true;
    }
    /*
    * =============================================================================================== END DEPOSIT TRANSACTION UPLOAD ===============================================================================================
    */
}