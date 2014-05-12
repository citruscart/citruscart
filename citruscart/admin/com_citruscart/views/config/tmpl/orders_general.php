<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $row = @$this -> row; ?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_GUEST_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('guest_checkout_enabled', 'class="inputbox"', $this -> row -> get('guest_checkout_enabled', '1')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ONE_PAGE_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('one_page_checkout', '' ,  $this -> row -> get('one_page_checkout', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ONE_PAGE_CHECKOUT_LAYOUT'); ?>
            </th>
            <td><?php
            echo CitruscartSelect::opclayouts($this->row->get('one_page_checkout_layout', 'standard'), 'one_page_checkout_layout');
            ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ENABLE_TOOLTIPS_ONE_PAGE_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('one_page_checkout_tooltips_enabled', '' , $this -> row -> get('one_page_checkout_tooltips_enabled', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_REQUIRE_ACCEPTANCE_OF_TERMS_ON_CHECKOUT'); ?>
            </th>
            <td><?php  echo CitruscartSelect::btbooleanlist('require_terms', 'class="inputbox"', $this -> row -> get('require_terms', '0')); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_TERMS_AND_CONDITIONS_ARTICLE'); ?>
            </th>
            <td style="width: 280px;"><?php echo $this -> elementArticle_terms; ?> <?php echo $this -> resetArticle_terms; ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_FOR_TERMS_AND_CONDITIONS_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_SUCCESSFUL_CHECKOUT'); ?>
            </th>
            <td style="width: 280px;"><?php echo $this -> elementArticleModel -> _fetchElement('article_checkout', $this -> row -> get('article_checkout')); ?> <?php echo $this -> elementArticleModel -> _clearElement('article_checkout', '0'); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_SUCCESSFUL_CHECKOUT_DESC'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 25%;"><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_UNSUCCESSFUL_CHECKOUT'); ?>
            </th>
            <td style="width: 280px;"><?php echo $this -> elementArticleModel -> _fetchElement('article_default_payment_failure', $this -> row -> get('article_default_payment_failure')); ?> <?php echo $this -> elementArticleModel -> _clearElement('article_default_payment_failure', '0'); ?>
            </td>
            <td><?php echo JText::_('COM_CITRUSCART_ARTICLE_TO_DISPLAY_AFTER_UNSUCCESSFUL_CHECKOUT_DESC'); ?>
            </td>
        </tr>
    </tbody>
</table>
