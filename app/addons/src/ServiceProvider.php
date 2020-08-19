<?php

namespace Tygh\Addons\MultiStoreSeo;

class ServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $pimple)
    {
        $pimple['addons.multistore_seo.alternative_url_finder'] = function ($container) {
            return new AlternativeUrlFinder();
        };
    }
}