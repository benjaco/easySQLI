<?php

/**
 * Created by Benjaco
 * https://github.com/benjaco
 * License: MIT
 */
class sqli
{
    /** @var $connection mysqli */
    private $connection = null;

    /**
     * sqli constructor.
     * @param $conf - array with host, username, password and database name for the mysql server
     */
    function __construct($conf)
    {
        $this->connection = new mysqli($conf[0], $conf[1], $conf[2], $conf[3]);
    }

    /**
     * @param $query - standard sql statement with question marks as placeholders
     * @param $dataMode - string with the number of variables and length of string, types must match the parameters in the statement.
     * i for integer
     * d for double
     * s for string
     * b for blob and will be sent in packets
     * @param $parameters - array of variables for the placehoders
     * @return stdClass - returs a object with following propertys:
     * status: true if the prepare methode is fine
     * error_msg: error from the connection if the prepare methode returns false
     * affected_rows: affected rows from the sql statement
     * id: the id there has ben insert if sql statement was a insert statement
     */
    public function push($query, $dataMode = null, $parameters = null)
    {
        $return = (object) array("status" => true, "error_msg" => "", "affected_rows" => 0, "id" => false);
        if ($stmt = $this->connection->prepare($query)) {
            $bindingparams = func_get_args();
            unset($bindingparams[0]);
            if (count($bindingparams) != 0) {
                $bindingparams = array_values($bindingparams);
                if ( strlen($bindingparams[0]) == count($bindingparams)-1 ) {
                    call_user_func_array(array($stmt, "bind_param"), $this->refValues($bindingparams));
                } else {
                    $return->error_msg="Bindede vaerdiger og datamode stemmer ikke overens";
                }
            }
            $stmt->execute();
            if (substr(strtolower($query), 0, 6) == "insert") {
                $return->id = $stmt->insert_id;
            }
            $return->affected_rows = $stmt->affected_rows;
            $stmt->close();
        } else {
            $return->status = false;
            $return->error_msg = $this->connection->error;
        }
        return $return;
    }

    /**
     * @param $query - standard sql statement with question marks as placeholders
     * @param $dataMode - string with the number of variables and length of string, types must match the parameters in the statement.
     * i for integer
     * d for double
     * s for string
     * b for blob and will be sent in packets
     * @param $parameters - array of variables for the placehoders
     * @return stdClass - returs a object with following propertys:
     * status: true if the prepare methode is fine
     * error_msg: error from the connection if the prepare methode returns false
     * data: array of the feilds of the (first) row there has ben selected, empty array of nothing has ben selected
     * count: the row count there has ben selected from the sql statement
     */
    public function pull_once($query, $dataMode = null, $parameters = null)
    {
        $return = (object)array("status" => true, "data" => array(), "error_msg" => "", "count" => 0);
        if ($stmt = $this->connection->prepare($query)) {
            $bindingparams = func_get_args();
            unset($bindingparams[0]);
            if (count($bindingparams) != 0) {
                $bindingparams = array_values($bindingparams);
                if ( strlen($bindingparams[0]) == count($bindingparams)-1 ) {
                    call_user_func_array(array($stmt, "bind_param"), $this->refValues($bindingparams));
                } else {
                    $return->error_msg="Bindede vaerdiger og datamode stemmer ikke overens";
                }
            }
            $stmt->execute();
            $stmt->store_result();
            $return->count = $stmt->num_rows;
            if($return->count){
                $this->bind_array($stmt, $info);
                $stmt->fetch();
                $return->data = $info;
            }

            $stmt->close();
        } else {
            $return->status = false;
            $return->error_msg = $this->connection->error;
        }
        return $return;
    }

    /**
     * @param $query - standard sql statement with question marks as placeholders
     * @param $dataMode - string with the number of variables and length of string, types must match the parameters in the statement.
     * i for integer
     * d for double
     * s for string
     * b for blob and will be sent in packets
     * @param $parameters - array of variables for the placehoders
     * @return stdClass - returs a object with following propertys:
     * status: true if the prepare methode is fine
     * error_msg: error from the connection if the prepare methode returns false
     * data: array of the rows there has ben selected from the sql statement, each item is a array of the feilds
     * count: the row count there has ben selected from the sql statement
     */
    public function pull_multiple($query, $dataMode = null, $parameters = null)
    {
        $return = (object) array("status" => true, "data" => array(), "error_msg" => "", "count" => 0);
        if ($stmt = $this->connection->prepare($query)) {
            $bindingparams = func_get_args();
            unset($bindingparams[0]);
            if (count($bindingparams) != 0) {
                $bindingparams = array_values($bindingparams);
                if ( strlen($bindingparams[0]) == count($bindingparams)-1 ) {
                    call_user_func_array(array($stmt, "bind_param"), $this->refValues($bindingparams));
                } else {
                    $return->error_msg="Bindede vaerdiger og datamode stemmer ikke overens";
                }
            }
            $stmt->execute();

            $stmt->store_result();
            $return->count = $stmt->num_rows;


            $this->bind_array($stmt, $info);
            while ($stmt->fetch()) {
                $row = array();
                foreach ($info as $coll_k => $coll_v) {
                    $row[$coll_k] = $coll_v;
                }
                array_push($return->data, $row);
            }
            $stmt->close();
        } else {
            $return->status = false;
            $return->error_msg = $this->connection->error;
        }
        return $return;
    }

    /**
     * @param $arr
     * @return stdClass
     */
    private function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = & $arr[$key];
            }
            return $refs;
        }
        return $arr;
    }


    /**
     * @param $stmt
     * @param $row
     */
    private function bind_array($stmt, &$row)
    {
        /** @var $stmt mysqli_stmt */
        $md = $stmt->result_metadata();
        $params = array();
        while ($field = $md->fetch_field()) {
            $params[] = & $row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
    }
}