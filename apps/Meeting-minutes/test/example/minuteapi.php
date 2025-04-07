<?php
/*
 * @Description: 会议记录API处理
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:09:08
 * @LastEditTime: 2025-04-07 09:29:33
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\apps\Meeting-minutes\test\example\minuteapi.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

require_once '../../../../common/common.php';
use Hs\Fmproject\Minutes;
$minuteIndex = Minutes\Index::getInstance();
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
  case 'create':
    // 创建新会议记录
    $result = $minuteIndex->create($_POST);
    if (is_rest_ok($result) && !empty($_POST['id'])) {
      $result['data'] = $minuteIndex->getItem($_POST['id']);
    }
    break;
    
  case 'update':
    // 更新现有会议记录
    $result = $minuteIndex->update($_POST);
    // 如果更新成功，获取完整记录信息返回
    if (is_rest_ok($result) && !empty($_POST['id'])) {
      $result['data'] = $minuteIndex->getItem($_POST['id']);
    }
    break;
    
  case 'delete':
    // 删除会议记录
    if (!empty($_POST['id'])) {
      $result = $minuteIndex->delete(['id' => $_POST['id']]);
    } else {
      $result = ['code' => 1, 'message' => 'ID不能为空'];
    }
    break;
    
  case 'get':
    // 获取单个会议记录
    if (!empty($_GET['id'])) {
      $result = $minuteIndex->getItem($_GET['id']);
      if ($result) {
        $result = ['code' => 0, 'message' => '获取成功', 'data' => $result];
      } else {
        $result = ['code' => 1, 'message' => '记录不存在'];
      }
    } else {
      $result = ['code' => 1, 'message' => 'ID不能为空'];
    }
    break;
    
  default:
    // 未指定操作或其他操作
    $result = ['code' => 1, 'message' => '未知操作'];
    break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;