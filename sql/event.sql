

-- Evenement MySQL : Historiser les sorties


CREATE EVENT IF NOT EXISTS `historize_event`
ON SCHEDULE
  EVERY 1 DAY
  COMMENT 'historize events states daily'
  DO
UPDATE etat e
    INNER JOIN sortie s ON s.state_id = e.id
    SET
        s.state_id = 193
WHERE
    (e.wording = 'Passée' OR e.wording = 'Annulée')
  AND
    ( NOW() >= DATE_ADD(s.start_date, INTERVAL 1 MONTH) )
;


-- Id 193 : l'état historisée.
-- NOTA : Activer l évenement MySQL -> SET GLOBAL event_scheduler = ON;
-- NOTA : connaitre la database config. MySql : select @@system_time_zone




