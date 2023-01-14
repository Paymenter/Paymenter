<?php

namespace App\Classes;

class API {
    public static function repaginate(mixed $data) {
        $data = $data->toArray();
        return [
            'metadata' => [
                'total_items' => $data['total'],
                'total_pages' => $data['last_page'],
                'max_per_page' => $data['per_page'],
                'item_count' => count($data['data']),
            ],
            'data' => $data['data'],
        ];
    }
}
