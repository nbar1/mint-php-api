<?php
/*
 * Mint
 *
 * Mint API for logging in and returning transaction CSV
 */
class Mint
{
	/**
	 * Mint.com email address
	 */
	private $_email;

	/**
	 * Mint.com password
	 */
	private $_password;

	/**
	 * Writable text file for cookie storage
	 */
	private $_cookie_jar;

	/**
	 * Constructor
	 *
	 * @param String $email Mint.com email address
	 * @param String $password Mint.com password
	 * @param String $cookie_jar Writable text file for cookie storage
	 */
	public function __construct($email, $password, $cookie_jar)
	{
		$this->_email = $email;
		$this->_password = $password;
		$this->_cookie_jar = $cookie_jar;
	}

	/**
	 * Returns a formatted post payload for logging in
	 *
	 * @return Array Post payload
	 */
	private function _getPostPayload()
	{
		$fields_string = '';
		$fields = array(
			'username' => $this->_email,
			'password' => $this->_password,
			'task' => 'L',
			'nextPage' => 'transactionDownload.event',
		);
		foreach ($fields as $k=>$v)
		{
			$fields_string .= $k . '=' . $v . '&';
		}
		return rtrim($fields_string, '&');
	}

	/**
	 * Authenticate to Mint.com and return transactions
	 *
	 * @return String CSV of Mint.com transactions
	 */
	public function getTransactions()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wwws.mint.com/loginUserSubmit.xevent');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_getPostPayload());
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookie_jar);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === false)
		{
			throw new Exception("Error connecting to Mint.com service");
		}

		return $result;
	}
}
?>
