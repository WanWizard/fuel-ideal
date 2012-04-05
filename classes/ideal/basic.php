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
 * iDEAL classes transaction logic based on code in ideallite.cls.5.php by
 * Martijn Wieringa, PHP Solutions, info@php-solutions.nl
 *
 * The original code doesn't contain any license information, but the text on the
 * website (http://www.ideal-simulator.nl/ideal-scripts-en-plugins-algemeen.html)
 * indicates the code is free to be used by anyone.
 */

namespace Ideal;

/**
 * Ideal
 *
 * @package     Fuel
 * @subpackage  Ideal
 */
class Ideal_Basic
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
		// store the config passed
		$this->config = $this->validate($config);
	}

	/**
	 * Create the fieldset for the transaction form
	 *
	 * @param	mixed	Fieldset, or null to create one
	 * @return	void
	 */
	public function fieldset($fieldset = null)
	{
		// create a new fieldset if none was passed
		if (is_null($fieldset) or ! $fieldset instanceOf Fieldset)
		{
			$fieldset = \Fieldset::forge('idealform');
		}

		// calculate the amount
		$amount = round($this->config['amount'] * 100);

		// set the validity of the form to one hour
		$validuntil = date('Y-m-d\TG:i:s\Z', strtotime('+1 hour'));

		// calculate the hash for this transaction
		$hash = $this->config['bank']['basic']['hash_key'].$this->config['merchant_id'].$this->config['sub_id'].
			$amount.$this->config['reference'].$this->config['payment_type'].$validuntil.
			'1'.$this->config['description'].'1'.$amount;

		$hash = html_entity_decode($hash);

		// remove all whitespace characters: "\t", "\n", "\r" and " "
		$hash = str_replace(array("\t", "\n", "\r", " "), '', $hash);

		// generate hash
		$hash = sha1($hash);

		// add the form open action
		if ($this->config['testmode'] and ! empty($this->config['bank']['basic']['testurl']))
		{
			$fieldset->set_config(array('form_attributes' => array('action' => $this->config['bank']['basic']['testurl'])));
		}
		else
		{
			$fieldset->set_config(array('form_attributes' => array('action' => $this->config['bank']['basic']['url'])));
		}

		// add the hidden transaction fields to the fieldset
		$fieldset->add('merchantID', '', array('type' => 'hidden', 'value' => $this->config['merchant_id']));
		$fieldset->add('subID', '', array('type' => 'hidden', 'value' => $this->config['sub_id']));
		$fieldset->add('amount', '', array('type' => 'hidden', 'value' => $amount));
		$fieldset->add('purchaseID', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['reference'], ENT_COMPAT)));
		$fieldset->add('language', '', array('type' => 'hidden', 'value' => $this->config['language']));
		$fieldset->add('currency', '', array('type' => 'hidden', 'value' => $this->config['currency']));
		$fieldset->add('description', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['description'], ENT_COMPAT)));
		$fieldset->add('hash', '', array('type' => 'hidden', 'value' => $hash));
		$fieldset->add('paymentType', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['payment_type'], ENT_COMPAT)));
		$fieldset->add('validUntil', '', array('type' => 'hidden', 'value' => $validuntil));
		$fieldset->add('itemNumber1', '', array('type' => 'hidden', 'value' => '1'));
		$fieldset->add('itemDescription1', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['description'], ENT_COMPAT)));
		$fieldset->add('itemQuantity1', '', array('type' => 'hidden', 'value' => '1'));
		$fieldset->add('itemPrice1', '', array('type' => 'hidden', 'value' => $amount));

		// add the return URL's if configured
		empty($this->config['success_url']) and $this->config['success_url'] = \Uri::create().'?status=success';
		empty($this->config['cancel_url']) and $this->config['cancel_url'] = \Uri::create().'?status=cancel';
		empty($this->config['error_url']) and $this->config['error_url'] = \Uri::create().'?status=error';

		$fieldset->add('urlSuccess', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['success_url'], ENT_COMPAT)));
		$fieldset->add('urlCancel', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['cancel_url'], ENT_COMPAT)));
		$fieldset->add('urlError', '', array('type' => 'hidden', 'value' => htmlspecialchars($this->config['error_url'], ENT_COMPAT)));

		// and return the constructed fieldset
		return $fieldset;
	}

	// -------------------------------------------------------------------------
	// getters and setters
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
		// check if a valid reference is passed?
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
	 * get or set the merchant id and sub id
	 *
	 * @param	mixed	merchant id
	 * @param	mixed	sub id, optional. if not given, defaults to '0'
	 * @return	void
	 */
	public function set_merchant($merchantid, $subid = '0')
	{
		$this->config['merchant_id'] = $merchantid;
		$this->config['sub_id'] = $subid;
	}

	/**
	 * get or set the hash key
	 *
	 * @param	string	hashkey
	 * @return	void
	 */
	public function set_hashkey($hashkey)
	{
		$this->config['bank']['basic']['hashkey'] = $hashkey;
	}

	/**
	 * get or set the cancel url
	 *
	 * @param	string	url
	 * @return	void
	 */
	public function set_cancel_url($url)
	{
		return $this->config['cancel_url'];
	}

	/**
	 * set the success url
	 *
	 * @param	string	url
	 * @return	void
	 */
	public function set_success_url($url)
	{
		$this->config['success_url'] = $url;
	}

	/**
	 * get or set the error url
	 *
	 * @param	string	url
	 * @return	void
	 */
	public function set_error_url($url)
	{
		$this->config['error_url'] = $url;
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

		// return the validated config
		return $config;
	}
}
