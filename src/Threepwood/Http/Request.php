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
 * @package    Http
 * @subpackage Request
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: Request.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Http;
use Threepwood;

/**
 * Very, very basic representation of a HTTP request
 *
 * @category   Threepwood
 * @package    Http
 * @subpackage Request
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class Request
{
    /**
     * @var string
     */
    protected $_uri = null;
    
    /**
     * @var string
     */
    protected $_method = null;
    
    
    /**
     * Return a unique key for a specific
     * URI and a specific HTTP method
     * 
     * @param string $uri
     * @param string $httpMethod
     * @return string
     */
    public static function buildRequestKey($uri, $httpMethod)
    {
        return "$uri [$httpMethod]";
    }
    
    
    /**
     * Returns the current HTTP request's URI
     * 
     * @return string
     * @throws Threepwood\Exception\Unresolvable
     */
    public static function getCurrentUri()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new Exception\Unresolvable('Couldn\'t fetch the request URI');
        }
        
        return $_SERVER['REQUEST_URI'];
    }
    
    
    /**
     * Returns the current HTTP request's HTTP method
     * 
     * @return string
     * @throws Threepwood\Exception\Unresolvable
     */
    public static function getCurrentMethod()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new Exception\Unresolvable('Couldn\'t fetch the request HTTP method');
        }
        
        return $_SERVER['REQUEST_METHOD'];
    }
    
    
    /**
     * Constructor.
     * 
     * @param string $uri
     * @param string $method
     */
    public function __construct($uri = null, $method = null)
    {
        if (null === $uri) {
            $uri = self::getCurrentUri();
        }
        
        if (null === $method) {
            $method = self::getCurrentMethod();
        }
        
        $this->_uri    = $uri;
        $this->_method = $method;
    }
    
    
    /**
     * Return the URI
     * 
     * @param bool $includeQueryString
     * @return string
     */
    public function getUri($includeQueryString = false)
    {
        if ($includeQueryString) {
            return $this->_uri;
        }
        
        list($uri) = explode('?', $this->_uri);
        return $uri;
    }
    
    
    /**
     * Return the HTTP method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }
    
    
    /**
     * Return the request's key
     * 
     * @return string
     */
    public function getKey()
    {
        return self::buildRequestKey($this->_uri, $this->_method);
    }
}