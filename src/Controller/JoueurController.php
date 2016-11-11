<?php

namespace App\Controller;

use App\Helper\HelperDate;
use App\Model\ClubModel;
use App\Model\JoueurModel;
use App\Service\TokenService;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\Validator\Constraints as Assert;

class JoueurController implements ControllerProviderInterface {

    private $joueurModel;

    private $clubModel;

    /**
     * Retourne la page avec le tableau d'information sur tous les joueurs
     * @param Application $app
     * @return mixed
     */
    public function show(Application $app) {
        $this->joueurModel = new JoueurModel($app);
        $this->clubModel = new ClubModel($app);
        $joueurs = $this->joueurModel->getAllJoueur();
        return $app["twig"]->render('foot/v_table_joueur.html.twig', array(
            'joueurs' => $joueurs,
        ));
    }

    /**
     * Permet d'accèder à la page pour pouvoir ajouter un joueur dans une équipe
     * @param Application $app
     * @return mixed
     * @internal param \App\Controller\FormBuilderInterface|FormBuilderInterface $builder
     */
    public function addJoueur(Application $app) {

        session_start();

        $this->clubModel = new ClubModel($app);
        $clubs = $this->clubModel->getAllClub();

        $tokenService = new TokenService();
        $tokenService->init_token();

        return $app['twig']->render('foot/v_add_joueur.html.twig', array(
            'clubs' => $clubs,
            'token' => $_SESSION['token']
        ));
    }

    /**
     * Ajoute un joueur dans une équipe après vérification des données
     * @param Application $app
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $form
     * @internal param Request $request
     */
    public function confirmAddJoueur(Application $app) {

        session_start();

        if (isset($_POST['nomJoueur']) and isset($_POST['prenomJoueur']) and isset($_POST['dateNaissance']) and isset($_POST['idClub'])) {

            $donnees = [
                'token' => htmlspecialchars($_POST['token']),
                'nomJoueur' => htmlspecialchars($_POST['nomJoueur']),
                'prenomJoueur' => htmlspecialchars($_POST['prenomJoueur']),
                'dateNaissance' => htmlspecialchars($_POST['dateNaissance']),
                'idClub' => htmlspecialchars($_POST['idClub'])
            ];

            $tokenService = new TokenService();

            if (! preg_match("/^[A-Za-z ]{2,}/", $donnees['nomJoueur'])) $erreurs['nomJoueur'] = 'nom composé de 2 lettres minimum';
            if (! preg_match("/^[A-Za-z ]{2,}/", $donnees['prenomJoueur'])) $erreurs['prenomJoueur'] = 'prenom composé de 2 lettres minimum';
            if (! HelperDate::verifDate($donnees['dateNaissance'])) $erreurs['dateNaissance'] = 'la date doit être remplit sous le format jj/mm/aaaa';
            if (! (is_numeric($donnees['idClub']) and $donnees['idClub'] > 0)) $erreurs['idClub'] = 'veuillez saisir une valeur';
            if ( $tokenService->verif_token($donnees['token']) == false) $erreurs['token'] = 'Erreur dans le token';

            if(! empty($erreurs)) {

                $tokenService = new TokenService();
                $tokenService->init_token();

                $this->clubModel = new ClubModel($app);
                $clubs = $this->clubModel->getAllClub();
                $club = $this->clubModel->getClubById($donnees['idClub']);

                return $app["twig"]->render('foot/v_add_joueur.html.twig', array(
                    'donnees' => $donnees,
                    'erreurs' => $erreurs,
                    'clubs' => $clubs,
                    'club' => $club,
                    'token' => $_SESSION['token']
                ));

            } else {
                $this->joueurModel = new JoueurModel($app);
                $this->joueurModel->addJoueur($donnees);
                return $app->redirect($app["url_generator"]->generate("joueur.show"));
            }

        } else return "error ????? PB data form";

    }

