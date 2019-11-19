<?php

class receipt_log extends DatabaseObject
{

    protected static $table_name = "receipt_log";

    protected static $db_columns = [
        'id',
        'project_name',
        'block_number',
        'date_created',
        'date_received',
        'created_by',
        'delivered_by',
        'block_name',
        'received_status',
        'wrike_id'
    ];

    // sample properties
    public $id, $project_name, $block_number, $date_created, $date_received, $created_by, $delivered_by, $block_name, $received_status, $wrike_id;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? 0;
        
        $this->block_number = $args['block_number'] ?? null;
        $this->project_name = $args['project_name'] ?? null;

        $this->date_created = $args['date_created'] ?? date("Y-m-d H:i:s");
        $this->date_received = $args['date_received'] ?? null;

        $this->created_by = $args['created_by'] ?? null;
        $this->delivered_by = $args['delivered_by'] ?? null;
        
        $this->wrike_id = $args['wrike_id'] ?? null;

    }

    static public function find($inputs = [], Pagination $pagination = null)
    {
        $args = [];
        $sql = "SELECT * FROM " . static::$table_name;

        if (isset($inputs['q'])) {
            // handle query terms
            $sql .= " WHERE UPPER(block_name) LIKE UPPER(?)";
            $q = "%" . $inputs['q'] . "%";
            array_push($args, $q);
        } 
        
        if (isset($inputs['manually_entered']) && $inputs['manually_entered'] == "on"){
            // find only blocks created by Greenhouse
            $sql.= strpos($sql,"WHERE") > 0 ? " AND" : " WHERE";
            $sql .= " created_by = 'Greenhouse'";
        } else {
            $sql.= strpos($sql,"WHERE") > 0 ? " AND" : " WHERE";
            $sql .= " received_status = ?";
            isset($inputs['received']) && $inputs['received'] == "on"? array_push($args, 1): array_push($args, 0);
        }
        // handle pagination parameters
        if ($pagination) {
            $sql .= " LIMIT ? OFFSET ?;";
            array_push($args, $pagination->page_per, $pagination->offset());
        }
        
        // echo $sql;
        return static::find_by_sql($sql, $args);
    }
    
    static public function count_all($inputs = [])
    {
        $args = [];
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;

        if (isset($inputs['q'])) {
            // handle query terms
            $sql .= " WHERE UPPER(block_name) LIKE UPPER(%s)";
            $q = "'%" . $inputs['q'] . "%'";
            array_push($args, $q);
        }
        
        if (isset($inputs['manually_entered']) && $inputs['manually_entered'] == "on"){
            // find only blocks created by Greenhouse
            $sql.= strpos($sql,"WHERE") > 0 ? " AND" : " WHERE";
            $sql .= " created_by = 'Greenhouse'";
        } else {
            $sql.= strpos($sql,"WHERE") > 0 ? " AND" : " WHERE";
            $sql .= " received_status = %d";
            isset($inputs['received']) && $inputs['received'] == "on" ? array_push($args, 1): array_push($args, 0);
        }
        
        $sql = vsprintf($sql, $args);
        $result_set = self::$database->query($sql);
        $arr = $result_set->fetch();
        return array_shift($arr);
    }
    
    protected function validate()
    {
        $this->errors = [];
        
        if (is_blank($this->project_name) && $this->id < 1) {
            $this->errors[] = "Name cannot be blank.";
        } elseif (! $this::is_unique()) {
            $this->errors[] = "Block already exists.";
        }
    }
    
    protected function is_unique(){
        $return = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE project_name = ? AND block_number = ?", [$this->project_name, $this->block_number]);
        
        if ($return){
            return false;
        } else {
            return true;
        }
    }
    
    public function attributes()
    {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if ($column == 'id' || $column == 'block_name' || $column == 'received_status') {
                continue;
            }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }
    
    static function format_date(string $date)
    {
        $date = new DateTime($date);
        return $date->format('M d');
    }
    
    public function is_project_complete()
    {
        if(static::find_by_id($this->id))
        {
            $args = [];
            $sql = "SELECT * FROM " . static::$table_name;
            $sql .= " WHERE project_name = ? ";
            array_push($args, $this->project_name);
            $sql .= "AND delivered_by IS NULL;";
            
            $request = static::find_by_sql($sql, $args);
            return $request ? false : true;
        }
        return false;
    }
    
}