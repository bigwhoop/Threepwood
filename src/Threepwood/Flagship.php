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
 * @version    $Id: Flagship.php 74 2009-09-25 11:26:42Z phil $
 */

namespace Threepwood;
use Threepwood\Base;
use Threepwood\Http;

require_once __DIR__ . '/Base/Singleton.php';

/**
 * @var string
 */
const HTTP_ALL    = 'ALL';

/**
 * @var string
 */
const HTTP_POST   = 'POST';

/**
 * @var string
 */
const HTTP_GET    = 'GET';

/**
 * @var string
 */
const HTTP_PUT    = 'PUT';

/**
 * @var string
 */
const HTTP_DELETE = 'DELETE';


/**
 * The Flagship is the heart of Threepwood. It is router, dispatcher,
 * config, etc. all-in-one.
 *
 * @category   Threepwood
 * @package    Core
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class Flagship extends Base\Singleton
{
    /**
     * @var Threepwood\Flagship
     */
    protected static $_instance = null;
    
    /**
     * @see http://threepwood.wolowizard.is-a-geek.org/manual/flagship#options
     * @var array
     */
    protected $_options = array(
        'autoloader'                       => null,
        'exceptionHandler'                 => null,
        'routeCallbackClassFormat'         => '%s',
        'routeCallbackMethodFormat'        => '%s',
        'routeCallbackFunctionFormat'      => '%s',
        'viewScriptFormat'                 => '%s/%s.phtml',
        'viewBasePath'                     => null,
        'prependFile'                      => null,
        'appendFile'                       => null,
        'error404Route'                    => null,
        'validateRoutesOnStart'            => true,
        'baseUrl'                          => '/'
    );
    
    /**
     * @var array
     */
    protected $_staticRoutes = array();
    
    /**
     * @var array
     */
    protected $_dynamicRoutes = array();
    
    
    /**
     * Run this thingy ...
     * 
     * @param Threepwood\Http\Request $httpRequest
     * @return Threepwood\Flagship
     */
    public function setSail(Http\Request $httpRequest = null)
    {
        if (null === $httpRequest) {
            $httpRequest = new Http\Request();
        }
        
        // Validate the routes' callbacks
        if ((bool)$this->_options['validateRoutesOnStart']) {
            $this->validateRouteCallbacks();
        }
        
        // Try to get a route for the HTTP request
        $activeRoute = $this->findRoute($httpRequest);
        
        // If no route is found we look for the 404 route
        if (!$activeRoute)
        {
            if (!$this->_options['error404Route'] instanceof RouteAbstract) {
                throw new Route\Exception('No route found for URI "' . $httpRequest->getUri() . '"');
            }
            
            $activeRoute = $this->_options['error404Route'];
        }
        
        // Dispatch the route
        ob_start();
        $activeRoute->dispatch();
        $content = ob_get_clean();
        
        // Require a header file to prepend
        ob_start();
        if (null !== $this->_options['prependFile']) {
            require $this->_options['prependFile'];
        }
        $header = ob_get_clean();
        
        // Require a footer file to append
        ob_start();
        if (null !== $this->_options['appendFile']) {
            require $this->_options['appendFile'];
        }
        $footer = ob_get_clean();
        
        // TODO: Add this to the response object
        echo $header . $content . $footer;
        
        return $this;
    }
    
    
    /**
     * Validate all routes' callbacks
     * 
     * @return true
     * @throws \Threepwood\Exception\InvalidCallback
     */
    public function validateRouteCallbacks()
    {
        $routes = array_merge($this->_staticRoutes, $this->_dynamicRoutes);
        
        foreach ($routes as $route)
        {
            if (!$route->isCallable()) {
                throw new Exception\InvalidCallback($route->getCallbackUnformatted());
            }
        }
        
        return true;
    }
    
    
    /**
     * Return a defined route for a specifc URI
     * and HTTP method
     * 
     * @param string|Threepwood\Http\Request $requestUri
     * @param string $requestMethod
     * @return null|Threepwood\Route\StaticRoute|Threepwood\Route\DynamicRoute
     */
    public function findRoute($requestUri, $requestMethod = HTTP_ALL)
    {
        $route = $this->findStaticRoute($requestUri, $requestMethod);
        if ($route) {
            return $route;
        }
        
        $route = $this->findDynamicRoute($requestUri, $requestMethod);
        if ($route) {
            return $route;
        }
        
        return null;
    }
    
    
    /**
     * Find a particular static route.
     * 
     * @param string|Threepwood\Http\Request $requestUri
     * @param string $requestMethod
     * @return null|Threepwood\Route\StaticRoute|Threepwood\Route\DynamicRoute
     */
    public function findStaticRoute($requestUri, $requestMethod = HTTP_ALL)
    {
        if ($requestUri instanceof Http\Request) {
            $requestMethod = $requestUri->getMethod();
            $requestUri    = $requestUri->getUri();
        }
        
        foreach (array($requestMethod, HTTP_ALL) as $method)
        {
            $key = Http\Request::buildRequestKey($requestUri, $method);
            if (array_key_exists($key, $this->_staticRoutes)) {
                return $this->_staticRoutes[$key];
            }
        }
        
        return null;
    }

    
    /**
     * Find a particular dynamic route.
     * 
     * @todo: Two loops is a bit shitty ... we could sort the
     *        the dynamic routes somehow before (pair up specific
     *        before non-set HTTP method, but not change the routes
     *        order)
     * 
     * @param string|Threepwood\Http\Request $requestUri
     * @param string $requestMethod
     * @return null|Threepwood\Route\StaticRoute|Threepwood\Route\DynamicRoute
     */
    public function findDynamicRoute($requestUri, $requestMethod = HTTP_ALL)
    {
        if ($requestUri instanceof Http\Request) {
            $requestMethod = $requestUri->getMethod();
            $requestUri    = $requestUri->getUri();
        }
        
        // 
        
        // Check dynamic routes for given HTTP method
        foreach ($this->_dynamicRoutes as $route)
        {
            if (!$route->isMatching($requestUri, $requestMethod)) {
                continue;
            }
            
            return $route;
        }
        
        // Check dynamic routes for non-set HTTP method
        foreach ($this->_dynamicRoutes as $route)
        {
            if (!$route->isMatching($requestUri, HTTP_ALL)) {
                continue;
            }
            
            return $route;
        }
        
        return null;
    }
    
    
    /**
     * Connect a URI to a callback
     *
     * @param string $uri
     * @param string|array|closure $callback
     * @param string $method Default method is POST
     * @return Threepwood\Flagship
     */
    public function delegate($uri, $callback, $method = HTTP_ALL)
    {
        $uri = str_replace('//', '/', $this->getOption('baseUrl') . $uri);
        
        if (false === strpos($uri, ':'))
        {
            $route = new Route\StaticRoute($uri, $callback, $method);
            $this->_staticRoutes[$route->getHash()] = $route;
        }
        else
        {
            $route = new Route\DynamicRoute($uri, $callback, $method);
            $this->_dynamicRoutes[$route->getHash()] = $route;
        }
        
        return $this;
    }
    
    
    /**
     * Redirect a URI to another
     * 
     * This will add a static route using a closure
     * to call the redirect helper
     * 
     * @param string $fromUri
     * @param string $toUri
     * @return Threepwood\Flagship
     */
    public function redirect($fromUri, $toUri)
    {
        $toUri = str_replace('//', '/', $this->getOption('baseUrl') . $toUri);
        
        return $this->delegate(
            $fromUri,
            function() use ($toUri) {
                \Threepwood\Flagship::veer($toUri);
            }
        );
        
        return $this;
    }
    
    
    /**
     * Add an alias from one route to another
     * 
     * Because objects are passed by reference since
     * PHP 5, we can just add a new route key pointing
     * to the same route object. :-)
     * 
     * @param string $fromUri
     * @param string $toUri
     * @param string $fromHttpMethod
     * @param string $toHttpMethod
     * @return Threepwood\Flagship
     */
    public function alias($fromUri, $toUri, $fromHttpMethod = HTTP_ALL, $toHttpMethod = HTTP_ALL)
    {
        $fromUri = str_replace('//', '/', $this->getOption('baseUrl') . $fromUri);
        $toUri   = str_replace('//', '/', $this->getOption('baseUrl') . $toUri);
        
        $toRqst  = new Http\Request($toUri, $toHttpMethod);
        $fromKey = Http\Request::buildRequestKey($fromUri, $fromHttpMethod);
        
        $route = $this->findStaticRoute($toRqst);
        if ($route) {
            $this->_staticRoutes[$fromKey] = $route;
            return $this;
        }
        
        $route = $this->findDynamicRoute($toRqst);
        if ($route) {
            $this->_dynamicRoutes[$fromKey] = $route;
            return $this;
        }
        
        throw new Route\Exception('Can\'t alias to non-existing route "' . $toRqst->getKey() . '"');
    }
    
    
    /**
     * Set options
     * 
     * @param array $options
     * @return Threepwood\Flagship
     */
    public function embark(array $options)
    {
        foreach (array_keys($this->_options) as $key)
        {
            if (!array_key_exists($key, $options)) {
                continue;
            }
            
            $value = $options[$key];
            
            switch ($key)
            {
                // Autoloader
                // Can be an array with one or two elements
                // where the latter is a file to include
                case 'autoloader':
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    
                    if (2 == count($value)) {
                        require_once $value[1];
                    }
                    
                    if (!is_callable($value[0])) {
                        throw new Exception\InvalidCallback($value[0]);
                    }
                    
                    spl_autoload_register($value[0]);
                    break;
                
                // Error 404 route
                // Must be a valid callback
                case 'error404Route':
                    $route = new Route\StaticRoute('~error404Route~', $value);
                    if (!$route->isCallable()) {
                        throw new Exception\InvalidCallback($value);
                    }
                    
                    $this->_options['error404Route'] = $route;
                    break;
                
                // Exception handler
                case 'exceptionHandler':
                    if (!is_callable($value)) {
                        throw new Exception\InvalidCallback($value);
                    }
                    
                    set_exception_handler($value);
                    break;
                    
                default:
                    $this->_options[$key] = $value;
                    break;
            }
        }
        
        return $this;
    }
    
    
    /**
     * Return a particular option
     * 
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if (array_key_exists((string)$key, $this->_options)) {
            return $this->_options[(string)$key];
        }
        
        return null;
    }
    
    
    /**
     * Prepare a callback by applying defined
     * custom name formats
     * 
     * @param string|array $callback
     * @return string|array
     */
    public function prepareCallback($callback)
    {
        if (is_array($callback))
        {
            $callback = array(
                sprintf($this->_options['routeCallbackClassFormat'],  $callback[0]),
                sprintf($this->_options['routeCallbackMethodFormat'], $callback[1])
            );
        }
        elseif (is_string($callback)) {
            $callback = sprintf($this->_options['routeCallbackFunctionFormat'], $callback);
        }
        
        return $callback;
    }
    
    
    /**
     * Tries to map non-existing method
     * calls to helper (Sailor) classes.
     * 
     * @param string $method
     * @param array $params
     * @return Threepwood\Sailor\Abstract|stdClass
     */
    public static function __callStatic($method, array $params = array())
    {
        $method    = ucfirst($method);
        $className = __NAMESPACE__ . '\\Sailor\\' . $method;
        
        if (!class_exists($className, true)) {
            throw new Sailor\Exception('Helper class "' . $method . '" does not exist');
        }
        
        // The helper class must have a constructor
        $reflection = new \ReflectionClass($className);
        if (!$reflection->hasMethod('__construct')) {
            throw new Sailor\Exception('Helper class "' . $method . '" must have a constructor');
        }
        
        // If the helper implements the singleton class, we call the static getInstance() method
        if ($reflection->implementsInterface('Threepwood\Base\SingletonInterface')
         || $reflection->isSubclassOf('Threepwood\Base\Singleton')) {
            return call_user_func_array(array($className, 'getInstance'), $params);
        }
        
        // Otherwise we just return a new instance
        return $reflection->newInstanceArgs($params);
    }
    
    
    /**
     * Our error handler converts all errors
     * to ErrorException's
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    public static function cbHandleError($errno, $errstr, $errfile, $errline)
    {
        // @-operator was used?
        if (0 === error_reporting()) {
            return;
        }
        
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    
    
    /**
     * Guybrush loads all these classes all by himself!!!
     *
     * @param string $className
     * @return bool
     */
    public static function cbAutoload($className)
    {
        // Replace namespace token \ with directory separator
        $classFile = str_replace('\\', '/', $className) . '.php';
        
        $classPath = \Phar::running() . '/' . $classFile;
        
        if (file_exists($classPath)) {
            include_once $classPath;
            return true;
        }
        
        return false;
    }
}