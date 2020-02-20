<?php


namespace TheAkademy\RuleActionBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class ExistingAttributes extends Constraint
{
    /** @var string */
    public $message = 'There are no attributes with such code: "%attribute%"';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'pimee_constraint_attributes_validator';
    }
}