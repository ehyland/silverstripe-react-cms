<?php

class DataAdmin extends ModelAdmin{
  private static $managed_models = array(
    'TechnologyExperience'
  );
  private static $url_segment = 'data';
  private static $menu_title = 'My Data Admin';
}
