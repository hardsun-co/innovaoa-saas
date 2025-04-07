<?php
/*
 * @Description: 引入系统文件等公共设置
 * @Author: chris@hardsun
 * @Date: 2021-04-15 05:50:55
 * @LastEditTime: 2025-04-03 08:49:23
 * @LastEditors: pjw@hardsun
 * @File: /common/common.php
 * @Copyright: Copyright© 2019-2022 HARDSUN & CERADIR
 */
// ====== 重要！！！ ====== //
// 这个文件里面通过 $appName 或 $adminRootPath 以及 $loadWp 定义了 wp-load.php 的路径或不载入 wp 系统时所需的 core (includes)公共设置和参数

/** Absolute path to the root directory. */
// 定义站点/项目 根目录
$absPath = dirname(__DIR__, 1);
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', $absPath . '/');
}
// echo ROOT_PATH;

$mainSiteName = isset($mainSiteName) ? $mainSiteName : 'hardsun';
// 定义 APP 根目录
if (empty($adminRootPath)) {
	$appName = (!empty($appName)) ? $appName : (!empty($_GET['app']) ? $_GET['app'] : $mainSiteName);
	$adminRootPath = (!empty($appName)) ? 'apps/' . $appName . '/' : '';
}
if (!defined('ABSPATH')) {
	define('ABSPATH', ROOT_PATH . $adminRootPath);
}

$loadWp = isset($loadWp) ? $loadWp : false;
$includeCommon = isset($includeCommon) ? $includeCommon : true;

if (true === $loadWp) {
	// define( 'SHORTINIT', true );
	define('WP_USE_THEMES', false);
	$wpLoadFile = ABSPATH . 'wp-load.php';
	$wpLoadFile = (file_exists($wpLoadFile)) ? $wpLoadFile : ROOT_PATH . $adminRootPath . 'wp-load.php';
	require_once $wpLoadFile;
} else {

	if (isset($_GET['debug']) && 1 == $_GET['debug']) {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}
}


if (true == $includeCommon && false === $loadWp) {

	// tpl\footer-common.php
	// 底部公共文件的一些设置，防止引入此文件时，缺少功能函数而报错
	$showContact = false;
	$showTrackingFunc = false;
	$showOthersFunc = false;
	$simpleNonceTag = true;

	// 公共定义参数等
  
	if (file_exists(ABSPATH . 'app-config.php')) {
		require_once(ABSPATH . 'app-config.php');
	} else {
		require_once(ROOT_PATH . 'includes/config/defines-common.php');
	}
  
  if (!defined('ROOT_INC_PATH')) {
    define('ROOT_INC_PATH', ROOT_PATH . 'includes/');
  }
  if(!defined('DATE_DEFAULT_TIMEZONE')) {
    define('DATE_DEFAULT_TIMEZONE', 'Asia/Shanghai');
  }
	require_once ROOT_INC_PATH . 'src/autoload.php';
	require_once ROOT_INC_PATH . 'inc/hs-common-functions.php';
	// require_once ROOT_INC_PATH . 'func/file-handle.php';
}


date_default_timezone_set(DATE_DEFAULT_TIMEZONE);
