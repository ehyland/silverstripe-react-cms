<?php
// TODO: Cache results
class PageDataUtil extends Object {

    public function getByLink($url) {

        if (!($page = Page::get_by_link($url))) {
            $page = ErrorPage::get()->filter('ErrorCode', 404)->first();
        }

        $data = array(
            'timepstamp' => time(),
            'page' => $page->forAPI()
        );

        return $data;
    }
}
