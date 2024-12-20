<?php

    class Update{
        private $conn;
        private $tbl;
        private $id;

        public function __construct($conn, $tbl, $id){
            $this->conn = $conn;
            $this->tbl = $tbl;
            $this->id = $id;
        }
        public function updateQuery($data){
            $update = "";
            foreach($data as $key => $value){
                $update .= "$key='$value', ";
            }
            $update = substr($update, 0, -1); 
            $primary_key = array_keys($this->id)[0]; 
            $key_value = $this->id[$primary_key]; 
            $sql = "UPDATE $this->tbl SET $update WHERE $primary_key = $key_value";
            return $this->conn->query($sql);
        }
        
    }
?>