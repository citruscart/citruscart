<?php
/*------------------------------------------------------------------------
# com_citruscart - citruscart
# ------------------------------------------------------------------------
# author    Citruscart Team - Citruscart http://www.citruscart.com
# copyright Copyright (C) 2012 Citruscart.com All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://citruscart.com
# Technical Support:  Forum - http://citruscart.com/forum/index.html
# Fork of Tienda
# @license GNU/GPL  Based on Tienda by Dioscouri Design http://www.dioscouri.com.
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//require_once (JPATH_ADMINISTRATOR.'/components/com_citruscart/library/dioscouri/dioscouri.php');
require_once (JPATH_SITE.'/libraries/dioscouri/dioscouri.php');

class Citruscart extends DSC
{
	protected $_name 			= 'citruscart';
	protected $_version 		= '1.0';
	protected $_build          = null;
	protected $_versiontype    = 'community';
	protected $_copyrightyear 	= '2014 -  2019';
	protected $_min_php		= '5.3';
	static $_guestIdStart = -10;

	// View Options
	public $include_site_css                   = '1';
	public $use_bootstrap	= 		'1';
	public $show_linkback						= '1';
	public $amigosid                           = '';
	public $page_tooltip_dashboard_disabled	= '0';
	public $page_tooltip_config_disabled		= '0';
	public $page_tooltip_tools_disabled		= '0';
	public $page_tooltip_accounts_disabled		= '0';
	public $page_tooltip_payouts_disabled		= '0';
	public $page_tooltip_logs_disabled			= '0';
	public $page_tooltip_payments_disabled		= '0';
	public $page_tooltip_commissions_disabled	= '0';
	public $page_tooltip_users_view_disabled   = '0';
	public $article_default					= '0';
	public $article_potential					= '0';
	public $article_unapproved					= '0';
	public $article_disabled					= '0';
	public $article_application				= '0';
	public $approve_new						= '0';
	public $enable_unregistered				= '0';
	public $enable_payouttype_choice			= '0';
	public $company_information				= null;
	public $display_dashboard_thismonth_commissions	= '1';
	public $display_dashboard_thismonth_logs	= '1';
	public $display_dashboard_conversions		= '1';
	public $display_dashboard_statistics		= '1';
	public $default_currencyid					= '1'; // USD
	public $currency_exchange_autoupdate		= '1'; // yes
	public $login_url_redirect					= 'index.php';
	public $logout_url_redirect				= 'index.php';
	public $login_redirect						= '1';
	public $orderstates_csv                    = '2, 3, 5, 17';
	// Other Info
	public $display_shipping_tax               = '1';
	public $initial_order_state                = '15';
	public $pending_order_state                = '1';
	public $defaultShippingMethod              = '2';
	public $guest_checkout_enabled             = '1';
	// Shop Info
	public $shop_enabled                       = '1';
	public $shop_name							= '';
	public $shop_company_name					= '';
	public $shop_address_1						= '';
	public $shop_address_2						= '';
	public $shop_city							= '';
	public $shop_country						= '';
	public $shop_zone							= '';
	public $shop_zip							= '';
	public $shop_tax_number_1					= '';
	public $shop_tax_number_2					= '';
	public $shop_phone							= '';
	public $shop_owner_name					= '';
	// Default Dimensions for the images
	public $product_img_height 		        = 128;
	public $product_img_width 			        = 96;
	public $category_img_height 		        = 48;
	public $category_img_width			        = 48;
	public $manufacturer_img_width		        = 128;
	public $manufacturer_img_height	        = 96;
	// Unit measures
	public $dimensions_unit					= 'cm';
	public $weight_unit						= 'kg';
	public $date_format                        = '%a, %d %b %Y, %I:%M%p';
	public $use_default_category_image         = '1';
	public $lightbox_width                     = '800';
	public $lightbox_height                    = '480';
	public $require_terms                      = '0';
	public $article_terms                      = '';
	public $order_number_prefix                = '';
	public $article_shipping                   = '0';
	public $display_prices_with_shipping       = '0';
	public $display_prices_with_tax            = '0';
	public $display_taxclass_lineitems         = '0';
	public $addtocartaction                    = 'redirect';
	public $cartbutton                         = 'button';
	public $include_root_pathway               = '0';
	public $display_citruscart_pathway             = '1';
	public $display_out_of_stock               = '1';
	public $global_handling                    = '';
	public $shipping_tax_class                 = '';
	public $default_tax_geozone                = '';
	public $review_helpfulness_enable			='0';
	public $share_review_enable				='0';
	public $subscriptions_expiring_notice_days = '14';
	public $login_review_enable				='1';
	public $purchase_leave_review_enable		='1';
	public $use_captcha						='1';
	public $display_product_quantity           = '1';
	public $enable_reorder_table	            = '1';
	public $product_review_enable				= '1';
	public $force_ssl_checkout                 = '0';
	public $coupons_enabled                    = '1';
	public $coupons_before_tax                 = '1';
	public $multiple_usercoupons_enabled       = '0';
	public $default_user_group			 	    = '1';
	public $subcategories_per_line				= '5';
	public $custom_language_file				= '0';
	public $currency_preval				    = '$';
	public $currency_postval				    = 'USD';
	public $display_period					    = '1';
	public $article_checkout                   = '';
	public $display_category_cartbuttons       = '1';
	public $display_product_cartbuttons       = '1';
	public $product_reviews_autoapprove        = '0';

	//product sorting
	public $display_sort_by					= '1';
	public $display_sortings					= 'Name|product_name,Price|price,Rating|product_rating';

	//social bookmarking integration
	public $display_facebook_like				= '1';
	public $display_tweet						= '1';
	public $display_tweet_message				= 'Check this out!';
	public $display_google_plus1						= '1';
	public $display_google_plus1_size				= 'medium';
	public $display_bookmark_uri        = '0';
	public $bitly_key 								= '';
	public $bitly_login 						= '';

	//Ask a question about this product
	public $ask_question_enable				= '1';
	public $ask_question_showcaptcha			= '1';
	public $ask_question_modal					= '1';

	//address management
	public $show_field_address_name					= '0';
	public $show_field_title					= '0';
	public $show_field_name					= '3';
	public $show_field_middle					= '0';
	public $show_field_last					= '3';
	public $show_field_company					= '0';
	public $show_field_tax_number					= '0';
	public $show_field_address1				= '3';
	public $show_field_address2				= '0';
	public $show_field_zone					= '3';
	public $show_field_country					= '3';
	public $show_field_city					= '3';
	public $show_field_zip						= '3';
	public $show_field_phone					= '0';
	public $show_field_cell					= '0';
	public $show_field_fax						= '0';

	// address validation management
	public $validate_field_address_name				= '0';
	public $validate_field_title				= '0';
	public $validate_field_name				= '3';
	public $validate_field_middle				= '0';
	public $validate_field_last				= '3';
	public $validate_field_company				= '0';
	public $validate_field_tax_number				= '0';
	public $validate_field_address1			= '3';
	public $validate_field_address2			= '0';
	public $validate_field_zone				= '3';
	public $validate_field_country				= '3';
	public $validate_field_city				= '3';
	public $validate_field_zip					= '3';
	public $validate_field_phone				= '0';
	public $validate_field_cell				= '0';
	public $validate_field_fax					= '0';

	public $sha1_images						= '0';
	public $files_maxsize						= '3000';

	// email settings
	public $disable_guest_signup_email         = '0';
	public $obfuscate_guest_email				= '0';
	public $autonotify_onSetOrderPaymentReceived = '0';
	public $shop_email = '';
	public $shop_email_from_name = '';

	//one page checkout
	public $one_page_checkout					= '1';

	//since 0.7.2
	public $ignored_countries					= '83, 188, 190';

	//compare products
	public $enable_product_compare 			= '1';
	public $compared_products					= '5';
	public $show_manufacturer_productcompare 	= '1';
	public $show_rating_productcompare 		= '1';
	public $show_addtocart_productcompare 		= '1';
	public $show_model_productcompare			= '1';
	public $show_sku_productcompare			= '1';

	// since 0.7.3
	public $show_submenu_fe					= '1';

	// since 0.8.0
	public $display_subnum = '0';
	public $sub_num_digits = '8';
	public $default_sub_num = '1';
	public $dispay_working_image_product = '1';
	public $one_page_checkout_layout     = 'standard';
	public $low_stock_notify					= '0';
	public $low_stock_notify_value				= '0';
	public $one_page_checkout_tooltips_enabled = '0';

	// since 0.8.1
	public $multiupload_script = '0';

	// since 0.8.2
	public $display_relateditems = '1';
	public $article_default_payment_failure = '0';
	public $order_emails = '';
	public $display_credits = '0';
	public $display_wishlist = '0';
	public $display_subscriptions = '1';
	public $display_mydownloads = '1';

	// since 0.10.0
	public $show_tax_checkout = '4';
	public $secret_word = '';
	public $password_min_length = '5';
	public $password_req_alpha = '1';
	public $password_req_num = '0';
	public $password_req_spec = '0';
	public $password_php_validate = '0';
	public $content_plugins_product_desc = '0';
	public $lower_filename = '1';

  // since 0.9.1
	public $eavtext_content_plugin = '1';
	public $eavinteger_use_thousand_separator = '0';
	public $date_format_act = 'D, d M Y, h:iA';

	public $calc_tax_shipping = '0';

	public $default_category_layout = '';
	public $default_product_layout = '';
	public $enable_product_detail_nav = '';
	public $disable_changing_list_limit = '';
	public $default_list_limit = '';

	// since 0.10.2
	public $pos_request_clean_hours = '24';
    public $orders_confirmation_header_code = '';
	public $use_wishlist_search = '1';

	public static function getGuestIdStart()
	{
		return self::$_guestIdStart;
	}

	/**
	 * Get the URL to the folder containing all media assets
	 *
	 * @param string	$type	The type of URL to return, default 'media'
	 * @return 	string	URL
	 */
	public static function getURL($type = 'media', $com='')
	{
		$url = '';

		switch($type)
		{
			case 'media' :
				$url = JURI::root(true).'/media/citruscart/';
				break;
			case 'css' :
				$url = JURI::root(true).'/media/citruscart/css/';
				break;
			case 'images' :
				$url = JURI::root(true).'/media/citruscart/images/';
				break;
			case 'ratings' :
				$url = JURI::root(true).'/media/citruscart/images/ratings/';
				break;
			case 'js' :
				$url = JURI::root(true).'/media/citruscart/js/';
				break;
			case 'categories_images' :
				$url = JURI::root(true).'/images/com_citruscart/categories/';
				break;
			case 'categories_thumbs' :
				$url = JURI::root(true).'/images/com_citruscart/categories/thumbs/';
				break;
			case 'products_images' :
				$url = JURI::root(true).'/images/com_citruscart/products/';
				break;
			case 'products_thumbs' :
				$url = JURI::root(true).'/images/com_citruscart/products/thumbs/';
				break;
			case 'products_files' :
				$url = JURI::root(true).'/images/com_citruscart/files/';
				break;
			case 'order_files' :
				$url = JURI::root(true).'/images/com_citruscart/orders/';
				break;
			case 'manufacturers_images' :
				$url = JURI::root(true).'/images/com_citruscart/manufacturers/';
				break;
			case 'manufacturers_thumbs' :
				$url = JURI::root(true).'/images/com_citruscart/manufacturers/thumbs/';
				break;
			case 'cartitems_files':
				$url = JURI::root(true).'/images/com_citruscart/cartitems/';
				break;
			case 'orderitems_files':
				$url = JURI::root(true).'/images/com_citruscart/orderitems/';
				break;
		}

		return $url;
	}



