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
 * @subpackage View
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: View.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood;

/**
 * The view renders view scripts and provides easy
 * access to escape functionality.
 * 
 * @category   Threepwood
 * @package    MVC
 * @subpackage View
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class View
{
    /**
     * @var bool
     */
    protected $_autoRender = true;
    
    /**
     * @var bool
     */
    protected $_rendered = false;
    
    /**
     * @var array
     */
    protected $_params = array();
    
    
    /**
     * Escape a value
     * 
     * @param mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        return htmlspecialchars($value);
    }
    
    
    /**
     * Render the view template
     * 
     * @return string
     */
    public function render($path)
    {
        if (!file_exists($path)) {
            throw new View\Exception('Couldn\'t find the view script "' . $path . '"');
        }
        
        ob_start();
        require $path;
        
        $this->_rendered = true;
        return ob_get_clean();
    }
    
    
    /**
     * Gets/sets auto rendering
     * 
     * @param bool $flag
     * @return bool|\Threepwood\View
     */
    public function autoRender($flag = null, $disableLayout = false)
    {
        if (null === $flag) {
            return $this->_autoRender;
        }
        
        $this->_autoRender = (bool)$flag;
        
        if ($disableLayout)
        {
            Flagship::getInstance()->embark(array(
                'prependFile' => null,
                'appendFile'  => null
            ));
        }
        
        return $this;
    }
    
    
    /**
     * Return whether the view has already been rendered
     * 
     * @return bool
     */
    public function isRendered()
    {
        return $this->_rendered;
    }
    
    
    /**
     * Magic setter
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->_params[(string)$key] = $value;
    }
    
    
    /**
     * Magic getter
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists((string)$key, $this->_params)) {
            return $this->_params[(string)$key];
        }
        
        return null;
    }
}