<?php

namespace App\Affiliate\Actions;

use App\Affiliate\AffiliateModule;
use App\Affiliate\Database\AffiliateTable;
use App\Auth\DatabaseUserAuth;
use ClientX\Actions\Action;
use ClientX\Cookies\Cookie;
use ClientX\Database\NoRecordException;
use ClientX\Helpers\Str;
use ClientX\Router;
use ClientX\Session\FlashService;
use ClientX\Translator\Translater;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateCodeAction extends Action
{

    private AffiliateTable $affiliateTable;
    private string $redirecturl;

    public function __construct(Translater $translater,Router $router, FlashService $flash, AffiliateTable $affiliateTable, DatabaseUserAuth $auth, string $redirecturl)
    {
        $this->translater = $translater;
        $this->flash = $flash;
        $this->affiliateTable = $affiliateTable;
        $this->auth = $auth;
        $this->router = $router;
        $this->redirecturl = $redirecturl;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $token = $request->getAttribute("token");
        try {
            $aff = $this->affiliateTable->findBy("token", $token);
            setcookie(AffiliateModule::COOKIE_NAME, $token, time() + (3600 * 24 * 90), '/');

            $aff->addVisitor();
            $this->affiliateTable->saveAff($aff);
            return $this->redirect($this->redirecturl);

        } catch (NoRecordException $e){
            return $this->redirect($this->redirecturl);
        }
        $this->success($this->trans("affiliate.client.success"));
        return $this->back($request);
    }
}