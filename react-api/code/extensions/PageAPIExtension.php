<?php
class PageAPIExtension extends DataExtension {

    private static $db = array();
    private static $has_one = array(
        'ReactComponent' => 'ReactComponent'
    );

    public function updateCMSFields(FieldList $fields){
        $fields->addFieldsToTab('Root.Main', array(
            DropdownField::create(
                'ReactComponentID',
                'React Component',
                ReactComponent::get()->map('ID', 'ComponentName')
            )->setHasEmptyDefault(true)
            ->setEmptyString('Inherit from class name')
        ), 'Content');

    }

    public static function get_api_data_by_url($url) {
        $page = Page::get_by_link($url);

        if (is_object($page)) {
            $data = $page->getForAPI();
        }else{
            $data = array(
                'ReactComponent' => 'ErrorPage',
                'ErrorCode' => '404',
                'ErrorMessage' => 'Page not found'
            );
        }

        $data['HasLoaded'] = true;

        return $data;
    }

    public function getReactComponent() {
        $page = $this->owner;
        $component = $page->ReactComponent();
        if (is_object($component) && $component->exists()) {
            error_log('Its an object');
            return $component->ComponentName;
        }else{
            return $page->ClassName;
        }
    }

    public function getSummaryForAPI(){
        $page = $this->owner;

        if (method_exists($page, 'getCustomSummaryForAPI')) {
            $data = $page->getCustomSummaryForAPI();
        }else{
            $data = array(
                'ID' => $page->ID,
                'ParentID' => $page->ParentID,
                'URL' => $page->RelativeLink(),
                'Title' => $page->Title,
                'MenuTitle' => $page->MenuTitle,
                'ShowInMenus' => $page->ShowInMenus,
                'ShowInSearch' => $page->ShowInSearch,
                'Sort' => $page->Sort,
                'ReactComponent' => $page->ReactComponent
            );
        }

        if (!preg_match('/^\//', $data['URL'])) {
             $data['URL'] = '/' . $data['URL'];
        }

        $data['HasLoaded'] = false;

        return $data;
    }

    public function getForAPI(){
        $page = $this->owner;
        // $data = $page->getSummaryForAPI();

        if (method_exists($page, 'getCustomForAPI')) {
            $data = $page->getCustomForAPI();
        }else{
            $data = array(
                'Content' => $page->Content
            );
        }

        return array_merge(
            $page->getSummaryForAPI(),
            $data
        );
    }
}
