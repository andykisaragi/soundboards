<?php

namespace App\Http\Controllers;

use App\Models\SoundBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class FreesoundSearchController extends Controller
{
    protected string $apiKey;
    protected string $baseUrl = 'https://freesound.org/apiv2';

    public function __construct()
    {
        $this->apiKey = config('services.freesound.api_key');
    }

    public function index()
    {
        $boards = SoundBoard::with('sounds')->latest()->get();

        return Inertia::render('Freesound/Search', [
            'boards' => $boards,
        ]);
    }

    /**
     * Perform a search and return results via Inertia.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query'     => 'nullable|string|max:500',
            'page'      => 'nullable|integer|min:1',
            'page_size' => 'nullable|integer|min:1|max:150',
            'sort'      => 'nullable|string|in:score,duration_desc,duration_asc,created_desc,created_asc,downloads_desc,downloads_asc,rating_desc,rating_asc',
            'filter'    => 'nullable|string|max:1000',
        ]);

        $params = $this->buildSearchParams($request);
        $cacheKey = 'freesound_search_' . md5(json_encode($params));

        $data = Cache::remember($cacheKey, 600, function () use ($params) {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . '/search/', $params);

            if ($response->failed()) {
                return ['error' => true, 'message' => $response->json('detail') ?? 'API error'];
            }



            return $response->json();
        });

        if (isset($data['error'])) {
            return redirect()->back()->withErrors(['api' => $data['message']]);
        }


//
//        $keys = array_rand($data['results'], 9);
//        $randomResults = [];
//        foreach ($keys as $key) {
//            $randomResults[$key] = $data['results'][$key];
//        }
//        $data['results'] = $randomResults;

        $boards = SoundBoard::with('sounds')->latest()->get();

        return Inertia::render('Freesound/Search', [
            'results' => $data,
            'filters' => $request->only(['query', 'sort', 'page_size', 'filter', 'page']),
            'boards'  => $boards,
        ]);
    }

    /**
     * Show a single sound's details.
     */
    public function show(int $soundId)
    {
        $fields = 'id,name,tags,username,license,duration,previews,description,avg_rating,num_downloads,type,images,url,download,geotag,created';

        $cacheKey = "freesound_sound_{$soundId}";

        $sound = Cache::remember($cacheKey, 600, function () use ($soundId, $fields) {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . "/sounds/{$soundId}/", [
                'fields' => $fields,
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if (!$sound) {
            return redirect()->route('freesound.index')
                ->withErrors(['api' => 'Sound not found']);
        }

        // Also fetch similar sounds
        $similar = Cache::remember("freesound_similar_{$soundId}", 600, function () use ($soundId) {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . "/sounds/{$soundId}/similar/", [
                'page_size' => 8,
                'fields'    => 'id,name,tags,username,duration,previews,images',
            ]);

            if ($response->failed()) {
                return [];
            }

            return $response->json('results') ?? [];
        });

        return Inertia::render('Freesound/Show', [
            'sound'   => $sound,
            'similar' => $similar,
        ]);
    }

    protected function buildSearchParams(Request $request): array
    {
        $fields = 'id,name,tags,username,license,duration,previews,avg_rating,images,url';

        $params = [
            'query'     => $request->input('query', ''),
            'page'      => 1, // $request->input('page', 1),
            'page_size' => 200, // $request->input('page_size', 15),
            'sort'      => $request->input('sort', 'score'),
            'fields'    => $fields,
        ];

        $params['filter'] = 'duration:[0 TO 5]';
        if ($filter = $request->input('filter')) {
            $params['filter'] = $filter;
        }

        if ($request->boolean('group_by_pack')) {
            $params['group_by_pack'] = 1;
        }

        return array_filter($params, fn($value) => $value !== '' && $value !== null);
    }

    public function saveSoundBoard(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'search_term' => 'required|string|max:500',
            'sounds'      => 'required|array|min:1',
            'sounds.*.id'       => 'required|integer',
            'sounds.*.name'     => 'required|string',
            'sounds.*.username' => 'required|string',
            'sounds.*.previews' => 'nullable|array',
            'sounds.*.images'   => 'nullable|array',
        ]);

        $board = SoundBoard::create([
            'title'       => $validated['title'],
            'search_term' => $validated['search_term'],
        ]);

        // addSounds will find-or-create each Sound and attach to the board
        $board->addSounds($validated['sounds']);

        return redirect()->back()->with(
            'success',
            "SoundBoard '{$board->title}' saved with " . count($validated['sounds']) . " sounds!"
        );
    }

    public function destroySoundBoard(SoundBoard $soundBoard)
    {
        $soundBoard->delete();

        return redirect()->back()->with('success', 'SoundBoard deleted.');
    }


}
