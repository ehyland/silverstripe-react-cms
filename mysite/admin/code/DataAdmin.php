<?php

class DataAdmin extends ModelAdmin{
  private static $managed_models = array(
    'Skill'
  );
  private static $url_segment = 'data';
  private static $menu_title = 'Data';
}
