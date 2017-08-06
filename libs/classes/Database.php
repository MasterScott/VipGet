<?php

class dbal
{
    public $db_connect_id;
    public $query_result;
    public $return_on_error = false;
    public $transaction = false;
    public $sql_time = 0;
    public $num_queries = array();
    public $open_queries = array();
    public $curtime = 0;
    public $query_hold = '';
    public $html_hold = '';
    public $persistency = false;
    public $user = '';
    public $server = '';
    public $dbname = '';
    public $sql_error_triggered = false;
    public $sql_error_sql = '';
    public $sql_error_returned = array();
    public $transactions = 0;
    public $multi_insert = false;
    public $sql_layer = '';
    public $any_char;
    public $one_char;
    public $sql_server_version = false;


    public function dbal()
    {
        $this->num_queries = array(
            'cached'		=> 0,
            'normal'		=> 0,
            'total'			=> 0,
        );
        $this->sql_layer = substr(get_class($this), 5);
        $this->any_char = chr(0) . '%';
        $this->one_char = chr(0) . '_';
    }
    public function sql_return_on_error($fail = false)
    {
        $this->sql_error_triggered = false;
        $this->sql_error_sql = '';
        $this->return_on_error = $fail;
    }
    public function sql_num_queries($cached = false)
    {
        return ($cached) ? $this->num_queries['cached'] : $this->num_queries['normal'];
    }
    public function sql_add_num_queries($cached = false)
    {
        $this->num_queries['cached'] += ($cached !== false) ? 1 : 0;
        $this->num_queries['normal'] += ($cached !== false) ? 0 : 1;
        $this->num_queries['total'] += 1;
    }
    public function sql_close()
    {
        if (!$this->db_connect_id)
        {
            return false;
        }
        if ($this->transaction)
        {
            do
            {
                $this->sql_transaction('commit');
            }
            while ($this->transaction);
        }
        foreach ($this->open_queries as $query_id)
        {
            $this->sql_freeresult($query_id);
        }
        if ($result = $this->_sql_close())
        {
            $this->db_connect_id = false;
        }

        return $result;
    }
    public function sql_query_limit($query, $total, $offset = 0)
    {
        if (empty($query))
        {
            return false;
        }
        $total = ($total < 0) ? 0 : $total;
        $offset = ($offset < 0) ? 0 : $offset;
        return $this->_sql_query_limit($query, $total, $offset, $cache_ttl);
    }
    public function sql_fetchrowset($query_id = false)
    {
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        if ($query_id !== false)
        {
            $result = array();
            while ($row = $this->sql_fetchrow($query_id))
            {
                $result[] = $row;
            }
            return $result;
        }
        return false;
    }
    public function sql_rowseek($rownum, &$query_id)
    {
        global $cache;
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        if (isset($cache->sql_rowset[$query_id]))
        {
            return $cache->sql_rowseek($rownum, $query_id);
        }
        if ($query_id === false)
        {
            return false;
        }
        $this->sql_freeresult($query_id);
        $query_id = $this->sql_query($this->last_query_text);
        if ($query_id === false)
        {
            return false;
        }
        for ($i = 0; $i < $rownum; $i++)
        {
            if (!$this->sql_fetchrow($query_id))
            {
                return false;
            }
        }
        return true;
    }
    public function sql_fetchfield($field, $rownum = false, $query_id = false)
    {
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        if ($query_id !== false)
        {
            if ($rownum !== false)
            {
                $this->sql_rowseek($rownum, $query_id);
            }
            $row = $this->sql_fetchrow($query_id);
            return (isset($row[$field])) ? $row[$field] : false;
        }
        return false;
    }
    public function sql_like_expression($expression)
    {
        $expression = utf8_str_replace(array('_', '%'), array("\_", "\%"), $expression);
        $expression = utf8_str_replace(array(chr(0) . "\_", chr(0) . "\%"), array('_', '%'), $expression);
        return $this->_sql_like_expression('LIKE \'' . $this->sql_escape($expression) . '\'');
    }
    private function sql_transaction($status = 'begin')
    {
        switch ($status)
        {
            case 'begin':
                if ($this->transaction)
                {
                    $this->transactions++;
                    return true;
                }
                $result = $this->_sql_transaction('begin');
                if (!$result)
                {
                    $this->sql_error();
                }
                $this->transaction = true;
                break;
            case 'commit':
                if ($this->transaction && $this->transactions)
                {
                    $this->transactions--;
                    return true;
                }
                if (!$this->transaction)
                {
                    return false;
                }
                $result = $this->_sql_transaction('commit');
                if (!$result)
                {
                    $this->sql_error();
                }
                $this->transaction = false;
                $this->transactions = 0;
                break;
            case 'rollback':
                $result = $this->_sql_transaction('rollback');
                $this->transaction = false;
                $this->transactions = 0;
                break;
            default:
                $result = $this->_sql_transaction($status);
                break;
        }
        return $result;
    }
    public function sql_build_array($query, $assoc_ary = false)
    {
        if (!is_array($assoc_ary))
        {
            return false;
        }

        $fields = $values = array();

        if ($query == 'INSERT' || $query == 'INSERT_SELECT')
        {
            foreach ($assoc_ary as $key => $var)
            {
                $fields[] = $key;

                if (is_array($var) && is_string($var[0]))
                {
                    $values[] = $var[0];
                }
                else
                {
                    $values[] = $this->_sql_validate_value($var);
                }
            }

            $query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
        }
        else if ($query == 'MULTI_INSERT')
        {
            trigger_error('The MULTI_INSERT query value is no longer supported. Please use sql_multi_insert() instead.', E_USER_ERROR);
        }
        else if ($query == 'UPDATE' || $query == 'SELECT')
        {
            $values = array();
            foreach ($assoc_ary as $key => $var)
            {
                $values[] = "$key = " . $this->_sql_validate_value($var);
            }
            $query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
        }

        return $query;
    }
    public function sql_in_set($field, $array, $negate = false, $allow_empty_set = false)
    {
        if (!sizeof($array))
        {
            if (!$allow_empty_set)
            {
                $this->sql_error('No values specified for SQL IN comparison');
            }
            else
            {
                if ($negate)
                {
                    return '1=1';
                }
                else
                {
                    return '1=0';
                }
            }
        }
        if (!is_array($array))
        {
            $array = array($array);
        }
        if (sizeof($array) == 1)
        {
            @reset($array);
            $var = current($array);

            return $field . ($negate ? ' <> ' : ' = ') . $this->_sql_validate_value($var);
        }
        else
        {
            return $field . ($negate ? ' NOT IN ' : ' IN ') . '(' . implode(', ', array_map(array($this, '_sql_validate_value'), $array)) . ')';
        }
    }
    public function sql_multi_insert($table, &$sql_ary)
    {
        if (!sizeof($sql_ary))
        {
            return false;
        }
        if ($this->multi_insert)
        {
            $ary = array();
            foreach ($sql_ary as $id => $_sql_ary)
            {
                if (!is_array($_sql_ary))
                {
                    return $this->sql_query('INSERT INTO ' . $table . ' ' . $this->sql_build_array('INSERT', $sql_ary));
                }
                $values = array();
                foreach ($_sql_ary as $key => $var)
                {
                    $values[] = $this->_sql_validate_value($var);
                }
                $ary[] = '(' . implode(', ', $values) . ')';
            }
            return $this->sql_query('INSERT INTO ' . $table . ' ' . ' (' . implode(', ', array_keys($sql_ary[0])) . ') VALUES ' . implode(', ', $ary));
        }
        else
        {
            foreach ($sql_ary as $ary)
            {
                if (!is_array($ary))
                {
                    return false;
                }
                $result = $this->sql_query('INSERT INTO ' . $table . ' ' . $this->sql_build_array('INSERT', $ary));
                if (!$result)
                {
                    return false;
                }
            }
        }
        return true;
    }
    private function _sql_validate_value($var)
    {
        if (is_null($var))
        {
            return 'NULL';
        }
        else if (is_string($var))
        {
            return "'" . $this->sql_escape($var) . "'";
        }
        else
        {
            return (is_bool($var)) ? intval($var) : $var;
        }
    }
    public function sql_build_query($query, $array)
    {
        $sql = '';
        switch ($query)
        {
            case 'SELECT':
            case 'SELECT_DISTINCT';

                $sql = str_replace('_', ' ', $query) . ' ' . $array['SELECT'] . ' FROM ';
                $table_array = $aliases = array();
                $used_multi_alias = false;
                foreach ($array['FROM'] as $table_name => $alias)
                {
                    if (is_array($alias))
                    {
                        $used_multi_alias = true;
                        foreach ($alias as $multi_alias)
                        {
                            $table_array[] = $table_name . ' ' . $multi_alias;
                            $aliases[] = $multi_alias;
                        }
                    }
                    else
                    {
                        $table_array[] = $table_name . ' ' . $alias;
                        $aliases[] = $alias;
                    }
                }
                if (!empty($array['LEFT_JOIN']) && sizeof($array['FROM']) > 1 && $used_multi_alias !== false)
                {
                    $join = current($array['LEFT_JOIN']);
                    preg_match('/(' . implode('|', $aliases) . ')\.[^\s]+/U', str_replace(array('(', ')', 'AND', 'OR', ' '), '', $join['ON']), $matches);
                    if (!empty($matches[1]))
                    {
                        $first_join_match = trim($matches[1]);
                        $table_array = $last = array();
                        foreach ($array['FROM'] as $table_name => $alias)
                        {
                            if (is_array($alias))
                            {
                                foreach ($alias as $multi_alias)
                                {
                                    ($multi_alias === $first_join_match) ? $last[] = $table_name . ' ' . $multi_alias : $table_array[] = $table_name . ' ' . $multi_alias;
                                }
                            }
                            else
                            {
                                ($alias === $first_join_match) ? $last[] = $table_name . ' ' . $alias : $table_array[] = $table_name . ' ' . $alias;
                            }
                        }
                        $table_array = array_merge($table_array, $last);
                    }
                }
                $sql .= $this->_sql_custom_build('FROM', implode(' CROSS JOIN ', $table_array));
                if (!empty($array['LEFT_JOIN']))
                {
                    foreach ($array['LEFT_JOIN'] as $join)
                    {
                        $sql .= ' LEFT JOIN ' . key($join['FROM']) . ' ' . current($join['FROM']) . ' ON (' . $join['ON'] . ')';
                    }
                }
                if (!empty($array['WHERE']))
                {
                    $sql .= ' WHERE ' . $this->_sql_custom_build('WHERE', $array['WHERE']);
                }
                if (!empty($array['GROUP_BY']))
                {
                    $sql .= ' GROUP BY ' . $array['GROUP_BY'];
                }
                if (!empty($array['ORDER_BY']))
                {
                    $sql .= ' ORDER BY ' . $array['ORDER_BY'];
                }
                break;
        }
        return $sql;
    }
    public function sql_error($sql = '')
    {
        $this->sql_error_triggered = true;
        $this->sql_error_sql = $sql;
        $this->sql_error_returned = $this->_sql_error();
        if (!$this->return_on_error)
        {
            $message = 'SQL ERROR [ ' . $this->sql_layer . ' ]<br /><br />' . $this->sql_error_returned['message'] . ' [' . $this->sql_error_returned['code'] . ']';
            if ($this->transaction)
            {
                $this->sql_transaction('rollback');
            }
            if (strlen($message) > 1024)
            {
                global $msg_long_text;
                $msg_long_text = $message;
                trigger_error(false, E_USER_ERROR);
            }
            trigger_error($message, E_USER_ERROR);
        }
        if ($this->transaction)
        {
            $this->sql_transaction('rollback');
        }
        return $this->sql_error_returned;
    }
    public function get_estimated_row_count($table_name)
    {
        return $this->get_row_count($table_name);
    }
    public function get_row_count($table_name)
    {
        $sql = 'SELECT COUNT(*) AS rows_total
			FROM ' . $this->sql_escape($table_name);
        $result = $this->sql_query($sql);
        $rows_total = $this->sql_fetchfield('rows_total');
        $this->sql_freeresult($result);

        return $rows_total;
    }
}

