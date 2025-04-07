<?php
/*
 * @Description: 项目通用函数 一般用于初始化情况
 * @Author: chris@hardsun
 * @Date: 2022-07-29 10:48:11
 * @LastEditTime: 2025-03-26 09:30:48
 * @LastEditors: chris@hardsun.cn
 * @File: /includes/func/project-common.php
 * @Copyright: Copyright© 2019-2022 HARDSUN Ltd
 */

$hsInitMark = hs_set_init_cookie();

$browserLang = hs_browser_lang();
$hsPageUrl = hs_page_url();
// var_export($browserLang);

// 获取浏览器语言
function hs_browser_lang($lang = null)
{

  // 是否始终默认使用主语言
  // if (
  //   (defined('SITE_MAIN_LANG_ALWAYS_ON') && true === SITE_MAIN_LANG_ALWAYS_ON)
  //   // && (empty($_GET['lang']))
  // ) {
  //   return SITE_MAIN_LANG;
  // }

  $lang = substr(@$_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
  //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。

  // var_export($lang);
  if (preg_match("/zh-c/i", $lang))
    // echo "简体中文";
    return 'cn';
  else if (preg_match("/zh_C/i", $lang))
    // echo "中文";
    return 'cn';
  else if (preg_match("/zh/i", $lang))
    // echo "繁體中文";
    return 'tw';
  else if (preg_match("/en/i", $lang))
    // echo "English";
    return 'en';
  else if (preg_match("/fr/i", $lang))
    //echo "French";
    return 'fr';
  else if (preg_match("/de/i", $lang))
    //echo "German";
    return 'ge';
  else if (preg_match("/jp/i", $lang))
    //echo "Japanese";
    return 'jp';
  else if (preg_match("/ko/i", $lang))
    //echo "Korean";
    return 'ko';
  else if (preg_match("/es/i", $lang))
    //echo "Spanish";
    return 'sp';
  else if (preg_match("/sv/i", $lang))
    //echo "Swedish";
    return 'sw';
}

function hs_get_lang_from_domain($url = '')
{
  // $urlComponents = hs_parse_url($domain, -1);

  // hs_ve($urlComponents);
  $langCode = '';

  $url = empty($url) ? PAGE_URL : $url;

  // 获取当前网页的域名的子域名名称
  $host = parse_url($url, PHP_URL_HOST);
  $hostParts = explode('.', $host);
  // 判断是否是子域名
  if (count($hostParts) > 2) {
    $langCode = $hostParts[0];
  }
  // $langCode = ('www' == $langCode) ? '' : $langCode;

  if (
    !empty($langCode)
    && !in_array($langCode, hs_get_all_langs())
  ) {
    $langCode = '';
  }
  return empty($langCode) ? SITE_MAIN_LANG : $langCode;
}

// 是否是子域名
function hs_is_subdomain($domain = null)
{
  $domain = !empty($domain) ? $domain : $_SERVER['HTTP_HOST'];
  $pieces = explode('.', $domain);
  if (
    count($pieces) > 2
    && strpos($domain, "www.") === false // Does not contain www
  ) {
    // The domain is a subdomain
    // echo 'The domain is a subdomain';
    return true;
  } else {
    // The domain is not a subdomain
    // echo 'The domain is not a subdomain';
  }

  return false;
}


function hs_get_all_langs()
{
  $langs = [SITE_MAIN_LANG];

  if (!empty(SITE_LANG_EXTRA)) {
    $langs = array_merge($langs, (array) SITE_LANG_EXTRA);
  }

  return $langs;
}


// aws 相关设置
function hs_aws_config(string $type = null, string $lang = 'cn'): array
{

  $data = AWS_CONFIG;
  if (defined('AWS_ENV_TYPE') && !empty(AWS_ENV_TYPE)) {
    $type = AWS_ENV_TYPE[$type];
  } else {

    switch ($type) {
      case 'cn':
      case 'local':
      case 'staging':
        $type = 'cnw1';

        break;

        // case 'local':
        // case 'staging':

        // $type = 'apse1';

        // break;

      case 'production':
      case 'en':
        $type = 'uw2';

        break;

      default:

        if (empty($type)) {
          $type = defined('ENVIRONMENT_TYPE') ? ENVIRONMENT_TYPE : 'uw2';
        }

        break;
    }
  }


  // if ('cn' == $type) {
  //   # code...
  // }

  if (!empty($data[$type])) {
    return $data[$type];
  }

  return $data;
}


// 项目基础设置
function hs_project_config(string $env = null, string $lang = 'cn'): array
{


  $data = PROJECT_CONFIG;


  if (empty($env)) {
    $env = defined('ENVIRONMENT_TYPE') ? ENVIRONMENT_TYPE : 'production';
  }


  if (!empty($data[$env])) {
    return $data[$env];
  }

  return (array)$data;
}


// 获取项目设置
function hs_get_project_config(string $env = null, string $regionLang = null): array
{

  // 国内环境参数 - 用于提速等
  $projectConfigCn = hs_project_config('cn');

  // 如果站点主语言是中文
  if (
    (
      (defined('SITE_MAIN_LANG') && 'cn' == SITE_MAIN_LANG) // 如果站点主语言是中文
      || defined('ENVIRONMENT_TYPE') && 'staging' == ENVIRONMENT_TYPE // 或者是测试站点
    )
    && (defined('CN_SPEED_UP') && true === CN_SPEED_UP) // 如果提速为 true
  ) {
    $projectConfig = $projectConfigCn;
  } else {

    if (empty($env)) {
      $env = defined('ENVIRONMENT_TYPE') ? ENVIRONMENT_TYPE : 'production';
    }

    if (empty($regionLang)) {
      global $browserLang;
      $regionLang = $browserLang;
    }


    // 非中文主语言站点
    $projectConfig = hs_project_config($env);
    // var_export($regionLang);
    // var_export(CN_SPEED_UP);

    if (
      (defined('CN_SPEED_UP') && true === CN_SPEED_UP) // 如果提速
      && (!empty($regionLang) && 'cn' == $regionLang) //浏览器语言为中文
    ) {
      $projectConfig = $projectConfigCn;
    }
    // var_export($projectConfig);
  }


  // 内部IP 必须为当前环境的IP
  $projectConfig['serverInnerIp'] = hs_project_config($env)['serverInnerIp'];


  // var_export($projectConfigCn);
  // var_export($projectConfig);

  return $projectConfig;
}

// 页面链接
function hs_page_url(): string
{

  // 定义 http 类型 ============
  $http = 'http';
  if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
  ) {
    $http = 'https';
  }

  return $http . '://' . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// (判断是或否是后台页面)
function is_hs_admin_project()
{
  global $hsPageUrl;
  // var_export($hsPageUrl);

  if (
    str_contains($hsPageUrl, "/wp-admin")
  ) {
    return true;
  }
  return false;
}

// (判断是或否是后台页面的编辑页面)
function is_hs_admin_page_edit()
{

  // var_export($_GET);
  global $hsPageUrl;
  if (
    is_hs_admin_project()
    &&
    (
      (
        str_contains($hsPageUrl, "/post.php")
        && (isset($_GET['action']) && 'edit' == $_GET['action'])
        && (isset($_GET['post']) && !empty($_GET['post']))
      )
      || (
        str_contains($hsPageUrl, "/post-new.php")
        && (isset($_GET['post_type']) && 'page' == $_GET['post_type'])
      )
    )
  ) {

    return true;
  }
  return false;
}


// 是否当前是编辑器模式
if (!function_exists('is_divi_editor_on')) {
  function is_divi_editor_on()
  {

    if (!empty($_GET['et_fb']) && 1 == $_GET['et_fb']) {
      return true;
    }

    return false;
  }
}


function hs_set_init_cookie()
{

  // wp-cron.php 不设置 cookie
  // $isWpCronRun = str_contains(PAGE_URL, 'wp-cron.php');
  // if ($isWpCronRun) {
  //   return false;
  // }

  // wc-ajax=update_order_review 不设置 cookie
  // $isWcAjax = str_contains(PAGE_URL, '?wc-ajax=');
  // if ($isWcAjax) {
  //   return false;
  // }

  // hs_log(PAGE_URL, 'hs_set_beta_tester_cookie.PAGE_URL', 'user-common.php');

  if (
    (
      !isset($_COOKIE['hs_init_mark'])
    || !empty($_GET['init_mark'])
    )
    && !empty($loadWp) // 是否载入 wp
  ) {
    setcookie('hs_init_mark', time(), time() + 3600 * 24 * 365, '/');
  }

  // $hsInitMark = $_COOKIE['hs_init_mark'];
  return isset($_COOKIE['hs_init_mark']) ? $_COOKIE['hs_init_mark'] : '';
}
