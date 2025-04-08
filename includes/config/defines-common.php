<?php
/*
 * @Description: 定义-公共部分
 * @Author: groupshop@com
 * @Date: 2021-07-03 21:26:14
 * @LastEditTime: 2025-04-07 17:12:35
 * @LastEditors: pjw@hardsun
 * @File: /includes/config/defines-common.php
 * @Copyright: Copyright© 2019-2021 groupshop Ltd
 */

//  debug
if ((isset($_GET['showe']) && $_GET['showe'] == 1)) {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
}

$wpDebug = (isset($_GET['debug']) && $_GET['debug'] == 1) ? true : false;
$wp_debug = $wpDebug;


// 兼容 PHP 8.0 之前的 str_contains 函数
if (
  !function_exists('str_contains')
  // if is php version < 8.0
  || version_compare(PHP_VERSION, '8.0.0', '<')
  ) {
  function str_contains($haystack, $needle)
  {
    return strpos($haystack, $needle) !== false;
  }
}

// 域名服务器等相关设置
include_once(ROOT_PATH . 'config.php');
include_once(ROOT_PATH . 'includes/func/project-common.php');

// 项目使用的配置环境
$projectConfigEnv = ENVIRONMENT_TYPE;
if (defined('PROJECT_CONFIG_ENV') && PROJECT_CONFIG_ENV) {
  $projectConfigEnv = PROJECT_CONFIG_ENV;
}

// 环境参数
$projectConfig = hs_get_project_config($projectConfigEnv);
$awsConfigEnv = hs_aws_config($projectConfigEnv);

// var_export(ENVIRONMENT_TYPE);
// var_export($projectConfig);


// 生产环境项目域名 - 这个是固定的，用于 CDN或其他需要生产环境域名的情况
// 不可更改！！
if (!defined('MAIN_DOMAIN_PROD')) {
  define('MAIN_DOMAIN_PROD', $projectConfig['mainDomainProd']);
}

// 主域名 - 用于不同环境的不同域名设置
// 可根据环境域名修改
if (!defined('MAIN_DOMAIN')) {
  $mainDomain = (defined('MAIN_DOMAIN_COMMON') && MAIN_DOMAIN_COMMON) ? MAIN_DOMAIN_COMMON : $projectConfig['mainDomain'];
  define('MAIN_DOMAIN', $mainDomain);
}

// CDN 主域名
if (!defined('CDN_DOMAIN')) {
  $cdnDomain = ((defined('CN_SPEED_UP') && true === CN_SPEED_UP)) ? CDN_DOMAIN_CN : $projectConfig['cdnDomain'];
  define('CDN_DOMAIN', $cdnDomain);
}
if (!defined('CDN_DOMAIN_PROD')) {
  define('CDN_DOMAIN_PROD', CDN_DOMAIN);
}

// 服务器内部IP 用于权限判断等
if (!defined('SERVER_IP_INNER')) {
  define('SERVER_IP_INNER', $projectConfig['serverInnerIp']);
}


// == AWS
if (!defined('AWS_DOMAIN')) {
  define('AWS_DOMAIN', '');
}
if (!defined('AWS_REGION')) {
  define('AWS_REGION', '');
}
if (!defined('AWS_REGION_SHORT')) {
  define('AWS_REGION_SHORT', '');
}
if (!defined('AWS_PROFILE')) {
  define('AWS_PROFILE', '');
}
if (!defined('AWS_REGION_ENV')) {
  define('AWS_REGION_ENV', '');
}
if (!defined('AWS_ACCOUNT_ID')) {
  define('AWS_ACCOUNT_ID', '');
}
if (!defined('AWS_ACCOUNT_ID_ENV')) {
  define('AWS_ACCOUNT_ID_ENV',  '');
}
if (!defined('AWS_REGION_ENV_SHORT')) {
  define('AWS_REGION_ENV_SHORT', '');
}

if (!defined('AWS_ACCOUNT_ID')) {
  define('AWS_ACCOUNT_ID', '');
}

if (!defined('AWS_REGION_TRANSLATE')) {
  define('AWS_REGION_TRANSLATE', 'us-west-2');
}
if (!defined('AWS_PROFILE_ENV')) {
  define('AWS_PROFILE_ENV','');
}

// - S3
if (!defined('S3_BUCKET_NAME')) {
  define('S3_BUCKET_NAME', '');
}
if (!defined('S3_BUCKET_NAME_ENV')) {
  define('S3_BUCKET_NAME_ENV', '');
}




if (!defined('HS_PLUGIN_TOKEN')) {
  define('HS_PLUGIN_TOKEN', 'hs');
  define('HS_TEXT_DOMAIN', 'hs');
}
if (!defined('PROJECT')) {
  define('PROJECT', 'hsapp');
}
if (!defined('P_CODE')) {
  define('P_CODE', HS_TEXT_DOMAIN);
}
if (!defined('P_PREX')) {
  define('P_PREX', P_CODE . '_');
}
if (!defined('SITE_NAME')) {
  define('SITE_NAME', 'HARDSUN SAAS SYSTEM');
}



// 主语言
if (!defined('SITE_MAIN_LANG')) {
  define('SITE_MAIN_LANG', 'en');
}

// 多语言站点
if (!defined('MULTILANG')) {
  define('MULTILANG', false);
}
if (defined('MULTILANG') && MULTILANG && !defined('MULTILANG_CREATE_TRANSLATE')) {
  define('MULTILANG_CREATE_TRANSLATE', true);
}
if (!defined('SITE_LANG_EXTRA')) {
  define('SITE_LANG_EXTRA', []);
}
if (!defined('AUTO_TRANSLATE')) {
  define('AUTO_TRANSLATE', true);
}


// 后台语言
if (!defined('ADMIN_LANG')) {
  define('ADMIN_LANG', 'cn');
}

// 浏览器语言
global $browserLang;
if (!defined('BROWSER_LANG')) {
  define('BROWSER_LANG', $browserLang);
}

if (!defined('TERM_SEO_TO_POSTS_ON')) {
  define('TERM_SEO_TO_POSTS_ON', false);
}

$siteFrameworkPath = '';
if(defined('SITE_FRAMEWORK')) {
  if (!defined('SITE_FRAMEWORK_VER')) {
    define('SITE_FRAMEWORK_VER', 'v'.SITE_FRAMEWORK); // 前端结构框架
  }
  if (!defined('HEADER_STYLE')) {
    define('HEADER_STYLE', SITE_FRAMEWORK); // 前端结构框架
  }
  $siteFrameworkPath = '/' . SITE_FRAMEWORK_VER;
}

if (!defined('SITE_FRAMEWORK_PATH')) {
  define('SITE_FRAMEWORK_PATH', $siteFrameworkPath); // 前端结构框架
}

// echo $browserLang;
// echo BROWSER_LANG;

// exit;

// 是否是商城
// if (!defined('E_COMMERCE_SITE')) {
//   define('E_COMMERCE_SITE', false);
// }

