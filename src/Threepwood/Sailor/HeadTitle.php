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
 * @version    $Id: HeadTitle.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Sailor;
use Threepwood;

/**
 * A container to easely app-/prepend data.
 *
 * @category   Threepwood
 * @package    Core
 * @subpackage Sailor
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class HeadTitle extends Threepwood\Base\Singleton
{
    /**
     * The title parts
     * 
     * @var array
     */
    protected $_parts = array();
    
    /**
     * The token to "glue" the parts together
     * 
     * @var string
     */
    protected $_glue = ' | ';
    
    
    /**
     * Return the title
     * 
     * @return string
     */
    public function __toString()
    {
        return join($this->_glue, $this->_parts);
    }
    
    
    /**
     * Set the glue
     * 
     * @param string $value
     * @return Threepwood\Sailor\HeadTitle
     */
    public function setGlue($value)
    {
        $this->_glue = $value;
        
        return $this;
    }
    
    
    /**
     * Prepend a part
     * 
     * @param string $value
     * @return Threepwood\Sailor\HeadTitle
     */
    public function prepend($value)
    {
        $this->_parts = array_reverse($this->_parts);
        $this->append($value);
        $this->_parts = array_reverse($this->_parts);
        
        return $this;
    }
    
    
    /**
     * Append a part
     * 
     * @param string $value
     * @return Threepwood\Sailor\HeadTitle
     */
    public function append($value)
    {
        $this->_parts[] = $value;
        
        return $this;
    }
    
    
    /**
     * Reset all parts to just one part
     * 
     * @param string $value
     * @return Threepwood\Sailor\HeadTitle
     */
    public function set($value)
    {
        $this->_parts = array($value);
        
        return $this;
    }
    
    
    /**
     * Clear the current parts
     * 
     * @return Threepwood\Sailor\HeadTitle
     */
    public function clear()
    {
        $this->_parts = array();
        
        return $this;
    }
}