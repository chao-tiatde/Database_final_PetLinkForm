<?php
// 建立資料庫連線
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=petlink;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 設定錯誤模式為例外
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // 預設抓取關聯式陣列
} catch (PDOException $e) {
    echo "資料庫連接失敗：" . $e->getMessage();
    exit;
}

// 檢查表單是否經由 POST 提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- 1. 驗證與過濾輸入資料 ---
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $species = filter_input(INPUT_POST, 'species', FILTER_SANITIZE_STRING);
    $breed = filter_input(INPUT_POST, 'breed', FILTER_SANITIZE_STRING);
    $gender_id = filter_input(INPUT_POST, 'gender', FILTER_VALIDATE_INT);
    $size_id = filter_input(INPUT_POST, 'size', FILTER_VALIDATE_INT);
    $furColor = filter_input(INPUT_POST, 'furColor', FILTER_SANITIZE_STRING);
    $age_id = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $area_id = filter_input(INPUT_POST, 'area', FILTER_VALIDATE_INT);
    $personality = filter_input(INPUT_POST, 'personality', FILTER_SANITIZE_STRING);
    $health = filter_input(INPUT_POST, 'health', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    // 各項開銷（允許為空或整數）
    $food_expense = filter_input(INPUT_POST, 'food', FILTER_VALIDATE_INT);
    $daily_expense = filter_input(INPUT_POST, 'daily', FILTER_VALIDATE_INT);
    $medical_expense = filter_input(INPUT_POST, 'medical', FILTER_VALIDATE_INT);
    $train_expense = filter_input(INPUT_POST, 'train', FILTER_VALIDATE_INT);

    // 基本欄位檢查
    if (!$id || !$name || !$species || !$breed || !$gender_id || !$size_id || !$furColor || !$age_id || !$area_id || !$personality || !$health) {
        echo "必填欄位資料不完整，請回上一頁檢查。";
        exit;
    }

    // 定義對應的開銷類型（對應 expence 表中的 ID）
    $expense_categories = [
        1 => $food_expense,
        2 => $daily_expense,
        3 => $medical_expense,
        4 => $train_expense,
    ];

    // --- 2. 處理封面圖片上傳 ---
    $cover_path = ''; // 初始化封面路徑
    $current_cover_sql = "SELECT cover FROM pets WHERE id = ?";
    $stmt_current_cover = $pdo->prepare($current_cover_sql);
    $stmt_current_cover->execute([$id]);
    $current_cover = $stmt_current_cover->fetchColumn(); // 取得目前封面路徑

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // 圖片儲存資料夾
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // 若資料夾不存在則建立
        }

        $file_tmp_name = $_FILES['cover']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['cover']['name']); // 生成唯一檔名
        $destination = $upload_dir . $file_name;

        // 移動上傳檔案
        if (move_uploaded_file($file_tmp_name, $destination)) {
            $cover_path = $destination;

            // 若舊圖片存在且不是預設圖則刪除
            if ($current_cover && file_exists($current_cover) && strpos($current_cover, 'uploads/') !== false) {
                unlink($current_cover); // 刪除檔案
            }
        } else {
            echo "檔案上傳失敗。";
            exit;
        }
    } else {
        // 若無新圖片則保留原封面路徑
        $cover_path = $current_cover;
    }

    // --- 3. 開始交易 ---
    $pdo->beginTransaction();

    try {
        // --- 4. 更新主要寵物資料 ---
        $sql_update_pet = "UPDATE pets SET 
                            name = ?, species = ?, breed = ?, gender_id = ?, size_id = ?, 
                            furColor = ?, age_id = ?, area_id = ?, personality = ?, 
                            health = ?, comment = ?, cover = ? 
                           WHERE id = ?";
        $stmt_update_pet = $pdo->prepare($sql_update_pet);
        $stmt_update_pet->execute([
            $name, $species, $breed, $gender_id, $size_id, 
            $furColor, $age_id, $area_id, $personality, 
            $health, $comment, $cover_path, $id
        ]);

        // --- 5. 更新寵物開銷資料 ---
        foreach ($expense_categories as $expence_id => $amount) {
            // 檢查該筆開銷是否已存在
            $stmt_check_expense = $pdo->prepare("SELECT COUNT(*) FROM pet_expence WHERE pet_id = ? AND expence_id = ?");
            $stmt_check_expense->execute([$id, $expence_id]);
            $exists = $stmt_check_expense->fetchColumn();

            if ($amount !== null && $amount >= 0) { // 若有提供有效金額
                if ($exists) {
                    // 更新原有開銷資料
                    $sql_update_expense = "UPDATE pet_expence SET amount = ? WHERE pet_id = ? AND expence_id = ?";
                    $stmt_update_expense = $pdo->prepare($sql_update_expense);
                    $stmt_update_expense->execute([$amount, $id, $expence_id]);
                } else {
                    // 新增開銷資料
                    $sql_insert_expense = "INSERT INTO pet_expence (pet_id, expence_id, amount) VALUES (?, ?, ?)";
                    $stmt_insert_expense = $pdo->prepare($sql_insert_expense);
                    $stmt_insert_expense->execute([$id, $expence_id, $amount]);
                }
            } else {
                // 若無效或空值，且已有紀錄，則刪除該筆紀錄
                if ($exists) {
                    $sql_delete_expense = "DELETE FROM pet_expence WHERE pet_id = ? AND expence_id = ?";
                    $stmt_delete_expense = $pdo->prepare($sql_delete_expense);
                    $stmt_delete_expense->execute([$id, $expence_id]);
                }
            }
        }

        // 提交交易
        $pdo->commit();
        echo "<script>alert('寵物資料更新成功！'); window.location.href='list.php';</script>";
    } catch (PDOException $e) {
        // 發生錯誤時回滾交易
        $pdo->rollBack();
        echo "資料更新失敗：" . $e->getMessage();
    }
} else {
    echo "無效的請求方法。";
}
?>
