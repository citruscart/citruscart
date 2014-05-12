<?php

/*------------------------------------------------------------------------
# com_citruscart
# ------------------------------------------------------------------------
# author   Citruscart Team  - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE .'/libraries/dioscouri/library/image.php';
class CitruscartImage extends DSCImage
{
	var $image;
	var $type;
	var $is_archive = false;
	var $archive_files = array( );

	public $thumb_width = '160';
	public $thumb_height = '90';

	/**
	 * Support Zip files for image galleries
	 * @see CitruscartFile::upload()
	 */
	function upload( )
	{
		if ( $result = parent::upload( ) )
		{
			// Check if it's a supported archive
			$allowed_archives = array(
				'zip', 'tar', 'tgz', 'gz', 'gzip', 'tbz2', 'bz2', 'bzip2'
			);

			if ( in_array( strtolower( $this->getExtension( ) ), $allowed_archives ) )
			{
				$dir = $this->getDirectory( );
				jimport( 'joomla.filesystem.archive' );
				JArchive::extract( $this->full_path, $dir );
				JFile::delete($this->full_path);

				$this->is_archive = true;

				$files = JFolder::files( $dir );

				// Thumbnails support
				if ( count( $files ) )
				{
					// Name correction
					foreach ( $files as &$file )
					{
						$file = new CitruscartImage( $dir . '/' . $file);
					}

					$this->archive_files = $files;
					$this->physicalname = $files[0]->getPhysicalname( );
				}
			}

		}

		return $result;
	}
}