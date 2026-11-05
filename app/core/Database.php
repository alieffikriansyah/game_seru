<?php

class Database {
    private static $instance = null;
    private $pdo;
    
    // Query Builder Components
    private $selects = ['*'];
    private $from = '';
    private $joins = [];
    private $wheres = [];
    private $likes = [];
    private $group_by = [];
    private $havings = [];
    private $order_by = [];
    private $limit = null;
    private $offset = null;
    
    private $params = []; 
    private $stmt = null;
    private $last_query = '';

    private function __construct() {
        $host = Env::get('DB_HOST', 'localhost');
        $db   = Env::get('DB_NAME', 'game_seru');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASS', '');
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException("Database Connection Error: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function get_pdo() {
        return $this->pdo;
    }

    private function _reset_builder() {
        $this->selects = ['*'];
        $this->from = '';
        $this->joins = [];
        $this->wheres = [];
        $this->likes = [];
        $this->group_by = [];
        $this->havings = [];
        $this->order_by = [];
        $this->limit = null;
        $this->offset = null;
        $this->params = [];
    }

    public function query($sql, $params = []) {
        $this->last_query = $sql;
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this; 
    }
    
    public function result_array() {
        if ($this->from !== '') $this->get();
        if ($this->stmt) return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return [];
    }
    
    public function row_array() {
        if ($this->from !== '') $this->get('', 1);
        if ($this->stmt) {
            $res = $this->stmt->fetch(PDO::FETCH_ASSOC);
            return $res ? $res : null;
        }
        return null;
    }
    
    public function result() {
        if ($this->from !== '') $this->get();
        if ($this->stmt) return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        return [];
    }
    
    public function fetchAll($mode = PDO::FETCH_OBJ) {
        if ($this->stmt) return $this->stmt->fetchAll($mode);
        return [];
    }
    
    public function fetch($mode = PDO::FETCH_OBJ) {
        if ($this->from !== '') $this->get('', 1);
        if ($this->stmt) return $this->stmt->fetch($mode);
        return null;
    }
    
    public function row() {
        return $this->fetch();
    }
    
    public function num_rows() {
        if ($this->stmt) return $this->stmt->rowCount();
        return 0;
    }

    // --- Query Builder Methods ---
    
    public function select($select = '*') {
        if ($select !== '*') {
            if ($this->selects === ['*']) $this->selects = [];
            if (is_array($select)) {
                $this->selects = array_merge($this->selects, $select);
            } else {
                $this->selects[] = $select;
            }
        }
        return $this;
    }

    public function from($from) {
        $this->from = $from;
        return $this;
    }
    
    public function table($table) {
        $this->_reset_builder();
        $this->from = $table;
        return $this;
    }

    public function join($table, $cond, $type = 'INNER') {
        $this->joins[] = strtoupper($type) . " JOIN $table ON $cond";
        return $this;
    }

    public function where($field, $value = null, $operator = '=') {
        if (is_array($field)) {
            foreach ($field as $k => $v) {
                if (is_int($k)) {
                    $this->wheres[] = ['AND', $v];
                } else {
                    $this->where($k, $v);
                }
            }
            return $this;
        }
        
        if ($value === null && strpos($field, ' ') !== false) {
             $this->wheres[] = ['AND', $field];
             return $this;
        }
        
        $this->wheres[] = ['AND', "$field $operator ?"];
        $this->params[] = $value;
        return $this;
    }

    public function or_where($field, $value = null, $operator = '=') {
        if (is_array($field)) {
            foreach ($field as $k => $v) {
                $this->or_where($k, $v);
            }
            return $this;
        }
        
        if ($value === null && strpos($field, ' ') !== false) {
             $this->wheres[] = ['OR', $field];
             return $this;
        }
        
        $this->wheres[] = ['OR', "$field $operator ?"];
        $this->params[] = $value;
        return $this;
    }
    
    public function where_in($field, $values) {
        if (!is_array($values)) $values = [$values];
        if (empty($values)) {
            $this->wheres[] = ['AND', "1=0"];
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = ['AND', "$field IN ($placeholders)"];
        foreach ($values as $v) $this->params[] = $v;
        return $this;
    }
    
    public function where_not_in($field, $values) {
        if (!is_array($values)) $values = [$values];
        if (empty($values)) {
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = ['AND', "$field NOT IN ($placeholders)"];
        foreach ($values as $v) $this->params[] = $v;
        return $this;
    }
    
    public function like($field, $match = '', $side = 'both') {
        if (is_array($field)) {
            foreach($field as $k => $v) {
                $this->like($k, $v, $side);
            }
            return $this;
        }
        
        $v = $match;
        if ($side === 'before') $v = "%$match";
        elseif ($side === 'after') $v = "$match%";
        elseif ($side === 'both') $v = "%$match%";
        
        $this->wheres[] = ['AND', "$field LIKE ?"];
        $this->params[] = $v;
        return $this;
    }
    
    public function or_like($field, $match = '', $side = 'both') {
         if (is_array($field)) {
            foreach($field as $k => $v) {
                $this->or_like($k, $v, $side);
            }
            return $this;
        }
        
        $v = $match;
        if ($side === 'before') $v = "%$match";
        elseif ($side === 'after') $v = "$match%";
        elseif ($side === 'both') $v = "%$match%";
        
        $this->wheres[] = ['OR', "$field LIKE ?"];
        $this->params[] = $v;
        return $this;
    }
    
    public function group_start() {
        $this->wheres[] = ['AND', '('];
        return $this;
    }
    
    public function group_end() {
        $this->wheres[] = ['', ')'];
        return $this;
    }

    public function group_by($by) {
        if (is_array($by)) {
            foreach ($by as $b) {
                $this->group_by[] = $b;
            }
        } else {
            $this->group_by[] = $by;
        }
        return $this;
    }
    
    public function having($field, $value = null) {
        if ($value === null && strpos($field, ' ') !== false) {
             $this->havings[] = $field;
             return $this;
        }
        $this->havings[] = "$field = ?";
        $this->params[] = $value;
        return $this;
    }

    public function order_by($orderby, $direction = 'ASC') {
        $this->order_by[] = "$orderby $direction";
        return $this;
    }

    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        if ($offset !== null) {
            $this->offset = $offset;
        }
        return $this;
    }
    
    public function get_where($table = '', $where = null, $limit = null, $offset = null) {
        if ($table !== '') $this->from($table);
        if ($where !== null) $this->where($where);
        if ($limit !== null) $this->limit($limit, $offset);
        return $this->get();
    }
    
    private function _compile_select() {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM " . $this->from;
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                
                if ($cond === '(') {
                    if (!$first) $sql .= " $op ";
                    $sql .= "(";
                    $first = true; 
                } elseif ($cond === ')') {
                    $sql .= ")";
                    $first = false;
                } else {
                    if (!$first) $sql .= " $op ";
                    $sql .= $cond;
                    $first = false;
                }
            }
        }
        
        if (!empty($this->group_by)) {
            $sql .= " GROUP BY " . implode(', ', $this->group_by);
        }
        
        if (!empty($this->havings)) {
            $sql .= " HAVING " . implode(' AND ', $this->havings);
        }
        
        if (!empty($this->order_by)) {
            $sql .= " ORDER BY " . implode(', ', $this->order_by);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT " . (int)$this->limit;
            if ($this->offset !== null) {
                $sql .= " OFFSET " . (int)$this->offset;
            }
        }
        
        return $sql;
    }

