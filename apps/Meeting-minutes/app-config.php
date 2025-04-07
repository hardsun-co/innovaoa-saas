<?php
/*
 * @Description: 数据库，app 自定义配置参数等
 * @Author: chris@hardsun
 * @Date: 2021-06-25 23:10:33
 * @LastEditTime: 2025-04-03 09:44:45
 * @LastEditors: pjw@hardsun
 * @File: /apps/innovaoa/app-config.php
 * @Copyright: Copyright© 2019-2021 HARDSUN Ltd
 */

// app 名 - 最重要！！！
define('APP_NAME', 'innovaoa');
define('MODAL_NAME', 'meeting_minutes');

if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

// 根目录
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', dirname(ABSPATH, 2) . '/');
}

// ===== 自定义常量等 =====
// 是否移动 app 系统的文件夹
if (!defined('MOVE_WP_FOLDER')) {
	define('MOVE_WP_FOLDER', true);
}

// 是否大陆地区加速
define('CN_SPEED_UP', false);

define('RFQ_ON', false);
define('TRACKING_ON', false);
define('IMPORTING_ON', false);

define('SUBSCRIBE_EMAIL_ON', false);


define('LOG_DATA_ON', false);

// 主语言
define('SITE_MAIN_LANG', 'en');

// 多语言站点
define('MULTILANG', false);
// define('SITE_LANG_EXTRA', ['cn']);
// define('MULTI_SUB_DOMAIN', false); // 是否使用子域名作为语言网站



define('WORDS2LANG', [
]);


//管理员前端页面产品编辑链接是否显示
// define('EDIT_BTN_FRONT_ON', false);

//联系人字段是否开启
// define('USER_COLUMNS_ON_ITEM_LIST', false);

//自定义meta
// define('ITEM_DEF_METAS', [
// 	'rf_frequency', 'rf_output_power', 'active_aperture', 'factory_default_mode', 'dc_supply_voltage', 'moq', 'certification'
// ]);
// define('ITEM_DEF_METAS', ['item_no', 'item_name', 'material', 'size', 'sample_time', 'price_term', 'payment_terms', 'delivery_time', 'color', 'customization']);

//自定义重复器
// define('REPEATOR', [
// 	'faq'
// ]);

// if (!IS_PROD) {
// define('SEO_DATA_ON', true);
// define('SEO_KEYWORDS_DATA_ON', false);
define('DASHBOARD_OVERVIEW_ON', false);

//企业微信
if (!defined('WEWORK_AGENT_ID')) {
  define('WEWORK_AGENT_ID', '1000033');
}
if (!defined('WEWORK_CORP_ID')) {
  define('WEWORK_CORP_ID', 'ww931c8cda4e62363b');
}
if (!defined('WEWORK_SECRET')) {
  define('WEWORK_SECRET', '596Bmzb9selT_k3iCtreZN72hSJ6cM2clHmhqd3ohQk');
}
if (!defined('WEWORK_TOKEN')) {
  define('WEWORK_TOKEN', 'pevWUe4rYKOhyCCjD6gZ');
}
if (!defined('WEWORK_AES_KEY')) {
  define('WEWORK_AES_KEY', 'iX2eTsS6bdw98dSXmXA3Uz2MnCs1dPIws6iymSP7lv1');
}
if (!defined('WEWORK_ROSTER_SECRET')) {
  define('WEWORK_ROSTER_SECRET', 'sslFJ2VFKWq0WZFmik9Pm6gxSg01Pvq92jpEQP6Ff9Y');
}
// if (!defined('WEWORK_DOC_LIST')) {
//   define('WEWORK_DOC_LIST', [
//     'dce_rt1ARK0rnykQeC5UA64oeYVrP3y-ZV5sgOQ9ihfjpUhsEuuc-W6LiGSxymhI2W-IQlgqzw1wepWog1m3G58g'
//   ]);
// }
if (!defined('WEWORK_DOC_ADMINS')) {
  define('WEWORK_DOC_ADMINS', [
    'pjw1998',
    'huxianjian',
    'lindeqiang'
  ]);
}
// var $docIdFile = definded('FEEDBACK_DOC_FILE')&&!empty(FEEDBACK_DOC_FILE)?FEEDBACK_DOC_FILE:APP_ROOT_PATH.'test/feedback/data/common/docid.txt';
// //sheetId保存文件
// var $sheetIdFile = definded('FEEDBACK_SHEET_FILE')&&!empty(FEEDBACK_SHEET_FILE)?FEEDBACK_SHEET_FILE:APP_ROOT_PATH.'test/feedback/data/common/sheetid.txt';
// //viewId保存文件
// var $viewIdFile = definded('FEEDBACK_VIEW_FILE')&&!empty(FEEDBACK_VIEW_FILE)?FEEDBACK_VIEW_FILE:APP_ROOT_PATH.'test/feedback/data/common/viewid.txt';
// //反馈行保存文件目录
// var $rowRecordPath = definded('FEEDBACK_Record_PATH')&&!empty(FEEDBACK_Record_PATH)?FEEDBACK_Record_PATH:APP_ROOT_PATH.'test/feedback/data/records/';


// define('GA4_PROPERTY_ID', '311672087');
// }
// define('GA4_PROPERTY_ID', 351682618);

// define('NEWS_FEATURED', true);
// define('POSTS_FEATURED', true);

// 是否开启数据库客户追踪功能
// define('TRACKING_RECORD_ON', true);
// define('TRACKING_RECORD_WHATSAPP_ON', true); // 是否追踪 whatsapp 按钮的点击记录

// define('EXTERNAL_IMG_SAVE_TO_DB_ON', false); // 是否追踪 whatsapp 按钮的点击记录

// $wp_debug = (isset($_GET['debug']) && $_GET['debug'] == 1) ? true : false;





// 公共定义参数等
require_once(ROOT_PATH . 'includes/config/defines-common.php');


// hsapp 系统的静态文件链接路径
if (!defined('ROOT_ASSETS_URL_BASE')) {
  define('ROOT_ASSETS_URL_BASE', '../assets');
}


// app 根目录链接路径
// if (!defined('APP_ROOT_URL_BASE')) {
//   define('APP_ROOT_URL_BASE', '../apps/'. APP_NAME);
// }

// if (!defined('OA_FEEDBACK_URL_BASE')) {
//   define('OA_FEEDBACK_URL_BASE', APP_ROOT_URL_BASE . '/tools/oa-feedback/api/');
// }



// oa 菜单文件
// if (!defined('OA_MENUS_FILE')) {
//   define('OA_MENUS_FILE', APP_DATA_PATH . '/menus.json');
// }