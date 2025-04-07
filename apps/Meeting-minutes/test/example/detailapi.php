<?php
/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:09:08
 * @LastEditTime: 2025-04-07 09:01:15
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\apps\Meeting-minutes\test\example\detailapi.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

require_once '../../../../common/common.php';
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