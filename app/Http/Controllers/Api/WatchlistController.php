<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\WatchlistItem;
use App\Services\OmdbService;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function __construct(private OmdbService $omdbService)
    {
    }

    public function index(Request $request)
    {
        $query = $request->user()->watchlistItems()
            ->with('movie');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->paginate($request->get('per_page', 15));

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'imdb_id' => 'required_without:title|string|nullable',
            'title'   => 'required_without:imdb_id|string|nullable',
        ]);

        // Find or fetch movie
        $movie = $this->findOrFetchMovie($request);

        if (!$movie) {
            return response()->json([
                'message' => 'Movie not found.',
            ], 404);
        }

        // Check if already in watchlist
        $exists = $request->user()->watchlistItems()
            ->where('movie_id', $movie->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Movie already in your watchlist.',
            ], 409);
        }

        $item = $request->user()->watchlistItems()->create([
            'movie_id' => $movie->id,
            'status'   => 'to_watch',
        ]);

        return response()->json($item->load('movie'), 201);
    }

    public function show(Request $request, WatchlistItem $watchlistItem)
    {
        if ($watchlistItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json($watchlistItem->load('movie'));
    }

    public function update(Request $request, WatchlistItem $watchlistItem)
    {
        if ($watchlistItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'status'      => 'sometimes|in:to_watch,watching,watched',
            'user_rating' => 'sometimes|nullable|integer|min:1|max:10',
            'notes'       => 'sometimes|nullable|string',
            'watched_at'  => 'sometimes|nullable|date',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'watched' && !isset($validated['watched_at'])) {
            $validated['watched_at'] = now();
        }

        $watchlistItem->update($validated);

        return response()->json($watchlistItem->load('movie'));
    }

    public function destroy(Request $request, WatchlistItem $watchlistItem)
    {
        if ($watchlistItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $watchlistItem->delete();

        return response()->json(['message' => 'Item removed from watchlist.'], 200);
    }

    private function findOrFetchMovie(Request $request): ?Movie
    {
        if ($request->imdb_id) {
            $movie = Movie::where('imdb_id', $request->imdb_id)->first();
            if ($movie) return $movie;

            $data = $this->omdbService->findByImdbId($request->imdb_id);
        } else {
            $data = $this->omdbService->findByTitle($request->title);
        }

        if (!$data) return null;

        return Movie::firstOrCreate(
            ['imdb_id' => $data['imdbID']],
            [
                'title'       => $data['Title'],
                'year'        => $data['Year'],
                'genre'       => $data['Genre'],
                'director'    => $data['Director'],
                'actors'      => $data['Actors'],
                'plot'        => $data['Plot'],
                'poster_url'  => $data['Poster'],
                'imdb_rating' => $data['imdbRating'],
                'runtime'     => $data['Runtime'],
                'country'     => $data['Country'],
            ]
        );
    }
}
