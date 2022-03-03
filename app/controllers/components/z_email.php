<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 *
 * ZEmail Component
 * PHP versions 4 and 5
 *
 * Custom email component by Z0g
 * edited by Huynh Le 26-03-2020
 - Allow send Without $to
 - extends cakePHP EmailComponent
 */
App::import('Core', 'Multibyte');
App::import('Core', 'String');
class ZEmailComponent extends Object{
	var $to = null;
	var $from = null;
	var $replyTo = null;
	var $readReceipt = null;
	var $return = null;
	var $cc = array();
	var $bcc = array();

/**
 * The date to put in the Date: header.  This should be a date
 * conformant with the RFC2822 standard.  Leave null, to have
 * today's date generated.
 *
 * @var string
 */
	var $date = null;
	var $subject = null;

/**
 * Associative array of a user defined headers
 * Keys will be prefixed 'X-' as per RFC2822 Section 4.7.5
 *
 * @var array
 * @access public
 */
	var $headers = array();
	var $additionalParams = null;
	var $layout = 'default';
	var $template = null;
	var $lineLength = 70;
	var $lineFeed = PHP_EOL;
	var $_lineLength = null;
	var $sendAs = 'text';
	var $delivery = 'mail';
	var $charset = 'utf-8';
	var $attachments = array();
	var $xMailer = 'CakePHP Email Component';
	var $filePaths = array();

/**
 * List of options to use for smtp mail method
 *
 * Options is:
 * - port
 * - host
 * - timeout
 * - username
 * - password
 * - client
 *
 * @var array
 * @access public
 * @link http://book.cakephp.org/1.3/en/The-Manual/Core-Components/Email.html#sending-a-message-using-smtp
 */
	var $smtpOptions = array();
	var $smtpError = null;
	var $zEmailError = array();
	var $textMessage = null;
	var $htmlMessage = null;
	var $messageId = true;
	var $__header = array();
	var $__boundary = null;
	var $__message = array();
	var $__smtpConnection = null;
	function initialize(&$controller, $settings = array()) {
		$this->Controller =& $controller;
		if (Configure::read('App.encoding') !== null) {
			$this->charset = Configure::read('App.encoding');
		}
		$this->_set($settings);
	}
	function startup(&$controller) {}

/**
 * Send an email using the specified content, template and layout
 *
 * @param mixed $content Either an array of text lines, or a string with contents
 *  If you are rendering a template this variable will be sent to the templates as `$content`
 * @param string $template Template to use when sending email
 * @param string $layout Layout to use to enclose email body
 * @return boolean Success
 * @access public
 */
	function send($content = null, $template = null, $layout = null) {
		$this->_createHeader();

		if ($template) {
			$this->template = $template;
		}

		if ($layout) {
			$this->layout = $layout;
		}

		if (is_array($content)) {
			$content = implode("\n", $content) . "\n";
		}

		$this->htmlMessage = $this->textMessage = null;
		if ($content) {
			if ($this->sendAs === 'html') {
				$this->htmlMessage = $content;
			} elseif ($this->sendAs === 'text') {
				$this->textMessage = $content;
			} else {
				$this->htmlMessage = $this->textMessage = $content;
			}
		}

		if ($this->sendAs === 'text') {
			$message = $this->_wrap($content);
		} else {
			$message = $this->_wrap($content, 998);
		}

		if ($this->template === null) {
			$message = $this->_formatMessage($message);
		} else {
			$message = $this->_render($message);
		}

		$message[] = '';
		$this->__message = $message;

		if (!empty($this->attachments)) {
			$this->_attachFiles();
		}

		if (!empty($this->attachments)) {
			$this->__message[] = '';
			$this->__message[] = '--' . $this->__boundary . '--';
			$this->__message[] = '';
		}

		$_method = '_' . $this->delivery;
		$sent = $this->$_method();

		$this->__header = array();
		$this->__message = array();

		return $sent;
	}

/**
 * Reset all EmailComponent internal variables to be able to send out a new email.
 *
 * @access public
 * @link http://book.cakephp.org/1.3/en/The-Manual/Core-Components/Email.html#sending-multiple-emails-in-a-loop
 */
	function reset() {
		$this->template = null;
		$this->to = array();
		$this->from = null;
		$this->replyTo = null;
		$this->return = null;
		$this->cc = array();
		$this->bcc = array();
		$this->headers = array();
		$this->subject = null;
		$this->additionalParams = null;
		$this->date = null;
		$this->smtpError = null;
		$this->attachments = array();
		$this->htmlMessage = null;
		$this->textMessage = null;
		$this->messageId = true;
		$this->delivery = 'mail';
		$this->__header = array();
		$this->__boundary = null;
		$this->__message = array();
	}

/**
 * Render the contents using the current layout and template.
 *
 * @param string $content Content to render
 * @return array Email ready to be sent
 * @access private
 */
	function _render($content) {
		$viewClass = $this->Controller->view;

		if ($viewClass != 'View') {
			list($plugin, $viewClass) = pluginSplit($viewClass);
			$viewClass = $viewClass . 'View';
			App::import('View', $this->Controller->view);
		}

		$View = new $viewClass($this->Controller);
		$View->layout = $this->layout;
		$msg = array();

		$content = implode("\n", $content);

		if ($this->sendAs === 'both') {
			$htmlContent = $content;
			if (!empty($this->attachments)) {
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: multipart/alternative; boundary="alt-' . $this->__boundary . '"';
				$msg[] = '';
			}
			$msg[] = '--alt-' . $this->__boundary;
			$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			$content = $View->element('email' . DS . 'text' . DS . $this->template, array('content' => $content), true);
			$View->layoutPath = 'email' . DS . 'text';
			$content = explode("\n", $this->textMessage = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($content)));

			$msg = array_merge($msg, $content);

			$msg[] = '';
			$msg[] = '--alt-' . $this->__boundary;
			$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
			$msg[] = 'Content-Transfer-Encoding: 7bit';
			$msg[] = '';

			$htmlContent = $View->element('email' . DS . 'html' . DS . $this->template, array('content' => $htmlContent), true);
			$View->layoutPath = 'email' . DS . 'html';
			$htmlContent = explode("\n", $this->htmlMessage = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($htmlContent)));

