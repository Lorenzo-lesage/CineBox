<?php

namespace App\Services;

use App\Data\GenreMediaListData;
use App\Data\MovieData;
use App\Data\MovieListData;
use App\Enums\MediaType;
use App\Exceptions\TmdbApiException;
use Illuminate\Support\Facades\Http;

class TmdbService implements TmdbServiceInterface
{
    protected string $token;

    protected string $baseUrl;

    private const TIMEOUT = 5;

    private const RETRY_ATTEMPTS = 3;

    private const RETRY_DELAY = 300;

    private const MOVIE_APPEND = [
        'videos',
        'credits',
        'images',
        'keywords',
        'release_dates',
        'similar',
        'recommendations',
        'watch_providers',
    ];

    public function __construct(?string $token = null, ?string $baseUrl = null)
    {
        $this->token = $token ?? config('services.tmdb.token');
        $this->baseUrl = $baseUrl ?? config('services.tmdb.base_url');
    }

    /**
     * Get the sort value based on the provided key
     */
    public function getSortValue(string $sortKey): string
    {
        $allowedSorts = [
            'popular' => 'popularity.desc',
            'top_rated' => 'vote_average.desc',
            'latest' => 'release_date.desc',
            'newest' => 'primary_release_date.desc',
            'oldest' => 'primary_release_date.asc',
            'title_az' => 'title.asc',
            'title_za' => 'title.desc',
        ];

        return $allowedSorts[$sortKey] ?? $allowedSorts['popular'];
    }

    /**
     * Get a movie from TMDB
     * Summary of getMovie
     *
     * @see https://developers.themoviedb.org/3/movies/get-movie-details
     */
    public function getMedia(MediaType $type, int $tmdbId, string $lang = 'en-US'): MovieData
    {
        $appendToResponse = implode(',', self::MOVIE_APPEND);

        $response = $this->request('GET', "/{$type->value}/{$tmdbId}", [
            'language' => $lang,
            'append_to_response' => $appendToResponse,
        ]);

        return MovieData::fromTmdb($response->json());
    }

    /**
     * Get a list of movies from TMDB
     * Summary of getMoviesList
     *
     * @see https://developers.themoviedb.org/3/discover/movie-discover
     */
    public function getMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array
    {
        $response = $this->request('GET', "/{$endpoint}", array_merge([
            'language' => $lang,
            'page' => $page,
            'sort_by' => $sortBy,
        ], $params));

        $results = collect($response->json('results'));

        return $results->take(10)
            ->map(fn (array $movie) => MovieListData::fromTmdb($movie)->toArray())
            ->toArray();
    }

    /**
     * Get a list of movies from TMDB
     * Summary of getSearchMediaList
     */
    public function getSearchMediaList(string $endpoint, array $params = [], int $page = 1, string $lang = 'en-US', string $sortBy = 'popularity.desc'): array
    {
        $response = $this->request('GET', "/{$endpoint}", array_merge([
            'language' => $lang,
            'page' => $page,
            'sort_by' => $sortBy,
        ], $params));

        $data = $response->json();
        $results = collect($data['results'] ?? []);

        /*
    | -------------------------------------------------------------------------
    | Envelope Pattern
    | -------------------------------------------------------------------------
    | We return an envelope containing the results and the pagination info.
    */
        return [
            'results' => $results->map(fn (array $movie) => MovieListData::fromTmdb($movie)->toArray())->toArray(),
            'pagination' => [
                'current_page' => (int) ($data['page'] ?? 1),
                'total_pages' => (int) ($data['total_pages'] ?? 1),
                'total_results' => (int) ($data['total_results'] ?? 0),
            ],
        ];
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
        $response = $this->request('GET', "/{$endpoint}", array_merge([
            'language' => $lang,
            'page' => $page,
            'sort_by' => $sortBy,
        ], $params));

        return GenreMediaListData::fromTmdb([
            ...$response->json(),
            'genre' => $genre,
        ]);
    }

    /**
     * Get a movie trailer from TMDB
     * Summary of getMovieTrailer
     *
     * @see https://developers.themoviedb.org/3/movies/get-movie-videos
     */
    public function getMediaTrailer(MediaType $type, int $tmdbId): ?string
    {
        $response = $this->request('GET', "/{$type->value}/{$tmdbId}/videos");

        return collect($response->json('results'))
            ->where('site', 'YouTube')
            ->where('type', 'Trailer')
            ->first()['key'] ?? null;
    }

    /**
     * Centralized request method to handle auth, timeouts, and retries.
     */
    protected function request(string $method, string $endpoint, array $query = [])
    {
        $response = Http::withToken($this->token)
            ->timeout(self::TIMEOUT)
            ->retry(self::RETRY_ATTEMPTS, self::RETRY_DELAY)
            ->{$method}($this->baseUrl.$endpoint, $query);

        if (! $response->successful()) {
            throw new TmdbApiException('TMDB API Error: '.$response->status().' - '.$response->body());
        }

        return $response;
    }
}
