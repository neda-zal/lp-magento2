<?php
/**
 * @package    Eshoper/LPShipping/Block/Form/Field
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Block\Adminhtml\Form\Field;


class Export extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * Backend url /admin
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        $this->_backendUrl = $backendUrl;

        parent::__construct ( $factoryElement, $factoryCollection, $escaper, $data );
    }

    /**
     * Return html for the export button
     *
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm ()->getParent ()->getLayout ()->createBlock (
            \Magento\Backend\Block\Widget\Button::class
        );

        $params = [ 'website' => $buttonBlock->getRequest ()->getParam( 'website' ) ];

        // Select particular controller for import/export
        switch ( $this->getHtmlId () ) {
            case 'carriers_lpcarrier_lpcarriershipping_lp_export_rates':
                $fileName = 'lp-tables';
                $routePath = '*/*/exportLpTableRates';
                break;
            case 'carriers_lpcarrier_lpcarriershipping_lpexpress_export_rates':
                $fileName = 'lpexpress-tables';
                $routePath = '*/*/exportLpExpressTableRates';
                break;
            case 'carriers_lpcarrier_lpcarriershipping_lp_export_country':
                $fileName = 'lp-country-rates';
                $routePath = '*/*/exportLpCountryRates';
                break;
            case 'carriers_lpcarrier_lpcarriershipping_lpexpress_export_country':
                $fileName = 'lpexpress-country-rates';
                $routePath = '*/*/exportLpExpressCountryRates';
                break;
        }

        $url = $this->_backendUrl->getUrl ( $routePath, $params );
        $data = [
            'label' => __( 'Export CSV' ),
            'onclick' => "setLocation('" .
                $url .
                $fileName . ".csv' )",
            'class' => '',
        ];

        return $buttonBlock->setData ( $data )->toHtml ();
    }
}
