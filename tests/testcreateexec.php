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
#$execConfig->setCmd(["/bin/ls", "?"]) ;
$execConfig->setCmd(["/bin/gcc", "-o", "/home/jobe/prog", "/home/jobe/test.c"]) ;
#$execConfig->setAttachStdin(true);
$execConfig->setAttachStdout(true);
$execConfig->setAttachStderr(true);
$execStartConfig = new ExecStartConfig() ;
$execStartConfig->setDetach(false) ;
$execCreateResponse = $execManager->create($containerCreateResult->getId(), $execConfig, []) ;
$response = $execManager->start($execCreateResponse->getId(), $execStartConfig, []) ;

$stream = new \Docker\Stream\DockerRawStream($response->getBody());

$stream->onStdout(function($stdout) {
    echo "stdout: " . $stdout;
  });
  $stream->onStderr(function($stderr) {
    echo "stderr: " . $stderr;
  });

$stream->wait();

#$resource = StreamWrapper::getResource($stream);
#var_dump($stream) ;
#while (!feof($resource)) {
#	$line = fgets($resource);
#}

# Dump the first char by reading 8 bytes
#$stream->read(8) ;
#while (!$stream->eof()) {
#	$line = $stream->read(1024);
#	echo $line ;
##}
#
echo $execManager->find($execCreateResponse->getId())->getExitCode();
echo "\n" ;

# Now run the command
$execManager = $docker->getExecManager() ;
$execConfig = new ExecConfig() ;
$execConfig->setCmd(["/home/jobe/prog"]) ;
#$execConfig->setAttachStdin(true);
$execConfig->setAttachStdout(true);
#$execConfig->setAttachStderr(true);
$execStartConfig = new ExecStartConfig() ;
$execStartConfig->setDetach(false) ;
$execCreateResponse = $execManager->create($containerCreateResult->getId(), $execConfig, []) ;
$response = $execManager->start($execCreateResponse->getId(), $execStartConfig, []) ;

#$stream = $response->getBody();
# Dump the first char by reading 8 bytes
#$stream->read(8) ;
#while (!$stream->eof()) {
#        $line = $stream->read(1024);
#        echo $line ;
#}
$stream = new \Docker\Stream\DockerRawStream($response->getBody());

$stream->onStdout(function($stdout) {
    #echo "stdout: " . $stdout;
  });
  $stream->onStderr(function($stderr) {
    #echo "stderr: " . $stderr;
  });

$stream->wait();

echo $execManager->find($execCreateResponse->getId())->getExitCode();
echo "\n";


#var_dump($execManager->inspect($execCreateResponse->getId()));
#$containerManager->stop($containerCreateResult->getId()) ;
#$containerManager->remove($containerCreateResult->getId());

#var_dump($stream) ;
