<?php
/**
 * Ideal FuelPHP package - Dutch online bank payments
 *
 * @package    Fuel
 * @version    1.0
 * @author     Harro "WanWizard" Verton
 * @license    MIT License
 * @copyright  2012 Exite Development Services
 * @link       http://exite.eu
 */

/**
 * iDEAL classes transaction logic based on code in idealprofessional.cls.5.php by
 * Martijn Wieringa, PHP Solutions, info@php-solutions.nl
 *
 * The original code doesn't contain any license information, but the text on the
 * website (http://www.ideal-simulator.nl/ideal-scripts-en-plugins-algemeen.html)
 * indicates the code is free to be used by anyone.
 */

namespace Ideal;

class IdealConnectionException extends IdealException {};

class IdealProcessException extends IdealException {};

class IdealCertificateException extends IdealException {};

/**
 * Ideal
 *
 * @package     Fuel
 * @subpackage  Ideal
 */
class Ideal_Professional
{
	/**
	 * @var	array	driver configuration
	 */
	protected $config = array();

	/**
	 * Constructor, store the configuration passed
	 */
	public function __construct($config)
	{
		// we need OpenSSL to be available
		if ( ! defined('OPENSSL_VERSION_NUMBER'))
		{
			throw new \IdealException('OpenSSL PHP extension is required to use iDEAL professional services');
		}

		// store the config passed
		$this->config = $this->validate($config);
	}

	/**
	 * request the list of issuers
	 *
	 * @return	array
	 */
	 public function get_issuers()
	 {
		 // check for a cached issuers list
		try
		{
			$issuers = \Cache::get('ideal.issuers');
		}

		 // not in cache, request it from the service provider
		catch (\CacheNotFoundException $e)
		{
			// construct the message timestamp
			$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';

			// calculate the token and token code
			$message = $timestamp.$this->config['merchant_id'].$this->config['sub_id'];

			// construct the XML message
			$message = '<?xml version="1.0" encoding="UTF-8" ?>'.LF.
				'<DirectoryReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">'.LF.
				'<createDateTimeStamp>'.$this->xml_escape($timestamp).'</createDateTimeStamp>'.LF.
				'<Merchant>'.LF.
				'<merchantID>'.$this->xml_escape($this->config['merchant_id']).'</merchantID>'.LF.
				'<subID>'.$this->xml_escape($this->config['sub_id']).'</subID>'.LF.
				'<authentication>SHA1_RSA</authentication>'.LF.
				'<token>'.$this->xml_escape($this->security_get_fingerprint()).'</token>'.LF.
				'<tokenCode>'.$this->xml_escape($this->security_get_signature($message)).'</tokenCode>'.LF.
				'</Merchant>'.LF.
				'</DirectoryReq>';

			// send the message and fetch the reply
			$url = $this->config['testmode'] ? 'testurl' : 'url';
			$reply = $this->send_data($this->config['bank']['professional']['issuer'][$url], $message, 10);

			// process the reply
			if ($reply)
			{
				// check for reported errors
				if ($this->get_xml_tag('errorCode', $reply))
				{
					throw new \IdealProcessException($this->get_xml_tag('errorCode', $reply).' - '.$this->get_xml_tag('errorMessage', $reply).' - '.$this->get_xml_tag('errorDetail', $reply));
				}
				else
				{
					$shortlist = array();
					$longlist = array();

					// parse the reply
					while(strpos($reply, '<issuerID>'))
					{
						// fetch issuer id, name and list
						$issuer_id = $this->get_xml_tag('issuerID', $reply);
						$issuer_name = $this->get_xml_tag('issuerName', $reply);
						$issuer_list = $this->get_xml_tag('issuerList', $reply);

						// short or long list received
						if (strcmp($issuer_list, 'Short') === 0)
						{
							$shortlist[$issuer_id] = $issuer_name;
						}
						else
						{
							$longlist[$issuer_id] = $issuer_name;
						}

						// cut the bit we've just processed
						$reply = substr($reply, strpos($reply, '</issuerList>') + 13);
					}

					// merge the two lists
					$issuers = array_merge($shortlist, $longlist);

					// and write the list to cache (when not in development mode!)
					if (\Fuel::$env != \Fuel::DEVELOPMENT)
					{
						// expire the cache every 6 hours
						\Cache::set('ideal.issuers', $issuers, 3600 * 6);
					}
				}
			}
			else
			{
				throw new \IdealConnectionException('No data received from Issuer request');
			}

		}

		// return the issuers list
		return $issuers;
	 }

