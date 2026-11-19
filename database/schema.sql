CREATE TABLE IF NOT EXISTS aulas (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS reservas (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  aula_id INTEGER NOT NULL,
  fecha TEXT NOT NULL,
  hora TEXT NOT NULL,
  responsable TEXT NOT NULL,
  FOREIGN KEY (aula_id) REFERENCES aulas(id),
  UNIQUE (aula_id, fecha, hora)
);

INSERT INTO aulas (nombre) VALUES ('Aula 1');
INSERT INTO aulas (nombre) VALUES ('Aula 2');
INSERT INTO aulas (nombre) VALUES ('Aula 3');