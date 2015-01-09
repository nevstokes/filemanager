<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
 */

namespace NevStokes\FileManager;

/**
 *
 */
class ReleaseFilterIterator extends \FilterIterator
{
	/**
	 * The formatted date string to filter releases on
	 * @var string
	 */
	private $_cutoff;

	/**
	 * [__construct description]
	 * @param Iterator $iterator The iterator to filter
	 * @param string   $cutoff   THe date string to filter on
	 */
	public function __construct(\Iterator $iterator, $cutoff)
	{
		parent::__construct($iterator);
		$this->_cutoff = $cutoff;
	}

	/**
	 * Acceptance method of the filter
	 *
	 * Each item in the iterator will be tested with this method for inclusion
	 * by the filter.
	 *
	 * @return boolean true if release should be included, false otherwise
	 */
	public function accept()
	{
		$release = $this->getInnerIterator()->current();
		return ($release->getFilename() <= $this->_cutoff);
	}
}
