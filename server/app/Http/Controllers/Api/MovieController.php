<?php

namespace App\Http\Controllers\Api;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Services\TmdbServiceInterface;

class MovieController extends Controller
{
    public function __construct(protected TmdbServiceInterface $tmdbService) {}

    /**
     * Display the specified resource.
     */
    public function show(MediaType $type, int $tmdbId)
    {
        $movieData = $this->tmdbService->getMedia($type, $tmdbId, 'en-US');

        // 2. Check if we have local data (ratings, etc.)
        $localMovie = Movie::firstWhere('tmdb_id', $tmdbId);
        if ($localMovie) {
            $movieData->community_rating = $localMovie->avg_rating;
        }

        return response()->json($movieData);
    }

    /**
     * Summary of trailer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trailer(MediaType $type, int $tmdbId)
    {
        $trailerKey = $this->tmdbService->getMediaTrailer($type, $tmdbId);

        if (! $trailerKey) {
            return response()->json(['message' => 'Trailer not found'], 404);
        }

        return response()->json([
            'trailer_key' => $trailerKey,
        ]);
    }
}
