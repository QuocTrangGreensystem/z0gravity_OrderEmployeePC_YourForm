<?phpif (!defined('TwoFactorAuth_ROOT')) {
	define('TwoFactorAuth_ROOT', dirname(__FILE__) . '/');
	require_once(TwoFactorAuth_ROOT . 'TwoFactorAuth/lib/Providers/Qr/DefinedQR.php');	require_once(TwoFactorAuth_ROOT . 'TwoFactorAuth/lib/Providers/Rng/DefinedRNG.php');	require_once(TwoFactorAuth_ROOT . 'TwoFactorAuth/lib/TwoFactorAuth.php');
}
