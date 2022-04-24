<?php

/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */


namespace Walcart\Otplogin\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Otp
 * @package Walcart\Otplogin\Model
 */
class Otp extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Walcart\Otplogin\Model\ResourceModel\Otp');
    }
}
