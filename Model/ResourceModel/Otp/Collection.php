<?php

/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */


namespace Walcart\Otplogin\Model\ResourceModel\Otp;

/**
 * Class Collection
 * @package Walcart\Otplogin\Model\ResourceModel\Otp
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Walcart\Otplogin\Model\Otp', 'Walcart\Otplogin\Model\ResourceModel\Otp');
	}
}
