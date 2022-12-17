<?php

namespace App\Affiliate\Actions;

use App\Affiliate\AffiliateService;
use App\Affiliate\Database\AffiliateTable;
use App\Affiliate\Entity\Affiliate;
use App\Auth\DatabaseUserAuth;
use ClientX\Actions\Action;
use ClientX\Helpers\Currency;
use ClientX\Renderer\RendererInterface;
use ClientX\Translator\Translater;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateIndexAction extends Action
{
    private AffiliateTable $usersTable;
    private AffiliateService $service;

    public function __construct(AffiliateService $service, Translater $translater, RendererInterface $renderer, AffiliateTable $usersTable, DatabaseUserAuth $auth)
    {
        $this->renderer = $renderer;
        $this->usersTable = $usersTable;
        $this->auth = $auth;
        $this->translater = $translater;
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($this->usersTable->isAffiliated($this->getUserId())) {
            $aff = $this->usersTable->findBy("user_id", $this->getUserId());
            return $this->render('@affiliate/index', [
                'aff' => $aff,
                'items' => $this->getItems($aff),
                'withdrawals' => $this->service->findWithdrawalsForUser($this->getUserId()),
                'logs' => $this->service->findLogsForUser($aff->getId())
            ]);
        } else {
            return $this->render('@affiliate/index', ['aff' => null, 'items' => $this->getItems()]);
        }
    }

    private function getItems(?Affiliate $affiliate = null)
    {
        $col = $affiliate ? 3 : 6;
        $default = [
            [
            'value' => $this->trans('affiliate.client.onorder'),
            'name' => $this->service->textAmount('order'),
            'icon' => 'fas fa-cart-shopping',
            'color' => 'primary',
            'col' => $col,
        ],
            [
            'value' => $this->trans('affiliate.client.onregistration'),
            'name' => $this->service->textAmount('registration'),
            'icon' => 'fas fa-users',
            'color' => 'secondary',
            'col' => $col,
        ]
        ];

        if ($affiliate) {
            $default = array_merge($default, [
                [
                    'value' => $this->trans('affiliate.client.visitors'),
                    'name' => $affiliate->getVisitors(),
                    'icon' => 'fas fa-link',
                    'col' => $col,
                    'color' => 'warning'
                ],
                [
                'value' => $this->trans('affiliate.client.signups'),
                'name' => $affiliate->getSignups(),
                'icon' => 'fas fa-users',
                'col' => $col,
                'color' => 'info'
                ]
                ]);
        }
        return $default;
    }
}
