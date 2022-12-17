<?php

namespace App\Affiliate;

use App\Account\Event\SignupEvent;
use App\Affiliate\Database\AffiliateTable;
use ClientX\Database\NoRecordException;
use function ClientX\request;

class AffiliateSignup
{

    private AffiliateService $service;
    private AffiliateTable $affiliateTable;

    public function __construct(AffiliateService $service, AffiliateTable $affiliateTable)
    {
        $this->service = $service;
        $this->affiliateTable = $affiliateTable;
    }


    public function __invoke(SignupEvent $event)
    {
        $user = $event->getTarget();
        $cookies = request()->getCookieParams();
        if (isset($cookies[AffiliateModule::COOKIE_NAME])){
            $userId = $cookies[AffiliateModule::COOKIE_NAME];
            try {
                $aff = $this->affiliateTable->findBy("token", $userId);
            } catch (NoRecordException $e){
                return;
            }
            $aff->addSignup($this->service->generateAmount('registration', 0));
            $id = $this->service->addUserLog($user->getId(), $aff->getUserId());
            $this->service->addRegistrationLog($user->getId(), $aff->getId(), $id);
            $this->affiliateTable->saveAff($aff);

        }

    }
}