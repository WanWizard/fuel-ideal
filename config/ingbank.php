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

/**
 * Note that if relative paths are used in this file, they are relative
 * to the iDEAL package directory.
 */

return array(

	/**
	 * Does this provider support multi-line data?
	 */
	'multiline'          => true,

	/**
	 * Configuration for basic services
	 */
	'basic' => array(

		/**
		 * URL to access the ING bank basic iDEAL service (production mode)
		 */
		'url'            => 'https://ideal.secure-ing.com/ideal/mpiPayInitIng.do',

		/**
		 * URL to access the ING bank basic iDEAL service (test mode)
		 */
		'testurl'        => 'https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do',

		/**
		 * Encryption hash key issued by your iDEAL service provider
		 */
		'hash_key'       => '',
	),

	/**
	 * Configuration for professional services
	 */
	'professional' => array(

		/**
		 * URLs to access the ING bank professional iDEAL service
		 */
		'issuer'            => array(
			/**
			 * URL to access the ING bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.secure-ing.com:443/ideal/iDeal',

			/**
			 * URL to access the ING bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.secure-ing.com:443/ideal/iDeal',
		),

		'transaction'        => array(
			/**
			 * URL to access the ING bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.secure-ing.com:443/ideal/iDeal',

			/**
			 * URL to access the ING bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.secure-ing.com:443/ideal/iDeal',
		),

		'status'            => array(
			/**
			 * URL to access the ING bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.secure-ing.com:443/ideal/iDeal',

			/**
			 * URL to access the ING bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.secure-ing.com:443/ideal/iDeal',
		),

		/**
		 * Your private certificate passphrase
		 */
		'passphrase'     => '',

		/**
		 * Your private certificate key
		 */
		'private_key'    => 'certificates'.DS.'private'.DS.'private.key',

		/**
		 * Your private certificate (the simulator uses their own private cert!)
		 */
		'private_cert'   => 'certificates'.DS.'private'.DS.'private.cer',

		/**
		 * Public certificate issued by your iDEAL service provider
		 */
		'public_cert'    => 'certificates'.DS.'ingbank'.DS.'ingbank.cer',
	),
);
