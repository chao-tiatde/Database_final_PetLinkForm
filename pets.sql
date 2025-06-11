-- 資料庫：petlink（請先執行 CREATE DATABASE petlink;）

USE petlink;

-- 1. gender 表
CREATE TABLE gender (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gender VARCHAR(10) NOT NULL
);

INSERT INTO gender (gender) VALUES 
('公'), ('母'), ('不確定');

-- 2. size 表（新增“幼型”）
CREATE TABLE size (
  id INT AUTO_INCREMENT PRIMARY KEY,
  size VARCHAR(10) NOT NULL
);

INSERT INTO size (size) VALUES 
('幼型'), ('小型'), ('中型'), ('大型');

-- 3. age 表（細分年齡）
CREATE TABLE age (
  id INT AUTO_INCREMENT PRIMARY KEY,
  age_range VARCHAR(20) NOT NULL
);

INSERT INTO age (age_range) VALUES 
('未離乳'),
('一至三月'),
('三至六月'),
('六月至一歲'),
('一歲至三歲'),
('三歲至七歲'),
('七歲以上');

-- 4. area 表（以縣市為單位）
CREATE TABLE area (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(10) NOT NULL
);

INSERT INTO area (name) VALUES 
('基隆市'), ('臺北市'), ('新北市'), ('桃園市'), ('新竹市'), ('新竹縣'),
('宜蘭縣'), ('苗栗縣'), ('臺中市'), ('彰化縣'), ('南投縣'), ('雲林縣'),
('嘉義市'), ('嘉義縣'), ('臺南市'), ('高雄市'), ('屏東縣'), ('花蓮縣'),
('臺東縣'), ('金門縣'), ('連江縣'), ('澎湖縣');

-- 5. expence 表（開銷類別）
CREATE TABLE expence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(20) NOT NULL
);

INSERT INTO expence (type) VALUES 
('食物開銷'), ('日常開銷'), ('醫療開銷'), ('訓練開銷');

-- 6. 主表：pets
CREATE TABLE pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL,
  species VARCHAR(20),
  breed VARCHAR(30),
  gender_id INT,
  size_id INT,
  furColor VARCHAR(20),
  age_id INT,
  area_id INT,
  personality TEXT,
  health TEXT,
  comment TEXT,
  cover VARCHAR(255),
  FOREIGN KEY (gender_id) REFERENCES gender(id),
  FOREIGN KEY (size_id) REFERENCES size(id),
  FOREIGN KEY (age_id) REFERENCES age(id),
  FOREIGN KEY (area_id) REFERENCES area(id)
);
CREATE TABLE pet_expence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pet_id INT,
  expence_id INT,
  amount INT,
  note TEXT,
  FOREIGN KEY (pet_id) REFERENCES pets(id),
  FOREIGN KEY (expence_id) REFERENCES expence(id),
  UNIQUE KEY uniq_pet_expence (pet_id, expence_id)
);

