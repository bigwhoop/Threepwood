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
 * @package    Build
 * @author     Philippe Gerber <philippe@bigwhoop.ch>
 * @copyright  Copyright (c) 2009 Philippe Gerber
 * @license    http://threepwood.wolowizard.is-a-geek.org/licence     New BSD License
 * @version    $Id: build.php 73 2009-09-24 18:54:26Z phil $
 */

$srcDir   = __DIR__ . '/../src';
$pharPath = __DIR__ . '/Threepwood.phar';

if (file_exists($pharPath)) {
    unlink($pharPath);
}

$phar = new Phar($pharPath, 0, basename($pharPath));
$phar->buildFromDirectory($srcDir, '/\.(php|phtml|xml)$/');
$phar->setStub($phar->createDefaultStub('init.php'));