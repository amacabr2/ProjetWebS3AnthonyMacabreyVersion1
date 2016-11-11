<?php

namespace App\Model;


use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class ClubModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    /**
     * Retourne les informations de tous les clubs
     * @return array
     */
    public function getAllClub() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.idClub', 'c.nomClub', 'c.villeClub', 'count(j.idClub) as nbrJoueurByClub')
            ->from('club', 'c')
            ->innerJoin('c', 'joueur', 'j', 'j.idClub = c.idClub')
            ->addOrderBy('c.nomClub', 'ASC')
            ->groupBy('c.idClub');
        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Retourne les informations d'un seul club par rapport à son identifiant
     * @param $id
     * @return mixed
     */
    public function getClubById($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.idClub', 'c.nomClub', 'c.villeClub')
            ->from('club', 'c')
            ->where('c.idClub = :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    /**
     * Retourne la ville par rapport au nom du club
     * @param $club
     * @return mixed
     */
    public function getVilleForClub($club) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.villeClub')
            ->from('club', 'c')
            ->where('c.nomClub = :club')
            ->setParameter('club', $club);
        return $queryBuilder->execute()->fetch();
    }

    public function getNbrJoueurByClub($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('count(j.idJoueur) as nbrJoueurById')
            ->from('club', 'c')
            ->where('j.idClub = :idClub')
            ->innerJoin('c', 'joueur', 'j', 'j.idClub = c.idClub')
            ->setParameter(':idClub', $id);
        return $queryBuilder->execute()->fetchAll();
    }

}