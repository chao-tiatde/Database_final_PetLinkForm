<?php
try {
    // 連接資料庫
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=petlink;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 圖片上傳資料夾檢查與建立
    $uploadDir = "upload/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 圖片上傳檢查
    if (!isset($_FILES["cover"]) || $_FILES["cover"]["error"] !== UPLOAD_ERR_OK) {
        throw new Exception('請上傳圖片');
    }

    // 圖片檔名安全處理
    $filename = basename($_FILES["cover"]["name"]);
    $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
    $targetPath = $uploadDir . time() . "_" . $safeName;

    if (!move_uploaded_file($_FILES["cover"]["tmp_name"], $targetPath)) {
        throw new Exception('圖片上傳失敗');
    }

    // 取得並整理表單資料
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $furColor = trim($_POST['furColor'] ?? '');
    $personality = trim($_POST['personality'] ?? '');
    $health = trim($_POST['health'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    $gender_id = $_POST['gender'] ?? null;
    $size_id = $_POST['size'] ?? null;
    $age_id = $_POST['age'] ?? null;
    $area_id = $_POST['area'] ?? null;

    $food = trim($_POST['food'] ?? '');
    $daily = trim($_POST['daily'] ?? '');
    $medical = trim($_POST['medical'] ?? '');
    $train_expense = trim($_POST['train'] ?? '');

    // 必填欄位檢查
    if (
        !$name || !$species || !$breed || !$furColor || !$personality || !$health ||
        !$gender_id || !$size_id || !$age_id || !$area_id ||
        $food === '' || $daily === '' || $medical === '' || $train_expense === ''
    ) {
        throw new Exception('必填欄位不足');
    }

    // 檢查開銷是否皆為數字（可含小數點）
    foreach (['食物開銷' => $food, '日常開銷' => $daily, '醫療開銷' => $medical, '訓練開銷' => $train_expense] as $key => $val) {
        if (!is_numeric($val)) {
            throw new Exception("{$key} 必須為數字");
        }
    }

    // 新增寵物資料，pets 表不包含 train 欄位
    $sql = "INSERT INTO pets 
        (name, species, breed, gender_id, size_id, furColor, age_id, area_id, personality, health, comment, cover) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $name,
        $species,
        $breed,
        $gender_id,
        $size_id,
        $furColor,
        $age_id,
        $area_id,
        $personality,
        $health,
        $comment,
        $targetPath
    ]);

    // 取得剛插入的寵物ID
    $pet_id = $pdo->lastInsertId();

    // 插入 pet_expence 表，四種開銷分別對應 expence.id: 1~4
    $expences = [
        1 => $food,
        2 => $daily,
        3 => $medical,
        4 => $train_expense
    ];
    $sql_exp = "INSERT INTO pet_expence (pet_id, expence_id, amount, note) VALUES (?, ?, ?, ?)";
    $stmt_exp = $pdo->prepare($sql_exp);

    foreach ($expences as $expence_id => $amount) {
        $stmt_exp->execute([$pet_id, $expence_id, $amount, '']);
    }

    // 新增成功導向列表頁
    echo "<script>alert('新增成功！'); location.href='list.php';</script>";

} catch (Exception $e) {
    $msg = $e->getMessage();
    // 失敗導回新增頁，並顯示錯誤訊息
    echo "<script>alert('錯誤：$msg'); location.href='index.php';</script>";
}
