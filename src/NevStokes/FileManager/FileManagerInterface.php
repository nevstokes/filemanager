<?php

/**
 * @author Nev Stokes <mail@nevstokes.com>
 */

namespace NevStokes\FileManager;

/**
 *
 */
interface FileManagerInterface
{
	/**
	 * The format of a release directory name
	 */
	const PATTERN   = 'YmdHi';

	const LIVE      = 'live';
	const APPLIED   = 'applied';
	const SCHEDULED = 'scheduled';

	/**
	 * [get_contents description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function get_contents($file);

	/**
	 * [get description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function get($file);
}
