<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
 */

namespace NevStokes\FileManager;

/**
 *
 */
class TwigLoader implements \Twig_LoaderInterface
{
	/**
	 * [$_manager description]
	 * @var FileManagerInterface
	 */
	protected $_manager;

	/**
	 * Path to the templates
	 * @var string
	 */
	protected $_path;

	/**
	 * [__construct description]
	 * @param FileManagerInterface $manager [description]
	 * @param [type]               $path    [description]
	 */
	public function __construct(FileManagerInterface $manager, $path)
	{
		$this->_manager = $manager;
		$this->_path = trim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * [_getPathToFile description]
	 * @param  string $name [description]
	 * @return string       [description]
	 */
	protected function _getPathToFile($name)
	{
		return $this->_path . $name;
	}

	/**
	 * [getSource description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getSource($name)
	{
		$file = $this->_getPathToFile($name);

		return $this->_manager->get_contents($file);
	}

	/**
	 * [getCacheKey description]
	 * @param  string $name [description]
	 * @return string       [description]
	 */
	public function getCacheKey($name)
	{
		$file = $this->_getPathToFile($name);

		return md5($name);
	}

	/**
	 * [isFresh description]
	 * @param  string  $name [description]
	 * @param  int  $time [description]
	 * @return boolean       [description]
	 */
	public function isFresh($name, $time)
	{
		$file = $this->_getPathToFile($name);
		$mod = $this->_manager->mtime($file);

		return ($mod < $time);
	}
}
