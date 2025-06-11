<?php
// 連接資料庫
$pdo = new PDO("mysql:host=localhost;port=3306;dbname=petlink;charset=utf8", "root", "");

// 取得寵物 ID 並讀取主資料
$id = $_GET['id'] ?? 0;
if (!$id) {
    echo "請提供有效的寵物ID";
    exit;
}

// 撈寵物主資料
$stmt = $pdo->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    echo "找不到該寵物資料";
    exit;
}

// 撈該寵物所有開銷資料，結果以 expence_id => amount 陣列形式
$stmtExp = $pdo->prepare("SELECT expence_id, amount FROM pet_expence WHERE pet_id = ?");
$stmtExp->execute([$id]);
$expences = $stmtExp->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <title>編輯寵物資料</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <h1>編輯寵物資料</h1>
  <form action="update.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

    <label>名字：</label>
    <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required><br>

    <label>種類：</label>
    <input type="text" name="species" value="<?= htmlspecialchars($row['species']) ?>" required><br>

    <label>品種：</label>
    <input type="text" name="breed" value="<?= htmlspecialchars($row['breed']) ?>" required><br>

    <label>性別：</label>
    <select name="gender" required>
      <option value="">請選擇</option>
      <option value="1" <?= ($row['gender_id'] == 1) ? 'selected' : '' ?>>公</option>
      <option value="2" <?= ($row['gender_id'] == 2) ? 'selected' : '' ?>>母</option>
      <option value="3" <?= ($row['gender_id'] == 3) ? 'selected' : '' ?>>其他</option>
    </select><br>

    <label>體型：</label>
    <select name="size" required>
      <option value="">請選擇</option>
      <option value="1" <?= ($row['size_id'] == 1) ? 'selected' : '' ?>>幼型</option>
      <option value="2" <?= ($row['size_id'] == 2) ? 'selected' : '' ?>>小型</option>
      <option value="3" <?= ($row['size_id'] == 3) ? 'selected' : '' ?>>中型</option>
      <option value="4" <?= ($row['size_id'] == 4) ? 'selected' : '' ?>>大型</option>
    </select><br>

    <label>毛色：</label>
    <input type="text" name="furColor" value="<?= htmlspecialchars($row['furColor']) ?>" required><br>

    <label>年齡：</label>
    <select name="age" required>
      <option value="">請選擇</option>
      <option value="1" <?= ($row['age_id'] == 1) ? 'selected' : '' ?>>未離乳</option>
      <option value="2" <?= ($row['age_id'] == 2) ? 'selected' : '' ?>>一至三月</option>
      <option value="3" <?= ($row['age_id'] == 3) ? 'selected' : '' ?>>三至六月</option>
      <option value="4" <?= ($row['age_id'] == 4) ? 'selected' : '' ?>>六月至一歲</option>
      <option value="5" <?= ($row['age_id'] == 5) ? 'selected' : '' ?>>一歲至三歲</option>
      <option value="6" <?= ($row['age_id'] == 6) ? 'selected' : '' ?>>三歲至七歲</option>
      <option value="7" <?= ($row['age_id'] == 7) ? 'selected' : '' ?>>七歲以上</option>
    </select><br>

    <label>地區：</label>
    <select name="area" required>
      <option value="">請選擇</option>
      <option value="1" <?= ($row['area_id'] == 1) ? 'selected' : '' ?>>基隆市</option>
      <option value="2" <?= ($row['area_id'] == 2) ? 'selected' : '' ?>>臺北市</option>
      <option value="3" <?= ($row['area_id'] == 3) ? 'selected' : '' ?>>新北市</option>
      <option value="4" <?= ($row['area_id'] == 4) ? 'selected' : '' ?>>桃園市</option>
      <option value="5" <?= ($row['area_id'] == 5) ? 'selected' : '' ?>>新竹市</option>
      <option value="6" <?= ($row['area_id'] == 6) ? 'selected' : '' ?>>新竹縣</option>
      <option value="7" <?= ($row['area_id'] == 7) ? 'selected' : '' ?>>宜蘭縣</option>
      <option value="8" <?= ($row['area_id'] == 8) ? 'selected' : '' ?>>苗栗縣</option>
      <option value="9" <?= ($row['area_id'] == 9) ? 'selected' : '' ?>>臺中市</option>
      <option value="10" <?= ($row['area_id'] == 10) ? 'selected' : '' ?>>彰化縣</option>
      <option value="11" <?= ($row['area_id'] == 11) ? 'selected' : '' ?>>南投縣</option>
      <option value="12" <?= ($row['area_id'] == 12) ? 'selected' : '' ?>>雲林縣</option>
      <option value="13" <?= ($row['area_id'] == 13) ? 'selected' : '' ?>>嘉義市</option>
      <option value="14" <?= ($row['area_id'] == 14) ? 'selected' : '' ?>>嘉義縣</option>
      <option value="15" <?= ($row['area_id'] == 15) ? 'selected' : '' ?>>臺南市</option>
      <option value="16" <?= ($row['area_id'] == 16) ? 'selected' : '' ?>>高雄市</option>
      <option value="17" <?= ($row['area_id'] == 17) ? 'selected' : '' ?>>屏東縣</option>
      <option value="18" <?= ($row['area_id'] == 18) ? 'selected' : '' ?>>花蓮縣</option>
      <option value="19" <?= ($row['area_id'] == 19) ? 'selected' : '' ?>>臺東縣</option>
      <option value="20" <?= ($row['area_id'] == 20) ? 'selected' : '' ?>>金門縣</option>
      <option value="21" <?= ($row['area_id'] == 21) ? 'selected' : '' ?>>連江縣</option>
      <option value="22" <?= ($row['area_id'] == 22) ? 'selected' : '' ?>>澎湖縣</option>
    </select><br>

    <label>個性：</label>
    <input type="text" name="personality" value="<?= htmlspecialchars($row['personality']) ?>" required><br>

    <label>健康狀況：</label>
    <input type="text" name="health" value="<?= htmlspecialchars($row['health']) ?>" required><br>

    <!-- 開銷相關欄位 -->
    <label>食物開銷：</label>
    <input type="text" name="food" value="<?= htmlspecialchars($expences[1] ?? '') ?>" required><br>

    <label>日常開銷：</label>
    <input type="text" name="daily" value="<?= htmlspecialchars($expences[2] ?? '') ?>" required><br>

    <label>醫療開銷：</label>
    <input type="text" name="medical" value="<?= htmlspecialchars($expences[3] ?? '') ?>" required><br>

    <label>訓練開銷：</label>
    <input type="text" name="train" value="<?= htmlspecialchars($expences[4] ?? '') ?>" required><br>

    <label>備註說明：</label>
    <textarea name="comment"><?= htmlspecialchars($row['comment']) ?></textarea><br>

    <label>目前圖片：</label><br>
    <img src="<?= htmlspecialchars($row['cover']) ?>" width="150" alt="寵物圖片"><br>

    <label>更換圖片：</label>
    <input type="file" name="cover" accept="image/*"><br><br>

    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

    <input type="submit" value="更新寵物">
  </form>

  <div class="link">
    <a href="list.php">查看寵物清單</a>
  </div>
</body>

</html>
