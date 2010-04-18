<?php
/**
 * Threepwood
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://threepwood.wolowizard.is-a-geek.org/licence
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Threepwood
 * @package    Core
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2010 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License

 */

require_once __DIR__ . '/Threepwood/Flagship.php';

// Note: The include path is automagically set by the phar archive!

// Set error handler
set_error_handler(array('Threepwood\Flagship', 'cbHandleError'));

// Register autoloader
spl_autoload_register(array('Threepwood\Flagship', 'cbAutoload'));