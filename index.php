<!-- index.php -->
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <title>新增寵物</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <h1>新增寵物資料</h1>
  <form action="insert.php" method="post" enctype="multipart/form-data">
    <label>名字：</label><input type="text" name="name" required><br>

    <label>種類：</label><input type="text" name="species" required><br>

    <label>品種：</label><input type="text" name="breed" required><br>

    <label>性別：</label>
    <select name="gender" required>
      <option value="">請選擇</option>
      <option value="1">公</option>
      <option value="2">母</option>
      <option value="3">其他</option>
    </select><br>

    <label>體型：</label>
    <select name="size" required>
      <option value="">請選擇</option>
      <option value="1">幼型</option>
      <option value="2">小型</option>
      <option value="3">中型</option>
      <option value="4">大型</option>
    </select><br>

    <label>毛色：</label><input type="text" name="furColor" required><br>

    <label>年齡：</label>
    <select name="age" required>
      <option value="">請選擇</option>
      <option value="1">未離乳</option>
      <option value="2">一至三月</option>
      <option value="3">三至六月</option>
      <option value="4">六月至一歲</option>
      <option value="5">一歲至三歲</option>
      <option value="6">三歲至七歲</option>
      <option value="7">七歲以上</option>
    </select><br>

    <label>地區：</label>
    <select name="area" required>
      <option value="">請選擇</option>
      <option value="1">基隆市</option>
      <option value="2">臺北市</option>
      <option value="3">新北市</option>
      <option value="4">桃園市</option>
      <option value="5">新竹市</option>
      <option value="6">新竹縣</option>
      <option value="7">宜蘭縣</option>
      <option value="8">苗栗縣</option>
      <option value="9">臺中市</option>
      <option value="10">彰化縣</option>
      <option value="11">南投縣</option>
      <option value="12">雲林縣</option>
      <option value="13">嘉義市</option>
      <option value="14">嘉義縣</option>
      <option value="15">臺南市</option>
      <option value="16">高雄市</option>
      <option value="17">屏東縣</option>
      <option value="18">花蓮縣</option>
      <option value="19">臺東縣</option>
      <option value="20">金門縣</option>
      <option value="21">連江縣</option>
      <option value="22">澎湖縣</option>
    </select><br>

    <label>個性：</label><input type="text" name="personality" required><br>
    <label>健康狀況：</label><input type="text" name="health" required><br>

    <label>食物開銷：</label>
    <input type="text" name="food" required><br>

    <label>日常開銷：</label>
    <input type="text" name="daily" required><br>

    <label>醫療開銷：</label>
    <input type="text" name="medical" required><br>

    <label>訓練開銷：</label>
    <input type="text" name="train" required><br>

     <label>備註說明：</label><textarea name="comment"></textarea><br>

    <label>上傳圖片：</label><input type="file" name="cover" accept="image/*" required><br><br>

    <input type="submit" value="新增寵物">
  </form>

  <div class="link">
    <a href="list.php">查看寵物清單</a>
  </div>
</body>

</html>