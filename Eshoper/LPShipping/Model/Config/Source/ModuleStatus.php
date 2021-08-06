<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class ModuleStatus implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * ModuleStatus constructor.
     * @param \Eshoper\LPShipping\Model\Config $config
     */
    public function __construct(
        \Eshoper\LPShipping\Model\Config $config
    ) {
        $this->_config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getCommentText($elementValue)
    {
        if ( !$this->_config->getStatus () ) {
            return sprintf (
                '<span style="font-size: 16px; color: red;">%s</span>',
                __('Unauthorized')
            );
        } else {
            return sprintf (
                '<span style="font-size: 16px; color: green;">%s</span>',
                __('Active')
            );
        }
    }
}
