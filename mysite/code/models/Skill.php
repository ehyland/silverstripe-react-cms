<?php
class Skill extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(255)'
    );

    private static $has_one = array();
    private static $has_many = array();
    private static $belongs_to = array();
    private static $belongs_many_many = array();

    private static $defaults = array();

    protected function validate() {
        return parent::validate();
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }
}
