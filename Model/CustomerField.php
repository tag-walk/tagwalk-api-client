<?php

namespace Tagwalk\ApiClientBundle\Model;

class CustomerField
{
    const FIELD_TYPE_BOOLEAN = 'boolean';

    /**
     * The field accepts different values listed by the endpoint GET /api/references
     */
    const FIELD_TYPE_REFERENCE = 'reference';

    /**
     * The value is an integer >= 0
     */
    const FIELD_TYPE_UINT = 'uint';

    const FIELD_TYPE_TEXT = 'text';

    public int $id;
    public string $slug;
    public string $name;
    public string $contentType;
    public string $fieldType;
    public bool $required;
    public bool $multiple;
}
