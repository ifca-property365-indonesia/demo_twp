-- Menomori ulang all_login.id supaya baris tenant punya id = tenant.id
-- (admin@ifca.co.id tenant -> id 2, sisanya mengikuti). Management (administrator) bergeser 2 -> 5.
-- Tidak ada kode yang memakai all_login.id (semua lewat email / idforeign).

START TRANSACTION;

UPDATE all_login SET id = 5 WHERE email = 'management@ifca.co.id' AND tableforeign = 'administrator' AND id = 2;
UPDATE all_login SET id = 2 WHERE email = 'admin@ifca.co.id'      AND tableforeign = 'tenant'        AND idforeign = 2;

COMMIT;

ALTER TABLE all_login AUTO_INCREMENT = 6;
