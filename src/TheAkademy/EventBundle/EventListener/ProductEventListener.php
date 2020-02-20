<?php


namespace TheAkademy\EventBundle\EventListener;


use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Updater\ProductUpdater;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductEventListener
{
    /** @var ProductUpdater */
    private $prUpdater;

    /**
     * @param ProductUpdater $prUpdater
     */
    public function __construct( $prUpdater )
    {
        $this->prUpdater = $prUpdater;
    }

    public function updateDescription(GenericEvent $event)
    {
        $product = $event->getSubject();

        if (!$product instanceof ProductInterface) {
            return;
        }

        $valid['values']['description'][] = [
            'locale' => 'en_US',
            'scope' => 'ecommerce',
            'data' => 'event description data',
        ];

        $this->prUpdater->update($product, $valid);
    }
}