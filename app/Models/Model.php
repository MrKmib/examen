<?php

namespace App\Models;

use mysqli;
class Model{
    protected $db_host = DB_HOST;
    protected $db_user = DB_USER;
    protected $db_pass = DB_PASS;
    protected $db_name =DB_NAME;
    protected $connection;
    protected $query;
    protected $sql, $data = [], $params = null;
    protected $select = '*';

    protected $where, $values = [];
    protected $orderBy = '';
    protected $table;

    public function __construct(){
        $this->connection();
    }

    public function connection(){
        $this->connection=new mysqli($this->db_host, $this->db_user, $this->db_pass,$this->db_name);
        if($this->connection->connect_error){
            die('Erro de conection'.$this->connection->connect_error);

        }
    }
    public function query($sql,$data=[],$params=null){
         if($data){
            if($params==null){
                $params =str_repeat('s', count($data));
            }
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param($params,...$data);
            $stmt->execute();
            $this->query = $stmt->get_result();
         }else{
            $this->query= $this->connection->query($sql);
         }
        // $this->query= $this->connection->query($sql);
        return $this;
    }
    public function select(...$columns){
        $this->select = implode(', ', $columns);
        return $this;
    }
    public function where($column,$operador,$value=null){
        if($value == null){
            $value = $operador;
            $operador ='=';
            
        }
        
        /*----old consulta-- */
        if(empty($this->sql)){
            //SELECT *FROM contacts WHERE name = 'juan'
            $this->sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} WHERE {$column} {$operador} ?";
                    
        }else{
            //sql concatenado con ""
            $this->sql .= " AND {$column} {$operador} ?";   
        }
        $this->data[]=$value;
        /*---end old consult----*/

