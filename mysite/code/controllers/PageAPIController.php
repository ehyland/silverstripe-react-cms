<?php

class PageAPIController extends Controller{

    private static $allowed_actions = array(
        'pageAction'
    );
    private static $url_handlers = array(
        '' => 'pageAction'
    );

    public function pageAction(SS_HTTPRequest $request) {
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
