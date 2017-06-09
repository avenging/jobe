<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ==============================================================
 *
 * C
 *
 * ==============================================================
 *
 * @copyright  2014 Richard Lobb, University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('application/libraries/docker/DockerLanguageTask.php');
use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\API\Model\ContainerConfig;

class C_Task extends DockerTask {

    public function __construct($source, $filename, $input, $params) {
        DockerTask::__construct($source, $filename, $input, $params);
        $this->default_params['compileargs'] = array(
            '-Wall',
            '-Werror',
            '-std=c99',
            '-x c');
    }

    public static function getVersionCommand() {
        return array('gcc --version', '/gcc \(.*\) ([0-9.]*)/');
    }

    public function compile() {

        $src = basename($this->sourceFileName);
        $errorFileName = "$src.err";
        $execFileName = "$src.exe";
        $compileargs = $this->getParam('compileargs');
        $linkargs = $this->getParam('linkargs');
	$cmddocker = "gcc " . implode(' ', $compileargs) . " -o " . DockerTask::DOCKER_WORK_DIR . "/" . $execFileName . " " . DockerTask::DOCKER_WORK_DIR . "/" . $src . " " . implode(' ', $linkargs); 
        // incase no linker args we want rid of the whitespace at the end.
	$cmddocker = trim($cmddocker);
	$cmddocker = explode(' ', $cmddocker);
	$result = $this->dockerExec($cmddocker);
	$returnVar = $result['retVal'];
	$this->cmpinfo = $result['stderr'];
        if ($returnVar == 0) {
            $this->cmpinfo = '';
            $this->executableFileName = $execFileName;
        }

    }

    // A default name for C programs
    public function defaultFileName($sourcecode) {
        return 'prog.c';
    }


    // The executable is the output from the compilation
    public function getExecutablePath() {
	return DockerTask::DOCKER_WORK_DIR . "/" . $this->executableFileName;
    }


    public function getTargetFile() {
        return '';
    }
};
