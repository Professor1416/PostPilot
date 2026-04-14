<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Services\AnthropicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GenerateController extends Controller
{
    public function __construct(private AnthropicService $ai) {}

    public function generate(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'business_type' => 'required|string|max:100',
            'offer'         => 'required|string|max:500',
            'content_type'  => 'required|in:instagram,facebook,poster',
            'language'      => 'nullable|in:english,hindi,hinglish',
            'tone'          => 'nullable|in:friendly,professional,exciting,urgent',
            'festival'      => 'nullable|string|max:60',
        ]);

        try {
            $result = $this->ai->generateCaption([
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'offer'         => $request->offer,
                'content_type'  => $request->content_type,
                'language'      => $request->language ?? 'hinglish',
                'tone'          => $request->tone ?? 'friendly',
                'festival'      => $request->festival ?? 'None',
            ]);

            return response()->json(['result' => $result, 'content_type' => $request->content_type]);

        } catch (\Exception $e) {
            Log::error('Generate failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function festivals(Request $request)
    {
        $months = (int) ($request->months ?? 3);

        $festivals = Festival::upcoming($months)->get([
            'slug', 'name', 'name_hindi', 'date', 'emoji', 'region',
        ]);

        // Fallback if DB is empty
        if ($festivals->isEmpty()) {
            $festivals = collect($this->hardcodedFestivals());
        }

        return response()->json(['festivals' => $festivals]);
    }

    private function hardcodedFestivals(): array
    {
        $year = now()->year;
        return [
            ['slug' => "eid-$year",           'name' => 'Eid al-Fitr',      'date' => "$year-04-10", 'emoji' => '🌙', 'region' => 'national'],
            ['slug' => "dussehra-$year",       'name' => 'Dussehra',         'date' => "$year-10-02", 'emoji' => '🏹', 'region' => 'national'],
            ['slug' => "diwali-$year",         'name' => 'Diwali',           'date' => "$year-10-20", 'emoji' => '🪔', 'region' => 'national'],
            ['slug' => "christmas-$year",      'name' => 'Christmas',        'date' => "$year-12-25", 'emoji' => '🎄', 'region' => 'national'],
            ['slug' => "newyear-" . ($year+1), 'name' => 'New Year',         'date' => ($year+1)."-01-01", 'emoji' => '🎉', 'region' => 'national'],
            ['slug' => "republic-" . ($year+1),'name' => 'Republic Day',     'date' => ($year+1)."-01-26", 'emoji' => '🇮🇳', 'region' => 'national'],
            ['slug' => "holi-" . ($year+1),    'name' => 'Holi',             'date' => ($year+1)."-03-14", 'emoji' => '🎨', 'region' => 'national'],
            ['slug' => "independence-" . ($year+1), 'name' => 'Independence Day', 'date' => ($year+1)."-08-15", 'emoji' => '🇮🇳', 'region' => 'national'],
        ];
    }
}
