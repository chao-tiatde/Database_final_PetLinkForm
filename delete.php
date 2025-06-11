<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=petlink;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'] ?? null;
    if (!$id) throw new Exception('缺少 id');

    // 刪除花費資料
    $stmt = $pdo->prepare("DELETE FROM pet_expence WHERE pet_id = ?");
    $stmt->execute([$id]);

    // 刪除寵物資料
    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->execute([$id]);

    echo "<script>alert('刪除成功'); location.href='list.php';</script>";

} catch (Exception $e) {
    $msg = $e->getMessage();
    echo "<script>alert('刪除失敗：{$msg}'); history.back();</script>";
}
?>
