<?php
/**
 * Created by PhpStorm.
 * User: Anthony Macabrey
 * Date: 15/10/2016
 * Time: 15:39
 */

namespace App\Helper;

class HelperDate{

    /**
     * Indique si la date mis dans le formuaire est juste
     * @param $date
     * @return bool
     */
    public static function verifDate($date) {
        if ($date == "") return false;
        if (! preg_match("^\\d{1,2}/\\d{2}/\\d{4}^", $date)) return false;
        $dateCoupe = explode("/", $date);
        $annee = $dateCoupe[2];
        $mois = $dateCoupe[1];
        $jour = $dateCoupe[0];
        for ($i = 0; $i < sizeof($date); $i++) {
            if (!is_numeric($date[$i])) return false;
        }
        if ($mois > 12) return false;
        if ($mois % 2 == 0) {
            if ($mois != '2' and $mois != '02') {
                if ($jour > 30) return false;
            } else {
                if ($jour > 29) return false;
            }
        } else {
            if ($jour > 31 ) return false;
        }
        return true;
    }

    /**
     * Transforme la date pour la base de données (FR => US)
     * @param $date
     * @return mixed
     */
    public static function transformDate($date) {
        $date = explode("/", $date);
        $newsdate = $date[2] . '-' . $date[1] . '-'. $date[0];
        return $newsdate;
    }

    /**
     * Transforme la date pour l'affichage (US => FR)
     * @param $date
     * @return string
     */
    public static function afficheDate($date) {
        $date = explode("-", $date);
        $newdate = $date[2] . '/' . $date[1] . '/' . $date[0];
        return $newdate;
    }

}