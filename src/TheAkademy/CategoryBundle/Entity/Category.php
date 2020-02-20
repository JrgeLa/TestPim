<?php

namespace TheAkademy\CategoryBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Category\Model\Category as AkeneoCategory;

/**
 * @author Olivier Soulet <olivier.soulet@akeneo.com>
 */
class Category extends AkeneoCategory
{
    /** @var string */
    private $description;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
