<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Content;

class SearchController {
    public function search(Request $request): void {
        $q = trim((string)$request->query('q', ''));
        $type = (string)$request->query('type', 'all');
        $results = [];

        if ($q !== '') {
            $results = Content::search($q, $type, 30);
        }

        Response::view('public/search', [
            'title' => ($q ? "Search: {$q}" : 'Search Platform') . ' | TechScout',
            'query' => $q,
            'currentType' => $type,
            'results' => $results
        ]);
    }
}
