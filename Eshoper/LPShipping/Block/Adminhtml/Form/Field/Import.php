<?php
/**
 * @package    Eshoper/LPShipping/Block/Form/Field
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Block\Adminhtml\Form\Field;


class Import extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @return void
     */
    protected function _construct ()
    {
        parent::_construct ();
        $this->setType ('file');
    }

    /**
     *
     * @return string
     */
    public function getElementHtml ()
    {
        return parent::getElementHtml ();
    }
}

