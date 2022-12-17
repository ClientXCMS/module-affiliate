<?php

namespace App\Affiliate\Actions\Admin;

use App\Affiliate\Database\AffiliateLogTable;
use App\Affiliate\Database\AffiliateTable;
use ClientX\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateAdminUsersAction extends \ClientX\Actions\Action
{
    private AffiliateTable $affiliateTable;

    public function __construct(AffiliateTable $affiliateTable, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->affiliateTable = $affiliateTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $order = $params['order'] ?? 'desc';
        $items = $this->affiliateTable->makeQueryForAdmin($params, $order)->paginate(12, $params['p'] ?? 1);
        $query = $params['s'] ?? null;
        return $this->render('@affiliate_admin/users', compact('items', 'query', 'order'));
    }

}