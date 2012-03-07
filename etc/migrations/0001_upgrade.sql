BEGIN TRANSACTION;

CREATE TABLE storage_units (
    key CHAR(2) PRIMARY KEY,
    x INTEGER NOT NULL,
    y INTEGER NOT NULL,
    orientation INTEGER NOT NULL
);

CREATE TABLE storage_bays (
    key CHAR(2) PRIMARY KEY,
    x INTEGER NOT NULL,
    y INTEGER NOT NULL
);

CREATE TABLE storage_locations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name CHAR(6) UNIQUE,
    shelf CHAR(2) NOT NULL REFERENCES storage_units(key),
    bay CHAR(2) NOT NULL REFERENCES storage_bays(key),
    UNIQUE(shelf, bay)
);

CREATE TABLE boxes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    owner_id INTEGER REFERENCES users(id),
    creator_id INTEGER NOT NULL REFERENCES users(id),
    owned BOOLEAN NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT 1,
    location_id INTEGER REFERENCES storage_locations(id)
);

INSERT INTO storage_units (key, x, y, orientation) VALUES ('01', 11, 225, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('02', 11, 163, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('03', 11, 101, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('04', 11, 40, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('05', 75, 11, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('06', 75, 73, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('07', 75, 135, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('08', 75, 197, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('09', 75, 259, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('10', 76, 321, 2);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('11', 107, 259, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('12', 107, 197, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('13', 107, 135, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('14', 107, 73, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('15', 107, 11, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('16', 171, 11, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('17', 171, 73, 3);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('18', 203, 73, 1);
INSERT INTO storage_units (key, x, y, orientation) VALUES ('19', 203, 11, 1);

INSERT INTO storage_bays (key, x, y) VALUES ('01', 18, 303);
INSERT INTO storage_bays (key, x, y) VALUES ('02', 104, 303);
INSERT INTO storage_bays (key, x, y) VALUES ('03', 18, 255);
INSERT INTO storage_bays (key, x, y) VALUES ('04', 104, 255);
INSERT INTO storage_bays (key, x, y) VALUES ('05', 18, 207);
INSERT INTO storage_bays (key, x, y) VALUES ('06', 104, 207);
INSERT INTO storage_bays (key, x, y) VALUES ('07', 18, 159);
INSERT INTO storage_bays (key, x, y) VALUES ('08', 104, 159);
INSERT INTO storage_bays (key, x, y) VALUES ('09', 18, 111);
INSERT INTO storage_bays (key, x, y) VALUES ('10', 104, 111);
INSERT INTO storage_bays (key, x, y) VALUES ('11', 18, 63);
INSERT INTO storage_bays (key, x, y) VALUES ('12', 104, 63);
INSERT INTO storage_bays (key, x, y) VALUES ('13', 18, 15);
INSERT INTO storage_bays (key, x, y) VALUES ('14', 104, 15);

INSERT INTO storage_locations (name, shelf, bay) SELECT 's'||storage_units.key||'b'||storage_bays.key, storage_units.key, storage_bays.key FROM storage_units, storage_bays;

END TRANSACTION;