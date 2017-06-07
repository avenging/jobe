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

require_once('application/libraries/LanguageTask.php');

class Python3_Task extends Task {
    public function __construct($source, $filename, $input, $params) {
        Task::__construct($source, $filename, $input, $params);
        $this->default_params['interpreterargs'] = array('-BE');
    }

    public static function getVersionCommand() {
        return array('python3 --version', '/Python ([0-9._]*)/');
    }

    public function compile() {

        $outputLines = array();
        $returnVar = 0;
	if ($this->usedocker) {
   	    $cmddocker = "python3 -m py_compile " . Task::DOCKER_WORK_DIR . "/" . $this->sourceFileName;
            $cmddocker = explode(' ', $cmddocker);
            $result = $this->dockerExec($cmddocker);
            $returnVar = $result['retVal'];
            $this->cmpinfo = $result['stderr'];
	}
	else {
            exec("python3 -m py_compile {$this->sourceFileName} 2>compile.out",
                    $outputLines, $returnVar);
	}
        if ($returnVar == 0) {
            $this->cmpinfo = '';
            $this->executableFileName = $this->sourceFileName;
        }
        else {
	    if ($this->usedocker) {
	        $output = $result['stdout'];
		$compileErrs = $result['stderr'];
	    }
	    else {
                $output = implode("\n", $outputLines);
                $compileErrs = file_get_contents('compile.out');
	    }
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
	 if ($this->usedocker) {
             return Task::DOCKER_WORK_DIR . "/" . $this->sourceFileName;
	 }
         else {
             return $this->sourceFileName;
         }
     }
};
