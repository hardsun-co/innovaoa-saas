<?php


/**
 * debug 函数
 */
if (!function_exists('hs_ve')) {
  function hs_ve($value = '')
  {
    // var_export(__DIR__);
    if ('' == ($value) || is_null($value)) {
      return false;
    }

    // 线上环境
    if (
      IS_REMOTE // 不是本地
      && empty($_GET['hs_ve']) // 并且不包含参数
    ) {
      return false;
    }

    // 判断当前 url 是否是 api 地址
    // if (is_hs_api_url()) {
    //   return false;
    // }

    if (is_array($value)) {
      $isRecursive = false;
      $dump = print_r($value, true);
      if (strpos($dump, '*RECURSION*') !== false) {
        $isRecursive = true;
      }
      if ($isRecursive) return null;
    }

    if (
      (IS_REMOTE && isset($_GET['hs_ve'])
        && $_GET['hs_ve'] == 1
      )
      || !IS_REMOTE
    ) {
      //带输出位置信息
      if (isset($_GET['hs_ve_trace']) && $_GET['hs_ve_trace'] == 1) {
        $trace = debug_backtrace();
        $trace = is_array($trace) && !empty($trace) ? $trace : [];
        $currentTrace = $trace[0];
        $rootPath = substr(ROOT_PATH, 0, -1);
        $currentFile = str_replace($rootPath, 'saas', $currentTrace['file']);

        $locate = '文件' . $currentFile . '的第' . $currentTrace['line'] . '行';

        var_export($locate);
        var_export('<br>' . PHP_EOL);
        var_export($value);
      } else {
        var_export($value);
      }
    }
  }
}
if (!function_exists('is_rest_ok')) {
  function is_rest_ok($response = NULL)
  {

    // 如果是数组，且为空数组，则为false
    if (is_array($response) && empty($response)) {
      return false;
      // } else {
      // 	if (!empty($response['code']) || !empty($response['statusCode'])) return false;
    }

    // hs_log($response, 'is_rest_ok.response', 'hs-common-functions.php');
    if (is_numeric($response) && 0 === (int)$response) {
      return true;
    }

    if (isset($response) && is_bool($response) === true) {
      // hs_log($response, 'is_rest_ok.response-2', 'hs-common-functions.php');

      return empty($response) ? false : true;
    }

    // 如果是字符或数字
    if (is_string($response) && 1 == $response) {
      return true;
    }
    if (is_string($response) && 0 == $response) {
      return false;
    }

    // 针对 aws
    if (
      is_string($response)
      && (str_contains($response, 'Error')
        || str_contains($response, 'NoSuchKey')
      )
    ) {
      return false;
    }

    $code = $response;
    if (is_array($response)) {
      $code = isset($response['statusCode']) ? $response['statusCode'] : null;

      $code = (!empty($code) || 0 === $code) ? $code : (isset($response['errcode']) ? $response['errcode'] : null); // 微信接口

      $code = (!empty($code) || 0 === $code) ? $code : (isset($response['rescode']) ? $response['rescode'] : null); // mpesa 接口

      $code = (!empty($code) || 0 === $code) ? $code : (isset($response['result_code']) ? $response['result_code'] : null); // startnet 接口

      if (empty($code) && $code !== 0) {
        $code = (isset($response['code']) ? $response['code'] : null);
      }
    }


    if (
      (int)$code === 0
      // || $code == 0
      || ($code >= 200 && $code < 400)
      || (int)$code == 2
      || $code === true
    ) {
      return true;
    }

    return false;
  }
}



