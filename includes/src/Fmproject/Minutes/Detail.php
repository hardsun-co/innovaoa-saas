<?/*
/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:15:03
 * @LastEditTime: 2025-04-07 13:57:45
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\includes\src\Fmproject\Minutes\detail.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

namespace Hs\Fmproject\Minutes;

/**
 * 会议记录处理类
 * 
 * 提供会议记录明细表的增、删、改、查等功能
 * 
 * @author pjw@hardsun
 * @date 2025-04-03
 */
class Detail extends \Hs\Data\Msdb\Common
{
  //数据库连接对象
  var $msdb;
  //表名
  var $table_name;
  var $main_table_name;
  //构造函数
  public function __construct()
  {
    //引入数据库类
    $this->msdb = \Hs\Msdb\Index::getInstance();
    //初始化表名
    $this->table_name = 'FM_model_meeting_minutes_detail';
    //引入主表名
    $this->main_table_name = 'FM_modal_meeting_minutes';
    //初始化
    $this->init();
  }
  public function getItems(
    $params = [],
    $from = 'db',
    $post_type = '',
    $output = 'all'
  ) {
    // global $wpdb;


    // $args = [];
    if (!is_string($params)) {
      // 默认参数
      $defaults = [
        'paged' => 1,
        'per_page' => 10,
        'deleted' => 1,
        'orderby' => 'id',
        'order' => 'DESC',
        'filters' => [],
        'search' => '',
        'date_range' => []
      ];

      $params = array_merge($defaults, $params);

      $column = $this->dataTypeSets();
      foreach ($column as $key => $value) {
        // //hs_ve($key);
        if (!empty($params[$key])) {
          $args['query'][$key] = $params[$key];
        }
      }

      $args['paged'] = $params['paged'];
      $args['posts_per_page'] = $params['per_page'];
      $args['combinatorial'] = !empty($params['combinatorial']) && is_array($params['combinatorial']) ? $params['combinatorial'] : [];
      // $args['date_query'] = $params['date_range'];
      $args['orderby'] = $params['orderby'];
      $args['order'] = $params['order'];
      $paged = $args['paged'];
      $per_page = $args['posts_per_page'];
      $sql = fm_prepare_sql($args, $this->dataTypeSets(), $this->table_name);

      $items = $this->msdb->getResults($sql);
      // var_export($items);
      if (false === $items) {
        $items = [];
        return [
          'total' => 0,
          'page' => $paged,
          'per_page' => $per_page,
          'max_pages' => 0,
          'items' => $items,
          'code' => 1,
          'message' => '数据库错误'
        ];
      }

      $sql = fm_get_sql_total($args, $this->dataTypeSets(), $this->table_name);

      $total = $this->msdb->getValue($sql);
    } else {
      $args['sql'] = $params;
      $paged = 1;
      $per_page = 1;
      $sql = fm_prepare_sql($args, $this->dataTypeSets(), $this->table_name);

      $items = $this->msdb->getResults($sql);
      if (false === $items) {
        $items = [];
        return [
          'total' => 0,
          'page' => $paged,
          'per_page' => $per_page,
          'max_pages' => 0,
          'items' => $items,
          'code' => 1,
          'message' => '数据库错误'
        ];
      }
      $items = !empty($items) && is_array($items) ?? [];
      $total = count($items);
    }
    return [
      'total' => !empty($total) ? (int)$total : 0,
      'page' => $per_page == -1 ? 1 : $paged,
      'per_page' => $per_page == -1 ? $total : $per_page,
      'max_pages' => $per_page == -1 ? 1 : ceil($total / $per_page),
      'items' => $items,
      'code' => 0,
      'message' => '数据获取成功'
    ];
  }

