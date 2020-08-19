<?php

namespace Tygh\Addons\MultiStoreSeo;

use Tygh\Core\ApplicationInterface;
use Tygh\Core\BootstrapInterface;
use Tygh\Core\HookHandlerProviderInterface;

/**
 * Class Bootstrap
 *
 * @package Tygh\Addons\MultiStoreSeo
 */
class Bootstrap implements BootstrapInterface, HookHandlerProviderInterface
{
    const ADDON_NAME = 'multistore_seo';

    /** @var ApplicationInterface */
    protected $app;

    public function boot(ApplicationInterface $app)
    {
        $this->app = &$app;
        $this->app->register(new ServiceProvider());
    }

    /**
     * @inheritDoc
     */
    public function getHookHandlerMap()
    {
        return [
            'dispatch_before_display' => [
                'addons.multistore_seo.alternative_url_finder',
                'onDispatchBeforeDisplay',
            ],
        ];
    }
}