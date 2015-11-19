<?php
class ReactAPIController extends Controller{

    const PAGE_CACHE_NAME = 'API_PAGE_CAHCE';
    const SITE_DATA_CACHE_NAME = 'API_SITE_DATA_CAHCE';

    private static $cache_key_transforms = array(
        '/' => '_SLASH_',
        '-' => '_DASH_',
        '?' => '_QUERY_',
        '.' => '_DOT_',
        '%' => '_PERCENT_',
        ':' => '_COLON_'
    );

    private static $allowed_actions = array(
        'page',
        'pageWithSiteData'
    );

    private static $url_handlers = array(
        '' => 'page',
        'data' => 'pageWithSiteData'
    );

    public function sendResponse($data, $code = 200, $description = 'OK', $contentType = 'application/json'){
        return $this->response
            ->addHeader('Content-Type:', 'application/json')
            ->setStatusCode($code, $description)
            ->setBody(json_encode($data));
    }

    /**
     * Actions
     */
    public function page(SS_HTTPRequest $request) {
        $url = $this->getRequestedPageURL($request);
        return $this->sendResponse($this->getPageForURL($url));
    }

    public function pageWithSiteData(SS_HTTPRequest $request) {
        $url = $this->getRequestedPageURL($request);
        $pageData = $this->getPageForURL($url);
        $siteData = $this->getSiteData();
        return $this->sendResponse(array_merge($pageData, $siteData));
    }

    /**
     * Getters
     */

    public function getRequestedPageURL(SS_HTTPRequest $request) {
        $url = $request->getVar('pageURL');
        if (!is_string($url)) {
            $url = '/';
        }
        return $url;
    }

    public function getPageForURL($url) {
        $cache = SS_Cache::factory(self::PAGE_CACHE_NAME);
        $cachekey = self::parse_cache_key($url);

        // Check cache
        if (!($result = $cache->load($cachekey))) {

            // Generate result and save to cache
            $result = array(
                'timestamp' => time(),
                'page' => PageAPIExtension::get_api_data_by_url($url)
            );

            $cache->save(serialize($result), $cachekey);
        }else{
            $result = unserialize($result);
        }
        return $result;
    }

    public function getSiteData() {
        $cache = SS_Cache::factory(self::SITE_DATA_CACHE_NAME);
        $cachekey = 'SITE_DATA_CACHE_KEY';

        // Check cache
        if (!($result = $cache->load($cachekey))) {

            // Generate result and save to cache
            $result = self::generate_site_data();
            $cache->save(serialize($result), $cachekey);
        }else{
            $result = unserialize($result);
        }
        return $result;
    }

    static function generate_site_data() {
        $siteConfig = SiteConfig::current_site_config();
        $data = array(
            'timestamp' => time(),
            'siteData' => array(
                'siteConfig' => array(
                    'LastEdited' => $siteConfig->LastEdited,
                    'Title' => $siteConfig->Title,
                    'Tagline' => $siteConfig->Tagline,
                    '404_Message' => 'Durp',
                    '500_Message' => 'Oops'
                ),
                'pages' => array()
            )
        );

        foreach (Page::get()->exclude('ShowInMenus', false) as $page) {
            $data['siteData']['pages'][] = $page->getSummaryForAPI();
        }

        return $data;
    }


    /**
     * Utils
     */
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
