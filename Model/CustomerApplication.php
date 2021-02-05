<?php

namespace Tagwalk\ApiClientBundle\Model;

class CustomerApplication
{
    public int $id;
    public string $name;
    public string $applicationType;
    public string $company;
    public ?string $logo;
    public ?string $email;
    public bool $watermarked;
    public ?CustomerBucketConfiguration $customerBucketConfiguration;
    public bool $sharedTags;

    /**
     * @var CustomerField[]
     */
    public array $customerFields = [];

    public function hasCustomReferenceField(): bool
    {
        return array_filter(
            $this->customerFields,
            fn($field) => $field->fieldType === CustomerField::FIELD_TYPE_REFERENCE
        ) !== [];
    }

    public function getBucket(): ?string
    {
        return !empty($this->customerBucketConfiguration->bucketMedia)
            ? $this->customerBucketConfiguration->bucketMedia
            : null;
    }

    public function getBucketCache(): ?string
    {
        return !empty($this->customerBucketConfiguration->bucketCacheMedia)
            ? $this->customerBucketConfiguration->bucketCacheMedia
            : null;
    }

    public function getCdn(): ?string
    {
        return $this->useDedicatedCdn()
            ? $this->customerBucketConfiguration->cdn
            : null;
    }

    public function useDedicatedCdn(): bool
    {
        return !empty($this->customerBucketConfiguration->cdn);
    }

    public function usePrivateCdn(): bool
    {
        return $this->useDedicatedCdn()
            && !empty($this->customerBucketConfiguration->usePrivateBucket);
    }
}
