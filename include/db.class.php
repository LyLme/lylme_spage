<?php

/**
 * 数据库操作类 - MySQLi/PDO/SQLite 三合一

 */

if (!defined('IN_CRONLITE')) {
    exit('Access denied');
}

// 检测可用的数据库扩展
$db_available = [];

// 检测PDO (推荐，用于SQLite和MySQL)
if (extension_loaded('PDO')) {
    $pdo_drivers = PDO::getAvailableDrivers();
    // 检测 MySQL/MariaDB 相关驱动
    foreach ($pdo_drivers as $driver) {
        if (in_array($driver, ['mysql', 'mysq', 'mariadb'])) {
            $db_available['pdo_mysql'] = $driver;
            break;
        }
    }
    // SQLite 也检测一下
    if (in_array('sqlite', $pdo_drivers)) {
        $db_available['pdo_sqlite'] = 'sqlite';
    }
}

// 检测MySQLi (PHP 5.3+)
if (extension_loaded('mysqli')) {
    $db_available['mysqli'] = true;
}

// 检测旧版MySQL扩展 (PHP 5.5已废弃, PHP 7.0已移除)
// 仅在需要兼容极老版本时启用
$enable_old_mysql = false;
if (defined('USE_OLD_MYSQL') && USE_OLD_MYSQL === true) {
    if (extension_loaded('mysql')) {
        $db_available['mysql'] = true;
        $enable_old_mysql = true;
    }
}

/**
 * 数据库抽象基类
 */
abstract class DBBase
{
    public $link = null;
    public $result = null;
    public $last_error = '';

    abstract public function fetch($q);
    abstract public function num_rows($result);
    abstract public function get_row($q);
    abstract public function query($q);
    abstract public function error();

    /**
     * 安全转义字符串
     */
    public function escape($str)
    {
        if (is_array($str)) {
            return array_map([$this, 'escape'], $str);
        }
        return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
    }

    /**
     * 获取插入ID
     */
    public function insert_id()
    {
        return 0;
    }

    /**
     * 获取影响行数
     */
    public function affected()
    {
        return 0;
    }

    /**
     * 插入数据
     */
    public function insert($q)
    {
        if ($this->query($q)) {
            return $this->insert_id();
        }
        return false;
    }

    /**
     * 批量插入
     */
    public function insert_array($table, $array)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        $keys = array_keys($array);
        $values = array_values($array);

        // 转义值
        $escaped_values = array_map([$this, 'escape'], $values);

        $q = "INSERT INTO `{$table}`";
        $q .= " (`" . implode("`,`", $keys) . "`) ";
        $q .= " VALUES ('" . implode("','", $escaped_values) . "') ";

        return $this->insert($q);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        return true;
    }
}

/**
 * PDO数据库类 (支持MySQL和SQLite)
 */
