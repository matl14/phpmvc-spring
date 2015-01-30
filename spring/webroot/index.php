<?php 
/**
 * This is a Anax frontcontroller.
 *
 */

// Get environment & autoloader.
require __DIR__.'/config_with_app.php';

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_sqlite.php');
    $db->connect();
    return $db;
});

$di->set('CForm', function() use ($di) {
    $CForm = new \Mos\HTMLForm\CForm(); 
    return $CForm;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('CommentsController', function() use ($di) {
    $controller = new \Anax\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new \Anax\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});

// Make links pretty
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

// To use a different theme-file
$app->theme->configure(ANAX_APP_PATH . 'config/theme_spring.php');

// Include navbar
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_spring.php');

// Start session
$app->session;

// Routes

// Home route
$app->router->add('', function() use ($app) {
$app->theme->setTitle("Hem");
	
// featured
$app->dispatcher->forward([
    'controller'	=> 'comments',
    'action'     	=> 'latest',
	'params'		=> [],
    ]);
	
	$content = $app->fileContent->get('home.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
	
// main
$app->views->add('default/page', [
	'title'		=> 'Välkommen till Vårkänslor,',
	'content'	=> $content,
	]);
	
// sidebar
$app->dispatcher->forward([
    'controller'	=> 'tags',
    'action'     	=> 'popular-tags',
	'params'		=> [],
    ]);
	
// triptych
$app->dispatcher->forward([
    'controller'	=> 'users',
    'action'     	=> 'active-users',
	'params'		=> [],
    ]);
});

// Questions route
    $app->router->add('comments', function() use ($app) {
    $app->theme->setTitle("Frågor");

    $app->dispatcher->forward([
    'controller'	=> 'comments',
    'action'     	=> 'question-list',
	'params'		=> [],
    ]);

});

// Users route
$app->router->add('users', function() use ($app) {
    $app->theme->setTitle("Användare");
	
    $app->dispatcher->forward([
    'controller'	=> 'users',
    'action'     	=> 'list',
	'params'		=> [],
    ]);
});
	
// Tags route
    $app->router->add('tags', function() use ($app) {
    $app->theme->setTitle("Taggar");
	
    $app->dispatcher->forward([
    'controller'	=> 'tags',
    'action'     	=> 'list',
	'params'		=> [],
    ]);

});

// About route
    $app->router->add('about', function() use ($app) {
    $app->theme->setTitle("Om sidan");
	
    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
	
    $app->views->add('default/onlyContent', ['content'	=> $content]);
});

// Report route
$app->router->add('report', function() use ($app) {
$app->theme->setTitle("PHPMVC KMOM07-10: Redovisning");
	
    $content = $app->fileContent->get('report.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
	
    $app->views->add('default/onlyContent', ['content'	=> $content]);
});

// Source route
    $app->router->add('source', function() use ($app)    {
    $app->theme->setTitle("Källkod");
     
    $source = new \Mos\Source\CSource([
            'secure_dir' => '..', 
            'base_dir' => '..', 
            'add_ignore' => ['.htaccess'],
        ]);
     
    $app->views->add('me/source', [
            'content' => $source->View(),
        ]);
    
});


$app->router->add('rss', function() use ($app) {
    require '../vendor/matl14/rss/src/library/CRSS.php';
    $feed = new \matl14\library\CRSS([
            'http://www.irishtimes.com/cmlink/news-1.1319192',
            'http://expandedconsciousness.com/feed/rss/'
    ]);
    
    $app->theme->setTitle("We Love Spring");
    $app->theme->addStylesheet('css/rss.css');
    
    $app->views->add('default/page', [
        'title' => 'We <i class="fa fa-heart"></i> Spring',        
        'content' => 'Detta RSS-flöde ger oss lite gott och blandat inspiration inför våren, samt nyheter för att påminna oss om allt som är viktigt viktigt för oss människor.' . $feed->printFeed(),
    ], 'flash');
    
});

// Check for matching routes and dispatch to controller/handler of route
$app->router->handle();

// Render the page
$app->theme->render();