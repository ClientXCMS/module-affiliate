<?php

namespace App\Affiliate\Database;

use ClientX\Database\Query;

class AffiliateWithdrawalTable extends \ClientX\Database\Table
{

    protected $table = "affiliates_withdrawals";
    protected $entity = \stdClass::class;

    public function makeWithdrawal(int $userId, float $amount){
        $this->insert([
            'user_id' => $userId,
            'amount' => $amount,
            'state' => 'PENDING',
        ]);
    }

    public function makeQueryForAdmin(?array $search = null, $order = "desc"): Query
    {
        $query = parent::makeQueryForAdmin($search, $order);
        $query->select('CONCAT(u.firstname," ",u.lastname) as username', 'a.*')
            ->join('users as u', 'u.id = a.user_id');
        $query->order = [];
        $query->order('a.id');
        return $query;
    }

    public function countPending(): int
    {
        return $this->makeQuery()->where('state = :state')->setParameter('state', 'PENDING')->count();
    }

}