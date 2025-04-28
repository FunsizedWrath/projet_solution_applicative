CREATE TABLE Record(
   id_record INTEGER,
   PRIMARY KEY(id_record)
);

CREATE TABLE Role(
   id_role INTEGER,
   name_role TEXT NOT NULL,
   PRIMARY KEY(id_role),
   UNIQUE(name_role)
);

CREATE TABLE Permission(
   id_permission INTEGER,
   name_permission TEXT NOT NULL,
   PRIMARY KEY(id_permission),
   UNIQUE(name_permission)
);

CREATE TABLE Subscription_type(
   id_subscription_type INTEGER,
   name_subscription_type TEXT NOT NULL,
   duration_subscription_type INTEGER,
   price_subscription_type NUMERIC,
   PRIMARY KEY(id_subscription_type),
   UNIQUE(name_subscription_type)
);


CREATE TABLE Location(
   id_location INTEGER,
   name_location TEXT NOT NULL,
   description_location TEXT,
   PRIMARY KEY(id_location),
   UNIQUE(name_location)
);

CREATE TABLE Document(
   id_document INTEGER,
   title_document TEXT NOT NULL,
   publishing_date_document NUMERIC NOT NULL,
   description_document TEXT,
   acquisition_date_document NUMERIC NOT NULL,
   id_location INTEGER NOT NULL,
   PRIMARY KEY(id_document),
   FOREIGN KEY(id_location) REFERENCES Location(id_location)
);

CREATE TABLE Tag(
   id_tag INTEGER,
   name_tag TEXT NOT NULL,
   PRIMARY KEY(id_tag),
   UNIQUE(name_tag)
);

CREATE TABLE Users(
   id_user INTEGER,
   lastname_user TEXT NOT NULL,
   name_user TEXT NOT NULL,
   email_user TEXT NOT NULL,
   phone_user INTEGER,
   address_user TEXT,
   postcode_user TEXT,
   password_user TEXT,
   city_user TEXT,
   id_subscription INTEGER,
   id_role INTEGER NOT NULL DEFAULT 4,
   PRIMARY KEY(id_user),
   FOREIGN KEY(id_subscription) REFERENCES Subscription(id_subscription),
   FOREIGN KEY(id_role) REFERENCES Role(id_role)
);

CREATE TABLE Book(
   id_document INTEGER,
   author_book TEXT,
   nbr_words_book TEXT,
   publisher_book TEXT,
   PRIMARY KEY(id_document),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Disk(
   id_document INTEGER,
   artist_disk TEXT,
   producer_disk TEXT,
   director_disk TEXT,
   PRIMARY KEY(id_document),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Role_permission(
   id_role INTEGER,
   id_permission INTEGER,
   PRIMARY KEY(id_role, id_permission),
   FOREIGN KEY(id_role) REFERENCES Role(id_role),
   FOREIGN KEY(id_permission) REFERENCES Permission(id_permission)
);

CREATE TABLE Dispute(
   id_user INTEGER,
   id_record INTEGER,
   id_document INTEGER,
   type_dispute TEXT NOT NULL,
   description_dispute TEXT,
   status_dispute TEXT,
   start_date_dispute NUMERIC,
   end_date_dispute NUMERIC,
   PRIMARY KEY(id_user, id_record, id_document),
   FOREIGN KEY(id_user) REFERENCES Users(id_user),
   FOREIGN KEY(id_record) REFERENCES Record(id_record),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Subscription(
   id_subscription INTEGER,
   end_date_subscription NUMERIC,
   start_date_subscription NUMERIC,
   id_subscription_type INTEGER NOT NULL,
   id_user INTEGER NOT NULL,
   PRIMARY KEY(id_subscription),
   FOREIGN KEY(id_subscription_type) REFERENCES Subscription_type(id_subscription_type),
   FOREIGN KEY(id_user) REFERENCES Users(id_user)
);


CREATE TABLE Borrowed(
   id_borrowed INTEGER,
   date_borrowed NUMERIC NOT NULL,
   return_date_borrowed NUMERIC,
   id_user INTEGER NOT NULL,
   id_document INTEGER NOT NULL,
   PRIMARY KEY(id_borrowed),
   FOREIGN KEY(id_user) REFERENCES Users(id_user),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);


CREATE TABLE Document_tag(
   id_document INTEGER,
   id_tag INTEGER,
   PRIMARY KEY(id_document, id_tag),
   FOREIGN KEY(id_document) REFERENCES Document(id_document),
   FOREIGN KEY(id_tag) REFERENCES Tag(id_tag)
);


INSERT INTO Users VALUES (1,'super','admin','superadmin@email.com',NULL,NULL,NULL,'$2y$10$KXGI.JGpzKMZ4PFlSLX6DeNWO1/hKg0fu52rBQvcHDzgwzNpIw0EO',NULL,1);

INSERT INTO role (id_role, name_role) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'employee'),
(4, 'client');