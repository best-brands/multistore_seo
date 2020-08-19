<?php

namespace Tygh\Addons\MultiStoreSeo;

use Tygh\Tools\Url;
use Tygh\Tygh;
use Tygh\Registry;

/**
 * Class AlternativeUrlFinder
 *
 * @package Tygh\Addons\MultiStoreSeo
 */
class AlternativeUrlFinder
{
    /** @var array all currently active languages for each storefront */
    private static $languages = [];

    /** @var array|string where each storefront is located */
    private static $zones = [];

    /** @var array|string which dispatch matches which shared object */
    private static $objects = [];

    /**
     * AlternativeUrlFinder constructor.
     */
    public function __construct() {
        $this->getLanguages();
        self::$objects = fn_get_schema('mseo', 'objects');
        self::$zones = fn_get_schema('mseo', 'zones');
    }

    /**
     * @hook dispatch_before_display
     */
    public function onDispatchBeforeDisplay() {
        $cache = [];
        $alternates = Tygh::$app['view']->getTemplateVars('seo_alt_hreflangs_list') ?? [];
        $current_company_id = Registry::get('runtime.company_id');

        // We only want to add alternate tags for urls that have not yet been added by the SEO addon
        $languages = array_filter(self::$languages, function ($item) use ($current_company_id) {
            return $item['share_company_id'] !== $current_company_id;
        });

        // Parse the current URL
        $url = new Url(Registry::get('config.current_url'));
        $query_params = $url->getQueryParams();
        $dispatch = $url->getQueryParams()['dispatch'] ?? 'index.index';

        // Check if we have behaviour defined for our dispatch
        if (!($object = (self::$objects[$dispatch] ?? false)))
            return ;

        // Now we loop over the remaining languages, and just add everything
        foreach ($languages as $language) {
            $query_params['sl'] = $language['lang_code'];
            $query_params['company_id'] = $language['share_company_id'];

            // If we have a persistent object, it means it always has an alternative URL
            if ($object['persistent'] ?? false) {
                $is_shared = true;
            } else {
                if (isset($cache[$language['share_company_id']]))
                    $is_shared = $cache[$language['share_company_id']];
                else
                    $is_shared = $cache[$language['share_company_id']] = $object['is_shared'](
                        $object['type'],
                        $_REQUEST[$object['identifier']],
                        $language['share_company_id']
                    );
            }

            // If it is shared, then we generate an alternative language tag
            if ($is_shared)
                $alternates[$this->getLanguageIsoCode($language, $language['share_company_id'])] = [
                    'name' => $language['name'],
                    'direction' => 'ltr',
                    'href' => fn_url($url->setQueryParams($query_params)->build())
                ];
        }

        Tygh::$app['view']->assign('seo_alt_hreflangs_list', $alternates);
    }

    /**
     * Get the iso code for a language in a region
     *
     * @param $language
     * @param $company_id
     *
     * @return mixed|string
     */
    private function getLanguageIsoCode($language, $company_id) {
        if (self::$zones[$company_id] ?? false) {
            return sprintf("%s-%s", $language['lang_code'], self::$zones[$company_id]);
        } else {
            return $language['lang_code'];
        }
    }

    /**
     * Get the languages
     */
    private function getLanguages() {
        self::$languages = Registry::get('cache.addons.multistore_seo.languages');

        if (!self::$languages) {
            self::$languages = db_get_array(
                "SELECT * FROM ?:ult_objects_sharing " .
                "LEFT JOIN ?:languages ON share_object_id = lang_id " .
                "WHERE share_object_type = 'languages'"
            );
            Registry::set('cache.addons.multistore_seo.languages', self::$languages);
        }
    }
}