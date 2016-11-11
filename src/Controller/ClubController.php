<?php

namespace App\Controller;

use App\Model\ClubModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class ClubController implements ControllerProviderInterface {

    private $clubModel;

    public function show(Application $app) {
        $this->clubModel = new ClubModel($app);
        $clubs = $this->clubModel->getAllClub();
        return $app["twig"]->render('club/v_table_club.html.twig', array('clubs' => $clubs));
    }

    /**
     * Returns routes to connect to the given application.
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app){

        $controllers = $app['controllers_factory'];

        $controllers->get('/club/show', 'App\Controller\ClubController::show')->bind('club.show');

        return $controllers;

    }
}