<?php

namespace App\Http\Controllers\Api;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Services\TmdbServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected TmdbServiceInterface $tmdbService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(MediaType $type, Request $request)
    {
        $page = (int) $request->input('page', 1);

        // Genres are served in chunks of 5 so the home feed can load them lazily
        $genrePages = array_chunk(config('tmdb.genres'), 5);
        $currentPageIndex = $page - 1;

        $response = [];

        if ($page === 1) {
            $heroList = $this->tmdbService->getMediaList("trending/{$type->value}/week");
            shuffle($heroList);
            $response['hero'] = $heroList;

            $response['popular'] = $this->tmdbService->getMediaList("{$type->value}/popular");
            $response['top_rated'] = $this->tmdbService->getMediaList("{$type->value}/top_rated");

            // TMDB has no shared "upcoming" endpoint: each media type exposes its own
            [$upcomingEndpoint, $upcomingLabel] = match ($type) {
                MediaType::Movie => ['movie/upcoming', 'Upcoming'],
                MediaType::Tv => ['tv/on_the_air', 'On the air'],
            };

            $response['upcoming'] = [
                'label' => $upcomingLabel,
                'data' => $this->tmdbService->getMediaList($upcomingEndpoint, ['region' => 'IT']),
            ];
        }

        if (isset($genrePages[$currentPageIndex])) {
            foreach ($genrePages[$currentPageIndex] as $genre) {
                $genreId = $genre[$type->value];

                // Some genres exist for only one media type (e.g. Music has no TV id)
                if (! $genreId) {
                    continue;
                }

                $response[$genre['key']] = [
                    'label' => $genre['label'],
                    'genreId' => $genreId,
                    'data' => $this->tmdbService->getMediaList("discover/{$type->value}", ['with_genres' => $genreId]),
                ];
            }
        }

        $response['hasMore'] = isset($genrePages[$page]);
        $response['nextPage'] = $response['hasMore'] ? $page + 1 : null;

        return response()->json($response);
    }
}
