<?php

declare(strict_types=1);

namespace Auctane\Api\Api\Data;

interface OrderAddressTimestampInterface
{
    const ENTITY_ID = 'entity_id';
    const UPDATED_AT = 'updated_at';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set entity ID
     *
     * @param int $value
     * @return $this
     */
    public function setId(int $value): self;

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Set updated at
     *
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt(string $value): self;
}
