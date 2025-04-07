<?/*
 * @Description: 
 * @Author: pjw@hardsun
 * @Date: 2025-04-03 10:12:18
 * @LastEditTime: 2025-04-03 10:13:00
 * @LastEditors: pjw@hardsun
 * @FilePath: \Innovaoa\includes\src\Fmproject\Minutes\index.php
 * @Copyright: Copyright©2019-2025 HARDSUN TECH Ltd
 */

namespace Hs\Fmproject\Minutes;

/**
 * 会议记录处理类
 * 
 * 会议记录主要功能
 * 
 * @author pjw@hardsun
 * @date 2025-04-03
 */
class Index
{
  //数据库连接对象
  var $TableHandle;
  var $DetailHandle;
  //表名
  var $table_name;
  //构造函数单例模式
  private static $instance = null;
  private function __construct()
  {
    //引入数据库类
    $this->TableHandle = new Table;
    $this->DetailHandle = new Detail;
  }
  //单例模式获取实例
  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new Index();
    }
    return self::$instance;
  }

  public function getItems(
    $params = [],
    $from = 'db',
    $post_type = '',
    $output = 'all'
  ) {
    unset($params['sql']);
    $results = $this->TableHandle->getItems($params, $from, $post_type, $output);
    if (is_rest_ok($results)) {
      $resultData = empty($results['items']) ? [] : $results['items'];
      $results['items'] = [];
      foreach ($resultData as $key => $value) {
        $results['items'][] = $this->getItem($value['id']);
      }
    } else {
      $results['items'] = [];
    }
    return $results;
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

    $data = $this->TableHandle->getItem($itemId);
    if (empty($data)) {
      return $data;
    }
    $detailQuery = [
      'minutes_id' => $itemId,
      'per_page' => -1,
    ];
    $detailResults = $this->DetailHandle->getItems($detailQuery);
    if (is_rest_ok($detailResults)) {
      $data['details'] = $detailResults['items'];
    } else {
      $data['details'] = [];
    }
    return $data;
  }

  //创建会议记录
  public function create($data)
  {
    //初始化变量
    $detailResults = [];
  
    //先存主表数据
    $result = $this->TableHandle->create($data);
    if (is_rest_ok($result)) {
      $minuteId = $result['id'];
      //存明细数据
      if (isset($data['details']) && !empty($data['details'])) {
        foreach ($data['details'] as $key => $value) {
          if (empty($value) || !is_array($value)) {
            continue;
          }
          $value['minutes_id'] = $minuteId;
          $detailResult = $this->addDetail($value);
          if (is_rest_ok($detailResult)) {
            $detailResults[] = $detailResult;
          }
        }
        // 添加明细结果到主结果
        $result['details_added'] = count($detailResults);
        $result['detail_results'] = $detailResults;
      }
    }
    
    // 无论成功还是失败都返回结果
    return $result;
  }
  //更新会议记录
  public function update($data = [])
  {
    //更新主表数据
    $result = $this->TableHandle->update($data);
    return $result;
  }
  //删除会议记录
  public function delete($data = [])
  {
    $id = $data['id'];
    if (empty($id)) {
      return ['id'=>0 ,'code' => 1, 'message' => 'ID不能为空'];
    }
    //删除主表数据
    $result = $this->TableHandle->delete($data);
    if (is_rest_ok($result)) {
      //删除明细数据
      $detailQuery = [
        'minutes_id' => $id,
        'per_page' => -1,
      ];
      $detailResults = $this->DetailHandle->getItems($detailQuery);
      if ($detailResults['total'] > 0) {
        foreach ((array)$detailResults['items'] as $key => $value) {
          $this->DetailHandle->delete(['id' => $value['id']]);
        }
      }
    } else {
      return $result;
    }
    
    return $result;
  }
  //添加会议记录明细
  public function addDetail($data = [])
  {
    $result = $this->DetailHandle->create($data);
    return $result;
  }
  //更新会议记录明细
  public function updateDetail($data = [])
  {
    $result = $this->DetailHandle->update($data);
    return $result;
  }
  //删除会议记录明细
  public function deleteDetail($data = [])
  {
    $result = $this->DetailHandle->delete($data);
    return $result;
  }
  
}
