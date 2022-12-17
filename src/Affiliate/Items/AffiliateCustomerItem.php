<?php

namespace App\Affiliate\Items;

use App\Affiliate\Database\AffiliateLogTable;
use App\Affiliate\Database\AffiliateUsersTable;
use App\Affiliate\Database\AffiliateWithdrawalTable;
use App\Auth\User;
use App\Fund\FundService;
use ClientX\Database\NoRecordException;
use ClientX\Database\Query;
use ClientX\Navigation\NavigationItemInterface;
use ClientX\Renderer\RendererInterface;

class AffiliateCustomerItem implements NavigationItemInterface
{

    private User $user;
    private AffiliateUsersTable $affiliateUsersTable;
    private AffiliateLogTable $affiliateLogTable;
    private AffiliateWithdrawalTable $affiliateWithdrawalTable;

    public function __construct(AffiliateUsersTable $affiliateUsersTable, AffiliateLogTable $affiliateLogTable, AffiliateWithdrawalTable $affiliateWithdrawalTable)
    {
        $this->affiliateUsersTable = $affiliateUsersTable;
        $this->affiliateLogTable = $affiliateLogTable;
        $this->affiliateWithdrawalTable = $affiliateWithdrawalTable;
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return 80;
    }

    /**
     * @inheritDoc
     */
    public function render(RendererInterface $renderer): string
    {
        try {
            $isAff = $this->affiliateUsersTable->findBy("target_id", $this->user->getId());
        } catch (NoRecordException $e) {
            $isAff = null;
        }
        $id = $this->user->getId();
        $items = $this->affiliateUsersTable->makeQuery()->where('ref_id = :id')->params(compact('id'))->order('a.id DESC')->join('users as u', 'u.id = a.target_id')->select('CONCAT(u.firstname," ",u.lastname) as username', 'a.*');;

        $logs = $this->affiliateLogTable->findForUser($this->user->getId());
        $withdrawals = $this->affiliateWithdrawalTable->findForUser($this->user->getId());
        return $renderer->render('@affiliate_admin/customer', compact('items', 'isAff', 'logs', 'withdrawals'));
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
