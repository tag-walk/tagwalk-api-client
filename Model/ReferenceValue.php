<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ReferenceValue
{
    use Traits\Nameable;
    use Traits\NameTranslatable;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-z0-9]+(?:-[a-z0-9]+)*$/")
     */
    public string $slug;
}
