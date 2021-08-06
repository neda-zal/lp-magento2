<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/Action
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\Action;

class SaveShipmentDetails extends \Magento\Backend\App\Action
{
    /**
     * @var \Eshoper\LPShipping\Api\CN22RepositoryInterface $_CN22Repository
     */
    protected $_CN22Repository;

    /**
     * @var \Eshoper\LPShipping\Api\Data\CN22Interface $_CN22
     */
    protected $_CN22;

    /**
     * @var \Eshoper\LPShipping\Api\CN23RepositoryInterface $_CN23Repository
     */
    protected $_CN23Repository;

    /**
     * @var \Eshoper\LPShipping\Api\Data\CN23Interface $_CN23
     */
    protected $_CN23;

    /**
     * @var \Eshoper\LPShipping\Api\SenderRepositoryInterface $_senderRepository
     */
    protected $_senderRepository;

    /**
     * @var \Eshoper\LPShipping\Api\Data\SenderInterface $_sender
     */
    protected $_sender;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * SaveShipmentDetails constructor.
     * @param \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository
     * @param \Eshoper\LPShipping\Api\Data\CN22Interface $CN22
     * @param \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23Repository
     * @param \Eshoper\LPShipping\Api\Data\CN23Interface $CN23
     * @param \Eshoper\LPShipping\Api\Data\SenderInterface $sender
     * @param \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository,
        \Eshoper\LPShipping\Api\Data\CN22Interface $CN22,
        \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23Repository,
        \Eshoper\LPShipping\Api\Data\CN23Interface $CN23,
        \Eshoper\LPShipping\Api\Data\SenderInterface $sender,
        \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository,
        \Eshoper\LPShipping\Model\Config $config,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_CN22Repository   = $CN22Repository;
        $this->_CN22             = $CN22;
        $this->_CN23Repository   = $CN23Repository;
        $this->_CN23             = $CN23;
        $this->_senderRepository = $senderRepository;
        $this->_sender           = $sender;
        $this->_config           = $config;
        $this->_orderRepository  = $orderRepository;
        parent::__construct( $context );
    }

    /**
     * @inheritDoc
     */
    public function execute ()
    {
        $request        = $this->getRequest ()->getParams ();
        $order          = $this->_orderRepository->get ( $request [ 'order_id' ] );
        $resultRedirect = $this->resultRedirectFactory->create ();
        $resultRedirect->setRefererUrl ();

        if ( $order ) {
            /**
             * Order shipment info
             */
            if ( key_exists ( 'shipping_type', $request ) ) {
                $order->setLpShippingType ( $request [ 'shipping_type' ] );

                // If size not selected LP EXPRESS
                if ( $this->_config->isLpExpressMethod ( $order->getShippingMethod () ) ) {
                    if ( $request [ 'shipping_type' ] != 'EBIN' && !key_exists ( 'shipping_size', $request ) ) {
                        $this->messageManager->addErrorMessage (
                            __( 'Please select shipping size. Changes have not been saved.' )
                        );

                        return $resultRedirect;
                    }
                }

                // For tracked types
                if ( $request [ 'shipping_type' ] == 'SMALL_CORESPONDENCE_TRACKED' ) {
                    $request [ 'shipping_size' ] = 'Small';
                }

                if ( $request [ 'shipping_type' ] == 'MEDIUM_CORESPONDENCE_TRACKED' ) {
                    $request [ 'shipping_size' ] = 'Medium';
                }
            }

            // Shipping Size
            if ( key_exists ( 'shipping_size', $request ) ) {
                $order->setLpShippingSize ( $request [ 'shipping_size' ] );
            } else {
                $order->setLpShippingSize ( null );
            }

            // COD Value
            if ( key_exists ( 'cod', $request ) ) {
                $order->setLpCod ( $request [ 'cod' ] );
            }

            // LP Express terminal
            if ( key_exists ( 'terminal_id', $request ) ) {
                $order->setLpexpressTerminal ( $request [ 'terminal_id' ] );
            }

            $this->_orderRepository->save ( $order );

            /**
             * Sender info
             */
            if ( key_exists ( 'sender', $request  ) ) {
                $requestSenderData = $request [ 'sender' ];
                $sender            = $this->_senderRepository->getByOrderId ( $order->getEntityId () );

                // If not exists create new
                if ( !$sender->getId () ) {
                    $sender = $this->_sender;
                }

                $sender->setOrderId ( $order->getEntityId () );
                $sender->setName ( $requestSenderData [ 'sender_name' ] );
                $sender->setPhone ( $requestSenderData [ 'sender_phone' ] );
                $sender->setEmail ( $requestSenderData [ 'sender_email' ] );
                $sender->setCountryId ( $requestSenderData [ 'sender_country' ] );
                $sender->setCity ( $requestSenderData [ 'sender_city' ] );
                $sender->setStreet ( $requestSenderData [ 'sender_street' ] );
                $sender->setBuildingNumber ( $requestSenderData [ 'sender_building' ] );
                $sender->setApartment ( $requestSenderData [ 'sender_apartment' ] );
                $sender->setPostcode ( $requestSenderData [ 'sender_postcode' ] );

                $this->_senderRepository->save ( $sender );
            }

            /**
             * CN22 Info
             */
            if ( key_exists ( 'cn22', $request ) ) {
                $requestCN22Data = $request [ 'cn22' ];
                $CN22            = $this->_CN22Repository->getByOrderId ( $order->getEntityId () );

                // If not exists create new
                if ( !$CN22->getId () ) {
                    $CN22 = $this->_CN22;
                }

                $CN22->setOrderId ( $order->getEntityId () );
                $CN22->setParcelType ( $requestCN22Data [ 'parcel_type' ] );
                $CN22->setParcelTypeNotes ( $requestCN22Data [ 'parcel_type_notes' ] );
                $CN22->setParcelDescription ( $requestCN22Data [ 'parcel_description' ] );
                $CN22->setCnParts ( json_encode ( $requestCN22Data [ 'items' ] ) );

                $this->_CN22Repository->save ( $CN22 );
            }

            /**
             * CN23 Info
             */
            if ( key_exists ( 'cn23', $request ) ) {
                $requestCN23Data = $request [ 'cn23' ];

                $CN23 = $this->_CN23Repository->getByOrderId ( $order->getEntityId () );

                if ( !$CN23->getId () ) {
                    $CN23 = $this->_CN23;
                }

                $CN23->setOrderId ( $order->getEntityId () );
                $CN23->setParcelType ( $requestCN23Data [ 'parcel_type' ] );
                $CN23->setParcelTypeNotes ( $requestCN23Data [ 'parcel_type_notes' ] );
                $CN23->setExporterCustomsCode ( $requestCN23Data [ 'exporter_customs_code' ] );
                $CN23->setLicense ( $requestCN23Data [ 'license' ] );
                $CN23->setCertificate ( $requestCN23Data [ 'certificate' ] );
                $CN23->setInvoice ( $requestCN23Data [ 'invoice' ] );
                $CN23->setNotes ( $requestCN23Data [ 'notes' ] );
                $CN23->setFailureInstruction ( $requestCN23Data [ 'failure_instruction' ] );
                $CN23->setImporterCode ( $requestCN23Data [ 'importer_code' ] );
                $CN23->setImporterCustomsCode ( $requestCN23Data [ 'importer_customs_code' ] );
                $CN23->setImporterEmail ( $requestCN23Data [ 'importer_email' ] );
                $CN23->setImporterFax ( $requestCN23Data [ 'importer_fax' ] );
                $CN23->setImporterPhone ( $requestCN23Data [ 'importer_phone' ] );
                $CN23->setImporterTaxCode ( $requestCN23Data [ 'importer_tax_code' ] );
                $CN23->setImporterVatCode ( $requestCN23Data [ 'importer_vat_code' ] );
                $CN23->setDescription ( $requestCN23Data [ 'description' ] );
                $CN23->setCnParts ( json_encode ( $requestCN23Data [ 'items' ] ) );

                $this->_CN23Repository->save ( $CN23 );
            }

            $this->messageManager->addSuccessMessage (
                __( 'Shipment details has been successfully saved.' )
            );
        }

        return $resultRedirect;
    }
}
