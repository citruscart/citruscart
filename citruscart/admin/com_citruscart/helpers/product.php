<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2014 - 2019 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class CitruscartHelperProduct extends CitruscartHelperBase
{
    public static $products = array( );
    public static $categoriesxref = array( );

    /**
     * Gets the list of available product layout files
     * from the template's override folder
     * and the Citruscart products view folder
     *
     * Returns array of filenames
     * Array
     * (
     *     [0] => view.php
     *     [1] => camera.php
     *     [2] => cameras.php
     *     [3] => computers.php
     *     [4] => laptop.php
     * )
     *
     * @param array $options
     * @return array
     */
    function getLayouts( $options = array( ) )
    {
        $layouts = array( );
        // set the default exclusions array
        $exclusions = array(
                'default.php', 'form_askquestion.php', 'product_buy.php', 'product_children.php', 'product_comments.php',
                'product_files.php', 'product_gallery.php', 'product_rating.php', 'product_relations.php',
                'product_requirements.php', 'product_share_buttons.php', 'quickadd.php', 'search.php'
        );
        // TODO merge $exclusions with $options['exclude']

        jimport( 'joomla.filesystem.file' );
        $app = JFactory::getApplication( );
        if ( $app->isAdmin( ) )
        {
            // TODO This doesn't account for when templates are assigned to menu items.  Make it do so
            $db = JFactory::getDBO();
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                // Joomla! 1.6+ code here
                $db -> setQuery("SELECT `template` FROM #__template_styles WHERE `home` = '1' AND `client_id` = '0';");
            } else {
                // Joomla! 1.5 code here
                $db -> setQuery("SELECT `template` FROM #__templates_menu WHERE `menuid` = '0' AND `client_id` = '0';");
            }

            $template = $db -> loadResult();

        }
        else
        {
            $template = $app->getTemplate( );
        }
        $folder = JPATH_SITE . '/templates/' . $template . '/html/com_citruscart/products';

        if ( JFolder::exists( $folder ) )
        {
            $extensions = array(
                    'php'
            );

            $files = JFolder::files( $folder );
            foreach ( $files as $file )
            {
                $namebits = explode( '.', $file );
                $extension = $namebits[count( $namebits ) - 1];
                if ( in_array( $extension, $extensions ) && ( substr( $file, 0, 8 ) != 'product_' ) )
                {
                    if ( !in_array( $file, $exclusions ) && !in_array( $file, $layouts ) )
                    {
                        $layouts[] = $file;
                    }
                }
            }
        }

        // now do the Citruscart folder
        $folder = JPATH_SITE . '/components/com_citruscart/views/products/tmpl';

        if ( JFolder::exists( $folder ) )
        {
            $extensions = array(
                    'php'
            );

            $files = JFolder::files( $folder );
            foreach ( $files as $file )
            {
                $namebits = explode( '.', $file );
                $extension = $namebits[count( $namebits ) - 1];
                if ( in_array( $extension, $extensions ) )
                {
                    if ( !in_array( $file, $exclusions ) && !in_array( $file, $layouts ) )
                    {
                        $layouts[] = $file;
                    }
                }
            }
        }

        // now do the media templates folder
        $folder = Citruscart::getPath( 'products_templates' );

        if ( JFolder::exists( $folder ) )
        {
            $extensions = array(
                    'php'
            );

            $files = JFolder::files( $folder );
            foreach ( $files as $file )
            {
                $namebits = explode( '.', $file );
                $extension = $namebits[count( $namebits ) - 1];
                if ( in_array( $extension, $extensions ) )
                {
                    if ( !in_array( $file, $exclusions ) && !in_array( $file, $layouts ) )
                    {
                        $layouts[] = $file;
                    }
                }
            }
        }

        sort( $layouts );

        return $layouts;
    }

    /**
     * Determines a product's layout
     *
     * @param int $product_id
     * @param array options(
     *              'category_id' = if specified, will be used to determine layout if product doesn't have specific one
     *              )
     * @return unknown_type
     */
    function getLayout( $product_id, $options = array( ) )
    {
        static $template;

        $app = JFactory::getApplication();
        $layout = 'view';

        jimport( 'joomla.filesystem.file' );
        $app = JFactory::getApplication( );

        if ( empty( $template ) )
        {
            $template = $app->getTemplate( );
        }

        $templatePath = JPATH_SITE . '/templates/' . $template . '/html/com_citruscart/products/%s' . '.php';
        $extensionPath = JPATH_SITE . '/components/com_citruscart/views/products/tmpl/%s' . '.php';
        $mediaPath = Citruscart::getPath( 'products_templates' ) . '/' . '%s' . '.php';

        if ( isset( $this ) && is_a( $this, 'CitruscartHelperProduct' ) )
        {
            $helper = $this;
        }
        else
        {
            $helper = CitruscartHelperBase::getInstance( 'Product' );
        }
        $product = $helper->load( ( int ) $product_id );

        // set the default
        $defines = Citruscart::getInstance();
        $default_product_layout = $defines->get('default_product_layout');
        if ($default_product_layout) {
            // set default = the selected default if it still exists
            if (JFile::exists(sprintf($templatePath, $default_product_layout)) ||
                    JFile::exists( sprintf($mediaPath, $default_product_layout) )
            ) {
                $layout = $default_product_layout;
            }
        }

        // if the product->product_layout file exists in the template, use it
        if ( !empty( $product->product_layout )
                && ( JFile::exists( sprintf( $templatePath, $product->product_layout ) )
                        || JFile::exists( sprintf( $extensionPath, $product->product_layout ) )
                        || JFile::exists( sprintf( $mediaPath, $product->product_layout ) ) ) )
        {
            $new_layout = $app->triggerEvent('onGetLayoutProduct', array( $product, $product->product_layout ) );

            if( count( $new_layout ) )
                return $new_layout[0];
            else
                return $product->product_layout;
        }

        if ( !empty( $options['category_id'] ) )
        {
            $helper_category = CitruscartHelperBase::getInstance( 'Category' );
            $category_id = $options['category_id'];
            if ( empty( $helper_category->categories[$category_id] ) )
            {
                // if the options[category_id] has a layout and it exists, use it
                Citruscart::load( 'CitruscartTableCategories', 'tables.categories' );
                $helper_category->categories[$category_id] = JTable::getInstance( 'Categories', 'CitruscartTable' );
                $helper_category->categories[$category_id]->load( $category_id );
            }
            $category = $helper_category->categories[$category_id];

            if ( !empty( $category->categoryproducts_layout )
                    && ( JFile::exists( sprintf( $templatePath, $category->categoryproducts_layout ) )
                            || JFile::exists( sprintf( $extensionPath, $category->categoryproducts_layout ) ) ) )
            {
                $new_layout = $app->triggerEvent('onGetLayoutProduct', array( $product, $category->categoryproducts_layout ) );

                if( count( $new_layout ) )
                    return $new_layout[0];
                else
                    return $category->categoryproducts_layout;
            }
        }

        // if the product is in a category, try to use the layout from that one
        $categories = $helper->getCategories( $product->product_id );
        if ( !empty( $categories ) )
        {
            $helper_category = CitruscartHelperBase::getInstance( 'Category' );
            $category_id = $categories[0];
            if ( empty( $helper_category->categories[$category_id] ) )
            {
                // if the options[category_id] has a layout and it exists, use it
                Citruscart::load( 'CitruscartTableCategories', 'tables.categories' );
                $helper_category->categories[$category_id] = JTable::getInstance( 'Categories', 'CitruscartTable' );
                $helper_category->categories[$category_id]->load( $category_id );
            }
            $category = $helper_category->categories[$category_id];

            if ( !empty( $category->categoryproducts_layout )
                    && ( JFile::exists( sprintf( $templatePath, $category->categoryproducts_layout ) )
                            || JFile::exists( sprintf( $extensionPath, $category->categoryproducts_layout ) ) ) )
            {
                $new_layout = $app->triggerEvent('onGetLayoutProduct', array( $product, $category->categoryproducts_layout ) );

                if( count( $new_layout ) )
                    return $new_layout[0];
                else
                    return $category->categoryproducts_layout;
            }
        }

        // TODO if there are multiple categories, which one determines product layout?
        // if the product is in multiple categories, try to use the layout from the deepest category
        // and move upwards in tree after that

        // if all else fails, use the default!
        $new_layout = $app->triggerEvent('onGetLayoutProduct', array( $product, $layout ) );

        if( count( $new_layout ) )
            return $new_layout[0];
        else
            return $layout;
    }

    /**
     * Converts a path string to a URI string
     *
     * @param $path
     * @return unknown_type
     */
    public static function getUriFromPath( $path )
    {
        $path = str_replace( JPATH_SITE . '/', JURI::root( ), $path );
        $path = str_replace( JPATH_SITE . "/", JURI::root( ), $path );
        $path = str_replace( '/', '/', $path );
        return $path;
    }

    /**
     * Will consolidate a product's images into its currently set path.
     * If an image already exists in the current path with the same name,
     * will either leave the iamge in the old path or delete it if delete_duplicates = true
     *
     * @param $row	Citruscart Product
     * @param $delete_duplicates
     * @return unknown_type
     */
    function consolidateGalleryImages( $row, $delete_duplicates = false )
    {
        $file_moved = null;

        // get the current path for the product
        $path = $this->getGalleryPath( $row );

        // get the current list of images in the current path
        $images = $this->getGalleryImages( $path, array(), false);


        // if there are any images in the other possible paths for the product, move them to the current path
        $dir = Citruscart::getPath( 'products_images' );

        // merge the SKU-based dir if it exists and isn't the current path
        if ( !empty( $row->product_sku ) && $this->checkDirectory( $dir . '/' . $row->product_sku, false )
                && ( $dir . '/' . $row->product_sku . '/' != $path ) )
        {
            $old_dir = $dir . '/' . $row->product_sku . '/';

            $files = JFolder::files( $old_dir );
            foreach ( $files as $file )
            {
                if ( !in_array( $file, $images ) )
                {
                    if ( JFile::move( $old_dir . $file, $path . $file ) )
                    {
                        // create new thumb too
                        Citruscart::load( 'CitruscartImage', 'library.image' );
                        $img = new CitruscartImage( $path . $file);
                        $img->setDirectory( $path );
                        Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
                        $imgHelper = CitruscartHelperBase::getInstance( 'Image', 'CitruscartHelper' );
                        $imgHelper->resizeImage( $img );

                        // delete old thumb
                        JFile::delete( $old_dir . 'thumbs/' . $file );

                        $file_moved = true;
                    }
                }
                else
                {
                    // delete the old one?
                    if ( $delete_duplicates )
                    {
                        JFile::delete( $old_dir . $file );
                    }
                }
            }
        }
        else
        {
            $subdirs = CitruscartHelperProduct::getSha1Subfolders( $row->product_sku );
            // merge the SKU-based SHA1 dir if it exists and isn't the current path
            if ( !empty( $row->product_sku ) && $this->checkDirectory( $dir . '/' . $subdirs . $row->product_sku, false )
                    && ( $dir . '/' . $subdirs . $row->product_sku . '/' != $path ) )
            {
                $old_dir = $dir . '/' . $subdirs . $row->product_sku . '/';

                $files = JFolder::files( $old_dir );
                foreach ( $files as $file )
                {
                    if ( !in_array( $file, $images ) )
                    {
                        if ( JFile::move( $old_dir . $file, $path . $file ) )
                        {
                            // create new thumb too
                            Citruscart::load( 'CitruscartImage', 'library.image' );
                            $img = new CitruscartImage( $path . $file);
                            $img->setDirectory( $path );
                            Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
                            $imgHelper = CitruscartHelperBase::getInstance( 'Image', 'CitruscartHelper' );
                            $imgHelper->resizeImage( $img );

                            // delete old thumb
                            JFile::delete( $old_dir . 'thumbs' . '/' . $file );

                            $file_moved = true;
                        }
                    }
                    else
                    {
                        // delete the old one?
                        if ( $delete_duplicates )
                        {
                            JFile::delete( $old_dir . $file );
                        }
                    }
                }
            }
        }

        // merge the ID-based dir if it exists and isn't the current path
        if ( $this->checkDirectory( $dir . '/' . $row->product_id, false ) && ( $dir . '/' . $row->product_id . '/' != $path ) )
        {
            $old_dir = $dir . '/' . $row->product_id . '/';

            $files = JFolder::files( $old_dir );
            foreach ( $files as $file )
            {
                if ( !in_array( $file, $images ) )
                {
                    if ( JFile::move( $old_dir . $file, $path . $file ) )
                    {
                        // create new thumb too
                        Citruscart::load( 'CitruscartImage', 'library.image' );
                        $img = new CitruscartImage( $path . $file);
                        $img->setDirectory( $path );
                        Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
                        $imgHelper = CitruscartHelperBase::getInstance( 'Image', 'CitruscartHelper' );
                        $imgHelper->resizeImage( $img );
                        // delete old thumb
                        JFile::delete( $old_dir . 'thumbs' . '/' . $file );

                        $file_moved = true;
                    }
                }
                else
                {
                    // delete the old one?
                    if ( $delete_duplicates )
                    {
                        JFile::delete( $old_dir . $file );
                    }
                }
            }
        }
        else
        {
            $subdirs = CitruscartHelperProduct::getSha1Subfolders( $row->product_id );
            // merge the ID-based SHA1 dir if it exists and isn't the current path
            if ( $this->checkDirectory( $dir . '/' . $subdirs . $row->product_id, false ) && ( $dir . '/' . $subdirs . $row->product_id . '/' != $path ) )
            {
                $old_dir = $dir . '/' . $subdirs . $row->product_id . '/';

                $files = JFolder::files( $old_dir );
                foreach ( $files as $file )
                {
                    if ( !in_array( $file, $images ) )
                    {
                        if ( JFile::move( $old_dir . $file, $path . $file ) )
                        {
                            // create new thumb too
                            Citruscart::load( 'CitruscartImage', 'library.image' );
                            $img = new CitruscartImage( $path . $file);
                            $img->setDirectory( $path );
                            Citruscart::load( 'CitruscartHelperImage', 'helpers.image' );
                            $imgHelper = CitruscartHelperBase::getInstance( 'Image', 'CitruscartHelper' );
                            $imgHelper->resizeImage( $img );
                            // delete old thumb
                            JFile::delete( $old_dir . 'thumbs/' . $file );

                            $file_moved = true;
                        }
                    }
                    else
                    {
                        // delete the old one?
                        if ( $delete_duplicates )
                        {
                            JFile::delete( $old_dir . $file );
                        }
                    }
                }
            }
        }

        return $file_moved;
    }

    /**
     *
     * Thanks to http://ryan.ifupdown.com/2008/08/17/warning-mkdir-too-many-links/
     * @param $string
     * @param $separator
     * @return unknown_type
     */
    function getSha1Subfolders( $string, $separator = DIRECTORY_SEPARATOR )
    {
        $sha1 = strtoupper( sha1( $string ) );

        // 4 level subfolding using sha1
        $i = 0;
        $subdirs = '';
        while ( $i < 4 )
        {
            if ( strlen( $string ) >= $i )
            {
                $subdirs .= $sha1[$i] . $separator;
            }
            $i++;
        }

        return $subdirs;
    }

    /**
     * Returns array of filenames
     * Array
     * (
     *     [0] => airmac.png
     *     [1] => airportdisk.png
     *     [2] => applepowerbook.png
     *     [3] => cdr.png
     *     [4] => cdrw.png
     *     [5] => cinemadisplay.png
     *     [6] => floppy.png
     *     [7] => macmini.png
     *     [8] => shirt1.jpg
     * )
     * @param $folder
     * @return array
     */
    public static function getGalleryImages( $folder = null, $options = array( ), $triggerEvent = true )
    {
    	$app = JFactory::getApplication();
        $images = array( );


        if ( empty( $folder ) )
        {
            return $images;
        }

        if ( empty( $options['exclude'] ) )
        {
            $options['exclude'] = array( );
        }
        elseif ( !is_array( $options['exclude'] ) )
        {
            $options['exclude'] = array(
                    $options['exclude']
            );
        }

        if ( JFolder::exists( $folder ) )
        {
            $extensions = array(
                    'png', 'gif', 'jpg', 'jpeg'
            );

            $files = JFolder::files( $folder );
            foreach ( $files as $file )
            {
                $namebits = explode( '.', $file );
                $extension = strtolower( $namebits[count( $namebits ) - 1] );
                if ( in_array( $extension, $extensions ) )
                {
                    if ( !in_array( $file, $options['exclude'] ) )
                    {
                        $images[] = $file;
                    }
                }
            }
        }

        if( $triggerEvent )
        {

            $app->triggerEvent( 'onPrepareGalleryImages', array( &$images ) );
        }

        return $images;
    }

    /**
     * Returns the full path to the product's image gallery files
     *
     * @param int $id
     * @return string
     */
    public static function getGalleryPath( $row )
    {

        static $paths;


        if( is_object( $row ) ) // passed CitruscartTable object
        {
            $id = $row->product_id;
            $paths[ $id ] = $row->getImagePath( true );

        }
        else
        {
            $id = ( int ) $row;

            if ( !is_array( $paths ) )
            {
                $paths = array( );
            }

            if ( empty( $paths[$id] ) )
            {
                $paths[$id] = '';

                if ( isset( $this ) && is_a( $this, 'CitruscartHelperProduct' ) )
                {
                    $helper = $this;
                }
                else
                {
                    $helper = new CitruscartHelperProduct();
                }
                $row = $helper->load( ( int ) $id, true, false );

                if ( empty( $row->product_id ) )
                {
                    // TODO figure out what to do if the id is invalid
                    return null;
                }

                $paths[$id] = $row->getImagePath( true );
            }
        }

        return $paths[$id];
    }

    /**
     * Returns the full path to the product's image gallery files
     *
     * @param int $id
     * @return string
     */
    function getGalleryUrl( $id )
    {
        static $urls;

        $id = ( int ) $id;

        if ( !is_array( $urls ) )
        {
            $urls = array( );
        }

        if ( empty( $urls[$id] ) )
        {
            $urls[$id] = '';

            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $row = JTable::getInstance( 'Products', 'CitruscartTable' );
            $row->load( ( int ) $id, true, false );
            if ( empty( $row->product_id ) )
            {
                // TODO figure out what to do if the id is invalid
                return null;
            }

            $urls[$id] = $row->getImageUrl( );
        }

        return $urls[$id];
    }

    /**
     * Returns the full path to the product's files
     *
     * @param int $id
     * @return string
     */
    function getFilePath( $id )
    {
        static $paths;

        $id = ( int ) $id;

        if ( !is_array( $paths ) )
        {
            $paths = array( );
        }

        if ( empty( $paths[$id] ) )
        {
            $paths[$id] = '';

            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $row = JTable::getInstance( 'Products', 'CitruscartTable' );
            $row->load( ( int ) $id, true, false );
            if ( empty( $row->product_id ) )
            {
                // TODO figure out what to do if the id is invalid
                return null;
            }

            // if product_images_path is valid and not empty, use it
            if ( !empty( $row->product_files_path ) )
            {
                $folder = $row->product_files_path;
                if ( JFolder::exists( $folder ) )
                {
                    $files = JFolder::files( $folder );
                    if ( !empty( $files ) )
                    {
                        $paths[$id] = $folder;
                    }
                }
            }

            // if no override, use path based on sku if it is valid and not empty
            // TODO clean SKU so valid characters used for folder name?
            if ( empty( $paths[$id] ) && !empty( $row->product_sku ) )
            {
                $folder = Citruscart::getPath( 'products_files' ) . '/sku/' . $row->product_sku;
                if ( JFolder::exists( $folder ) )
                {
                    $files = JFolder::files( $folder );
                    if ( !empty( $files ) )
                    {
                        $paths[$id] = $folder;
                    }
                }
            }

            // if still unset, use path based on id number
            if ( empty( $paths[$id] ) )
            {
                $folder = Citruscart::getPath( 'products_files' ) . '/id/' . $row->product_id;
                if ( !JFolder::exists( $folder ) )
                {
                    JFolder::create( $folder );
                }
                $paths[$id] = $folder;
            }
        }

        // TODO Make sure the files folder has htaccess file
        return $paths[$id];
    }

    /**
     *
     * @param $id
     * @param $by
     * @param $alt
     * @param $type
     * @param $url
     * @return unknown_type
     */
    public static function getImage( $id, $by = 'id', $alt = '', $type = 'thumb', $url = false, $resize = false, $options = array( ), $main_product = false )
    {
        $style = "";
        $height_style = "";
        $width_style = "";
        
        $dimensions = "";
        if ( !empty( $options['width'] ) )
        {
            $dimensions .= "width=\"" . $options['width'] . "\" ";
        }

        if ( !empty( $options['height'] ) )
        {
            $dimensions .= "height=\"" . $options['height'] . "\" ";
        }

        if ( !empty( $options['height'] ) )
        {
            $height_style = "max-height: " . $options['height'] . "px;";
        }
        if ( !empty( $options['width'] ) )
        {
            $width_style = "max-width: " . $options['width'] . "px;";
        }
        if ( !empty( $height_style ) || !empty( $width_style ) )
        {
            $style = "style='$height_style $width_style'";
        }
       
        switch ( $type )
        {
            case "full":
                $path = 'products_images';
                break;
            case "thumb":
            default:
                $path = 'products_thumbs';
                break;
        }
        
        

        $tmpl = "";
       
        if ( strpos( $id, '.' ) )
        {
            // then this is a filename, return the full img tag if file exists, otherwise use a default image
            $src = ( JFile::exists( Citruscart::getPath( $path ) . '/' . $id ) ) ? Citruscart::getUrl( $path ) . $id
            : JURI::root( true ) . '/media/citruscart/images/placeholder_239.gif';
           
            
            // if url is true, just return the url of the file and not the whole img tag
            $tmpl = ( $url ) ? $src
            : "<img " . $dimensions . " src='" . $src . "' alt='" . JText::_( $alt ) . "' title='" . JText::_( $alt )
            . "' align='middle' border='0' " . $style . " />";

        }
        else
        {
            if ( !empty( $id ) )
            {
                if ( isset( $this ) && is_a( $this, 'CitruscartHelperProduct' ) )
                {
                    $helper = $this;
                }
                else
                {
                    $helper = CitruscartHelperBase::getInstance( 'Product' );
                }
                
                $model = Citruscart::getClass('CitruscartModelProducts', 'models.products');
                $model->setId((int)$id);
                $item = $model->getItem();

                $full_image = !empty($item->product_full_image) ? $item->product_full_image : null;
                $thumb_image = !empty($item->product_thumb_image) ? $item->product_thumb_image : $full_image;
                                
                switch ( $type )
                {
                    case "full":
                        $image_ref = $full_image;
                        break;
                    case "thumb":
                    default:
                        $image_ref = $thumb_image;
                        break;
                }                

                if (filter_var($image_ref, FILTER_VALIDATE_URL) !== false) {
                    // $full_image contains a valid URL
                    $src = $image_ref;
                    if ($url) {
                        return $src;
                    } elseif (!empty($src)) {
                        $tmpl = "<img src='".$src."' alt='".JText::_( $alt )."' title='".JText::_( $alt )."' />";
                        return $tmpl;
                    }
                }

                $row = $helper->load( ( int ) $id, true, false );

                // load the item, get the filename, create tmpl
                $urli = $row->getImageUrl( );
                $dir = $row->getImagePath( );
                                
                if ( $path == 'products_thumbs' )
                {
                    $dir .= 'thumbs';
                    $urli .= 'thumbs/';
                }

                
                if( $main_product )
                {
                    JFactory::getApplication()->triggerEvent('onGetProductMainImage', array( $row->product_id, &$full_image, $options ) );
                }

                $dirname = dirname($image_ref);
                
                
                if (!empty($dirname) && $dirname !== ".")
                {
                    $dir = JPath::clean( JPATH_SITE . "/" . dirname($image_ref) );
                    $urli = JURI::root(true) . '/' . dirname($image_ref) . '/';
                    $file = JPath::clean( JPATH_SITE . "/" . $image_ref );
                    $id = JURI::root(true) . '/' . $image_ref;
                }
                else
                {
                    $file = $dir . '/' . $image_ref;
                    $id = $urli . $image_ref;
                }                  
               
                // Gotta do some resizing first?
                if ( $resize )
                {
                    // Add a suffix to the thumb to avoid conflicts with user settings
                    $suffix = '';
                    
                    if ( isset( $options['width'] ) && isset( $options['height'] ) )
                    {
                    	
                        $suffix = '_' . $options['width'] . 'x' . $options['height'];
                    }
                    elseif ( isset( $options['width'] ) )
                    {
                        $suffix = '_w' . $options['width'];
                    }
                    elseif ( isset( $options['height'] ) )
                    {
                        $suffix = '_h' . $options['height'];
                    }

                    // Add suffix to file path
                    $dot = strrpos( $file, '.' );
                    $resize = substr( $file, 0, $dot ) . $suffix . substr( $file, $dot );
                  
                    if ( !JFile::exists( $resize ) )
                    {

                        Citruscart::load( 'CitruscartImage', 'library.image' );
                        $image = new CitruscartImage( $file);
                        $image->load( );
                        // If both width and height set, gotta figure hwo to resize
                        if ( isset( $options['width'] ) && isset( $options['height'] ) )
                        {
                            // If width is larger, proportionally
                            if ( ( $options['width'] / $image->getWidth( ) ) < ( $options['height'] / $image->getHeight( ) ) )
                            {
                            	
                                $image->resizeToWidth( $options['width'] );
                                $image->save( $resize );
                            }
                            // If height is larger, proportionally
                            else
                            {
                                $image->resizeToHeight( $options['height'] );
                                $image->save( $resize );
                            }
                        }
                        // If only width is set
                        elseif ( isset( $options['width'] ) )
                        {                        	
                            $image->resizeToWidth( $options['width'] );
                            $image->save( $resize );
                        }
                        // If only height is set
                        elseif ( isset( $options['height'] ) )
                        {
                            $image->resizeToHeight( $options['height'] );
                            $image->save( $resize );
                        }

                    }

                    // Add suffix to url path
                    $dot = strrpos( $id, '.' );
                    $id = substr( $id, 0, $dot ) . $suffix . substr( $id, $dot );
                }
                                
                $src = ( JFile::exists( $file ) ) ? $id : JURI::root(true).'/media/citruscart/images/placeholder_239.gif';
                      
                $tmpl = ( $url ) ? $src
                : "<img " . $dimensions . " src='" . $src . "' alt='" . JText::_( $alt ) . "' title='" . JText::_( $alt )
                . "' align='middle' border='0' width='220px' height='180px'/>";
                                
            }
        }
        return $tmpl;
    }

    /**
     * Gets a product's list of prices
     *
     * @param $id
     * @return array
     */
    public static function getPrices( $id )
    {
        static $sets;

        if( !$id )
        {
            return array();
        }

        if ( empty( $sets ) || !is_array( $sets ) )
        {
            $sets = array( );
        }

        if ( empty( $sets[$id] ) )
        {
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'ProductPrices', 'CitruscartModel' );
            $model->setState( 'filter_id', $id );
			$model->setState('order', 'g.ordering');
			$model->setState( 'direction', 'ASC' );

            $sets[$id] = $model->getList( );
        }

        return $sets[$id];
    }

    /**
     * Returns a product's price based on the quantity purchased, user's group, and date
     *
     * @param int $id
     * @param unknown_type $quantity
     * @param unknown_type $group_id
     * @param unknown_type $date
     * @return unknown_type
     */
    static public function getPrice( $id, $quantity = '1', $group_id = '', $date = '' )
    {
        // $sets[$id][$quantity][$group_id][$date]
        static $sets;

        if ( !is_array( $sets ) )
        {
            $sets = array( );
        }

        $price = null;
        if ( empty( $id ) )
        {
            return $price;
        }

        if ( !isset( $sets[$id][$quantity][$group_id][$date] ) )
        {
            $product_helper = CitruscartHelperBase::getInstance( 'Product' );
            $prices = $product_helper->getPrices( $id );

            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'ProductPrices', 'CitruscartModel' );
            $model->setState( 'filter_id', $id );

            ( int ) $quantity;
            if ( $quantity <= '0' )
            {
                $quantity = '1';
            }
            //where price_quantity_start < $quantity
            $model->setState( 'filter_quantity', $quantity );

            // does date even matter?
            $nullDate = JFactory::getDBO( )->getNullDate( );
            if ( empty( $date ) || $date == $nullDate )
            {
                $date = JFactory::getDate( )->toSql( );
            }
            $model->setState( 'filter_date', $date );
            //where product_price_startdate <= $date
            //where product_price_enddate >= $date OR product_price_enddate == nullDate

            // does group_id?
            ( int ) $group_id;
            $default_user_group = Citruscart::getInstance( )->get( 'default_user_group', '1' ); /* Use a default $group_id */
            if ( $group_id <= '0' )
            {
                $group_id = $default_user_group;
            }
            // using ->getPrices(), do a getColumn() on the array for the group_id column
            $group_ids = CitruscartHelperBase::getColumn( $prices, 'group_id' );
            if ( !in_array( $group_id, $group_ids ) )
            {
                // if $group_id is in the column, then set the query to pull an exact match on it,
                // otherwise, $group_id_determined = the default $group_id
                $group_id = $default_user_group;
            }
            $model->setState( 'filter_user_group', $group_id );

            // set the ordering so the most discounted item is at the top of the list
            $model->setState( 'order', 'price_quantity_start' );
            $model->setState( 'direction', 'DESC' );

            // CitruscartModelProductPrices is a special model that overrides getItem
            $price = $model->getItem( );
            $sets[$id][$quantity][$group_id][$date] = $price;
        }

        return $sets[$id][$quantity][$group_id][$date];
    }

    /**
     *
     * Gets the tax total for a product based on an array of geozones
     * @param $product_id
     * @param $geozones
     * @return object
     */
    public static function getTaxTotal( $product_id, $geozones, $price = null )
    {
        $product_price = 0;
        if( $price )
            $product_price = $price;
        else
        {
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
            Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
            $user_id = JFactory::getUser( )->id;
            $filter_group = CitruscartHelperUser::getUserGroup( $user_id, $product_id );
            $model->setState( 'filter_group', $filter_group );
            $model->setId( $product_id );
            $row = $model->getItem( false, false );
            $product_price = $row->price;
        }

        $zones = array();
        foreach( $geozones as $geozone )
            $zones []= $geozone->geozone_id;

        $orderitem_tax = 0;
        $tax_rates = array( );
        $tax_amounts = array( );
        Citruscart::load('CitruscartHelperTax', 'helpers.tax' );
        $product = new stdClass();
        $product->product_id = $product_id;
        $product->product_price = $product_price;
        $taxrates = CitruscartHelperTax::calculateGeozonesTax( array( $product ), 2, $zones );

        foreach( $taxrates->tax_rate_rates as $rate )
        {
            $tax_rates[$rate->tax_rate_id] = $rate;
            $tax_amounts[$rate->tax_rate_id] = $rate->applied_tax;
        }

        $return = new stdClass( );
        $return->tax_rates = $tax_rates;
        $return->tax_amounts = $tax_amounts;
        $return->tax_total = $taxrates->tax_total;
        return $return;
    }

    /**
     * Returns the tax rate for an item
     *
     * @param int $product_id
     * @param int $geozone_id
     * @param boolean $return_object
     * @return float | object if $return_object=true
     */
    public function getTaxRate( $product_id, $geozone_id, $return_object = false )
    {
        // $sets[$product_id][$geozone_id] == object
        static $sets;
        if ( !is_array( $sets ) )
        {
            $sets = array( );
        }

        if ( !isset( $sets[$product_id][$geozone_id] ) )
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $taxrate = JTable::getInstance( 'TaxRates', 'CitruscartTable' );
            $taxrate->tax_rate = "0.00000";

            Citruscart::load( 'CitruscartQuery', 'library.query' );

            $db = JFactory::getDBO( );

            $query = new CitruscartQuery( );
            $query->select( 'tbl.*' );
            $query->from( '#__citruscart_taxrates AS tbl' );
            $query->join( 'LEFT', '#__citruscart_products AS product ON product.tax_class_id = tbl.tax_class_id' );
            $query->where( "product.product_id = '" . $product_id . "'" );
            $query->where( "tbl.geozone_id = '" . $geozone_id . "'" );

            $db->setQuery( ( string ) $query );
            if ( $data = $db->loadObject( ) )
            {
                $taxrate->load( array(
                        'tax_rate_id' => $data->tax_rate_id
                ) );
            }
            $sets[$product_id][$geozone_id] = $taxrate;
        }
        if ( !$return_object )
        {
            return $sets[$product_id][$geozone_id]->tax_rate;
        }
        return $sets[$product_id][$geozone_id];
    }

    /**
     * Gets a product's list of categories
     *
     * @param $id
     * @return array
     */
    public static function getCategories( $id )
    {

        if ( isset( $this ) && is_a( $this, 'CitruscartHelperProduct' ) )
        {
            $helper = $this;
        }
        else
        {
            $helper = CitruscartHelperBase::getInstance( 'Product' );
        }

        if ( empty( self::$categoriesxref[$id] ) )
        {
            Citruscart::load( 'CitruscartQuery', 'library.query' );
            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $table = JTable::getInstance( 'ProductCategories', 'CitruscartTable' );


            $db = JFactory::getDBO( );
            $query = $db->getQuery(true);
            $query->select("tbl.category_id");
			$query->from( $table->getTableName( ) . " AS tbl" );
			$query->where( "tbl.product_id = " . ( int ) $id );
			$db->setQuery($query);
			$row = $db->loadObjectList();
			self::$categoriesxref[$id] =  $db->loadColumn();
            /*$query = new CitruscartQuery( );
            $query->select( "tbl.category_id" );
            $query->from( $table->getTableName( ) . " AS tbl" );
            $query->where( "tbl.product_id = " . ( int ) $id );
            $db = JFactory::getDBO( );
            $db->setQuery( ( string ) $query ); */
           // self::$categoriesxref[$id] = $db->loadColumn( );
        }


        return self::$categoriesxref[$id];
    }

    /**
     * Returns a list of a product's attributes
     *
     * @param int      $id
     * @param int      $parent_option the id of the parent option. Use -1 for "all"
     * @return unknown_type
     */
    public static function getAttributes( $id, $parent_option = "-1" )
    {
        if ( empty( $id ) )
        {
            return array( );
        }
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductAttributes', 'CitruscartModel' );
        $model->setState( 'filter_product', $id );

        if ( $parent_option != '-1' )
        {
            $model->setState( 'filter_parent_option', $parent_option );
        }
        $model->setState( 'order', 'tbl.ordering' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList( );
        return $items;
    }

	/*
	 * Gets product attributes quantity map that is used to select
	 * only combinations of product attribute which are available in stock
	 *
	 * @param	$id				product_id
	 * @param	$parent_options	Parent options
	 * @param	$refresh		Refreshed pre-cached data
	 *
	 */
	public static function getAttributeQuantityMap( $id, $parent_options = "-1", $refresh = false )
	{
		static $map = null;
		if( $map == null ) {
			$map = array();
		}
		$po = $parent_options;
		if( is_array( $parent_options ) ) {
			$po = implode(',', $po );
		}
		if( isset( $map[$id][$po] ) && !$refresh ) {
			return $map[$id][$po];
		}

		$pao = array();
		$pq_list = array();
		$pa_list = array();
		$orders = array();
		$a_count = 0;

		$pao_count = 0;\
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		// first, we check, if we set up quantities
		$m_quantity = JModelLegacy::getInstance( 'ProductQuantities', 'CitruscartModel' );
		$m_quantity->setState( 'filter_productid', $id );
		$m_quantity->setState( 'filter_quantity_from', 1);
        $m_quantity->setState( 'order', 'tbl.product_attributes' );
        $m_quantity->setState( 'direction', 'ASC' );
		$res = $m_quantity->getList();

		if( count( $res ) ) {
			$pa = CitruscartHelperProduct::getAttributes($id, $parent_options);

			// get product attribute options of every attribute
			foreach( $pa as $a ) {
				$orders[ $a->productattribute_id ] = $a_count++; // save order of attributes
				$m_pao = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
				$m_pao->setState( 'filter_attribute', $a->productattribute_id );
		        $m_pao->setState( 'order', 'tbl.ordering' );
		        $m_pao->setState( 'direction', 'ASC' );
				$tmp = $m_pao->getList();

				for( $i = 0, $c = count( $tmp ); $i < $c; $i++ ) {
					$item = $tmp[$i];
					$pao[$item->productattributeoption_id] = $item;
				}
				$pa_list[ $a->productattribute_id ] = $tmp;
			}
			if( empty( $pao ) ) {
				$map[$id][$po] = $pao;
				return $map[$id][$po];
			}

			// now, create map from product quantities
			foreach ( $res as $item ) {
				// Information about product quantity for a certain combination of options needs to be polished.
				// Order of options in CSV format does usually not  match order of product attributes (which is important to keep)

				// first, I get list of option ids (unordered)
				$q_pao = explode( ',', $item->product_attributes );
				// then i need to transform it into list of attribute ids having those optiobs
				$tmp_q = array();
				for( $i = 0, $c = count( $q_pao ); $i < $c; $i++ ) {
					$a_id = $pao[$q_pao[$i]]->productattribute_id;
					$tmp_q[$orders[$a_id]] = $q_pao[$i];
				}
				$pq_list []= $tmp_q;
			}
		} else {
			// no quantities so we do it the old way
			return false;
		}
		// q => list of available combinations
		// pao => list of list of all product attribute options (index is PAO ID)
		// ap => list of all product attributes with their corresponding options (index is product attribute ID)
		// ord => order of attributes in list of available ombinations
		$map[$id][$po] = array( 'q' => $pq_list, 'pao' => $pao, 'pa' => $pa_list, 'ord' => $orders );

		return $map[$id][$po];
	}

	/*
	 * Gets list of available options for the selected product and product attrbute when specified options were selected
	 *
	 * @param $product_id		Product
	 * @param $aid				Attribute id
	 * @param $parent_options	Selected options
	 * @param $refresh			Refresh pre-cached data from database
	 *
	 * @return	Array with product attribute option objects
	 */
	public static function getAvailableAttributeOptions( $product_id, $aid, $fixed_aid, $fixed_pao, $parent_options = "-1", $refresh = false )
	{
		$map = CitruscartHelperProduct::getAttributeQuantityMap( $product_id, $parent_options, $refresh );
		if( is_array( $map ) ) { // product quantities are set up
			$pos = $map['ord'][$aid]; // which position we are going to examine
			$pos_fixed = -1; // none is fixed
			if( $fixed_aid > -1 ) {
				$pos_fixed = $map['ord'][$fixed_aid]; // which position is fixed (was recently changed)
			}
			$pa_list = $map['pa'][$aid]; // list of available options at the moment
			$q = $map['q']; // quantities
			$cq = count( $q );

			$final_list = array(); // final list of options that are available
			$identical = $pos == $pos_fixed; // options for recently changed attribute
			for( $i = 0, $c = count( $pa_list ); $i < $c; $i++ ) {
				$pao_id = $pa_list[$i]->productattributeoption_id;
				$found = false;
				if( $identical || $pos_fixed == -1) {
					// identical means that we need to make sure that for the examined option exists a combination that is available in the stock
					for( $j = 0; !$found && $j < $cq; $j++ ) {
						if( $q[$j][$pos] == $pao_id )  {
							$found = true;
						}
					}
				} else {
					// otherwise we need to find a combination which contains both this option and the recently changed option
					for( $j = 0; !$found && $j < $cq; $j++ ) {
						if( $q[$j][$pos] == $pao_id && $q[$j][$pos_fixed] == $fixed_pao)  {
							$found = true;
						}
					}
				}

				if( $found ) {
					$final_list []= $pa_list[$i];
				}
			}

			return $final_list;
		} else if( $map == false ) {
			// no product quantities
	        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
	        $model = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
	        $model->setState( 'filter_attribute', $aid );
	        $model->setState('order', 'tbl.ordering');

	        // Parent options
	        if(count($parent_options))
	        {
	        	$model->setState('filter_parent', $parent_options);
	        }

	        return $model->getList();
		}
	}

	/*
	 * Gets default options for specified attributes
	 *
	 * @param $attributes	Array with attributes
	 *
	 * @return	Array with options
	 */
	public static function getDefaultAttributeOptions( $attributes )
	{
		if( is_array( $attributes ) == false || empty( $attributes ) ) {
			return array();
		}

		$pid = $attributes[0]->product_id;
		$map = CitruscartHelperProduct::getAttributeQuantityMap($pid);

        $default = array( );
        foreach ( $attributes as $attribute ) {
        	$default[ $attribute->productattribute_id ] = 0;
        }

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		if( is_array( $map ) ) {
			// we figure out what should be the default attribute options combination by recursively searching the product quantity map
			$answer = array(); // array with final combination
			$not_finished = true;
			$act_pos = -1; // actual attribute
			$attr_order = array(); // order of attribute IDs
			$cq = count( $map['q']);
			$prev = array();
			foreach( $map['pa'] as $key => $value ) {
				$attr_order[$map['ord'][$key]] = $key;
			}
			$c_answers = count( $attr_order ); // number of elements that should be in the result


			do
			{
				$ca = count( $answer ); // order of attribution combination
				$attr_id = $attr_order[ $ca ];
				$attribs = $map['pa'][$attr_id];
				$c_attribs = count( $attribs );
				$pos = $map['ord'][$attr_id];
				$found = false; // found option for this attribute
				for($i = 0; !$found && $i < $c_attribs; $i++) {
					// first we find combination with this
					$pao_id = $attribs[$i]->productattributeoption_id;
					for( $j = 0; !$found && $j < $cq; $j++ ) {
						if( $map['q'][$j][$pos] == $pao_id ) {
							// found a combination with this option -> now we check it
							$wrong = false;
							for( $k = 0; !$wrong && $k < $ca; $k++ ) {
								$ord_a = $map['ord'][$attr_order[$k]];
								$wrong = $map['q'][$j][$ord_a] == $answer[$attr_order[$k]];
							}
							if( !$wrong ) {
								$found = true;
								$answer [$attr_id] = $pao_id;
							}
						}
					}
				}

				if( $i == $c_attribs || $c_answers == count($answer) ) {
					$not_finished = false;
				}
			}
			while( $not_finished );
			foreach( $answer as $key => $val ) {
				$default[$key] = $val;
			}
			return $default;
		} else { // no product quantities -> use the old way
	        foreach ( $attributes as $attribute )
	        {
	            $model = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
	            $model->setState( 'filter_attribute', $attribute->productattribute_id );
	            $model->setState( 'order', 'tbl.ordering' );

	            $items = $model->getList( );

	            if ( count( $items ) )
	            {
	                $default[$attribute->productattribute_id] = $items[0]->productattributeoption_id;
	            }
	        }
		}

		return $default;
	}

    /**
     * Returns a default list of a product's attributes
     *
     * @param int $id
     * @return array
     */
    function getDefaultAttributes( $id )
    {
        static $sets;

        if ( empty( $sets ) || !is_array( $sets ) )
        {
            $sets = array( );
        }

        if ( empty( $sets[$id] ) )
        {
            $list = array( );
            JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
			// first, we check, if we set up quantities
			$m_quantity = JModelLegacy::getInstance( 'ProductQuantities', 'CitruscartModel' );
			$m_quantity->setState( 'filter_productid', $id );
			$m_quantity->setState( 'filter_quantity_from', 1);
            $m_quantity->setState( 'order', 'tbl.product_attributes' );
            $m_quantity->setState( 'direction', 'ASC' );
			$res = $m_quantity->getList();
			if( count( $res ) && strlen( $res[0]->product_attributes ) ) {
				// combination of product attributes with quantity in stock being higher than 0 exists
				$opts = explode( ',', $res[0]->product_attributes );
				for( $i = 0, $c = count( $opts ); $i < $c; $i++ ) {
	                $m_pao = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
	                $m_pao->setId( $opts[$i] );
	                $attr = $m_pao->getItem();

					if( $attr ) {
		                $key = 'attribute_' . $attr->productattribute_id;
						$list[$key] = $opts[$i];
					}
				}
			} else { // no quantities were set so we need to dig up options manually
	            $model = JModelLegacy::getInstance( 'ProductAttributes', 'CitruscartModel' );
	            $model->setState( 'filter_product', $id );
	            $model->setState( 'order', 'tbl.ordering' );
	            $model->setState( 'direction', 'ASC' );
	            $items = $model->getList( );
	            if ( empty( $items ) )
	            {
	                $sets[$id] = array( );
	                return $sets[$id];
	            }
	            foreach ( $items as $item )
	            {
	                $key = 'attribute_' . $item->productattribute_id;
	                $model = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
	                $model->setState( 'filter_attribute', $item->productattribute_id );
	                $model->setState( 'order', 'tbl.ordering' );
	                $model->setState( 'direction', 'ASC' );
	                $options = $model->getList( );
	                if ( !empty( $options ) )
	                {
	                    $option = $options[0];
	                    $list[$key] = $option->productattributeoption_id;
	                }
	            }
			}
            $sets[$id] = $list;
        }

        return $sets[$id];
    }

    /**
     * Returns a list of a product's files
     *
     * @param unknown_type $id
     * @return unknown_type
     */
    function getFiles( $id )
    {
        if ( empty( $id ) )
        {
            return array( );
        }
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductFiles', 'CitruscartModel' );
        $model->setState( 'filter_product', $id );
        $items = $model->getList( );
        return $items;
    }

    /**
     * Returns array of filenames
     * Array
     * (
     *     [0] => airmac.png
     *     [1] => airportdisk.png
     *     [2] => applepowerbook.png
     *     [3] => cdr.png
     *     [4] => cdrw.png
     *     [5] => cinemadisplay.png
     *     [6] => floppy.png
     *     [7] => macmini.png
     *     [8] => shirt1.jpg
     * )
     * @param $folder
     * @return array
     */
    function getServerFiles( $folder = null, $options = array( ) )
    {
        $files = array( );

        if ( empty( $folder ) )
        {
            return $files;
        }

        if ( empty( $options['exclude'] ) )
        {
            $options['exclude'] = array( );
        }
        elseif ( !is_array( $options['exclude'] ) )
        {
            $options['exclude'] = array(
                    $options['exclude']
            );
        }

        // Add .htaccess exclusion
        if ( !in_array( '.htaccess', $options['exclude'] ) ) $options['exclude'][] = '.htaccess';

        if ( JFolder::exists( $folder ) )
        {
            $serverfiles = JFolder::files( $folder );
            foreach ( $serverfiles as $file )
            {
                if ( !in_array( $file, $options['exclude'] ) )
                {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    /**
     * Finds the prev & next items in the list
     *
     * @param $id   product id
     * @return array( 'prev', 'next' )
     */
    function getSurrounding( $id )
    {
        $return = array( );

        /* Get the application */
        $app = JFactory::getApplication();

        $prev = $app->input->getInt("prev");

        $next = $app->input->getInt("next");

        //$prev = intval( JRequest::getVar( "prev" ) );
        //$next = intval( JRequest::getVar( "next" ) );

        if ( $prev || $next )
        {
            $return["prev"] = $prev;
            $return["next"] = $next;
            return $return;
        }

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
        $ns = $app->getName( ) . '::' . 'com.citruscart.model.' . $model->getTable( )->get( '_suffix' );
        $state = array( );

        $config = Citruscart::getInstance( );

        $state['limit'] = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg( 'list_limit' ), 'int' );
        $state['limitstart'] = $app->getUserStateFromRequest( $ns . 'limitstart', 'limitstart', 0, 'int' );
        $state['filter'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
        $state['order'] = $app->getUserStateFromRequest( $ns . '.filter_order', 'filter_order', 'tbl.' . $model->getTable( )->getKeyName( ), 'cmd' );
        $state['direction'] = $app->getUserStateFromRequest( $ns . '.filter_direction', 'filter_direction', 'ASC', 'word' );

        $state['filter_id_from'] = $app->getUserStateFromRequest( $ns . 'id_from', 'filter_id_from', '', '' );
        $state['filter_id_to'] = $app->getUserStateFromRequest( $ns . 'id_to', 'filter_id_to', '', '' );
        $state['filter_name'] = $app->getUserStateFromRequest( $ns . 'name', 'filter_name', '', '' );
        $state['filter_enabled'] = $app->getUserStateFromRequest( $ns . 'enabled', 'filter_enabled', '', '' );
        $state['filter_quantity_from'] = $app->getUserStateFromRequest( $ns . 'quantity_from', 'filter_quantity_from', '', '' );
        $state['filter_quantity_to'] = $app->getUserStateFromRequest( $ns . 'quantity_to', 'filter_quantity_to', '', '' );
        $state['filter_category'] = $app->getUserStateFromRequest( $ns . 'category', 'filter_category', '', '' );
        $state['filter_sku'] = $app->getUserStateFromRequest( $ns . 'sku', 'filter_sku', '', '' );
        $state['filter_price_from'] = $app->getUserStateFromRequest( $ns . 'price_from', 'filter_price_from', '', '' );
        $state['filter_price_to'] = $app->getUserStateFromRequest( $ns . 'price_to', 'filter_price_to', '', '' );
        $state['filter_taxclass'] = $app->getUserStateFromRequest( $ns . 'taxclass', 'filter_taxclass', '', '' );
        $state['filter_ships'] = $app->getUserStateFromRequest( $ns . 'ships', 'filter_ships', '', '' );

        foreach ( $state as $key => $value )
        {
            $model->setState( $key, $value );
        }
        $rowset = $model->getList( false, false );

        $found = false;
        $prev_id = '';
        $next_id = '';

        for ( $i = 0; $i < count( $rowset ) && empty( $found ); $i++ )
        {
            $row = $rowset[$i];
            if ( $row->product_id == $id )
            {
                $found = true;
                $prev_num = $i - 1;
                $next_num = $i + 1;
                if ( isset( $rowset[$prev_num]->product_id ) )
                {
                    $prev_id = $rowset[$prev_num]->product_id;
                }
                if ( isset( $rowset[$next_num]->product_id ) )
                {
                    $next_id = $rowset[$next_num]->product_id;
                }

            }
        }

        $return["prev"] = $prev_id;
        $return["next"] = $next_id;
        return $return;
    }

    /**
     * Given a multi-dimensional array,
     * this will find all possible combinations of the array's elements
     *
     * Given:
     *
     * $traits = array
     * (
     *   array('Happy', 'Sad', 'Angry', 'Hopeful'),
     *   array('Outgoing', 'Introverted'),
     *   array('Tall', 'Short', 'Medium'),
     *   array('Handsome', 'Plain', 'Ugly')
     * );
     *
     * Returns:
     *
     * Array
     * (
     *      [0] => Happy,Outgoing,Tall,Handsome
     *      [1] => Happy,Outgoing,Tall,Plain
     *      [2] => Happy,Outgoing,Tall,Ugly
     *      [3] => Happy,Outgoing,Short,Handsome
     *      [4] => Happy,Outgoing,Short,Plain
     *      [5] => Happy,Outgoing,Short,Ugly
     *      etc
     * )
     *
     * @param string $string   The result string
     * @param array $traits    The multi-dimensional array of values
     * @param int $i           The current level
     * @param array $return    The final results stored here
     * @return array           An Array of CSVs
     */
    static function getCombinations( $string, $traits, $i, &$return )
    {
        if ( $i >= count( $traits ) )
        {
            $return[] = str_replace( ' ', ',', trim( $string ) );
        }
        else
        {
            foreach ( $traits[$i] as $trait )
            {
                CitruscartHelperProduct::getCombinations( "$string $trait", $traits, $i + 1, $return );
            }
        }
    }

    /**
     * Will return all the CSV combinations possible from a product's attribute options
     *
     * @param unknown_type $product_id
     * @param $attributeOptionId
     * @return unknown_type
     */
    static function getProductAttributeCSVs( $product_id, $attributeOptionId = '0' )
    {
        $return = array( );
        $traits = array( );

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );

        // get all productattributes
        $model = JModelLegacy::getInstance( 'ProductAttributes', 'CitruscartModel' );

        $model->setState( 'filter_product', $product_id );

        if ( $attributes = $model->getList( ) )
        {
            foreach ( $attributes as $attribute )
            {
                $paoModel = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
                $paoModel->setState( 'filter_attribute', $attribute->productattribute_id );
                if ( $paos = $paoModel->getList( ) )
                {
                    $options = array( );
                    foreach ( $paos as $pao )
                    {
                        // Genrate the arrray of single value with the id of newly created attribute option
                        if ( $attributeOptionId == $pao->productattributeoption_id )
                        {
                            $newOption = array( );
                            $newOption[] = ( string ) $attributeOptionId;
                            $options = $newOption;
                            break;
                        }

                        $options[] = $pao->productattributeoption_id;

                    }
                    $traits[] = $options;
                }
            }
        }
        // run recursive function on the data
        CitruscartHelperProduct::getCombinations( "", $traits, 0, $return );

        // before returning them, loop through each record and sort them
        $result = array( );
        foreach ( $return as $csv )
        {
            $values = explode( ',', $csv );
            sort( $values );
            $result[] = implode( ',', $values );
        }

        return $result;
    }

    /**
     * Given a product_id and vendor_id
     * will perform a full CSV reconciliation of the _productquantities table
     *
     * @param $product_id
     * @param $vendor_id
     * @param $attributeOptionId
     * @return unknown_type
     */
    static function doProductQuantitiesReconciliation( $product_id, $vendor_id = '0', $attributeOptionId = '0' )
    {

        if ( empty( $product_id ) )
        {
            return false;
        }

        $csvs = CitruscartHelperProduct::getProductAttributeCSVs( $product_id, $attributeOptionId );

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );

        $model = JModelLegacy::getInstance( 'ProductQuantities', 'CitruscartModel' );
        $model->setState( 'filter_productid', $product_id );
        $model->setState( 'filter_vendorid', $vendor_id );
        $items = $model->getList( );

        $results = CitruscartHelperProduct::reconcileProductAttributeCSVs( $product_id, $vendor_id, $items, $csvs );
    }

    /**
     * Adds any necessary _productsquantities records
     *
     * @param unknown_type $product_id     Product ID
     * @param unknown_type $vendor_id      Vendor ID
     * @param array $items                 Array of productQuantities objects
     * @param unknown_type $csvs           CSV output from getProductAttributeCSVs
     * @return array $items                Array of objects
     */
    static function reconcileProductAttributeCSVs( $product_id, $vendor_id, $items, $csvs )
    {
        // remove extras
        $done = array( );
        foreach ( $items as $key => $item )
        {
            if ( !in_array( $item->product_attributes, $csvs ) || in_array( $item->product_attributes, $done ) )
            {
                $row = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
                if ( !$row->delete( $item->productquantity_id ) )
                {
                    JError::raiseNotice( '1', $row->getError( ) );
                }
                unset( $items[$key] );
            }
            $done[] = $item->product_attributes;
        }

        // add new ones
        $existingEntries = CitruscartHelperBase::getColumn( $items, 'product_attributes' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        foreach ( $csvs as $csv )
        {
            if ( !in_array( $csv, $existingEntries ) )
            {
                $row = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
                $row->product_id = $product_id;
                $row->vendor_id = $vendor_id;
                $row->product_attributes = $csv;
                if ( !$row->save( ) )
                {
                    JError::raiseNotice( '1', $row->getError( ) );
                }
                $items[] = $row;
            }
        }

        return $items;
    }

    /**
     * Gets whether a product requires shipping or not
     *
     * @param $id
     * @return boolean
     */
    function isShippingEnabled( $id )
    {
        static $sets;

        if ( empty( $sets ) || !is_array( $sets ) )
        {
            $sets = array( );
        }

        if ( empty( $sets[$id] ) )
        {
            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
            $table = JTable::getInstance( 'Products', 'CitruscartTable' );
            $table->load( ( int ) $id, true, false );
            $sets[$id] = $table;
        }

        if ( $sets[$id]->product_ships )
        {
            return true;
        }

        return false;
    }

    /**
     * Checks if the specified relationship exists
     * TODO Make this support $product_to='any'
     * TODO Make this support $relation_type='any'
     *
     * @param $product_from
     * @param $product_to
     * @param $relation_type
     * @return unknown_type
     */
    function relationshipExists( $product_from, $product_to, $relation_type = 'relates' )
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        $table = JTable::getInstance( 'ProductRelations', 'CitruscartTable' );
        $keys = array(
                'product_id_from' => $product_from, 'product_id_to' => $product_to, 'relation_type' => $relation_type,
        );
        $table->load( $keys );
        if ( !empty( $table->product_id_from ) )
        {
            return true;
        }

        // relates can be inverted
        if ( $relation_type == 'relates' )
        {
            // so try the inverse
            $table = JTable::getInstance( 'ProductRelations', 'CitruscartTable' );
            $keys = array(
                    'product_id_from' => $product_to, 'product_id_to' => $product_from, 'relation_type' => $relation_type,
            );
            $table->load( $keys );
            if ( !empty( $table->product_id_from ) )
            {
                return true;
            }
        }

        return false;

    }

    /**
     * returns a product's quantity list for all combination
     * @return array with CSV and quantity;
     */
    public function getProductQuantities( $id )
    {
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );

        $tableQuantity = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
        $query = new CitruscartQuery( );

        $select[] = "quantities.product_attributes";
        $select[] = "quantities.quantity";

        $query->select( $select );
        $query->from( $tableQuantity->getTableName( ) . " AS quantities" );
        $query->where( "quantities.product_id = " . $id );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );

        $results = $db->loadObjectList( );
        $inventoryList = array( );

        foreach ( $results as $result )
        {
            $inventoryList[$result->product_attributes] = $result->quantity;
        }

        return $inventoryList;
    }

    /**
     * return the total quantity and Product name of product on the basis of the attribute
     *
     * @param $id
     * @param $attribute  CSV of attribute properties, in numeric order
     * @return an array with the name and the quantity of Product;
     */
    function getAvailableQuantity( $id, $attribute )
    {
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );

        $tableQuantity = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
        $tableProduct = JTable::getInstance( 'Products', 'CitruscartTable' );

        $tableProduct->load( $id, true, false );
        if ( empty( $tableProduct->product_check_inventory ) )
        {
            $tableProduct->quantity = '9999';
            return $tableProduct;
        }

        $query = new CitruscartQuery( );

        $select[] = "product.product_name";
        $select[] = "quantities.quantity";
        $select[] = "product.product_check_inventory";

        $query->select( $select );
        $query->from( $tableProduct->getTableName( ) . " AS product" );

        $leftJoinCondition = $tableQuantity->getTableName( ) . " as quantities ON product.product_id = quantities.product_id ";
        $query->leftJoin( $leftJoinCondition );

        $whereClause[] = "quantities.product_id = " . ( int ) $id;
        $whereClause[] = "quantities.product_attributes='" . $attribute . "'";
        $whereClause[] = "product.product_check_inventory =1 ";
        $query->where( $whereClause, "AND" );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );
        $item = $db->loadObject( );

        if ( empty( $item ) )
        {
            $return = new JObject( );
            $return->product_id = $id;
            $return->product_name = $tableProduct->product_name;
            $return->quantity = 0;
            $return->product_check_inventory = $tableProduct->product_check_inventory;
            return $return;
        }

        return $item;

    }
    /**
     * Checks orderitem table for provided product id
     * @param $product_id
     *
     */

    public static function getOrders( $product_id )
    {
        //Check the registry to see if our Citruscart class has been overridden
        if ( !class_exists( 'Citruscart' ) ) JLoader::register( "Citruscart",
                JPATH_ADMINISTRATOR . "/components/com_citruscart/defines.php" );
        // load the config class
        Citruscart::load( 'Citruscart', 'defines' );
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        // get the model
        $model = JModelLegacy::getInstance( 'OrderItems', 'CitruscartModel' );
        $user = JFactory::getUser( );
        $model->setState( 'filter_userid', $user->id );
        $model->setState( 'filter_productid', $product_id );
        $orders = $model->getList( true, false );
        return $orders;

    }

    /**
     * Gets a product's id and User id from Product comment table
     *
     * @param $id
     * @return array
     */
    public static function getUserAndProductIdForReview( $product_id, $user_id )
    {
        if ( empty( $product_id ) && empty( $user_id ) )
        {
            return array( );
        }
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        $table = JTable::getInstance( 'productcomments', 'CitruscartTable' );

        $query = new CitruscartQuery( );
        $query->select( "tbl.productcomment_id" );
        $query->from( $table->getTableName( ) . " AS tbl" );
        //$query->where( "tbl.product_id = ".(int) $id ." AND tbl.user_id= ".(int)$uid);
        $query->where( "tbl.product_id = " . ( int ) $product_id );
        $query->where( "tbl.user_id = " . ( int ) $user_id );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );
        $items = $db->loadColumn( );
        return $items;
    }

    /**
     * Gets user's emails from Product comment table
     *
     * @param int $product_id
     * @return array
     */
    public static function getUserEmailForReview( $product_id )
    {
        if ( empty( $product_id ) )
        {
            return array( );
        }
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR .'/components/com_citruscart/tables' );
        $table = JTable::getInstance( 'productcomments', 'CitruscartTable' );

        $query = new CitruscartQuery( );
        $query->select( "tbl.user_email" );
        $query->from( $table->getTableName( ) . " AS tbl" );
        $query->where( "tbl.product_id = " . ( int ) $product_id );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );
        $items = $db->loadColumn( );

        return $items;
    }

    /**
     *
     * Check if the current user already given a feedback to a review
     * @param $uid - the id of the user
     * @param $cid - comment id
     * @return boolean
     */
    public static function isFeedbackAlready( $uid, $cid )
    {

        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
        $table = JTable::getInstance( 'productcommentshelpfulness', 'CitruscartTable' );

        $query = new CitruscartQuery( );
        $query->select( "tbl.productcommentshelpfulness_id" );
        $query->from( $table->getTableName( ) . " AS tbl" );
        $query->where( "tbl.user_id = " . ( int ) $uid );
        $query->where( "tbl.productcomment_id = " . ( int ) $cid );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );
        $items = $db->loadColumn( );

        return !empty( $items ) ? true : false;
    }

    /**
     * returns a product's quantity list for all combination
     * @return array of the entire Objects;
     */
    public function getProductQuantitiesObjects( $id )
    {
        Citruscart::load( 'CitruscartQuery', 'library.query' );
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );

        $tableQuantity = JTable::getInstance( 'ProductQuantities', 'CitruscartTable' );
        $query = new CitruscartQuery( );
        $select[] = "quantities.*";

        $query->select( $select );
        $query->from( $tableQuantity->getTableName( ) . " AS quantities" );
        $query->where( "quantities.product_id = " . $id );

        $db = JFactory::getDBO( );
        $db->setQuery( ( string ) $query );

        $results = $db->loadObjectList( );

        return $results;
    }

    /**
     * returns a attributes's options list
     * @return array of the entire Objects;
     */
    public function getAttributeOptionsObjects( $id )
    {
        if ( empty( $id ) )
        {
            return array( );
        }

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
        $model->setState( 'filter_attribute', $id );
        $model->setState( 'order', 'tbl.ordering' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList( );
        return $items;
    }

    /**
     *
     * Used in diagnostic helper
     * @param $product_id
     * @return unknown_type
     */
    public function updateOverallRatings( )
    {
        $success = true;

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductComments', 'CitruscartModel' );
        $model->setState( 'filter_enabled', '1' );
        $model->setState( 'select', 'tbl.product_id' );
        $query = $model->getQuery( );
        $query->select( 'tbl.productcomment_id' );
        $query->group( 'tbl.product_id' );
        $model->setQuery( $query );
        if ( $items = $model->getList( ) )
        {
            foreach ( $items as $item )
            {
                // get the product row
                $product = JTable::getInstance( 'Products', 'CitruscartTable' );
                $product->load( $item->product_id, true, false );
                $product->updateOverallRating( true );
            }
        }

        return $success;
    }

    /**
     * Returns a rating image
     * @param mixed Boolean
     * @return array
     */
    public static function getRatingImage( $num, $view=null, $clickable = false , $layout = 'product_rating' )
    {
        if( !$clickable )
        {
            if ( $num <= '0' )
            {
                $id = "0";
            }
            elseif ( $num > '0' && $num <= '0.5' )
            {
                $id = "0.5";
            }
            elseif ( $num > '0.5' && $num <= '1' )
            {
                $id = "1";
            }
            elseif ( $num > '1' && $num <= '1.5' )
            {
                $id = "1.5";
            }
            elseif ( $num > '1.5' && $num <= '2' )
            {
                $id = "2";
            }
            elseif ( $num > '2' && $num <= '2.5' )
            {
                $id = "2.5";
            }
            elseif ( $num > '2.5' && $num <= '3' )
            {
                $id = "3";
            }
            elseif ( $num > '3' && $num <= '3.5' )
            {
                $id = "3.5";
            }
            elseif ( $num > '3.5' && $num <= '4' )
            {
                $id = "4";
            }
            elseif ( $num > '4' && $num <= '4.5' )
            {
                $id = "4.5";
            }
            elseif ( $num > '4.5' && $num <= '5' )
            {
                $id = "5";
            }
        }
        $rating = new stdClass();
        $rating->clickable = $clickable;
        if( !$clickable )
            $rating->rating = $id;
        $rating->count = $num;

        if( $view === null ) // if nothing is specified, load products view
            $view = CitruscartHelperProduct::getProductViewObject();

        $view->rating =  $rating;
        $view->setLayout( $layout );

        $result = $view->loadTemplate( null );
        if (JError::isError($result))
            return '';

        unset( $view->rating );

        return $result;
    }

    /**
     *
     * Used in Diagnostic helper
     * @return unknown_type
     */
    function updatePriceUserGroups( )
    {
        $success = true;

        $db = JFactory::getDBO( );
        $db->setQuery( "UPDATE #__citruscart_productprices SET `group_id` = '1' WHERE `group_id` = '0'; " );
        if ( !$db->query( ) )
        {
            $this->setError( $db->getErrorMsg( ) );
            $success = false;
        }
        return $success;
    }

    /**
     * Function executes after a product is saved
     * and is used for triggering native integrations
     * (rather than encurring the overhead of a plugin)
     *
     * @param $product
     * @return unknown_type
     */
    function onAfterSaveProducts( $product )
    {
        // add params to Ambrasubs types
        $helper = CitruscartHelperBase::getInstance( 'Ambrasubs' );
        $helper->onAfterSaveProducts( $product );
    }

    /**
     * Function to display price with tax base on the configuration
     * @param float $price - item price
     * @param float $tax -  product price
     * @param int $show - to show price with tax
     * @return string
     */
    public static function dispayPriceWithTax( $price = '0', $tax = '0', $show = '0' )
    {
        $txt = '';
        if ( $show && $tax )
        {
            switch( $show )
            {
                case 1: // display tax next to price
                    {
                        $txt .= CitruscartHelperBase::currency( $price );
                        $txt .= sprintf( JText::_('COM_CITRUSCART_INCLUDE_TAX'), CitruscartHelperBase::currency( $tax ) );
                        break;
                    }
                case 2: // sum the tax and product price
                    {
                        $txt .= CitruscartHelperBase::currency( $price + $tax );
                        break;
                    }
                case 3: // sum the tax and product price (+text that the price includes tax)
                    {
                        $txt .= sprintf( JText::_('COM_CITRUSCART_SUM_INCLUDE_PRICE_TAX'), CitruscartHelperBase::currency( $price + $tax ) );
                        break;
                    }
                case 4: // display both price without and with tax
                    {
                        $txt .= CitruscartHelperBase::currency( $price );
                        $txt .= sprintf( JText::_('COM_CITRUSCART_INCLUDING_TAX'), CitruscartHelperBase::currency( $price + $tax ) );
                        break;
                    }
            }
        }
        else
        {
            $txt .= CitruscartHelperBase::currency( $price );
        }

        return $txt;
    }

    /**
     * Loads a product by its ID
     * and stores it for later use by the application
     *
     * @param unknown_type $id
     * @param boolean $reset
     * @param boolean $load_eav
     * @return unknown_type
     */
    public function load( $id, $reset = true, $load_eav = true )
    {
        if ( empty( self::$products[$id][$load_eav] ) )
        {
            JTable::addIncludePath( trim( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' ) );
            $productTable = JTable::getInstance( 'Products', 'CitruscartTable' );
            $productTable->load( $id, $reset, $load_eav );
            self::$products[$id][$load_eav] = $productTable;
        }
        return self::$products[$id][$load_eav];
    }

    /**
     * Guesses the default options (first in the list)
     * Enter description here ...
     * @param unknown_type $attributes
	 *
	 * DEPRECIATED
    public static function getDefaultAttributeOptions( $attributes )
    {
        $default = array( );
		if( !is_array( $attributes ) || empty( $attributes ) ) {
			return array();
		}
        foreach ( @$attributes as $attribute ) {
        	$default[ $attribute->productattribute_id ] = 0;
        }

        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/models' );
		// first, we check, if we set up quantities
		$m_quantity = JModelLegacy::getInstance( 'ProductQuantities', 'CitruscartModel' );
		$vals = array_values( $attributes );
		$attribute = array_shift( $vals ); // get first attribute so you can access product_id
		$m_quantity->setState( 'filter_productid', $attribute->product_id );
		$m_quantity->setState( 'filter_quantity_from', 1);
        $m_quantity->setState( 'order', 'tbl.product_attributes' );
        $m_quantity->setState( 'direction', 'ASC' );
		$res = $m_quantity->getList();
		if( count( $res ) && strlen( $res[0]->product_attributes ) ) {
			// combination of product attributes with quantity in stock being higher than 0 exists
			$opts = explode( ',', $res[0]->product_attributes );
			for( $i = 0, $c = count( $opts ); $i < $c; $i++ ) {
                $m_pao = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
                $m_pao->setId( $opts[$i] );
                $attr = $m_pao->getItem();

				if( $attr ) {
					$default[$attr->productattribute_id] = $opts[$i];
				}
			}
		} else {
	        foreach ( @$attributes as $attribute )
	        {
	            $model = JModelLegacy::getInstance( 'ProductAttributeOptions', 'CitruscartModel' );
	            $model->setState( 'filter_attribute', $attribute->productattribute_id );
	            $model->setState( 'order', 'tbl.ordering' );

	            $items = $model->getList( );

	            if ( count( $items ) )
	            {
	                $default[$attribute->productattribute_id] = $items[0]->productattributeoption_id;
	            }
	        }
		}

        return $default;
    }
     */

    /**
     * Get the cart button form for a specific product
     *
     * @param int $product_id 	The id of the product
     * @return html	The add to cart form
     */
    public static function getCartButton( $product_id, $layout = 'product_buy', $values = array( ), &$callback_js = '' )
    {
		/*  Get the application */
    	$app = JFactory::getApplication();
        if( is_array( $values ) && !count( $values ) )
        {
        	$values = $app->input->get('request');
            //$values = JRequest::get( 'request' );
        }
        $html = '';

        $page = $app->input->get('page', 'product');

        //$page = JRequest::getVar( 'page', 'product' );
		$isPOS = $page == 'pos';

		if( $isPOS ) {
	        JLoader::register( "CitruscartViewPOS", JPATH_ADMINISTRATOR."/components/com_citruscart/views/pos/view.html.php" );
	        $view = new CitruscartViewPOS( );
		} else {
	        JLoader::register( "CitruscartViewProducts", JPATH_SITE."/components/com_citruscart/views/products/view.html.php" );
	        $view = new CitruscartViewProducts( );
		}
        $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
        $model->setId( $product_id );
        $model->setState( 'task', 'product_buy' );

        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper_product = CitruscartHelperBase::getInstance( 'Product' );

        Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
        $user_id = JFactory::getUser( )->id;
		if( $isPOS ) {

			$user_id = $app->input->getInt('user_id', $user_id);
			//$user_id = JRequest::getInt( 'user_id', $user_id );
		}

        $filter_group = CitruscartHelperUser::getUserGroup( $user_id, $product_id );
		$qty = ( isset( $values['product_qty'] ) && !empty( $values['product_qty'] ) ) ? $values['product_qty'] : 1;
        $model->setState( 'filter_group', $filter_group );
		$model->setState( 'product.qty', $qty );
		$model->setState( 'user.id', $user_id );
        $row = $model->getItem( false, true, false );
      	if ( $row->product_notforsale || Citruscart::getInstance( )->get( 'shop_enabled' ) == '0' )
        {
            return $html;
        }
        // This enable this helper method to be used outside of Citruscart
        if( $isPOS ) {
        	$view->set( '_controller', 'pos' );
	        $view->set( '_view', 'pos' );
        } else {
	        $view->addTemplatePath( JPATH_SITE . '/components/com_citruscart/views/products/tmpl' );
	        $view->addTemplatePath( JPATH_SITE . '/templates/' . JFactory::getApplication('site')->getTemplate() . '/html/com_citruscart/products/' );
	        // add extra templates
	        $view->addTemplatePath( Citruscart::getPath( 'product_buy_templates' ) );
	        $view->set( '_controller', 'products' );
	        $view->set( '_view', 'products' );
		}
        $view->set( '_doTask', true );
        $view->set( 'hidemenu', true );
        $view->setModel( $model, true );
        $view->setLayout( $layout );
        $view->product_id = $product_id;
        $view->values = $values;
        $filter_category = $model->getState( 'filter_category', $app->input->getInt( 'filter_category',0 ) );
        //$filter_category = $model->getState( 'filter_category', JRequest::getInt( 'filter_category', ( int ) $values['filter_category'] ) );
        $view->filter_category = $filter_category;

		if( $isPOS ) {
    	    $view->validation = "index.php?option=com_citruscart&view=pos&task=validate&format=raw";
		} else {
	        $view->validation = "index.php?option=com_citruscart&view=products&task=validate&format=raw";
		}

        $config = Citruscart::getInstance( );
        // TODO What about this??
        $show_shipping = $config->get( 'display_prices_with_shipping' );
        if ( $show_shipping )
        {
            $article_link = $config->get( 'article_shipping', '' );
            $shipping_cost_link = JRoute::_( 'index.php?option=com_content&view=article&id=' . $article_link );
            $view->shipping_cost_link = $shipping_cost_link;
        }

        $quantity_min = 1;
        if ( $row->quantity_restriction )
        {
            $quantity_min = $row->quantity_min;
        }

        $invalidQuantity = '0';
        $attributes = array( );
		$attr_orig = array();
        if ( empty( $values ) )
        {
            $product_qty = $quantity_min;
            // get the default set of attribute_csv
            if (!isset($row->default_attributes)) {
                $default_attributes = $helper_product->getDefaultAttributes( $product_id );
            } else {
                $default_attributes = $row->default_attributes;
            }

            sort( $default_attributes );
            $attributes_csv = implode( ',', $default_attributes );
            $availableQuantity = $helper_product->getAvailableQuantity( $product_id, $attributes_csv );
            if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
            {
                $invalidQuantity = '1';
            }
            $attr_orig = $attributes = $default_attributes;
        }
        else
        {
            $product_qty = !empty( $values['product_qty'] ) ? ( int ) $values['product_qty'] : $quantity_min;

            // TODO only display attributes available based on the first selected attribute?
            foreach ( $values as $key => $value )
            {
                if ( substr( $key, 0, 10 ) == 'attribute_' )
                {
                	if( empty( $value ) ){
                		$attributes[$key] = 0;
					} else {
	                    $attributes[$key] = $value;
					}
                }
            }

            if( !count( $attributes ) ) { // no attributes are selected -> use default
                if (!isset($row->default_attributes)) {
                    $attributes = $helper_product->getDefaultAttributes( $product_id );
                } else {
                    $attributes = $row->default_attributes;
                }
            }
			$attr_orig = $attributes;

            sort( $attributes );
            // Add 0 to attributes to include all the root attributes
            //$attributes[] = 0;//remove this one. its causing the getAvailableQuantity to not get quantity because of wrong csv

            // For getting child opts
            $view->selected_opts = json_encode( array_merge( $attributes, array(
                    '0'
            ) ) );

            $attributes_csv = implode( ',', $attributes );
            // Integrity checks on quantity being added
            if ( $product_qty < 0 )
            {
                $product_qty = '1';
            }

            // using a helper file to determine the product's information related to inventory
            $availableQuantity = $helper_product->getAvailableQuantity( $product_id, $attributes_csv );
            if ( $availableQuantity->product_check_inventory && $product_qty > $availableQuantity->quantity )
            {
                $invalidQuantity = '1';
            }
        }

        // adjust the displayed price based on the selected or default attributes
        CitruscartHelperProduct::calculateProductAttributeProperty( $row, $attr_orig, 'price', 'product_weight' );
        $show_tax = $config->get( 'display_prices_with_tax' );
        $show_product = $config->get( 'display_category_cartbuttons' );
        $view->show_tax = $show_tax;

        $row->tax = '0';
        $row->taxtotal = '0';
        if ( $show_tax )
        {
            // finish CitruscartHelperUser::getGeoZone -- that's why this isn't working
            Citruscart::load( 'CitruscartHelperUser', 'helpers.user' );
            $geozones_user = CitruscartHelperUser::getGeoZones( $user_id );
            if ( empty( $geozones_user ) )
            {
                $geozones = array( Citruscart::getInstance( )->get( 'default_tax_geozone' ) );
            }
            else
            {
                $geozones = array();
                foreach( $geozones_user as $value )
                    $geozones[] = $value->geozone_id;
            }
            Citruscart::load( 'CitruscartHelperTax', 'helpers.tax' );
            $product = new stdClass();
         	$product->product_price = $row->price;
            $product->product_id = $product_id;
            $tax = CitruscartHelperTax::calculateGeozonesTax( array( $product ), 2, $geozones );
            $row->taxtotal = $tax->tax_total;
            $row->tax = $tax->tax_total;
        }

        $row->_product_quantity = $product_qty;

        if ($page == 'product' || $isPOS ) {
            $display_cartbutton = Citruscart::getInstance()->get( 'display_product_cartbuttons', '1' );
        }
        else {
            $display_cartbutton = Citruscart::getInstance()->get( 'display_category_cartbuttons', '1' );
        }

        $view->page = $page;
        $view->display_cartbutton = $display_cartbutton;
        $view->availableQuantity = $availableQuantity;
        $view->invalidQuantity = $invalidQuantity;
		if( $isPOS ) {
			$view->product = $row;
		} else {
	        $view->item = $row;
		}

        $dispatcher = JDispatcher::getInstance( );

        ob_start( );
        JFactory::getApplication()->triggerEvent( 'onDisplayProductAttributeOptions', array(
                $row->product_id
        ) );

        $view->onDisplayProductAttributeOptions = ob_get_contents( );


        ob_end_clean( );

        $html = $view->loadTemplate();

		if( isset( $view->callback_js ) && !empty( $view->callback_js ) ) {
			$callback_js = $view->callback_js;
		}
        return $html;
    }

    /**
     * Get the share buttons for a specific product
     *
     * @param int $product_id 	The id of the product
     * @return html	The add to product detail view
     */
    public static function getProductShareButtons( $view, $product_id, $layout = 'product_share_buttons' )
    {
        $share_data = new stdClass();
        $share_data->product_id = $product_id;
        if( $view === null ) // if nothing is specified, load products view
            $view = CitruscartHelperProduct::getProductViewObject();
        $view->share_data = $share_data;

        $lt = $view->getLayout();
        $view->setLayout( $layout );
        ob_start( );
        echo $view->loadTemplate( null );
        $html = ob_get_contents( );
        ob_end_clean( );
        $view->setLayout( $lt );
        unset( $view->share_data );
        return $html;
    }

    public static function getSocialBookMarkUri( $uri = null )
    {
        if( $uri === null )
            $uri = JFactory::getUri()->__toString();

        static $cached_uri = array();
        $type = Citruscart::getInstance()->get( 'display_bookmark_uri', 0 );

        switch( $type )
        {
            case 0 : // Long URI
                {
                    return $uri;
                }
            case 1 : // Bit.ly
                {
                    if( !isset( $cached_uri[$type][$uri] ) )
                    {
                        $key = Citruscart::getInstance()->get( 'bitly_key', '' );
                        $logn = Citruscart::getInstance()->get( 'bitly_login', '' );
                        $link = 'http://api.bit.ly/v3/shorten?apiKey='.$key.'&login='.$login.'&longURL='.urlencode($uri);
                        $c = curl_init();
                        curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
                        curl_setopt( $c, CURLOPT_URL, $link );
                        $request = curl_exec( $c );
                        curl_close( $c );
                        $uri_short = json_decode( $request )->data->url;
                        $cached_uri[$type][$uri] = $uri_short;
                    }
                    return $cached_uri[$type][$uri];
                }
        }
    }

    /*
     * Converts product attributes from CSV format to an array
    *
    * @param $product_id    ID of the product with attributes
    * @param $values_csv    String with product attributes in CSV format
    *
    * @return Array of arrays in format (attribute_id, attribute_value)
    */
    public static function convertAttributesToArray( $product_id, $values_csv )
    {
        JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/models' );
        $model = JModelLegacy::getInstance( 'ProductAttributes', 'CitruscartModel' );
        $model->setState( 'filter_product', $product_id );
        $list = $model->getList();
        $result = array();
        $values = explode( ',', $values_csv );
        for( $i = 0, $c = count( $list ); $i < $c; $i++ )
        {
            if( isset( $values[$i]) )
                $result []= array( $list[$i]->productattribute_id, $values[$i] );
        }

        return $result;
    }

    /*
     * Method to display a selected gallery layout for a specific product
    *
    * @param $product_id Product ID
    * @param $product_name Product name
    * @param $exclude Exclude this image from the gallery
    * @param $layout Layout of the gallery ('product_gallery' by default)
    * @param $values Values from the request (POST array by default)
    *
    * @return HTML of the layout
    */
    public static function getGalleryLayout( $view, $product_id, $product_name = '', $exclude = '', $layout = 'product_gallery', $values = array() )
    {
    	/* Get the application */
    	$app= JFactory::getApplication();
        if( is_array( $values ) && !count( $values ) )
        {
        	$values = $app->input->get( 'post' );
            //$values = JRequest::get( 'post' );
        }
        if( $view === null ) // if nothing is specified, load products view
            $view = CitruscartHelperProduct::getProductViewObject();

        $path = CitruscartHelperProduct::getGalleryPath( $product_id );
        $images = CitruscartHelperProduct::getGalleryImages( $path, array( 'exclude' => $exclude ) );
        $uri = CitruscartHelperProduct::getUriFromPath( $path );
        $show_gallery = false;
        if ( !empty( $path ) && !empty( $images ) )
            $show_gallery = true;

        Citruscart::load( 'CitruscartUrl', 'library.url' );
        $gallery_data = new stdClass();
        $gallery_data->values = $values;
        $gallery_data->show_gallery = $show_gallery;
        $gallery_data->uri = $uri;
        $gallery_data->images = $images;
        $gallery_data->product_name = $product_name;
        $view->gallery_data = $gallery_data;

        $lt = $view->getLayout();
        $view->setLayout( $layout );
        ob_start( );
        echo $view->loadTemplate( null );
        $html = ob_get_contents( );
        ob_end_clean( );
        $view->setLayout( $lt );
        unset( $view->gallery_data );
        return $html;
    }

    public static function getProductViewObject( $model = null, $hidemenu = true, $dotask = true )
    {
        JLoader::register( "CitruscartViewProducts", JPATH_SITE."/components/com_citruscart/views/products/view.html.php" );
        $view = new CitruscartViewProducts( );
        $view->set( '_controller', 'products' );
        $view->set( '_view', 'products' );
        $view->set( '_doTask', $dotask );
        $view->set( 'hidemenu', $hidemenu );

        if( $model === null )
            $model = JModelLegacy::getInstance( 'Products', 'CitruscartModel' );
        $view->setModel( $model, true );

        return $view;
    }

    public static function calculateProductAttributeProperty( &$product, $attributes, $product_price, $product_weight )
    {
        Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );
        $helper_product = CitruscartHelperBase::getInstance( 'Product' );

    	// first we get rid off phantom attributes (the ones that should be hidden because their parent attribute was just unselected)
    	$attr_base = CitruscartHelperProduct::getAttributes( $product->product_id, array_merge( $attributes, array( '0' ) ) );
    	$attr_final = CitruscartHelperProduct::getDefaultAttributeOptions($attr_base);

		foreach( $attr_final as $key => $value ) {
			if( isset( $attributes['attribute_'.$key] ) ) {
				$attr_final[$key] = $attributes['attribute_'.$key];
			}
		}

        Citruscart::load( 'CitruscartQuery', 'library.query' );
        $q = new CitruscartQuery();
        $q->select( 'tbl.`productattributeoption_price` , tbl.`productattributeoption_prefix`, tbl.`productattributeoption_id` ' );
        $q->select( 'tbl.`productattributeoption_code`, tbl.`productattributeoption_weight`, tbl.`productattributeoption_prefix_weight`' );
        $q->from( '`#__citruscart_productattributeoptions` tbl' );
        $q->join( 'left','`#__citruscart_productattributes` atr ON tbl.	productattribute_id = atr.productattribute_id' );
        $q->where( "tbl.productattributeoption_id IN ('".implode( "', '", $attr_final )."')" );
        $q->order( 'atr.ordering ASC' );
        $db = JFactory::getDbo();
        $db->setQuery( $q );
        $res = $db->loadObjectList();

        $attributes = array();
        for( $i = 0, $c = count( $res ); $i < $c; $i++ )
        {
            // update product price
            // is not + or -
            if ( $res[$i]->productattributeoption_prefix == '=' )
            {
                $product->$product_price = floatval( $res[$i]->productattributeoption_price );
            }
            else
            {
                $product->$product_price = $product->$product_price + floatval( $res[$i]->productattributeoption_prefix.$res[$i]->productattributeoption_price );
            }

            // update product weight
            if ( $res[$i]->productattributeoption_prefix_weight == '=' )
            {
                $product->$product_weight = floatval( $res[$i]->productattributeoption_weight );
            }
            else
            {
                $product->$product_weight = $product->$product_weight + floatval( $res[$i]->productattributeoption_prefix_weight.$res[$i]->productattributeoption_weight );
            }
            $attributes[] = $res[$i]->productattributeoption_id;
        }

        $product->sku = self::getProductSKU($product, $attributes);
    }

    public static function getProductSKU($product, $attributes_array = array())
    {
        $product_sku = $product->product_sku;

        // Reorder to use the attributes order
        $attributes = array();
        if( !count( $attributes_array ) )
        {
            return $product_sku;
        }

        foreach($attributes_array as $id)
        {
            $table = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
            $table->load( $id );

            // Load attribute
            $attr_id = $table->productattribute_id;

            $attributes[] = $attr_id;
        }

        // Load list of attributes
        $model = JModelLegacy::getInstance('ProductAttributes', 'CitruscartModel');
        $model->setState('filter_id', $attributes);
        $model->setState('order', 'ordering');
        $model->setState('direction', 'ASC');
        $list = $model->getList();



        // now that they are in order, generate sku
        foreach($list as $attr)
        {
            foreach($attributes_array as $attrib_id)
            {
                $table = JTable::getInstance('ProductAttributeOptions', 'CitruscartTable');
                $table->load( $attrib_id );

                if($table->productattribute_id == $attr->productattribute_id)
                {
                    $product_sku .= $table->productattributeoption_code;
                    continue;
                }
            }
        }

        return $product_sku;
    }

}
