<?php

namespace Tygh\Addons\MultiStoreSeo;

use Tygh\Registry;

/**
 * Class LanguageCache
 *
 * @package Tygh\Addons\MultiStoreSeo
 */
class Cache
{
    /**
     * LanguageCache constructor.
     */
    public function __construct()
    {
        Registry::registerCache(
            'multistore_seo',
            [
                'companies',
                'languages',
                'storefronts',
                'storefronts_languages',
                'ult_objects_sharing'
            ],
            Registry::cacheLevel('static'),
            true
        );
    }

    /**
     * @param string $key
     *
     * @return array|bool|mixed|string|null
     */
    public function get(string $key) {
        return Registry::get('multistore_seo.' . $key);
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return bool
     */
    public function set(string $key, $value) {
        return Registry::set('multistore_seo.' . $key, $value);
    }

    /**
     * @return array|bool|mixed|string|null
     */
    public function getLanguages()
    {
        if ($languages = Registry::get('multistore_seo.languages'))
            return $languages;

        $languages = db_get_array(
            "SELECT * FROM ?:ult_objects_sharing " .
            "LEFT JOIN ?:languages ON share_object_id = lang_id " .
            "WHERE share_object_type = 'languages'"
        );

        Registry::set('multistore_seo.languages', $languages);

        return $languages;
    }
}
