<?php

/**
 *
 * @category  Sabbir
 * @package   Walcart_Otplogin
 * @author    Sabbir Hossain
 */


namespace Walcart\Otplogin\Controller\Account;

use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Walcart\Otplogin\Model\OtpFactory;
use PHPUnit\Framework\Constraint\IsTrue;

/**
 * Class OtpPost
 * @package Walcart\Otplogin\Controller\Account
 */
class OtpPost extends \Magento\Framework\App\Action\Action
{

    /**
     * OtpPost constructor
     * @param \Magento\Framework\App\Action\Context                $context        [description]
     * @param \Magento\Customer\Model\CustomerFactory              $customer       [description]
     * @param \Walcart\Otplogin\Model\OtpFactory                   $otpFactory     [description]
     * @param \Magento\Customer\Model\Session                      $session        [description]
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig    [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory  [description]
     * @param \Walcart\Otplogin\Helper\Data                        $helper             [description]
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection    [description]    
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customer,
        OtpFactory $otpFactory,
        \Magento\Customer\Model\Session $customersession,
        SessionManagerInterface $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Walcart\Otplogin\Helper\Data $helper,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->collection = $collection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_customer = $customer;
        $this->otpFactory = $otpFactory;
        $this->customersession = $customersession;
        $this->_sessionManager = $session;
        return parent::__construct($context);
    }

    /**
     * @return PageFactory
     */
    public function execute()
    {

        //get session
        $sessiondata = $this->_sessionManager->getUserFormData();
        $collection = $this->collection->addAttributeToSelect('*')
            ->addAttributeToFilter('mobile_number', $sessiondata['mobile_number'])
            ->load()->getData();
        //get otp
        $otpbymobile = $this->getRequest()->getParam('otp');
        $otp = base64_encode($otpbymobile);
        $otpvalue = $this->otpFactory->create()->getCollection()->addFieldToFilter('otp', $otp)->getData();
        $status = $this->otpFactory->create()->getCollection()->addFieldToFilter('otp', $otp)->addFieldToSelect('status')->getData();


        //config expire time
        $expiredtime = $this->helper->getExpiretime();

        // check value is empty or not
        if (!empty($otpvalue)) {
            $timestamp =  $_SERVER["REQUEST_TIME"];
            $oktime = $timestamp - $_SESSION['time'];
            $created_at = (int) strtotime($otpvalue[0]['created_at']);
            $now = time();
            $now = (int) $now;
            $expire = $now -= $created_at;
            $otpstatus = $status[0]['status'];
            if ($otpstatus == 0) {
                //check expiredtime
               if ($oktime <= 30) {
                    if (!empty($collection)) {
                        $customer = $this->_customer->create()->load($collection[0]['entity_id']);
                        $customerSession = $this->customersession;
                        $customerSession->setCustomerAsLoggedIn($customer);
                        $customerSession->regenerateId();
                        $response = [
                            'errors' => false,
                            'message' => __("Logged In Successfully.")
                        ];
                        $resultJson = $this->resultJsonFactory->create();
                        return $resultJson->setData($response);
                    } else {
                        $customer = $this->_customer->create();
                        $customer->setEmail($sessiondata['email']);
                        $customer->setFirstname($sessiondata['firstname']);
                        $customer->setLastname($sessiondata['lastname']);
                        $customer->setPassword($sessiondata['password']);
                        $customer->save();
                        $customerData = $customer->getDataModel();
                        $customerData->setCustomAttribute('mobile_number', $sessiondata['mobile_number']);
                        $customer->updateData($customerData);
                        $customer->save();

                        $customer = $this->_customer->create()->load($customer->getEntityId());
                        $customerSession = $this->customersession;
                        $customerSession->setCustomerAsLoggedIn($customer);
                        $customerSession->regenerateId();
                        $response = [
                            'errors' => false,
                            'message' => __("User Created Successfully.")
                        ];
                        $resultJson = $this->resultJsonFactory->create();
                        return $resultJson->setData($response);
                    }
                } else {
                    $response = [
                        'errors' => true,
                        'message' => __('OTP Exprired')
                    ];
                    $resultJson = $this->resultJsonFactory->create();
                    return $resultJson->setData($response);
                }
            } else {
                $response = [
                    'errors' => true,
                    'message' => __('Invalid Status')
                ];
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($response);
            }
        } else {
            $response = [
                'errors' => true,
                'message' => __("Invalid Value")
            ];
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        }
    }
}