// 准备 sql 用参数
function fm_prepare_sql($args = [], $data_type_sets = [], $table_name = '')
{

  if (!empty($args['sql']))
    return $args['sql'];


  if (empty($data_type_sets) && $args['DataTypeSets']) {
    $data_type_sets = $args['DataTypeSets'];
    unset($args['DataTypeSets']);
  }

  $orderby_column = 'id';
  $order = "DESC";
  $limit = 10;

  $paged = $args['paged'] ? (int)$args['paged'] : 1;
  $posts_per_page = $args['posts_per_page'] ? (int)$args['posts_per_page'] : $limit;

  if (isset($args['column'])) {
    $column = $args['column'];
  }
  if (empty($column)) {
    $column = '*';
  }


  $sql = "SELECT $column FROM $table_name ";

  // hs_log($sql, 'hs_prepare_sql.sql', 'hs-common-functions.php');
  // hs_log($args, 'hs_prepare_sql.args', 'hs-common-functions.php');

  if (!empty($args)) {


    $args_ori = $args; //先存起来

    $args_query = !empty($args['query']) ? $args['query'] : [];
    // if (!array_key_exists('column', $args)) { //如果传进来的 $args 参数里面没有 column 这个参数，则整个直接当作 query 的数据
    // 	$args_query = $args_ori;
    // }

    $relation = 'AND';

    if (!empty($args_query['relation'])) {
      $relation = $args_query['relation'];
      unset($args_query['relation']);
    }

    // var_export($args_query);
    // hs_ve($data_type_sets);
    $where = '';
    // var_export($args_query);
    if (!empty($args_query)) {

      foreach ($args_query as $key => $value_ori) {

        // var_export($key);
        if (array_key_exists($key, $data_type_sets)) {
          // var_export($key);
          $compare = "=";

          // 数组的情况，获取相应的值和 compare 符
          if (is_array($value_ori)) {
            $compare = $value_ori['compare'];
            $value = $value_ori['value'];
          } elseif ('s' === $key) {
            // 关键词
            $compare = 'LIKE';
            $value = '%' . $value_ori . '%';
          } else {
            $value = $value_ori;
          }

          // hs_log($args_query, 'hs_prepare_sql.args_query', 'hs-common-functions.php');
          // hs_ve($compare);

          // IN 的情况
          if ('IN' === $compare || 'NOT IN' === $compare) {
            $value = $value_ori['value'];
            // $value = '';
            if (is_array($value_ori['value'])) {
              $value = implode("', '", $value_ori['value']);
            }

            $where .= " $key $compare ('" . $value . "') $relation ";
          } // bewteen 的情况
          else if ('between' === $compare || 'BEWTEEN' === $compare || 'BETWEEN' === $compare || 'bewteen' === $compare) {
            $value = $value_ori['value'];
            // $value = '';
            $where .= " $key $compare '" . $value[0] . "' AND '" . $value[1] . "' $relation ";
          }
          // 复杂查询条件 的情况
          else if ('complex' === strtolower($compare)) {
            $value = $value_ori['value'];
            $where .= "" . $value . " $relation ";
          } else {
            $where .= "$key $compare $value $relation ";
          }
        } elseif ('date_query' == $key) {
        }
      }
      // hs_ve($where);



      // or组合
      $args_combinatorial = !empty($args_query['combinatorial']) ? $args_query['combinatorial'] : false;

      // hs_ve($args_combinatorial);
      if (!empty($args_combinatorial)) {
        $value = '';
        $where_part = '';
        foreach ($args_combinatorial as $key1 => $value_part) {
          if (!empty($value_part)) {
            $whereThings = "";
            foreach ($value_part as $key => $value_ori) {
              // hs_ve($value_ori);
              $key = $value_ori['key'];
              if (array_key_exists($key, $data_type_sets)) {

                $compare = "=";

                // 数组的情况，获取相应的值和 compare 符
                if (is_array($value_ori)) {
                  $compare = $value_ori['compare'];
                  $value = $value_ori['value'];
                } elseif ('s' === $key) {
                  // 关键词
                  $compare = 'LIKE';
                  $value = '%' . $value_ori . '%';
                } else {
                  $value = $value_ori;
                }

                // hs_log($args_combinatorial, 'hs_prepare_sql.args_combinatorial', 'hs-common-functions.php');


                // IN 的情况
                if ('IN' === $compare) {
                  $value = $value_ori['value'];
                  // $value = '';
                  if (is_array($value_ori['value'])) {
                    $value = implode("', '", $value_ori['value']);
                  }

                  $whereThings .= " $key $compare ('" . $value . "') OR ";
                } // bewteen 的情况
                else if ('between' === $compare || 'BEWTEEN' === $compare || 'BETWEEN' === $compare || 'bewteen' === $compare) {
                  $value = $value_ori['value'];
                  // $value = '';
                  $whereThings .= " $key $compare '" . $value[0] . "' AND '" . $value[1] . "' OR ";
                }
                // 复杂查询条件 的情况
                else if ('complex' === strtolower($compare)) {
                  $value = $value_ori['value'];
                  $whereThings .= "" . $value . " OR ";
                } else {
                  $whereThings .= "$key $compare $value OR ";
                }
              } elseif ('date_query' == $key) {
              }
              // hs_ve($where_part);
            }
            // hs_ve($whereThings);
            $whereThings = rtrim($whereThings, " OR ");
            // hs_ve($whereThings);
            if (!empty($whereThings)) $whereThings = "(" . $whereThings . ") AND ";
            else $whereThings = '';
          }
          $where_part .= $whereThings;
        }
        $where .= $where_part;
      }
      // hs_ve($where);
      $where = rtrim($where, " AND ");
    }

    // $where = '';
    $sql .= $where ? 'WHERE ' . $where : $where;




    if (!empty($args['order'])) {
      $order = $args['order'];
    }

    if (!empty($args['orderby'])) {
      $orderby = $args['orderby'];
      $orderby_column = $orderby;
      if (
        str_contains($orderby, 'ASC')
        || str_contains($orderby, 'DESC')
        || str_contains($orderby, 'asc')
        || str_contains($orderby, 'desc')
        || str_contains($orderby, ',')
      ) {
        $order = '';
      }
    }

    if (!empty($args['limit']) && $args['limit'] == "-1") {
      $limit = '';
    } else if (!empty($posts_per_page) && $posts_per_page == "-1") {
      $limit = '';
    } elseif (!empty($args['limit']) && $args['limit'] > 0) {
      $limit = (int)$args['limit'];
    } else {
      $limit = $posts_per_page;
    }

    if (!empty($args['offset'])) {
      $offset = (int)$args['offset'];
    } else {

      $offset = ($paged - 1) * $posts_per_page;
      if ($offset == 0) {
        $offset = '';
      }
    }

    // hs_log(var_export($args, true));


  }

  if ($column !== "COUNT(*)") {
    if ($limit) {
      // 处理offset可能为空的情况
      $offsetValue = (!empty($offset) || $offset === 0) ? (int)$offset : 0;

      $sql = "SELECT * FROM (
              SELECT *, ROW_NUMBER() OVER (ORDER BY $orderby_column $order) AS row_num 
              FROM ($sql) AS sub_query
          ) AS numbered_query 
          WHERE row_num > $offsetValue " . ($limit ? "AND row_num <= " . ($offsetValue + $limit) : "");
    } else {
      $sql .= " ORDER BY $orderby_column $order";
    }
  }



  // hs_log($sql, 'hs_prepare_sql.$sql', 'hs-common-functions.php');

  // hs_ve($sql);
  // echo "\n";
  return $sql;
  // return "SELECT * FROM hsce3eduxw_auction_sessions WHERE status = 'pending' AND ( ( YEAR( hsce3eduxw_auction_sessions.start_date ) = 2021 AND MONTH( hsce3eduxw_auction_sessions.start_date ) = 7 AND DAYOFMONTH( hsce3eduxw_auction_sessions.start_date ) = 16 ) ) ORDER BY id ASC  LIMIT 20 ";
}
function fm_get_sql_total($args = [], $data_type_sets = [], $table_name = '')
{
  $args['column'] = 'COUNT(*)';
  $sql = fm_prepare_sql($args, $data_type_sets, $table_name);
  return $sql;
}
/***********************************数据库************************************* */
function hs_data_output(
  string $output = 'all',
  array $common = [],
  array $list = [],
  array $detail = [],
  array $datatable = [],
  array $other = [],
  array $db = [],
  array $es = []
): array {


  // 输出相应的数据
  switch ($output) {
    case 'all': //所有数据
    case 'es': // ES 数据用

      $data = array_merge($common, $list, $detail, $datatable, $db, $other);

      break;

    case 'list': //列表页数据

      $data = array_merge($common, $db, $list);

      break;


    case 'detail': // 详情页数据

      $data = array_merge($common, $list, $datatable, $detail);

      break;


    case 'datatable': // datatable 数据

      $data = array_merge($common, $list, $datatable, $db, $other);

      break;

    case 'other': // 详情页数据

      $data = array_merge($common, $other);

      break;

    default: // 公共数据

      $data = $common;

      break;
  }
  // }



  return $data;
}

