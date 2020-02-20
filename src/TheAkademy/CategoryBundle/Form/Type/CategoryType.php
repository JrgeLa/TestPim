<?php

namespace TheAkademy\CategoryBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Akeneo\Pim\Enrichment\Bundle\Form\Type\CategoryType as AkeneoCategoryType;

/**
 * @author Olivier Soulet <olivier.soulet@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 */
final class CategoryType extends AkeneoCategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);
    }
}
