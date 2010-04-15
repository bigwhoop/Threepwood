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
 * @version    $Id: RouteAbstract.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood;

/**
 * This is the mother of all routes.
 * 
 * @category   Threepwood
 * @package    Core
 * @subpackage Router
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
abstract class RouteAbstract implements RouteInterface
{
    /**
     * @var string
     */
    protected $_uri = null;
    
    /**
     * @var string|array
     */
    protected $_callback = null;
    
    /**
     * @var string|array
     */
    protected $_callbackRaw = null;
    
    /**
     * @var string
     */
    protected $_httpMethod = null;
    
    /**
     * @var bool|null
     */
    protected $_isCallable = null;
    
    
    /**
     * Constructor
     * 
     * @param string $uri
     * @param string|array|closure $callback
     * @param string $method
     */
    public function __construct($uri, $callback, $httpMethod = HTTP_ALL)
    {
        $this->_uri         = $uri;
        $this->_callbackRaw = $callback;
        $this->_callback    = Flagship::getInstance()->prepareCallback($callback);
        $this->_httpMethod  = $httpMethod;
        
        // We want to create a non-static method of the class
        // if the callback is an array (-> class/method callback)
        // TODO: Shouldn't this be done by the user? Or a switch
        // like "auto-instance"? Maybe the user wants a static
        // method callback?!
        if (is_array($this->_callback) && !is_object($this->_callback[0])) {
            $this->_callback = array(new $this->_callback[0], $this->_callback[1]);
        }
    }
    
    
    /**
     * Return whether this route's callback
     * is callable. The result is cached.
     * 
     * @return bool
     */
    public function isCallable()
    {
        if (null === $this->_isCallable)
        {
            $this->_isCallable = is_callable($this->_callback);
            
            // There's a problem with \Threepwood\ControllerAbstrac
            // and the magic __call() method. is_callable() will
            // always return true.
            if ($this->_isCallable
                && is_array($this->_callback)
                && $this->_callback[0] instanceof ControllerAbstract) {
                $this->_isCallable = method_exists($this->_callback[0], $this->_callback[1]);
            }
        }
                
        
        return $this->_isCallable;
    }
    
    
    /**
     * Dispatch -> Call the callback
     */
    public function dispatch()
    {
        // Check whether callback is callable.
        // We do this here because "start up validation" could
        // have been disabled.
        if (!$this->isCallable()) {
            throw new Exception\InvalidCallback($this->_callbackRaw);
        }
        
        call_user_func($this->_callback);
        
        // If callback is a method of a class which extends our
        // controller base we automagically render the view
        if (is_array($this->_callback)
            && $this->_callback[0] instanceof ControllerAbstract
            && !$this->_callback[0]->getView()->isRendered()
            && $this->_callback[0]->getView()->autoRender())
        {
            $viewScriptPath = sprintf(
                '%s%s%s',
                Flagship::getInstance()->getOption('viewBasePath'),
                DIRECTORY_SEPARATOR,
                sprintf(
                    Flagship::getInstance()->getOption('viewScriptFormat'),
                    $this->_callbackRaw[0],
                    strtolower(preg_replace('/([A-Z])+/', '-$1', $this->_callbackRaw[1]))
                )
            );
            
            echo $this->_callback[0]->getView()->render($viewScriptPath);
        }
    }
    
    
    /**
     * Return the URI
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }
    
    
    /**
     * Return the callback
     * 
     * @return string
     */
    public function getCallback()
    {
        return $this->_callback;
    }
    
    
    /**
     * Return the unformatted/raw callback
     * 
     * @return string
     */
    public function getCallbackUnformatted()
    {
        return $this->_callbackRaw;
    }
    
    
    
    /**
     * Return the HTTP method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_httpMethod;
    }
}