<?/*
/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:15:03
 * @LastEditTime: 2025-04-08 16:18:38
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\includes\src\Fmproject\Minutes\table.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

namespace Hs\Fmproject\Minutes;

/**
 * 会议记录处理类
 * 
 * 提供会议记录主表的增、删、改、查等功能
 * 
 * @author pjw@hardsun
 * @date 2025-04-03
 */
class Table extends \Hs\Data\Msdb\Common
{
  var $post_type = 'meeting_minutes';
  var $post_type_label = '会议记录';
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
    //初始化表名
    $this->table_name = 'FM_modal_meeting_minutes';
    // //引入明细表类
    // if (empty($this->detailHandle)) {
    //   $this->detailHandle = new Detail;
    // }
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
      if(!empty($params['search'])||!empty($params['s'])){ 
        $searchKey = !empty($params['s']) ? $params['s'] : $params['search'];
        $params['combinatorial'][] = [
          [
            'key' => 'title',
            'value' => "'%" . $searchKey . "%'",
            'compare' => 'like'
          ],
          [
            'key' => 'theme',
            'value' => "'%" . $searchKey . "%'",
            'compare' => 'like'
          ],
          [
            'key' => 'host',
            'value' => "'%" . $searchKey . "%'",
            'compare' => 'like'
          ],
          [
            'key' => 'recorder',
            'value' => "'%" . $searchKey . "%'",
            'compare' => 'like'
          ],
        ];
        unset($params['s']);
        unset($params['search']);
      }

      //时间范围查询



