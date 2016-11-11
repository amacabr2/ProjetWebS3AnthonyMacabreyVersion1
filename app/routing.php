<?php

// Montage des contrôleurs sur le routeur
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app->mount("/", new App\Controller\JoueurController($app));

$app->mount("/club", new App\Controller\ClubController($app));

$app->mount("/user", new App\Controller\LoginController($app));

//On lance l'application
$app->run();