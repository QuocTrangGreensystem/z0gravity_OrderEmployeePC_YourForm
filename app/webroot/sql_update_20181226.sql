/* Edit by Huynh */ 
/* Add Name and IP for table auth_codes */
ALTER table `auth_codes`
add column `name` varchar(255) DEFAULT NULL;

ALTER table `auth_codes`
add column `ip` varchar(40) DEFAULT NULL;

