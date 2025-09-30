-- backfill sid baseado no nome da cidade
UPDATE flippy_bizlister_v2.business b
LEFT JOIN flippy_bizlister_v2.city c
  ON LOWER(TRIM(b.city)) = LOWER(TRIM(c.city))
SET b.sid = c.city_id
WHERE b.sid IS NULL OR b.sid IN (0,7,8);
