CREATE USER 'user_read_access'@'localhost' IDENTIFIED BY 'z0guserreadaccess';

GRANT SELECT ON *.* TO 'user_read_access'@'localhost';

GRANT SELECT ON `nextversiondb\_20200605`.* TO 'user_read_access'@'localhost';
