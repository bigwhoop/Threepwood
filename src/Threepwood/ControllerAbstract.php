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
 * @package    MVC
 * @subpackage Controller
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: ControllerAbstract.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood;

/**
 * This abstract controller provides automagical view 
 * rendering and access to filter/validation functionality
 * for evil user data.
 * 
 * @category   Threepwood
 * @package    MVC
 * @subpackage Controller
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
abstract class ControllerAbstract
{
    const PARAM_TYPE_IGNORE  = 'PARAM_TYPE_IGNORE';
    const PARAM_TYPE_STRING  = 'PARAM_TYPE_STRING';
    const PARAM_TYPE_INTEGER = 'PARAM_TYPE_INTEGER';
    const PARAM_TYPE_FLOAT   = 'PARAM_TYPE_FLOAT';
    const PARAM_TYPE_ARRAY   = 'PARAM_TYPE_ARRAY';
    
    const PARAM_COLLECTION_GET     = 'PARAM_COLLECTION_GET';
    const PARAM_COLLECTION_POST    = 'PARAM_COLLECTION_POST';
    const PARAM_COLLECTION_SESSION = 'PARAM_COLLECTION_SESSION';
    const PARAM_COLLECTION_COOKIE  = 'PARAM_COLLECTION_COOKIE';
    
    const PARAM_DEFAULT_VALUE = null;
    
    
    /**
     * @var \Threepwood\View
     */
    protected $_view = null;
    
    
    /**
     * Set view object
     */
    public function __construct()
    {
        $this->_view = new View();
    }
    
    
    public function filter($value, $type)
    {
        ;
    }
    
    
    /**
     * Return a particular GET/POST/SESSION/COOKIE param
     * 
     * @param string $name       Collection key
     * @param string $collection Collection to look for the key (GET, POST, etc.)
     * @param string $type       Force type casting
     * @param mixed  $default    Default value if the param was not set in the collection 
     * @return mixed
     */
    public function getParam($name, $collection = self::PARAM_COLLECTION_GET, $type = self::PARAM_TYPE_IGNORE, $default = self::PARAM_DEFAULT_VALUE)
    {
        // Get value from collection
        switch ($collection)
        {
            case self::PARAM_COLLECTION_GET:
                $value = array_key_exists($name, $_GET) ? $_GET[$name] : $default;
                break;
            
            case self::PARAM_COLLECTION_POST:
                $value = array_key_exists($name, $_POST) ? $_POST[$name] : $default;
                break;
            
            case self::PARAM_COLLECTION_SESSION:
                $value = array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $default;
                break;
            
            case self::PARAM_COLLECTION_COOKIE:
                $value = array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : $default;
                break;
            
            default:
                die('The collection "' . $collection . '" is invalid');
        }
        
        // Cast type
        switch ($type)
        {
            case self::PARAM_TYPE_STRING:
                $value = (string)$value;
                break;
            
            case self::PARAM_TYPE_INTEGER:
                $value = (int)$value;
                break;
            
            case self::PARAM_TYPE_FLOAT:
                $value = (float)$value;
                break;
            
            case self::PARAM_TYPE_ARRAY:
                $value = explode(',', (string)$value);
                break;
            
            case self::PARAM_TYPE_STRING:
                $value = (string)$value;
                break;
            
            
            case self::PARAM_TYPE_IGNORE:
            default:
                // Dumdidum ...
                break;
        }
        
        return $value;
    }
    
    
    /**
     * Return the view object
     * 
     * @return \Threepwood\View
     */
    public function getView()
    {
        return $this->_view;
    }
    
    
    /**
     * Return a particular GET param
     * 
     * @see \Threepwood\ControllerAbstrac::getParam
     */
    public function __call($method, $args)
    {
        $matches = array();
        
        // Eases access to getParam() ...
        if (preg_match('/get(GET|POST|COOKIE|SESSION)Param/', $method, $matches))
        {
            $collection = 'self::PARAM_COLLECTION_' . $matches[1];
            
            $args = array_slice(func_get_args(), 1);
            $args = array_shift($args);
            
            $args = array_merge(
                array($args[0], constant($collection)),
                $args
            );
            
            return call_user_func_array(array($this, 'getParam'), $args);
        }
        
        return false;
    }
}