	/**
	 * prepare a transaction for execution
	 *
	 * @return	array
	 */
	 public function transaction_prepare()
	 {
		// construct the message timestamp
		$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';

		// calculate the token and token code
		$message = $this->multiline($timestamp.$this->config['issuer'].$this->config['merchant_id'].$this->config['sub_id'].$this->config['return_url'].$this->config['reference'].$this->config['amount'].$this->config['currency'].strtolower($this->config['language']).$this->config['description'].$this->config['entrance_code']);

		// construct the XML message
		$message = '<?xml version="1.0" encoding="UTF-8" ?>'.LF.
			'<AcquirerTrxReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">'.LF.
			'<createDateTimeStamp>'.$this->xml_escape($timestamp).'</createDateTimeStamp>'.LF.
			'<Issuer>'.LF.
			'<issuerID>'.$this->xml_escape($this->config['issuer']).'</issuerID>'.LF.
			'</Issuer>'.LF.
			'<Merchant>'.LF.
			'<merchantID>'.$this->xml_escape($this->config['merchant_id']).'</merchantID>'.LF.
			'<subID>'.$this->xml_escape($this->config['sub_id']) . '</subID>'.LF.
			'<authentication>SHA1_RSA</authentication>'.LF.
			'<token>'.$this->xml_escape($this->security_get_fingerprint()).'</token>'.LF.
			'<tokenCode>'.$this->xml_escape($this->security_get_signature($message)).'</tokenCode>'.LF.
			'<merchantReturnURL>'.$this->xml_escape($this->config['return_url']).'</merchantReturnURL>'.LF.
			'</Merchant>'.LF.
			'<Transaction>'.LF.
			'<purchaseID>'.$this->xml_escape($this->config['reference']) . '</purchaseID>'.LF.
			'<amount>'.$this->xml_escape($this->config['amount']).'</amount>'.LF.
			'<currency>EUR</currency>'.LF.
			'<expirationPeriod>PT30M</expirationPeriod>'.LF.
			'<language>nl</language>'.LF.
			'<description>'.$this->xml_escape($this->config['description']).'</description>'.LF.
			'<entranceCode>'.$this->xml_escape($this->config['entrance_code']).'</entranceCode>'.LF.
			'</Transaction>'.LF.
			'</AcquirerTrxReq>';

		// send the message and fetch the reply
		$url = $this->config['testmode'] ? 'testurl' : 'url';
		$reply = $this->send_data($this->config['bank']['professional']['transaction'][$url], $message, 10);

		// process the reply
		if ($reply)
		{
			// check for reported errors
			if ($this->get_xml_tag('errorCode', $reply))
			{
				throw new \IdealProcessException($this->get_xml_tag('errorCode', $reply).' - '.$this->get_xml_tag('errorMessage', $reply).' - '.$this->get_xml_tag('errorDetail', $reply));
			}
			else
			{
				$transaction_id = $this->get_xml_tag('transactionID', $reply);
				$this->transaction_url = html_entity_decode($this->get_xml_tag('issuerAuthenticationURL', $reply));
			}
		}

		return $transaction_id;
	 }

	/**
	 * execute transaction for execution
	 *
	 * @return	void
	 */
	 public function transaction_execute()
	 {
		 if ($this->transaction_url)
		 {
			 \Response::redirect($this->transaction_url);
			 exit;
		 }
		 else
		 {
			throw new \IdealProcessException('Execute transaction called without a valid prepared transaction available');
		 }
	 }

