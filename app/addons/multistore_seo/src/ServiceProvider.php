<?php

namespace Tygh\Addons\MultiStoreSeo;

/**
 * Class ServiceProvider
 *
 * @package Tygh\Addons\MultiStoreSeo
 */
class ServiceProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(\Pimple\Container $pimple)
    {
        $pimple['addons.multistore_seo.alternative_url_finder'] = function () {
            return new AlternativeUrlFinder();
        };

        $pimple['addons.multistore_seo.cache'] = function () {
            return new Cache();
        };
    }
}