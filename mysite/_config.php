<?php

global $project;
$project = 'mysite';

global $database;
$database = 'eamon';

require_once('conf/ConfigureFromEnv.php');

// Log notices
if(defined('MY_SS_ERROR_LOG')) {
	SS_Log::add_writer(new SS_LogFileWriter(MY_SS_ERROR_LOG), SS_Log::NOTICE, '<=');
}

// Set the site locale
i18n::set_locale('en_GB');

CMSMenu::remove_menu_item('ReportAdmin');

// Set cache life
$devCacheLife = -1;
$liveCacheLife = 60*5; // 5 min

$cacheLife = Director::isDev() ? $devCacheLife : $liveCacheLife;

SS_Cache::set_cache_lifetime(Cached_PageDataUtil::CACHE_NAME, $cacheLife, 100);
