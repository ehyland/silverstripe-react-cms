<?php
class PageAPIController extends Controller{

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
        'page'
    );

    private static $url_handlers = array(
        '' => 'page'
    );

    /**
     * Actions
     */
    public function page(SS_HTTPRequest $request) {
        $url = $request->getVar('search_url');
        $data = Cached_PageDataUtil::create()->getByLink($url);
        return $this->sendResponse($data);
    }

    public function sendResponse($data, $code = 200, $description = 'OK', $contentType = 'application/json'){
        return $this->response
            ->addHeader('Content-Type:', 'application/json')
            ->setStatusCode($code, $description)
            ->setBody(json_encode($data));
    }
}