// 是否是生产环境
// 生产环境需设置为 true！！
// $isProd = (defined('ENVIRONMENT_TYPE') && ('production' === ENVIRONMENT_TYPE || 'local' === ENVIRONMENT_TYPE)) ? true : false;
$isProd = (defined('ENVIRONMENT_TYPE') && 'production' === ENVIRONMENT_TYPE) ? true : false;
if (!defined('IS_PROD')) {
  define('IS_PROD', $isProd);
}
// 测试环境
$isStaging = (defined('ENVIRONMENT_TYPE') && 'staging' === ENVIRONMENT_TYPE) ? true : false;
if (!defined('IS_STAGING')) {
  define('IS_STAGING', $isStaging);
}
// 本地环境
$isLocal = (defined('ENVIRONMENT_TYPE') && 'local' === ENVIRONMENT_TYPE) ? true : false;
if (!defined('IS_LOCAL')) {
  define('IS_LOCAL', $isLocal);
}

// 静态文件是否使用二级域名？
if (!defined('ASSETS_DOMAIN')) {
  define('ASSETS_DOMAIN', TRUE);
}

// 是否开启 AWS
if (!defined('AWS_ON')) {
  define('AWS_ON', true);
}

// 是否启用 CDN
if (!defined('CDN_ON')) {
  define('CDN_ON', false);
}

// 是否SSL 数据库
if (!defined('DB_SSL')) {
  define('DB_SSL', true);
}

if (!defined('RICH_EDITOR')) {
  define('RICH_EDITOR', true);
}

if (!defined('REST_API_ON')) {
  define('REST_API_ON', true);
}



// 测试站开启前后端静态文件加速
// TODO: 测试没问题后给所有环境开放
if (
  IS_STAGING || IS_LOCAL
) {
  // 是否大陆地区加速
  if (!defined('CN_SPEED_UP')) {
    define('CN_SPEED_UP', true);
  }

  // 不是 divi 编辑器页面
  // if(
  // !is_divi_editor_on()
  // ) {
  //   // 是否启用内容目录 CDN
  //   if (!defined('CONTENT_FOLDER_CDN') && defined('CONTENT_FOLDER_CDN_PRE') && true == defined('CONTENT_FOLDER_CDN_PRE')) {
  //     define('CONTENT_FOLDER_CDN', true);
  //   }
  //   // 是否启用 includes 目录 CDN
  //   if (!defined('WP_INCLUDES_FOLDER_CDN') && defined('WP_INCLUDES_FOLDER_CDN_PRE') && true == defined('WP_INCLUDES_FOLDER_CDN_PRE')) {
  //     define('WP_INCLUDES_FOLDER_CDN', true);
  //   }
  // }

  // 是否启用内容目录 CDN
  if (!defined('CONTENT_FOLDER_CDN_PRE')) {
    define('CONTENT_FOLDER_CDN_PRE', true);
  }
  // 是否启用 includes 目录 CDN
  if (!defined('WP_INCLUDES_FOLDER_CDN_PRE')) {
    define('WP_INCLUDES_FOLDER_CDN_PRE', true);
  }
} else {
}

// 不是 divi 编辑器页面
if (
  !is_divi_editor_on()
) {
  // 是否启用内容目录 CDN
  if (!defined('CONTENT_FOLDER_CDN') && defined('CONTENT_FOLDER_CDN_PRE') && true == defined('CONTENT_FOLDER_CDN_PRE')) {
    define('CONTENT_FOLDER_CDN', true);
  }
  // 是否启用 includes 目录 CDN
  if (!defined('WP_INCLUDES_FOLDER_CDN') && defined('WP_INCLUDES_FOLDER_CDN_PRE') && true == defined('WP_INCLUDES_FOLDER_CDN_PRE')) {
    define('WP_INCLUDES_FOLDER_CDN', true);
  }
}


// 是否启用内容目录 CDN
if (!defined('CONTENT_FOLDER_CDN')) {
  define('CONTENT_FOLDER_CDN', false);
}
// 是否启用 includes 目录 CDN
if (!defined('WP_INCLUDES_FOLDER_CDN')) {
  define('WP_INCLUDES_FOLDER_CDN', false);
}

// 是否将page页面转化为静态文件
if (!defined('ENABLE_STATIC_PAGES')) {
  define('ENABLE_STATIC_PAGES', false);
}
if (!defined('STATIC_PAGES')) {
  define('STATIC_PAGES', false);
}

// 是否压缩 html
if (!defined('MINIFY_HTML')) {
  define('MINIFY_HTML', false);
}

// 跳转功能
if (!defined('REDIRECT_FUNC')) {
  define('REDIRECT_FUNC', TRUE);
}

// 页面右侧浮动按钮
if (!defined('FLOAT_ICONS')) {
  define('FLOAT_ICONS', TRUE);
}
// 是否开启站点验证
// if (!defined('SITE_AUTH_ON')) {
//   define('SITE_AUTH_ON', TRUE);
// }

// 站点地图名称
if (!defined('SITEMAP_NAME')) {
  define('SITEMAP_NAME', 'sitemap');
}

// 是否开启新闻模块
if (!defined('NEWS_SECTION_ON')) {
  define('NEWS_SECTION_ON', true);
}
// 是否开启店铺模块
if (!defined('STORE_SECTION_ON')) {
  define('STORE_SECTION_ON', false);
}
// 是否开启签证业务模块
if (!defined('VISA_SECTION_ON')) {
  define('VISA_SECTION_ON', false);
}
if (!defined('PROJECT_SECTION_ON')) {
  define('PROJECT_SECTION_ON', false);
}

if (!defined('POST_SECTION_NAME')) {
  define('POST_SECTION_NAME', 'Post');
}
if (!defined('NEWS_SECTION_NAME')) {
  define('NEWS_SECTION_NAME', 'News');
}

// 是否开启邮件订阅功能
if (!defined('SUBSCRIBE_EMAIL_ON')) {
  define('SUBSCRIBE_EMAIL_ON', false);
}

// 是否导入公共字段文件夹的字段组
if (!defined('FIELDS_COMMON_FOLDER')) {
  define('FIELDS_COMMON_FOLDER', true);
}

if (!defined('POST_CATETORY_NAME')) {
  define('POST_CATETORY_NAME', 'Category');
}

// 后台是否显示站点设置菜单 - 对于客户管理账户
if (!defined('SITE_SETTING_MENU')) {
  define('SITE_SETTING_MENU', true);
}
// 前端页面禁用 - 请勿随意更改
if (!defined('DISABLE_FRONT')) {
  define('DISABLE_FRONT', false);
}

if (!defined('IMG_LAZYLOAD')) {
  define('IMG_LAZYLOAD', false);
}

// 是否启用 Log日志 数据模块
if (!defined('LOG_DATA_ON')) {
  define('LOG_DATA_ON', false);
}
// 是否启用 seo 数据模块
if (!defined('SEO_DATA_ON')) {
  define('SEO_DATA_ON', false);
}
//是否启用seo自然关键词数据模块
if (!defined('SEO_KEYWORDS_DATA_ON')) {
  define('SEO_KEYWORDS_DATA_ON', false);
}
// 是否启用 webp 图片格式转换功能
if (!defined('SAVE2WEBP_ON')) {
  define('SAVE2WEBP_ON', true);
}


