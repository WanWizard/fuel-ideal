<?php
/**
 * Part of the Ideal FuelPHP package.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Harro "WanWizard" Verton
 * @license    MIT License
 * @copyright  2012 Exite Development Services
 * @link       http://exite.eu
 */

/**
 * Copy this file to app/config before making changes!
 */

return array(

	/**
	 * Some banks support a test mode. Set this to true to use the test
	 * environment of the bank instead of performing real transactions
	 */
	'testmode' => true,

	/**
	 * Your merchant id, issued by your bank
	 */
	'merchant_id' => '123456789',

	/**
	 * Your merchant sub id, issued by your bank. If not given, use '0'
	 */
	'sub_id' => '0',

	/**
	 * The name of the bank you have an iDEAL contract with.
	 *
	 * Supported banks are:
	 * simulator, rabo, ing, amnamro, asn, fortis, friesland, sns and mollie
	 */
	'bank' => 'simulator',

	/**
	 * The type of service contract you have. Note that some banks use their
	 * own name for the service. In general, names like 'lite', 'hosted' and
	 * 'zakelijk' point to the basic service, while 'advanced', 'integrated'
	 * en 'zelfbouw' generally point the the professional version.
	 *
	 * Supported are:
	 * basic, professional
	 */
	'service' => 'professional',

	/**
	 * Return URL. After the bank transaction is completed, the bank will redirect
	 * to this URL so your application can deal with the transaction status
	 *
	 * If left blank, the bank will return to the current URL.
	 *
	 * Used with professional services only!
	 */
	 'return_url' => '',

	/**
	 * Cancel URL. If the user has cancelled the transaction, the bank will redirect
	 * to this URL so your application can deal with the transaction cancelled status
	 *
	 * If left blank, the bank will return to the current URL plus '?status=cancel'
	 *
	 * Used with basic services only!
	 */
	 'cancel_url' => '',

	/**
	 * Success URL. If the user has completed the transaction, the bank will redirect
	 * to this URL so your application can deal with the completed transaction status
	 *
	 * If left blank, the bank will return to the current URL plus '?status=success'
	 *
	 * Used with basic services only!
	 */
	 'success_url' => '',

	/**
	 * Error URL. If the bank has detected an error in the transaction, the bank will
	 * redirect to this URL so your application can deal with the error status
	 *
	 * If left blank, the bank will return to the current URL plus '?status=error'
	 *
	 * Used with basic services only!
	 */
	 'error_url' => '',

);
