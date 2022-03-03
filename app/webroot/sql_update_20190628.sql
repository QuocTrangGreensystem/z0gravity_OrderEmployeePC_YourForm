/* 
By QuanNV 2019-06-28
Ticket 395.
*/
/* Set value update_your_form = 0, fix loi ticket #395 when save user have role is Profile Project Manager */
UPDATE `employees` SET update_your_form = 0 WHERE profile_account > 0;