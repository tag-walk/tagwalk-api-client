<?php

namespace Tagwalk\ApiClientBundle\Manager;

use Tagwalk\ApiClientBundle\Model\Reference;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ReferenceManager extends AbstractManager
{
    protected function setGetEndpoint()
    {
        $this->getEndpoint = '/api/references/';
    }

    protected function setUpdateEndpoint()
    {
        $this->updateEndpoint = '/api/references/';
    }

    protected function setListEndpoint()
    {
        $this->listEndpoint = '/api/references';
    }

    protected function setModelClass()
    {
        $this->modelClass = Reference::class;
    }
}
