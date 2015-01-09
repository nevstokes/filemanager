<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
 */

namespace NevStokes\FileManager;

use NevStokes\Utilities\Logging\Notifier;
use NevStokes\Utilities\Logging\LogMessage;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 *
 */
class ReleaseScheduler extends Notifier
{
	/**
	 * [$_manager description]
	 * @var ReleaseManager
	 */
	protected $_manager;

	/**
	 *
	 */
	public function __construct(ReleaseManager $manager)
	{
		parent::__construct();
		$this->_manager = $manager;
	}

	/**
	 *
	 */
	public function process()
	{
		$dir = $this->_manager->getDirectory();
		$initial = $this->_manager->getInitialReleaseDirectory();
		$cutoff = $this->_manager->getBase();
		$preview = $this->_manager->getPreviewDirectory();
		$releases = $this->_manager->getReleases($preview);

		// only interested in releases prior to the cutoff
		$filter = new ReleaseFilterIterator($releases->getIterator(), $cutoff);

		if (iterator_count($filter)) {
			$fs = new Filesystem();

			/*
				Need some way of indicating files to delete from live.
				- zero sized file in release?
				- filename prefixes?
				- "sync" and "delete" subdirectories?
			*/

			if (!is_dir($initial)) {
				$fs->mirror($dir . ReleaseManager::LIVE, $initial);
				$message = 'Archiving initial release';

				$this->_setMessage(new LogMessage($message, array(
					'release' => $release,
				)));
			}

			// an iterator seemingly loses its shit if its source dir its moved
			$traversable = iterator_to_array($filter);

			foreach ($traversable as $release) {
				try {
					$fs->mirror($release, $dir . ReleaseManager::LIVE);

					$message = sprintf(
						'Mirroring release to %s',
						$dir . ReleaseManager::LIVE
					);

					$this->_setMessage(new LogMessage($message, array(
						'release' => $release,
					)));

					$target = str_replace(
						ReleaseManager::SCHEDULED,
						ReleaseManager::APPLIED,
						$release
					);

					$fs->rename($release, $target);

					$message = sprintf(
						'Moving release to %s',
						$target
					);

					$this->_setMessage(new LogMessage($message, array(
						'release' => $release,
					)));
				} catch (IOExceptionInterface $e) {
					$message = new LogMessage(
						$e->getMessage(),
						array(
							'release'   => $release,
							'exception' => $e,
						),
						LOG_ALERT
					);

					$this->_setMessage($message);
				}
			}

			return true;
		}

		return false;
	}
}