//首页仪表盘数据概览是否开启
if (!defined('DASHBOARD_OVERVIEW_ON')) {
  define('DASHBOARD_OVERVIEW_ON', true);
}
if (!defined('GA4_PROPERTY_ID')) {
  define('GA4_PROPERTY_ID', false);
}

// 后台首页仪表盘是否显示 tips 小贴士 数据
if (!defined('DASHBOARD_TIPS_DATA_ON')) {
  define('DASHBOARD_TIPS_DATA_ON', true);
}


//管理员前端页面产品编辑链接是否显示
if (!defined('EDIT_BTN_FRONT_ON')) {
  define('EDIT_BTN_FRONT_ON', true);
}

//后台分类/标签列表等，联系人字段是否开启
if (!defined('USER_COLUMNS_ON_ITEM_LIST')) {
  define('USER_COLUMNS_ON_ITEM_LIST', false);
}

//普通用户后台媒体库只能看到自己上传的文件
// ML = Media Library
if (!defined('VIEW_USER_OWN_FILES_IN_ML')) {
  define('VIEW_USER_OWN_FILES_IN_ML', true);
}

// 是否有邮箱发送功能
if (!defined('EMAIL_SMTP_ON')) {
  define('EMAIL_SMTP_ON', true);
}

// 是否启用文件上传 API
if (!defined('FILE_UPLOAD_API_ON')) {
  define('FILE_UPLOAD_API_ON', false);
}


// 是否开启数据库客户追踪功能
if (!defined('TRACKING_RECORD_ON')) {
  define('TRACKING_RECORD_ON', true);
}

// whatsapp 按钮相关 ========
if (!defined('WHATAPP_TRANS')) {
  define('WHATAPP_TRANS', true);
}
if (!defined('WHATAPP_TRANS_TYPE')) {
  define('WHATAPP_TRANS_TYPE', 'random');
}

if (!defined('FLOAT_WHATAPP_SHOW_TYPE')) {
  define('FLOAT_WHATAPP_SHOW_TYPE', 'date');
}

if (!defined('TRACKING_RECORD_WHATSAPP_ON')) {
  define('TRACKING_RECORD_WHATSAPP_ON', true);
}

// 是否强制设置前后端语言
if (!defined('SET_LOCALE_ON')) {
  define('SET_LOCALE_ON', false);
}
// 后台菜单翻译功能
if (!defined('BACKEND_MENU_TRANSLATE_ON')) {
  define('BACKEND_MENU_TRANSLATE_ON', true);
}



// 是否关闭用户在浏览器浏览页面 tab 离开时的信息提醒
// TODO: 测试没问题后给所有环境开放
if (IS_STAGING || IS_LOCAL) {
  // 是否大陆地区加速
  if (!defined('WINDOW_TITLE_TIP')) {
    define('WINDOW_TITLE_TIP', true);
  }
} else {
  if (!defined('WINDOW_TITLE_TIP')) {
    define('WINDOW_TITLE_TIP', false);
  }
}


// 定义 http 类型 ============
$http = 'http';
if (
  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  ||
  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
) {
  $http = 'https';
}
// $http = check_http();
// $root_url = $http.'://'.MAIN_SITE_DOMAIN;

if (!defined('HS_HTTP')) {
  define('HS_HTTP', $http . '://');
}

$local_name = 'local';
$local_folder = $local_name . '/';
$local_pre = $local_name . '_';

$is_remote = false;
$whitelist = array(
  '127.0.0.1',
  '::1'
);

if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
  // remote server
  $is_remote = true;
  $local_name = '';
  $local_folder = '';
  $local_pre = '';
}

if (!defined('IS_REMOTE')) {
  define('IS_REMOTE', $is_remote);
}
if (!defined('LOCAL_NAME')) {
  define('LOCAL_NAME', $local_name);
}
if (!defined('LOCAL_FOLDER')) {
  define('LOCAL_FOLDER', $local_folder);
}
if (!defined('LOCAL_PRE')) {
  define('LOCAL_PRE', $local_pre);
}


// 环境定义
$envName = 'stg';
$envFolder = $envName . '/'; //文件夹
$envPre = $envName . '_'; //前缀
$envSuf = '-' . $envName; //后缀
if (FALSE !== IS_PROD) {
  $envName = '';
  $envFolder = '';
  $envPre = '';
  $envSuf = '';
}

if (!defined('ENV_NAME')) {
  define('ENV_NAME', $envName);
}
if (!defined('ENV_FOLDER')) {
  define('ENV_FOLDER', $envFolder);
}
if (!defined('ENV_PRE')) {
  define('ENV_PRE', $envPre);
}
if (!defined('ENV_SUF')) {
  define('ENV_SUF', $envSuf);
}

// 环境文件夹
$envPath = ('production' !== ENVIRONMENT_TYPE) ? ENVIRONMENT_TYPE . '/' : '';
if (!defined('ENV_PATH')) {
  define('ENV_PATH', $envPath);
}

