<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Reference
{
    use Traits\Nameable;
    use Traits\NameTranslatable;
    use Traits\Sluggable;
    use Traits\Statusable;
    use Traits\Timestampable;

    /**
     * @var null|ReferenceValue[]
     * @Assert\Valid()
     */
    public ?array $values = null;
}
