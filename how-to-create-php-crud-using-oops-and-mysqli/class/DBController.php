<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "crud_example";
    private $conn;

    function __construct() {
        $this->conn = $this->connectDB();
    }

    private function connectDB() {
        $conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        
        // التحقق من الاتصال
        if (mysqli_connect_errno()) {
            die("Database connection failed: " . mysqli_connect_error());
        }
        
        return $conn;
    }

    function runBaseQuery($query) {
        $result = $this->conn->query($query);
        $resultset = []; // تأكد من تهيئة المتغير

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        return $resultset;
    }

    function runQuery($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        if (!$sql) {
            die("Prepare failed: " . $this->conn->error);
        }
        
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        $result = $sql->get_result();
        $resultset = []; // تأكد من تهيئة المتغير

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        return !empty($resultset) ? $resultset : null; // إعادة null إذا لم يكن هناك نتائج
    }

    function bindQueryParams($sql, $param_type, $param_value_array) {
        $param_value_reference[] = &$param_type; // استخدام المرجع للتوافق مع bind_param
        foreach ($param_value_array as $key => $value) {
            $param_value_reference[] = &$param_value_array[$key]; // استخدام المرجع
        }
        call_user_func_array([$sql, 'bind_param'], $param_value_reference);
    }

    function insert($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        if (!$sql) {
            die("Prepare failed: " . $this->conn->error);
        }

        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        
        return $sql->insert_id; // إعادة معرف الإدخال
    }

    function update($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        if (!$sql) {
            die("Prepare failed: " . $this->conn->error);
        }

        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
    }
}
?>
