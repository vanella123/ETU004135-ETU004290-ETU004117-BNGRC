
ALTER TABLE echange
ADD COLUMN date_envoie DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN date_acceptation DATETIME DEFAULT NULL;

-- Mettre à jour la première ligne (id = 1)
UPDATE echange
SET date_envoie = '2026-02-13 10:00:00',
    date_acceptation = NULL
WHERE id = 1;

-- Mettre à jour la deuxième ligne (id = 2)
UPDATE echange
SET date_envoie = '2026-02-12 15:30:00',
    date_acceptation = '2026-02-12 16:00:00'
WHERE id = 2;

-- Mettre à jour la troisième ligne (id = 3)
UPDATE echange
SET date_envoie = '2026-02-11 09:45:00',
    date_acceptation = '2026-02-11 10:15:00'
WHERE id = 3;

-- Mettre à jour la quatrième ligne (id = 4)
UPDATE echange
SET date_envoie = '2026-02-13 11:20:00',
    date_acceptation = NULL
WHERE id = 4;
