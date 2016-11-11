<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class LoginModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    /**
     * Vérifie dans la base de données si le login ou le mot de passe éxiste
     * @param $login
     * @param $mdp
     * @return bool|mixed
     */
    public function verifLogin($login, $mdp){

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('u.login, u.motdepasse')
            ->from('utilisateurs', 'u')
            ->where('u.login = :login')
            ->andWhere('u.motdepasse = :mdp')
            ->setParameter('login', $login)
            ->setParameter('mdp', $mdp);
        $resultat = $queryBuilder->execute()->fetch();

        if ($resultat != false) return $resultat;

        return false;

    }

    /**
     * Renvoit les droit de la personne
     * @param $login
     * @param $mdp
     * @return mixed
     */
    public function recupDroit($login, $mdp) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('u.droit')
            ->from('utilisateurs', 'u')
            ->where('u.login = :login')
            ->andWhere('u.motdepasse = :mdp')
            ->setParameter('login', $login)
            ->setParameter('mdp', $mdp);
        return $queryBuilder->execute()->fetch();
    }

}