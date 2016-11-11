<?php

namespace App\Helper;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class HelperMiddleWare{

    function verifAdmin(Request $request, Application $app) {
        if ($app['session']->get('droit') != 'DROITadmin')
            return $app->redirect($app["url_generator"]->generate("index.errorDroit"));
    }

}