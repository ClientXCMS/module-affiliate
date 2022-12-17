<?php

namespace App\Affiliate\Database;

use ClientX\Database\Query;

class AffiliateLogTable extends \ClientX\Database\Table
{
    protected $table = 'affiliate_logs';
    protected $entity = \stdClass::class;


    public function makeQueryForAdmin(?array $search = null, $order = "desc"): Query
    {
        $query = parent::makeQueryForAdmin($search, $order);
        $query->select('CONCAT(u.firstname," ",u.lastname) as username', 'a.*')
            ->join('users as u', 'u.id = a.user_id');

        $query->order = [];
        $query->order('a.id');
        return $query;
    }


    public function findForUser(int $id)
    {
        if ($this->order === null) {
            return $this->makeQuery()->where('aff_id = :id')->params(compact('id'));
        }
        return $this->makeQuery()->where('aff_id = :id')->params(compact('id'))->order($this->order);
    }
}