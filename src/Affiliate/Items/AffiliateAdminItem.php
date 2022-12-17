<?php

namespace App\Affiliate\Items;

use ClientX\Renderer\RendererInterface;

class AffiliateAdminItem implements \ClientX\Navigation\NavigationItemInterface
{

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function render(RendererInterface $renderer): string
    {
        return $renderer->render('@affiliate_admin/admin');
    }
}