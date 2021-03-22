<?php

namespace Tagwalk\ApiClientBundle\Manager;

use Tagwalk\ApiClientBundle\Model\WornLook;

class WornLookManager extends AbstractManager
{
    protected function getGetEndpoint(): string
    {
        return '/api/worn-looks/';
    }

    protected function getUpdateEndpoint(): string
    {
        return '/api/worn-looks/';
    }

    protected function getListEndpoint(): string
    {
        return '';
    }

    protected function getCreateEndpoint(): string
    {
        return '/api/worn-looks';
    }

    protected function getDeleteEndpoint(): string
    {
        return '/api/worn-looks/';
    }

    protected function getModelClass(): string
    {
        return WornLook::class;
    }
}
