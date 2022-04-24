<?php
/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */

namespace Walcart\Otplogin\Model\ResourceModel;

/**
 * Class Otp
 * @package Walcart\Otplogin\Model\ResourceModel
 */
class Otp extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mobile_otp', 'entity_id');
    }
}
