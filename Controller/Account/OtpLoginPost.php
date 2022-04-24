<?php

/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */


namespace Walcart\Otplogin\Controller\Account;

use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class OtpLoginPost
 * @package Walcart\Otplogin\Controller\Account
 */
class OtpLoginPost extends \Magento\Framework\App\Action\Action
{

    /**
     * OtpLoginPost constructor
     * @param \Magento\Framework\App\Action\Context                $context            [description]
     * @param \Magento\Framework\Session\SessionManagerInterface   $session            [description]
     * @param \Walcart\Otplogin\Helper\Data                        $helper             [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory  [description]
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection    [description]    
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManagerInterface $session,
        \Walcart\Otplogin\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
        $this->helper = $helper;
        $this->collection = $collection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_sessionManager = $session;
        parent::__construct($context);
    }

    /**
     * @return PageFactory
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['email'])) {
            $collection = $this->collection->addAttributeToSelect('*')
                ->addAttributeToFilter('mobile_number', $params['mobile_number'])
                ->load()->getData();
            if (empty($collection)) {
                $response = [
                    'errors' => true,
                    'message' => __("Mobile Number Not Registered")
                ];
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($response);
            }
        }
        // set session
        $session = $this->_sessionManager;
        $session->setUserFormData($params);

        //update status
        try {
            $this->helper->setUpdateotpstatus($params['mobile_number']);

            //otp
            $otp_code = $this->helper->getOtpcode();

            //sms
             $this->helper->getSendotp($otp_code,$params['mobile_number']);

            //save data
            $otp = base64_encode($otp_code);
            $this->helper->setOtpdata($otp,$params['mobile_number']);

            $response = [
                'errors' => false,
                'message' => __('OTP send to your Mobile Number')
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