// 页面链接
if (!defined('PAGE_URL')) {
  define('PAGE_URL', HS_HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
}


// ===== ▼▼▼ 全局 ROOT 路径 ▼▼▼ ==== //
// 公共 includes 文件夹  ============
if (!defined('ROOT_INC_PATH')) {
  define('ROOT_INC_PATH', ROOT_PATH . 'includes/');
}

if (!defined('ROOT_CONFIG_PATH')) {
  define('ROOT_CONFIG_PATH', ROOT_INC_PATH . 'config/');
}

if (!defined('ROOT_TPL_PATH')) {
  define('ROOT_TPL_PATH', ROOT_INC_PATH . 'tpl/');
}

if (!defined('ROOT_TOOLS_PATH')) {
  define('ROOT_TOOLS_PATH', ROOT_INC_PATH . 'tools/');
}


// 后台文件模版目录
if (!defined('ROOT_TPL_ADMIN_PATH')) {
  define('ROOT_TPL_ADMIN_PATH', ROOT_TPL_PATH . 'admin/');
}
if (!defined('ADMIN_TPL_PATH')) {
  define('ADMIN_TPL_PATH', ROOT_TPL_ADMIN_PATH);
}

// 数据文件目录
// -生产环境上的目录
if (!defined('ROOT_DATA_PATH_PROD')) {
  define('ROOT_DATA_PATH_PROD', ROOT_PATH . 'data/');
}
// - 根据环境不同则不同的目录
if (!defined('ROOT_DATA_PATH')) {
  define('ROOT_DATA_PATH', ROOT_DATA_PATH_PROD . ENV_PATH);
}

// 内容目录
if (!defined('ROOT_CONTENT_PATH')) {
  define('ROOT_CONTENT_PATH', ROOT_PATH . 'content/');
}
// 主题目录
if (!defined('ROOT_THEMES_PATH')) {
  define('ROOT_THEMES_PATH', ROOT_CONTENT_PATH . 'themes/');
}
// 复制用主题目录
if (!defined('THEMES_DEFAULT_PATH')) {
  define('THEMES_DEFAULT_PATH', ROOT_THEMES_PATH . 'template/');
}

// 插件/Library 目录
// define('ROOT_LIB_PATH', ROOT_INC_PATH . 'lib/');
// if (!defined('ROOT_PLUGINS_PATH')) {
//   define('ROOT_PLUGINS_PATH', ROOT_LIB_PATH);
// }
define('ROOT_LIB_PATH', ROOT_PATH . 'library/');
if (!defined('ROOT_PLUGINS_PATH')) {
  define('ROOT_PLUGINS_PATH', ROOT_LIB_PATH);
}


// 静态文件目录
if (!defined('ROOT_ASSETS_PATH')) {
  define('ROOT_ASSETS_PATH', ROOT_PATH . 'assets/');
}

// 静态文件 common 目录
if (!defined('ROOT_ASSETS_COMMON_PATH')) {
  define('ROOT_ASSETS_COMMON_PATH', ROOT_PATH . 'assets/common/');
}


if (!defined('ROOT_TEST_PATH')) {
  define('ROOT_TEST_PATH', ROOT_PATH . 'test/');
}


if (!defined('MEDIA_TEMP')) {
  define('MEDIA_TEMP', 'media/temp/');
}



// FIELDS ===========
if (!defined('ROOT_FIELDS_PATH')) {
  define('ROOT_FIELDS_PATH', ROOT_DATA_PATH . 'fields/acf/');
}

// APP  ============
if (!defined('ROOT_APPS_PATH')) {
  define('ROOT_APPS_PATH', ROOT_PATH . 'apps/');
}

// SYMFONY ===========
if (!defined('SYMFONY_PATH')) {
  define('SYMFONY_PATH', ROOT_LIB_PATH . 'symfony/');
  define('LIB_SYMFONY_PATH', SYMFONY_PATH);
}

// AWS ===========
if (!defined('AWS_PATH')) {
  define('AWS_PATH', ROOT_LIB_PATH . 'aws/');
  define('LIB_AWS_PATH', AWS_PATH);
}

// GOOGLE ===========
if (!defined('LIB_GOOGLE_PATH')) {
  define('LIB_GOOGLE_PATH', ROOT_LIB_PATH . 'google/');
}


//域名首页  ============
if (!defined('ROOT_URL')) {
  define('ROOT_URL', HS_HTTP . MAIN_DOMAIN);
}
if (!defined('ROOT_PROD_URL')) {
  define('ROOT_PROD_URL', HS_HTTP . MAIN_DOMAIN_PROD);
}

// URLs  ============
if (!defined('ROOT_INC_URL')) {
  define('ROOT_INC_URL', ROOT_URL . '/includes/');
}
// 生产环境 数据目录链接
if (!defined('ROOT_DATA_PROD_URL')) {
  define('ROOT_DATA_PROD_URL', ROOT_PROD_URL . '/data/');
}
if (!defined('ROOT_DATA_URL')) {
  define('ROOT_DATA_URL', ROOT_URL . '/data/' . LOCAL_FOLDER);
}


// ==== APP 根目录 ==== //
if (!defined('APP_PATH')) {
  define('APP_PATH', ABSPATH);
}
// 防呆
if (!defined('APP_ROOT_PATH')) {
  define('APP_ROOT_PATH', ABSPATH);
}
if (!defined('APP_DATA_PATH')) {
  define('APP_DATA_PATH', ABSPATH . 'data/' . ENV_PATH);
}

if (!defined('APP_ITEMS_PATH')) {
  define('APP_ITEMS_PATH', APP_DATA_PATH . 'items/');
}

if (!defined('APP_TOOLS_PATH')) {
  define('APP_TOOLS_PATH', ABSPATH . 'tools/');
}

if (!defined('APP_STATIC_PATH')) {
  define('APP_STATIC_PATH', ABSPATH . 'static/');
}

if (!defined('UPLOADS_PATH') && !defined('APP_UPLOADS_PATH')) {
  define('APP_UPLOADS_PATH', ABSPATH . 'uploads/');
}

if (!defined('APP_INFO_PATH')) {
  define('APP_INFO_PATH', APP_DATA_PATH . 'app_info/');
}

if (!defined('APP_FIELDS_PATH')) {
  define('APP_FIELDS_PATH', APP_DATA_PATH . 'fields/');
}
if (!defined('APP_ACF_FIELDS_PATH')) {
  define('APP_ACF_FIELDS_PATH', APP_FIELDS_PATH . 'acf/');
}


// ===== ▼▼▼▼▼ AWS/CDN 等静态链接/路径等定义的常量 ▼▼▼▼▼ ===== //

// app 设置信息文件
$appAdminFile = APP_INFO_PATH . 'administor.json';

// 默认值
$isAwsOn = defined('AWS_ON') ? AWS_ON : false; // aws 是否开启
$isAliyunOn = defined('ALIYUN_ON') ? ALIYUN_ON : false; // aliyun 是否开启
$isCdnOn = defined('CDN_ON') ? CDN_ON : false; // cdn 是否开启
$isStaticPagesOn = defined('STATIC_PAGES') ? STATIC_PAGES : false; // 静态页面 是否开启
$userEditPageOn = defined('USER_EDIT_PAGE_ON') ? USER_EDIT_PAGE_ON : false; // 是否开放页面编辑权限
$showMenuPage = defined('SHOW_MENU_PAGE') ? SHOW_MENU_PAGE : false; // 是否显示后台页面菜单

$disableCopyPage = defined('DISABLE_COPY_PAGE_ITEMS') ? DISABLE_COPY_PAGE_ITEMS : false; // 页面内容是否禁止复制

$useGoogleTranslator = defined('USE_GOOGLE_TRANSLATOR') ? USE_GOOGLE_TRANSLATOR : false; // 语言翻译器

if (file_exists($appAdminFile)) {
  $appAdministor = json_decode(file_get_contents($appAdminFile), true);
  if (
    (isset($appAdministor['file_sources'])) // 如果有设置了图床的参数
  ) {
    $isAwsOn = ('aws' == $appAdministor['file_sources']) ? true : false;
    $isAliyunOn = ('aliyun' == $appAdministor['file_sources']) ? true : false;
  }

  $isCdnOn = (isset($appAdministor['cdn_on'])) ? (bool)$appAdministor['cdn_on'] : $isCdnOn;

  $isStaticPagesOn = (isset($appAdministor['static_pages'])) ? (bool)$appAdministor['static_pages'] : $isStaticPagesOn;

  // 是否开放页面编辑权限
  $userEditPageOn = (isset($appAdministor['user_edit_page_on'])) ? (bool)$appAdministor['user_edit_page_on'] : $userEditPageOn;

  // 是否显示后台页面菜单
  // $showMenuPage = (isset($appAdministor['show_menu_page'])) ? (bool)$appAdministor['show_menu_page'] : (isset($appAdministor['admin_menu_page']['show_menu_page']) ? (bool)$appAdministor['admin_menu_page']['show_menu_page'] : $showMenuPage);
  $showMenuPage = isset($appAdministor['admin_menu_page']['show_menu_page']) ? (bool)$appAdministor['admin_menu_page']['show_menu_page'] : (isset($appAdministor['show_menu_page']) ? (bool)$appAdministor['show_menu_page'] : $showMenuPage
  );
  // $showMenuPage = (isset($showMenuPage)) ? (bool)$showMenuPage : $showMenuPage;

  // 页面内容是否禁止复制
  $disableCopyPage = (isset($appAdministor['disable_copy_page_items_on'])) ? (bool)$appAdministor['disable_copy_page_items_on'] : $disableCopyPage;

  $useGoogleTranslator = (isset($appAdministor['use_google_translator'])) ? (bool)$appAdministor['use_google_translator'] : $useGoogleTranslator;
}
if (!defined('IS_AWS_ON')) {
  define('IS_AWS_ON', $isAwsOn);
}
if (!defined('IS_ALIYUN_ON')) {
  define('IS_ALIYUN_ON', $isAliyunOn);
}
if (!defined('IS_CDN_ON')) {
  define('IS_CDN_ON', $isCdnOn);
  // define('IS_CDN_ON', false);
}
if (!defined('IS_STATIC_PAGES_ON')) {
  define('IS_STATIC_PAGES_ON', $isStaticPagesOn);
}
if (!defined('USER_EDIT_PAGE_ON')) {
  define('USER_EDIT_PAGE_ON', $userEditPageOn);
}
if (!defined('SHOW_MENU_PAGE')) {
  define('SHOW_MENU_PAGE', $showMenuPage);
}

if (!defined('DISABLE_COPY_PAGE_ITEMS')) {
  define('DISABLE_COPY_PAGE_ITEMS', $disableCopyPage);
}

// 语言翻译器
if (!defined('USE_GOOGLE_TRANSLATOR')) {
  define('USE_GOOGLE_TRANSLATOR', $useGoogleTranslator);
}

// app 是否站点验证文件
$siteAuthFile = APP_INFO_PATH . 'ip-restrict.json';
$isSiteAuthOn = defined('SITE_AUTH_ON') ? (bool)SITE_AUTH_ON : false; // 是否站点验证
$ipRestrictChina = defined('IP_RESTRICT_CN') ? (bool)IP_RESTRICT_CN : false; // 是否限制国内IP
$isSitePasswordOn = defined('SITE_PASSWORD_ON') ? (bool)SITE_PASSWORD_ON : false; // 是否密码验证站点
if (file_exists($siteAuthFile)) {
  // 获取设置数据
  $siteAuthInfo = json_decode(file_get_contents($siteAuthFile), true);

  // var_export($siteAuthInfo);

  $ipRestrictChina = (isset($siteAuthInfo['ip_restrict_china'])) ? (bool)$siteAuthInfo['ip_restrict_china'] : $ipRestrictChina;

  $isSitePasswordOn = (isset($siteAuthInfo['restrict_password_on'])) ? (bool)$siteAuthInfo['restrict_password_on'] : $isSitePasswordOn;

  $isSiteAuthOn = (true == $ipRestrictChina || true == $isSitePasswordOn) ? true : $isSiteAuthOn;
}
if (!defined('IP_RESTRICT_CN')) {
  define('IP_RESTRICT_CN', $ipRestrictChina);
}
if (!defined('SITE_PASSWORD_ON')) {
  define('SITE_PASSWORD_ON', $isSitePasswordOn);
}
if (!defined('SITE_AUTH_ON')) {
  define('SITE_AUTH_ON', $isSiteAuthOn);
}
if (!defined('REDIRECT_FUNC')) {
  define('REDIRECT_FUNC', $isSiteAuthOn);
}


// var_dump(IS_AWS_ON);
// var_dump(IS_CDN_ON);
// var_dump(IS_STATIC_PAGES_ON);

if (IS_REMOTE) {
  // $assets_http = 'https://';
  $assets_http = HS_HTTP;
} else {
  $assets_http = '//'; //IE/Safari 无法辨认
}

// 根目录静态文件域名
if (!defined('ASSETS_ROOT_URL')) {
  // define('ASSETS_ROOT_URL', HS_HTTP.'assets.'.MAIN_DOMAIN); //IE/Safari 无法辨认

  if (defined('ASSETS_DOMAIN') && FALSE === ASSETS_DOMAIN) {
    $assets_url = $assets_http . MAIN_DOMAIN . '/assets';
    // $assets_url = $assets_http . APP_DOMAIN . '/assets';
  } else {

    $assets_url = $assets_http . 'assets.' . MAIN_DOMAIN;

    if (IS_CDN_ON) {
      $assets_url =  '//assets.' . CDN_DOMAIN;
    }
    // else {
    // $assets_url = $assets_http . 'assets.' . MAIN_DOMAIN;
    // }
    // $assets_url = $assets_http . 'assets.' . MAIN_DOMAIN;
  }


  define('ASSETS_ROOT_URL', $assets_url);
}


if (!defined('ASSETS_ROOT_LOC_URL')) {
  if (
    (defined('ASSETS_DOMAIN') && false != ASSETS_DOMAIN)
    || !defined('ASSETS_DOMAIN')
  ) {
    define('ASSETS_ROOT_LOC_URL', $assets_http . 'assets.' . hs_project_config('local')['mainDomain']);
  } else {
    define('ASSETS_ROOT_LOC_URL', ASSETS_ROOT_URL);
  }
}


// 上传的文件域名
if (!defined('FILES_ROOT_URL')) {

  if (IS_CDN_ON) {
    // $files_url = '//files' . ENV_SUF . '.' . CDN_DOMAIN;
    $cdnComain = (defined('FILE_CDN_DOMAIN_PROD') && true === FILE_CDN_DOMAIN_PROD) ? CDN_DOMAIN_PROD : CDN_DOMAIN;
    $fileCdnDomain = str_replace('cdn.', '', $cdnComain);
    $files_url = HS_HTTP . 'files' . ENV_SUF . '.' . $fileCdnDomain; //files-stg.xxx.com, files.xxx.com

    // echo $fileCdnDomain;
  } else {
    $files_url = HS_HTTP . 'files.' . MAIN_DOMAIN;
  }

  define('FILES_ROOT_URL', $files_url);
}

// echo ASSETS_ROOT_URL;

// exit;

// content 文件夹内的静态文件域名
if (
  !defined('STATIC_ROOT_URL')
  && IS_CDN_ON // CDN 是否开启
) {
  define('STATIC_ROOT_URL', 'https://static.' . CDN_DOMAIN);
}

// 后台管理静态文件 cdn 域名
if (
  !defined('ADMIN_CDN_URL')
  && IS_CDN_ON // CDN 是否开启
) {
  // $staticProjectName = ('hsapp' == PROJECT) ? 'hardsun' : PROJECT;
  define('ADMIN_CDN_URL', 'https://static-admin.' . CDN_DOMAIN);
}
if (
  !defined('LIBRARY_CDN_URL')
  && IS_CDN_ON // CDN 是否开启
) {
  // $staticProjectName = ('hsapp' == PROJECT) ? 'hardsun' : PROJECT;
  define('LIBRARY_CDN_URL', 'https://library.' . CDN_DOMAIN);
}
// ===== ▲▲▲▲▲ AWS/CDN 等静态链接/路径等定义的常量 ▲▲▲▲▲ ===== //


// ===== ▼▼▼▼▼ ROOT 链接/路径、其他数据等定义的常量 ▼▼▼▼▼ ===== //
if (!defined('TOOLS_ROOT_URL')) {
  define('TOOLS_ROOT_URL', '//' . MAIN_DOMAIN . '/tools');
}

// if (!defined('FILES_ROOT_URL')) {
//   define('FILES_ROOT_URL', '//files.' . MAIN_DOMAIN);
// }


if (!defined('ROOT_ASSETS_URL')) {
  define('ROOT_ASSETS_URL', ROOT_URL . '/assets/');
}
if (!defined('ROOT_ASSETS_COMMON_URL')) {
  define('ROOT_ASSETS_COMMON_URL', ROOT_URL . '/assets/common/');
}
if (!defined('ROOT_FIELDS_URL')) {
  define('ROOT_FIELDS_URL', ROOT_DATA_URL . 'fields/acf/');
}
// 内容目录链接
if (!defined('ROOT_CONTENT_URL')) {

  $rootContentUrl = ROOT_URL . '/content/';

  // var_export($_GET);

  // var_export($rootContentUrl);
  // var_export($hsPageUrl);
  // var_export(is_hs_admin_project());
  // var_export(is_hs_admin_page_edit());

  // echo ROOT_URL;
  // 如果有定义了静态域名链接
  if (
    defined('CONTENT_FOLDER_CDN')
    && true === CONTENT_FOLDER_CDN
    && defined('STATIC_ROOT_URL') && !empty(STATIC_ROOT_URL)
    // 同时，不是 divi 编辑器页面
    && (
      // empty($_GET['et_fb'])
      (!is_divi_editor_on() && !is_hs_admin_project()) // 不是 divi 编辑器页面 且 不是 hsapp 后台
      || !is_hs_admin_page_edit() // 不是 hsapp 后台页面编辑
    )
  ) {
    // var_export($hsPageUrl);
    // var_export(is_hs_admin_project());
    $rootContentUrl = STATIC_ROOT_URL . '/';
  }

  // var_export($rootContentUrl);

  define('ROOT_CONTENT_URL', $rootContentUrl);
}

// echo ROOT_CONTENT_URL;
// 主题目录链接
if (!defined('ROOT_THEME_URL')) {
  define('ROOT_THEME_URL', ROOT_CONTENT_URL . 'themes/');
}
// app 插件目录链接
if (!defined('ROOT_PLUGINS_URL')) {
  define('ROOT_PLUGINS_URL', ROOT_CONTENT_URL . 'plugins/');
}

// 插件/lib目录链接
// if (!defined('ROOT_LIB_URL')) {
//   define('ROOT_LIB_URL', ROOT_INC_URL . 'lib/');
// }
if (!defined('ROOT_LIB_URL')) {
  define('ROOT_LIB_URL', ROOT_URL . '/library/');
}
// ===== ▲▲▲▲▲ ROOT 链接/路径、其他数据等定义的常量 ▲▲▲▲▲ ===== //


// ===== ▼▼▼▼▼ APP 链接/路径、其他数据等定义的常量 ▼▼▼▼▼ ===== //

// app 基础信息文件
$appConfFile = APP_INFO_PATH . 'app.json';
if (file_exists($appConfFile)) {
  $appConf = json_decode(file_get_contents($appConfFile), true);

  // 如果有设置了域名
  $appDomain = $appConf['domain'];

  if (!empty($appDomain)) {
    if (!defined('APP_POST_ID')) { //app/站点名称
      define('APP_POST_ID', $appConf['id']);
    }
    if (!defined('APP_NAME')) { //app/站点名称
      define('APP_NAME', $appConf['app_name']);
    }
    if (!defined('APP_NAME_PARENT')) { //app/站点名称 父级
      define('APP_NAME_PARENT', $appConf['app_name_parent']);
    }

    if (!defined('APP_TITLE_APP')) { //app/站点名称
      define('APP_TITLE_APP', ucwords(!empty($appConf['app_title']) ? trim($appConf['app_title']) : trim($appConf['company_name'])));
    }

    if (!empty($appConf['app_name_parent'])) {
      $appDomain .= '/' . APP_NAME;
    }

    if (!defined('APP_DOMAIN')) {
      define('APP_DOMAIN', $appDomain);
    }

    if (!defined('APP_ROOT_URL')) { //站点链接
      define('APP_ROOT_URL', HS_HTTP . APP_DOMAIN);
    }

    if (!defined('APP_ADMIN_ROOT_URL')) { //站点链接
      $appAdminRootUrl = APP_ROOT_URL;
      if (defined('ADMIN_FOLDER') && !empty(ADMIN_FOLDER)) {
        $appAdminRootUrl = $appAdminRootUrl . ADMIN_FOLDER;
        // define('APP_ADMIN_ROOT_URL', $appAdminRootUrl . ADMIN_FOLDER);
      }
      define('APP_ADMIN_ROOT_URL', $appAdminRootUrl);
    }



    $isEcommerceSite = (!empty($appConf['is_ecommerce_site'])) ? (bool)$appConf['is_ecommerce_site'] : false;
  }
}

// app 内容目录
if (!defined('APP_CONTENT_PATH')) {
  define('APP_CONTENT_PATH', APP_ROOT_PATH . 'content/');
}
if (!defined('APP_THEMES_PATH')) {
  define('APP_THEMES_PATH', APP_CONTENT_PATH . 'themes/');
}

// var_export($_SERVER);
// app 内容目录链接
if (!defined('APP_DOMAIN')) { //站点链接
  define('APP_DOMAIN', $_SERVER['HTTP_HOST']);
}
if (!defined('APP_ROOT_URL')) { //站点链接
  define('APP_ROOT_URL', HS_HTTP . APP_DOMAIN);
}
$appContentUrl = APP_ROOT_URL . '/content/';
if (!defined('APP_CONTENT_URL')) {
  define('APP_CONTENT_URL', $appContentUrl);
}
// if (!defined('STATIC_ROOT_URL')) { //站点域名
//   define('STATIC_ROOT_URL', APP_CONTENT_URL);
// }
// app 主题目录链接
if (!defined('APP_THEMES_URL')) {
  define('APP_THEMES_URL', APP_CONTENT_URL . 'themes/');
}

// app 插件文件夹路径
if (!defined('APP_PLUGINS_PATH')) {
  define('APP_PLUGINS_PATH', ROOT_CONTENT_PATH . 'plugins/');
}

if (!defined('APP_TOOLS_URL')) {
  define('APP_TOOLS_URL', APP_ROOT_URL . '/tools');
}

// 是否是商城
if (!defined('E_COMMERCE_SITE')) {
  define('E_COMMERCE_SITE', isset($isEcommerceSite) ? $isEcommerceSite : false);
}

// SHOP_NAME
if (!defined('SHOP_NAME')) {
  define('SHOP_NAME', 'Shop');
}
// ▲▲▲▲▲ APP 定义的常量 ▲▲▲▲▲ //








// 定义主站域名状态  ============
$http_host = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : APP_DOMAIN;

if (!defined('SUB_SITE')) {
  $sub_site = (strpos($http_host, MAIN_DOMAIN) === false) ? true : false; //如果网址不包含主域名，则是子站
  $sub_site = (strpos($http_host, APP_DOMAIN) !== false) ? true : $sub_site; // 如果网址包含子站域名，则是子站，覆盖前一个设置
  define('SUB_SITE', $sub_site);
}

//是否是管理站
if (!defined('MANAGE_SITE')) {
  $manage_site = (strpos($http_host, 'manage.') !== false) ? true : false;
  // $manage_site = (str_contains($http_host, 'manage.')) ? true : false;
  define('MANAGE_SITE', $manage_site);
}

// 是否是主站
// $mainSite = (PROJECT == APP_NAME) ? true : !SUB_SITE;
if (!defined('MAIN_SITE') && defined('PROJECT') && defined('APP_NAME')) {
  define('MAIN_SITE', (PROJECT == APP_NAME) ? true : !SUB_SITE);
}


// ==== WP =====
// @link https://wordpress.org/support/article/editing-wp-config-php/
if (!isset($loadWp) || (isset($loadWp) && false != $loadWp)) {
  include_once(ROOT_CONFIG_PATH . 'defines-wp.php');
}



// API  ============

// api 版本

if (!defined('API_VER_NUM')) {
  define('API_VER_NUM', 1);
}

$apiVer = 'v' . API_VER_NUM;

// 如果不知道作用，请勿修改
if (!defined('API_PREFIX')) {
  define('API_PREFIX', 'wp-json'); // api
  $apiVer = P_CODE . '/v2';
}

// api 版本
if (!defined('API_VER')) {
  define('API_VER', $apiVer);
}

if (!defined('API_BASE')) {
  define('API_BASE',  '/' . API_PREFIX . '/' . API_VER);
}

if (!defined('APP_API_ROOT_URL')) {
  define('APP_API_ROOT_URL', APP_ROOT_URL . '/' . API_PREFIX);
}
if (!defined('APP_API_BASE_URL')) {
  define('APP_API_BASE_URL', APP_ROOT_URL . API_BASE);
}

if (!defined('ROOT_API_URL')) {
  define('ROOT_API_URL', ROOT_URL . '/' . API_PREFIX);
}

if (!defined('ROOT_API_BASE_URL')) {
  define('ROOT_API_BASE_URL', ROOT_URL . API_BASE);
}
if (!defined('WP_API_BASE_URL')) {
  define('WP_API_BASE_URL', ROOT_API_URL . 'wp/v2/');
}


if (!defined('API_KEY')) {
  define('API_KEY', 'b128p5ooh87we1qjcv8eyf9jwllpqdx7');
}


// JWT Settings
if (!defined('JWT_AUTH_SECRET_KEY')) {
  define('JWT_AUTH_SECRET_KEY', 'm>lNGoc{@JJ95jrQ*$95=F?|vJ/7]]vzPOs8r|||9E>P|)+VzB6r%LzzL)G*g6=');
}

if (!defined('API_JWT_VER')) {
  define('API_JWT_VER', 'jwt-auth/v1');
}

if (!defined('JWT_AUTH_CORS_ENABLE')) {
  define('JWT_AUTH_CORS_ENABLE', true);
}

// JWT token 过期时间
if (!defined('JWT_AUTH_EXPIRE')) {
  define('JWT_AUTH_EXPIRE', 30); // JWT token 过期时间，一个月
}


// if ( ! defined( 'APP_API_BASE_URL' ) ) {
//   define('APP_API_BASE_URL', ROOT_URL . API_BASE);
// }






//（数据库形式）普通用户后台管理界面-根目录链接-简洁
if (!defined('ACCOUNT_URL')) {
  define('ACCOUNT_URL', '/account');
}

//（数据库形式）公司内部后台管理界面-根目录链接
if (!defined('ACCOUNT_ADMIN_URL')) {
  define('ACCOUNT_ADMIN_URL', '/account-admin');
}

//（自构建）公司内部后台管理界面-根目录文件夹名称
if (!defined('ADMIN_MANAGE_NAME')) {
  define('ADMIN_MANAGE_NAME', 'admin-manage');
}
//（自构建）公司内部后台管理界面-根目录链接
if (!defined('ADMIN_MANAGE_URL')) {
  define('ADMIN_MANAGE_URL', ROOT_URL . '/' . ADMIN_MANAGE_NAME);
}
//（自构建）公司内部后台管理界面-根目录路径
if (!defined('ADMIN_MANAGE_PATH')) {
  define('ADMIN_MANAGE_PATH', ROOT_PATH . '/' . ADMIN_MANAGE_NAME);
}




if (!defined('PROTECT_METAS')) {
  define('PROTECT_METAS', [
    'tel',
    'e_mail',
    'company_web',
    'audit_profile',
    'level_id',
    'total_score',
    'total_filled_fields',
  ]);
}

if (!defined('SUB_SITE_NAMES')) {
  define('SUB_SITE_NAMES', [
    'events',
    'news',
    'video',
    'academic',
    'doc',
    'company',
    'db',
    'help',
    'auctions'
  ]);
}

if (!defined('CUSTOM_LABELS')) {
  define('CUSTOM_LABELS', [
    'yes' => '支持',
    'no' => '不支持',
    'ask' => '可协商',
  ]);
}

if (!defined('KEYWORD_EXCLUDE')) {
  define('KEYWORD_EXCLUDE', "，。、；：“”‘’【】「」～！.,\|\{\}~\@\#\$\/\+");
}



// 判断是否为手机  ============
// $isMobile = false;
$useragent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$isMobile = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) ? true : false;


