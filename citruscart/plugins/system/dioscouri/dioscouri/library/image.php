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
require_once JPATH_SITE .'/libraries/dioscouri/library/file.php';

class DSCImage extends DSCFile
{
	var $image;
	var $type;
	var $_full_path;
	public $thumb_width = '160';
	public $thumb_height = '90';

	function __construct($filename = "")
	{
		parent::__construct();

		if(!empty($filename))
		{
			if ( !JFile::exists($filename))
			{
				$this->setError("Image does not exist");
				return;
			}

			$this->full_path = $filename;
			$this->setDirectory( substr( $this->full_path, 0, strrpos($this->full_path, DS) ) );
			$this->proper_name = JFile::getName($filename);

			if (!empty($this->full_path))
			{
                $image_info = getimagesize($this->full_path);
                $this->type = $image_info[2];
			}
		}
	}

    /**
     * Prepares the storage directory
     * We override the parent::setDirectory()
     * because images dont need htaccess
     *
     * @param mixed Boolean
     * @param mixed Boolean
     * @return array
     */
    function setDirectory( $dir=null ,$protect = true )
    {
        $success = false;

        // checks to confirm existence of directory
        // then confirms directory is writeable
        if ($dir === null)
        {
            $dir = $this->getDirectory();
        }

        $helper = new DSCHelper();
        $helper->checkDirectory($dir);
        $this->_directory = $dir;
        return $this->_directory;
    }

	/**
	 * Load the image!
	 */
	function load()
	{
		$filename = $this->full_path;
		$image_info = getimagesize($filename);
      	$this->type = $image_info[2];

		if( $this->type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}

	/**
	 * Save the image and chmods
	 * @param $filename
	 * @param $image_type image type: png, gif, jpeg
	 * @param $compression
	 * @param $permissions
	 */
	function save($filename, $compression=75, $permissions=null)
	{
        $success = true;
        $image_type = $this->type;

        ob_start();
        //$mime = image_type_to_mime_type( $image_type );
        //header("Content-type: $mime");

        if( $image_type == IMAGETYPE_JPEG ) {
            if (!$success = imagejpeg($this->image, null ))
            {
                $this->setError( "DSCImage::save( 'jpeg' ) Failed" );
            }
        } elseif( $image_type == IMAGETYPE_GIF ) {
            if (!$success = imagegif($this->image, null ))
            {
                $this->setError( "DSCImage::save( 'gif' ) Failed" );
            }
        } elseif( $image_type == IMAGETYPE_PNG ) {
            if (!$success = imagepng($this->image, null ))
            {
                $this->setError( "DSCImage::save( 'png' ) Failed" );
            }
        }

        if ($success)
        {
            $imgToWrite = ob_get_contents();
            ob_end_clean();

            if (!JFile::write( $filename, $imgToWrite))
            {
                $this->setError( JText::_( "Could not write file" ).": ".$filename );
                return false;
            }

            if( $permissions != null) {
                chmod($filename,$permissions);
            }
            unset($this->image);
            return true;
        }

        return false;
	}

	/**
	 * Get the image width
	 */
	function getWidth() {
		return imagesx($this->image);
	}

	/**
	 * Get the image height
	 */
	function getHeight() {
		return imagesy($this->image);
	}

	/**
	 * Resize the image to a defined height
	 * @param $height
	 */
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	/**
	 * Resize the image to a defined width
	 * @param $width
	 */
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}

