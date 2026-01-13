ALTER SESSION SET CONTAINER = XEPDB1;
ALTER SESSION SET CURRENT_SCHEMA = recipeasy;

SET DEFINE OFF;

-- Minimal seed for authentication
INSERT INTO roles (role_key) VALUES ('admin');
INSERT INTO roles (role_key) VALUES ('chef');
INSERT INTO roles (role_key) VALUES ('customer');

COMMIT;
