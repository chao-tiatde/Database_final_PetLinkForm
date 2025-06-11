<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=petlink;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 撈寵物主資料，不含開銷欄位
    $sql = "
    SELECT pets.*, 
           gender.gender AS gender_name,
           size.size AS size_name,
           age.age_range AS age_name,
           area.name AS area_name
    FROM pets
    JOIN gender ON pets.gender_id = gender.id
    JOIN size ON pets.size_id = size.id
    JOIN age ON pets.age_id = age.id
    JOIN area ON pets.area_id = area.id
    ";
    $stmt = $pdo->query($sql);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 撈所有寵物開銷
    $sql_expense = "
    SELECT pet_id, expence_id, amount, note
    FROM pet_expence
    ";
    $stmt_exp = $pdo->query($sql_expense);
    $expenses_raw = $stmt_exp->fetchAll(PDO::FETCH_ASSOC);

    // 整理開銷資料，依 pet_id 分組
    $expenses = [];
    foreach ($expenses_raw as $row) {
        $expenses[$row['pet_id']][] = $row;
    }

    // 定義開銷類型名稱對應（請依實際資料庫更新）
    $expense_names = [
        1 => '食物開銷',
        2 => '日常開銷',
        3 => '醫療開銷',
        4 => '訓練開銷',
    ];
} catch (PDOException $e) {
    echo "資料庫錯誤：" . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>寵物清單</title>
  <link rel="stylesheet" href="list.css" />
</head>
<body>
  <h1>寵物清單</h1>
  <a href="index.php" class="add-pet">新增寵物</a>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>操作</th>
          <th>ID</th>
          <th>名字</th>
          <th>種類</th>
          <th>品種</th>
          <th>性別</th>
          <th>體型</th>
          <th>毛色</th>
          <th>年齡</th>
          <th>封面</th>
          <th>地區</th>
          <th>個性</th>
          <th>健康</th>
          <th>開銷</th>
          <th>備註</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pets as $pet): ?>
          <tr>
            <td>
              <a href="edit.php?id=<?= $pet['id'] ?>">編輯</a>
              <a href="delete.php?id=<?= $pet['id'] ?>" onclick="return confirm('確定刪除？')">刪除</a>
            </td>
            <td><?= $pet['id'] ?></td>
            <td><?= htmlspecialchars($pet['name']) ?></td>
            <td><?= htmlspecialchars($pet['species']) ?></td>
            <td><?= htmlspecialchars($pet['breed']) ?></td>
            <td><?= htmlspecialchars($pet['gender_name']) ?></td>
            <td><?= htmlspecialchars($pet['size_name']) ?></td>
            <td><?= htmlspecialchars($pet['furColor']) ?></td>
            <td><?= htmlspecialchars($pet['age_name']) ?></td>
            <td>
              <?php if (!empty($pet['cover'])): ?>
                <img src="<?= htmlspecialchars($pet['cover']) ?>" alt="cover" class="thumb" />
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($pet['area_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($pet['personality'])) ?></td>
            <td><?= nl2br(htmlspecialchars($pet['health'])) ?></td>
            <td>
              <?php
              if (isset($expenses[$pet['id']])) {
                  foreach ($expenses[$pet['id']] as $exp) {
                      $name = $expense_names[$exp['expence_id']] ?? '其他開銷';
                      echo htmlspecialchars($name) . "： " . htmlspecialchars($exp['amount']) . "<br>";
                      if (!empty($exp['note'])) {
                          echo "備註：" . nl2br(htmlspecialchars($exp['note'])) . "<br>";
                      }
                  }
              } else {
                  echo "無開銷資料";
              }
              ?>
            </td>
            <td><?= nl2br(htmlspecialchars($pet['comment'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
