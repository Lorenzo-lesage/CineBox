<?php

namespace App\Services;

use App\Data\GenreMediaListData;
use App\Data\MovieData;
use App\Enums\MediaType;
use App\Enums\SortOption;

interface TmdbServiceInterface
{
    public function getMedia(MediaType $type, int $tmdbId, string $lang = 'en-US'): MovieData;

    public function getMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array;

    public function getMediaTrailer(MediaType $type, int $tmdbId): ?string;

    public function getSortValue(SortOption $sort, MediaType $type): string;

    public function getPaginatedMediaList(
        string $endpoint,
        array $params = [],
        int $page = 1,
        string $lang = 'en-US',
        string $sortBy = 'popularity.desc',
        array $genre = [],
    ): GenreMediaListData;

    public function getSearchMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array;
}
