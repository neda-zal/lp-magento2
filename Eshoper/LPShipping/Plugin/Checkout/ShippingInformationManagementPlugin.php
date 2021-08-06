<?php
/**
 * @package    Eshoper/LPShipping/Plugin/Checkout
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Plugin\Checkout;


class ShippingInformationManagementPlugin
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository $_quoteRepository
     */
    protected $_quoteRepository;

    /**
     * ShippingInformationManagementPlugin constructor.
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct (
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * Process before checkout shipping info save
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes ();
        $selectedTerminal = $extAttributes->getLpexpressTerminal ();

        $quote = $this->_quoteRepository->getActive ( $cartId );

        if ( $addressInformation->getShippingMethodCode() ==
            'lpcarrierlpexpress_terminal' && $selectedTerminal == null ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __( 'Please select LP Express terminal.' )
            );
        }

        $quote->setLpexpressTerminal ( $selectedTerminal );
    }
}
