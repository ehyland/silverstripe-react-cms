<?php
class HomePage extends Page {

    private static $db = array(
        'Test' => 'Varchar'
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

class HomePage_Controller extends Page_Controller {
  private static $allowed_actions = array();

  public function init() {
    parent::init();
  }

}
