<?php

require '../vendor/autoload.php' ;

use Docker\Docker;
use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\API\Model\ContainerConfig;


$docker = new Docker();
$containerManager = $docker->getContainerManager();

$containerConfig = new ContainerConfig();
$containerConfig->setOpenStdin(true);
$containerConfig->setUser('jobe') ;
$containerConfig->setImage('local/jobe-centos');
$containerConfig->setCmd(['/bin/bash']);
$containerConfig->setNetworkDisabled(true);

$containerCreateResult = $containerManager->create($containerConfig);
$containerManager->start($containerCreateResult->getId());
