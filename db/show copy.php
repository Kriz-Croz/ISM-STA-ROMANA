<?php

    class Show{

        private $conn;
        private $tbl;

        public function __construct($conn, $tbl){
            $this->conn = $conn; 
            $this->tbl = $tbl;
        }

        public function sqlExecute($sql){
            $result = $this->conn->query($sql);
            $data = array();
            if($result->num_rows > 0){
                while($rows = $result->fetch_assoc()){ 
                    $data_row = array();
                    foreach ($rows as $row) {
                        array_push($data_row, $row);
                    }                
                    array_push($data,$data_row);
                }
            }
            return $data;
        }

        public function showRecords($where=null, $order=null, $limit=null){
            $sql = "SELECT * FROM $this->tbl"; 
            if($where!=null){
                $sql.=" WHERE $where"; 
            }
            if($order != null){
                $sql .= " ORDER BY $order" ; 
            }
            if($limit!=null){
                $sql .= " LIMIT $limit"; 
            }
            return $this->sqlExecute($sql);
        }

        public function showRecordsWithPagination($page = 1, $where =null, $limit = NULL, $order = null) {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT COUNT(*) AS total FROM $this->tbl";
            $result = $this->conn->query($sql);
            $totalRecords = $result->fetch_assoc()['total'];
            $totalPages = ceil($totalRecords / $limit);
            if($order != null && $where==null){
                $sql = "SELECT * FROM $this->tbl ORDER BY $order LIMIT $limit OFFSET $offset";
            } elseif ($where != null && $order == null) {
                $sql = "SELECT * FROM $this->tbl WHERE $where LIMIT $limit OFFSET $offset";
            } elseif ($where != null && $order != null) {
                $sql = "SELECT * FROM $this->tbl WHERE $where ORDER BY $order LIMIT $limit OFFSET $offset";
            } else {
                $sql = "SELECT * FROM $this->tbl LIMIT $limit OFFSET $offset";
            }
            $records = $this->sqlExecute($sql);
            return array('records' => $records, 'totalPages' => $totalPages);
        }

    }
?>