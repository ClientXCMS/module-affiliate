<?php

namespace App\Affiliate\Actions\Admin;

use App\Affiliate\Database\AffiliateTable;
use App\Affiliate\Database\AffiliateWithdrawalTable;
use App\Affiliate\Entity\Affiliate;
use ClientX\Session\FlashService;

use App\Auth\Database\UserTable;
use ClientX\Translator\Translater;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateAdminMakeWithdrawalsAction extends \ClientX\Actions\Action
{

    private AffiliateTable $affiliateTable;
    private AffiliateWithdrawalTable $affiliateWithdrawalTable;
    private UserTable $userTable;

    public function __construct(AffiliateWithdrawalTable $affiliateWithdrawalTable,Translater $translater, FlashService $flash,AffiliateTable $affiliateTable, UserTable $userTable)
    {
        $this->affiliateTable = $affiliateTable;
        $this->affiliateWithdrawalTable = $affiliateWithdrawalTable;
        $this->userTable = $userTable;
        $this->translater = $translater;
        $this->flash = $flash;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $withdrawal = $this->affiliateWithdrawalTable->find($request->getAttribute('id'));
        /** @var Affiliate $affiliate */
        $affiliate = $this->affiliateTable->findBy("user_id", $withdrawal->userId);
        if (array_key_exists('refuse', $params)){
            $state = 'REFUSED';
        } else if (array_key_exists('accept_manu', $params)){
            $state = 'ACCEPTED';
        } else if (array_key_exists('accept_auto', $params)){
            $state = 'ACCEPTED';
            $affiliate->addBalance($withdrawal->amount);
            $user = $this->userTable->find($affiliate->getUserId());
            $user->addFund($withdrawal->amount);
            $this->userTable->updateWallet($user);
            $this->affiliateTable->saveAff($affiliate);
        }
        $this->affiliateWithdrawalTable->update($withdrawal->id,['state' => $state]);

        $this->success($this->trans("affiliate.admin.success"));
        return $this->back($request);
    }
}
