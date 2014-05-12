<?php

class CitruscartToolBar30 extends JToolbar
{
	/** @var array The links to be rendered in the toolbar */
	protected $linkbar = array();


	public function __construct($config = array()) {}

	protected function renderSubmenu()
	{
		$views = array(
				'dashboard',
				'COM_CITRUSCART_ORDERS' => array('orders','payments','subscriptions','orderitems'),
				'COM_CITRUSCART_CATALOG' => array('products','categories',  'manufacturers', 'reviews'),
				'COM_CITRUSCART_USERS' => array('users','groups',  'credits', 'address'),
				'coupons',
				'COM_CITRUSCART_LOCALISATION' => array('countries', 'zones', 'geozones', 'currencies','emails','orderstates', 'taxclasses'),
				'COM_CITRUSCART_MAINMENU_REPORTS' => array('reports', 'tools'),
				'config'
		);

		//show product attribute migration menu only for the upgraded users

		require_once (JPATH_ADMINISTRATOR.'/components/com_citruscart/helpers/version.php');

		if(CitruscartVersion::getPreviousVersion() == '2.0.2' && COM_CITRUSCART_ATTRIBUTES_MIGRATED==false) {
			$views['COM_CITRUSCART_MAINMENU_TOOLS'] = array('migrate');
		}

		foreach($views as $label => $view) {
			if(!is_array($view)) {
				$this->addSubmenuLink($view);
			} else {
				$label = JText::_($label);
				$this->appendLink($label, '', false);
				foreach($view as $v) {
					$this->addSubmenuLink($v, $label);
				}
			}
		}
	}

	private function addSubmenuLink($view, $parent = null)
	{
		static $activeView = null;
		if(empty($activeView)) {
			$activeView = JFactory::getApplication()->input->getCmd('view','dashboard');
		}

		$key = strtoupper('COM_CITRUSCART_'.strtoupper($view));

		$name = JText::_($key);

		$link = 'index.php?option=com_citruscart&view='.$view;

		$active = $view == $activeView;

		if(strtolower($view) == 'dashboard') {
			$name = JText::_('COM_CITRUSCART_DASHBOARD');
		}

		if(strtolower($view) == 'currencies') {
			$name = JText::_('COM_CITRUSCART_CURRENCY');
		}

		if(strtolower($name) == 'emailtemplates') {
			$name = JText::_('COM_CITRUSCART_EMAILTEMPLATES');
		}

		if(strtolower($name) == 'migrate') {
			$name = JText::_('COM_CITRUSCART_MIGRATE');
		}

		$this->appendLink($name, $link, $active, null, $parent);
	}


	public function renderLinkbar() {

		$app = JFactory::getApplication();
		$tmpl = $app->input->getCmd('tmpl');
		if($tmpl == 'component') {
			return;
		}

		$this->renderSubMenu();
		$links = $this->getLinks();
		//if(!empty($links)) {
		//	foreach($links as $link) {
		//JSubMenuHelper::addEntry($link['name'], $link['link'], $link['active']);
		//	}
		//}

		if(!empty($links)) {
			echo "<div class=\"citruscart-menu\">\n";
			echo "<ul class=\"nav nav-tabs\">\n";
			foreach($links as $link) {
				$dropdown = false;
				if(array_key_exists('dropdown', $link)) {
					$dropdown = $link['dropdown'];
				}

				if($dropdown) {
					echo "<li";
					$class = 'dropdown';
					if($link['active']) $class .= ' active';
					echo ' class="'.$class.'">';

					echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
					if($link['icon']) {
						echo "<i class=\"icon icon-".$link['icon']."\"></i>";
					}
					echo $link['name'];
					echo '<b class="caret"></b>';
					echo '</a>';

					echo "\n<ul class=\"dropdown-menu\">";
					foreach($link['items'] as $item) {

						echo "<li";
						if($item['active']) echo ' class="active"';
						echo ">";
						if($item['icon']) {
							echo "<i class=\"icon icon-".$item['icon']."\"></i>";
						}
						if($item['link']) {
							echo "<a tabindex=\"-1\" href=\"".$item['link']."\">".$item['name']."</a>";
						} else {
							echo $item['name'];
						}
						echo "</li>";

					}
					echo "</ul>\n";

				} else {
					echo "<li";
					if($link['active']) echo ' class="active"';
					echo ">";
					if($link['icon']) {
						echo "<i class=\"icon icon-".$link['icon']."\"></i>";
					}
					if($link['link']) {
						echo "<a href=\"".$link['link']."\">".$link['name']."</a>";
					} else {
						echo $link['name'];
					}
				}

				echo "</li>\n";
			}
			echo "</ul>\n";
			echo "</div>\n";
		}
	}



