<?php
/******************************************************
* Programmer: Ben Lobaugh (ben <AT> lobaugh <DOT> net)
* File: Encryption.class.php
* Site: N/A
* Creation Date: 04-04-2007
* Modified Date: 04-04-2007
* Description: Class provides wrapper functionality
*	to the standard php mcrypt functions.
*	This class was created with eaze of use in
*	mind as most programmers, particularly
*	myself, do not want to worry about all the
*	extra junk that goes into encrypting something
*	and just want to do it.
* Extra info: Encryptions is key based. Do not loose
*	the key or your are hosed.
******************************************************/

/***** List of functions *****
* Encryption Encryption($key=NULL, $cipher='MCRYPT_RIJNDAEL_256', $mode='MCRYPT_MODE_ECB')
* String encrypt($s)
* String decrypt($s)
****** List of functions ****/



class Encryption {

	/**
	* @var string - This must be overloaded by child classes
	**/
	var $key;

	/**
	* @var - What encryption mode is used?
	**/
	var $mode;

	/**
	* @var string - What type of cipher to use. See http://us3.php.net/mcrypt for supported ciphers.
	**/
	var $cipher;

	/**
	* Sets up the Encryption class
	*
	* @param string $key - Private key, or salt, used to create encryption
	* @param string $cipher - Type of cipher used to encrypt. See http://us3.php.net/mcrypt for supported
	*	ciphers.
	* @param string $mode - Encryption mode
	* @return Encryption
	**/
	function __construct($key=NULL, $cipher='MCRYPT_RIJNDAEL_256', $mode='MCRYPT_MODE_ECB') {
		// Set key
		if($key == NULL) {
		}
		$this->key = $key;
		// Set the cipher.
		$this->cipher = $cipher;
		// Set the mode
		$this->mode = $mode;

	}

	/**
	* Encrypts a given input based upon the key with a specified cipher.
	* In theory anything may be passed in and encrypted.
	*
	* @param mixed $s - Object to encrypt
	* @return string - Encrypted value
	**/
	function encrypt($s) {
		//$s = serialize($s);
		$value = NULL;
		// Check key. If key is NULL alert user and die
		if($this->key !== NULL) {
			// Key good start encrypting

			//$iv_size = mcrypt_get_iv_size($this->cipher, $this->mode);
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			//$value = mcrypt_encrypt($this->cipher, $this->key, $s, $this->mode, $iv);
			$value = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $s, MCRYPT_MODE_ECB, $iv);
		} else {
			die("You MUST generate a key to encrypt! Key must be the same for decrypt to work properly. Encryption.encrypt");
		}

		return $value;
	}

	/**
	* Decrypts a given input based upon the key with a specified cipher.
	* In theory anything may be decrypted.
	*
	* @param string $s - Encrypted value
	* @return mixed - Decrypted value
	**/
	function decrypt($s) {
		$value = NULL;
		// Check key. If key is NULL alert user and die
		if($this->key !== NULL) {
			// Key good start encrypting

			//$iv_size = mcrypt_get_iv_size($this->cipher, $this->mode);
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			//$value = mcrypt_decrypt($this->cipher, $this->key, $s, $this->mode, $iv);
			$value = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $s, MCRYPT_MODE_ECB, $iv));
			$value = unserialize($value);
		} else {
			die("You MUST have a key to decrypt! Key must be the same as key used to encrypt. Encryption.decrypt");
		}

		return $value;
	}

} // end class Encryption
