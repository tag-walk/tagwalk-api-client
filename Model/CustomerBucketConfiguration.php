<?php

namespace Tagwalk\ApiClientBundle\Model;

class CustomerBucketConfiguration
{
    public int $id;
    public string $region;
    public string $bucketMedia;
    public string $bucketCacheMedia;
    public bool $usePrivateBucket;
    public ?string $defaultMediaType;
    public ?string $cdn;
}
