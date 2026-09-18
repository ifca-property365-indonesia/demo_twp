-- Memberi akun admin@ifca.co.id akses portal Tenant (tenant id 2, "PT. IFCA Property365 Indonesia")
-- dengan password yang sama seperti akun administrator-nya, supaya menu "Pindah ke Tenant" di header
-- muncul (lihat README: Pindah portal tanpa login ulang).
--
-- Aman dijalankan berulang: tidak menambah baris kalau sudah ada.

INSERT INTO all_login (name, email, password, pict, handphone, tableforeign, idforeign)
SELECT t.name, t.email, a.password, NULL, NULL, 'tenant', t.id
FROM tenant t
JOIN all_login a ON a.email = t.email AND a.tableforeign = 'administrator'
WHERE t.id = 2
  AND NOT EXISTS (
      SELECT 1 FROM all_login x WHERE x.tableforeign = 'tenant' AND x.idforeign = t.id
  );
