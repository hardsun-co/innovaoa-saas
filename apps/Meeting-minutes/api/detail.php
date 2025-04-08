<?php
/*
 * @Description:
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:09:08
 * @LastEditTime: 2025-04-08 17:02:41
 * @LastEditors: pjw@hardsun
 * @FilePath: \hsapp\apps\meeting-minutes\api\detail.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

// 获取根目录的路径下的 common/common.php
require_once dirname(__DIR__,3) . '/common/common.php';

use Hs\Fmproject\Minutes;
$minuteIndex = Minutes\Index::getInstance();
$action = isset($_POST['action'])?$_POST['action']:'';

switch ($action) {
  case 'create':
    $result = $minuteIndex->addDetail($_POST);
    break;
  case 'update':
    $result = $minuteIndex->updateDetail($_POST);
    break;
  case 'delete':
    $result = $minuteIndex->deleteDetail($_POST);
    break;
  default:
    $result = [
      'code' => 400,
      'message' => 'Invalid action'
    ];
    break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;