if (!defined('IS_MOBILE')) {
  define('IS_MOBILE', $isMobile);
}
if (!defined('DEVICE_AP')) {
  define('DEVICE_AP', (!empty($isMobile)) ? '-sp' : '');
}
if (!defined('DEVICE_AP_UL')) {
  define('DEVICE_AP_UL', (!empty($isMobile)) ? '_sp' : '');
}

// Others  ============

// 测试手机号
if (!defined('TEST_PHONE')) {
  define('TEST_PHONE', '13459225864');
}
if (!defined('TEST_MAIL')) {
  define('TEST_MAIL', 'chris@hardsun.cn');
}


// 客服邮箱
if (!defined('EMAIL_INFO')) {
  define('EMAIL_INFO', 'info@' . MAIN_DOMAIN_PROD);
}



// 测试手机号
// if (!defined('TEST_PHONE')) {
//   define('TEST_PHONE', '13459225864');
// }


if (!defined('PER_PAGE')) {
  define('PER_PAGE', 24);
}


if (!defined('SCHEMA_DRAFT')) {
  define('SCHEMA_DRAFT', 'http://json-schema.org/draft-04/schema#');
}

if (!defined('ID_CODE')) {
  define('ID_CODE', 'hsapppid');
}

// if (!defined('APP_KEY_ON')) {
//   define('APP_KEY_ON', false);
// }
if (!defined('APP_KEY_LABEL')) {
  define('APP_KEY_LABEL', 'hs_ak');
}
// if (!defined('APP_KEY')) {
//   define('APP_KEY', 'b128p5ooh87we1qjcv8eyf9jwllpqdx7');
// }
// if (!defined('APP_SECRET')) {
//   define('APP_SECRET', 'bnf84lcqo5onlua8j8lbkcuw14z2uuvp');
// }

