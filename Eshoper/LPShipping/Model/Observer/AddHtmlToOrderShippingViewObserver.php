<?php
/**
 * @package    Eshoper/LPShipping/Model/Observer
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Observer;

use Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface;

class AddHtmlToOrderShippingViewObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Element\Template $_block
     */
    protected $_block;

    /**
     * @var \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $_terminalRepository
     */
    protected $_terminalRepository;

    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Backend\Model\UrlInterface $_backendUrl
     */
    protected $_backendUrl;

    /**
     * @var \Eshoper\LPShipping\Api\CN22RepositoryInterface $_CN22
     */
    protected $_CN22;

    /**
     * @var \Eshoper\LPShipping\Api\CN23RepositoryInterface $_CN23
     */
    protected $_CN23;

    /**
     * @var \Eshoper\LPShipping\Api\SenderRepositoryInterface $_senderRepository
     */
    protected $_senderRepository;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Eshoper\LPShipping\Model\Config\Source\AvailableCountries $_availableCountries
     */
    protected $_availableCountries;

    /**
     * @var \Eshoper\LPShipping\Helper\ShippingTemplate $_shippingTemplateHelper
     */
    protected $_shippingTemplateHelper;

    /**
     * AddHtmlToOrderShippingViewObserver constructor.
     * @param \Magento\Framework\View\Element\Template $block
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22
     * @param \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23
     * @param \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository
     * @param \Eshoper\LPShipping\Model\Config\Source\AvailableCountries $availableCountries
     * @param \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper
     * @param \Eshoper\LPShipping\Model\Config $config
     */
    public function __construct (
        \Magento\Framework\View\Element\Template $block,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository,
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22,
        \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23,
        \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository,
        \Eshoper\LPShipping\Model\Config\Source\AvailableCountries $availableCountries,
        \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper,
        \Eshoper\LPShipping\Model\Config $config
    ) {
        $this->_block                       = $block;
        $this->_terminalRepository          = $terminalRepository;
        $this->_apiHelper                   = $apiHelper;
        $this->_backendUrl                  = $backendUrl;
        $this->_CN22                        = $CN22;
        $this->_CN23                        = $CN23;
        $this->_senderRepository            = $senderRepository;
        $this->_availableCountries          = $availableCountries;
        $this->_config                      = $config;
        $this->_shippingTemplateHelper      = $shippingTemplateHelper;
    }

    /**
     * Pass additional template to the order_view and shipment_view
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute ( \Magento\Framework\Event\Observer $observer )
    {
        $shipment = $order = null;
        $orderShippingViewBlock = $observer->getLayout ()
            ->getBlock ( 'order_shipping_view' ) ?: $observer->getLayout ()
                ->getBlock ( 'shipment_tracking' );

        if ( $orderShippingViewBlock ) {
            $order = $orderShippingViewBlock->getOrder ();
        }

        // If in shipment page
        if ( $order === null && $orderShippingViewBlock != null ) {
            $shipId = $orderShippingViewBlock->getRequest ()->getParam ( 'shipment_id' );
            if ( $shipId !== null ) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $shipmentCollection = $objectManager->create('Magento\Sales\Model\Order\Shipment');
                $shipment = $shipmentCollection->load($shipId);
                $order = $shipment->getOrder();
            }
        }

        if ( $order ) {
            // Do not execute if not LP or LPEXPRESS method
            if ( !$this->_config->isLpMethod ( $order->getShippingMethod () ) &&
                !$this->_config->isLpExpressMethod ( $order->getShippingMethod () ) ) {
                return;
            }
        }

        // Show terminal on order or shipment page
        if ( $observer->getElementName () === 'order_shipping_view'
            || $observer->getElementName () === 'shipment_tracking' ) {

            /** @var \Magento\Sales\Model\Order $order */
            if ( $order !== null ) {
                $block = $this->_block;
                if ( $order->getLpexpressTerminal () !== null && $order->getShippingMethod ()
                    === 'lpcarrier_lpcarrierlpexpress_terminal' ) {

                    // Get terminal by terminal_id from terminal collection
                    $terminal = $this->_terminalRepository
                        ->getByTerminalId ( $order->getLpexpressTerminal () );

                    if ( $terminal !== null ) {
                        $block->setMethodName ( __( 'Terminal' ) );
                        $block->setMethodInfo (
                            !$terminal->isEmpty () ? sprintf ( '%s - %s',
                                $terminal->getName (), $terminal->getAddress () )
                                : __( 'Something wen\'t wrong here..' )
                        );

                        if ($shipment !== null) {
                            echo sprintf('<b>' . __( 'Terminal' ) . '</b> %s - %s',
                                $terminal->getName (), $terminal->getAddress () );
                        }
                    }
                }

                $block->setTemplate('Eshoper_LPShipping::order_info_shipping_info.phtml');

                // Set output to order view
                $html = $observer->getTransport ()->getOutput () . $block->toHtml ();
                $observer->getTransport ()->setOutput ( $html );
            }
        }

        // Shipment modalbox
        if ( strpos ( $observer->getElementName (), 'eshoper_lpshipping_create_shipment' ) ) {
            $block               = $this->_block;
            $availableCountries  = $this->_availableCountries;

            // Get shipping template according to shipping type and shipping size
            $template = $this->_shippingTemplateHelper->getTemplate (
                $order->getLpShippingType (),
                $order->getLpShippingSize ()
            );

            $sender = $this->_senderRepository->getByOrderId(
                $order->getEntityId ()
            );

            $block->setData ( [
                'order' => $order,
                'actionUrl' => $this->_backendUrl->getUrl ( 'lp_action/action/saveshipmentdetails' ),
                'countries' => $availableCountries,
                'terminals' => $this->_terminalRepository->getList (),
                'sender' => [
                    'name' => $sender->getName () ?? $this->_config->getSenderName (),
                    'phone' => $sender->getPhone () ?? $this->_config->getSenderPhone (),
                    'email' => $sender->getEmail () ?? $this->_config->getSenderEmail (),
                    'country' => $sender->getCountryId () ?? $this->_config->getSenderCountryId (),
                    'city' => $sender->getCity () ?? $this->_config->getSenderCity (),
                    'street' => $sender->getStreet () ?? $this->_config->getSenderStreet (),
                    'building' => $sender->getBuildingNumber () ?? $this->_config->getSenderBuilding (),
                    'apartment' => $sender->getApartment () ?? $this->_config->getSenderApartment (),
                    'postcode' => $sender->getPostcode () ?? $this->_config->getSenderPostCode ()
                ],
                'CN22' => $this->_shippingTemplateHelper->isCN22 ( $order, $template ['id'] ) ?
                    $this->_CN22->getByOrderId ( $order->getId () ) : null,
                'CN23' => $this->_shippingTemplateHelper->isCN23 ( $order, $template [ 'id' ] ) ?
                    $this->_CN23->getByOrderId ( $order->getId () ) : null
            ] );

            $block->setTemplate('Eshoper_LPShipping::form_shipment_modalbox.phtml');

            // Set output to order view
            $html = $observer->getTransport ()->getOutput () . $block->toHtml ();
            $observer->getTransport ()->setOutput ( $html );
        }
    }
}
