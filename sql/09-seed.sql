-- 0. Clean up
DELETE FROM production_tag;
DELETE FROM production_category;
DELETE FROM production_platform;
DELETE FROM ratings;
DELETE FROM productions;
DELETE FROM tags;
DELETE FROM categories;
DELETE FROM platforms;

UPDATE sqlite_sequence SET seq = 0 WHERE name IN ('productions', 'platforms', 'categories', 'tags', 'ratings');

-- 1. Platformy (Loga w katalogu /public/assets/logo/)
INSERT INTO platforms (nazwa, logo_url, platform_url) VALUES
    ('Netflix', '/public/assets/logo/netflix.png', 'https://www.netflix.com'),
    ('Amazon Prime Video', '/public/assets/logo/prime.png', 'https://www.primevideo.com'),
    ('Crunchyroll', '/public/assets/logo/crunchyroll.png', 'https://www.crunchyroll.com'),
    ('SkyShowtime', '/public/assets/logo/skyshowtime.png', 'https://www.skyshowtime.com');

-- 2. Kategorie
INSERT INTO categories (nazwa) VALUES
    ('Action'), ('Thriller'), ('Horror'), ('Anime'), ('Sci-Fi'), ('Drama'), ('Comedy');

-- 3. Tagi
INSERT INTO tags (nazwa) VALUES
    ('Klasyk'), ('Cyberpunk'), ('Isekai'), ('Okruchy życia'), ('Komedia');

-- 4. Produkcje
-- Filmy
INSERT INTO productions (tytul, opis, typ, rok, plakat_url) VALUES
    ('Matrix', 'Haker odkrywa, że rzeczywistość jest symulacją stworzoną przez maszyny.', 'film', 1999, 'https://www.imdb.com/title/tt0133093/mediaviewer/rm525547776/?ref_=tt_ov_i'),
    ('Szklana Pułapka', 'Policjant John McClane walczy z terrorystami w wieżowcu.', 'film', 1988, 'https://www.imdb.com/title/tt0095016/mediaviewer/rm270892032/?ref_=tt_ov_i'),
    ('Speed', 'Autobus nie może zwolnić poniżej 50 mil na godzinę.', 'film', 1994, 'https://www.imdb.com/title/tt0111257/mediaviewer/rm4192537344/?ref_=tt_ov_i'),
    ('Bullet Train', 'Pięcioro morderców w szybkim pociągu.', 'film', 2022, 'https://www.imdb.com/title/tt12593682/mediaviewer/rm36704513/?ref_=tt_ov_i'),
    ('Baby Driver', 'Młody kierowca ucieczek chce rzucić fach.', 'film', 2017, 'https://www.imdb.com/title/tt3890160/mediaviewer/rm2955687168/?ref_=tt_ov_i'),
    ('Batman', 'Mroczny Rycerz walczy z przestępczością w Gotham.', 'film', 2022, 'https://www.imdb.com/title/tt1877830/mediaviewer/rm3486063105/?ref_=tt_ov_i');

-- Seriale
INSERT INTO productions (tytul, opis, typ, rok, plakat_url) VALUES
    ('Frieren: Beyond Journey''s End', 'Opowieść o elfiej czarodziejce i jej podróży po pokonaniu Króla Demonów.', 'serial', 2023, 'https://www.imdb.com/title/tt22248376/mediaviewer/rm255742465/?ref_=tt_ov_i'),
    ('My Dress-Up Darling', 'Licealista pasjonujący się lalkami Hina zaczyna szyć stroje cosplayowe.', 'serial', 2022, 'https://www.imdb.com/title/tt15765670/mediaviewer/rm1263002369/?ref_=tt_ov_i'),
    ('Spy x Family', 'Szpieg, zabójczyni i telepatka udają rodzinę, by utrzymać pokój na świecie.', 'serial', 2022, 'https://www.imdb.com/title/tt13706018/mediaviewer/rm218701057/?ref_=tt_ov_i'),
    ('Friends', 'Perypetie szóstki przyjaciół z Manhattanu.', 'serial', 1994, 'https://www.imdb.com/title/tt0108778/mediaviewer/rm2513109504/?ref_=tt_ov_i'),
    ('Peaky Blinders', 'Gangsterska rodzina w Birmingham.', 'serial', 2013, 'https://www.imdb.com/title/tt2442560/mediaviewer/rm1542654721/?ref_=tt_ov_i'),
    ('Heweliusz', 'Polski serial o katastrofie promu.', 'serial', 2024, 'https://www.imdb.com/title/tt32253092/mediaviewer/rm2931275778/?ref_=tt_ov_i'),
    ('Star Trek: The Next Generation', 'Załoga USS Enterprise (NCC-1701-D) pod dowództwem Jeana-Luca Picarda odkrywa nowe światy.', 'serial', 1987, 'https://www.imdb.com/title/tt0092455/mediaviewer/rm3121789185/?ref_=tt_ov_i');

