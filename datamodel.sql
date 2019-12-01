--++++++++++++++++++++++++++++++
-- CREATE TABLES users, objects, reviews, files
-- 
--++++++++++++++++++++++++++++++
create table users(
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    email VARCHAR(60) NOT NULL,
    password VARCHAR(60) NOT NULL,
    country VARCHAR(30) NOT NULL,
    postal_code VARCHAR(30),
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
);

create table objects(
    business_name VARCHAR(100) NOT NULL, 
    description VARCHAR(500) NOT NULL, 
    latitude decimal(10,6) NOT NULL, 
    longitude decimal(10,6) NOT NULL, 
    image_url text,owner_id INT UNSIGNED, 
    rating decimal(2,1) DEFAULT 0,
    object_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
);

create table reviews(
    rating decimal(2,1) NOT NULL, 
    date DATE NOT NULL, 
    content VARCHAR(500) NOT NULL, 
    user_id INT UNSIGNED NOT NULL, 
    object_id INT UNSIGNED NOT NULL, 
    review_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    foreign key (object_id) references objects(object_id) on delete cascade,
    foreign key (user_id) references users(user_id) on delete cascade
);

create table files(
    object_id INT NOT NULL, 
    path VARCHAR(250) NOT NULL, 
    pic_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    foreign key (object_id) references objects(object_id) on delete cascade
);