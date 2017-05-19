<?php

require 'jobe/vendor/autoload.php' ;

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
#$containerConfig->setTty(true) ;
$containerConfig->setCmd(['/bin/bash']);
#$containerConfig->setCmd(["/bin/gcc", "-o", "/home/jobe/out", "/home/jobe/test.c"]) ;
#$containerConfig->setAttachStdin(true);
#$containerConfig->setAttachStdout(true);
#$containerConfig->setAttachStderr(true);

$containerCreateResult = $containerManager->create($containerConfig);

$stream = file_get_contents('/root/job.tar');
$containerManager->putArchive($containerCreateResult->getId(), $stream, [ 'path' => '/home/jobe']);
#$attachStream = $containerManager->attach($containerCreateResult->getId(), [
#    'stream' => true,
#    'stdin' => true,
#    'stdout' => true,
#    'stderr' => true
#]);
$containerManager->start($containerCreateResult->getId());
#
#$attachStream->onStdout(function ($stdout) {
#    echo $stdout;
#});
#$attachStream->onStderr(function ($stderr) {
#    echo $stderr;
#});

#$attachStream->wait();




$execManager = $docker->getExecManager() ;
$execConfig = new ExecConfig() ;
$execConfig->setCmd(["/bin/gcc", "-o", "prog", "/home/jobe/test.c"]) ;
$execConfig->setAttachStdin(true);
$execConfig->setAttachStdout(true);
$execConfig->setAttachStderr(true);
$execStartConfig = new ExecStartConfig() ;
$execStartConfig->setDetach(false) ;
$execCreateResponse = $execManager->create($containerCreateResult->getId(), $execConfig, []) ;
$response = $execManager->start($execCreateResponse->getId(), $execStartConfig, []) ;
$stream = $response->getBody();
echo $stream->read(1024) ;
#var_dump($stream) ;
