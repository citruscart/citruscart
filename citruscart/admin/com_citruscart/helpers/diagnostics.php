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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );
Citruscart::load( 'CitruscartHelperBase', 'helpers._base' );

require_once(JPATH_SITE.'/libraries/dioscouri/library/helper/diagnostics.php');

class CitruscartHelperDiagnostics extends DSCHelperDiagnostics
{
	/**
	 * Redirects with message
	 *
	 * @param object $message [optional]    Message to display
	 * @param object $type [optional]       Message type
	 */
	function redirect($message = '', $type = '')
	{
		$mainframe = JFactory::getApplication();

		if ($message)
		{
			$mainframe->enqueueMessage($message, $type);
		}
				
		$mainframe->input->set('controller', 'dashboard');
		$mainframe->input->set('view', 'dashboard');
		$mainframe->input->set('task', '');
		
		//JRequest::setVar('controller', 'dashboard');
		//JRequest::setVar('view', 'dashboard');
		//JRequest::setVar('task', '');
		return;
	}
	
	/**
	 *
	 * @param unknown_type $fieldname
	 * @param unknown_type $value
	 */
	protected function setCompleted( $fieldname, $value='1' )
	{
	    JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_citruscart/tables' );
	    $config = JTable::getInstance( 'Config', 'CitruscartTable' );
	    $config->load( array( 'config_name'=>$fieldname ) );
	    $config->config_name = $fieldname;
	    $config->value = '1';
	    $config->save();
	}

