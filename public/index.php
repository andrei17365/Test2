<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<title>Document</title>
</head>
<body>

<?php
//if( !session_id() ) @session_start();

require '../vendor/autoload.php';
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;

$faker = Faker\Factory::create();

$pdo = new PDO("mysql:host=localhost;dbname=app3;charset=utf8;","root","root");
$queryFactory = new QueryFactory('mysql');

// $insert = $queryFactory->newInsert();

// $insert->into('posts');
// for ($i=0; $i < 30; $i++) {
// 	$insert->cols([
// 		'title' => $faker->words(3, true),
// 		'content' => $faker->text
// 	]);
// 	$insert->addRow();
// }

// $sth = $pdo->prepare($insert->getStatement());

// $sth->execute($insert->getBindValues());
$select = $queryFactory->newSelect();
$select
	->cols(['*'])
	->from('posts');
// prepare the statment
$sth = $pdo->prepare($select->getStatement());

// bind the values and execute
$sth->execute($select->getBindValues());
$totalItems = $sth->fetchAll(PDO::FETCH_ASSOC);

$select = $queryFactory->newSelect();
$select
	->cols(['*'])
	->from('posts')
	->setPaging(3)
	->page($_GET['page'] ?? 1);

// prepare the statment
$sth = $pdo->prepare($select->getStatement());

// bind the values and execute
$sth->execute($select->getBindValues());

// get the results back as an associative array
$items = $sth->fetchAll(PDO::FETCH_ASSOC);

$itemsPerPage = 3;
$currentPage = $_GET['page'] ?? 1;
$urlPattern = '?page=(:num)';

$paginator = new Paginator(count($totalItems), $itemsPerPage, $currentPage, $urlPattern);
foreach($items as $item){
	echo $item['id'] . PHP_EOL . $item['title'] . '</br>';
}

//echo $paginator;

// $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
//     $r->addRoute('GET', '/home', ['App\controllers\HomeController','index']);
//     // {id} must be a number (\d+)
//     $r->addRoute('GET', '/about', ['App\controllers\HomeController','about']);
//     $r->addRoute('GET', '/verification', ['App\controllers\HomeController','email_verification']);
//     $r->addRoute('GET', '/login', ['App\controllers\HomeController','login']);
// });

// // Fetch method and URI from somewhere
// $httpMethod = $_SERVER['REQUEST_METHOD'];
// $uri = $_SERVER['REQUEST_URI'];

// // Strip query string (?foo=bar) and decode URI
// if (false !== $pos = strpos($uri, '?')) {
//     $uri = substr($uri, 0, $pos);
// }
// $uri = rawurldecode($uri);

// $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
// switch ($routeInfo[0]) {
//     case FastRoute\Dispatcher::NOT_FOUND:
//         // ... 404 Not Found
//     	echo '404';
//         break;
//     case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
//         $allowedMethods = $routeInfo[1];
//         // ... 405 Method Not Allowed
//         echo 'Метод не разрешен';
//         break;
//     case FastRoute\Dispatcher::FOUND:
//         $handler = $routeInfo[1];
//         $vars = $routeInfo[2];
//         // ... call $handler with $vars

//         $controller = new $handler[0];

//         call_user_func([$controller, $handler[1]], $vars);
//         break;
// }
?>
<ul class="pagination">
    <?php if ($paginator->getPrevUrl()): ?>
        <li><a href="<?php echo $paginator->getPrevUrl(); ?>">&laquo; Предыдущая </a></li>
    <?php endif; ?>

    <?php foreach ($paginator->getPages() as $page): ?>
        <?php if ($page['url']): ?>
            <li <?php echo $page['isCurrent'] ? 'class="active"' : ''; ?>>
                <a href="<?php echo $page['url']; ?>"><?php echo $page['num']; ?></a>
            </li>
        <?php else: ?>
            <li class="disabled"><span><?php echo $page['num']; ?></span></li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($paginator->getNextUrl()): ?>
        <li><a href="<?php echo $paginator->getNextUrl(); ?>"> Следующая &raquo;</a></li>
    <?php endif; ?>
</ul>

<p>
    <?php echo $paginator->getTotalItems(); ?> найдено.

    Выводим
    <?php echo $paginator->getCurrentPageFirstItem(); ?>
    -
    <?php echo $paginator->getCurrentPageLastItem(); ?>.
</p>
</body>
</html>




