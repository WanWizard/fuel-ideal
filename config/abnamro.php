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
	'multiline'          => false,

	/**
	 * Configuration for basic services
	 */
	'basic' => array(

		/**
		 * URL to access the ABN-AMRO Bank basic iDEAL service (production mode)
		 */
		'url'            => '',

		/**
		 * URL to access the ABN-AMRO Bank basic iDEAL service (test mode)
		 */
		'testurl'        => '',

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
		 * URLs to access the ABN-AMRO Bank professional iDEAL service
		 */
		'issuer'            => array(
			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://idealm.abnamro.nl:443/nl/issuerInformation/getIssuerInformation.xml',

			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Directory.aspx',
		),

		'transaction'        => array(
			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://idealm.abnamro.nl:443/nl/acquirerTrxRegistration/getAcquirerTrxRegistration.xml',

			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Transaction.aspx',
		),

		'status'            => array(
			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (production mode)
			 */
			'url'            => 'ssl://idealm.abnamro.nl:443/nl/acquirerStatusInquiry/getAcquirerStatusInquiry.xml',

			/**
			 * URL to access the ABN-AMRO Bank professional iDEAL service (test mode)
			 */
			'testurl'        => 'ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Status.aspx',
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
		'public_cert'    => 'ingbank'.DS.'ingbank.cer',
	),
);