        // if($this->where){
        //     $this->where .= " AND {$column} {$operador} ?";
        // }else{
        //     $this->where = "{$column} {$operador} ?";
        // }
        // $this->$value [] = $value;

        
        return $this;
    }
    public function orderBy($column, $order = 'ASC'){
        /*--old consulta */
        // if(empty($this->orderBy)){
        //     // el espacio antes del ORDER BY: es vital
        //     $this->orderBy = " ORDER BY {$column} {$order}";
        // }else{
        //     $this->orderBy .= ", {$column} {$order}";
        // }
        /*---end old consulta--- */


        if($this->orderBy){
            // el espacio antes del ORDER BY: es vital
            $this->orderBy .= ", {$column} {$order}";
        }else{
            $this->orderBy = "{$column} {$order}";
        }

        
        return $this;
    }

    public function first(){
        if(empty($this->query)){

            /*----old consult--*/
            // $sql = "SELECT {$this->select} FROM {$this->table}";
            // if($this->where){
            //     $sql .= " WHERE {$this->where}";
            // }
            // if($this->orderBy){
            //     $sql .= " ORDER BY {$this->orderBy}";
            // }
            // $this->query($sql, $this->values);
            /*----------- */
            // if(empty($this->sql)){
            //     $this->sql = "SELECT *FROM {$this->table}";
            // }

            // $this->sql .= $this->orderBy;
            // $this->query($this->sql, $this->data, $this->params);
            /*-----end old concult--*/

            $sql = "SELECT {$this->select} FROM {$this->table}";
            if($this->where){
                $sql .= " WHERE {$this->where}";
            }
            if($this->orderBy){
                $sql .= " ORDER BY {$this->orderBy}";
            }
            $this->query($sql, $this->values);

        }

        return $this->query->fetch_assoc();
    }
    public function get(){
        if(empty($this->query)){

            /*--old consulta-*/
            // if(empty($this->sql)){
            //     $this->sql = "SELECT *FROM {$this->table}";
            // }

            // $this->sql .= $this->orderBy;
            // $this->query($this->sql, $this->data, $this->params);
            /*--end conulta------*/

            $sql = "SELECT {$this->select} FROM {$this->table}";
            if($this->where){
                $sql .= " WHERE {$this->where}";
            }
            if($this->orderBy){
                $sql .= " ORDER BY {$this->orderBy}";
            }
            $this->query($sql, $this->values);

        }
        return $this->query->fetch_all(MYSQLI_ASSOC);
    }


    //consultas
    public function all(){
        //SELECT *FROM contacts
        $sql = "SELECT *FROM {$this->table}";
        return $this->query($sql)->get();
    }


    public function paginate($cant =15){
        $page =isset($_GET['page']) ? $_GET['page'] : 1;
        
        
        /*---old consult ----*/
        if($this->sql){
            $sql = $this->sql . ($this->orderBy ?? '') . " LIMIT " . ($page-1) * $cant . ",{$cant}";
            $data = $this->query($sql, $this->data, $this->params)->get();
        }else{
            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} " . ($this->orderBy ?? '') . " LIMIT " . ($page-1)*$cant . ",{$cant}";
            //return $sql;
            //ejecuta la sentencia
            $data = $this->query($sql)->get();
        }
        /*----end old consult---*/
        
        /*---------new consulta-----*/
        // if(empty($this->query)){
        //     $sql = "SELECT {$this->select} FROM {$this->table}";
        //     if($this->where){
        //         $sql .= " WHERE {$this->where}";
        //     }
        //     if($this->orderBy){
        //         $sql .= " ORDER BY {$this->orderBy}";
        //     }
        //     $sql .= " LIMIT " . ($page-1) * $cant . ", {$cant}";
        //     //return $sql;
        //     $data = $this->query($sql, $this->values)->get();

        // }
        /*---------end new consulta-----*/
        
        
        
        
        
        
        $total = $this->query('SELECT FOUND_ROWS() AS total')->first()['total'];
        
        $uri=$_SERVER['REQUEST_URI'];
        $uri=trim($uri,'/');
        if(strpos($uri,'?')){
            $uri = substr($uri,0,strpos($uri,'?'));
        }
        $last_page = ceil($total /$cant);
         //return '/'. $uri . '?page=' . $page-1;
        //return ceil($last_page);
        return [
            'from'=>($page-1)*$cant+1,
            'to' =>($page-1)*$cant+count($data),
            'current_page'=>$page,
            'last_page'=>$last_page,
            'total' => $total,
            'next_page_url'=>$page < $last_page? '/'. $uri . '?page=' . $page+1: null,
            'prev_page_url'=>$page > 1? '/'. $uri . '?page=' . $page-1: null,
            'data' => $data
            
        ];

    }
    public function find($id){
        //SELECT *FROM contacts WHERE id=1
        $sql = "SELECT * FROM {$this->table} WHERE id=?";
        return $this->query($sql, [$id],'i')->first();
    }
    
   
    //insertar registros
    public function create($data){
        //INSERT INTO contacts (name,email,phone) VALUES (?,?,?)
        $columns = array_keys($data);
        $columns = implode(',',$columns);
        $values = array_values($data);
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES (".str_repeat('?, ',count($values)-1)."?)";
        $this->query($sql, $values);
        //INSERT INTO contacts (name,email,phone) VALUES ('','','')
        // $columns = array_keys($data);
        // $columns = implode(',',$columns);

        // $values = array_values($data);
        // $values = "'".implode("','",$values)."'";

        // $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        // $this->query($sql);


        $insert_id = $this->connection->insert_id;

        return $this->find($insert_id);

    }
    public function update($id,$data){
        //UPDATE contacts SET name=?, email=?,phone=? WHERE id=1

        //UPDATE contacts SET name='', email='',phone='' WHERE id=''
        $fields=[];
        foreach ($data as $key =>$value) {
            $fields[] ="{$key} =?";
        }
        $fields = implode(', ',$fields);

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id=?";
        $values = array_values($data);
        $values[]=$id;

        $this->query($sql, $values);
        return $this->find($id);
    }
    public function delete($id){
        //DELETE FROM contacts WHERE id=1
        $sql = "DELETE FROM {$this->table} WHERE id =?";
        //ejecutamos 
        $this->query($sql, [$id],'i');
    }
    
    
}