<?php

    class Add{
        private $conn;
        private $tbl;

        public function  __construct($conn, $tbl) {
            $this->conn = $conn;
            $this->tbl = $tbl;
        }
        public function addQuery($data){
            $tbl_columns = implode(",",array_keys($data));
            $tbl_values = implode("','",$data);
            $sql = "INSERT INTO $this->tbl($tbl_columns) VALUES ('$tbl_values')";
            return $this->conn->query($sql);
        }
    }
?>