// if (!defined('API_KEY_ON')) {
//   define('API_KEY_ON', APP_KEY_ON);
// }
if (!defined('API_KEY_LABEL')) {
  define('API_KEY_LABEL', APP_KEY_LABEL);
}
// if (!defined('APP_KEY') && defined('API_KEY')) {
//   define('APP_KEY', API_KEY);
// }
// if (!defined('API_SECRET')) {
//   define('API_SECRET', APP_SECRET);
// }

if (!defined('API_TOKEN_LABEL')) {
  define('API_TOKEN_LABEL', 'hs_at');
}

if (!defined('AUTH_USER_COOKIE_NAME')) {
  define('AUTH_USER_COOKIE_NAME', '9e03a542fc1ecc5c213c3ef8d7');
}

if (!defined('CAPTCHA_CLIENT_KEY')) {
  define('CAPTCHA_CLIENT_KEY', '6LfWUc8dAAAAAFQm1hG_0Q3k0X30p4NloypS9A8c');
}
if (!defined('CAPTCHA_SITE_KEY')) {
  define('CAPTCHA_SITE_KEY', '6LfWUc8dAAAAACNJnNId15Y0NAqZ7JdOAweuiaUd');
}

$captchaDomain = ('cn' == BROWSER_LANG) ? 'www.recaptcha.net' : 'www.google.com';
if (!defined('CAPTCHA_DOMAIN')) {
  define('CAPTCHA_DOMAIN', $captchaDomain);
}

