CREATE TABLE Record(
   id_record INT,
   PRIMARY KEY(id_record)
);

CREATE TABLE Role(
   id_role INT,
   name_role VARCHAR(50)  NOT NULL,
   PRIMARY KEY(id_role),
   UNIQUE(name_role)
);

CREATE TABLE Permission(
   id_permission INT,
   name_permission VARCHAR(50)  NOT NULL,
   PRIMARY KEY(id_permission),
   UNIQUE(name_permission)
);

CREATE TABLE Subscription(
   id_subscription INT,
   name_subscription VARCHAR(50)  NOT NULL,
   PRIMARY KEY(id_subscription),
   UNIQUE(name_subscription)
);

CREATE TABLE Location(
   id_location INT,
   name_location VARCHAR(50)  NOT NULL,
   description_location VARCHAR(50) ,
   PRIMARY KEY(id_location),
   UNIQUE(name_location)
);

CREATE TABLE Document(
   id_document INT,
   name_document VARCHAR(50)  NOT NULL,
   publishing_date_document DATE NOT NULL,
   description_document VARCHAR(50) ,
   acquisition_date_document DATE NOT NULL,
   PRIMARY KEY(id_document)
);

CREATE TABLE Tag(
   id_tag INT,
   name_tag VARCHAR(50)  NOT NULL,
   PRIMARY KEY(id_tag),
   UNIQUE(name_tag)
);

CREATE TABLE Users(
   id_user INT,
   lastname_user VARCHAR(50)  NOT NULL,
   name_user VARCHAR(50)  NOT NULL,
   email_user VARCHAR(50)  NOT NULL,
   phone_user INT,
   address_user VARCHAR(50) ,
   postcode_user VARCHAR(50) ,
   password_user VARCHAR(50) ,
   city_user VARCHAR(50) ,
   id_role INT NOT NULL,
   PRIMARY KEY(id_user),
   FOREIGN KEY(id_role) REFERENCES Role(id_role)
);

CREATE TABLE Book(
   id_document INT,
   author_book VARCHAR(50) ,
   nbr_words_book VARCHAR(50) ,
   publisher_book VARCHAR(50) ,
   PRIMARY KEY(id_document),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Disk(
   id_document INT,
   artist_disk VARCHAR(50) ,
   producer_disk VARCHAR(50) ,
   director_disk VARCHAR(50) ,
   PRIMARY KEY(id_document),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Role_permission(
   id_role INT,
   id_permission INT,
   PRIMARY KEY(id_role, id_permission),
   FOREIGN KEY(id_role) REFERENCES Role(id_role),
   FOREIGN KEY(id_permission) REFERENCES Permission(id_permission)
);

CREATE TABLE Dispute(
   id_user INT,
   id_record INT,
   id_document INT,
   type_dispute VARCHAR(50)  NOT NULL,
   description_dispute VARCHAR(50) ,
   status_dispute VARCHAR(50) ,
   PRIMARY KEY(id_user, id_record, id_document),
   FOREIGN KEY(id_user) REFERENCES Users(id_user),
   FOREIGN KEY(id_record) REFERENCES Record(id_record),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Subscribed(
   id_user INT,
   id_subscription INT,
   PRIMARY KEY(id_user, id_subscription),
   FOREIGN KEY(id_user) REFERENCES Users(id_user),
   FOREIGN KEY(id_subscription) REFERENCES Subscription(id_subscription)
);

CREATE TABLE Borrowed(
   id_user INT,
   id_document INT,
   PRIMARY KEY(id_user, id_document),
   FOREIGN KEY(id_user) REFERENCES Users(id_user),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Put_away(
   id_location INT,
   id_document INT,
   PRIMARY KEY(id_location, id_document),
   FOREIGN KEY(id_location) REFERENCES Location(id_location),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

CREATE TABLE Document_tag(
   id_document INT,
   id_tag INT,
   PRIMARY KEY(id_document, id_tag),
   FOREIGN KEY(id_document) REFERENCES Document(id_document),
   FOREIGN KEY(id_tag) REFERENCES Tag(id_tag)
);
