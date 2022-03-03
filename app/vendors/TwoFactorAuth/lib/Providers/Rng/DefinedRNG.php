<?php
// debug(1);
// exit;
// if (!defined('TwoFactorAuth_ROOT')) {
	// define('TwoFactorAuth_ROOT', dirname(__FILE__) . '/');
	define('TFAPROVIDERS_RNG_DIR', TwoFactorAuth_ROOT . 'TwoFactorAuth/lib/Providers/Rng/');

	require_once(TFAPROVIDERS_RNG_DIR . 'IRNGProvider.php');
	require_once(TFAPROVIDERS_RNG_DIR . 'CSRNGProvider.php');
	require_once(TFAPROVIDERS_RNG_DIR . 'HashRNGProvider.php');
	require_once(TFAPROVIDERS_RNG_DIR . 'MCryptRNGProvider.php');
	require_once(TFAPROVIDERS_RNG_DIR . 'OpenSSLRNGProvider.php');
	require_once(TFAPROVIDERS_RNG_DIR . 'RNGException.php');
// }
