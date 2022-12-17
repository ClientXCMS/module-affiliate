<?php

namespace App\Affiliate\Database;

use App\Affiliate\Entity\Affiliate;
use ClientX\Database\Query;
use ClientX\Database\Table;

class AffiliateTable extends Table
{

    protected $table = 'affiliates';
    protected $entity = Affiliate::class;
    protected $element = "user_id";

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userTable = new AffiliateUsersTable($pdo);
    }

    public function isAffiliated(int $userId):bool
    {
        return $this->makeQuery()
                ->where('user_id = :userId')
                ->params(compact('userId'))
                ->count() == 1;
    }

    public function find(int $id)
    {
        $affiliate = parent::find($id);
        $users = $this->userTable->findBy("ref_id", $affiliate->getUserId());
        $affiliate->setAffiliated($users);
        return $affiliate;
    }

    public function saveAff(Affiliate $aff)
    {
        $this->update($aff->getId(), ['visitors' => $aff->getVisitors(), 'withdrawn' => $aff->getWithdrawn(), 'signups' => $aff->getSignups(), 'balance' => $aff->getBalance()]);
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
}