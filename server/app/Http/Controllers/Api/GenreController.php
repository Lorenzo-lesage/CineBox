<?php

namespace App\Http\Controllers\Api;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Services\TmdbServiceInterface;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __construct(
        protected TmdbServiceInterface $tmdbService
    ) {}

    /**
     * Summary of index
     * Index of genres
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(MediaType $type)
    {

        // Retrieve the genres from the config/tmdb.php file we created
        $genres = config('tmdb.genres');

        // Filter out genres that don't have an ID for the given media type
        // (e.g. 'Kids' doesn't have a movie ID, so we hide it when type=movie)
        $filtered = array_filter($genres, function ($genre) use ($type) {
            return ! is_null($genre[$type->value]);
        });

        // Use array_values to reset the array keys after filtering
        return response()->json(array_values($filtered));
    }

    /**
     * Paginated media by genre.
     */
    public function media(MediaType $type, int $genreId, Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */
        $page = (int) $request->input('page', 1);
        $sortBy = $this->tmdbService->getSortValue($request->input('sort_by', 'popular'));
        $endpoint = "discover/{$type->value}";

        $genre = collect(config('tmdb.genres'))
            ->first(function (array $genre) use ($type, $genreId) {
                return (int) ($genre[$type->value] ?? 0) === $genreId;
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json(
            $this->tmdbService->getPaginatedMediaList(
                $endpoint,
                ['with_genres' => $genreId],
                $page,
                'en-US',
                $sortBy,
                [
                    'type' => $type->value,
                    'id' => $genreId,
                    'key' => $genre['key'] ?? null,
                    'label' => $genre['label'] ?? null,
                ],
            )
        );
    }
}
