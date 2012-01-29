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
	 * Configuration for basic services
	 */
	'basic' => array(

		/**
		 * URL to access the simulator basic iDEAL service (production mode)
		 */
		'url'            => 'https://ideal.rabobank.nl/ideal/mpiPayInitRabo.do',

		/**
		 * URL to access the simulator basic iDEAL service (test mode)
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
		 * URL to access the Rabobank professional iDEAL service
		 */
		'url'            => '',

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
		'public_cert'    => 'certificates'.DS.'rabobank'.DS.'rabobank.cer',
	),
);
