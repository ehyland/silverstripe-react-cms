<?php
// TODO: Cache results
class PageDataUtil extends Object {

    public function getByLink($url) {

        if (!($page = Page::get_by_link($url))) {
            $page = ErrorPage::get()->filter('ErrorCode', 404)->first();
        }

        $data = $page->forAPI();

        return $data;
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
