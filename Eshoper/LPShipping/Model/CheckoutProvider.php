<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class CheckoutProvider extends \Magento\Framework\App\Helper\AbstractHelper
    implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $_terminalCollection
     */
    protected $_terminalRepository;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * CheckoutProvider constructor.
     * @param \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository,
        \Eshoper\LPShipping\Model\Config $config,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_terminalRepository = $terminalRepository;
        $this->_config = $config;

        parent::__construct ( $context );
    }

    /**
     * Retrieve assoc array of checkout configuration
     * Pass terminal list to the checkout frontend
     *
     * @return array
     */
    public function getConfig ()
    {
        return [
            'terminal' => [
                'list' => array_reverse ( $this->_terminalRepository->getList () )
            ],
            'lp_delivery_time' => [
                'lpcarrierlp_postoffice' =>
                    $this->_config->getLPPostOfficeDeliveryTime (),
                'lpcarrierlpexpress_courier' =>
                    $this->_config->getLPExpressCourierDeliveryTime (),
                'lpcarrierlpexpress_terminal'
                    => $this->_config->getLPExpressTerminalDeliveryTime (),
                'lpcarrierlpexpress_postoffice'
                    => $this->_config->getLPExpressPostOfficeDeliveryTime (),
                'lpcarrierlp_overseas'
                    => $this->_config->getLPOverseasDeliveryTime (),
                'lpcarrierlpexpress_overseas'
                    => $this->_config->getLPExpressOverseasDeliveryTime ()
            ]
        ];
    }
}
