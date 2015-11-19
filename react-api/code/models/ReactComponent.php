<?php
class ReactComponent extends DataObject {
    private static $db = array(
        'ComponentName' => 'Varchar'
    );

    private static $has_many = array(
        'Pages' => 'Page'
    );
}
