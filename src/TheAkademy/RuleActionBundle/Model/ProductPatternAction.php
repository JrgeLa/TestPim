<?php


namespace TheAkademy\RuleActionBundle\Model;

use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\FieldImpactActionInterface;

class ProductPatternAction implements ActionInterface, FieldImpactActionInterface
{
    const ACTION_TYPE = 'pattern';

    /** @var string */
    protected $field;

    /** @var array */
    protected $attributes = [];

    /** @var string */
    protected $pattern;

    /** @var array */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * {@inheritdoc}
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    /**
     * @return array
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getImpactedFields()
    {
        return [$this->getField()];
    }

}