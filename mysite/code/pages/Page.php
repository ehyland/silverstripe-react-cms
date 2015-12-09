<?php
class Page extends SiteTree {
    private static $db = array();
    private static $has_one = array();
    private static $has_many = array(
        'Sections' => 'ContentSection'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.ContentSections', array(
            GridField::create(
                'Sections',
                'Sections',
                $this->Sections(),
                GridFieldConfig_RecordEditor::create()
                    ->addComponents(
                        new GridFieldOrderableRows()
                    )
            )
        ));


        return $fields;
    }

    public function forApi () {
        $fields = array_merge(
            array(
                'ID',
                'ClassName',
                'LastEdited',
                'Title',
                'MenuTitle',
                'ShowInMenus',
                'ShowInSearch',
                'Sort',
                'ParentID',
                'Content',
                'Sections'
            ),
            array_keys(DataObject::custom_database_fields($this->ClassName))
        );

        return $this->getFieldsForAPI($fields);
    }
}

class Page_Controller extends ContentController {
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
    }
}
