<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/Action
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\Action;

use Eshoper\LPShipping\Model\CN22;

class SaveCN22Form extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface $_messageManger
     */
    protected $_messageManger;

    /**
     * @var \Eshoper\LPShipping\Model\CN22Factory $_CN22Factory
     */
    protected $_CN22Factory;

    /**
     * @var \Eshoper\LPShipping\Model\CN22Repository $_CN22Repository
     */
    protected $_CN22Repository;

    /**
     * LpSaveCN22Form constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManger
     * @param \Eshoper\LPShipping\Api\Data\CN22InterfaceFactory $CN22Factory
     * @param \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository
     */
    public function __construct (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManger,
        \Eshoper\LPShipping\Api\Data\CN22InterfaceFactory $CN22Factory,
        \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository
    ) {
        $this->_messageManger = $messageManger;
        $this->_CN22Factory = $CN22Factory;
        $this->_CN22Repository = $CN22Repository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $CN22FormData = $this->getRequest()->getParams ();

        if ( $orderId = $CN22FormData [ 'order' ] ) {
             // Check if record exists
            $CN22 = $this->_CN22Repository->getByOrderId ( $orderId );
            if ( !$CN22 ) {
                $CN22 = $this->_CN22Factory->create ();
            }

            $CN22->setOrderId ( $orderId );
            $CN22->setParcelType ( $CN22FormData [ 'parcel_type' ] );
            $CN22->setParcelTypeNotes ( $CN22FormData [ 'parcel_type_notes' ] );
            $CN22->setParcelDescription ( $CN22FormData [ 'parcel_description' ] );
            $CN22->setCnParts ( json_encode ( $CN22FormData [ 'items' ] ) );

            $this->_CN22Repository->save ( $CN22 );

            $this->_messageManger->addSuccessMessage (
                __( 'CN22 form saved successfully. Now you can create your shipping label.' )
            );
        }

        return $this->_redirect ( $this->_redirect->getRefererUrl () );
    }
}
