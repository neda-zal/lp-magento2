<?php
/**
 * @package    Eshoper/LPShipping/Block/Order/Create
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Block\Adminhtml\Order\Create;

class Terminal extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $_terminalRepository
     */
    protected $_terminalRepository;

    /**
     * Terminal constructor.
     * @param \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct (
        \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_terminalRepository = $terminalRepository;
        parent::__construct ( $context, $sessionQuote, $orderCreate, $priceCurrency, $data );
    }

    /**
     * Show select if lpexpress terminal shipping method selected
     * @return bool
     */
    public function showSelect ()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getShippingMethod()
            === 'lpcarrier_lpcarrierlpexpress_terminal';
    }

    /**
     * Get terminal list from repository
     * @return array
     */
    public function getTerminals ()
    {
        return $this->_terminalRepository->getList ();
    }
}