  // 获取单个数据
  public function getItem(
    $itemId = 0,
    $output = 'all',
    $column = '*',
    $item = null,
    $from = 'db'
  ) {

    if (empty($itemId)) {
      return;
    }

    $data = [];
    if (empty($item) || (!is_object($item) && !is_array($item))) {

      $id = $itemId;
      // $item = $this->wpdb->GetItem($itemId, $column, $this->table_name);
    }

    // 根据需要转化成对象或数组
    if (!is_array($item)) {
      $item = (array)$item;
    }
    if (empty($itemId)) {
      $itemId = $item['id']; //找出询盘id
    }

    $table_name = $this->table_name;
    $item = $this->msdb->getRow("SELECT $column FROM $table_name WHERE id = ?", [$itemId]);
    if($item['deleted'] == 2){
      return [];
    }
    // $item = $wpdb->get_row(
    //   $wpdb->prepare("SELECT $column FROM $table_name WHERE id = %d", $id)
    // );

    // ===== 公共数据 ======

    $common = $item;
    // hs_log($item,'getItemWpdb.item','Content\Common.php');
    $list = [];
    $detail = [];
    $datatable = [];
    $db = [];
    $es = [];

    //其他的数据，不一定用于前端
    $other = [];
    $data = $common;

    foreach (['created_date', 'modified_date', 'deleted_date'] as $key) {
      if (!empty($data[$key])) {
        $data[$key] = date('Y-m-d H:i:s', strtotime($data[$key]));
      }
    }
    // 下面可以自定义其他数据
    // ....
    $list = [];

    if (!empty($list)) {
      $detail = [];
      $datatable = [];
      $db = [];
      $es = [];
      $other = [];

      $data = hs_data_output(
        $output,
        $common,
        $list,
        $detail,
        $datatable,
        $other,
        $db,
        $es
      );
    }

    // unset($data['s']);

    // hs_log($data, 'getItem.$data', 'keywords/Index.php');

    // $this->data = $data;
    return $data;
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

  public function delete($data = [])
  {
    $data['deleted'] = 2;
    $result = $this->update($data, true);
    if (is_rest_ok($result)) {
      $result['id'] = $data['id'];
      $result['code'] = 0;
      $result['message'] = $this->post_type_label . ' Data Deleted';
    } else {
      $result['id'] = $data['id'];
      $result['code'] = 1;
      $result['message'] = $result['message'];
    }
    return $result;
  }

  public function canCreate($data)
  {
    if (empty($data)) return $this->erroData(1001, '数据不能为空');
    if (empty($data['minutes_id'])) return $this->erroData(1002, '会议记录ID不能为空');
    //外键检测
    $minutesTable = new Table();
    $minutesItem = $minutesTable->getItem($data['minutes_id']);
    if (empty($minutesItem)) return $this->erroData(1002, '会议记录ID不存在');

    return true;
  }
  public function canUpdate($data)
  {
    if (empty($data)) return $this->erroData(1001, '数据不能为空');
    if(!empty($data['deleted']) && $data['deleted'] == 2) return $this->canDelete($data);
    if (empty($data['id'])) return $this->erroData(1002, 'ID不能为空');
    if (empty($data['minutes_id'])) return $this->erroData(1002, '会议记录ID不能为空');
    //外键检测
    $minutesTable = new Table();
    $minutesItem = $minutesTable->getItem($data['minutes_id']);
    if (empty($minutesItem)) return $this->erroData(1002, '会议记录ID不存在');
    return true;
  }
  public function canDelete($data)
  {
    if (empty($data['id'])) return $this->erroData(1002, 'ID不能为空');
    return true;
  }

  // 准备其他数据
  public function prepareOtherData($data = [], $update = 'update')
  {
    $oriData = $data;
    if ($update == 'update') {
      if(!empty($oriData['deleted']) && $oriData['deleted'] == 2){
        $data = [
          'id' => !empty($oriData['id']) ? (int)$oriData['id'] : 0,
          'deleted' => 2,
          'deleted_date' => date('Y-m-d H:i:s'),
        ];
      }else{
        $oriItem = $this->getItem($oriData['id']);
        $data = [
          'id' => !empty($oriData['id']) ? (int)$oriData['id'] : 0,
          'minutes_id' => !empty($oriData['minutes_id']) ? (int)$oriData['minutes_id'] : $oriItem['minutes_id'],
          'department' => !empty($oriData['department']) ? $oriData['department'] : $oriItem['department'],
          'responsible_person' => !empty($oriData['responsible_person']) ? $oriData['responsible_person'] : $oriItem['responsible_person'],
          // 'date' => $date,
          'meeting_content' => !empty($oriData['meeting_content']) ? $oriData['meeting_content'] : $oriItem['meeting_content'],
          'solution' => !empty($oriData['solution']) ? $oriData['solution'] : $oriItem['solution'],
          'planned_completion_time' => !empty($oriData['planned_completion_time']) ? $oriData['planned_completion_time'] : $oriItem['planned_completion_time'],
          'cooperating_department' => !empty($oriData['cooperating_department']) ? $oriData['cooperating_department'] : $oriItem['cooperating_department'],
          'others' => !empty($oriData['others']) ? $oriData['others'] : $oriItem['others'],
          'modified_date' => date('Y-m-d H:i:s'),
        ];
      }
    } else {
      // $date = !empty($oriData['date']) && strtotime($oriData['date']) ? date('Y-m-d H:i:s', strtotime($oriData['date'])) : '';

      $data = [
        'minutes_id' => !empty($oriData['minutes_id']) && is_numeric($oriData['minutes_id']) && $oriData['minutes_id'] > 0 ? $oriData['minutes_id'] : 0,
        'department' => !empty($oriData['department']) ? $oriData['department'] : '',
        'responsible_person' => !empty($oriData['responsible_person']) ? $oriData['responsible_person'] : '',
        // 'date' => $date,
        'meeting_content' => !empty($oriData['meeting_content']) ? $oriData['meeting_content'] : '',
        'solution' => !empty($oriData['solution']) ? $oriData['solution'] : '',
        'planned_completion_time' => !empty($oriData['planned_completion_time']) ? $oriData['planned_completion_time'] : '',
        'cooperating_department' => !empty($oriData['cooperating_department']) ? $oriData['cooperating_department'] : '',
        'others' => !empty($oriData['others']) ? $oriData['others'] : '',
      ];
    }
    return $data;
  }


  // 数据库数据字段设定
  public function dataSettings($data_type = 'db')
  {

    $common = [
      'id' => fm_dt('id', 'ID', 'int'),
      'minutes_id' => fm_dt('minutes_id', '会议记录ID', 'int'),
      'department' => fm_dt('department', '责任部门'),
      'responsible_person' => fm_dt('responsible_person', '责任人'),
      'meeting_content' => fm_dt('meeting_content', '会议内容'),
      'solution' => fm_dt('solution', '解决办法'),
      'planned_completion_time' => fm_dt('planned_completion_time', '计划完成时间'),
      'cooperating_department' => fm_dt('cooperating_department', '配合解决问题部门'),
      'others' => fm_dt('others', '其他'),
    ];

    $db = [

      // 's' => fm_dt('s', '关键字'),

      'deleted' => fm_dt('deleted', '已删除', 'int'),
      'deleted_date' => fm_dt('deleted_date', '删除时间'),
      'created_date' => fm_dt('created_date', '访问时间'),
      'modified_date' => fm_dt('modified_date', '更新时间'),

    ];


    $list = [
      // 'url' => fm_dt('url', '页面链接'),
      // 'url_html' => fm_dt('url_html', '页面链接'),
      // 'country' => fm_dt('country', '国家'),
      // 'action_label' => fm_dt('action_label', '操作名'),
      // 'user_agent_info' => fm_dt('user_agent_info', '访客设备信息'),
      // 'area' => fm_dt('area', '地区'),
    ];


    $detail = [];

    $es = [];

    $datatable = [
      // 'location' => fm_dt('location', '位置'),
      // 'item_type_label' => fm_dt('item_type_label', '询盘类型')
      // 'url_full' => fm_dt('url_full', '链接'),
    ];

    // $common = array_merge($common, $this->getDataSettingsDef());


    $settings = hs_data_settings($data_type, $common, $db, $list, $detail, $es, $datatable);

    // $settings = apply_filters('hs_tracking_data_settings', $settings, $data_type);
    // hs_log($settings, 'dataSettings.settings', 'record/Index.php');

    return $settings;
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

  public function dbFieldExist($name = '')
  {
    if(empty($name)||!is_string($name)) return false;
    $sql = "SELECT COUNT(*) FROM sys.columns WHERE object_id = OBJECT_ID('$this->table_name') AND name = '$name'";
    $result = $this->msdb->getValue($sql);
    if ($result > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function addCol()
  {
    // if(!$this->dbFieldExist('title')) {
    //   $sql = "ALTER TABLE $this->table_name ADD title NVARCHAR(255) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('theme')) {
    //   $sql = "ALTER TABLE $this->table_name ADD theme NVARCHAR(255) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('host')) {
    //   $sql = "ALTER TABLE $this->table_name ADD host NVARCHAR(255) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('date')) {
    //   $sql = "ALTER TABLE $this->table_name ADD date DATETIME NOT NULL DEFAULT GETDATE()";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('recorder')) {
    //   $sql = "ALTER TABLE $this->table_name ADD recorder NVARCHAR(255) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('participants')) {
    //   $sql = "ALTER TABLE $this->table_name ADD participants NVARCHAR(MAX) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('absentees')) {
    //   $sql = "ALTER TABLE $this->table_name ADD absentees NVARCHAR(MAX) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('absence_reason')) {
    //   $sql = "ALTER TABLE $this->table_name ADD absence_reason NVARCHAR(MAX) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
    // if(!$this->dbFieldExist('summary')) {
    //   $sql = "ALTER TABLE $this->table_name ADD summary NVARCHAR(MAX) NOT NULL DEFAULT ''";
    //   $this->msdb->query($sql);
    // }
  }

  //初始化函数
  public function init()
  {
    //创建msdb数据库表
    //检查表是否存在
    if (!$this->msdb->tableExists($this->table_name) && $this->msdb->tableExists($this->main_table_name)) {
      //sqlserver创建表
      $sql = "CREATE TABLE " . $this->table_name . " (
        id BIGINT PRIMARY KEY IDENTITY(1,1),
        minutes_id BIGINT NOT NULL,
        department NVARCHAR(255) NOT NULL DEFAULT '',
        responsible_person NVARCHAR(255) NOT NULL DEFAULT '',
        meeting_content NVARCHAR(MAX) NOT NULL DEFAULT '',
        solution NVARCHAR(MAX) NOT NULL DEFAULT '',
        planned_completion_time NVARCHAR(MAX) NOT NULL DEFAULT '',
        cooperating_department NVARCHAR(255) NOT NULL DEFAULT '',
        others NVARCHAR(MAX) NOT NULL DEFAULT '',

        deleted INT DEFAULT 1,
        deleted_date DATETIME DEFAULT NULL,
        created_date DATETIME DEFAULT GETDATE(),
        modified_date DATETIME DEFAULT GETDATE(),
        CONSTRAINT FK_Minutes FOREIGN KEY (minutes_id) REFERENCES FM_modal_meeting_minutes (id) ON DELETE CASCADE ON UPDATE CASCADE
      )";
      //执行sql语句
      $this->msdb->query($sql);
    }
  }
}
