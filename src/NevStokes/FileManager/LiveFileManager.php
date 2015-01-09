<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
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
		$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		// Check the directory
		if (!is_dir($dir) && is_readable($dir)) {
			throw new \RuntimeException($dir . ' is not a directory');
		}

		$this->_dir = $dir;
	}

	/**
	 * Allow multiple domains to be served from the same release folder
	 * @param bool $enable
	 */
	public function setDomainedReleases($enable = true)
	{
		// Append the current domain to the given release directory
		// $this->_dir .= $_SERVER['SERVER_NAME'] . DIRECTORY_SEPARATOR;
		$this->_dir .= 'localhost' . DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns the contents of the file
	 * @param  string $file The file for which to get contents
	 * @return mixed       The contents of the file
	 */
	public function get_contents($file)
	{
		return file_get_contents($this->get($file));
	}

	/**
	 * Returns the last modified date of the file
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function mtime($file)
	{
		return filemtime($this->get($file));
	}

	/**
	 * Returns the path to the file
	 * @param  string $file The file for which to determine the path
	 * @return string       [description]
	 */
	public function get($file)
	{
		return $this->_dir . self::LIVE . DIRECTORY_SEPARATOR . $file;
	}
}
