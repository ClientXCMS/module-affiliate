<?php

namespace App\Affiliate;

use App\Admin\Settings\SettingsInterface;
use ClientX\Renderer\RendererInterface;
use ClientX\Validator;

class AffiliateSettings implements SettingsInterface
{

    public function name(): string
    {
        return "affiliate";
    }

    public function title(): string
    {
        return "Affiliate";
    }

    public function icon(): string
    {
        return 'fas fa-user-tag';
    }

    public function render(RendererInterface $renderer)
    {
        $types = [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage'
        ];
        return $renderer->render("@affiliate_admin/settings", compact('types'));
    }

    public function validate(array $params): Validator
    {
        return (new Validator($params))
            ->positiveOrZero('affiliate_onorder', 'affiliate_onregistration')
            ->inArray('affiliate_onordertype', ['fixed', 'percentage']);
    }
}