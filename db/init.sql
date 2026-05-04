CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users (email);

INSERT INTO users (name, email, password_hash) VALUES
    ('Admin', 'admin@click-store.local', '$2y$12$LBYvlT.LZhr3E3KL2D51v.FBShnO17sUYGe5wBo2EVSky2IqeMBgG'),
    ('User', 'user@click-store.local', '$2y$12$nggum26xjPvVif2/pQowEeUH3Xh3S7uXjXLb63NDmyo7Q5V.5yHD6');
