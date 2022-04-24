<?php
/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */

namespace Walcart\Otplogin\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Otptype
 * @package Walcart\Otplogin\Model\Source
 */
class Otptype implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'number', 'label' => __('Number')],
            ['value' => 'alphabets', 'label' => __('Alphabets')],
            ['value' => 'alphanumeric', 'label' => __('Alphanumeric')]
        ];
    }
}
