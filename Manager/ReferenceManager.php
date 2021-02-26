<?php

namespace Tagwalk\ApiClientBundle\Manager;

use Tagwalk\ApiClientBundle\Model\Reference;

class ReferenceManager extends AbstractManager
{
    protected function getGetEndpoint(): string
    {
        return '/api/references/';
    }

    protected function getUpdateEndpoint(): string
    {
        return '/api/references/';
    }

    protected function getCreateEndpoint(): string
    {
        return '';
    }

    protected function getListEndpoint(): string
    {
        return '/api/references';
    }

    protected function getDeleteEndpoint(): string
    {
        return '';
    }

    protected function getModelClass(): string
    {
        return Reference::class;
    }
}
