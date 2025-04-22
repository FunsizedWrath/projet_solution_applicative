INSERT INTO role (id_role, name_role) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'employee'),
(4, 'client');

INSERT INTO users (id_user, lastname_user, name_user, email_user, password_user, id_role) VALUES
(1, 'super', 'admin', 'superadmin@email.com', 'password', 1);