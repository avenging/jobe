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

require_once('application/libraries/LanguageTask.php');
use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\API\Model\ContainerConfig;

class C_Task extends Task {

    public function __construct($source, $filename, $input, $params) {
        Task::__construct($source, $filename, $input, $params);
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
	$returnVar = 1 ;
	if ($this->usedocker) {
	        $cmddocker = "gcc " . implode(' ', $compileargs) . " -o " . Task::DOCKER_WORK_DIR . "/" . $execFileName . " " . Task::DOCKER_WORK_DIR . "/" . $src . " " . implode(' ', $linkargs); 
		// incase no linker args we want rid of the whitespace at the end.
		$cmddocker = trim($cmddocker);
		$cmddocker = explode(' ', $cmddocker);
		$result = $this->dockerExec($cmddocker);
		$returnVar = $result['retVal'];
		$this->cmpinfo = $result['stderr'];
	}
	else {

	        $cmd = "gcc " . implode(' ', $compileargs) . " -o $execFileName $src " . implode(' ', $linkargs) . " 2>$errorFileName";
	        exec($cmd, $output, $returnVar);
		if ($returnVar != 0) {
			$this->cmpinfo = file_get_contents($errorFileName);
		}
	}
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
	if ($this->usedocker) {
	        return Task::DOCKER_WORK_DIR . "/" . $this->executableFileName;
	}
	else {
	        return "./" . $this->executableFileName;
	}
    }


    public function getTargetFile() {
        return '';
    }
};
