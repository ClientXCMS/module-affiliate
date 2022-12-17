<?php

namespace App\Affiliate\Items;


use App\Affiliate\Database\AffiliateWithdrawalTable;
use ClientX\Navigation\NavigationItemInterface;
use ClientX\Renderer\RendererInterface;

class AffiliateWidget implements NavigationItemInterface
{

    private AffiliateWithdrawalTable $withdrawalTable;

    public function __construct(AffiliateWithdrawalTable $withdrawalTable)
    {
        $this->withdrawalTable = $withdrawalTable;
    }

    public function render(RendererInterface $renderer): string
    {
        return $renderer->render('@affiliate_admin/dashboard', ['count' =>  $this->withdrawalTable->countPending()]);
    }

    public function getPosition(): int
    {
        return 1;
    }
}