	public function appendLink($name, $link = null, $active = false, $icon = null, $parent = '')
	{
		$linkDefinition = array(
				'name'		=> $name,
				'link'		=> $link,
				'active'	=> $active,
				'icon'		=> $icon
		);
		if(empty($parent)) {
			$this->linkbar[$name] = $linkDefinition;
		} else {
			if(!array_key_exists($parent, $this->linkbar)) {
				$parentElement = $linkDefinition;
				$parentElement['link'] = null;
				$this->linkbar[$parent] = $parentElement;
				$parentElement['items'] = array();
			} else {
				$parentElement = $this->linkbar[$parent];
				if(!array_key_exists('dropdown', $parentElement) && !empty($parentElement['link'])) {
					$newSubElement = $parentElement;
					$parentElement['items'] = array($newSubElement);
				}
			}

			$parentElement['items'][] = $linkDefinition;
			$parentElement['dropdown'] = true;

			$this->linkbar[$parent] = $parentElement;
		}
	}


	public function &getLinks()
	{
		return $this->linkbar;
	}

	public static  function _custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false, $taskName = 'shippingTask')
	{


		$bar = JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		$bar->appendButton( 'Citruscart', $icon, $alt, $task, $listSelect, $x, $taskName );
	}

	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false, $taskName = 'shippingTask')
	{
		$bar = JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		$bar->appendButton( static::$_name, $icon, $alt, $task, $listSelect, $x, $taskName );
	}

	/**
	 * Writes the common 'new' icon for the button bar
	 * @param string An override for the task
	 * @param string An override for the alt text
	 * @since 1.0
	 */
	public static function addNew($task = 'add', $alt = 'New', $taskName = 'shippingTask')
	{
		$bar = JToolBar::getInstance('toolbar');
		// Add a new button
		$bar->appendButton( static::$_name, 'new', $alt, $task, false, false, $taskName );
	}
}


class JToolbarButtonCitruscart30 extends JToolbarButton {


	function fetchButton( $type='Citruscart', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $taskName = 'shippingTask' )
	{
		$i18n_text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand($text, $task, $list, $hideMenu, $taskName);
		if($class=='icon-new') {
			$btn_class='btn btn-small btn-success';
		} else {
			$btn_class='btn btn-small';
		}
		$html = '';
		$html	.= "<button href=\"#\" onclick=\"$doTask\" class=\"toolbar $btn_class\">\n";
		$html	.="<i class='$class icon-white'></i>";
		$html	.= "$i18n_text\n";
		$html	.= "</button>\n";

		return $html;
	}

	function fetchId( $type='Confirm', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		return $this->_name.'-'.$name;
	}

	function _getCommand($name, $task, $list, $hide, $taskName)
	{
		$todo		= JString::strtolower(JText::_( $name ));
		$message	= JText::sprintf( 'COM_CITRUSCART_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO', $todo );
		$message	= addslashes($message);

		if ($list) {
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ submitCitruscartButton('$task', '$taskName')}";
		} else {
			$cmd = "javascript:submitCitruscartButton('$task', '$taskName')";
		}


		return $cmd;
	}
}