-- 5. Oceny
INSERT INTO ratings (id_produkcji, ocena, tresc, data, status_moderacji) VALUES
    (1, 5, 'Absolutny klasyk kina!', '2025-01-10', 'zatwierdzone'),
    (3, 5, 'Przepiękna animacja i historia.', '2025-01-12', 'oczekujące'),
    (5, 4, 'Bardzo zabawne, polecam.', '2025-01-14', 'zatwierdzone');

-- 6. Dostępność na platformach
-- Filmy
INSERT INTO production_platform (id_produkcji, id_platformy, dostepny_sezon) VALUES
    (1, 1, NULL), (1, 2, NULL), -- Matrix (Netflix, Prime)
    (2, 4, NULL),               -- Szklana Pułapka (SkyShowtime)
    (6, 4, NULL),               -- Speed (SkyShowtime)
    (7, 1, NULL),               -- Bullet Train (Netflix)
    (8, 1, NULL),               -- Baby Driver (Netflix)
    (11, 2, NULL);              -- Batman (Prime)

-- Seriale (Rozpisane sezony)
INSERT INTO production_platform (id_produkcji, id_platformy, dostepny_sezon) VALUES
    (3, 3, 1), -- Frieren S1 (Crunchyroll)
    (4, 3, 1), -- My Dress-Up Darling S1 (Crunchyroll)
    (5, 3, 1), (5, 3, 2), (5, 1, 1), -- Spy x Family (Sezony 1-2 na Crunchyroll, S1 na Netflix)
    (9, 1, 1), (9, 1, 2), (9, 1, 3),
    (9, 1, 4), (9, 1, 5), (9, 1, 6),
    (9, 1, 7), (9, 1, 8), (9, 1, 9),
    (9, 1, 10), -- Friends (Wszystkie 10 sezonów na Netflix)
    (10, 1, 1), (10, 1, 2), (10, 1, 3),
    (10, 1, 4), (10, 1, 5), (10, 1, 6), -- Peaky Blinders (Wszystkie 6 sezonów na Netflix)
    (12, 1, 1), -- Heweliusz (Sezon 1 na Netflix)
    (13, 1, 1), (13, 1, 2), (13, 1, 3),
    (13, 1, 4), (13, 1, 5), (13, 1, 6),
    (13, 1, 7), -- Star Trek TNG na Netflix (Sezony 1-7)
    (13, 4, 1), (13, 4, 2), (13, 4, 3); -- Star Trek TNG na SkyShowtime (Sezony 1-3)

-- 7. Przypisanie do kategorii
INSERT INTO production_category (id_produkcji, id_kategorii) VALUES
    (1, 1), (1, 5), -- Matrix: Action, Sci-Fi
    (2, 1), (2, 2), -- Szklana Pułapka: Action, Thriller
    (3, 4),         -- Frieren: Anime
    (4, 4),         -- My Dress-Up Darling: Anime
    (5, 4), (5, 1), -- Spy x Family: Anime, Action
    (6, 1), (6, 2), -- Speed: Action, Thriller
    (7, 1), (7, 7), -- Bullet Train: Action, Comedy
    (8, 1), (8, 2), -- Baby Driver: Action, Thriller
    (9, 7),         -- Friends: Comedy
    (10, 2), (10, 6),-- Peaky Blinders: Thriller, Drama
    (11, 1), (11, 2),-- Batman: Action, Thriller
    (12, 6),        -- Heweliusz: Drama
    (13, 1), (13, 5); -- Action, Sci-Fi