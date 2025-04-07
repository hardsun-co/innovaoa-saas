<?/*
/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:15:03
 * @LastEditTime: 2025-04-03 16:57:04
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\includes\src\Data\Msdb\Common.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

namespace Hs\Data\Msdb;

/**
 * mssql数据库基础类
 * 
 * 提供表的增、删、改、查等功能
 * 
 * @author pjw@hardsun
 * @date 2025-04-03
 */
class Common
{
  var $post_type;
  var $post_type_label;
  //数据库连接对象
  var $msdb;
  //表名
  var $table_name;
  var $detailHandle;
  //数据类型定义
  var $dataTypeSets;
  //构造函数
  public function __construct()
  {
    //引入数据库类
    $this->msdb = \Hs\Msdb\Index::getInstance();
  }

  public function create($data = [])
  {
    $data['id'] = 0;
    $result = $this->update($data, false);
    return $result;
  }

  public function update($data = [], $update = true)
  {
    $actionLabel = (true == $update) ? 'update' : 'create';
    $data = $this->prepareOtherData($data, $actionLabel);
    $result = [];
    $dbData = $this->prepareRawData($data, $this->dataSettings('all'));

    $can = $this->can($dbData, $actionLabel);

    if (is_rest_ok($can)) {
      $result['id'] = $this->saveData($dbData, $update, $this); //如果找到相似的则不更新？

      // hs_log($result, 'updateDbCommon.result', 'wpdb/Common.php');

      if ($result['id'] > 0) {
        $result['code'] = $result['statusCode'] = 0;
        $result['message'] = $this->post_type_label . ' Data Updated';
        $result['data'] = [
          'id' => $result['id']
        ];
      } else {
        $result['code'] = $result['statusCode'] = 1;
        // $result['message'] = 'Error occured, please try again';
        $result['message'] = 'Error occured, please try again';
      }
    } else {
      $result = $can;
    }

    $result['dbData'] = $dbData;
    // hs_ve($result);
    return $result;
  }

  public function canCreate($data)
  {
    return true;
  }
  public function canUpdate($data)
  {
    return true;
  }
  public function canDelete($data)
  {
    return true;
  }

  // 准备其他数据
  public function prepareOtherData($data = [], $update = 'update')
  {

    $oriData = $data;
    if ($update == 'update') {
      // $date = !empty($oriData['date']) && strtotime($oriData['date']) ? date('Y-m-d H:i:s', strtotime($oriData['date'])) : '';

      // $date = $oriData['date'] ? $oriData['date'] : '';
      // $data['date'] = $date;
      // $data = [
      //   'id' => $oriData['id'] ? $oriData['id'] : 0,
      //   'title' => $oriData['title'] ? $oriData['title'] : '',
      //   'theme' => $oriData['theme'] ? $oriData['theme'] : '',
      //   'host' => $oriData['host'] ? $oriData['host'] : '',
      //   'date' => $date,
      //   'recorder' => $oriData['recorder'] ? $oriData['recorder'] : '',
      //   'participants' => $oriData['participants'] ? $oriData['participants'] : '',
      //   'absentees' => $oriData['absentees'] ? $oriData['absentees'] : '',
      //   'absence_reason' => $oriData['absence_reason'] ? $oriData['absence_reason'] : '',
      //   'summary' => $oriData['summary'] ? $oriData['summary'] : '',
      // ];
    } else {
      
    }
    
    return $data;
  }
  // 准备原始数据
  public function prepareRawData($data, $settings)
  {
    $rawData = [];

    foreach ($settings as $key => $value) {
      if (isset($data[$key])) {
        $rawData[$key] = $data[$key];
      }
    }
    
    return $rawData;
  }
  // 准备数据
  public function prepareData($data, $settings)
  {
    $preparedData = [];

    foreach ($settings as $key => $value) {
      if (isset($data[$key])) {
        $preparedData[$key] = $data[$key];
      }
    }
    unset($preparedData['id']);

    return $preparedData;
  }
  // 保存数据
  //保存数据
  public function saveData($data = [], $update = false)
  {
    if (empty($data)) {
      return;
    }

    // 保存主体数据
    // $item_id = $this->setItem($data, '', $this->post_type, $update);
    // $result = $this->updateCommon($data, $hsMain->dataTypeSets(), $hsMain->table_name, $update);
    $args = [
      'dataTypeSets' => $this->dataTypeSets()
    ];
    $id = isset($data['ID']) ? $data['ID'] : (isset($data['id'])?$data['id']:0);
    $id = $id ? $id : 0;
    // 如果ID大于0，则查询是否存在
    // TODO: 这个查询好像没必要？
    // if ($id > 0) {
    //   $column = isset($data['ID']) ? 'ID' : 'id';
    //   $argsQuery = [
    //     'query' => [
    //       $column => $id,
    //     ],
    //     'limit' => 1,
    //     'column' => $column,
    //     // 'dataTypeSets' => $dataTypeSets
    //   ];

    //   $args = array_merge($args, $argsQuery);
    // }

    // hs_log($args, 'updateCommon.$args', 'wpdb/common.php');
    $id = $this->setItem($data, $args, $this->table_name, $update);

    $result['id'] = $id;
    if (!empty($id)) {
      // $result['status'] = $db_data['status'];
      $result['statusCode'] = 0;
      $result['code'] = 0;
    } else {
      $result['statusCode'] = 1;
      $result['code'] = 1;
    }

    $itemId = (int)$result['id'];

    return $itemId;
  }
  // 写入数据
  public function setItem($db_data, $args = [], $table_name = '', $update = false)
  {
    $dataTypeSets = isset($args['DataTypeSets']) ? $args['DataTypeSets'] : $args['dataTypeSets'];
    // var_export('<br>dataTypeSets:<br>');
    // var_export($dataTypeSets);
    unset($args['DataTypeSets'], $args['dataTypeSets']);


    $id = !empty($db_data['ID']) ? (int)$db_data['ID'] : (!empty($db_data['id']) ? (int)$db_data['id'] : 0);

    $data = $this->prepareData($db_data, $dataTypeSets, $table_name);
    // var_export('<br>prepareData:<br>');
    // var_export($data);

    //写入数据
    if (empty($id)) { //如果未找到数据
      $result = $this->msdb->insert($this->table_name,$data);
      if($result){
        $id = (int)$result;
      } else {
        $id = 0;
      }
    } else {
      $result = $this->msdb->update($this->table_name, $data, (int)$id);
      if($result){
        $id = (int)$result;
      } else {
        $id = 0;
      }
    }
    
    return $id;
  }


