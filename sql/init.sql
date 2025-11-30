-- criar DB e tabelas
-- Substitua "crudphp" pelo nome que quiser.
CREATE DATABASE crudphp;
\ c crudphp;
-- tabela de usuários
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT now()
);
-- tabela de items (exemplo de CRUD)
CREATE TABLE items (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority SMALLINT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'pendente',
    user_id INTEGER REFERENCES users(id) ON DELETE
    SET NULL,
        created_at TIMESTAMP DEFAULT now(),
        updated_at TIMESTAMP
);
