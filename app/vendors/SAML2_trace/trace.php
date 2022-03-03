<?php 
// Replace function for trace
if (file_exists( __DIR__ .'/TAuth.php')) {
    include_once  __DIR__ .'/TAuth.php';
}
if (file_exists( __DIR__ .'/TError.php')) {
    include_once  __DIR__ .'/TError.php';
}
if (file_exists( __DIR__ .'/TResponse.php')) {
    include_once  __DIR__ .'/TResponse.php';
}