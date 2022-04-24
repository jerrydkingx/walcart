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
 * Class ResendOtp
 * @package Walcart\Otplogin\Controller\Account
 */
class ResendOtp extends \Magento\Framework\App\Action\Action
{
    /**
     * ResendOtp constructor
     * @param \Magento\Framework\App\Action\Context                $context        [description]
     * @param \Magento\Framework\Session\SessionManagerInterface   $session        [description]
     * @param \Walcart\Otplogin\Helper\Data                        $helper         [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory  [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManagerInterface $session,
        \Walcart\Otplogin\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_sessionManager = $session;
        parent::__construct($context);
    }

    /**
     * @return PageFactory
     */
    public function execute()
    {
        //get session
        $sessiondata = $this->_sessionManager->getUserFormData();

        try {
            //update status
            $this->helper->setUpdateotpstatus($sessiondata['mobile_number']);

            //otp
            $otp_code = $this->helper->getOtpcode();

            //sms
            $this->helper->getSendotp($otp_code, $sessiondata['mobile_number']);

            //save data
            $otp = base64_encode($otp_code);
            $this->helper->setOtpdata($otp, $sessiondata['mobile_number']);

            $response = [
                'errors' => false,
                'message' => __('OTP Resend Successfully.')
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
