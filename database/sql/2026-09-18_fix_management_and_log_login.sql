-- 1) management@ifca.co.id (administrator) harus menunjuk administrator.id = 2 ("Admin Management"),
--    bukan 1 ("Admin").
-- 2) Tabel tenant dinomori ulang (2,3,4 -> 1,2,3). log_login.idforeign masih memakai id lama:
--    baris uji hari ini (id 287-301) dihapus, sisanya di-remap 4->3, 3->2, 2->1 dalam satu UPDATE.

START TRANSACTION;

UPDATE all_login SET idforeign = 2
WHERE email = 'management@ifca.co.id' AND tableforeign = 'administrator';

DELETE FROM log_login WHERE id BETWEEN 287 AND 301;

UPDATE log_login
SET idforeign = CASE idforeign
    WHEN 2 THEN 1
    WHEN 3 THEN 2
    WHEN 4 THEN 3
    ELSE idforeign
END;

COMMIT;
