<?php

namespace App\Affiliate\Actions;

use App\Affiliate\AffiliateService;
use App\Affiliate\Database\AffiliateTable;
use App\Affiliate\Database\AffiliateWithdrawalTable;
use App\Auth\DatabaseUserAuth;
use ClientX\Session\FlashService;
use ClientX\Translator\Translater;
use GuzzleHttp\Psr7\Response;
use function ClientX\request;

class AffiliateWithdrawalAction extends \ClientX\Actions\Action
{
    private AffiliateTable $affiliateTable;
    private AffiliateService $service;

    public function __construct(AffiliateTable $affiliateTable, DatabaseUserAuth $auth,AffiliateService $service,FlashService $flash, Translater $translater)
    {
        $this->flash = $flash;
        $this->translater = $translater;
        $this->auth = $auth;
        $this->affiliateTable = $affiliateTable;
        $this->service = $service;
    }

    public function __invoke()
    {
        if (!$this->affiliateTable->isAffiliated($this->getUserId())) {
            return new Response(404);
        }
        $aff = $this->affiliateTable->findBy("user_id", $this->getUserId());
        $success = $this->service->makeWithdrawal($this->getUserId(), $aff->getWithdrawn());
        if ($success == false){
            return new Response(404);
        }
        //$aff->addBalance($aff->getWithdrawn());

        $aff->setWithdrawn(0);
        $this->affiliateTable->saveAff($aff);
        $this->success($this->trans('affiliate.client.requestawithdrawalsuccess'));
        return $this->back(request());
    }
}