    public function get($table = '', $limit = null, $offset = null) {
        if ($table !== '') $this->from($table);
        if ($limit !== null) $this->limit($limit, $offset);
        
        $sql = $this->_compile_select();
        $params = $this->params;
        
        $this->query($sql, $params);
        $this->_reset_builder(); 
        return $this;
    }
    
    public function insert($table = '', $data = []) {
        if (is_array($table)) {
            $data = $table;
            $table = $this->from;
        } elseif ($table !== '') {
            $this->from($table);
        }
        
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO " . $table . " (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";

        $this->query($sql, array_values($data));
        $id = $this->pdo->lastInsertId();
        $this->_reset_builder();
        return $id;
    }

    public function last_insert_id() {
        return $this->pdo->lastInsertId();
    }

    public function insert_id() {
        return $this->pdo->lastInsertId();
    }

    public function update($table = '', $data = null, $where = null) {
        if (is_array($table)) {
            $data = $table;
            $table = $this->from;
        } elseif ($table !== '') {
            $this->from($table);
        }
        
        if ($where !== null) $this->where($where);
        
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "`$key` = ?";
            $values[] = $value;
        }

        $sql = "UPDATE " . $table . " SET " . implode(', ', $fields);

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                if (!$first) $sql .= " $op ";
                $sql .= $cond;
                $first = false;
            }
            $values = array_merge($values, $this->params);
        }

        $this->query($sql, $values);
        $this->_reset_builder();
        return $this->stmt->rowCount();
    }

    public function delete($table = '', $where = null) {
        if ($table === '') $table = $this->from;
        if ($where !== null) $this->where($where);
        
        $where_sql = "";
        if (!empty($this->wheres)) {
            $where_sql = " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                if (!$first) $where_sql .= " $op ";
                $where_sql .= $cond;
                $first = false;
            }
        }

        $sql = "DELETE FROM " . $table . $where_sql;
        $this->query($sql, $this->params);
        $this->_reset_builder();
        return $this->stmt->rowCount();
    }
    
    public function count($table = '') {
        if ($table !== '') $this->from($table);
        $this->selects = ['COUNT(*) as total'];
        $this->get();
        $row = $this->row();
        return $row ? (int)$row->total : 0;
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
