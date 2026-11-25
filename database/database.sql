-- =====================================================
-- Database Warkop Cafetaria De Barrio (Bahasa Indonesia)
-- =====================================================
CREATE DATABASE IF NOT EXISTS warkop_de_barrio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE warkop_de_barrio;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- 1. pengguna
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pengguna (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama         VARCHAR(100)    NOT NULL,
  email        VARCHAR(150)    NOT NULL,
  password     VARCHAR(255)    NOT NULL,
  peran        ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
  status_aktif TINYINT(1)      NOT NULL DEFAULT 1,
  created_at   TIMESTAMP       NULL,
  updated_at   TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY pengguna_email_unique (email)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 2. kategori
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama        VARCHAR(80)     NOT NULL,
  urutan      SMALLINT        NOT NULL DEFAULT 0,
  created_at  TIMESTAMP       NULL,
  updated_at  TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY kategori_nama_unique (nama)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 3. menu
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS menu (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kategori_id      BIGINT UNSIGNED NOT NULL,
  nama             VARCHAR(120)    NOT NULL,
  deskripsi        TEXT            NULL,
  harga            DECIMAL(10,2)   NOT NULL,
  foto             VARCHAR(255)    NULL,
  status_tersedia  TINYINT(1)      NOT NULL DEFAULT 1,
  created_at       TIMESTAMP       NULL,
  updated_at       TIMESTAMP       NULL,
  PRIMARY KEY (id),
  KEY menu_kategori_id_index (kategori_id),
  CONSTRAINT menu_kategori_id_foreign FOREIGN KEY (kategori_id)
    REFERENCES kategori (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 4. meja
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS meja (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nomor_meja   VARCHAR(30)     NOT NULL,
  token_qr     VARCHAR(64)     NOT NULL,
  status_aktif TINYINT(1)      NOT NULL DEFAULT 1,
  created_at   TIMESTAMP       NULL,
  updated_at   TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY meja_nomor_meja_unique (nomor_meja),
  UNIQUE KEY meja_token_qr_unique (token_qr)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 5. pesanan
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode_pesanan    VARCHAR(20)     NOT NULL,
  meja_id         BIGINT UNSIGNED NULL,
  pengguna_id     BIGINT UNSIGNED NULL,
  sumber          ENUM('qr','kasir') NOT NULL,
  status          ENUM('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  nama_pelanggan  VARCHAR(80)     NULL,
  total_harga     DECIMAL(12,2)   NOT NULL DEFAULT 0,
  catatan         VARCHAR(255)    NULL,
  uuid_klien      CHAR(36)        NULL,
  waktu_pesan     DATETIME        NOT NULL,
  created_at      TIMESTAMP       NULL,
  updated_at      TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY pesanan_kode_pesanan_unique (kode_pesanan),
  UNIQUE KEY pesanan_uuid_klien_unique (uuid_klien),
  KEY pesanan_status_index (status),
  KEY pesanan_waktu_pesan_index (waktu_pesan),
  CONSTRAINT pesanan_meja_id_foreign FOREIGN KEY (meja_id)
    REFERENCES meja (id) ON DELETE SET NULL,
  CONSTRAINT pesanan_pengguna_id_foreign FOREIGN KEY (pengguna_id)
    REFERENCES pengguna (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 6. detail_pesanan
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS detail_pesanan (
  id           BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  pesanan_id   BIGINT UNSIGNED   NOT NULL,
  menu_id      BIGINT UNSIGNED   NOT NULL,
  nama_menu    VARCHAR(120)      NOT NULL,
  harga_satuan DECIMAL(10,2)     NOT NULL,
  jumlah       SMALLINT UNSIGNED NOT NULL,
  subtotal     DECIMAL(12,2)     NOT NULL,
  catatan      VARCHAR(255)      NULL,
  PRIMARY KEY (id),
  KEY detail_pesanan_pesanan_id_index (pesanan_id),
  KEY detail_pesanan_menu_id_index (menu_id),
  CONSTRAINT detail_pesanan_pesanan_id_foreign FOREIGN KEY (pesanan_id)
    REFERENCES pesanan (id) ON DELETE CASCADE,
  CONSTRAINT detail_pesanan_menu_id_foreign FOREIGN KEY (menu_id)
    REFERENCES menu (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 7. pembayaran
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  pesanan_id    BIGINT UNSIGNED NOT NULL,
  pengguna_id   BIGINT UNSIGNED NOT NULL,
  metode        ENUM('tunai','qris','transfer') NOT NULL,
  jumlah_bayar  DECIMAL(12,2)   NOT NULL,
  kembalian     DECIMAL(12,2)   NOT NULL DEFAULT 0,
  waktu_bayar   DATETIME        NOT NULL,
  created_at    TIMESTAMP       NULL,
  updated_at    TIMESTAMP       NULL,
  PRIMARY KEY (id),
  UNIQUE KEY pembayaran_pesanan_id_unique (pesanan_id),
  KEY pembayaran_pengguna_id_index (pengguna_id),
  CONSTRAINT pembayaran_pesanan_id_foreign FOREIGN KEY (pesanan_id)
    REFERENCES pesanan (id) ON DELETE RESTRICT,
  CONSTRAINT pembayaran_pengguna_id_foreign FOREIGN KEY (pengguna_id)
    REFERENCES pengguna (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 8. bahan
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS bahan (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama         VARCHAR(100)    NOT NULL,
  satuan       VARCHAR(20)     NOT NULL,
  stok         DECIMAL(12,2)   NOT NULL DEFAULT 0,
  stok_minimum DECIMAL(12,2)   NOT NULL DEFAULT 0,
  created_at   TIMESTAMP       NULL,
  updated_at   TIMESTAMP       NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 9. resep_menu
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS resep_menu (
  menu_id       BIGINT UNSIGNED NOT NULL,
  bahan_id      BIGINT UNSIGNED NOT NULL,
  jumlah_pakai  DECIMAL(10,2)   NOT NULL,
  PRIMARY KEY (menu_id, bahan_id),
  KEY resep_menu_bahan_id_index (bahan_id),
  CONSTRAINT resep_menu_menu_id_foreign FOREIGN KEY (menu_id)
    REFERENCES menu (id) ON DELETE CASCADE,
  CONSTRAINT resep_menu_bahan_id_foreign FOREIGN KEY (bahan_id)
    REFERENCES bahan (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 10. mutasi_stok
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS mutasi_stok (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bahan_id     BIGINT UNSIGNED NOT NULL,
  pengguna_id  BIGINT UNSIGNED NOT NULL,
  jenis        ENUM('masuk','keluar','penyesuaian') NOT NULL,
  jumlah       DECIMAL(12,2)   NOT NULL,
  catatan      VARCHAR(255)    NULL,
  created_at   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY mutasi_stok_bahan_id_index (bahan_id),
  KEY mutasi_stok_pengguna_id_index (pengguna_id),
  CONSTRAINT mutasi_stok_bahan_id_foreign FOREIGN KEY (bahan_id)
    REFERENCES bahan (id) ON DELETE RESTRICT,
  CONSTRAINT mutasi_stok_pengguna_id_foreign FOREIGN KEY (pengguna_id)
    REFERENCES pengguna (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 11. pengeluaran
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS pengeluaran (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  pengguna_id         BIGINT UNSIGNED NOT NULL,
  deskripsi           VARCHAR(150)    NOT NULL,
  jumlah              DECIMAL(12,2)   NOT NULL,
  tanggal_pengeluaran DATE            NOT NULL,
  catatan             VARCHAR(255)    NULL,
  created_at          TIMESTAMP       NULL,
  updated_at          TIMESTAMP       NULL,
  PRIMARY KEY (id),
  KEY pengeluaran_pengguna_id_index (pengguna_id),
  KEY pengeluaran_tanggal_index (tanggal_pengeluaran),
  CONSTRAINT pengeluaran_pengguna_id_foreign FOREIGN KEY (pengguna_id)
    REFERENCES pengguna (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- Data Awal Contoh (Seed)
-- -----------------------------------------------------
INSERT INTO kategori (nama, urutan, created_at, updated_at) VALUES
  ('Kopi', 1, NOW(), NOW()),
  ('Non-Kopi', 2, NOW(), NOW()),
  ('Makanan Ringan', 3, NOW(), NOW()),
  ('Makanan Berat', 4, NOW(), NOW())
ON DUPLICATE KEY UPDATE urutan = VALUES(urutan);

INSERT INTO meja (nomor_meja, token_qr, status_aktif, created_at, updated_at) VALUES
  ('Meja 01', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW()),
  ('Meja 02', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW()),
  ('Meja 03', SUBSTRING(MD5(RAND()), 1, 32), 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE status_aktif = VALUES(status_aktif);
