<?php

namespace App\Controller;

use App\Service\TokenService;
use App\Model\LoginModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class LoginController implements ControllerProviderInterface{

    private $loginModel;

    /**
     * Accède à la page pour se connecter
     * @param Application $app
     * @return mixed
     */
    public function login(Application $app) {

        session_start();

        $tokenService = new TokenService();
        $tokenService->init_token();

        return $app['twig']->render('utilisateur/login.html.twig', array('token' => $_SESSION['token']));

    }

    /**
     * Vérifie si l'authentification de la personne est bonne
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginConfirm(Application $app, Request $request) {

        session_start();

        if (isset($_POST['login']) and isset($_POST['motdepasse'])) {

            $app['session']->clear();

            $donnees = array(
                'login' => $app->escape($request->get('login')),
                'motdepasse' => $app->escape($request->get('motdepasse')),
                'token' => htmlspecialchars($_POST['token'])
            );

            $tokenService = new TokenService();
            $this->loginModel = new LoginModel($app);

            if ($this->loginModel->verifLogin($donnees['login'], $donnees['motdepasse']) == false) $erreurs['identification'] = 'erreur dans le login ou le mot de passe';
            if ($tokenService->verif_token($donnees['token']) == false) $erreurs['token'] = 'Erreur dans le token';

            if(! empty($erreurs)) {
                return $app["twig"]->render('utilisateur/login.html.twig', array(
                    'token' => $_SESSION['token'],
                    'erreurs' => $erreurs
                ));
            } else {
                $droit = $this->loginModel->recupDroit($donnees['login'], $donnees['motdepasse']);
                $app['session']->set('droit', $droit['droit']);
                $app['session']->set('login', $donnees['login']);
                $app['session']->set('logged', 1);
                return $app->redirect($app["url_generator"]->generate("joueur.show"));
            }

        }

    }

    public function logout(Application $app) {
        $app['session']->clear();
        $app['session']->getFlashBag()->add('msg', 'Vous êtes déconneté');
        return $app->redirect($app["url_generator"]->generate("joueur.show"));
    }

    /**
     * Returns routes to connect to the given application.
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app) {

        $controllers = $app['controllers_factory'];

        $controllers->get('/user/login', 'App\Controller\LoginController::login')->bind('utilisateur.login');
        $controllers->post('/user/login', 'App\Controller\LoginController::loginConfirm')->bind('utilisateur.loginConfirm');

        $controllers->get('/user/logout', 'App\Controller\LoginController::logout')->bind('utilisateur.logout');

        return $controllers;

    }
}