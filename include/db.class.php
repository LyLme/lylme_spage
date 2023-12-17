<?php

//MySQL、MySQLi、SQLite 三合一数据库操作类
if(!defined('IN_CRONLITE')) {
    exit();
}

$nomysqli = false;

if(defined('SQLITE') == true) {
    class DB
    {
        public $link = null;
        public $result = null;
        public function __construct($db_file)
        {
            global $siteurl;
            $this->link = new PDO('sqlite:' . ROOT . 'includes/sqlite/' . $db_file . '.db');
            if (!$this->link) {
                die('Connection Sqlite failed.\n');
            }
            return true;
        }

        public function fetch($q)
        {
            return $q->fetch();
        }
        public function get_row($q)
        {
            $sth = $this->link->query($q);
            return $sth->fetch();
        }
        public function count($q)
        {
            $sth = $this->link->query($q);
            return $sth->fetchColumn();
        }
        public function query($q)
        {
            return $this->result = $this->link->query($q);
        }
        public function affected()
        {
            return $this->result->rowCount();
        }
        public function error()
        {
            $error = $this->link->errorInfo();
            return '[' . $error[1] . '] ' . $error[2];
        }
    }
} elseif(extension_loaded('mysqli') && $nomysqli == false) {
    class DB
    {
        public $link = null;

        public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port)
        {

            $this->link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

            if (!$this->link) {
                die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
            }

            //mysqli_select_db($this->link, $db_name) or die(mysqli_error($this->link));


            mysqli_query($this->link, "set sql_mode = ''");
            //字符转换，读库
            mysqli_query($this->link, "set character set 'utf8'");
            //写库
            mysqli_query($this->link, "set names 'utf8'");

            return true;
        }
        public function fetch($q)
        {
            return mysqli_fetch_assoc($q);
        }
        public function num_rows($result)
        {
            return mysqli_num_rows($result);
        }
        public function get_row($q)
        {
            $result = mysqli_query($this->link, $q);
            return mysqli_fetch_assoc($result);
        }
        public function get_column($q)
        {
            $result = mysqli_query($this->link, $q);
            $row = mysqli_fetch_array($result);
            return $row[0];
        }
        public function count($q)
        {
            $result = mysqli_query($this->link, $q);
            $count = mysqli_fetch_array($result);
            return $count[0];
        }
        public function query($q)
        {
            return mysqli_query($this->link, $q);
        }
        public function escape($str)
        {
            return mysqli_real_escape_string($this->link, $str);
        }
        public function insert($q)
        {
            if(mysqli_query($this->link, $q)) {
                return mysqli_insert_id($this->link);
            }
            return false;
        }
        public function affected()
        {
            return mysqli_affected_rows($this->link);
        }
        public function insert_array($table, $array)
        {
            $q = "INSERT INTO `$table`";
            $q .= " (`" . implode("`,`", array_keys($array)) . "`) ";
            $q .= " VALUES ('" . implode("','", array_values($array)) . "') ";

            if(mysqli_query($this->link, $q)) {
                return mysqli_insert_id($this->link);
            }
            return false;
        }
        public function error()
        {
            $error = mysqli_error($this->link);
            $errno = mysqli_errno($this->link);
            return '[' . $errno . '] ' . $error;
        }
        public function close()
        {
            $q = mysqli_close($this->link);
            return $q;
        }
    }
} else { // we use the old mysql
    class DB
    {
        public $link = null;

        public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port)
        {

            $this->link = @mysql_connect($db_host . ':' . $db_port, $db_user, $db_pass);

            if (!$this->link) {
                die('Connect Error (' . mysql_errno() . ') ' . mysql_error());
            }

            mysql_select_db($db_name, $this->link) or die(mysql_error($this->link));

            mysql_query("set sql_mode = ''");
            //字符转换，读库
            mysql_query("set character set 'utf8'");
            //写库
            mysql_query("set names 'utf8'");


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
            return mysql_fetch_assoc($result);
        }
        public function get_column($q)
        {
            $result = mysql_query($q, $this->link);
            $row = mysql_fetch_array($result);
            return $row[0];
        }
        public function count($q)
        {
            $result = mysql_query($q, $this->link);
            $count = mysql_fetch_array($result);
            return $count[0];
        }
        public function query($q)
        {
            return mysql_query($q, $this->link);
        }
        public function escape($str)
        {
            return mysql_real_escape_string($str, $this->link);
        }
        public function affected()
        {
            return mysql_affected_rows($this->link);
        }
        public function insert($q)
        {
            if(mysql_query($q, $this->link)) {
                return mysql_insert_id($this->link);
            }
            return false;
        }
        public function insert_array($table, $array)
        {
            $q = "INSERT INTO `$table`";
            $q .= " (`" . implode("`,`", array_keys($array)) . "`) ";
            $q .= " VALUES ('" . implode("','", array_values($array)) . "') ";

            if(mysql_query($q, $this->link)) {
                return mysql_insert_id($this->link);
            }
            return false;
        }
        public function error()
        {
            $error = mysql_error($this->link);
            $errno = mysql_errno($this->link);
            return '[' . $errno . '] ' . $error;
        }
        public function close()
        {
            $q = mysql_close($this->link);
            return $q;
        }
    }

}