/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$query = "SELECT * FROM #__citruscart_config";
		return $query;
	}

	/**
	 * Get component config
	 *
	 * @acces	public
	 * @return	object
	 */
	 public static function getInstance()
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = new Citruscart();
		}

		return $instance;

	}




	/**
	 * Get the path to the folder containing all media assets
	 *
	 * @param 	string	$type	The type of path to return, default 'media'
	 * @return 	string	Path
	 */
	public static function getPath($type = 'media', $com='')
	{
		$path = '';

		switch($type)
		{
			case 'media' :
				$path = JPATH_SITE.'/media/com_citruscart';
				break;
			case 'css' :
				$path = JPATH_SITE.'/media/citruscart/css';
				break;
			case 'images' :
				$path = JPATH_SITE.'/media/citruscart/images';
				break;
			case 'ratings' :
				$path = JPATH_SITE.'/media/citruscart/images/ratings';
				break;
			case 'js' :
				$path = JPATH_SITE.'/media/citruscart/js';
				break;
			case 'products_templates' :
				$path = JPATH_SITE.'/media/citruscart/templates/site/products';
				break;
            case 'product_buy_templates' :
                $path = JPATH_SITE.'/media/citruscart/templates/site/product_buy';
                break;
			case 'categories_templates' :
				$path = JPATH_SITE.'/media/citruscart/templates/site/categories';
				break;
			case 'categories_images' :
				$path = JPATH_SITE.'/images/com_citruscart/categories';
				break;
			case 'categories_thumbs' :
				$path = JPATH_SITE.'/images/com_citruscart/categories/thumbs';
				break;
			case 'products_images' :
				$path = JPATH_SITE.'/images/com_citruscart/products';
				break;
			case 'products_thumbs' :
				$path = JPATH_SITE.'/images/com_citruscart/products/thumbs';
				break;
			case 'products_files' :
				$path = JPATH_SITE.'/images/com_citruscart/files';
				break;
			case 'manufacturers_images' :
				$path = JPATH_SITE.'/images/com_citruscart/manufacturers';
				break;
			case 'manufacturers_thumbs' :
				$path = JPATH_SITE.'/images/com_citruscart/manufacturers/thumbs';
				break;
			case 'order_files' :
				$path = JPATH_SITE.'/images/com_citruscart/orders';
				break;
			case 'cartitems_files':
				$path = JPATH_SITE.'/images/com_citruscart/cartitems';
				break;
			case 'orderitems_files':
				$path = JPATH_SITE.'/images/com_citruscart/orderitems';
				break;
		}

		return $path;
	}

    /**
     * Intelligently loads instances of classes in framework
     *
     * Usage: $object = BIllets::getClass( 'BIlletsHelperCarts', 'helpers.carts' );
     * Usage: $suffix = BIllets::getClass( 'BIlletsHelperCarts', 'helpers.carts' )->getSuffix();
     * Usage: $categories = BIllets::getClass( 'BIlletsSelect', 'select' )->category( $selected );
     *
     * @param string $classname   The class name
     * @param string $filepath    The filepath ( dot notation )
     * @param array  $options
     * @return object of requested class (if possible), else a new JObject
     */
    public static function getClass( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_citruscart' )  )
    {
        return parent::getClass( $classname, $filepath, $options  );
    }

    /**
     * Method to intelligently load class files in the framework
     *
     * @param string $classname   The class name
     * @param string $filepath    The filepath ( dot notation )
     * @param array  $options
     * @return boolean
     */
    public static function load( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_citruscart' ) )
    {
        return parent::load( $classname, $filepath, $options  );
    }


	/**
	 * Copy of Joomla method to fix problem with JS when SSL is turned on
	 */
	public static function getUriRoot()
	{
		$root = array();
		/* Get the application */
		$app = JFactory::getApplication();
		$view = $app->input->get('view', 'dashboard');
		$config = JFactory::getConfig();
		$uri = JURI::getInstance(JURI::base());
		if( $config->get('config.force_ssl') || ( $app->isSite() &&  $view == 'checkout' && Citruscart::getInstance()->get( 'force_ssl_checkout',0 ) ) )
			$uri->setScheme( 'https' );
		$root['prefix'] = $uri->__toString( array('scheme', 'host', 'port') );
		$root['path']   = rtrim($uri->__toString( array('path') ), '/\\');

		return $root['prefix'].$root['path'].'/';
	}

	/**
	 * Returns the result of the named function if it exists,
	 * otherwise returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed    The value of the property.
	 *
	 * @since   11.1
	 *
	 * @see     getProperties()
	 */
	public function get($property, $default = null)
	{
	    if (method_exists($this, 'get'.$property))
	    {
	        $method_name = 'get'.$property;
	        return $this->{$method_name}($default);
	    }

	    return parent::get($property, $default);
	}

	/**
	 *
	 */
	public function getDate_Format($default=null)
	{
	    if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	        return parent::get('date_format_act', $default);
	    } else {
	        // Joomla! 1.5 code here
	        return parent::get('date_format', $default);
	    }
	}
}


// keeping for compatibility
class CitruscartConfig extends Citruscart {}
