<?php

namespace App\Services;

use App\Data\GenreMediaListData;
use App\Data\MovieData;
use App\Enums\MediaType;
use App\Enums\SortOption;
use Illuminate\Support\Facades\Cache;

class CachingTmdbService implements TmdbServiceInterface
{
    protected TmdbServiceInterface $inner;

    public function __construct(TmdbServiceInterface $inner)
    {
        $this->inner = $inner;
    }

    public function getSortValue(SortOption $sort, MediaType $type): string
    {
        // No cache needed for a simple mapping
        return $this->inner->getSortValue($sort, $type);
    }

    /**
     * Get a movie from TMDB
     * Summary of getMovie
     *
     * @see https://developers.themoviedb.org/3/movies/get-movie-details
     */
    public function getMedia(MediaType $type, int $tmdbId, string $lang = 'en-US'): MovieData
    {
        $key = "movie_{$type->value}_{$tmdbId}_{$lang}";
        $cache = Cache::tags(['movies']);

        if ($cached = $cache->get($key)) {
            return $cached;
        }

        return Cache::lock("lock_{$key}", 10)->block(5, function () use ($type, $tmdbId, $lang, $key, $cache) {
            if ($cached = $cache->get($key)) {
                return $cached;
            }

            $movie = $this->inner->getMedia($type, $tmdbId, $lang);
            $cache->put($key, $movie, now()->addDay());

            return $movie;
        });
    }

    /**
     * Get a list of movies from TMDB
     * Summary of getMoviesList
     *
     * @see https://developers.themoviedb.org/3/discover/movie-discover
     */
    public function getMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array
    {
        $cacheKey = 'list_'.md5($endpoint.serialize($params).$page.$lang.$sortBy);

        return Cache::tags(['movies', 'lists'])->remember($cacheKey, now()->addHours(6), function () use ($endpoint, $lang, $params, $page, $sortBy) {
            return $this->inner->getMediaList($endpoint, $params, $page, $lang, $sortBy);
        });
    }

    /**
     * Summary of getSearchMediaList
     */
    public function getSearchMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array
    {
        $cacheKey = 'Search_'.md5($endpoint.serialize($params).$page.$lang.$sortBy);

        return Cache::tags(['search'])->remember($cacheKey, now()->addHours(6), function () use ($endpoint, $lang, $params, $page, $sortBy) {
            return $this->inner->getSearchMediaList($endpoint, $params, $page, $lang, $sortBy);
        });
    }

    /**
     * Get a paginated media list from TMDB.
     */
    public function getPaginatedMediaList(
        string $endpoint,
        array $params = [],
        int $page = 1,
        string $lang = 'en-US',
        string $sortBy = 'popularity.desc',
        array $genre = [],
    ): GenreMediaListData {
        $cacheKey = 'paginated_list_'.md5(
            $endpoint.serialize($params).$page.$lang.$sortBy.serialize($genre)
        );

        return Cache::tags(['movies', 'lists'])->remember($cacheKey, now()->addHours(6), function () use (
            $endpoint,
            $params,
            $page,
            $lang,
            $sortBy,
            $genre
        ) {
            return $this->inner->getPaginatedMediaList(
                $endpoint,
                $params,
                $page,
                $lang,
                $sortBy,
                $genre,
            );
        });
    }

    /**
     * Get a movie trailer from TMDB
     * Summary of getMovieTrailer
     *
     * @see https://developers.themoviedb.org/3/movies/get-movie-videos
     */
    public function getMediaTrailer(MediaType $type, int $tmdbId): ?string
    {
        $key = "movie_trailer_{$type->value}_{$tmdbId}";

        return Cache::remember($key, now()->addWeek(), function () use ($type, $tmdbId) {
            return $this->inner->getMediaTrailer($type, $tmdbId);
        });
    }
}
