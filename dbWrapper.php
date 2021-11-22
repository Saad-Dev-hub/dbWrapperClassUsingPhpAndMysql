<?php


// usage:
// select statement 
// $db = new dbWrapper('localhost', 'root', '', 'test');   // create a new instance of the DB class with the given parameters
// $db->select('users', ['*'])->getAll();             // select all rows from the users table
// $db->select('users',['id','name'])->getRow();       // select a single row from the users table
//$db->select('users',['id','name'])->where('id','=','1')->getAll(); // select all rows from the users table where id = 1
//$db->select('users',['id','name'])->where('id','=','1')->andWhere('name','=','John')->getAll(); // select all rows from the users table where id = 1 and name = John
///////////////////////////////////////////
// insert,update and delete statements
// $db->insert('users', ['name'], ['John'])->execute(); // insert a row into the users table with name = John
//$db->update('categories', ['name' => 'test3'])->where('id', '=', '9')->execute(); // update the name of the category with id = 9 to test3
//$db->delete('categories')->where('id', '=', '9')->execute(); // delete the category with id = 9
///////////////////////////////////////////
// join statements
//$db->select('products',['products.name AS Products','categories.name'])->innerJoin('categories','products.category_id','categories.id')->getAll(); // select all rows from the products table and join the categories table on the category_id column of the products table
//$db->select('products',['products.name AS Products','categories.name'])->leftJoin('categories','products.category_id','categories.id')->getAll(); // select all rows from the products table that have categories and not 
//$db->select('products',['products.name AS Products','categories.name'])->rightJoin('categories','products.category_id','categories.id')->getAll(); // select all rows from the categories table that have products and not  



class dbWrapper
{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $con;
    private $sql;
    public function __construct($host, $user, $pass, $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->con = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->con->connect_errno) {
            die('Failed to connect to MySQL: ' . $this->con->connect_error);
        }
    }
    public function execute()
    {
        $result = $this->con->query($this->sql);
        if ($this->con->affected_rows > 0) {
            return $result;
        } else {
            echo "Cannot Insert or Update or Delete ,Please Check Your Constraints".'<br/>'. $this->sql;
            
        }
    }
    public function select($tableName, $columns)
    {
        $selectedColumns = '';
        foreach ($columns as $column) {
            $selectedColumns .= $column . ',';
        }
        $selectedColumns = implode(',', $columns);
        $selectedColumns = rtrim($selectedColumns, ',');
        $this->sql = "SELECT $selectedColumns FROM `$tableName`";
        return ($this);
    }
    public function getAll()
    {
        $result = $this->execute();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function getRow()
    {
        $result = $this->execute();
        $row = $result->fetch_assoc();
        return $row;
    }
    public function insert($tableName, $columns, $values)
    {
        $columns = implode(',', $columns);
        $val = '';
        foreach ($values as $value) {
            $val .= "'" . $value . "',";
        }
        $val = rtrim($val, ',');
        $this->sql = "INSERT INTO `$tableName` ($columns) VALUES ($val) ";
        return ($this);
    }
    public function update($tableName, $columns)
    {
        $statement = '';
        foreach ($columns as $column => $value) {
            $statement .= "$column = '$value',";
        }
        $statement = rtrim($statement, ','); {
            $this->sql = "UPDATE `$tableName` SET $statement";
            return ($this);
        }
    }
    public function where($column, $operator, $value)
    {
        $this->sql .= " WHERE `$column` $operator '$value'";
        // echo $this->sql;die;
        return ($this);
    }
    public function andWhere($column, $operator, $value)
    {
        $this->sql .= " AND `$column` $operator '$value'";
        return ($this);
    }
    public function delete($tableName)
    {
        $this->sql = "DELETE FROM `$tableName`";
        return ($this);
    }
    public function innerJoin($tableName, $primaryKey, $foreignKey)
    {
        $this->sql .= " JOIN `$tableName` ON $primaryKey = $foreignKey";
        //echo $this->sql;die;
        return ($this);
    }
    public function leftJoin($tableName, $primaryKey, $foreignKey)
    {
        $this->sql .= " LEFT JOIN `$tableName` ON $primaryKey = $foreignKey";
        return ($this);
    }
    public function rightJoin($tableName, $primaryKey, $foreignKey)
    {
        $this->sql .= " RIGHT JOIN `$tableName` ON $primaryKey = $foreignKey";
        return ($this);
    }
    public function orderBy($column, $order)
    {
        $this->sql .= " ORDER BY `$column` $order";
        return ($this);
    }

    public function limit($limit)
    {
        $this->sql .= " LIMIT $limit";
        return ($this);
    }
    public function __destruct()
    {
        $this->con->close();
    }
}

?>