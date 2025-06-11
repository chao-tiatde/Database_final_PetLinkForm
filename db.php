<!-- 連接 MySQL 資料庫 -->
<?php
$host = '127.0.0.1';
$port = 3306;
$dbname = 'petlink';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=petlink;charset=utf8", $username, $password);
    echo "連線成功！";
} catch (PDOException $e) {
    echo "連線失敗：" . $e->getMessage();
}
?>
