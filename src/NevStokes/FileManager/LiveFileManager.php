<?php

/**
 *
 */

namespace NevStokes\FileManager;

/**
 *
 */
class LiveFileManager implements FileManagerInterface
{
	/**
	 *
	 */
	const BASEDIR = './releases';

	/**
	 * [$_dir description]
	 * @var [type]
	 */
	protected $_dir;

	/**
	 * [__construct description]
	 * @param string $dir  The path the the release directory
	 */
	public function __construct($dir = self::BASEDIR)
	{
		if (!is_dir($dir)) {
			throw new \RuntimeException($dir . ' is not a directory');
		}

		$this->_dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * [get_contents description]
	 * @param  string $file The file for which to get contents
	 * @return mixed       The contents of the file
	 */
	public function get_contents($file)
	{
		return file_get_contents($this->get($file));
	}

	/**
	 * [mtime description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function mtime($file)
	{
		return filemtime($this->get($file));
	}

	/**
	 * [get description]
	 * @param  string $file The file for which to determine the path
	 * @return string       [description]
	 */
	public function get($file)
	{
		return $this->_dir . $file;
	}
}
