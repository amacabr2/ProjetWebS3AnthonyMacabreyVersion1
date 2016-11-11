<?php

namespace App\Model;

use App\Helper\HelperDate;
use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class JoueurModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    /**
     * Renvoie les informations de tous les joueurs
     * @return mixed
     */
    public function getAllJoueur() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('j.idJoueur', 'j.nomJoueur', 'j.prenomJoueur', 'j.dateNaissance', 'j.idClub', 'c.nomClub', 'c.villeClub')
            ->from('joueur', 'j')
            ->innerJoin('j', 'club', 'c', 'j.idClub = c.idClub')
            ->addOrderBy('j.nomJoueur', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Renvoie les informations d'un joueur par rapport à son identifiant
     * @param $id
     * @return mixed
     */
    public function getJoueurById($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('j.idJoueur', 'j.nomJoueur', 'j.prenomJoueur', 'j.dateNaissance', 'j.idClub', 'c.nomClub', 'c.villeClub')
            ->from('joueur', 'j')
            ->innerJoin('j', 'club', 'c', 'j.idClub = c.idClub')
            ->where('j.idJoueur = :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    /**
     * Ajoute un joueur dans une équipe en base de données
     * @param $donnees
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function addJoueur($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert('joueur')
            ->values([
                'nomJoueur' => ':nomJoueur',
                'prenomJoueur' => ':prenomJoueur',
                'dateNaissance' => ':dateNaissance',
                'idClub' => ':idClub'
            ])
            ->setParameter('nomJoueur', $donnees['nomJoueur'])
            ->setParameter('prenomJoueur', $donnees['prenomJoueur'])
            ->setParameter('dateNaissance', HelperDate::transformDate($donnees['dateNaissance']))
            ->setParameter('idClub', $donnees['idClub']);
        return $queryBuilder->execute();
    }

    /**
     * Retire un joueur de la base de donnée
     * @param $id
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function deleteJoueur($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('joueur')
            ->where('joueur.idJoueur = :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute();
    }

    public function updateJoueur($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('joueur', 'j')
            ->set('j.nomJoueur', ':nomJoueur')
            ->set('j.prenomJoueur', ':prenomJoueur')
            ->set('j.dateNaissance', ':dateNaissance')
            ->set('j.idClub', ':idClub')
            ->where('j.idJoueur=:id')
            ->setParameter('nomJoueur', $donnees['nomJoueur'])
            ->setParameter('prenomJoueur', $donnees['prenomJoueur'])
            ->setParameter('dateNaissance', HelperDate::transformDate($donnees['dateNaissance']))
            ->setParameter('idClub', $donnees['idClub'])
            ->setParameter('id', $donnees['idJoueur']);
        return $queryBuilder->execute();
    }

}