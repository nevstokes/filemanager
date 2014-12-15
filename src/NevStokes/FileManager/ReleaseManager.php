<?php

/**
 *
 */

namespace NevStokes\FileManager;

use Symfony\Component\Finder\Finder;

/**
 *
 */
class ReleaseManager extends LiveFileManager
{
	/**
	 * The format of a release directory name
	 */
	const PATTERN   = 'YmdHi';

	const LIVE      = 'live';
	const APPLIED   = 'applied';
	const SCHEDULED = 'scheduled';

	/**
	 * [$_base description]
	 * @var [type]
	 */
	protected $_base;

	/**
	 * [__construct description]
	 * @param [type] $dir  [description]
	 * @param [type] $base [description]
	 */
	public function __construct(
		\DateTime $base = null,
		$dir = LiveFileManager::BASEDIR
	) {
		parent::__construct($dir);

		// default to now
		$base = $base ?: new \DateTime;

		$this->_base = $base->format(self::PATTERN);
		$this->_scan = $this->_dir . (($this->_base < date(self::PATTERN)) ?
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
	public function getPreviewDirectory()
	{
		return $this->_scan;
	}

	/**
	 * [getReleases description]
	 * @return [type] [description]
	 */
	public function getReleases($dir = null)
	{
		$dir = $dir ?: $this->_dir;

		$finder = new Finder;
		$releases = $finder->
			directories()->
			in($dir)->
			depth('< 2')->
			filter(function(\SplFileInfo $file) {
				return (preg_match('/^\d{12}$/', $file->getFilename()) > 0);
		});

		return $releases;
	}

	/**
	 * [get description]
	 * @param  string $file [description]
	 * @return [type]       [description]
	 */
	public function get($file)
	{
		// normalisation
		$file = trim($file, '/');

		// split the request into file and path components
		$basename = basename($file);
		$path = str_replace($basename, '', $file);

		$fileFinder = new Finder;

		// test for preview setting
		if (isset($this->_base)) {
			// find all pertinent release directories
			$finder = new Finder;
			$releases = $finder->directories()->in($this->_scan);

			// only interested in releases prior to the cutoff
			$filter = new ReleaseFilterIterator($releases->getIterator(), $this->_base);

			// test found releases
			if (iterator_count($filter)) {
				// at least one release was found
				foreach ($filter as $dir) {
					$fileFinder->in($dir->getRealpath());
				}
			}
		}

		// don't fallback to live if reviewing historic releases
		if (!isset($this->_base) || ($this->_base > date(self::PATTERN))) {
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
		return array_pop($versions);
	}
}