	/**
	 * Performs basic checks on your installation to ensure it is OK
	 * @return unknown_type
	 */
	function checkInstallation()
	{

	    $functions = array();
	    $functions[] = 'checkEAVAttributesFormatStrftime';
	    $functions[] = 'checkEAVAttributesFormatDate';
	    $functions[] = 'checkOrderHistoryID';
	    //$functions[] = 'createTableEAVValuesTime'; // NO NEW FEATURES YET
	    $functions[] = 'checkProductClassSuffix';
	    $functions[] = 'checkProductThumbImage';
	    
	    foreach ($functions as $function)
	    {
	        if (!$this->{$function}())
	        {
	            return $this->redirect( JText::_("COM_CITRUSCART_".$function."_FAILED") .' :: '. $this->getError(), 'error' );
	        }
	    }
	    
		// OLD CHECKS?
		if (!Citruscart::getInstance()->get('checkOldDiagnostics', '0'))
		{
			 
			// Check default currency
			if (!$this->checkDefaultCurrency())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKDEFAULTCURRENCY_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the productfiles table
			// deprecate this check eventually, b/c it is only needed it the admin installed 0.2.0
			if (!$this->checkProductFiles())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTFILES_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the orders table
			if (!$this->checkOrdersOrderCurrency())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERSORDERCURRENCY_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsInventory())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSINVENTORY_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the category root
			if (!$this->checkCategoriesRootDesc())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCATEGORIESROOTDESC_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the products table
			if (!$this->checkProductsParamsLayout())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSPARAMSLAYOUT_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the categories table
			if (!$this->checkCategoriesParamsLayout())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCATEGORIESPARAMSLAYOUT_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the countries table
			if (!$this->checkCountriesEnabled())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCOUNTRIESENABLED_FAILED') .' :: '. $this->getError(), 'error' );
			}
			if (!$this->checkCountriesOrdering())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCOUNTRIESORDERING_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// Check order history table
			if (!$this->checkOrderHistory())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERHISTORY_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsShortDesc())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSSHORTDESC_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkTaxclassesOrdering())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKTAXCLASSESORDERING_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrdersOrderNumber())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERSORDERNUMBER_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderInfoZones())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERINFOZONES_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkCurrenciesExchange())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCURRENCIESEXCHANGE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkCartsSessionId())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCARTSSESSIONID_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderZoneAndUser())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERZONEANDUSER_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderCompletedTasks())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERCOMPLETEDTASKS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderQuantitiesUpdated())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERQUANTITIESUPDATED_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrdersOrderShips())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERSORDERSHIPS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the products table
			if (!$this->checkProductsName())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSNAME_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the user info table
			if (!$this->checkUserInfoEmailDropId())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKUSERINFOEMAILDROPID_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the user info table
			if (!$this->checkProductsOrdering())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSORDERING_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			// check the user info table
			if (!$this->checkOrderitemsRecurringPrice())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMSRECURRINGPRICE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsCheckInventory())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSCHECKINVENTORY_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductRelationsExisting())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTRELATIONSEXISTING_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductRelationsType())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTRELATIONSTYPE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkSubscriptionsExpire())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBSCRIPTIONSEXPIRE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsSubscriptions())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSSUBSCRIPTIONS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderItemsSubscriptions())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMSSUBSCRIPTIONS_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductsNotForSale())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSNOTFORSALE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkSubscriptionsCheckFiles())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBSCRIPTIONSCHECKFILES_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsSQL())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSSQL_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductcommentshelpful_votes())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTCOMMENTSHELPFUL_VOTES_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkOrderitemsDiscount())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMSDISCOUNT_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrdershippings())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERSHIPPINGS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductCommentsReported())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTCOMMENTSREPORTED_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductFilesMaxDownload())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTFILESMAXDOWNLOAD_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductsProductListprice())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSPRODUCTLISTPRICE_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductCommentsRatingUpdated())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTCOMMENTSRATINGUPDATED_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkProductsOverallRating())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSOVERALLRATING_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->updateOverallRatings())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_UPDATEOVERALLRATINGS_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkCartParams())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCARTPARAMS_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkOrderitemParams())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMPARAMS_FAILED') .' :: '. $this->getError(), 'error' );
			}

			if (!$this->checkPricesGroupId())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRICESGROUPID_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->updatePriceUserGroups())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_UPDATEPRICEUSERGROUPS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductQuantityLimits())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTQUANTITYLIMITS_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductAttributeOptionCode())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTATTRIBUTEOPTIONCODE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkOrderItemAttributeCode())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMATTRIBUTECODE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkZoneRelationsZipRange())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKZONERELATIONSZIPRANGE_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkProductCommentsUserEmail())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTCOMMENTSUSEREMAIL_FAILED') .' :: '. $this->getError(), 'error' );
			}
			 
			if (!$this->checkCategoriesOrdering())
			{
				return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCATEGORIESORDERING_FAILED') .' :: '. $this->getError(), 'error' );
			}

		}

		if (!$this->checkProductsArticle())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTSARTICLE_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkEavEntityID())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEAVENTITYID_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkEavEditableBy())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEAVEDITABLEBY_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkEavEntityType())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEAVENTITYTYPE_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkProductCommentsUserName())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTCOMMENTSUSERNAME_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkCartsCartId())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCARTSCARTID_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkManufacturersDescParams())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKMANUFACTURERSDESCPARAMS_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkGroupsOrdering())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKMANUFACTURERSDESCPARAMS_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkParentAttributeOption())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPARENTATTRIBUTEOPTION_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkEavRequired())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEAVREQUIRED_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkParentAttributeOption2())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPARENTATTRIBUTEOPTION2_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkEavAlias())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEAVALIAS_FAILED') .' :: '. $this->getError(), 'error' );
		}
		
		if (!$this->checkProRatedSubscriptionProducts())
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRORATEDSUBSCRIPTIONPRODUCTS_FAILED') .' :: '. $this->getError(), 'error' );
		}
		if (!$this->checkProRatedSubscriptionOrderitems() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRORATEDSUBSCRIPTIONORDERITEMS_FAILED') .' :: '. $this->getError(), 'error' );
		}
		
		if (!$this->checkSubscriptionByIssue() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBSCRIPTIONBYISSUE_FAILED') .' :: '. $this->getError(), 'error' );
		}
		
		if (!$this->checkSubNumUserInfo() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBNUMUSERINFO_FAILED') .' :: '. $this->getError(), 'error' );
		}
		if (!$this->checkSubNumSubscriptions() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBNUMSUBSCRIPTIONS_FAILED') .' :: '. $this->getError(), 'error' );
		}		
		
		if (!$this->checkOrderInfoTaxNumber() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERINFOTAXNUMBER_FAILED') .' :: '. $this->getError(), 'error' );
		}		

		if (!$this->checkAddressTaxNumber() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKADDRESSTAXNUMBER_FAILED') .' :: '. $this->getError(), 'error' );
		}		

		if (!$this->checkSubByIssueGtmField() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBBYISSUEGTMFIELD_FAILED') .' :: '. $this->getError(), 'error' );
		}		

		if (!$this->checkCategoryDisplayFields() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKCATEGORYDISPLAYFIELDS_FAILED') .' :: '. $this->getError(), 'error' );
		}		
		
		if (!$this->checkEmptyEavTable() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKEMPTYEAVTABLE_FAILED') .' :: '. $this->getError(), 'error' );
		}		
		
		if (!$this->checkOrderCreditFields() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERCREDITFIELDS_FAILED') .' :: '. $this->getError(), 'error' );
		}		
		
		if (!$this->checkUserInfoCreditFields() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKUSERINFOCREDITFIELDS_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if (!$this->checkProductAttributeOptionBlank() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTATTRIBUTEOPTIONBLANK_FAILED') .' :: '. $this->getError(), 'error' );
		}

		if( !$this->checkLevelTaxes() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKLEVELTAXES_FAILED') .' :: '. $this->getError(), 'error' );
		}
		
		if( !$this->checkOrderitemLevelTaxes() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMLEVELTAXES_FAILED') .' :: '. $this->getError(), 'error' );
		}
		
		if( !$this->checkSecretWord() )
		{
			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSECRETWORD_FAILED') .' :: '. $this->getError(), 'error' );
		}
    
        if( !$this->dropZoneIdOrderInfo() )
        {
    			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_DROPZONEIDORDERINFO_FAILED') .' :: '. $this->getError(), 'error' );    
        }
        
        if( !$this->checkOrderHashField() )
        {
    			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERHASHFIELD_FAILED') .' :: '. $this->getError(), 'error' );    
        }
    
        if( !$this->checkSubtotalMax() )
        {
    			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSUBTOTALMAX_FAILED') .' :: '. $this->getError(), 'error' );    
        }
    
        if( !$this->checkProductAttributesWeight() )
        {
    			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKPRODUCTATTRIBUTESWEIGHT_FAILED') .' :: '. $this->getError(), 'error' );    
        }
    
        if( !$this->checkOrderItemAttributesWeight() )
        {
    			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMATTRIBUTESWEIGHT_FAILED') .' :: '. $this->getError(), 'error' );    
        }
    
        if( !$this->checkOrderItemsWeight() )
        {
   			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKORDERITEMSWEIGHT_FAILED') .' :: '. $this->getError(), 'error' );    
        }

		if( !$this->checkShippingUserGroupRates() )
		{
  			return $this->redirect( JText::_('COM_CITRUSCART_DIAGNOSTIC_CHECKSHIPPINGUSERGROUPRATES_FAILED') .' :: '. $this->getError(), 'error' );			
		}
	}

	/**
	 * Check if a default currencies has been selected,
	 * and if the selected currency really exists
	 * @return boolean
	 */
	function checkDefaultCurrency()
	{
		$default_currencyid = Citruscart::getInstance()->get('default_currencyid', '-1');
		if ($default_currencyid == '-1')
		{
			JError::raiseNotice( 'checkDefaultCurrency', JText::_('COM_CITRUSCART_NO_DEFAULT_CURRENCY_SELECTED') );
			// do not return false here to enable users to actually change the default currency
			return true;
		}
		else
		{
				Citruscart::load('CitruscartHelperCurrency','helpers.currency');
			$currency_helper = new CitruscartHelperCurrency();
			$currency = $currency_helper->load( $default_currencyid );

			// Check if the currency exists
			if ( empty($currency->currency_id) )
			{
				JError::raiseNotice( 'checkDefaultCurrency', JText::_('COM_CITRUSCART_CURRENCY_DOES_NOT_EXISTS') );
				// do not return false here to enable users to actually change the default currency
				return true;
			}
		}
		return true;
	}

	/**
	 * Check if the _productfiles table is correct
	 * This is only necessary if 0.2.0 was ever installed
	 *
	 * @return boolean
	 */
	function checkProductFiles()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductFiles', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productfiles';
		$definitions = array();
		$fields = array();

		$fields[] = "file_id";
		$newnames["file_id"] = "productfile_id";
		$definitions["file_id"] = "int(11) NOT NULL AUTO_INCREMENT";

		$fields[] = "file_name";
		$newnames["file_name"] = "productfile_name";
		$definitions["file_name"] = "varchar(128) NOT NULL DEFAULT ''";

		$fields[] = "file_path";
		$newnames["file_path"] = "productfile_path";
		$definitions["file_path"] = "varchar(255) NOT NULL";

		$fields[] = "file_description";
		$newnames["file_description"] = "productfile_description";
		$definitions["file_description"] = "mediumtext NOT NULL";

		$fields[] = "file_extension";
		$newnames["file_extension"] = "productfile_extension";
		$definitions["file_extension"] = "varchar(6) NOT NULL DEFAULT ''";

		$fields[] = "file_mimetype";
		$newnames["file_mimetype"] = "productfile_mimetype";
		$definitions["file_mimetype"] = "varchar(64) NOT NULL DEFAULT ''";

		$fields[] = "file_url";
		$newnames["file_url"] = "productfile_url";
		$definitions["file_url"] = "varchar(255) NOT NULL DEFAULT ''";

		$fields[] = "file_enabled";
		$newnames["file_enabled"] = "productfile_enabled";
		$definitions["file_enabled"] = "tinyint(1) NOT NULL DEFAULT '0'";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductFiles') );
			$config->config_name = 'checkProductFiles';
			$config->value = '1';
			$config->save();
			return true;
		}

		return false;
	}

	/**
	 * Checks the products table to confirm it has the params and layout fields
	 *
	 * return boolean
	 */
	function checkProductsInventory()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsInventory', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_check_inventory";
		$definitions["product_check_inventory"] = "tinyint(1) DEFAULT '0' COMMENT 'Check Product Inventory?'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsInventory') );
			$config->config_name = 'checkProductsInventory';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orders table to confirm it has the order_currency field
	 *
	 * return boolean
	 */
	function checkOrdersOrderCurrency()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrdersOrderCurrency', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();

		$fields[] = "order_currency";
		$newnames["order_currency"] = "order_currency";
		$definitions["order_currency"] = "TEXT NOT NULL COMMENT 'Stores a DSCParameter formatted version of the current currency. Used to maintain the order integrity'";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrdersOrderCurrency') );
			$config->config_name = 'checkOrdersOrderCurrency';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Confirm that the category root is properly named and doesn't yell at users
	 * return boolean
	 */
	function checkCategoriesRootDesc()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCategoriesRootDesc', '0'))
		{
			return true;
		}

		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$category = JTable::getInstance( 'Categories', 'CitruscartTable' );
		$root = $category->getRoot();

		if ($root->category_name == "ROOT" || $root->category_description == "root" || !empty($root->category_description))
		{
			$category->load( $root->category_id );
			$category->category_name = "All Categories";
			$category->category_description = "";
			if (!$category->save())
			{
				return false;
			}
		}

		// Update config to say this has been done already
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$config = JTable::getInstance( 'Config', 'CitruscartTable' );
		$config->load( array( 'config_name'=>'checkCategoriesRootDesc') );
		$config->config_name = 'checkCategoriesRootDesc';
		$config->value = '1';
		$config->save();
		return true;
	}

	/**
	 * Checks the products table to confirm it has the params and layout fields
	 *
	 * return boolean
	 */
	function checkProductsParamsLayout()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsParamsLayout', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_params";
		$definitions["product_params"] = "text";

		$fields[] = "product_layout";
		$definitions["product_layout"] = "varchar(255) DEFAULT '' COMMENT 'The layout file for this product'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsParamsLayout') );
			$config->config_name = 'checkProductsParamsLayout';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the categories table to confirm it has the layout field
	 *
	 * return boolean
	 */
	function checkCategoriesParamsLayout()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCategoriesParamsLayout', '0'))
		{
			return true;
		}

		$table = '#__citruscart_categories';
		$definitions = array();
		$fields = array();

		$fields[] = "category_params";
		$definitions["category_params"] = "text";

		$fields[] = "category_layout";
		$definitions["category_layout"] = "varchar(255) DEFAULT '' COMMENT 'The layout file for this category'";

		$fields[] = "categoryproducts_layout";
		$definitions["categoryproducts_layout"] = "varchar(255) DEFAULT '' COMMENT 'The layout file for all products in this category'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCategoriesParamsLayout') );
			$config->config_name = 'checkCategoriesParamsLayout';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the countries table to confirm it has the enabled field
	 *
	 * return boolean
	 */
	function checkCountriesEnabled()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCountriesEnabled', '0'))
		{
			return true;
		}

		$table = '#__citruscart_countries';
		$definitions = array();
		$fields = array();

		$fields[] = "country_enabled";
		$definitions["country_enabled"] = "TINYINT(1) NOT NULL DEFAULT '1'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCountriesEnabled') );
			$config->config_name = 'checkCountriesEnabled';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the countries table to confirm it has the ordering field
	 *
	 * return boolean
	 */
	function checkCountriesOrdering()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCountriesOrdering', '0'))
		{
			return true;
		}

		$table = '#__citruscart_countries';
		$definitions = array();
		$fields = array();

		$fields[] = "ordering";
		$definitions["ordering"] = "int(11) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );

			// also fix the ordering values for the countries
			$country = JTable::getInstance( 'Countries', 'CitruscartTable' );
			$country->reorder();

			// Update config to say this has been done already
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCountriesOrdering') );
			$config->config_name = 'checkCountriesOrdering';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orderhistory table to confirm it has the notify_customer field change
	 *
	 * return boolean
	 */
	function checkOrderHistory()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderHistory', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderhistory';
		$definitions = array();
		$fields = array();

		$fields[] = "customer_notified";
		$newnames["customer_notified"] = "notify_customer";
		$definitions["customer_notified"] = "TINYINT(1) NOT NULL DEFAULT '1'";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );

			// Update config to say this has been done already
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderHistory') );
			$config->config_name = 'checkOrderHistory';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 *
	 * return boolean
	 */
	function checkProductsShortDesc()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsShortDesc', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_description_short";
		$definitions["product_description_short"] = "text";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );

			// Update config to say this has been done already
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsShortDesc') );
			$config->config_name = 'checkProductsShortDesc';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the countries table to confirm it has the ordering field
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkTaxclassesOrdering()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkTaxclassesOrdering', '0'))
		{
			return true;
		}

		$table = '#__citruscart_taxclasses';
		$definitions = array();
		$fields = array();

		$fields[] = "ordering";
		$definitions["ordering"] = "int(11) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );

			// also fix the ordering values
			$country = JTable::getInstance( 'Taxclasses', 'CitruscartTable' );
			$country->reorder();

			// Update config to say this has been done already
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkTaxclassesOrdering') );
			$config->config_name = 'checkTaxclassesOrdering';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orders table for the order_number field
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrdersOrderNumber()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrdersOrderNumber', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();

		$fields[] = "order_number";
		$definitions["order_number"] = "varchar(255) DEFAULT '' COMMENT 'The Invoice Number that Can be Set by Admins'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrdersOrderNumber') );
			$config->config_name = 'checkOrdersOrderNumber';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orderinfo table for the zones fields
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrderInfoZones()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderInfoZones', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderinfo';
		$definitions = array();
		$fields = array();

		$fields[] = "billing_zone_id";
		$definitions["billing_zone_id"] = "int(11) NOT NULL DEFAULT '0'";

		$fields[] = "billing_country_id";
		$definitions["billing_country_id"] = "int(11) NOT NULL DEFAULT '0'";

		$fields[] = "shipping_zone_id";
		$definitions["shipping_zone_id"] = "int(11) NOT NULL DEFAULT '0'";

		$fields[] = "shipping_country_id";
		$definitions["shipping_country_id"] = "int(11) NOT NULL DEFAULT '0'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderInfoZones') );
			$config->config_name = 'checkOrderInfoZones';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the currencies table for the exchange fields
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkCurrenciesExchange()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCurrenciesExchange', '0'))
		{
			return true;
		}

		$table = '#__citruscart_currencies';
		$definitions = array();
		$fields = array();

		$fields[] = "updated_date";
		$definitions["updated_date"] = "datetime NOT NULL COMMENT 'The last time the currency was updated'";

		$fields[] = "exchange_rate";
		$definitions["exchange_rate"] = "DECIMAL(15,8) NOT NULL DEFAULT '0.00000000' COMMENT 'Value of currency in USD'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCurrenciesExchange') );
			$config->config_name = 'checkCurrenciesExchange';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the carts table for missing fields
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkCartsSessionId()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCartsSessionId', '0'))
		{
			return true;
		}

		$table = '#__citruscart_carts';
		$definitions = array();
		$fields = array();

		$fields[] = "session_id";
		$definitions["session_id"] = "VARCHAR(200) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCartsSessionId') );
			$config->config_name = 'checkCartsSessionId';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}


	/**
	 * Checks the orderinfo table for the zone id and the user id
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrderZoneAndUser()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderZoneAndUser', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderinfo';
		$definitions = array();
		$fields = array();

		$fields[] = "zone_id";
		$definitions["zone_id"] = "int(11) NULL DEFAULT '0'";
		 
		$fields[] = "user_id";
		$definitions["user_id"] = "int(11)NULL DEFAULT '0'";
		 

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderZoneAndUser') );
			$config->config_name = 'checkOrderZoneAndUser';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orders table for the completed_tasks
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrderCompletedTasks()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderCompletedTasks', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();

		$fields[] = "completed_tasks";
		$definitions["completed_tasks"] = "TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Were the OrderCompleted tasks executed?'";
		 
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderCompletedTasks') );
			$config->config_name = 'checkOrderCompletedTasks';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orders table for the quantities_updated
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrderQuantitiesUpdated()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderQuantitiesUpdated', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();

		$fields[] = "quantities_updated";
		$definitions["quantities_updated"] = "TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Were the Product Quantities updated?'";
		 
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderQuantitiesUpdated') );
			$config->config_name = 'checkOrderQuantitiesUpdated';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orders table for the order_ships
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkOrdersOrderShips()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrdersOrderShips', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();

		$fields[] = "order_ships";
		$definitions["order_ships"] = "TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Does this order require shipping?'";
		 
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrdersOrderShips') );
			$config->config_name = 'checkOrdersOrderShips';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}


	/**
	 * Check if the _product table is correct
	 *
	 * @return boolean
	 */
	function checkProductsName()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsName', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_name";
		$newnames["product_name"] = "product_name";
		$definitions["product_name"] =" varchar(255) NOT NULL DEFAULT ''";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsName') );
			$config->config_name = 'checkProductsName';
			$config->value = '1';
			$config->save();
			return true;
		}


		return false;
	}

	/**
	 * Checks the user info table for the emailid fields
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkUserInfoEmailDropId()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkUserInfoEmailDropId', '0'))
		{
			return true;
		}

		$table = '#__citruscart_userinfo';
		$definitions = array();
		$fields = array();
		$newnames = array();

		$fields[] = "emailId";
		$newnames["emailId"] = "email";
		$definitions["emailId"] =" varchar(255) NOT NULL DEFAULT ''";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{

		}

		// this will only execute a change if email doesn't exist
		$definitions = array();
		$fields = array();

		$fields[] = "email";
		$definitions["email"] = "varchar(255) NOT NULL DEFAULT ''";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkUserInfoEmailDropId') );
			$config->config_name = 'checkUserInfoEmailDropId';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the products table for the ordering field
	 * As of v0.5.0
	 *
	 * return boolean
	 */
	function checkProductsOrdering()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsOrdering', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "ordering";
		$definitions["ordering"] = "int(11) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsOrdering') );
			$config->config_name = 'checkProductsOrdering';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orderitems table for the recurring_price field
	 * As of v0.5.2
	 *
	 * return boolean
	 */
	function checkOrderitemsRecurringPrice()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderitemsRecurringPrice', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderitems';
		$definitions = array();
		$fields = array();

		$fields[] = "recurring_price";
		$definitions["recurring_price"] = "decimal(15,5) NOT NULL DEFAULT '0.00000' COMMENT 'Recurring price of the item'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderitemsRecurringPrice') );
			$config->config_name = 'checkOrderitemsRecurringPrice';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _products table is correct
	 * As of v0.5.2
	 *
	 * @return boolean
	 */
	function checkProductsCheckInventory()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsCheckInventory', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_check_inventory";
		$newnames["product_check_inventory"] = "product_check_inventory";
		$definitions["product_check_inventory"] = "tinyint(1) DEFAULT '0' COMMENT 'Check Inventory for this Product?'";

		$fields[] = "product_ships";
		$newnames["product_ships"] = "product_ships";
		$definitions["product_ships"] = "tinyint(1) DEFAULT '0' COMMENT 'Product Requires Shipping?'";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsCheckInventory') );
			$config->config_name = 'checkProductsCheckInventory';
			$config->value = '1';
			$config->save();
			return true;
		}

		return false;
	}

	/**
	 * Check if the _productrelations table is correct
	 * As of v0.5.3
	 *
	 * @return boolean
	 */
	function checkProductRelationsExisting()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductRelationsExisting', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productrelations';

		$db = JFactory::getDBO();
		$query = " SHOW COLUMNS FROM {$table} LIKE 'productrelation_id' ";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($rows)
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductRelationsExisting') );
			$config->config_name = 'checkProductRelationsExisting';
			$config->value = '1';
			$config->save();
			return true;
		}

		$query = "DROP TABLE `{$table}`";
		$db->setQuery( $query );
		if (!$db->query())
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}

		$query = "
        CREATE TABLE IF NOT EXISTS `#__citruscart_productrelations` (
          `productrelation_id` int(11) NOT NULL AUTO_INCREMENT,
          `product_id_from` INT(11) NOT NULL DEFAULT '0' ,
          `product_id_to` INT(11) NOT NULL DEFAULT '0' ,
          `relation_type` VARCHAR(64) NOT NULL DEFAULT '' ,
          PRIMARY KEY (`productrelation_id`) ,
          INDEX `fk_Product_ProductRelationsA` (`product_id_from` ASC) ,
          INDEX `fk_Product_ProductRelationsB` (`product_id_to` ASC) ,
          CONSTRAINT `fk_Product_ProductRelationsA`
            FOREIGN KEY (`product_id_from` )
            REFERENCES `#__citruscart_products` (`product_id` )
            ON DELETE CASCADE
            ON UPDATE CASCADE,
          CONSTRAINT `fk_Product_ProductRelationsB`
            FOREIGN KEY (`product_id_to` )
            REFERENCES `#__citruscart_products` (`product_id` )
            ON DELETE CASCADE
            ON UPDATE CASCADE)
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8;
        ";

		$db->setQuery( $query );
		if ($db->query())
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductRelationsExisting') );
			$config->config_name = 'checkProductRelationsExisting';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _productrelations table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkProductRelationsType()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductRelationsType', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productrelations';
		$definitions = array();
		$fields = array();

		$fields[] = "relation_type";
		$definitions["relation_type"] = "VARCHAR(64) NOT NULL DEFAULT ''";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductRelationsType') );
			$config->config_name = 'checkProductRelationsType';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _subscriptions table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkSubscriptionsExpire()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubscriptionsExpire', '0'))
		{
			return true;
		}

		$table = '#__citruscart_subscriptions';
		$definitions = array();
		$fields = array();

		$fields[] = "lifetime_enabled";
		$definitions["lifetime_enabled"] = "tinyint(1) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubscriptionsExpire') );
			$config->config_name = 'checkSubscriptionsExpire';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _products table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkProductsSubscriptions()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsSubscriptions', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_subscription";
		$definitions["product_subscription"] = "tinyint(1) NOT NULL COMMENT 'Product creates a subscription?'";

		$fields[] = "subscription_lifetime";
		$definitions["subscription_lifetime"] = "tinyint(1) NOT NULL COMMENT 'Lifetime subscription?'";

		$fields[] = "subscription_period_interval";
		$definitions["subscription_period_interval"] = "int(3) NOT NULL COMMENT 'How many period-units does the subscription last?'";

		$fields[] = "subscription_period_unit";
		$definitions["subscription_period_unit"] = "varchar(1) NOT NULL COMMENT 'D, W, M, Y = Day, Week, Month, Year'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsSubscriptions') );
			$config->config_name = 'checkProductsSubscriptions';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _orderitems table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkOrderItemsSubscriptions()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderItemsSubscriptions', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderitems';
		$definitions = array();
		$fields = array();

		$fields[] = "orderitem_subscription";
		$definitions["orderitem_subscription"] = "tinyint(1) NOT NULL COMMENT 'Orderitem creates a subscription?'";

		$fields[] = "subscription_lifetime";
		$definitions["subscription_lifetime"] = "tinyint(1) NOT NULL COMMENT 'Lifetime subscription?'";

		$fields[] = "subscription_period_interval";
		$definitions["subscription_period_interval"] = "int(3) NOT NULL COMMENT 'How many period-units does the subscription last?'";

		$fields[] = "subscription_period_unit";
		$definitions["subscription_period_unit"] = "varchar(1) NOT NULL COMMENT 'D, W, M, Y = Day, Week, Month, Year'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderItemsSubscriptions') );
			$config->config_name = 'checkOrderItemsSubscriptions';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _products table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkProductsNotForSale()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsNotForSale', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_notforsale";
		$definitions["product_notforsale"] = "tinyint(1) NOT NULL";

		$fields[] = "quantity_restriction";
		$definitions["quantity_restriction"] = "tinyint(1) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsNotForSale') );
			$config->config_name = 'checkProductsNotForSale';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _subscriptions table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkSubscriptionsCheckFiles()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubscriptionsCheckFiles', '0'))
		{
			return true;
		}

		$table = '#__citruscart_subscriptions';
		$definitions = array();
		$fields = array();

		$fields[] = "checkedfiles_datetime";
		$definitions["checkedfiles_datetime"] = "datetime NOT NULL COMMENT 'When were this subscriptions files last checked?'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubscriptionsCheckFiles') );
			$config->config_name = 'checkSubscriptionsCheckFiles';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Check if the _subscriptions table is correct
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkProductsSQL()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsSQL', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_sql";
		$definitions["product_sql"] = "text NOT NULL COMMENT 'SQL queries to be executed after the product is purchased'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsSQL') );
			$config->config_name = 'checkProductsSQL';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * checks Productcomment table for helpful_votes and helpful_votes_total
	 * @return unknown_type
	 */
	function checkProductcommentshelpful_votes()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductcommentshelpful_votes', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productcomments';
		$definitions = array();
		$fields = array();

		$fields[] = "helpful_votes";
		$definitions["helpful_votes"] = "int(11) NOT NULL DEFAULT '0'";

		$fields[] = "helpful_votes_total";
		$definitions["helpful_votes_total"] = "int(11) NOT NULL DEFAULT '0'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductcommentshelpful_votes') );
			$config->config_name = 'checkProductcommentshelpful_votes';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orderitems table for the recurring_price field
	 * As of v0.5.3
	 *
	 * return boolean
	 */
	function checkOrderitemsDiscount()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderitemsDiscount', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderitems';
		$definitions = array();
		$fields = array();

		$fields[] = "orderitem_discount";
		$definitions["orderitem_discount"] = "decimal(15,5) NOT NULL DEFAULT '0.00000' COMMENT 'Coupon discount applied to each item'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderitemsDiscount') );
			$config->config_name = 'checkOrderitemsDiscount';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the ordershippings table for the ordershipping_code and ordershipping_tracking_id fields
	 * As of v0.5.5
	 *
	 * return boolean
	 */
	function checkOrdershippings()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrdershippings', '0'))
		{
			return true;
		}

		$table = '#__citruscart_ordershippings';
		$definitions = array();
		$fields = array();

		$fields[] = "ordershipping_code";
		$definitions["ordershipping_code"] = "VARCHAR(255) NOT NULL DEFAULT ''";

		$fields[] = "ordershipping_tracking_id";
		$definitions["ordershipping_tracking_id"] = "mediumtext NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrdershippings') );
			$config->config_name = 'checkOrdershippings';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the productcomments table for the reported_count fields
	 * As of v0.5.5
	 *
	 * return boolean
	 */
	function checkProductCommentsReported()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductCommentsReported', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productcomments';
		$definitions = array();
		$fields = array();

		$fields[] = "reported_count";
		$definitions["reported_count"] = "int(11) NOT NULL DEFAULT '0'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductCommentsReported') );
			$config->config_name = 'checkProductCommentsReported';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the productfiles table for the max_download field
	 * As of v0.5.2
	 *
	 * return boolean
	 */
	function checkProductFilesMaxDownload()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductFilesMaxDownload', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productfiles';
		$definitions = array();
		$fields = array();

		$fields[] = "max_download";
		$definitions["max_download"] = "INT NULL DEFAULT '-1'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductFilesMaxDownload') );
			$config->config_name = 'checkProductFilesMaxDownload';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the products table for the product_cost field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductsProductListprice()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsProductListprice', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_listprice";
		$definitions["product_listprice"] = "decimal(15,5) NOT NULL DEFAULT '0.00000' ";

		$fields[] = "product_listprice_enabled";
		$definitions["product_listprice_enabled"] = "tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Display the product_listprice field?'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsProductListprice') );
			$config->config_name = 'checkProductsProductListprice';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the productcomments table for the rating_updated field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductCommentsRatingUpdated()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductCommentsRatingUpdated', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productcomments';
		$definitions = array();
		$fields = array();

		$fields[] = "rating_updated";
		$definitions["rating_updated"] = "tinyint(1) NOT NULL COMMENT 'Was the product overall rating updated?' ";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductCommentsRatingUpdated') );
			$config->config_name = 'checkProductCommentsRatingUpdated';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the products table for the product_ratings fields
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductsOverallRating()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsOverallRating', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_rating";
		$definitions["product_rating"] = "decimal(15,5) NOT NULL DEFAULT '0.00000' COMMENT 'The overall rating for the product. Is x out of 5'";

		$fields[] = "product_comments";
		$definitions["product_comments"] = "int(11) NOT NULL DEFAULT '0' COMMENT 'The number of enabled comments the product has'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsOverallRating') );
			$config->config_name = 'checkProductsOverallRating';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * for every product that has received a comment, update its overall rating fields
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function updateOverallRatings()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('updateOverallRatings', '0'))
		{
			return true;
		}

		Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
		$helper = new CitruscartHelperProduct();

		if ($helper->updateOverallRatings())
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'updateOverallRatings') );
			$config->config_name = 'updateOverallRatings';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the carts table for the params field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkCartParams()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCartParams', '0'))
		{
			return true;
		}

		$table = '#__citruscart_carts';
		$definitions = array();
		$fields = array();

		$fields[] = "cartitem_params";
		$definitions["cartitem_params"] = "TEXT COMMENT 'Params for the cart item'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCartParams') );
			$config->config_name = 'checkCartParams';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the orderitems table for the params field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkOrderitemParams()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderitemParams', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderitems';
		$definitions = array();
		$fields = array();

		$fields[] = "orderitem_params";
		$definitions["orderitem_params"] = "TEXT COMMENT 'Params for the orderitem'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderitemParams') );
			$config->config_name = 'checkOrderitemParams';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks the prices tbl for the group_id field
	 *
	 * return boolean
	 */
	function checkPricesGroupId()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkPricesGroupId', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productprices';
		$definitions = array();
		$fields = array();

		$fields[] = "user_group_id";
		$newnames["user_group_id"] = "group_id";
		$definitions["user_group_id"] = "int(11) NOT NULL";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkPricesGroupId') );
			$config->config_name = 'checkPricesGroupId';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update the productprices default user group
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function updatePriceUserGroups()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('updatePriceUserGroups', '0'))
		{
			return true;
		}

		Citruscart::load( 'CitruscartHelperProduct', 'helpers.product' );
		$helper = new CitruscartHelperProduct();

		if ($helper->updatePriceUserGroups())
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'updatePriceUserGroups') );
			$config->config_name = 'updatePriceUserGroups';
			$config->value = '1';
			$config->save();
			return true;
		}

		return false;
	}

	/**
	 * update the products table for quantity min, max & step
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductQuantityLimits()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductQuantityLimits', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "quantity_min";
		$definitions["quantity_min"] = "INT(11) DEFAULT NULL";
		$fields[] = "quantity_max";
		$definitions["quantity_max"] = "INT(11) DEFAULT NULL";
		$fields[] = "quantity_step";
		$definitions["quantity_step"] = "INT(11) DEFAULT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductQuantityLimits') );
			$config->config_name = 'checkProductQuantityLimits';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update the orderitemattributes for the "code" field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkOrderItemAttributeCode()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderItemAttributeCode', '0'))
		{
			return true;
		}

		$table = '#__citruscart_orderitemattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "orderitemattribute_code";
		$definitions["orderitemattribute_code"] = "VARCHAR(255) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderItemAttributeCode') );
			$config->config_name = 'checkOrderItemAttributeCode';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update the product attribute option for the "code" field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductAttributeOptionCode()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductAttributeOptionCode', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productattributeoptions';
		$definitions = array();
		$fields = array();

		$fields[] = "productattributeoption_code";
		$definitions["productattributeoption_code"] = "VARCHAR(255) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductAttributeOptionCode') );
			$config->config_name = 'checkProductAttributeOptionCode';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update the zonerelations table for the "zip_range" field
	 * As of v0.6.0
	 *
	 * return boolean
	 */
	function checkZoneRelationsZipRange()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkZoneRelationsZipRange', '0'))
		{
			return true;
		}

		$table = '#__citruscart_zonerelations';
		$definitions = array();
		$fields = array();

		$fields[] = "zip_range";
		$definitions["zip_range"] = "VARCHAR(255) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkZoneRelationsZipRange') );
			$config->config_name = 'checkZoneRelationsZipRange';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update #__citruscart_productcomments table for the "user_email" field
	 * As of v0.5.6
	 *
	 * return boolean
	 */
	function checkProductCommentsUserEmail()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductCommentsUserEmail', '0'))
		{
			return true;
		}

		$database = JFactory::getDBO();
		$table = '#__citruscart_productcomments';
		$fieldEmail = "user_email";
		$fieldUserId = "user_id";
		$fieldProductid = "product_id";
		$definition= "VARCHAR( 255 ) NULL DEFAULT NULL";

		//check if user_email column already exist
		$database->setQuery( " SHOW COLUMNS FROM {$table} LIKE '{$fieldEmail}' " );
		$rows = $database->loadObjectList();

		$errors = '';
		if (!$rows && !$database->getErrorNum())
		{
			$query = "";
			$query .= "ALTER TABLE `{$table}` DROP INDEX `{$fieldProductid}`, ";
			$query .= "ADD `{$fieldEmail}` {$definition} AFTER `{$fieldUserId}`,";
			$query .= "ADD INDEX `{$fieldUserId}` ( `{$fieldUserId}` )";
			$database->setQuery( $query );

			if (!$database->query()) $errors = $database->getErrorMsg();
		}

		 
		if (!empty($errors))
		{
			$this->setError( implode('<br/>', $errors) );
			return false;
		}
		 
		// Update config to say this has been done already
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$config = JTable::getInstance( 'Config', 'CitruscartTable' );
		$config->load( array( 'config_name'=>'checkProductCommentsUserEmail') );
		$config->config_name = 'checkProductCommentsUserEmail';
		$config->value = '1';
		$config->save();
		return true;
	}

	/**
	 * update the categories table for the "ordering" field
	 * As of v0.6.0
	 *
	 * return boolean
	 */
	function checkCategoriesOrdering()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCategoriesOrdering', '0'))
		{
			return true;
		}

		$table = '#__citruscart_categories';
		$definitions = array();
		$fields = array();

		$fields[] = "ordering";
		$definitions["ordering"] = "INT(11) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCategoriesOrdering') );
			$config->config_name = 'checkCategoriesOrdering';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update the products table for the "product_article" field
	 * As of v0.6.1
	 *
	 * return boolean
	 */
	function checkProductsArticle()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductsArticle', '0'))
		{
			return true;
		}

		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "product_article";
		$definitions["product_article"] = "INT(11) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductsArticle') );
			$config->config_name = 'checkProductsArticle';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function checkEavEntityID()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEavEntityID', '0'))
		{
			return true;
		}

		$table = '#__citruscart_eavattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "eaventity_id";
		$definitions["eaventity_id"] = "int(11) NOT NULL COMMENT 'PK of the entity we are extending'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEavEntityID') );
			$config->config_name = 'checkEavEntityID';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function checkEavEditableBy()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEavEditableBy', '0'))
		{
			return true;
		}

		$table = '#__citruscart_eavattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "editable_by";
		$definitions["editable_by"] = "tinyint(1) NOT NULL COMMENT '0=no one, 1=admin, 2=user'";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEavEditableBy') );
			$config->config_name = 'checkEavEditableBy';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function checkEavEntityType()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEavEntityType', '0'))
		{
			return true;
		}

		$tables = array('varchar', 'text', 'int', 'decimal', 'datetime');

		$error = false;
		foreach($tables as $t)
		{
			$table = '#__citruscart_eavvalues' . $t;
			$definitions = array();
			$fields = array();
			 
			$fields[] = "eaventity_type";
			$definitions["eaventity_type"] = "VARCHAR( 255 ) NOT NULL COMMENT 'table name of the entity'";
			 
			if (!$this->insertTableFields( $table, $fields, $definitions ))
			{
				$error = true;
			}
		}

		if(!$error)
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEavEntityType') );
			$config->config_name = 'checkEavEntityType';
			$config->value = '1';
			$config->save();
			return true;
		}

		return false;
	}

	/**
	 * update the carts table for the "cart_id" field
	 * As of v0.6.3
	 *
	 * return boolean
	 */
	function checkCartsCartId()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCartsCartId', '0'))
		{
			return true;
		}

		$table = '#__citruscart_carts';
		$definitions = array();
		$fields = array();

		$fields[] = "cart_id";
		$definitions["cart_id"] = "INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCartsCartId') );
			$config->config_name = 'checkCartsCartId';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update #__citruscart_productcomments table for the "user_name" field
	 * As of v0.6.3
	 *
	 * return boolean
	 */
	function checkProductCommentsUserName()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductCommentsUserName', '0')) return true;
		 
		$table = '#__citruscart_productcomments';
		$definitions = array();
		$fields = array();

		$fields[] = "user_name";
		$definitions["user_name"] = "VARCHAR(255) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
 		{
      // Update config to say this has been done already
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
  		$config = JTable::getInstance( 'Config', 'CitruscartTable' );
  		$config->load( array( 'config_name'=>'checkProductCommentsUserName') );
  		$config->config_name = 'checkProductCommentsUserName';
  		$config->value = '1';
  		$config->save();
  		return true;
		}
		return false;
	}

	/**
	 * update the manufacturers table for the "description" and "params" fields
	 * As of v0.6.3
	 *
	 * return boolean
	 */
	function checkManufacturersDescParams()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkManufacturersDescParams', '0'))
		{
			return true;
		}

		$table = '#__citruscart_manufacturers';
		$definitions = array();
		$fields = array();

		$fields[] = "manufacturer_params";
		$definitions["manufacturer_params"] = "text";

		$fields[] = "manufacturer_description";
		$definitions["manufacturer_description"] = "text";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkManufacturersDescParams') );
			$config->config_name = 'checkManufacturersDescParams';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update #__citruscart_groups table for the "ordering" field
	 * As of v0.6.3
	 *
	 * return boolean
	 */
	function checkGroupsOrdering()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkGroupsOrdering', '0')) return true;
		 
		$table = '#__citruscart_groups';
		$definitions = array();
		$fields = array();

		$fields[] = "ordering";
		$definitions["ordering"] = "INT( 11 ) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkGroupsOrdering') );
			$config->config_name = 'checkGroupsOrdering';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update #__citruscart_productattributes table for the "parent_productattributeoption_id" field
	 * As of v0.6.5
	 *
	 * return boolean
	 */
	function checkParentAttributeOption()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkParentAttributeOption', '0')) return true;
		 
		$table = '#__citruscart_productattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "parent_productattributeoption_id";
		$definitions["parent_productattributeoption_id"] = "INT( 11 ) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkParentAttributeOption') );
			$config->config_name = 'checkParentAttributeOption';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * update #__citruscart_productattributeoptions table for the "parent_productattributeoption_id" field
	 * As of v0.7.0
	 *
	 * return boolean
	 */
	function checkParentAttributeOption2()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkParentAttributeOption2', '0')) return true;
		 
		$table = '#__citruscart_productattributeoptions';
		$definitions = array();
		$fields = array();

		$fields[] = "parent_productattributeoption_id";
		$definitions["parent_productattributeoption_id"] = "INT( 11 ) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkParentAttributeOption2') );
			$config->config_name = 'checkParentAttributeOption2';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function checkEavRequired()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEavRequired', '0'))
		{
			return true;
		}

		$table = '#__citruscart_eavattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "eavattribute_required";
		$definitions["eavattribute_required"] = "tinyint(1) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEavRequired') );
			$config->config_name = 'checkEavRequired';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Enter description here ...
	 * @return unknown_type
	 */
	function checkEavAlias()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEavAlias', '0'))
		{
			return true;
		}

		$table = '#__citruscart_eavattributes';
		$definitions = array();
		$fields = array();

		$fields[] = "eavattribute_alias";
		$definitions["eavattribute_alias"] = "varchar(255) NOT NULL";

		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEavAlias') );
			$config->config_name = 'checkEavAlias';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in products table) for pro-rated subscription
	 * @version 0.7.3
	 * @return unknown_type
	 */
	function checkProRatedSubscriptionProducts()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProRatedSubscriptionProducts', '0')) return true;
		 
		$table = '#__citruscart_products';
		$definitions = array();
		$fields = array();

		$fields[] = "subscription_prorated";
		$fields[] = "subscription_prorated_date";
		$fields[] = "subscription_prorated_charge";
		$fields[] = "subscription_prorated_term";

		$definitions["subscription_prorated"] = "TINYINT( 1 ) NOT NULL DEFAULT  '0'";
		$definitions["subscription_prorated_date"] = "VARCHAR( 5 ) NULL DEFAULT NULL";
		$definitions["subscription_prorated_charge"] = "TINYINT( 1 ) NOT NULL DEFAULT  '1'";
		$definitions["subscription_prorated_term"] = "VARCHAR( 1 ) NOT NULL DEFAULT  'D'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProRatedSubscriptionProducts') );
			$config->config_name = 'checkProRatedSubscriptionProducts';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in Orderitem table) for pro-rated subscription
	 * @version 0.7.3
	 * @return unknown_type
	 */
	function checkProRatedSubscriptionOrderitems()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProRatedSubscriptionOrderitems', '0')) return true;
		 
		$table = '#__citruscart_orderitems';
		$definitions = array();
		$fields = array();

		$fields[] = "subscription_prorated";

		$definitions["subscription_prorated"] = "TINYINT( 1 ) NOT NULL DEFAULT  '0'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProRatedSubscriptionOrderitems') );
			$config->config_name = 'checkProRatedSubscriptionOrderitems';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in Subscriptions table) for subscription by issue
	 * @version 0.7.3
	 * @return unknown_type
	 */
	function checkSubscriptionByIssue()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubscriptionByIssue', '0')) return true;
		 
		$table = '#__citruscart_subscriptions';
		$definitions = array();
		$fields = array();

		$fields[] = "subscription_issue_end_id";

		$definitions["subscription_issue_end_id"] = "INT NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubscriptionByIssue') );
			$config->config_name = 'checkSubscriptionByIssue';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in UserInfo table) for subscription number
	 * @version 0.7.4
	 * @return unknown_type
	 */
	function checkSubNumUserInfo()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubNumUserInfo', '0')) return true;
		 
		$table = '#__citruscart_userinfo';
		$definitions = array();
		$fields = array();

		$fields[] = "sub_number";

		$definitions["sub_number"] = "INT NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubNumUserInfo') );
			$config->config_name = 'checkSubNumUserInfo';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in UserInfo table) for subscription number
	 * @version 0.7.4
	 * @return unknown_type
	 */
	function checkSubNumSubscriptions()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubNumSubscriptions', '0')) return true;
		 
		$table = '#__citruscart_subscriptions';
		$definitions = array();
		$fields = array();

		$fields[] = "sub_number";

		$definitions["sub_number"] = "INT NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubNumSubscriptions') );
			$config->config_name = 'checkSubNumSubscriptions';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in Addresses table) for Company Tax Number
	 * @version 0.7.4
	 * @return unknown_type
	 */
	function checkAddressTaxNumber()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkAddressTaxNumber', '0')) return true;
		 
		$table = '#__citruscart_addresses';
		$definitions = array();
		$fields = array();

		$fields[] = "tax_number";

		$definitions["tax_number"] = "VARCHAR( 32 ) NOT NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkAddressTaxNumber') );
			$config->config_name = 'checkAddressTaxNumber';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in OrderInfo table) for Company Tax Number
	 * @version 0.7.4
	 * @return unknown_type
	 */
	function checkOrderInfoTaxNumber()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderInfoTaxNumber', '0')) return true;
		 
		$table = '#__citruscart_orderinfo';
		$definitions = array();
		$fields = array();

		$fields[] = "billing_tax_number";
		$fields[] = "shipping_tax_number";
		
		$definitions["billing_tax_number"] = "VARCHAR( 32 ) NULL";
		$definitions["shipping_tax_number"] = "VARCHAR( 32 ) NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderInfoTaxNumber') );
			$config->config_name = 'checkOrderInfoTaxNumber';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}
	
	/**
	 * Change of type of a field to support time zones in subscription by issue
	 * @version 0.8.0
	 * @return unknown_type
	 */
	function checkSubByIssueGtmField()
	{
		// if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubByIssueGtmField', '0'))
		{
			return true;
		}

		$table = '#__citruscart_productissues';
		$definitions = array();
		$fields = array();

		$fields[] = "publishing_date";
		$newnames["publishing_date"] = "publishing_date";
		$definitions["publishing_date"] = "DATETIME NOT NULL";

		if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkSubByIssueGtmField') );
			$config->config_name = 'checkSubByIssueGtmField';
			$config->value = '1';
			$config->save();
			return true;
		}

		return false;		
	}

	/**
	 *
	 * Additional fields (in Categories table) for hiding category names in listing
	 * @version 0.8.1
	 * @return unknown_type
	 */
	function checkCategoryDisplayFields()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkCategoryDisplayFields', '0')) return true;
		 
		$table = '#__citruscart_categories';
		$definitions = array();
		$fields = array();

		$fields[] = "display_name_category";
		$fields[] = "display_name_subcategory";
		
		$definitions["display_name_category"] = "TINYINT NOT NULL DEFAULT  '1'";
		$definitions["display_name_subcategory"] = "TINYINT NOT NULL DEFAULT  '1'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkCategoryDisplayFields') );
			$config->config_name = 'checkCategoryDisplayFields';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Empty all EAV values, because they might be wrong cached values
	 * @version 0.8.2
	 * @return unknown_type
	 */
	function checkEmptyEavTable()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkEmptyEavTable', '0')) return true;
		 
		$db = JFactory::getDbo();
		$tables = array( '#__citruscart_eavvaluesdatetime', '#__citruscart_eavvaluesdecimal', '#__citruscart_eavvaluesint', '#__citruscart_eavvaluestext', '#__citruscart_eavvaluesvarchar' );		
		$ok = true;
		for( $i = 0, $c = count( $tables ); $ok && $i < $c; $i++ )
		{
			$db->setQuery( 'TRUNCATE TABLE `'.$tables[$i].'`' );
			$ok = $db->query() !== false;
		}
		if ( $ok )
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkEmptyEavTable') );
			$config->config_name = 'checkEmptyEavTable';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}
	
	/**
	 *
	 * Additional fields (in Orders table) for order credits
	 * @version 0.8.2
	 * @return boolean
	 */
	function checkOrderCreditFields()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderCreditFields', '0')) return true;
		 
		$table = '#__citruscart_orders';
		$definitions = array();
		$fields = array();
		$fields[] = "order_credit";			
		$definitions["order_credit"] = "DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Stores the sum of all credits applied to this order'";		
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderCreditFields') );
			$config->config_name = 'checkOrderCreditFields';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}
	
	/**
	 *
	 * Additional fields (in UserInfo table) for credits
	 * @version 0.8.2
	 * @return boolean
	 */
	function checkUserInfoCreditFields()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkUserInfoCreditFields', '0')) return true;
		 
		$table = '#__citruscart_userinfo';
		$definitions = array();
		$fields = array();

		$fields[] = "credits_total";
		$fields[] = "credits_withdrawable_total";
		
		$definitions["credits_total"] = "DECIMAL(12,5) NOT NULL";
		$definitions["credits_withdrawable_total"] = "DECIMAL(12,5) NOT NULL";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkUserInfoCreditFields') );
			$config->config_name = 'checkUserInfoCreditFields';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in ProdcutAttributeOptions table) for blank options in product attributes
	 * @version 0.8.2
	 * @return boolean
	 */
	function checkProductAttributeOptionBlank()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductAttributeOptionsBlank', '0')) return true;
		 
		$table = '#__citruscart_productattributeoptions';
		$definitions = array();
		$fields = array();

		$fields[] = "is_blank";
		
		$definitions["is_blank"] = "TINYINT( 1 ) NOT NULL DEFAULT '0'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkProductAttributeOptionsBlank') );
			$config->config_name = 'checkProductAttributeOptionsBlank';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in Taxrates table) for compound taxes
	 * @version 0.8.3
	 * @return boolean
	 */
	function checkLevelTaxes()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkLevelTaxes', '0')) return true;
		 
		$table = '#__citruscart_taxrates';
		$definitions = array();
		$fields = array();

		$fields[] = "level";
		
		$definitions["level"] = "INT NOT NULL DEFAULT '0'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkLevelTaxes') );
			$config->config_name = 'checkLevelTaxes';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 *
	 * Additional fields (in Taxrates table) for compound taxes
	 * @version 0.8.3
	 * @return boolean
	 */
	function checkOrderitemLevelTaxes()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderitemLevelTaxes', '0')) return true;
		 
		$table = '#__citruscart_ordertaxrates';
		$definitions = array();
		$fields = array();

		$fields[] = "ordertaxrate_level";
		$fields[] = 'ordertaxclass_id';
		
		$definitions["ordertaxrate_level"] = "INT NOT NULL DEFAULT '0'";
		$definitions["ordertaxclass_id"] = "INT NOT NULL DEFAULT '0'";
		
		if ($this->insertTableFields( $table, $fields, $definitions ))
		{
			// Update config to say this has been done already
			JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
			$config = JTable::getInstance( 'Config', 'CitruscartTable' );
			$config->load( array( 'config_name'=>'checkOrderitemLevelTaxes') );
			$config->config_name = 'checkOrderitemLevelTaxes';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Checks, if Citruscart already generated a secret word. If not, it generates one
	 * 
	 * @version 0.8.3
	 * @return boolean
	 */
	function checkSecretWord()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSecretWord', '0')) return true;
		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
		$config = JTable::getInstance( 'Config', 'CitruscartTable' );
		$config->config_name = 'secret_word';
		
		Citruscart::load( 'CitruscartHelperBase', 'helper._base' );
		$config->value = CitruscartHelperBase::generateSecretWord();
		if ( $config->save() )
		{
			unset( $config->config_id );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkSecretWord') );
			$config->config_name = 'checkSecretWord';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
		
	}

	/**
	 * Drops zone_id from OrderInfo table
	 * 
	 * @version 0.8.3
	 * @return boolean
	 */
	function dropZoneIdOrderInfo()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('dropZoneIdOrderInfo', '0')) return true;

		$fields = array();
		$fields[] = "zone_id";
    
    $table = '#__citruscart_orderinfo';
		if ($this->dropTableFields( $table, $fields ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'dropZoneIdOrderInfo') );
			$config->config_name = 'dropZoneIdOrderInfo';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Adds a new field for order hash into Orders table
	 * 
	 * @version 0.8.3
	 * @return boolean
	 */
	function checkOrderHashField()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderHashField', '0')) return true;

		$fields = array();
		$fields[] = "order_hash";
    $definitions = array();
    $definitions['order_hash'] = 'VARCHAR( 40 ) NOT NULL , ADD INDEX (  `order_hash` )';
    
    $table = '#__citruscart_orders';
		if ($this->insertTableFields( $table, $fields, $definitions ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkOrderHashField') );
			$config->config_name = 'checkOrderHashField';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Adds a new field for shipping methods into Shipping Methods table
	 * 
	 * @version 0.8.3
	 * @return boolean
	 */
	function checkSubtotalMax()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkSubtotalMax', '0')) return true;

		$fields = array();
		$fields[] = "subtotal_maximum";
    $definitions = array();
    $definitions['subtotal_maximum'] = 'DECIMAL( 12, 5 ) NOT NULL DEFAULT \'-1\'';
    
    $table = '#__citruscart_shippingmethods';
		if ($this->insertTableFields( $table, $fields, $definitions ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkSubtotalMax') );
			$config->config_name = 'checkSubtotalMax';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

  /**
	 * Adds new fields for calculating weight into Order Items table
	 * 
	 * @version 0.9.1
	 * @return boolean                                         
	 */
	function checkOrderItemsWeight()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderItemsWeight', '0')) return true;

		$fields = array();
		$fields[] = "orderitem_weight";
    $fields[] = "orderitem_attributes_weight";
    $definitions = array();
    $definitions['orderitem_weight'] = 'DECIMAL( 12, 5) NULL';
    $definitions['orderitem_attributes_weight'] = 'VARCHAR( 64 ) NOT NULL';
    
    $table = '#__citruscart_orderitems';
		if ($this->insertTableFields( $table, $fields, $definitions ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkOrderItemsWeight') );
			$config->config_name = 'checkOrderItemsWeight';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Adds new fields for calculating weight into Order Item Attributes table
	 * 
	 * @version 0.9.1
	 * @return boolean                                         
	 */
	function checkOrderItemAttributesWeight()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkOrderItemAttributesWeight', '0')) return true;

		$fields = array();
		$fields[] = "orderitemattribute_weight";
    $fields[] = "orderitemattribute_prefix_weight";
    $definitions = array();
    $definitions['orderitemattribute_weight'] = 'DECIMAL( 12, 5 ) NOT NULL DEFAULT  \'0\'';
    $definitions['orderitemattribute_prefix_weight'] = 'VARCHAR( 1 ) NOT NULL DEFAULT  \'+\'';
    
    $table = '#__citruscart_orderitemattributes';
		if ($this->insertTableFields( $table, $fields, $definitions ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkOrderItemAttributesWeight') );
			$config->config_name = 'checkOrderItemAttributesWeight';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}

	/**
	 * Adds new fields for calculating weight into Product Attributes table
	 * 
	 * @version 0.9.1
	 * @return boolean                                         
	 */
	function checkProductAttributesWeight()
	{
		//if this has already been done, don't repeat
		if (Citruscart::getInstance()->get('checkProductAttributesWeight', '0')) return true;

		$fields = array();
		$fields[] = "productattributeoption_weight";
    $fields[] = "productattributeoption_prefix_weight";
    $definitions = array();
    $definitions['productattributeoption_weight'] = 'DECIMAL( 12, 5 ) NOT NULL DEFAULT  \'0\'';
    $definitions['productattributeoption_prefix_weight'] = 'VARCHAR( 1 ) NOT NULL DEFAULT  \'+\'';
    
    $table = '#__citruscart_productattributeoptions';
		if ($this->insertTableFields( $table, $fields, $definitions ) )
		{
  		JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_citruscart/tables' );
      $config = JTable::getInstance( 'Config', 'CitruscartTable' );
			// Update config to say this has been done already
			$config->load( array( 'config_name'=>'checkProductAttributesWeight') );
			$config->config_name = 'checkProductAttributesWeight';
			$config->value = '1';
			$config->save();
			return true;
		}
		return false;
	}
	
	/**
	 *
	 * @return boolean
	 */
	private function checkEAVAttributesFormatStrftime()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_eavattributes';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "eavattribute_format_strftime";
	    $definitions["eavattribute_format_strftime"] = "varchar(255) NULL";
	
	    if ($this->insertTableFields( $table, $fields, $definitions ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}
	
	/**
	 *
	 * @return boolean
	 */
	private function checkEAVAttributesFormatDate()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_eavattributes';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "eavattribute_format_date";
	    $definitions["eavattribute_format_date"] = "varchar(255) NULL";
	
	    if ($this->insertTableFields( $table, $fields, $definitions ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}
	
	/**
	 *
	 * @return boolean
	 */
	private function createTableEAVValuesTime()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_eavvaluestime';
		$definition = '
            CREATE TABLE  IF NOT EXISTS `#__citruscart_eavvaluestime` (
            `eavvalue_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `eavattribute_id` INT NOT NULL ,
            `eaventity_type` VARCHAR( 255 ) NOT NULL,
            `eaventity_id` INT NOT NULL ,
            `eavvalue_value` TIME NOT NULL ,
            `created_date` DATETIME NOT NULL ,
            `modified_date` DATETIME NOT NULL ,
            INDEX (  `eavattribute_id` ,  `eaventity_id` ),
            CONSTRAINT `fk_eavvaluestime_eavattribute`
                FOREIGN KEY (`eavattribute_id` )
                REFERENCES `#__citruscart_eavattributes` (`eavattribute_id` )
                ON DELETE CASCADE
                ON UPDATE CASCADE
            )
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;		
        ';
	
	    if ($this->createTable( $table, $definition ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}

	/**
	 * 
	 * @return boolean
	 */
	private function checkOrderHistoryID()
	{
		if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_orderhistory';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "order_history_id";
	    $newnames["order_history_id"] = "orderhistory_id";
	    $definitions["order_history_id"] = "INT(11) NOT NULL AUTO_INCREMENT";
	
	    if ($this->changeTableFields( $table, $fields, $definitions, $newnames ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}
	
	/**
	 *
	 * @return boolean
	 */
	private function checkProductClassSuffix()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_products';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "product_class_suffix";
	    $definitions["product_class_suffix"] = "varchar(255) NULL";
	
	    if ($this->insertTableFields( $table, $fields, $definitions ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}

	private function checkProductThumbImage()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_products';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "product_thumb_image";
	    $definitions["product_thumb_image"] = "varchar(255) NULL";
	
	    if ($this->insertTableFields( $table, $fields, $definitions ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}
	
	private function checkShippingUserGroupRates()
	{
	    if (Citruscart::getInstance()->get( __FUNCTION__, '0' ))
	    {
	        return true;
	    }
	
	    $table = '#__citruscart_shippingrates';
	    $definitions = array();
	    $fields = array();
	
	    $fields[] = "group_id";
	    $definitions["group_id"] = "int NOT NULL";
	
	    if ($this->insertTableFields( $table, $fields, $definitions ))
	    {
	        $this->setCompleted( __FUNCTION__ );
	        return true;
	    }
	    return false;
	}
}

	
