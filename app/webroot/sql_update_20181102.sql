/* Phần này được lưu chung trong file sql_update_20180816.sql nên không cần update nếu đã update trước đó */
/* Remove chuỗi https://nextversion.z0gravity.com/ for history_filters table*/
/* CHỈ DÙNG CHO NEXTVERSION*/
/* Update by Huynh 2018-11-02 */

/* --------------------------------------------------*/
/* --------- CHỈ DÙNG CHO NEXTVERSION ---------------*/
/* --------- thay chuỗi cho site khác ---------------*/
/* --------------------------------------------------*/

UPDATE history_filters set params =  REPLACE(history_filters.params, 'https://nextversion.z0gravity.com','') where path='rollback_url_employee_when_login';