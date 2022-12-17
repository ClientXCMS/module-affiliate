<?php

namespace App\Affiliate\Actions\Admin;

use App\Affiliate\Database\AffiliateLogTable;
use ClientX\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class AffiliateAdminLogsAction extends \ClientX\Actions\Action
{

    private AffiliateLogTable $affiliateLogTable;

    public function __construct(AffiliateLogTable $affiliateLogTable, RendererInterface $renderer)
    {
        $this->affiliateLogTable = $affiliateLogTable;
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {

        $params = $request->getQueryParams();
        $order = $params['order'] ?? 'desc';
        $items = $this->affiliateLogTable->makeQueryForAdmin($params, $order)->paginate(12, $params['p'] ?? 1);
        $query = $params['s'] ?? null;
        return $this->render('@affiliate_admin/logs', compact('items', 'query', 'order'));
    }

}