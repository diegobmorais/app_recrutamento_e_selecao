<?php

namespace Modules\Recruitment\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'Hrm';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Offer Letter Settings'),
            'name' => 'offer-letter-settings',
            'order' => 140,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'offer-letter-settings',
            'module' => $module,
            'permission' => 'hrm manage'
        ]);
        $menu->add([
            'title' => __('Recruitment Print Settings'),
            'name' => 'recruitment-print-settings',
            'order' => 145,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'recruitment-print-settings',
            'module' => $module,
            'permission' => 'hrm manage'
        ]);
    }
}