	/**
	 * fetch the status of a transaction id
	 *
	 * @return	array
	 */
	 public function transaction_status($transaction_id = null)
	 {
		// check if we have a transaction id
		if (func_num_args() == 0)
		{
			throw new \IdealProcessException('No transaction ID specified when requesting a transaction status');
		}

		// construct the message timestamp
		$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';

		// calculate the token and token code
		$message = $timestamp.$this->config['merchant_id'].$this->config['sub_id'].$transaction_id;

		// construct the XML message
		$message = '<?xml version="1.0" encoding="UTF-8" ?>'.LF.
			'<AcquirerStatusReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">'.LF.
			'<createDateTimeStamp>'.$this->xml_escape($timestamp).'</createDateTimeStamp>'.LF.
			'<Merchant>'.
			'<merchantID>'.$this->xml_escape($this->config['merchant_id']).'</merchantID>'.LF.
			'<subID>'.$this->xml_escape($this->config['sub_id']) . '</subID>'.LF.
			'<authentication>SHA1_RSA</authentication>'.LF.
			'<token>'.$this->xml_escape($this->security_get_fingerprint()).'</token>'.LF.
			'<tokenCode>'.$this->xml_escape($this->security_get_signature($message)).'</tokenCode>'.LF.
			'</Merchant>'.LF.
			'<Transaction>'.
			'<transactionID>'.$this->xml_escape($transaction_id).'</transactionID>'.LF.
			'</Transaction>'.
			'</AcquirerStatusReq>';

		// send the message and fetch the reply
		$url = $this->config['testmode'] ? 'testurl' : 'url';
		$reply = $this->send_data($this->config['bank']['professional']['status'][$url], $message, 10);

		// process the reply
		if ($reply)
		{
			// check for reported errors
			if ($this->get_xml_tag('errorCode', $reply))
			{
				throw new \IdealProcessException($this->get_xml_tag('errorCode', $reply).' - '.$this->get_xml_tag('errorMessage', $reply).' - '.$this->get_xml_tag('errorDetail', $reply));
			}
			else
			{
				// parse the result
				$result = array(
					'timestamp' => $this->get_xml_tag('createDateTimeStamp', $reply),
					'transaction_id' => $this->get_xml_tag('transactionID', $reply),
					'transaction_status' => $this->get_xml_tag('status', $reply),
					'account_number' => $this->get_xml_tag('consumerAccountNumber', $reply),
					'account_name' => $this->get_xml_tag('consumerName', $reply),
					'account_city' => $this->get_xml_tag('consumerCity', $reply),
				);

				// construct the message
				$message = $this->multiline($result['timestamp'].$result['transaction_id'].$result['transaction_status'].$result['account_number']);

				// validate the fingerprint
				$fingerprint = $this->get_xml_tag('fingerprint', $reply);
				if (strcasecmp($fingerprint, $this->security_get_fingerprint(true)) !== 0)
				{
					throw new \IdealProcessException('Invalid fingerprint detected in status response message');
				}

				// validate the signature of the message
				$signature = base64_decode($this->get_xml_tag('signatureValue', $reply));
				if($this->security_verify_signature($message, $signature) == false)
				{
					throw new \IdealProcessException('Invalid signature detected in status response message');
				}

				// make sure the status is uppercase
				$result['transaction_status'] = strtoupper($result['transaction_status']);
			}
		}

		// return the status
		return $result;
	 }

	// -------------------------------------------------------------------------
	// setters and getters
	// -------------------------------------------------------------------------

	/**
	 * set the amount to be paid
	 *
	 * @param	mixed	amount for this transaction (integer or float)
	 * @return	void
	 */
	public function set_amount($amount)
	{
		// check if a valid amount is passed?
		if (is_numeric($amount) and $amount > 0)
		{
			$this->config['amount'] = round($amount * 100);
		}
		else
		{
			throw new \IdealException('Invalid amount passed. The amount must be numeric and larger than zero');
		}
	}

	/**
	 * set the selected issuer
	 *
	 * @param	string	id of the selected issuer
	 * @return	void
	 */
	public function set_issuer($issuer)
	{
		$this->config['issuer'] = $issuer;
	}

	/**
	 * set an entrance code, or generate one if not given
	 *
	 * @param	string	entrance code to set
	 * @return	string	entrance code currently set
	 */
	public function set_entrance_code($entrance_code = null)
	{
		// generate a random code if none is given
		is_null($entrance_code) and $entrance_code = sha1(rand(1000000, 9999999));

		// check if a valid amount is passed?
		if (is_string($entrance_code) and strlen($entrance_code) > 0 and strlen($entrance_code) < 41)
		{
			$this->config['entrance_code'] = $entrance_code;
		}
		else
		{
			throw new \IdealException('Invalid entrance_code passed. The entrance_code must be a string and maximum 40 characters');
		}

		return $this->config['entrance_code'];
	}

	/**
	 * set the transaction description
	 *
	 * @param	string	text for the transaction. max 32 characters
	 * @return	void
	 */
	public function set_description($description)
	{
		// check if a valid description is passed?
		if (is_string($description) and strlen($description) > 0 and strlen($description) < 33)
		{
			$this->config['description'] = $description;
		}
		else
		{
			throw new \IdealException('Invalid description passed. The description must be a string and maximum 32 characters');
		}
	}

	/**
	 * set the transaction reference
	 *
	 * @param	string	text for the transaction. max 16 characters
	 * @return	void
	 */
	public function set_reference($reference)
	{
		// check if a valid amount is passed?
		if (is_string($reference) and strlen($reference) > 0 and strlen($reference) < 17)
		{
			$this->config['reference'] = $reference;
		}
		else
		{
			throw new \IdealException('Invalid reference passed. The transaction reference must be a string and maximum 16 characters');
		}
	}

