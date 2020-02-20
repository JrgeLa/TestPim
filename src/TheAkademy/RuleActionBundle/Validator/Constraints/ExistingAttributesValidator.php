<?php


namespace TheAkademy\RuleActionBundle\Validator\Constraints;

use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExistingAttributesValidator extends ConstraintValidator
{
    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(AttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($attributes, Constraint $constraint)
    {
        foreach ($attributes as $attribute) {
            if (null === $this->attributeRepository->findOneByIdentifier($attribute)) {
                $this->context->buildViolation($constraint->message, ['%attribute%' => $attribute])->addViolation();
            }
        }
}