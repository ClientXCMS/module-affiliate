<?php

namespace App\Affiliate\Actions;

use App\Affiliate\Database\AffiliateTable;
use App\Auth\DatabaseUserAuth;
use ClientX\Actions\Action;
use ClientX\Helpers\Str;
use ClientX\Session\FlashService;
use ClientX\Translator\Translater;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateBecomeAction extends Action
{

    private AffiliateTable $affiliateTable;

    public function __construct(Translater $translater, FlashService $flash, AffiliateTable $affiliateTable, DatabaseUserAuth $auth)
    {
        $this->translater = $translater;
        $this->flash = $flash;
        $this->affiliateTable = $affiliateTable;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($this->affiliateTable->isAffiliated($this->getUserId())) {
            return new Response(404);
        }
        $this->affiliateTable->insert([
            'user_id' => $this->getUserId(),
            'token' => Str::randomStr(16)
        ]);
        $this->success($this->trans("affiliate.client.success"));
        return $this->back($request);
    }
}