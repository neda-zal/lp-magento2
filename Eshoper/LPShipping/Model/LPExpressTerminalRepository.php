<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class LPExpressTerminalRepository
    implements \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $_terminalsFactory
     */
    protected $_terminalsFactory;

    /**
     * @var \Eshoper\LPShipping\Model\LPExpressTerminals $_terminalResource
     */
    protected $_terminalResource;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals\CollectionFactory $_terminalCollectionFactory
     */
    protected $_terminalCollectionFactory;

    /**
     * LPExpressTerminalRepository constructor.
     * @param \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $terminalsFactory
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals $terminalResource
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals\CollectionFactory $terminalCollectionFactory
     */
    public function __construct (
        \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $terminalsFactory,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals $terminalResource,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals\CollectionFactory $terminalCollectionFactory
    ) {
        $this->_terminalsFactory            = $terminalsFactory;
        $this->_terminalResource            = $terminalResource;
        $this->_terminalCollectionFactory   = $terminalCollectionFactory;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByTerminalId ( $terminalId )
    {
        /** @var \Eshoper\LPShipping\Model\LPExpressTerminals $terminal */
        $terminal = $this->_terminalsFactory->create ();
        $this->_terminalResource->load ( $terminal, $terminalId, 'terminal_id' );

        if ( !$terminal->getId () ) {
            throw new \Magento\Framework\Exception\NoSuchEntityException (
                __( 'Unable to find terminal.' )
            );
        }

        return $terminal;
    }

    /**
     * @return array
     */
    public function getList ()
    {
        $formattedTerminalList = [];

        // Terminal cities at top
        $topList = [
            'Vilnius',
            'Kaunas',
            'Klaipėda',
            'Šiauliai',
            'Panevežys',
            'Alytus',
            'Marijampolė',
            'Utena',
            'Telšiai',
            'Tauragė'
        ];

        /** @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressTerminals\Collection $terminalCollection */
        $terminalCollection = $this->_terminalCollectionFactory->create ()->getItems ();

        /** @var \Eshoper\LPShipping\Model\LPExpressTerminals $terminal */
        foreach ( $terminalCollection as $terminal ) {
            // Add city groups
            if ( !array_key_exists ( $terminal->getCity (), $formattedTerminalList ) ) {
                $formattedList [ $terminal->getCity () ] = [];
            }

            // Formatted grouped list by city
            $formattedTerminalList [ $terminal->getCity () ][ $terminal->getTerminalId () ]
                = trim ( sprintf ( '%s - %s', $terminal->getName (), $terminal->getAddress () ) );
        }

        // Sort terminals alphabetically
        foreach ( $formattedTerminalList as $key => $list ) {
            asort ( $formattedTerminalList [ $key ], SORT_ASC );
        }

        // Top sort cities
        $ordered = [];

        foreach ( $topList as $key ) {
            if ( array_key_exists ( $key, $formattedTerminalList ) ) {
                $ordered [ $key ] = $formattedTerminalList [ $key ];
                // Unset top listed cities
                unset ( $formattedTerminalList [ $key ] );
            }
        }

        // Sort alphabetically
        ksort ( $formattedTerminalList );

        // Concat
        $formattedTerminalList = $ordered + $formattedTerminalList;

        return $formattedTerminalList;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save ( \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterface $terminal )
    {
        /** @var \Eshoper\LPShipping\Model\LPExpressTerminals $terminal */
        $this->_terminalResource->save ( $terminal );
        return $terminal;
    }
}
