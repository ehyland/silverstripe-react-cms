<?php

class Cached_PageDataUtil extends PageDataUtil {

    const CACHE_NAME = 'PAGE_CACHE';

    private static $cache_key_transforms = array(
        '/' => '_SLASH_',
        '-' => '_DASH_',
        '?' => '_QUERY_',
        '.' => '_DOT_',
        '%' => '_PERCENT_',
        ':' => '_COLON_'
    );

    public function getByLink($url) {
        $cache = SS_Cache::factory(self::CACHE_NAME);
        $cacheKey = self::parse_cache_key($url);

        if ($result = $cache->load($cacheKey)) {
            $result = unserialize($result);
        }else{
            $result = parent::getByLink($url);
            $cache->save(serialize($result), $cacheKey);
        }

        return $result;
    }

    public static function parse_cache_key($key) {
        $key = str_replace(
            array_keys(self::$cache_key_transforms),
            array_values(self::$cache_key_transforms),
            $key
        );

        $key = preg_replace('/[^a-zA-Z0-9_]/', '_ESCAPED_', $key);

        return $key;
    }

}
