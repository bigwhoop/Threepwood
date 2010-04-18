WHAT'S THREEPWOOD?
==================

Threepwood is a micro web framework completely written in PHP 5.3 Lightweight,
fast, and distributed in a single .phar file. The main goal is to map URIs to
callbacks (functions, methods, closures).

It should be easily extendable.


TODO
====

 * Move from SVN to GIT
 * Refactor existing code
 * Unit testing
 * Plugins / Event management
 * Remove MVC capability from core (MvcPlugin)
 * Provide default autoloading
 * Provide default exception handling
 * SQLite plugin
 * CoucheDB plugin
 * ...


EXAMPLE
=======

    require LIB_PATH . '/Threepwood.phar';
    
    $ship = Threepwood\Flagship::getInstance();
    
    // Set the default options
    $ship->embark(array(
        'autoloader'                  => array(
            array('ThreepWeb\Autoloader', 'autoload'),
            realpath(LIB_PATH . '/ThreepWeb/Autoloader.php')
        ),
        'exceptionHandler'            => array('ThreepWeb\ExceptionHandler', 'handleException'),
        'error404Route'               => array('Error', 'error404'),
        'routeCallbackClassFormat'    => 'ThreepWeb\Controller\%sController',
        'routeCallbackMethodFormat'   => '%sAction',
        'viewBasePath'                => APP_PATH . '/views',
        'prependFile'                 => APP_PATH . '/layout/header.phtml',
        'appendFile'                  => APP_PATH . '/layout/footer.phtml'
    ));
    
    // Index page
    $ship->delegate('/', array('Index', 'index'));
    
    // Manual pages
    $ship->delegate('/manual',                array('Manual', 'list'));
    $ship->delegate('/manual/quick-guide',    array('Manual', 'quickGuide'));
    $ship->delegate('/manual/flagship',       array('Manual', 'flagship'));
    $ship->delegate('/manual/sailors',        array('Manual', 'sailor'));
    $ship->delegate('/manual/routes',         array('Manual', 'route'));
    $ship->delegate('/manual/installation',   array('Manual', 'installation'));
    $ship->delegate('/manual/error-handling', array('Manual', 'errorHandling'));
    $ship->delegate('/manual/user-data',      array('Manual', 'userData'));
    $ship->delegate('/manual/mvc',            array('Manual', 'mvc'));
    $ship->delegate('/faq',                   array('Manual', 'faq'));
    $ship->redirect('/docs', '/manual');
    
    // User pages
    $ship->delegate('/user/:username', array('User', 'show'));
    
    // Download pages
    $ship->delegate('/download', array('Download', 'list'));
    $ship->delegate('/download/get', array('Download', 'get'));
    
    // Licence
    $ship->delegate('/licence', function() {
        header('content-type: text/plain');
        readfile(PROJECT_PATH . '/var/new-bsd-licence.txt');
        exit();
    });
    $ship->alias('/license', '/licence');
    
    // Rock 'n roll!!!
    $ship->setSail();