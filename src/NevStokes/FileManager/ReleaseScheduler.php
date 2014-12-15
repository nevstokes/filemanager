<?php

/**
 *
 */

namespace NevStokes\FileManager;

use NevStokes\Utilities\Logging\Notifier;
use NevStokes\Utilities\Logging\LogMessage;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 *
 */
class ReleaseScheduler extends Notifier
{
	/**
	 *
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
		$cutoff = $this->_manager->getBase();
		$preview = $this->_manager->getPreviewDirectory();

		$releases = $this->_manager->getReleases($preview);

		// only interested in releases prior to the cutoff
		$filter = new ReleaseFilterIterator($releases->getIterator(), $cutoff);

		if (iterator_count($filter)) {
			$fs = new Filesystem();

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
		}
	}
}
