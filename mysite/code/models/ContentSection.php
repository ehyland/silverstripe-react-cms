<?php
class ContentSection extends DataObject {

    private static $db = array(
        'Sort' => 'Int',
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText'
    );
    private static $has_one = array(
        'Page' => 'Page'
    );
    private static $has_many = array();
    private static $belongs_to = array();
    private static $belongs_many_many = array();

    private static $defaults = array();

    protected function validate() {
        return parent::validate();
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(array(
            'Sort',
            'PageID'
        ));
        return $fields;
    }

    public function forAPI(){
        return $this->getFieldsForAPI(array_keys($this->db()));
    }
}
