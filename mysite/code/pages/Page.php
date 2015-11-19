<?php
class Page extends SiteTree {
  private static $db = array();
  private static $has_one = array();
}

class Page_Controller extends ContentController {
  private static $allowed_actions = array ();

  public function init() {
    parent::init();
  }
}