$sql_db = (!empty($dbms)) ? 'dbal_' . basename($dbms) : 'dbal';
//----------------------------------------------------------------------------------------------------|
//----------------------------------------------------------------------------------------------------|
/**
 * class dbal
 */
class Database extends dbal
{
    var $multi_insert = true;
    public function sql_connect($sqlserver, $sqluser, $sqlpassword, $database, $port = false, $persistency = false, $new_link = false)
    {
        $this->persistency = $persistency;
        $this->user = $sqluser;
        $this->server = $sqlserver . (($port) ? ':' . $port : '');
        $this->dbname = $database;
        $this->sql_layer = 'mysql4';
        $this->db_connect_id = ($this->persistency) ? @mysql_pconnect($this->server, $this->user, $sqlpassword) : @mysql_connect($this->server, $this->user, $sqlpassword, $new_link);
        if ($this->db_connect_id && $this->dbname != '')
        {
            if (@mysql_select_db($this->dbname, $this->db_connect_id))
            {
                if (version_compare($this->sql_server_info(true), '4.1.0', '>='))
                {
                    @mysql_query("SET NAMES 'utf8'", $this->db_connect_id);
                    if (version_compare($this->sql_server_info(true), '5.0.2', '>='))
                    {
                        $result = @mysql_query('SELECT @@session.sql_mode AS sql_mode', $this->db_connect_id);
                        $row = @mysql_fetch_assoc($result);
                        @mysql_free_result($result);
                        $modes = array_map('trim', explode(',', $row['sql_mode']));
                        if (!in_array('TRADITIONAL', $modes))
                        {
                            if (!in_array('STRICT_ALL_TABLES', $modes))
                            {
                                $modes[] = 'STRICT_ALL_TABLES';
                            }
                            if (!in_array('STRICT_TRANS_TABLES', $modes))
                            {
                                $modes[] = 'STRICT_TRANS_TABLES';
                            }
                        }
                        $mode = implode(',', $modes);
                        @mysql_query("SET SESSION sql_mode='{$mode}'", $this->db_connect_id);
                    }
                }
                else if (version_compare($this->sql_server_info(true), '4.0.0', '<'))
                {
                    $this->sql_layer = 'mysql';
                }
                return $this->db_connect_id;
            }
        }
        return $this->sql_error('');
    }
    public function sql_server_info($raw = false, $use_cache = true)
    {
        if ($use_cache)
        {
            $result = @mysql_query('SELECT VERSION() AS version', $this->db_connect_id);
            $row = @mysql_fetch_assoc($result);
            @mysql_free_result($result);
            $this->sql_server_version = $row['version'];
            if (!empty($cache) && $use_cache)
            {
                $cache->put('mysql_version', $this->sql_server_version);
            }
        }
        return ($raw) ? $this->sql_server_version : 'MySQL ' . $this->sql_server_version;
    }
    function _sql_transaction($status = 'begin')
    {
        switch ($status)
        {
            case 'begin':
                return @mysql_query('BEGIN', $this->db_connect_id);
                break;

            case 'commit':
                return @mysql_query('COMMIT', $this->db_connect_id);
                break;

            case 'rollback':
                return @mysql_query('ROLLBACK', $this->db_connect_id);
                break;
        }

        return true;
    }
    public function sql_query($query = '')
    {
        if ($query != '')
        {
            $this->query_result = false;
            $this->sql_add_num_queries($this->query_result);
            if ($this->query_result === false)
            {
                if (($this->query_result = @mysql_query($query, $this->db_connect_id)) === false)
                {
                    $this->sql_error($query);
                }
                else if (strpos($query, 'SELECT') === 0 && $this->query_result)
                {
                    $this->open_queries[(int) $this->query_result] = $this->query_result;
                }
            }
            else if (defined('DEBUG_EXTRA'))
            {
                $this->sql_report('fromcache', $query);
            }
        }
        else
        {
            return false;
        }
        return $this->query_result;
    }
    function _sql_query_limit($query, $total, $offset = 0)
    {
        $this->query_result = false;
        if ($total == 0)
        {
            $total = '18446744073709551615';
        }
        $query .= "\n LIMIT " . ((!empty($offset)) ? $offset . ', ' . $total : $total);
        return $this->sql_query($query);
    }
    public function sql_affectedrows()
    {
        return ($this->db_connect_id) ? @mysql_affected_rows($this->db_connect_id) : false;
    }
    public function sql_fetchrow($query_id = false)
    {
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        return ($query_id !== false) ? @mysql_fetch_assoc($query_id) : false;
    }
    public function sql_rowseek($rownum, &$query_id)
    {
        global $cache;
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        if (isset($cache->sql_rowset[$query_id]))
        {
            return $cache->sql_rowseek($rownum, $query_id);
        }
        return ($query_id !== false) ? @mysql_data_seek($query_id, $rownum) : false;
    }
    public function sql_nextid()
    {
        return ($this->db_connect_id) ? @mysql_insert_id($this->db_connect_id) : false;
    }
    public function sql_freeresult($query_id = false)
    {
        if ($query_id === false)
        {
            $query_id = $this->query_result;
        }
        if (isset($this->open_queries[(int) $query_id]))
        {
            unset($this->open_queries[(int) $query_id]);
            return @mysql_free_result($query_id);
        }
        return false;
    }
    public function sql_escape($msg)
    {
        if (!$this->db_connect_id)
        {
            return @mysql_real_escape_string($msg);
        }
        return @mysql_real_escape_string($msg, $this->db_connect_id);
    }
    public function get_estimated_row_count($table_name)
    {
        $table_status = $this->get_table_status($table_name);

        if (isset($table_status['Engine']))
        {
            if ($table_status['Engine'] === 'MyISAM')
            {
                return $table_status['Rows'];
            }
            else if ($table_status['Engine'] === 'InnoDB' && $table_status['Rows'] > 100000)
            {
                return '~' . $table_status['Rows'];
            }
        }
        return parent::get_row_count($table_name);
    }
    public function get_row_count($table_name)
    {
        $table_status = $this->get_table_status($table_name);

        if (isset($table_status['Engine']) && $table_status['Engine'] === 'MyISAM')
        {
            return $table_status['Rows'];
        }
        return parent::get_row_count($table_name);
    }
    public function get_table_status($table_name)
    {
        $sql = "SHOW TABLE STATUS
			LIKE '" . $this->sql_escape($table_name) . "'";
        $result = $this->sql_query($sql);
        $table_status = $this->sql_fetchrow($result);
        $this->sql_freeresult($result);
        return $table_status;
    }
    function _sql_like_expression($expression)
    {
        return $expression;
    }
    function _sql_custom_build($stage, $data)
    {
        switch ($stage)
        {
            case 'FROM':
                $data = '(' . $data . ')';
                break;
        }

        return $data;
    }
    function _sql_error()
    {
        if (!$this->db_connect_id)
        {
            return array(
                'message'	=> @mysql_error(),
                'code'		=> @mysql_errno()
            );
        }
        return array(
            'message'	=> @mysql_error($this->db_connect_id),
            'code'		=> @mysql_errno($this->db_connect_id)
        );
    }
    function _sql_close()
    {
        return @mysql_close($this->db_connect_id);
    }
}
//----------------------------------------------------------------------------------------------------|
//----------------------------------------------------------------------------------------------------|
?>