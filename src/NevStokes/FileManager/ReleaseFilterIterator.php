<?php

/**
 *
 */

namespace NevStokes\FileManager;

/**
 *
 */
class ReleaseFilterIterator extends \FilterIterator
{
	private $_cutoff;

	/**
	 * [__construct description]
	 * @param Iterator $iterator [description]
	 * @param [type]   $cutoff   [description]
	 */
	public function __construct(\Iterator $iterator, $cutoff)
	{
		parent::__construct($iterator);
		$this->_cutoff = $cutoff;
	}

	/**
	 * [accept description]
	 * @return [type] [description]
	 */
	public function accept()
	{
		$release = $this->getInnerIterator()->current();
		return ($release->getFilename() <= $this->_cutoff);
	}
}
