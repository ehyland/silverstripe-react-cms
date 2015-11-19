<?php

class ReactComponentsAdmin extends ModelAdmin{
  private static $managed_models = array(
    'ReactComponent'
  );
  private static $url_segment = 'react-components-admin';
  private static $menu_title = 'React Components Admin';
}