/**
 * 第三方以及微信设置
 */

// weixin dev
if (!defined('WX_APPID')) {
  define('WX_APPID', 'xxxx');
}
if (!defined('WX_SECRET')) {
  define('WX_SECRET', 'xxxx');
}

if (!defined('ENCODE_KEY')) {
  define('ENCODE_KEY', 'D5c8Ym3DJ4DE9ZZPdpwfKkyGyKQWXjuy');
}


if (!defined('API_3RD_PATH_ROOT')) {
  define('API_3RD_PATH_ROOT', ROOT_PATH . 'api-3rd/');
}
if (!defined('API_3RD_PATH')) {
  define('API_3RD_PATH', ROOT_PATH . 'api-3rd/');
}


if (!defined('API_3RD_URL')) {
  define('API_3RD_URL', ROOT_URL . "/api-3rd/");
}


if (!defined('LIB_WX_PATH')) {
  define('LIB_WX_PATH', ROOT_LIB_PATH . 'wechat-developer/vendor/zoujingli/wechat-developer/');
}

if (!defined('WX_CONFIG_PATH')) {
  define('WX_CONFIG_PATH', API_3RD_PATH_ROOT . 'wx/');
}
if (!defined('WX_API_PATH')) {
  define('WX_API_PATH', API_3RD_PATH . 'wx/');
}
if (!defined('WX_API_URL')) {
  define('WX_API_URL', API_3RD_URL . 'wx/');
}

