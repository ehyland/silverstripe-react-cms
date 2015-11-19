<?php

// 5 minute cache
$liveCacheLife = 60*5;
$devCacheLife = -1;
$cacheLife = Director::isDev() ? $devCacheLife : $liveCacheLife;

SS_Cache::set_cache_lifetime(ReactAPIController::PAGE_CACHE_NAME, $cacheLife, 100);
SS_Cache::set_cache_lifetime(ReactAPIController::SITE_DATA_CACHE_NAME, $cacheLife, 100);
