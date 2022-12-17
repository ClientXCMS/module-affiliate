<?php

use App\Affiliate\AffiliateSettings;
use App\Affiliate\Items\AffiliateAdminItem;
use App\Affiliate\Items\AffiliateCustomerItem;
use App\Affiliate\Items\AffiliateWidget;
use ClientX\Navigation\DefaultMainItem;
use function ClientX\setting;
use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'admin.settings' => add(get(AffiliateSettings::class)),
    "admin.menu.items" => add(get(AffiliateAdminItem::class)),
    'admin.dashboard.items' => add(get(AffiliateWidget::class)),
    'admin.customer.items' => add(get(AffiliateCustomerItem::class)),

    'navigation.main.items' => add(new DefaultMainItem(array(DefaultMainItem::makeItem("affiliate.index", "client.affiliate.index", "fas fa-user-tag")), 90)),
    App\Affiliate\AffiliateService::class => autowire()
        ->constructorParameter('onorder', setting('affiliate_onorder', '0'))
        ->constructorParameter('minwithdraw', setting('affiliate_minwithdraw', '50'))
        ->constructorParameter('onregistration', setting('affiliate_onregistration', '0'))
        ->constructorParameter('onordertype', setting('affiliate_onordertype','fixed'))
        ->constructorParameter('currency', \DI\get('app.currency'))
];