<?php

namespace Tagwalk\ApiClientBundle\Model;

class CustomerFields
{
    public int $id;
    public string $slug;
    public string $name;
    public string $contentType;
    public string $fieldType;
    public bool $required;
    public bool $multiple;
}