			$msg = array_merge($msg, $htmlContent);

			$msg[] = '';
			$msg[] = '--alt-' . $this->__boundary . '--';
			$msg[] = '';

			ClassRegistry::removeObject('view');
			return $msg;
		}

		if (!empty($this->attachments)) {
			if ($this->sendAs === 'html') {
				$msg[] = '';
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: text/html; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			} else {
				$msg[] = '--' . $this->__boundary;
				$msg[] = 'Content-Type: text/plain; charset=' . $this->charset;
				$msg[] = 'Content-Transfer-Encoding: 7bit';
				$msg[] = '';
			}
		}

		$content = $View->element('email' . DS . $this->sendAs . DS . $this->template, array('content' => $content), true);
		$View->layoutPath = 'email' . DS . $this->sendAs;
		$content = explode("\n", $rendered = str_replace(array("\r\n", "\r"), "\n", $View->renderLayout($content)));

		if ($this->sendAs === 'html') {
			$this->htmlMessage = $rendered;
		} else {
			$this->textMessage = $rendered;
		}

		$msg = array_merge($msg, $content);
		ClassRegistry::removeObject('view');

		return $msg;
	}

/**
 * Create unique boundary identifier
 *
 * @access private
 */
	function _createboundary() {
		$this->__boundary = md5(uniqid(time()));
	}

/**
 * Sets headers for the message
 *
 * @access public
 * @param array Associative array containing headers to be set.
 */
	function header($headers) {
		foreach ($headers as $header => $value) {
			$this->__header[] = sprintf('%s: %s', trim($header), trim($value));
		}
	}
