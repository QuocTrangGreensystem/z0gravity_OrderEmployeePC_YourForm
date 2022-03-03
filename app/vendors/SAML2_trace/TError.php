<?php
 
/**
 * Error class of OneLogin PHP Toolkit
 *
 * Defines the Error class
 */
class OneLogin_Saml2_TError extends OneLogin_Saml2_Error
{
   
    /**
     * Constructor
     *
     * @param string     $msg  Describes the error.
     * @param int        $code The code error (defined in the error class).
     * @param array|null $args Arguments used in the message that describes the error.
     */
    // public function __construct($msg, $code = 0, $args = null)
    // {
        // assert('is_string($msg)');
        // assert('is_int($code)');

        // $message = OneLogin_Saml2_Utils::t($msg, $args);

        // parent::__construct($message, $code);
    // }
    public function msg_error($msg, $code = 0){
		assert('is_string($msg)');
        assert('is_int($code)');
        $message = OneLogin_Saml2_Utils::t($msg);
		if( $code) $message .= '<br> Error Code: ' . $code;
		return $message;
	}
}

/**
 * This class implements another custom Exception handler,
 * related to exceptions that happens during validation process.
 */
class OneLogin_Saml2_TValidationError extends OneLogin_Saml2_ValidationError
{
   
    /**
     * Constructor
     *
     * @param string     $msg  Describes the error.
     * @param int        $code The code error (defined in the error class).
     * @param array|null $args Arguments used in the message that describes the error.
     */
    // public function __construct($msg, $code = 0, $args = null)
    // {
        // assert('is_string($msg)');
        // assert('is_int($code)');

        // $message = OneLogin_Saml2_Utils::t($msg, $args);

        // parent::__construct($message, $code);
    // }
	public function msg_error($msg, $code = 0){
		assert('is_string($msg)');
        assert('is_int($code)');
        $message = OneLogin_Saml2_Utils::t($msg);
		if( $code) $message .= '<br> Error Code: ' . $code;
		return $message;
	}
}