  // 检查数据是否可以操作

  public function can(array $data = [], string $can_do = 'update', $hsMain = null)
  {
    // if (empty($this->error)) {
    //   $this->error = new \Hs\Apps\Error;
    // }


    // 如果传入的是数组数据
    if (is_array($data)) {
      switch ($can_do) {
        case 'view':
          return $this->canUpdate($data);
          break;

        case 'update':
          return $this->canUpdate($data);
          break;

        case 'delete':
          return $this->canDelete($data);
          break;

        default:
          return $this->canCreate($data);
          break;
      }
    }


    return false;
  }

  /************************************其他设置********************************************* */

  // 数据库数据字段设定
  public function dataSettings($data_type = 'db')
  {

    $common = [
      'id' => fm_dt('id', 'ID', 'int'),
      'title' => fm_dt('title', '标题'),
    ];

    $db = [
      
    ];


    $list = [
    ];


    $detail = [];

    $es = [];

    $datatable = [
    ];

    // $common = array_merge($common, $this->getDataSettingsDef());


    $settings = hs_data_settings($data_type, $common, $db, $list, $detail, $es, $datatable);

    return $settings;
  }
  public function setDataSets($settings = [], $output = '')
  {

    if (empty($settings)) {
      return;
    }

    $settings_new = [];
    if (
      $output == ''
      || empty($output)
      || 'db' == $output
      || 'all' == $output
    ) {
      foreach ($settings as $name => $set) {
        $settings_new[$name] = $set['d_type'];
      }
      $this->dataTypeSets = $settings_new;
    } elseif ($output == 'table') {
      $settings_new = $settings;
      foreach ($settings_new as $name => $set) {
        unset($settings_new[$name]['d_type']);
      }
      unset(
        $settings_new['deleted'],
        $settings_new['deleted_date'],
        $settings_new['modified_date']
      );
    } elseif ($output == 'email') {
      $settings_new = $settings;
      foreach ($settings_new as $name => $set) {
        unset($settings_new[$name]['d_type']);
      }
      unset(
        $settings_new['id'],
        $settings_new['deleted'],
        $settings_new['deleted_date'],
        $settings_new['modified_date']
      );
    }

    return $settings_new;
  }
  //数据类型定义
  public function dataTypeSets($output = '', string $dataType = 'db')
  {
    // if ($this->dataTypeSets) {
    //   return $this->dataTypeSets;
    // }

    // if (empty($this->wpdb)) {
    //   $this->wpdb = new Wpdb\Common;
    // }

    // $this->dataTypeSets = $this->wpdb->setDataSets($this->dataSettings($dataType), $output);
    $this->dataTypeSets = $this->setDataSets($this->dataSettings($dataType), $output);
    return $this->dataTypeSets;
  }
  public function erroData($code = 0, $message = '', $data = null)
  {
    $message = !empty($message) ? $message : 'Data fetched ' . (($code > 0) ? 'failed' : 'successfully');
    // $data = !empty($data) ? $data : ''
    if (is_string($data)) {
      $data = $data;
    }

    // hs_log($data, 'erroData.$data', 'apps/error.php');
    return [
      'code' => $code,
      'message' => $message,
      'data' => is_null($data) ? [] : $data,
    ];
  }

  //初始化函数
  public function init()
  {
    
  }
}
