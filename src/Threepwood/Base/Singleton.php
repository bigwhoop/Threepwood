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
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: Singleton.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Base;
require_once 'SingletonInterface.php';

/**
 * Abstract Singleton class
 *
 * @category   Threepwood
 * @package    Core
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
abstract class Singleton implements SingletonInterface
{
    /**
     * @var array
     */
    protected static $_instances = array();
    
    
    /**
     * Return the only instance
     * 
     * @return stdClass
     */
    public static function getInstance()
    {
        $clazz = get_called_class();
        
        if (!array_key_exists($clazz, self::$_instances)) {
            self::$_instances[$clazz] = new $clazz();
        }
        
        return self::$_instances[$clazz];
    }
    
    
    /**
     * Constructor is protected
     */
    protected function __construct()
    {}
    
    
    /**
     * Cloning is forbidden
     */
    protected function __clone()
    {}
}