/**
 * Create emails headers including (but not limited to) from email address, reply to,
 * bcc and cc.
 *
 * @access private
 */
	function _createHeader() {
        $headers = array();

		if ($this->delivery == 'smtp') {
			$headers['To'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->to));
		}
		$headers['From'] = $this->_formatAddress($this->from);

		if (!empty($this->replyTo)) {
			$headers['Reply-To'] = $this->_formatAddress($this->replyTo);
		}
		if (!empty($this->return)) {
			$headers['Return-Path'] = $this->_formatAddress($this->return);
		}
		if (!empty($this->readReceipt)) {
			$headers['Disposition-Notification-To'] = $this->_formatAddress($this->readReceipt);
		}

		if (!empty($this->cc)) {
			$headers['Cc'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->cc));
		}

		if (!empty($this->bcc) && $this->delivery != 'smtp') {
			$headers['Bcc'] = implode(', ', array_map(array($this, '_formatAddress'), (array)$this->bcc));
		}
		if ($this->delivery == 'smtp') {
			$headers['Subject'] = $this->_encode($this->subject);
		}

		if ($this->messageId !== false) {
			if ($this->messageId === true) {
				$headers['Message-ID'] = '<' . str_replace('-', '', String::uuid()) . '@' . env('HTTP_HOST') . '>';
			} else {
				$headers['Message-ID'] = $this->messageId;
			}
		}

		$date = $this->date;
		if ($date == false) {
			$date = date(DATE_RFC2822);
		}
		$headers['Date'] = $date;

		$headers['X-Mailer'] = $this->xMailer;

		if (!empty($this->headers)) {
			foreach ($this->headers as $key => $val) {
				$headers['X-' . $key] = $val;
			}
		}

		if (!empty($this->attachments) || $this->sendAs === 'both') {
			$this->_createBoundary();
		}

		if (!empty($this->attachments)) {
			$headers['Content-Type'] = 'multipart/mixed; boundary="' . $this->__boundary . '"';
		} elseif ($this->sendAs === 'text') {
			$headers['Content-Type'] = 'text/plain; charset=' . $this->charset;
		} elseif ($this->sendAs === 'html') {
			$headers['Content-Type'] = 'text/html; charset=' . $this->charset;
		} elseif ($this->sendAs === 'both') {
			$headers['Content-Type'] = 'multipart/alternative; boundary="alt-' . $this->__boundary . '"';
		}
		
		$headers['MIME-Version'] = '1.0';
		$headers['Content-Transfer-Encoding'] = '7bit';

        $this->header($headers);
	}

/**
 * Format the message by seeing if it has attachments.
 *
 * @param string $message Message to format
 * @access private
 */
	function _formatMessage($message) {
		if (!empty($this->attachments)) {
			$prefix = array('--' . $this->__boundary);
			if ($this->sendAs === 'text') {
				$prefix[] = 'Content-Type: text/plain; charset=' . $this->charset;
			} elseif ($this->sendAs === 'html') {
				$prefix[] = 'Content-Type: text/html; charset=' . $this->charset;
			} elseif ($this->sendAs === 'both') {
				$prefix[] = 'Content-Type: multipart/alternative; boundary="alt-' . $this->__boundary . '"';
			}
			$prefix[] = 'Content-Transfer-Encoding: 7bit';
			$prefix[] = '';
			$message = array_merge($prefix, $message);
		}
		return $message;
	}

/**
 * Attach files by adding file contents inside boundaries.
 *
 * @access private
 * @TODO: modify to use the core File class?
 */
	function _attachFiles() {
		$files = array();
		foreach ($this->attachments as $filename => $attachment) {
			$file = $this->_findFiles($attachment);
			if (!empty($file)) {
				if (is_int($filename)) {
					$filename = basename($file);
				}
				$files[$filename] = $file;
			}
		}

		foreach ($files as $filename => $file) {
			$handle = fopen($file, 'rb');
			$data = fread($handle, filesize($file));
			$data = chunk_split(base64_encode($data)) ;
			fclose($handle);

			$this->__message[] = '--' . $this->__boundary;
			$this->__message[] = 'Content-Type: application/octet-stream';
			$this->__message[] = 'Content-Transfer-Encoding: base64';
			$this->__message[] = 'Content-Disposition: attachment; filename="' . basename($filename) . '"';
			$this->__message[] = '';
			$this->__message[] = $data;
			$this->__message[] = '';
		}
	}

/**
 * Find the specified attachment in the list of file paths
 *
 * @param string $attachment Attachment file name to find
 * @return string Path to located file
 * @access private
 */
	function _findFiles($attachment) {
		if (file_exists($attachment)) {
			return $attachment;
		}
		foreach ($this->filePaths as $path) {
			if (file_exists($path . DS . $attachment)) {
				$file = $path . DS . $attachment;
				return $file;
			}
		}
		return null;
	}

