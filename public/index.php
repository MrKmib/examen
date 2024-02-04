<?php
//declare(strict_types=1);
/* Esta es la unica puerta de entrada */

require_once '../vendor/autoload.php';

//$config = require '../.env.php';
//echo $config['username'];


//importamos las configuraciones de la base de datos
require_once '../config/database.php';

/*--cargado de Route desde autoload.php--*/
//require_once '../lib/Route.php';
require_once '../autoload.php';

/*---rutas de routes/web.php----*/
require_once '../routes/web.php';