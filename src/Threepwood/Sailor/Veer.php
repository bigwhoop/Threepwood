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
 * @copyright  Copyright (c) 2010 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License

 */

namespace Threepwood\Sailor;
use Threepwood;

/**
 * This sailor let's you do HTTP redirects.
 *
 * @category   Threepwood
 * @package    Core
 * @subpackage Sailor
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2010 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class Veer extends Threepwood\SailorAbstract
{
    /**
     * @var int
     */
    const PERMANENTLY = 301;
    
    /**
     * @var int
     */
    const TEMPORARY   = 307;
    
    
    /**
     * Constructor. 
     * 
     * @param string $uri
     * @param int $code
     */
    public function __construct($uri = '', $code = self::TEMPORARY)
    {
        if (headers_sent()) {
            echo '<meta http-equiv="refresh" content="0; url=' . htmlspecialchars($uri) . '" />';
        }
        else {
            header("location: $uri", true, $code);
        }
        
        exit();
    }
}