/**
 * Wrap the message using EmailComponent::$lineLength
 *
 * @param string $message Message to wrap
 * @param integer $lineLength Max length of line
 * @return array Wrapped message
 * @access protected
 */
	function _wrap($message, $lineLength = null) {
		$message = $this->_strip($message, true);
		$message = str_replace(array("\r\n","\r"), "\n", $message);
		$lines = explode("\n", $message);
		$formatted = array();

		if ($this->_lineLength !== null) {
			trigger_error(__('_lineLength cannot be accessed please use lineLength', true), E_USER_WARNING);
			$this->lineLength = $this->_lineLength;
		}

		if (!$lineLength) {
			$lineLength = $this->lineLength;
		}

		foreach ($lines as $line) {
			if (substr($line, 0, 1) == '.') {
				$line = '.' . $line;
			}
			$formatted = array_merge($formatted, explode("\n", wordwrap($line, $lineLength, "\n", true)));
		}
		$formatted[] = '';
		return $formatted;
	}

/**
 * Encode the specified string using the current charset
 *
 * @param string $subject String to encode
 * @return string Encoded string
 * @access private
 */
	function _encode($subject) {
		$subject = $this->_strip($subject);

		$nl = "\r\n";
		if ($this->delivery == 'mail') {
			$nl = '';
		}
		$internalEncoding = function_exists('mb_internal_encoding');
		if ($internalEncoding) {
			$restore = mb_internal_encoding();
			mb_internal_encoding($this->charset);
		}
		$return = mb_encode_mimeheader($subject, $this->charset, 'B', $nl);
		if ($internalEncoding) {
			mb_internal_encoding($restore);
		}
		return $return;
	}

/**
 * Format a string as an email address
 *
 * @param string $string String representing an email address
 * @return string Email address suitable for email headers or smtp pipe
 * @access private
 */
	function _formatAddress($string, $smtp = false) {
		$hasAlias = preg_match('/((.*))?\s?<(.+)>/', $string, $matches);
		if ($smtp && $hasAlias) {
			return $this->_strip('<' .  $matches[3] . '>');
		} elseif ($smtp) {
			return $this->_strip('<' . $string . '>');
		}

		if ($hasAlias && !empty($matches[2])) {
			return $this->_encode(trim($matches[2])) . $this->_strip(' <' . $matches[3] . '>');
		}
		return $this->_strip($string);
	}

/**
 * Remove certain elements (such as bcc:, to:, %0a) from given value.
 * Helps prevent header injection / mainipulation on user content.
 *
 * @param string $value Value to strip
 * @param boolean $message Set to true to indicate main message content
 * @return string Stripped value
 * @access private
 */
	function _strip($value, $message = false) {
		$search  = '%0a|%0d|Content-(?:Type|Transfer-Encoding)\:';
		$search .= '|charset\=|mime-version\:|multipart/mixed|(?:[\n\r]+to|b?cc)\:.*';

		if ($message !== true) {
			$search .= '|\r|\n';
		}
		$search = '#(?:' . $search . ')#i';
		while (preg_match($search, $value)) {
			$value = preg_replace($search, '', $value);
		}
		return $value;
	}

/**
 * Wrapper for PHP mail function used for sending out emails
 *
 * @return bool Success
 * @access private
 */
	function _mail() {
		$header = implode($this->lineFeed, $this->__header);
		$message = implode($this->lineFeed, $this->__message);
		if (is_array($this->to)) {
			$to = implode(', ', array_map(array($this, '_formatAddress'), $this->to));
		} else {
			$to = $this->to;
		}
		if (ini_get('safe_mode')) {
			return @mail($to, $this->_encode($this->subject), $message, $header);
		}
		return @mail($to, $this->_encode($this->subject), $message, $header, $this->additionalParams);
	}


/**
 * Helper method to get socket, overridden in tests
 *
 * @param array $config Config data for the socket.
 * @return void
 * @access protected
 */
	function _getSocket($config) {
		$this->__smtpConnection =& new CakeSocket($config);
	}

