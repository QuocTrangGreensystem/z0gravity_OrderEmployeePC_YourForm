/* Edit by Huynh */ 
/* Add updated and description for table auth_codes */

ALTER table `auth_codes`
add column `updated` datetime DEFAULT NULL;
ALTER table `auth_codes`
add column `description` varchar(255) DEFAULT NULL;
