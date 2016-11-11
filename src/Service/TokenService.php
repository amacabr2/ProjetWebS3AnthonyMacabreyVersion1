<?php

namespace App\Service;

class TokenService {

    public $token;

    /**
     *Initialisation de token
     */
    public function init_token(){
        //On démarre les sessions dans le fichier connexion_bdd.php
        //On génére un jeton totalement unique
        $token = uniqid(rand(), true);
        //Et on le stocke
        $_SESSION['token'] = $token;
        //On enregistre aussi le timestamp correspondant au moment de la création du token
        $_SESSION['token_time'] = time();
    }

    /**
     * Vérifie la validité du token
     * @param $token
     * @return bool
     */
    public function verif_token($token) {
        if (isset($_SESSION['token']) && isset($_SESSION['token_time']) && !empty($token)) {
            if ($_SESSION['token'] == $token) { //Si le jeton de la session correspond à celui du formulaire
                $timestamp_ancien = time() - (15 * 60); //On stocke le timestamp qu'il était il y a 15 minutes
                //Si le jeton n'est pas expiré
                if ($_SESSION['token_time'] >= $timestamp_ancien) return true; //FIN PROTECTION CSRF
                else return false;
            } else return false;
        }
        return false;
    }

}