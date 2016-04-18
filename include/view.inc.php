<?php
/**
 * View
 *
 * Call template compiller and processing pages
 *
 * @package   Dandelion
 * @author    Fedic
 * @version   1.0.0
 * @copyright (2013) Dandelion Framework
 *****************************************
 */

class View
{
	private $value = array();
	private $compiler;

	/**
	 * @param (string) $file
	 * @param (string) $id
	 * @param (bool) $cache
	 */
	public function build($file, $id='', $cache=true)
	{
		$html_file = ROOT.'web/html/'.$file;
		$cache_file = ROOT.'temp/cache/'.$id.'.ch';
		$compile_file = ROOT.'temp/compile/'.$file.'.php';
		if (!is_file($compile_file) || MODE == 3) {
			$compiler = $this->load_compiler();
			$compiler->compile($html_file, $compile_file);
		}
		ob_start();
		require($compile_file);
		$html = ob_get_contents();
		ob_end_clean();
		if ($cache) {
			file_put_contents($cache_file, $html);
		}
		return $html;
	}

	/**
	 * @param (string) $key
	 * @param (string) $value
	 */
	public function assign($key, $value)
	{
		$this->value[$key] = $value;
	}

	private function load_compiler()
	{
		if ($compiler == NULL) {
			require_once(ROOT.'include/compiler.inc.php');
			$this->compiler = new Compiler;
		}
		return $this->compiler;
	}
}
?>