	/**
	 * set the return url
	 *
	 * @param	string	url
	 * @return	void
	 */
	public function set_return_url($url)
	{
		// need to escape square brackets for some banks
		$this->config['return_url'] = str_replace(array('[', ']'), array('%5B', '%5D'), $url);
	}

	/**
	 * set the merchant id and sub id
	 *
	 * @param	mixed	merchant id
	 * @param	mixed	sub id
	 * @return	array
	 */
	public function set_merchant($merchantid, $subid = '0')
	{
		$this->config['merchant_id'] = $merchantid;
		$this->config['sub_id'] = $subid;
	}

	// -------------------------------------------------------------------------
	// internal methods
	// -------------------------------------------------------------------------

	/**
	 * validate the configuration passed
	 *
	 * @param	array	configuration array
	 * @return	string
	 */
	protected function validate($config)
	{
		// force the language to NL
		$config['language'] = 'NL';

		// force the currency to EUR
		$config['currency'] = 'EUR';

		// force the payment type to ideal
		$config['payment_type'] = 'ideal';

		// set some defaults
		isset($config['amount']) or $config['amount'] = 0;
		isset($config['reference']) or $config['reference'] = '';
		isset($config['description']) or $config['description'] = '';
		isset($config['issuer']) or $config['issuer'] = '';
		isset($config['entrance_code']) or $config['entrance_code'] = $this->set_entrance_code();

		// check the availability of the keys and certs, make sure the paths are correct
		foreach (array('private_key', 'private_cert', 'public_cert') as $key)
		{
			if ( ! isset($config['bank']['professional'][$key]))
			{
				throw new \IdealException('No "'.$key.'" defined in the "'.$config['bank']['name'].'" configuration file');
			}
			else
			{
				// make sure the path is fully qualified
				is_file($config['bank']['professional'][$key]) or $config['bank']['professional'][$key] = rtrim($config['cert_path'], DS).DS.$config['bank']['professional'][$key];
				$config['bank']['professional'][$key] = realpath($config['bank']['professional'][$key]);

				// make sure the path is fully qualified and the file exists
				if ( ! is_file($config['bank']['professional'][$key]))
				{
					throw new \IdealException('The "'.$key.'" defined in the "'.$config['bank']['name'].'" configuration file can not be found.');
				}
			}
		}


		// return the validated config
		return $config;
	}

	/**
	 * post data to the iDEAL provider using socket communication
	 *
	 * @param	string	the URL to post too
	 * @param	string	the data to send
	 * @param	integer	communication timeout
	 * @return	void
	 */
	protected function send_data($url, $data, $timeout = 30)
	{
		// split the url
		$urlparts = parse_url($url);

		// reconstruct the scheme+host, needed for the socket connection
		empty($urlparts['port']) and $urlparts['port'] = '80';
		empty($urlparts['scheme']) or $urlparts['scheme'] .= '://';
		$urlparts['scheme'] .= $urlparts['host'].':'.$urlparts['port'];

		// storage for the result
		$result = '';

		// connect to the server
		if ($handle = @fsockopen($urlparts['scheme'], $urlparts['port'], $errno, $errstr, $timeout))
		{
			// send the HTTP request
			fputs($handle, 'POST '.$urlparts['path'].' HTTP/1.0'.CRLF);
			fputs($handle, 'Host: '.$urlparts['host'].CRLF);
			fputs($handle, 'Accept: text/html'.CRLF);
			fputs($handle, 'Accept: charset=ISO-8859-1'.CRLF);
			fputs($handle, 'Content-Length:'.strlen($data).CRLF);
			fputs($handle, 'Content-Type: text/html; charset=ISO-8859-1'.CRLF.CRLF);
			fputs($handle, $data, strlen($data));

			// fetch the response from the server
			while ( ! feof($handle))
			{
				$result .= @fgets($handle, 128);
			}

			// and close the connection
			fclose($handle);
		}
		else
		{
			// connection failed
			throw new \IdealConnectionException($url);
		}

		// return the result
		return $result;
	}

	/**
	 * get a tag value from an XML string
	 *
	 * @param	string	tag
	 * @param	string	xml string
	 * @return	mixed	tag value, or false if not found
	 */
	protected function get_xml_tag($tag, $xml_string)
	{
		// locate the requested tag
		if ($begin = strpos($xml_string, '<'.$tag.'>'))
		{
			// adjust the begin position
			$begin += strlen($tag) + 2;

			// find the closing tag
			if ($end = strpos($xml_string, '</'.$tag.'>'))
			{
				return $this->xml_unescape(substr($xml_string, $begin, $end-$begin));
			}
		}

		// tag not found, or no closing tag found (malformed XML)
		return false;
	}