    /**
     * Affiche une page de confirmation sur le joueur visé avant suppression ou annulation
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function deleteJoueur(Application $app, $id) {

        session_start();

        $this->joueurModel = new JoueurModel($app);
        $donnees = $this->joueurModel->getJoueurById($id);

        $tokenService = new TokenService();
        $tokenService->init_token();

        return $app["twig"]->render('foot/v_delete_joueur.html.twig', array(
            'data' => $donnees,
            'token' => $_SESSION['token']
        ));

    }

    /**
     * Retire le joueur de la base de donnée si confirmation, sinon retour à la page d'acceuil
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmDeleteJoueur(Application $app) {

        session_start();

        $id = htmlspecialchars($_POST['idJoueur']);
        $token = htmlspecialchars($_POST['token']);

        $tokenService = new TokenService();
        if ( $tokenService->verif_token($token) == false) $erreurs['token'] = 'Erreur dans le token';

        if(! empty($erreurs)) {

            $tokenService = new TokenService();
            $tokenService->init_token();

            $this->joueurModel = new JoueurModel($app);
            $donnees = $this->joueurModel->getJoueurById($id);

            return $app["twig"]->render('foot/v_delete_joueur.html.twig', array(
                'data' => $donnees,
                'token' => $_SESSION['token'],
                'erreurs' => $erreurs
            ));

        } else {
            $this->joueurModel = new JoueurModel($app);
            $this->joueurModel->deleteJoueur($id);
            return $app->redirect($app["url_generator"]->generate("joueur.show"));
        }

    }

    /**
     * Affiche une page de modifier le joueur visé
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function updateJoueur(Application $app, $id) {

        session_start();

        $this->joueurModel = new JoueurModel($app);
        $donnees = $this->joueurModel->getJoueurById($id);

        $this->clubModel = new ClubModel($app);
        $clubs = $this->clubModel->getAllClub();
        $club = $this->clubModel->getClubById($donnees['idClub']);

        $tokenService = new TokenService();
        $tokenService->init_token();

        return $app["twig"]->render('foot/v_update_joueur.html.twig', array(
            'donnees' => $donnees,
            'dateNaissance' => HelperDate::afficheDate($donnees['dateNaissance']),
            'clubs' => $clubs,
            'club' => $club,
            'token' => $_SESSION['token']
        ));

    }

    /**
     * Confirme la modification après vérification
     * @param Application $app
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmUpdateJoueur(Application $app) {

        if (isset($_POST['nomJoueur']) and isset($_POST['prenomJoueur']) and isset($_POST['dateNaissance']) and isset($_POST['idClub'])) {

            session_start();

            $donnees = [
                'idJoueur' => htmlspecialchars($_POST['idJoueur']),
                'nomJoueur' => htmlspecialchars($_POST['nomJoueur']),
                'prenomJoueur' => htmlspecialchars($_POST['prenomJoueur']),
                'dateNaissance' => htmlspecialchars($_POST['dateNaissance']),
                'idClub' => htmlspecialchars($_POST['idClub']),
                'token' => htmlspecialchars($_POST['token'])
            ];

            //var_dump($donnees); die();
            $tokenService = new TokenService();

            if (! preg_match("/^[A-Za-z ]{2,}/", $donnees['nomJoueur'])) $erreurs['nomJoueur'] = 'nom composé de 2 lettres minimum';
            if (! preg_match("/^[A-Za-z ]{2,}/", $donnees['prenomJoueur'])) $erreurs['prenomJoueur'] = 'prenom composé de 2 lettres minimum';
            if (! HelperDate::verifDate($donnees['dateNaissance'])) $erreurs['dateNaissance'] = 'la date doit être remplit sous le format jj/mm/aaaa';
            if (! is_numeric($donnees['idClub'])) $erreurs['idClub'] = 'veuillez saisir une valeur';
            if ( $tokenService->verif_token($donnees['token']) == false) $erreurs['token'] = 'Erreur dans le token';

            if(! empty($erreurs)) {

                $this->clubModel = new ClubModel($app);
                $clubs = $this->clubModel->getAllClub();
                $club = $this->clubModel->getClubById($donnees['idClub']);

                $tokenService = new TokenService();
                $tokenService->init_token();

                return $app["twig"]->render('foot/v_update_joueur.html.twig', array(
                    'donnees' => $donnees,
                    'dateNaissance' =>$donnees['dateNaissance'],
                    'erreurs' => $erreurs,
                    'clubs' => $clubs,
                    'club' => $club,
                    'token' => $_SESSION['token']
                ));

            } else {
                $this->joueurModel = new JoueurModel($app);
                $this->joueurModel->updateJoueur($donnees);
                return $app->redirect($app["url_generator"]->generate("joueur.show"));
            }

        } else return "error ????? PB data form";

    }

    /**
     * Retourne routes to connect to the given application.
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app){

        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\JoueurController::show')->bind('joueur.show');

        $controllers->get('/add', 'App\Controller\JoueurController::addJoueur')->bind('joueur.addJoueur');
        $controllers->post('/add', 'App\Controller\JoueurController::confirmAddJoueur')->bind('joueur.confirmAddJoueur');

        $controllers->get('/delete/{id}', 'App\Controller\JoueurController::deleteJoueur')->bind('joueur.deleteJoueur');
        $controllers->delete('/deleteConfirm', 'App\Controller\JoueurController::confirmDeleteJoueur')->bind('joueur.confirmDeleteJoueur');

        $controllers->get('/update/{id}', 'App\Controller\JoueurController::updateJoueur')->bind('joueur.updateJoueur');
        $controllers->put('/updateConfirm', 'App\Controller\JoueurController::confirmUpdateJoueur')->bind('joueur.confirmUpdateJoueur');

        return $controllers;

    }

}