	/**
	 * Scale the image to the defined proportion in %
	 * @param unknown_type $scale
	 */
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	/**
	 * Resize the image
	 * Based heavily on http://github.com/maxim/smart_resize_image
	 * by maxim - thanks man!
	 * @param $width
	 * @param $height
	 */
	function resize( $width, $height )
	{
        //		$new_image = imagecreatetruecolor($width, $height);
        //
        //		    imagesavealpha($new_image, true);
        //		    $trans_colour = imagecolorallocatealpha($new_image, 255, 255, 255, 256);
        //		    imagefill($new_image, 0, 0, $trans_colour);
        //		    header("Content-type: image/png");
        //		    imagepng($new_image);
        //
        //		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        //
        //		imagefill($new_image, 0, 0, $trans_colour);
        //
        //		$this->image = $new_image;

		/****************/

    	$image_resized = imagecreatetruecolor( $width, $height );

        if ( ($this->type == IMAGETYPE_GIF) || ($this->type == IMAGETYPE_PNG) )
        {
            $transparency_index = imagecolortransparent($this->image);

            // If we have a specific transparent color
            if ($transparency_index >= 0)
            {
                // Get the original image's transparent color's RGB values
                $transparent_color    = imagecolorsforindex($this->image, $transparency_index);

                // Allocate the same color in the new image resource
                $transparency_index    = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);

                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $transparency_index);

                // Set the background color for new image to transparent
                imagecolortransparent($image_resized, $transparency_index);
            }
                elseif ($this->type == IMAGETYPE_PNG)
            {
                // Always make a transparent background color for PNGs that don't have one allocated already

                // Turn off transparency blending (temporarily)
                imagealphablending($image_resized, false);

                // Create a new transparent color for image
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $color);

                // Restore transparency blending
                imagesavealpha($image_resized, true);
            }
        }

        imagecopyresampled( $image_resized, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );

        $this->image = $image_resized;
	}

	/**
	* Batch actions for saving a thumb of an image
	*
	* @param image	string	filename of the image
	* @param options	array	array of options: width, height, thumb_path
	* @return thumb full path
	*/
	function saveThumb( $img, $options = array() )
	{
	    $thumb_path = $img->getDirectory().DS.'thumbs';
	    $img_width = 'thumb_width';
	    $img_height = 'thumb_height';

	    $img->load();

	    // Default width or options width?
	    if(!empty($options['width']) && is_numeric($options['width']))
	    {
	        $width = $options['width'];
	    }
	        else
	    {
	        $width = $this->$img_width;
	    }

	    // Default height or options height?
	    if(!empty($options['height']) && is_numeric($options['height']))
	    {
	        $height = $options['height'];
	    }
	        else
	    {
	        $height = $this->$img_height;
	    }

	    // Default thumb path or options thumb path?
	    if(!empty($options['thumb_path']))
	    {
	        $dest_dir = $options['thumb_path'];
	    }
	        else
	    {
    	    $dest_dir = $thumb_path;
	    }

	    $helper = new DSCHelper();
	    $helper->checkDirectory($dest_dir);

	    if ($width >= $height)
	    {
	        $img->resizeToWidth( $width );
	    }
	        else
	    {
    	    $img->resizeToHeight( $height );
	    }

	    $dest_path = $dest_dir.DS.$img->getPhysicalName();

	    if (!$img->save( $dest_path ))
	    {
	        $this->setError( $img->getError() );
	        return false;
	    }

	    return $dest_path;
	}

	public static function loadUploadify(){
		static $loaded = false;


		if( $loaded )
			return;

		 DSC::loadJQuery('');
		 JHTML::_( 'script', 'jquery.uploadifive.min.js', 'media/citruscart/js/' );
		 JHTML::_( 'stylesheet', 'uploadifive.css', 'media/citruscart/css/' );
		 $loaded = true;
	}

	public static function uploadifyElement($id = "file_upload", $name = "file_upload", $queue = 'queue', $multiple="true" ) {

		$html = '<div id="'.$queue.'"></div>';
		$html .= '<input id="'.$id.'" name="'.$name.'" type="file" multiple="'.$multiple.'">';
		$html .= '<a style="position: relative; top: 8px;" href="javascript:jQuery(\'#'.$id.'\').uploadifive(\'upload\')">Upload Files</a>';
		$timestamp = time();
		$salt = md5('unique_salt' . $timestamp);
		$js = "
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : false,
				'formData'         : {
									   'timestamp' : '{$timestamp}',
									   'token'     : '{$salt}'
				                     },
				'queueID'          : '{$queue}',
				'uploadScript'     : 'uploadifive.php',
				'onUploadComplete' : function(file, data) { console.log(data); }
			});
		});";
		echo $html;

	}


}

