<?php

namespace MuhammadNawlo\FilamentScoutManager\DTO;

final readonly class ScoutModelConfigDTO
{
    public function __construct(
        public ?string $indexName = null,
        /** @var array<int, string>|null */
        public ?array $searchableFields = null,
        public ?string $engine = null,
        /** @var array<string, mixed>|null */
        public ?array $engineSettings = null,
        public ?string $queueConnection = null,
    ) {}
}
