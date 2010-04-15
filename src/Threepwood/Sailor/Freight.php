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
 * @subpackage Sailor
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: Freight.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Sailor;
use Threepwood;
use Threepwood\Base;

/**
 * A simple registry class.
 *
 * @category   Threepwood
 * @package    Core
 * @subpackage Sailor
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class Freight extends Base\Singleton
{
    /**
     * @var array
     */
    protected $_registry = array();
    
    
    /**
     * Set a key/value pair
     * 
     * @param string $key
     * @param mixed $value
     * @return \Threepwood\Sailor\Freight
     */
    public function set($key, $value)
    {
        $this->_registry[(string)$key] = $value;
        
        return $this;
    }
    
    
    /**
     * Append a value to a key
     * 
     * @param string $key
     * @param mixed $value
     * @return \Threepwood\Sailor\Freight
     */
    public function append($key, $value)
    {
        $this->_assertArrayForKey((string)$key);
        
        $this->_registry[(string)$key][] = $value;
        
        return $this;
    }
    
    
    /**
     * Prepend a value to a key
     * 
     * @param string $key
     * @param mixed $value
     * @return \Threepwood\Sailor\Freight
     */
    public function prepend($key, $value)
    {
        $this->_assertArrayForKey((string)$key);
        
        // Faster than array_unshift() ...
        $this->_registry[(string)$key] = array_reverse($this->_registry[(string)$key]);
        $this->_registry[(string)$key][] = $value;
        $this->_registry[(string)$key] = array_reverse($this->_registry[(string)$key]);
        
        return $this;
    }
    
    
    /**
     * Get a value by a key
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists((string)$key, $this->_registry)) {
            return $this->_registry[(string)$key];
        }
        
        return null;
    }
    
    
    /**
     * Magic setter
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
    
    
    /**
     * Magic getter
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $this->get($key);
    }
    
    
    /**
     * Make sure that a particular key
     * has an array value
     * 
     * @param string $key
     * @return \Threepwood\Sailor\Freight
     */
    protected function _assertArrayForKey($key)
    {
        if (array_key_exists($key, $this->_registry))
        {
            if (!is_array($this->_registry[$key])) {
                $this->_registry[$key] = array($this->_registry[$key]);
            }
        }
        else {
            $this->_registry[$key] = array();
        }
        
        return $this;
    }
}