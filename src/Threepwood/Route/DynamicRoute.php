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
 * @subpackage Router
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: DynamicRoute.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Route;
use Threepwood;

/**
 * Representation of a "dynamic" route. This means that the
 * route contains placeholders for GET variables which will
 * be out-regex'd during routing.
 *
 * @category   Threepwood
 * @package    Core
 * @subpackage Router
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class DynamicRoute extends Threepwood\RouteAbstract
{
    /**
     * Pattern to match a param in the URI
     * 
     * @var string
     */
    protected $_paramPattern = '[\d\w.,\-_;%]+';
    
    
    /**
     * Return whether the route is matching
     * the current request URI
     * 
     * @param string $uri
     * @param string $method
     * @return bool
     */
    public function isMatching($uri, $httpMethod)
    {
        // Let's check the HTTP method first
        if (Threepwood\HTTP_ALL != $httpMethod && $this->_httpMethod != $httpMethod) {
            return false;
        }
        
        return preg_match($this->getPatternForUri(), $uri);
    }
    
    
    /**
     * Get the regex pattern to match the param values
     * 
     * @return string
     */
    public function getPatternForUri()
    {
        return '/' . preg_replace('/:[\d\w]+/i', '(' . $this->_paramPattern . ')', str_replace('/', '\\/', $this->_uri)) . '/i';
    }
    
    
    /**
     * Return an unique hash for the URI and method
     * 
     * @return string
     */
    public function getHash()
    {
        return "{$this->getPatternForUri()} [$this->_httpMethod]";
    }
    
    
    /**
     * Call the callback *sigh*
     * 
     * @return void
     */
    public function dispatch()
    {
        // Match all param keys
        $paramKeys = array();
        preg_match_all('/:' . $this->_paramPattern . '/i', $this->_uri, $paramKeys);
        
        // Remove leading :'s from keys
        $paramKeys = array_map(
            function($key) {
                $key = substr($key, 1);
                return $key;
            },
            $paramKeys[0]
        );
        
        // Match all param values
        $paramValues = array();
        preg_match_all($this->getPatternForUri(), $_SERVER['REQUEST_URI'], $paramValues, \PREG_SET_ORDER);
        
        // Remove leading match
        $paramValues = $paramValues[0];
        array_shift($paramValues);
        
        // Set $_GET values. User has to filter all by himself!
        foreach ($paramValues as $idx => $value) {
           $_GET[$paramKeys[$idx]] = urldecode($value);
        }
        
        parent::dispatch();
    }
}