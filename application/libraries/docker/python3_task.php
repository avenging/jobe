<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ==============================================================
 *
 * Python3
 *
 * ==============================================================
 *
 * @copyright  2014 Richard Lobb, University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('application/libraries/docker/DockerLanguageTask.php');

class Python3_Task extends DockerTask {
    public function __construct($source, $filename, $input, $params) {
        DockerTask::__construct($source, $filename, $input, $params);
        $this->default_params['interpreterargs'] = array('-BE');
    }

    public static function getVersionCommand() {
        return array('python3 --version', '/Python ([0-9._]*)/');
    }

    public function compile() {

        $outputLines = array();
        $returnVar = 0;
   	$cmddocker = "python3 -m py_compile " . DockerTask::DOCKER_WORK_DIR . "/" . $this->sourceFileName;
        $cmddocker = explode(' ', $cmddocker);
        $result = $this->dockerExec($cmddocker);
        $returnVar = $result['retVal'];
        $this->cmpinfo = $result['stderr'];
        if ($returnVar == 0) {
            $this->cmpinfo = '';
            $this->executableFileName = $this->sourceFileName;
        }
        else {
	    $output = $result['stdout'];
	    $compileErrs = $result['stderr'];
            if ($output) {
                $this->cmpinfo = $output . '\n' . $compileErrs;
            } else {
                $this->cmpinfo = $compileErrs;
            }
        }
    }


    // A default name for Python3 programs
    public function defaultFileName($sourcecode) {
        return 'prog.py';
    }


    public function getExecutablePath() {
        return '/usr/bin/python3';
     }


     public function getTargetFile() {
         return DockerTask::DOCKER_WORK_DIR . "/" . $this->sourceFileName;
     }
};
