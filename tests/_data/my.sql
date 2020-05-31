DROP TABLE IF EXISTS `migrations-create-table`;
DROP TABLE IF EXISTS `migrations-load`;
DROP TABLE IF EXISTS `migrations-add`;
DROP TABLE IF EXISTS `migrations-remove-by-id`;
DROP TABLE IF EXISTS `command-table`;
DROP TABLE IF EXISTS `command-migrate`;
DROP TABLE IF EXISTS `command-rollback`;

CREATE TABLE `migrations-load`
(
    `id`   BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(256)                      NOT NULL,
    `date` TIMESTAMP                         NOT NULL
);
INSERT INTO `migrations-load` (`name`, `date`)
VALUES ('init', '2019-01-01 23:00:00'),
       ('name 1', '2019-01-01 23:00:00'),
       ('name 2', '2019-01-01 23:00:00');
CREATE TABLE `migrations-add`
(
    `id`   BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(256)                      NOT NULL,
    `date` TIMESTAMP                         NOT NULL
);
CREATE TABLE `migrations-remove-by-id`
(
    `id`   BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(256)                      NOT NULL,
    `date` TIMESTAMP                         NOT NULL
);
INSERT INTO `migrations-remove-by-id`(`name`, `date`)
VALUES ('init', '2019-01-01 23:00:00'),
       ('name 1', '2019-01-01 23:00:00'),
       ('name 2', '2019-01-01 23:00:00');