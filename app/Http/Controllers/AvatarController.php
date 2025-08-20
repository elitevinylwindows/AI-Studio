<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AvatarController extends Controller
{
    /**
     * Show Avatar gallery page
     */
    public function index(Request $request)
    {
        // If you have a Model\App\Models\Avatar, replace this with a DB query.
        // For now we ship demo data that mirrors your screenshot.
        $myAvatars = [
            [
                'id' => 1,
                'name' => 'Mr Elite',
                'looks' => 1,
                'image' => asset('images/avatars/mr_elite.png'), // put a real file or keep placeholder below
            ],
        ];

        // Public avatars demo list
        $publicAvatars = [
            [
                'id' => 101,
                'name' => 'Expressive Host',
                'tags' => ['Professional'],
                'image' => 'https://placehold.co/600x400?text=Avatar+1',
            ],
            [
                'id' => 102,
                'name' => 'Tech Presenter',
                'tags' => ['Professional','UGC'],
                'image' => 'https://placehold.co/600x400?text=Avatar+2',
            ],
            [
                'id' => 103,
                'name' => 'Lifestyle Creator',
                'tags' => ['Lifestyle','Community'],
                'image' => 'https://placehold.co/600x400?text=Avatar+3',
            ],
            [
                'id' => 104,
                'name' => 'Studio Anchor',
                'tags' => ['Professional'],
                'image' => 'https://placehold.co/600x400?text=Avatar+4',
            ],
        ];

        // If you want to accept search/filter from querystring:
        $q = trim($request->get('q', ''));
        $tag = $request->get('tag');

        $filtered = collect($publicAvatars)->filter(function ($a) use ($q, $tag) {
            $ok = true;
            if ($q !== '') {
                $ok = stripos($a['name'], $q) !== false;
            }
            if ($ok && $tag) {
                $ok = in_array($tag, $a['tags'] ?? []);
            }
            return $ok;
        })->values()->all();

        $filters = ['All','Professional','Lifestyle','UGC','AI-generated','Community','Favorites'];

        return view('avatar.index', [
            'myAvatars' => $myAvatars,
            'publicAvatars' => $filtered,
            'filters' => $filters,
            'activeTag' => $tag ?: 'All',
            'q' => $q,
        ]);
    }
}
