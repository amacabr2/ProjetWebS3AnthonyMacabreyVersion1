<?php

use Silex\Provider\CsrfServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;

include('config.php');

//On initialise le timeZone
ini_set('date.timezone', 'Europe/Paris');

//On ajoute l'autoloader (compatible winwin)
$loader = require_once join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'vendor', 'autoload.php']);

//dans l'autoloader nous ajoutons notre répertoire applicatif
$loader->addPsr4('App\\',join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'src']));

//Nous instancions un objet Silex\Application
$app = new Silex\Application();

// connexion à la base de données
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => hostname,
        'host' => hostname,
        'dbname' => database,
        'user' => username,
        'password' => password,
        'charset'   => 'utf8mb4',
    ),
));

//utilisation de twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'src', 'View'])
));

// utilisation des sessions
$app->register(new Silex\Provider\SessionServiceProvider());

//utilisation de form, de validator, translation
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr'),
));

//faille CSRF
$app->register(new CsrfServiceProvider());

//en dev, nous voulons voir les erreurs
$app['debug'] = true;

// rajoute la méthode asset dans twig
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.named_packages' => array(
        'css' => array(
            'version' => 'css2',
            'base_path' => __DIR__.'/../web/'
        )
    ),
));

// par défaut les méthodes DELETE PUT ne sont pas prises en compte
Request::enableHttpMethodParameterOverride();

// validator      => php composer.phar  require symfony/validator
// $app->register(new Silex\Provider\ValidatorServiceProvider());

include('routing.php');