      $args['paged'] = $params['paged'];
      $args['posts_per_page'] = $params['per_page'];
      $args['query']['combinatorial'] = !empty($params['combinatorial']) && is_array($params['combinatorial']) ? $params['combinatorial'] : [];
      // $args['date_query'] = $params['date_range'];
      $args['orderby'] = $params['orderby'];
      $args['order'] = $params['order'];
      $paged = $args['paged'];
      $per_page = $args['posts_per_page'];
      $sql = fm_prepare_sql($args, $this->dataTypeSets(), $this->table_name);
      // hs_ve($sql);
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
      // hs_ve($sql);

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
      $items = $items ?? [];
      $total = count($items);
    }
    // hs_ve($total);
    // hs_ve($per_page);
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

    foreach (['date', 'created_date', 'modified_date', 'deleted_date'] as $key) {
      if (!empty($data[$key])) {
        $data[$key] = date('Y-m-d H:i:s', strtotime($data[$key]));
      }
    }
    if (!empty($data['date'])) {
      $data['date'] = date('Y-m-d', strtotime($data['date']));
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

    unset($data['deleted']);
    unset($data['deleted_date']);

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
    if (empty($data['title'])) return $this->erroData(1002, '会议标题不能为空');
    //时间格式验证
    if (empty($data['date']) || !strtotime($data['date'])) {
      return $this->erroData(1003, '会议时间格式不正确');
    } else {
      //如果时间戳小于2000年1月1日，则返回错误
      if (strtotime($data['date']) < strtotime('2000-01-01')) {
        return $this->erroData(1004, '会议时间不正确');
      }
      //如果时间格式不为YYYY-MM-DD HH:MM:SS，则返回错误
      if (date('Y-m-d H:i:s', strtotime($data['date'])) != $data['date']) {
        return $this->erroData(1005, '会议时间格式不正确');
      }
    }
    return true;
  }
  public function canUpdate($data)
  {
    if (empty($data)) return $this->erroData(1001, '数据不能为空');
    if(!empty($data['deleted']) && $data['deleted'] == 2) return $this->canDelete($data);
    if (empty($data['id'])) return $this->erroData(1002, 'ID不能为空');
    if (empty($data['title'])) return $this->erroData(1002, '会议标题不能为空');
    //时间格式验证
    if (empty($data['date']) || !strtotime($data['date'])) {
      return $this->erroData(1003, '会议时间格式不正确');
    } else {
      //如果时间戳小于2000年1月1日，则返回错误
      if (strtotime($data['date']) < strtotime('2000-01-01')) {
        return $this->erroData(1004, '会议时间不正确');
      }
      //如果时间格式不为YYYY-MM-DD HH:MM:SS，则返回错误
      if (date('Y-m-d H:i:s', strtotime($data['date'])) != $data['date']) {
        return $this->erroData(1005, '会议时间格式不正确');
      }
    }
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
      if (!empty($oriData['deleted']) && $oriData['deleted'] == 2) {
        $data = [
          'id' => !empty($oriData['id']) ? (int)$oriData['id'] : 0,
          'deleted' => 2,
          'deleted_date' => date('Y-m-d H:i:s'),
        ];
      } else {
        $oriItem = $this->getItem($oriData['id']);
        $date = $oriData['date'] ? $oriData['date'] : $oriItem['date'];
        if (!empty($oriData['date']) && strtotime($oriData['date'])) {
          $date = date('Y-m-d H:i:s', strtotime($oriData['date']));
        } else {
          $date = date('Y-m-d H:i:s', strtotime($oriItem['date']));
        }
        $data = [
          'id' => $oriData['id'] ? $oriData['id'] : 0,
          'title' => !empty($oriData['title']) ? $oriData['title'] : $oriItem['title'],
          'theme' => !empty($oriData['theme']) ? $oriData['theme'] : $oriItem['theme'],
          'host' => !empty($oriData['host']) ? $oriData['host'] : $oriItem['host'],
          'date' => $date,
          'recorder' => !empty($oriData['recorder']) ? $oriData['recorder'] : $oriItem['recorder'],
          'participants' => !empty($oriData['participants']) ? $oriData['participants'] : $oriItem['participants'],
          'absentees' => !empty($oriData['absentees']) ? $oriData['absentees'] : $oriItem['absentees'],
          'absence_reason' => !empty($oriData['absence_reason']) ? $oriData['absence_reason'] : $oriItem['absence_reason'],
          'summary' => !empty($oriData['summary']) ? $oriData['summary'] : $oriItem['summary'],
          'modified_date' => date('Y-m-d H:i:s'),
        ];
      }
    } else {
      $date = !empty($oriData['date']) && strtotime($oriData['date']) ? date('Y-m-d H:i:s', strtotime($oriData['date'])) : '';

      $data = [
        'title' => !empty($oriData['title']) ? $oriData['title'] : '',
        'theme' => !empty($oriData['theme']) ? $oriData['theme'] : '',
        'host' => !empty($oriData['host']) ? $oriData['host'] : '',
        'date' => $date,
        'recorder' => !empty($oriData['recorder']) ? $oriData['recorder'] : '',
        'participants' => !empty($oriData['participants']) ? $oriData['participants'] : '',
        'absentees' => !empty($oriData['absentees']) ? $oriData['absentees'] : '',
        'absence_reason' => !empty($oriData['absence_reason']) ? $oriData['absence_reason'] : '',
        'summary' => !empty($oriData['summary']) ? $oriData['summary'] : '',
      ];
    }
    return $data;
  }




  /************************************其他设置********************************************* */

  // 数据库数据字段设定
  public function dataSettings($data_type = 'db')
  {

    $common = [
      'id' => fm_dt('id', 'ID', 'int'),
      'title' => fm_dt('title', '会议标题'),
    ];

    $db = [
      'theme' => fm_dt('theme', '会议主题'),
      'host' => fm_dt('host', '会议主持人'),
      'date' => fm_dt('date', '会议时间'),
      'recorder' => fm_dt('recorder', '会议记录人'),
      'participants' => fm_dt('participants', '参会人员'),
      'absentees' => fm_dt('absentees', '缺席人员'),
      'absence_reason' => fm_dt('absence_reason', '缺席原因'),
      'summary' => fm_dt('summary', '会议总结'),
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
    if (!$this->msdb->tableExists($this->table_name)) {
      //sqlserver创建表
      $sql = "CREATE TABLE " . $this->table_name . " (
        id BIGINT PRIMARY KEY IDENTITY(1,1),
        title NVARCHAR(255) NOT NULL DEFAULT '',
        theme NVARCHAR(255) NOT NULL DEFAULT '',
        host NVARCHAR(255) NOT NULL DEFAULT '',
        date DATETIME NOT NULL DEFAULT GETDATE(),
        recorder NVARCHAR(255) NOT NULL DEFAULT '',
        participants NVARCHAR(MAX) NOT NULL DEFAULT '',
        absentees NVARCHAR(MAX) NOT NULL DEFAULT '',
        absence_reason NVARCHAR(MAX) NOT NULL DEFAULT '',
        summary NVARCHAR(MAX) NOT NULL DEFAULT '',
        
        deleted TINYINT DEFAULT 1,
        deleted_date DATETIME DEFAULT NULL,
        created_date DATETIME DEFAULT GETDATE(),
        modified_date DATETIME DEFAULT GETDATE()
      )";
      //执行sql语句
      $this->msdb->query($sql);
    }
    // if ($this->msdb->tableExists($this->table_name)) {
    //   if (empty($this->detailHandle)) {
    //     $this->detailHandle = new Detail;
    //   }
    //   $this->detailHandle->init();
    // }
  }
}
