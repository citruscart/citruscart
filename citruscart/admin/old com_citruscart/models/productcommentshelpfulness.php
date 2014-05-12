<?php
/**
 * @version	1.5
 * @package	Citruscart
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Citruscart::load( 'CitruscartModelBase', 'models._base' );

class CitruscartModelProductCommentsHelpfulness extends CitruscartModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
        $filter_comment  = $this->getState('filter_comment');
    
        if (strlen($filter_comment))
        {
            $query->where('tbl.productcomment_id = '.(int) $filter_comment);
        }
    }
}