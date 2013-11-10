<?php
/**
 * Created by JetBrains PhpStorm.
 * User: KESSILER
 * Date: 07/04/13
 * Time: 01:27
 * To change this template use File | Settings | File Templates.
 */

namespace StoredLibrary;
use StoredLibrary\Configuration as Configuration;


final class Connection {

    private static $instance;
    private $_PDOInstance;
    private $query = null;

    public function __construct()
    {
        if(!isset($this->_PDOInstance))
        {
            try {
                $arrayConfigs = Configuration::get(__DIR__.'/../application.ini');
                $dsn = $arrayConfigs['database']['adapter'].":host=".$arrayConfigs['database']['host'].";dbname=".$arrayConfigs['database']['dbname'];
                $this->_PDOInstance = new \PDO($dsn, $arrayConfigs['database']['username'], $arrayConfigs['database']['password'],array(
                    \PDO::ATTR_PERSISTENT => true
                ));
                $this->_PDOInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                throw new \Exception("PDO Connection error: " . $e->getMessage() . "<br/>");
            }
        } else {
            return $this->_PDOInstance;
        }
    }
    public static function getInstance() {
        if(!isset($instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        return $this->_PDOInstance->beginTransaction();
    }


    public function commit()
    {
        return $this->_PDOInstance->commit();
    }

    public function rollBack()
    {
        return $this->_PDOInstance->rollBack();
    }

    public function exec($statement)
    {
        $preparedSQL = $this->_PDOInstance->prepare($statement);
        $preparedSQL->execute();
        return $preparedSQL;
    }

    public function fetchAll($statement = "")
    {
        if(!empty($statement)) {
            $query = $this->exec($statement);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $query = $this->exec($this->query);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function fetchObject($statement = "")
    {
        if(!empty($statement)) {
            $query = $this->exec($statement);
            return $query->fetch(\PDO::FETCH_OBJ);
        } else {
            $query = $this->exec($this->query);
            return $query->fetch(\PDO::FETCH_OBJ);
        }
    }


    public function fetchRow($statement = "")
    {
        if(!empty($statement)) {
            $query = $this->exec($statement);
            return $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            $query = $this->exec($this->query);
            return $query->fetch(\PDO::FETCH_ASSOC);
        }
    }

    public function insert($name, $data, $now)
    {
        $statement = "INSERT INTO " . $name . "(" .implode(",", array_map("mysql_real_escape_string", array_keys($data))) . ")
                           VALUES ('" . implode("', '", array_map("mysql_real_escape_string", array_values($data))) . "');";
        if($now) {
            $statement = str_replace("'now()'", 'now()', $statement);
        }
        return $this->exec($statement)->rowCount();
    }

    public function update($name, array $data, $where)
    {
        $statement = "UPDATE " . $name . " SET ";
        $quote = "";
        foreach($data AS $key => $value){
            $statement .= $quote . $key . "='" . $value . "'";
            $quote = ",";
        }
        $statement .= " WHERE " . $where;
        return $this->exec($statement)->rowCount();
    }

    public function delete($name, $where)
    {
        $statement = "DELETE FROM " . $name . " WHERE " . $where;
        return $this->exec($statement)->rowCount();
    }

    public function select() {
        $this->query = "SELECT ";
        return $this;
    }

    public function from($table, $params = null)
    {
        if(is_null($params)){
            $this->query .= "* ";
        }else{
            if(is_array($params)){
                $this->query .= implode(",", $params) . " ";
            }else{
                $this->query .= $params . " ";
            }
        }
        $this->query .= "FROM " . $table . " ";
        return $this;
    }

    public function join($table, $relation)
    {
        $this->query .= "INNER JOIN " . $table . " ON " . $relation . " ";
        return $this;
    }

    public function joinLeft($table, $relation)
    {
        $this->query .= "LEFT JOIN " . $table . " ON " . $relation . " ";
        return $this;
    }

    public function where($key, $value)
    {
        $this->query .= "WHERE " . str_replace("?", "'{$value}'", $key) . " ";
        return $this;
    }

    public function andWhere($key, $value)
    {
        $this->query .= "AND " . str_replace("?", "'{$value}'", $key) . " ";
        return $this;
    }

    public function orWhere($key, $value)
    {
        $this->query .= "OR " . str_replace("?", "'{$value}'", $key) . " ";
        return $this;
    }

    public function group($group)
    {
        $this->query .= "GROUP BY " . $group . " ";
        return $this;
    }

    public function having($having)
    {
        $this->query .= "HAVING " . $having . " ";
        return $this;
    }

    public function order($param, $mode = "ASC")
    {
        $this->query .= "ORDER BY " . $param . " " . $mode . " ";
        return $this;
    }

    public function limit($count, $offset = 0)
    {
        $this->query .= "LIMIT " . $count . " OFFSET " . $offset . " ";
        return $this;
    }
}