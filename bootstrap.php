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


Autoloader::add_classes(array(
	'Ideal\\Ideal'                             => __DIR__.DS.'classes'.DS.'ideal.php',
	'Ideal\\Ideal_Basic'                       => __DIR__.DS.'classes'.DS.'ideal'.DS.'basic.php',
	'Ideal\\Ideal_Professional'                => __DIR__.DS.'classes'.DS.'ideal'.DS.'professional.php',
));