	/**
	 * escape a string to be included in the XML
	 *
	 * @param	string	string to escape
	 * @return	string	UTF-8 encoded escaped string
	 */
	protected function xml_escape($string)
	{
		return utf8_encode(htmlspecialchars($string, ENT_COMPAT));
	}

	/**
	 * un-escape a string that was included in the XML
	 *
	 * @param	string	UTF-8 encoded string to un-escape
	 * @return	string	un-escaped string
	 */
	protected function xml_unescape($string)
	{
		return htmlspecialchars_decode(utf8_decode($string), ENT_COMPAT);
	}

	/**
	 * deal with multiline strings
	 *
	 * @param	string
	 * @return	string	validated and converted string
	 */
	protected function multiline($string)
	{
		if (empty($this->config['bank']['multiline']))
		{
			return preg_replace('/(\f|\n|\r|\t|\v)/', '', $string);
		}
		else
		{
			return preg_replace('/\s/', '', $string);
		}
	}

	// -------------------------------------------------------------------------
	// security methods
	// -------------------------------------------------------------------------

	/**
	 * get the fingerprint from the certificate
	 *
	 * @param	bool	if true, use the banks public certificate, if false, the our private certificate
	 * @return	mixed	the fingerprint string, or false if it could not be retrieved
	 */
	 protected function security_get_fingerprint($public = false)
	 {
		// determine the certificate to read
		$cert = $public ? $this->config['bank']['professional']['public_cert'] : $this->config['bank']['professional']['private_cert'];

		// read the certificate and create an openssl resource
		try
		{
			$openssl = openssl_x509_read(\File::read($cert, true));
		}
		catch (\Exception $e)
		{
			throw new \IdealCertificateException('Can not read the configured '.($public ? 'public' : 'private').' certificate');
		}

		// validate it by converting the resource back to the certificate
		if ($openssl and openssl_x509_export($openssl, $openssl))
		{
			// get the key data from the file
			$openssl = preg_replace(array('/\-+BEGIN CERTIFICATE\-+/', '/\-+END CERTIFICATE\-+/'), '', $openssl);

			// and calculate the fingerprint
			$openssl = strtoupper(sha1(base64_decode($openssl)));
		}
		else
		{
			throw new \IdealCertificateException('Invalid '.($public ? 'public' : 'private').' certificate detected');
		}
//die($openssl);
		return $openssl;
	 }

	/**
	 * calculate the signature for a message string
	 *
	 * @param	string	the message for which the signature must be calculated
	 * @return	string	the calculated signature
	 */
	 protected function security_get_signature($message)
	 {
		// make sure the message format complies to the banks standards
		$message = $this->multiline($message);
		// read the private key file
		try
		{
			$private_key = (\File::read($this->config['bank']['professional']['private_key'], true));
		}
		catch (\Exception $e)
		{
			throw new \IdealCertificateException('Can not read the configured private key file');
		}
		// fetch it using the configured pass phrase
		if ($private_key = openssl_get_privatekey($private_key, $this->config['bank']['professional']['passphrase']))
		{
			$signature = '';
			if (openssl_sign($message, $signature, $private_key))
			{
				openssl_free_key($private_key);
				$signature = base64_encode($signature);
			}
			else
			{
				throw new \IdealCertificateException('Error signing the message using the private key');
			}
		}
		else
		{
			throw new \IdealCertificateException('Invalid PassPhrase for the configured private key');
		}

		// return the calculated signature
		return $signature;
	 }

	/**
	 * verify the signature on a received message
	 *
	 * @param	string	the message for which the signature must be calculated
	 * @return	string	the calculated signature
	 */
	 protected function security_verify_signature($message, $signature)
	 {
		// read the private key file
		try
		{
			$public_cert = (\File::read($this->config['bank']['professional']['public_cert'], true));
		}
		catch (\Exception $e)
		{
			throw new \IdealCertificateException('Can not read the configured public certificate file');
		}

		// fetch it using the configured pass phrase
		if ($public_key = openssl_get_publickey($public_cert))
		{
			$result = openssl_verify($message, $signature, $public_key) ? true : false;
			openssl_free_key($public_key);
		}
		else
		{
			// failed
			throw new \IdealCertificateException('Can not retrieve the key from the configured public certificate');
		}

		return $result;
	 }

}
