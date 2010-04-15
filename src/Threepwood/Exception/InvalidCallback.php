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
 * @version    $Id: InvalidCallback.php 72 2009-09-24 18:51:27Z phil $
 */

namespace Threepwood\Exception;

/**
 * An exception which can be thrown if a un-callable
 * callback was found.
 *
 * @category   Threepwood
 * @package    Core
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 */
class InvalidCallback extends \Exception
{
    public function __construct($callback, $code = 0)
    {
        if (is_array($callback))
        {
            if (is_object($callback[0])) {
                $callback[0] = get_class($callback[0]);
            }
            
            $callback = join('::', $callback);
        }
        
        parent::__construct(sprintf('"%s" is no valid callback', $callback), $code);
    }
}