<?php
/**
 * Compiler
 *
 * Compile template files into programs
 *
 * @package   Dandelion
 * @author    Fedic
 * @version   1.0.0
 * @copyright (2013) Dandelion Framework
 *****************************************
 */

class Compiler
{
	private $T_P = array();
	private $T_R = array();

	function __construct()
	{
		//Pattern
		$this->T_P[] = "#\{\\$(\w*?)\}#";
		$this->T_P[] = "#\{\\%(.*?)\}#";
		$this->T_P[] = "#\{\\@(.*?)\}#";
		$this->T_P[] = "#\{\\&([\+\-]\d*?) \\$(.*?)\}#";
		$this->T_P[] = "#\{\\$(\w*?) \\$(\w*?)\}#";
		$this->T_P[] = "#\{\\$(\w*?) \\%(\w*?)\}#";
		$this->T_P[] = "#\{foreach \\$(\w*?)}#";
		$this->T_P[] = "#\{(key|value)}#";
		$this->T_P[] = "#\{loop key\{(\w*?)\} value\{(\w*?)\} \\$(\w*?)\}#";
		$this->T_P[] = "#\{if (.*?)\}#";
		$this->T_P[] = "#\{elseif (.*?)\}#";
		$this->T_P[] = "#\{else\}#";
		$this->T_P[] = "#\{\/(foreach|loop|if)}#";
		//Replacement
		$this->T_R[] = "<?php echo \$this->value['\\1']; ?>";
		$this->T_R[] = "\$this->value['\\1']";
		$this->T_R[] = "<?php echo \$\\1; ?>";
		$this->T_R[] = "<?php echo \$this->value['\\2']\\1; ?>";
		$this->T_R[] = "<?php echo \$this->value['\\1']['\\2']; ?>";
		$this->T_R[] = "\$this->value['\\1']['\\2']";
		$this->T_R[] = "<?php foreach (\$this->value['\\1'] as \$key => \$value) { ?>";
		$this->T_R[] = "<?php echo \$\\1; ?>";
		$this->T_R[] = "<?php foreach (\$this->value['\\3'] as \$\\1 => \$\\2) { ?>";
		$this->T_R[] = "<?php if (\\1) { ?>";
		$this->T_R[] = "<?php } elseif (\\1) { ?>";
		$this->T_R[] = "<?php } else { ?>";
		$this->T_R[] = "<?php } ?>";
	}

	/**
	 * @param (string) $input
	 * @param (string) $output
	 */
	public function compile($input, $output)
	{
		if (!file_exists($input)) die("Template file not found!");
		$content = file_get_contents($input);
		$content = preg_replace($this->T_P, $this->T_R, $content);
		file_put_contents($output, $content);
		unset($content);
	}
}
?>