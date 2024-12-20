<?php

    class Delete{
        private $conn;
        private $tbl;
        private $id;

        public function __construct($conn, $tbl, $id){
            $this->conn = $conn;
            $this->tbl = $tbl;
            $this->id = $id;
        }
        public function deleteQuery(){
            $primary_key = array_keys($this->id)[0];
            $key_value = $this->id[$primary_key];
            $sql = "DELETE FROM $this->tbl WHERE $primary_key=$key_value";
            return $this->conn->query($sql);
        }
    }
?>