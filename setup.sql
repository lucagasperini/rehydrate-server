CREATE TABLE account (
        name varchar(16) primary key,
        pass char(64) NOT NULL,
        daily_need int NOT NULL,
        timezone varchar(64)
);

CREATE TABLE token (
        id SERIAL primary key, 
        token char(512) NOT NULL unique,
        fk_name varchar(16) NOT NULL, 
        FOREIGN KEY(fk_name) REFERENCES account(name),
        expire int NOT NULL
);

CREATE TABLE history (
        id SERIAL primary key,
        fk_token_id int NOT NULL,
        FOREIGN KEY(fk_token_id) REFERENCES token(id),
        quantity int NOT NULL,
        time int NOT NULL
);