<?php

namespace App\Affiliate;

use App\Affiliate\Database\AffiliateLogTable;
use App\Affiliate\Database\AffiliateTable;
use App\Affiliate\Database\AffiliateUsersTable;
use App\Affiliate\Database\AffiliateWithdrawalTable;
use App\Shop\Entity\Transaction;
use ClientX\App;
use ClientX\Database\NoRecordException;
use ClientX\Helpers\Currency;

class AffiliateService
{
    private float $onregistration = 0;
    private float $onorder = 0;
    /**
     * @var int|string percentage|fixed
     */
    private string $onordertype;
    private string $currency;
    private AffiliateUsersTable $usersTable;
    private AffiliateLogTable $logTable;
    private bool $enabled;
    private AffiliateTable $affiliateTable;
    private AffiliateWithdrawalTable $withdrawalTable;
    private int $minwithdraw;

    public function __construct(
        App $app,
        AffiliateUsersTable $usersTable,
        AffiliateLogTable $logTable,
        AffiliateTable $affiliateTable,
        AffiliateWithdrawalTable $withdrawalTable,
        string $onorder,
        string $onordertype,
        string $onregistration,
        string $minwithdraw,
        string $currency
    ) {
        $this->onorder = $onorder;
        $this->onordertype = $onordertype;
        $this->onregistration = $onregistration;
        $this->currency = $currency;
        $this->usersTable = $usersTable;
        $this->logTable = $logTable;
        $this->enabled = $app->moduleIsLoaded(AffiliateService::class);
        $this->affiliateTable = $affiliateTable;
        $this->withdrawalTable = $withdrawalTable;
        $this->minwithdraw = $minwithdraw;
    }

    public function generateAmount(string $event, float $amount)
    {
        if ($event == 'registration') {
            return $this->onregistration;
        }
        if ($event == 'order') {
            if ($this->onordertype == 'percentage') {
                return (0 +  ($this->onorder / 100 )) * $amount;
            }
            if ($this->onordertype == 'fixed') {
                return $this->onorder;
            }
            return $this->onorder;
        }
    }

    public function textAmount(string $event)
    {
        if ($event == 'registration') {
            return $this->onregistration . ' ' . Currency::symbolFor($this->currency);
        }
        if ($event == 'order') {
            if ($this->onordertype == 'percentage') {
                return $this->onorder  . '%';
            }
            if ($this->onordertype == 'fixed') {
                return $this->onorder . ' ' . Currency::symbolFor($this->currency);
            }
        }
    }

    public function addUserLog(int $userId, int $refid)
    {
        return $this->usersTable->insert([
            'ref_id' => $refid,
            'target_id' => $userId,
        ]);
    }

    public function onOrder(Transaction $transaction)
    {
        // Si le module n'est pas activé on ne fait rien
        if (!$this->enabled) {
            return;
        }
        if ($transaction->getState() !== Transaction::COMPLETED){
            return;
        }
        $user = $transaction->getUser();
        // vérifier s'il est parainer par quelqu'un
        try {
            $who = $this->usersTable->findBy("target_id", $user->getId());
            $aff = $this->affiliateTable->findBy("user_id", $who->refId);
            $this->addOrderLog($user->getId(), $aff->getId(), $transaction->getId(), $transaction->total());
            $aff->addWithdrawn($this->generateAmount('order', $transaction->total()));
            $this->affiliateTable->saveAff($aff);
        } catch (NoRecordException $e) {
            // Il est parrainé par personne
        }
    }


    public function addOrderLog(int $userId, int $affid, int $id, float $amount)
    {

        $this->logTable->insert([
            'description' => "New order #$id",
            'amount' => $this->generateAmount('order', $amount),
            'user_id' => $userId,
            'aff_id' => $affid,
        ]);
    }
    public function addRegistrationLog(int $userId, int $affid, int $id)
    {

        $this->logTable->insert([
            'description' => "New registration #$id",
            'amount' => $this->generateAmount('registration', 0),
            'user_id' => $userId,
            'aff_id' => $affid,
        ]);
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function findLogsForUser(int $userId)
    {
        return $this->logTable->findForUser($userId);
    }

    public function findWithdrawalsForUser(int $userId)
    {
        return $this->withdrawalTable->findForUser($userId);
    }

    public function makeWithdrawal(int $userId, int $amount)
    {
        if ($amount >= $this->minwithdraw) {
            $this->withdrawalTable->makeWithdrawal($userId, $amount);
            return true;
        }
        return false;
    }
}
