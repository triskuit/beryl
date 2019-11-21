<?php

class DatabaseObject
{

    protected static $database;

    protected static $db_name = "Joro";

    protected static $table_name = "";

    protected static $db_columns = [];
    
    protected static $read_only = [];

    public $errors = [];

    static public function set_database($database)
    {
        self::$database = $database;
    }
    
    static public function find_by_sql($sql, $args = [])
    {
        // make sure args is an array. Puts single values
        // into an array if passed in.
        $args = is_array($args) ? $args : [$args];
        $statement = self::$database->prepare($sql);
        $result = $statement->execute($args);
        
        // $result contains the true/false on query success
        // $statement contains the actual result data
        if (! $result) {
            exit("Database query failed");
        }

        // put results into object
        $object_array = [];
        while ($record = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object_array[] = static::instantiate($record);
        }
        
        $statement->closeCursor();
        return $object_array;
    }

    static public function find_all(Pagination $pagination = null)
    {
        if($pagination){
            $sql = "SELECT * FROM " . static::$table_name;
            $sql .= " LIMIT ? OFFSET ?;";
            $args = [$pagination->page_per, $pagination->offset()];
            return static::find_by_sql($sql, $args);
        } else {
            $sql = "SELECT * FROM " . static::$table_name;
            return static::find_by_sql($sql);
        }
    }

    static public function count_all()
    {
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $result_set = self::$database->query($sql);
        $row = $result_set->fetch();
        return array_shift($row);
    }

    static public function find_by_id($id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE id=?;";
        $obj_array = static::find_by_sql($sql, [$id]);
        if (! empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    static protected function instantiate($record)
    {
        $object = new static();
        foreach ($record as $property => $value) {
            if (property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
        return $object;
    }

    protected function validate()
    {
        $this->errors = [];
        return $this->errors;
    }

    protected function create()
    {
        $this->validate();
        if (! empty($this->errors)) {
            return false;
        }

        $attributes = $this->attributes();
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES (";
        $sql .= str_repeat("?,", count(array_values($attributes)) - 1) . "?";
        $sql .= ")";

//         echo $sql . "<br />";
//         print_r(array_values($attributes));
//         exit();

        $statement = self::$database->prepare($sql);
        $result = $statement->execute(array_values($attributes));
        if ($result) {
            $this->id = self::$database->lastInsertId();
        }

        return $result;
    }

    protected function update()
    {
        $this->validate();
        if (! empty($this->errors)) {
            return false;
        }
        $attributes = $this->attributes();
        $attribute_pairs = [];
        $args = [];
        foreach ($attributes as $key => $value) {
            if(!is_null($value) && $value != ''){
                $attribute_pairs[] = "{$key}=?";
                array_push($args, $value);
            }
        }
        array_push($args,$this->id);
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(', ', $attribute_pairs);
        $sql .= " WHERE id=? LIMIT 1;";
        
//         echo $sql . "<br />";
//         print_r($args);
//         exit;
        
        $statement = self::$database->prepare($sql);
        $result = $statement->execute($args);
        return $result;
    }

    public function save()
    {
        if ($this->id > 0) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    public function merge_attributes($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && ! is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    public function attributes()
    {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if (!in_array($column, static::$read_only)) {
                $attributes[$column] = $this->$column;
            }
        }
        return $attributes;
    }

    public function delete()
    {
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE id = ? ";
        $sql .= "LIMIT 1";
        $statement = self::$database->prepare($sql);
        $result = $statement->execute([$this->id]);
        return $result;
    }
}
