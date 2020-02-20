<?php

namespace TheAkademy\CategoryBundle\Normalizer\Standard;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TheAkademy\CategoryBundle\Entity\Category;

class CategoryNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($category, $format = null, array $context = [])
    {
        return array_merge(
            $this->normalizer->normalize($category, $format, $context),
            ['description' => $category->getDescription()]
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Category && $format == 'standard';
    }
}