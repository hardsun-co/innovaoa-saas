<?php

namespace Hs\Msdb;

/**
 * SQL Server 数据库连接类
 * 
 * 提供与MSSQL/SQL Server数据库的连接和基本操作
 * 
 * @author pjw@hardsun
 * @date 2025-04-03
 */
class Index
{
  /**
   * 单例实例
   * @var Connection
   */
  private static $instance = null;

  /**
   * PDO连接实例
   * @var \PDO
   */
  private $conn = null;

  /**
   * 最后执行的SQL语句
   * @var string
   */
  private $lastQuery = '';

  /**
   * 最后一个错误信息
   * @var string
   */
  private $lastError = '';

  /**
   * 私有构造函数，防止直接创建实例
   */
  private function __construct() {}

  /**
   * 获取数据库连接单例
   * 
   * @param array $config 可选的配置参数覆盖默认设置
   * @return Connection
   */
  public static function getInstance(array $config = [])
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    if (!self::$instance->isConnected()) {
      self::$instance->connect($config);
    }

    return self::$instance;
  }

  /**
   * 连接到SQL Server数据库
   * 
   * @param array $config 连接配置
   * @return bool 连接是否成功
   */
  public function connect(array $config = [])
  {
    // 默认配置
    $defaultConfig = [
      'host' => defined('FM_DB_HOST') ? FM_DB_HOST : 'localhost',
      'port' => defined('FM_DB_PORT') ? FM_DB_PORT : '1433',
      'database' => defined('FM_DB_NAME') ? FM_DB_NAME : '',
      'username' => defined('FM_DB_USER_NAME') ? FM_DB_USER_NAME : '',
      'password' => defined('FM_DB_PASSWORD') ? FM_DB_PASSWORD : '',
      'charset' => 'UTF-8',
      'trusted' => false, // 是否使用Windows身份验证
      'timeout' => 30     // 连接超时(秒)
    ];

    // 合并配置
    $config = array_merge($defaultConfig, $config);

    try {
      // 只保留有效参数
      $dsn = "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}";

      $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
      ];

      // 使用Windows身份验证
      if ($config['trusted']) {
        $this->conn = new \PDO(
          $dsn . ";TrustServerCertificate=true;Trusted_Connection=yes",
          null,
          null,
          $options
        );
      } else {
        // 使用SQL Server身份验证 - 移除CharacterSet参数
        $this->conn = new \PDO(
          $dsn . ";TrustServerCertificate=true",
          $config['username'],
          $config['password'],
          $options
        );
      }

      return true;
    } catch (\PDOException $e) {
      $this->lastError = "连接错误: " . $e->getMessage();
      var_export($this->lastError);
      return false;
    }
  }

  /**
   * 检查是否已连接到数据库
   * 
   * @return bool
   */
  public function isConnected()
  {
    return ($this->conn !== null);
  }

  /**
   * 执行SQL查询
   * 
   * @param string $sql SQL语句
   * @param array $params 绑定参数
   * @return \PDOStatement|false
   */
  public function query($sql, array $params = [])
  {
    if (!$this->isConnected()) {
      $this->lastError = "未连接到数据库";
      return false;
    }

    $this->lastQuery = $sql;

    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (\PDOException $e) {
      $this->lastError = "查询错误: " . $e->getMessage();
      hs_ve($this->lastError);
      return false;
    }
  }

  /**
   * 获取单行数据
   * 
   * @param string $sql SQL语句
   * @param array $params 绑定参数
   * @return array|false 返回单行数据或失败时返回false
   */
  public function getRow($sql, array $params = [])
  {
    $stmt = $this->query($sql, $params);

    if ($stmt === false) {
      return false;
    }

    return $stmt->fetch();
  }

  /**
   * 获取结果集
   * 
   * @param string $sql SQL语句
   * @param array $params 绑定参数
   * @return array|false 返回结果集或失败时返回false
   */
  public function getResults($sql, array $params = [])
  {
    $stmt = $this->query($sql, $params);

    if ($stmt === false) {
      return false;
    }

    return $stmt->fetchAll();
  }

  /**
   * 获取单个值
   * 
   * @param string $sql SQL语句
   * @param array $params 绑定参数
   * @return mixed|false 返回单个值或失败时返回false
   */
  public function getValue($sql, array $params = [])
  {
    $stmt = $this->query($sql, $params);

    if ($stmt === false) {
      return false;
    }

    $row = $stmt->fetch(\PDO::FETCH_NUM);
    return $row ? $row[0] : false;
  }

  /**
   * 检查表是否存在
   * 
   * @param string $table 表名
   * @return bool 是否存在
   */
  public function tableExists($table)
  {
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = ?";
    $stmt = $this->query($sql, [$table]);

    if ($stmt === false) {
      return false;
    }

    return $stmt->fetchColumn() > 0;
  }

  /**
   * 执行插入操作
   * 
   * @param string $table 表名
   * @param array $data 要插入的数据 [字段名 => 值]
   * @return int|false 成功返回插入的ID，失败返回false
   */
  public function insert($table, array $data)
  {
    if (empty($data)) {
      $this->lastError = "插入数据不能为空";
      return false;
    }

    $fields = array_keys($data);
    $placeholders = array_fill(0, count($fields), '?');

    $sql = sprintf(
      "INSERT INTO %s (%s) VALUES (%s)",
      $table,
      implode(', ', $fields),
      implode(', ', $placeholders)
    );

    if ($this->query($sql, array_values($data)) !== false) {
      try {
        return $this->conn->lastInsertId();
      } catch (\PDOException $e) {
        return false;
      }
    }

    return false;
  }

  /**
   * 执行更新操作
   * 
   * @param string $table 表名
   * @param array $data 要更新的数据 [字段名 => 值]
   * @param int $id 更新的记录ID
   * @return int|false 更新成功返回更新的id，失败返回false
   */
  public function update($table, array $data, int $id)
  {
    if (empty($data)) {
      $this->lastError = "更新数据不能为空";
      return false;
    }

    $setParts = [];
    $params = [];

    foreach ($data as $field => $value) {
      $setParts[] = "$field = ?";
      $params[] = $value;
    }

    $sql = sprintf("UPDATE %s SET %s", $table, implode(', ', $setParts));

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $this->query($sql, $params);

    if ($stmt !== false) {
      return $stmt->rowCount() > 0 ? $id : false;
    }
    return false;
  }

  /**
   * 执行删除操作
   * 
   * @param string $table 表名
   * @param string $where 条件语句 (不含WHERE关键字)
   * @param array $params WHERE条件的参数
   * @return int|false 受影响的行数或失败时返回false
   */
  public function delete($table, $where = '', array $params = [])
  {
    $sql = "DELETE FROM $table";

    if ($where) {
      $sql .= " WHERE $where";
    }

    $stmt = $this->query($sql, $params);

    return $stmt !== false ? $stmt->rowCount() : false;
  }

  /**
   * 开始事务
   * 
   * @return bool 是否成功
   */
  public function beginTransaction()
  {
    if (!$this->isConnected()) {
      $this->connect();
    }

    try {
      return $this->conn->beginTransaction();
    } catch (\PDOException $e) {
      $this->lastError = "事务启动失败: " . $e->getMessage();
      return false;
    }
  }

  /**
   * 提交事务
   * 
   * @return bool 是否成功
   */
  public function commit()
  {
    try {
      return $this->conn->commit();
    } catch (\PDOException $e) {
      $this->lastError = "事务提交失败: " . $e->getMessage();
      return false;
    }
  }

  /**
   * 回滚事务
   * 
   * @return bool 是否成功
   */
  public function rollback()
  {
    try {
      return $this->conn->rollBack();
    } catch (\PDOException $e) {
      $this->lastError = "事务回滚失败: " . $e->getMessage();
      return false;
    }
  }

  /**
   * 关闭数据库连接
   * 
   * @return void
   */
  public function close()
  {
    $this->conn = null;
  }

  /**
   * 获取最后执行的SQL语句
   * 
   * @return string
   */
  public function getLastQuery()
  {
    return $this->lastQuery;
  }

  /**
   * 获取最后一个错误信息
   * 
   * @return string
   */
  public function getLastError()
  {
    return $this->lastError;
  }

  /**
   * 获取原始PDO连接对象
   * 
   * @return \PDO|null
   */
  public function getPdo()
  {
    return $this->conn;
  }
}
