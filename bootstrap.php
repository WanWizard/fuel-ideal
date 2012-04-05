<?php
/**
 * Ideal FuelPHP package - Dutch online bank payments
 *
 * @package    Fuel
 * @version    1.0
 * @author     Harro "WanWizard" Verton
 * @license    MIT License
 * @copyright  2012 - Exite Development Services
 * @link       http://exite.eu
 */

Autoloader::add_core_namespace('Ideal');

Autoloader::add_classes(array(
	'Ideal\\Ideal'                             => __DIR__.DS.'classes'.DS.'ideal.php',
	'Ideal\\Ideal_Basic'                       => __DIR__.DS.'classes'.DS.'ideal'.DS.'basic.php',
	'Ideal\\Ideal_Professional'                => __DIR__.DS.'classes'.DS.'ideal'.DS.'professional.php',

	// Exceptions
	'Ideal\\IdealException'                    => __DIR__.DS.'classes'.DS.'ideal.php',
	'Ideal\\IdealConnectionException'          => __DIR__.DS.'classes'.DS.'ideal'.DS.'professional.php',
	'Ideal\\IdealProcessException'             => __DIR__.DS.'classes'.DS.'ideal'.DS.'professional.php',
	'Ideal\\IdealCertificateException'         => __DIR__.DS.'classes'.DS.'ideal'.DS.'professional.php',
));
