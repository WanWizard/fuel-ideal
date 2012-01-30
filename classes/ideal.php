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
 * iDEAL classes transaction logic based on code by
 * Martijn Wieringa, PHP Solutions, info@php-solutions.nl
 *
 * The original code doesn't contain any license information, but the text on the
 * website (http://www.ideal-simulator.nl/ideal-scripts-en-plugins-algemeen.html)
 * indicates the code is free to use by anyone.
 */

namespace Ideal;

/**
 * generic Ideal exception used throughout this package
 */
class IdealException extends \FuelException {};

/**
 * make sure we have a linefeed constant
 */
defined('LF') or define('LF', chr(10));

/**
 * Ideal
 *
 * @package     Fuel
 * @subpackage  Ideal
 */
class Ideal
{
	/**
	 * @var	object	ideal driver object instance
	 */
	protected static $instance = null;

	/**
	 * create the iDEAL singleton instance
	 */
	public static function instance(Array $config = array())
	{
		if (is_null(static::$instance))
		{
			// load the config file
			\Config::load('ideal', true);

			// merge the configs
			$config = static::validate(\Arr::merge(\Config::get('ideal'), $config));

			// load the configured bank config
			\Config::load($config['bank'], true);

			// and merge that with the global iDEAL config
			$config['bank'] = array_merge(\Config::get($config['bank']), array('name' => $config['bank']));

			// determine the driver to load
			$driver = '\\Ideal\\Ideal_'.ucfirst($config['service']);

			// create the instance
			static::$instance = new $driver($config);
		}

		// return the singleton
		return static::$instance;
	}

	/**
	 * validate the configuration, add defaults if needed
	 */
	protected static function validate(Array $config = array())
	{
		// check the configured bank
		if ( ! isset($config['bank']))
		{
			throw new IdealException('No bank defined in the iDEAL configuration file');
		}
		else
		{
			$config['bank'] = strtolower($config['bank']);
		}
		if ( ! in_array($config['bank'], array('rabo', 'abnamro', 'ing', 'simulator')))
		{
			throw new IdealException('The "'.$config['bank'].'" bank defined in the iDEAL configuration file is not supported (yet)');
		}

		// check the configured service
		if ( ! isset($config['service']))
		{
			throw new IdealException('No service type defined in the iDEAL configuration file');
		}
		else
		{
			$config['service'] = strtolower($config['service']);
		}

		if ( ! in_array($config['service'], array('basic', 'professional')))
		{
			throw new IdealException('The "'.$config['service'].'" service defined in the iDEAL configuration file is not supported. Use either "basic" or "professional"');
		}

		// return the config
		return $config;
	}

}