/**
 * 数据类型设置
 */
function fm_dt($name, $label = '', $type = 'string', $args = null)
{
  // global $lang;

  // 如果 $args 是数组，则是设置参数
  if (is_array($args)) {
    // global $lang;
    $lang = !empty($args['lang']) ? $args['lang'] : '';
  } else {
    $lang = $args;
  }
  $lang = !empty($lang) ? $lang : 'cn';
  $label = !empty($label) ? $label : $name;
  // if ('cn' == $lang) {
  //   $label = hs_lang($label);
  // }

  if (empty($type)) {
    $type = 'string';
  }

  switch ($type) {
    case 'int':
    case 'integer':
      $type = 'integer';
      $d_type = '%d';
      $sanitize_callback = 'absint';

      break;


    case 'editor':
    case 'array':
      $d_type = '%s';
      // $sanitize_callback = '';
      break;

    case 'email':
      $d_type = '%s';
      // $sanitize_callback = function ($value) {
      //   return sanitize_email($value);
      // };
      break;


    case 'textarea':

      $fieldType = $type;
      $type = 'string';

      $d_type = '%s';

      // $sanitize_callback = function ($value) {
      //   return sanitize_textarea_field($value);
      // };

      break;

    default:
      $d_type = '%s';

      // $sanitize_callback = function ($value) {
      //   return sanitize_text_field($value);
      // };

      break;
  }

  $data = [
    'name' => $name,
    'label' => $label,
    'd_type' => $d_type,
    'type' => $type,
    // 'fieldType' => $fieldType ? $fieldType : '',
    'sanitize_callback'  => isset($sanitize_callback) ? $sanitize_callback : '',
    // 'readonly'     => true,
  ];

  if (!empty($fieldType)) {
    $data['fieldType'] = $fieldType;
  }

  // 如果 $args 里面有 db 数组，则加入 'db' 键
  if (is_array($args)) {
    if (isset($args['db'])) {
      $data['db'] = $args['db'];
    }
    if (isset($args['datatable'])) {
      $data['datatable'] = $args['datatable'];
    }
  }

  return $data;
}

function hs_data_settings(
  string $dataType = 'list',
  array $common = [],
  array $db = [],
  array $list = [],
  array $detail = [],
  array $datatable = [],
  array $es = [],
  array $others = []
): array {


  switch ($dataType) {
    case 'all':

      $settings = array_merge($common, $list, $db, $detail, $datatable);

      break;


    case 'db':

      $settings = array_merge($common, $db);

      break;

    case 'list':

      $settings = array_merge($common, $db, $list);

      break;


    case 'detail':
      $settings = array_merge($common, $list, $detail, $datatable);

      break;

    case 'email':

      $settings = $common;

      break;

    case 'es': // ES 数据用
      $settings = array_merge(
        $common,
        $list,
        // $detail,
        $es
      );

      break;



    case 'datatable':
      $settings = array_merge($common, $db, $list, $datatable);

      break;


    default:

      $settings = array_merge($common, $db);
      break;
  }
  return $settings;
}
/***********************************数据库************************************* */
