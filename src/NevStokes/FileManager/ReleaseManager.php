<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
 * @package FileManager
 */

namespace NevStokes\FileManager;

use Symfony\Component\Finder\Finder;

/**
 *
 */
class ReleaseManager extends LiveFileManager
{
	/**
	 * [$_base description]
	 * @var \DateTime
	 */
	protected $_base;

	/**
	 * [$_releases description]
	 * @var array
	 */
	protected $_releases;

	/**
	 * [__construct description]
	 * @param \DateTime $base [description]
	 * @param string $dir  The path to releases
	 */
	public function __construct(
		\DateTime $base = null,
		$dir = LiveFileManager::BASEDIR
	) {
		parent::__construct($dir);

		// default to now
		$base = $base ?: new \DateTime;

		// Format the timestamp consistently
		$this->_base = $base->format(self::PATTERN);

		// Work out which subdirectory we need to search
		$this->_scan = (($this->_base < date(self::PATTERN)) ?
			self::APPLIED : self::SCHEDULED
		);
	}

	/**
	 *
	 */
	public function getDirectory()
	{
		return $this->_dir;
	}

	/**
	 *
	 */
	public function getBase()
	{
		return $this->_base;
	}

	/**
	 *
	 */
	public function getInitialReleaseDirectory()
	{
		return $this->_dir . FileManagerInterface::APPLIED . DIRECTORY_SEPARATOR . '000000000000';
	}

	/**
	 *
	 */
	public function getPreviewDirectory()
	{
		return $this->_dir . $this->_scan;
	}

	/**
	 * [getReleases description]
	 * @return array [description]
	 */
	public function getReleases($dir = null)
	{
		if (!isset($this->_releases)) {
			$dir = $dir ?: $this->_dir;

			$finder = new Finder;
			$this->_releases = $finder->
				directories()->
				in($dir)->
				depth('< 2')->
				filter(function(\SplFileInfo $file) {
					return (preg_match('/^\d{12}$/', $file->getFilename()) > 0);
			});
		}

		return $this->_releases;
	}

	/**
	 * [get description]
	 * @param  string $file [description]
	 * @return string       [description]
	 */
	public function get($file)
	{
		// normalisation
		$file = trim($file, '/');
		$scan = $this->getPreviewDirectory();

		// split the request into file and path components
		$basename = basename($file);
		$path = str_replace($basename, '', $file);

		$fileFinder = new Finder;

		// find all pertinent release directories
		$finder = new Finder;
		$releases = $finder->directories()->in($scan);

		// only interested in releases prior to the cutoff
		$filter = new ReleaseFilterIterator($releases->getIterator(), $this->_base);

		// test found releases
		if (iterator_count($filter)) {
			// at least one release was found
			foreach ($filter as $dir) {
				// add the release to the list of places to search
				$fileFinder->in($dir->getRealpath());
			}
		}

		// don't fallback to live if reviewing historic releases
		if (basename($scan) != self::APPLIED) {
			$fileFinder->in($this->_dir . self::LIVE);
		}

		// find matching files in release folders
		$results = $fileFinder->
			files()->
			path($path)->
			name($basename);

		// convert to array for easier access
		$versions = iterator_to_array($results);

		// return the most recent revision
		return array_shift($versions);
	}
}
