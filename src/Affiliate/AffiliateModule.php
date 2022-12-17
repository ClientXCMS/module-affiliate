<?php

namespace App\Affiliate;

use App\Affiliate\Actions\Admin\AffiliateAdminLogsAction;
use App\Affiliate\Actions\Admin\AffiliateAdminMakeWithdrawalsAction;
use App\Affiliate\Actions\Admin\AffiliateAdminUsersAction;
use App\Affiliate\Actions\Admin\AffiliateAdminWithdrawalsAction;
use App\Affiliate\Actions\AffiliateBecomeAction;
use App\Affiliate\Actions\AffiliateCodeAction;
use App\Affiliate\Actions\AffiliateIndexAction;
use App\Affiliate\Actions\AffiliateWithdrawalAction;
use ClientX\Event\EventManager;
use ClientX\Module;
use ClientX\Renderer\RendererInterface;
use ClientX\Router;
use ClientX\Theme\ThemeInterface;
use Psr\Container\ContainerInterface;

class AffiliateModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const COOKIE_NAME = "_aff_userid";
    public function __construct(EventManager $eventManager,ContainerInterface $container, Router $router, RendererInterface $renderer, ThemeInterface $theme)
    {
        $prefix = $container->get('clientarea.prefix');
        $renderer->addPath('affiliate', $theme->getViewsPath() . '/Affiliate');
        $renderer->addPath('affiliate_admin', __DIR__. '/Views');
        $router->get($prefix . '/affiliate', AffiliateIndexAction::class, 'client.affiliate.index');
        $router->post($prefix . '/affiliate', AffiliateBecomeAction::class);
        $router->get('/aff/[*:token]', AffiliateCodeAction::class, 'affiliate.code');
        $router->post($prefix . '/affiliate/withdraw', AffiliateWithdrawalAction::class, 'client.affiliate.withdraw');
        $eventManager->attach('account.signup', $container->get(AffiliateSignup::class));
        $prefix = $container->get('admin.prefix');
        $router->get($prefix . '/affiliate', null, 'admin.affiliate');
        $router->get($prefix . '/affiliate/logs', AffiliateAdminLogsAction::class, 'admin.affiliate.logs');
        $router->get($prefix . '/affiliate/users', AffiliateAdminUsersAction::class, 'admin.affiliate.users');
        $router->get($prefix . '/affiliate/withdrawals', AffiliateAdminWithdrawalsAction::class, 'admin.affiliate.withdrawals');
        $router->post($prefix . '/affiliate/withdrawals/[i:id]', AffiliateAdminMakeWithdrawalsAction::class, 'admin.affiliate.makewithdrawals');
    }
}
