USE tceron;

CREATE  TABLE users(
    id INT primary key AUTO_INCREMENT,
    username varchar(30) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    data_nascita DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)