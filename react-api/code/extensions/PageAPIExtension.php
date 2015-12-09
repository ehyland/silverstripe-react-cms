<?php
class PageAPIExtension extends Extension {

    // Get url for page
    public function getURLForAPI () {
        $url = $this->owner->RelativeLink();
        if (!preg_match('/^\//', $url)) {
             $url = '/' . $url;
        }
        return $url;
    }

    // Get summary fields for sitetree
    public function getSummaryForAPI(){
        $page = $this->owner;
        $data = array(
            'ID' => $page->ID,
            'ParentID' => $page->ParentID,
            'URL' => $page->getURLForAPI(),
            'Title' => $page->Title,
            'MenuTitle' => $page->MenuTitle,
            'ShowInMenus' => $page->ShowInMenus,
            'ShowInSearch' => $page->ShowInSearch,
            'Sort' => $page->Sort,
            'HasLoaded' => false
        );

        return $data;
    }
}
