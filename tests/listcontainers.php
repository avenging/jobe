<?php

require '../vendor/autoload.php' ;

use Docker\Docker;
use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\API\Model\ContainerConfig;


$docker = new Docker();
$containerManager = $docker->getContainerManager();

$containers = $containerManager->findall(['all' => true]) ;
#$containers = $containerManager->findall([]) ;

echo count($containers) ;


var_dump($containers) ;