class DBPdo extends DBBase
{
    private $db_type = 'mysql';

    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port = 3306, $db_type = 'mysql')
    {
        $this->db_type = strtolower($db_type);

        try {
            if ($this->db_type === 'sqlite') {
                // SQLite连接
                $dsn = 'sqlite:' . $db_host;
                $this->link = new PDO($dsn);
            } else {
                // MySQL连接 - 检测可用的驱动
                $available_drivers = PDO::getAvailableDrivers();

                // 优先使用 mysql (PHP 7+), 其次是 mysql (PHP 5.x)
                $driver = in_array('mysql', $available_drivers) ? 'mysql' : (in_array('mysq', $available_drivers) ? 'mysq' : '');

                if (empty($driver)) {
                    // 尝试所有可能的驱动
                    foreach ($available_drivers as $d) {
                        if (stripos($d, 'mysql') !== false || stripos($d, 'mariadb') !== false) {
                            $driver = $d;
                            break;
                        }
                    }
                }

                if (empty($driver)) {
                    throw new PDOException('No MySQL/MariaDB PDO driver available');
                }

                // 构建DSN
                $dsn = "{$driver}:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

                $this->link = new PDO($dsn, $db_user, $db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            die('Connection failed: ' . $this->last_error);
        }

        return true;
    }

    public function fetch($q)
    {
        if ($q instanceof PDOStatement) {
            return $q->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function num_rows($result)
    {
        if ($result instanceof PDOStatement) {
            return $result->rowCount();
        }
        return 0;
    }

    public function get_row($q)
    {
        if ($this->db_type === 'sqlite') {
            $stmt = $this->link->query($q);
            return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }

        $stmt = $this->link->prepare($q);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_column($q)
    {
        $stmt = $this->link->query($q);
        return $stmt ? $stmt->fetchColumn() : false;
    }

    public function count($q)
    {
        return $this->get_column($q);
    }

    public function query($q)
    {
        try {
            $this->result = $this->link->query($q);
            return $this->result;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    public function error()
    {
        $error = $this->link->errorInfo();
        return '[' . ($error[1] ?? 0) . '] ' . ($error[2] ?? 'Unknown error');
    }

    public function insert_id()
    {
        return (int) $this->link->lastInsertId();
    }

    public function affected()
    {
        return $this->result ? $this->result->rowCount() : 0;
    }

    public function escape($str)
    {
        return $this->link->quote($str);
    }

    public function close()
    {
        $this->link = null;
        return true;
    }
}

/**
 * MySQLi数据库类 (PHP 5.3+)
 */
class DBMysqli extends DBBase
{
    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port = 3306)
    {
        // PHP 8.1+ 错误报告模式
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }

        // 尝试使用持久连接 (PHP 5.3+)
        $host = (version_compare(PHP_VERSION, '5.3.0', '>=')) ? 'p:' . $db_host : $db_host;

        $this->link = @mysqli_connect($host, $db_user, $db_pass, $db_name, $db_port);

        if (!$this->link) {
            $errno = mysqli_connect_errno();
            $error = mysqli_connect_error();
            $this->last_error = "Connect Error ({$errno}) {$error}";
            die($this->last_error);
        }

        // 设置字符集
        mysqli_set_charset($this->link, 'utf8mb4');

        // 兼容旧版PHP
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            mysqli_query($this->link, "SET sql_mode = ''");
        }

        return true;
    }

    public function fetch($q)
    {
        if ($q instanceof mysqli_result) {
            return mysqli_fetch_assoc($q);
        }
        return false;
    }

    public function num_rows($result)
    {
        if ($result instanceof mysqli_result) {
            return mysqli_num_rows($result);
        }
        return 0;
    }

    public function get_row($q)
    {
        $result = mysqli_query($this->link, $q);
        return $result ? mysqli_fetch_assoc($result) : false;
    }

    public function get_column($q)
    {
        $result = mysqli_query($this->link, $q);
        if ($result) {
            $row = mysqli_fetch_array($result);
            mysqli_free_result($result);
            return $row[0] ?? false;
        }
        return false;
    }

    public function count($q)
    {
        return $this->get_column($q);
    }

    public function query($q)
    {
        $this->result = mysqli_query($this->link, $q);
        return $this->result;
    }

    public function error()
    {
        return mysqli_error($this->link);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->link);
    }

    public function affected()
    {
        return mysqli_affected_rows($this->link);
    }

    public function escape($str)
    {
        return mysqli_real_escape_string($this->link, $str);
    }

    public function close()
    {
        return mysqli_close($this->link);
    }
}

/**
 * 旧版MySQL数据库类 (PHP 5.5已废弃, PHP 7.0已移除)
 * 仅用于兼容极老系统
 */
if ($enable_old_mysql) {
    class DBOld extends DBBase
    {
        public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port = 3306)
        {
            $this->link = @mysql_connect($db_host . ':' . $db_port, $db_user, $db_pass);

            if (!$this->link) {
                $errno = mysql_errno();
                $error = mysql_error();
                $this->last_error = "Connect Error ({$errno}) {$error}";
                die($this->last_error);
            }

            mysql_select_db($db_name, $this->link);
            mysql_query("SET NAMES 'utf8mb4'", $this->link);

            return true;
        }

        public function fetch($q)
        {
            return mysql_fetch_assoc($q);
        }

        public function num_rows($result)
        {
            return mysql_num_rows($result);
        }

        public function get_row($q)
        {
            $result = mysql_query($q, $this->link);
            return $result ? mysql_fetch_assoc($result) : false;
        }

        public function get_column($q)
        {
            $result = mysql_query($q, $this->link);
            if ($result) {
                $row = mysql_fetch_array($result);
                mysql_free_result($result);
                return $row[0] ?? false;
            }
            return false;
        }

        public function count($q)
        {
            return $this->get_column($q);
        }

        public function query($q)
        {
            $this->result = mysql_query($q, $this->link);
            return $this->result;
        }

        public function error()
        {
            return mysql_error($this->link);
        }

        public function insert_id()
        {
            return mysql_insert_id($this->link);
        }

        public function affected()
        {
            return mysql_affected_rows($this->link);
        }

        public function escape($str)
        {
            return mysql_real_escape_string($str, $this->link);
        }

        public function close()
        {
            return mysql_close($this->link);
        }
    }
}

/**
 * 主DB类 - 自动选择最佳驱动
 * 
 * 优先级:
 * 1. MySQLi (推荐 - PHP 5.3+)
 * 2. PDO MySQL (PHP 5.1+)
 * 3. PDO SQLite (PHP 5.1+)
 * 4. 旧版MySQL (PHP 5.5-, 已废弃)
 */
if (!class_exists('DB', false)) {

    if (defined('SQLITE') && SQLITE === true) {
        // 使用SQLite
        if (!empty($db_available['pdo_sqlite'])) {
            class DB extends DBPdo
            {
                public function __construct($db_file = 'lylme')
                {
                    $db_path = defined('ROOT') ? ROOT . 'includes/sqlite/' . $db_file . '.db' : $db_file . '.db';
                    parent::__construct($db_path, '', '', '', 0, 'sqlite');
                }
            }
        } else {
            die('SQLite requires PDO extension. Please install PDO SQLite.');
        }
    } elseif (!empty($db_available['mysqli'])) {
        // 使用MySQLi (推荐)
        class DB extends DBMysqli
        {
            // 保持原有构造函数签名兼容
        }
    } elseif (!empty($db_available['pdo_mysql'])) {
        // 使用PDO MySQL
        class DB extends DBPdo
        {
            // 保持原有构造函数签名兼容
        }
    } elseif ($enable_old_mysql && !empty($db_available['mysql'])) {
        // 使用旧版MySQL (仅紧急兼容)
        class DB extends DBOld
        {
            // 保持原有构造函数签名兼容
        }
    } else {
        // 无可用数据库扩展 - 显示详细错误信息

        $error_msg = '<b>当前 PHP 版本：' . PHP_VERSION . '</b>缺少数据库扩展<br>';
        $error_msg .= '<br>请至少安装以下其中一个扩展：<br>';
        $error_msg .= '<ul>';
        $error_msg .= '<li>MySQLi 扩展</li>';
        $error_msg .= '<li>PDO MySQL 扩展</li>';
        $error_msg .= '</ul>';
        $error_msg .= '安装扩展方法：<br>';
        $error_msg .= '编辑 php.ini 文件，添加以下行：<br>';
        $error_msg .= '<code>extension=mysqli</code>（推荐）<br>';
        $error_msg .= '<code>extension=pdo_mysql</code>（备选）<br>';

        // 输出错误页面
        die('<h3>数据库连接错误</h3>' . $error_msg);
    }
}