// 支付通知文件路径
if (!defined('WX_PAY_NOTIFY_FILE')) {
  define('WX_PAY_NOTIFY_FILE', '/api-3rd/wx/notify-order.php');
}

if (!defined('WX_TOKEN_EXPIRED_DAYS')) {
  define('WX_TOKEN_EXPIRED_DAYS', 30 * 60 * 60 * 24); //30天
}


if (!defined('ALI_API_PATH')) {
  define('ALI_API_PATH', API_3RD_PATH . 'ali/');
}

if (!defined('ALI_API_URL')) {
  define('ALI_API_URL', API_3RD_URL . 'ali/');
}


// if (!defined('MINUTE_IN_SECONDS')) {
//   define('MINUTE_IN_SECONDS', 60);
//   define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
//   define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
//   define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
//   define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
//   define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);
// }

if (!defined('TITLE_SEP')) {
  define('TITLE_SEP', ' - ');
}

// pages
if (!defined('HOME_PAGE_ID')) {
  define('HOME_PAGE_ID', 2);
}


if (!defined('PARENT_THEME_NAME')) { //主题/站点名称
  define('PARENT_THEME_NAME', 'hsApp');
}

if (!defined('THEME_NAME')) { //主题/站点名称
  define('THEME_NAME', PARENT_THEME_NAME);
}
if (!defined('DEFAULT_THEME_NAME')) { //默认主题名称
  define('DEFAULT_THEME_NAME', 'hs');
}


if (!defined('ADMIN_PAGE_PX')) { //主题/站点名称
  define('ADMIN_PAGE_PX', 'hs-page-');
}

// Country calling codes
if (!defined('CC_CODE')) {
  define('CC_CODE', '86');
}


if (!defined('INVITE_CODE_LENGTH')) {
  define('INVITE_CODE_LENGTH', 6);
}

// 上传文件的路径，例：2022/08/xxx.jpg
if (!defined('UPLOADS_PATH_ROOT')) {
  define('UPLOADS_PATH_ROOT', date('Y') . '/' . date('m') . '/');
}

if (!defined('FORM_FIELD_PREX')) { //默认主题名称
  define('FORM_FIELD_PREX', P_CODE . '_f_field_');
}

if (
  !defined('ICL_LANGUAGE_CODE')
  && (
    !defined('WPML_ON')
    || (defined('WPML_ON') && true !== WPML_ON)
  )
) { //默认主题名称
  define('ICL_LANGUAGE_CODE', SITE_MAIN_LANG);
}

// 默认时区
if (!defined('DATE_DEFAULT_TIMEZONE')) {
  define('DATE_DEFAULT_TIMEZONE', 'asia/shanghai');
}

// 通知功能开启
if (!defined('NOTIFY_ON')) {
  define('NOTIFY_ON', false);
}
// 短信验证码长度
if (!defined('SMS_CODE_LENGTH')) {
  define('SMS_CODE_LENGTH', 6);
}



// COOKIE  ============

// 这个暂时不在这里写，不然可能会影响cookie正常功能，
// 如无法登录等
// if(false == MANAGE_SITE) {
//   if (!defined('COOKIEPATH')) {
//     define('COOKIEPATH', '/');
//   }

//   if (!defined('COOKIE_DOMAIN') && !empty(APP_DOMAIN)) {
//     define('COOKIE_DOMAIN', APP_DOMAIN);
//   }

//   if (!defined('COOKIE_SECURE')) {
//     $c_secure = false;

//     if (IS_REMOTE) {
//       $c_secure = true;
//     }
//     define('COOKIE_SECURE', $c_secure);
//   }
// }

// require_once(ROOT_PATH . 'includes/config/defines-common.php');
// 载入必要文件  ============
require_once ROOT_INC_PATH . 'src/autoload.php';
require_once ROOT_INC_PATH . 'inc/hs-common-functions.php';
require_once ROOT_INC_PATH . 'func/file-handle.php';

// 常量 SETTINGS // ※ 这个必须放在后面
// require_once ROOT_INC_PATH . 'func/conf-common.php';

// hs_include('func/conf-common.php');



// 页面压缩/设置等
// if (is_static_pages_on()) {
//   require_once ROOT_INC_PATH . 'func/minify-page.php';
// }
