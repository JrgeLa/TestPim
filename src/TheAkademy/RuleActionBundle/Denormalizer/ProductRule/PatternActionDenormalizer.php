<?php


namespace TheAkademy\RuleActionBundle\Denormalizer\ProductRule;


use TheAkademy\RuleActionBundle\Model\ProductPatternAction;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class PatternActionDenormalizer extends GetSetMethodNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return parent::denormalize($data, 'TheAkademy\RuleActionBundle\Model\ProductPatternAction');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['type']) && ProductPatternAction::ACTION_TYPE === $data['type'];
    }
}