/**
 * Sends out email via SMTP
 *
 * @return bool Success
 * @access private
 */
	function _smtp() {
		App::import('Core', array('CakeSocket'));

		$defaults = array(
			'host' => 'localhost',
			'port' => 25,
			'protocol' => 'smtp',
			'timeout' => 30
		);
		$this->smtpOptions = array_merge($defaults, $this->smtpOptions);
		$this->_getSocket($this->smtpOptions);

		if (!$this->__smtpConnection->connect()) {
			$this->smtpError = $this->__smtpConnection->lastError();
			return false;
		} elseif (!$this->_smtpSend(null, '220')) {
			return false;
		}

		$httpHost = env('HTTP_HOST');

		if (isset($this->smtpOptions['client'])) {
			$host = $this->smtpOptions['client'];
		} elseif (!empty($httpHost)) {
			list($host) = explode(':', $httpHost);
		} else {
			$host = 'localhost';
		}

		if (!$this->_smtpSend("EHLO {$host}", '250') && !$this->_smtpSend("HELO {$host}", '250')) {
			return false;
		}

		if (isset($this->smtpOptions['username']) && isset($this->smtpOptions['password'])) {
			$authRequired = $this->_smtpSend('AUTH LOGIN', '334|503');
			if ($authRequired == '334') {
				if (!$this->_smtpSend(base64_encode($this->smtpOptions['username']), '334')) {
					return false;
				}
				if (!$this->_smtpSend(base64_encode($this->smtpOptions['password']), '235')) {
					return false;
				}
			} elseif ($authRequired != '503') {
				return false;
			}
		}

		if (!$this->_smtpSend('MAIL FROM: ' . $this->_formatAddress($this->from, true))) {
			return false;
		}
		$tos = array();
		if (!empty($this->to)){
			if (!is_array($this->to)) {
				$tos = array_map('trim', explode(',', $this->to));
			} else {
				$tos = $this->to;
			}
		}
		foreach ($tos as $to) {
			if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($to, true))) {
				$this->addError($to);
			}
		}
		if (!empty($this->cc)){
			foreach ($this->cc as $cc) {
				if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($cc, true))) {
					$this->addError($cc);
				}
			}
		}
		if (!empty($this->bcc)){
			foreach ($this->bcc as $bcc) {
				if (!$this->_smtpSend('RCPT TO: ' . $this->_formatAddress($bcc, true))) {
					$this->addError($bcc);
				}
			}
		}

		if (!$this->_smtpSend('DATA', '354')) {
			return false;
		}

		$header = implode("\r\n", $this->__header);
		$message = implode("\r\n", $this->__message);
		if (!$this->_smtpSend($header . "\r\n\r\n" . $message . "\r\n\r\n\r\n.")) {
			return false;
		}
		$this->_smtpSend('QUIT', false);

		$this->__smtpConnection->disconnect();
		// debug( $this->zEmailError); 
		return true;
	}
	
	/* Save list error for debug */
	function addError($address){
		$this->zEmailError[$address] = array(
			'email' => $address,
			'error' => $this->smtpError,
			'time' => time(),
		);
	}

/**
 * Protected method for sending data to SMTP connection
 *
 * @param string $data data to be sent to SMTP server
 * @param mixed $checkCode code to check for in server response, false to skip
 * @return bool Success
 * @access protected
 */
	function _smtpSend($data, $checkCode = '250') {
		if (!is_null($data)) {
			$this->__smtpConnection->write($data . "\r\n");
		}
		while ($checkCode !== false) {
			$response = '';
			$startTime = time();
			while (substr($response, -2) !== "\r\n" && ((time() - $startTime) < $this->smtpOptions['timeout'])) {
				$response .= $this->__smtpConnection->read();
			}
			if (substr($response, -2) !== "\r\n") {
				$this->smtpError = 'timeout';
				return false;
			}
			$response = end(explode("\r\n", rtrim($response, "\r\n")));

			if (preg_match('/^(' . $checkCode . ')(.)/', $response, $code)) {
				if ($code[2] === '-') {
					continue;
				}
				return $code[1];
			}
			$this->smtpError = $response;
			return false;
		}
		return true;
	}

/**
 * Set as controller flash message a debug message showing current settings in component
 *
 * @return boolean Success
 * @access private
 */
	function _debug() {
		$nl = "\n";
		$header = implode($nl, $this->__header);
		$message = implode($nl, $this->__message);
		$fm = '<pre>';

		if (is_array($this->to)) {
			$to = implode(', ', array_map(array($this, '_formatAddress'), $this->to));
		} else {
			$to = $this->to;
		}
		$fm .= sprintf('%s %s%s', 'To:', $to, $nl);
		$fm .= sprintf('%s %s%s', 'From:', $this->from, $nl);
		$fm .= sprintf('%s %s%s', 'Subject:', $this->_encode($this->subject), $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Header:', $header, $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Parameters:', $this->additionalParams, $nl);
		$fm .= sprintf('%s%3$s%3$s%s', 'Message:', $message, $nl);
		$fm .= '</pre>';

		if (isset($this->Controller->Session)) {
			$this->Controller->Session->setFlash($fm, 'default', null, 'email');
			return true;
		}
		return $fm;
	}
}
