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
		'url'            => 'https://www.ideal-simulator.nl/lite/',

		/**
		 * URL to access the simulator basic iDEAL service (test mode)
		 */
		'testurl'        => 'https://www.ideal-simulator.nl/lite/',

		/**
		 * Encryption hash key issued by your iDEAL service provider
		 */
		'hash_key'       => 'Password',
	),

	/**
	 * Configuration for professional services
	 */
	'professional' => array(

		/**
		 * URL to access the simulator professional iDEAL service
		 */
		'url'            => 'https://www.ideal-simulator.nl/professional/',

		/**
		 * Your private certificate passphrase
		 */
		'passphrase'     => 'Password',

		/**
		 * Your private certificate key (the simulator uses their own private key!)
		 */
		'private_key'    => 'certificates'.DS.'private'.DS.'simulator.key',

		/**
		 * Your private certificate (the simulator uses their own private cert!)
		 */
		'private_cert'   => 'certificates'.DS.'private'.DS.'simulator.cer',

		/**
		 * Public certificate issued by your iDEAL service provider
		 */
		'public_cert'    => 'certificates'.DS.'simulator'.DS.'simulator.cer',
	),
);
