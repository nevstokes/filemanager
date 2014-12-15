<?php

/**
 *
 */

namespace NevStokes\FileManager;

/**
 *
 */
interface FileManagerInterface
{
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
