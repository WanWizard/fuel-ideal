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
		 * URL to access the Rabobank basic iDEAL service (production mode)
		 */
		'url'            => 'https://ideal.rabobank.nl/ideal/mpiPayInitRabo.do',

		/**
		 * URL to access the Rabobank basic iDEAL service (test mode)
		 */
		'testurl'        => 'https://idealtest.rabobank.nl/ideal/mpiPayInitRabo.do',

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
		 * URLs to access the Rabobank professional iDEAL service
		 */
		'issuer'            => array(
			/**
			 * URL to access the Rabobank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.rabobank.nl:443/ideal/iDeal',

			/**
			 * URL to access the Rabobank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.rabobank.nl:443/ideal/iDeal',
		),

		'transaction'        => array(
			/**
			 * URL to access the Rabobank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.rabobank.nl:443/ideal/iDeal',

			/**
			 * URL to access the Rabobank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.rabobank.nl:443/ideal/iDeal',
		),

		'status'            => array(
			/**
			 * URL to access the Rabobank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://ideal.rabobank.nl:443/ideal/iDeal',

			/**
			 * URL to access the Rabobank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://idealtest.rabobank.nl:443/ideal/iDeal',
		),

		/**
		 * Your private certificate passphrase
		 */
		'passphrase'     => '',

		/**
		 * Your private certificate key
		 */
		'private_key'    => 'private'.DS.'private.key',

		/**
		 * Your private certificate (the simulator uses their own private cert!)
		 */
		'private_cert'   => 'private'.DS.'private.cer',

		/**
		 * Public certificate issued by your iDEAL service provider
		 */
		'public_cert'    => 'rabobank'.DS.'rabobank.cer',
	),
);
