<?php
// debug(1);
// exit;
// if (!defined('TwoFactorAuth_ROOT')) {
	// define('TwoFactorAuth_ROOT', dirname(__FILE__) . '/');
	define('TFAPROVIDERS_QR_DIR', TwoFactorAuth_ROOT . 'TwoFactorAuth/lib/Providers/Qr/');

	require_once(TFAPROVIDERS_QR_DIR . 'IQRCodeProvider.php');
	require_once(TFAPROVIDERS_QR_DIR . 'BaseHTTPQRCodeProvider.php');
	require_once(TFAPROVIDERS_QR_DIR . 'GoogleQRCodeProvider.php');
	require_once(TFAPROVIDERS_QR_DIR . 'QRException.php');
	require_once(TFAPROVIDERS_QR_DIR . 'QRicketProvider.php');
	require_once(TFAPROVIDERS_QR_DIR . 'QRServerProvider.php');
// }
