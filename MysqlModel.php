<?php namespace Devtools;
/**
 * MysqlModel
 *
 * Model for MySQL databases
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT:
 * @link     http://github.com/seagoj/Devtools/MysqlModel.php
 **/

class MysqlModel extends PDORepository
{
    protected $table = 'users';
    protected $primaryKey = 'userid';
    protected $required = [];

    public function set($assignments, $collection, $where=null)
    {
        $key = array_keys($assignments);
        $fields = array();
        foreach ($key as $name) {
            array_push($fields, ':'.$name);
        }
        $sql = "INSERT INTO $collection ("
            .self::stringify($key, true, '`')
        .") VALUES ("
            .implode(',', $fields)
        .") ON DUPLICATE KEY UPDATE ";

        $first = true;
        foreach ($key as $field) {
            if (!$first) {
                $sql .= ',';
            }
            $sql .= "`$field`=VALUES(`$field`)";
            $first = false;
        }

        if (!is_null($where)) {
            extract(
                $this->where($where)
            );
            $sql .= $where;
        } else {
            $params = array();
        }
        return $this->query($sql, array_merge($assignments, $params));
    }
}
