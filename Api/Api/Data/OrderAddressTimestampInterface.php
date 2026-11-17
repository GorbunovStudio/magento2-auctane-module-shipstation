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
     * The parameter stays untyped to remain compatible with AbstractModel::setId($value).
     *
     * @param int|null $value
     * @return $this
     */
    public function setId($value): self;

    /**
     * Get updated at
     *
     * @return string|null Null until the entity is saved or loaded
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set updated at
     *
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt(string $value): self;
}
