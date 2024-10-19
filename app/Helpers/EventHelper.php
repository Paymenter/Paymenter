<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Event;

class EventHelper
{
    public static function itemEvent($items, $event){
        $eventItems = Event::dispatch($event);
        $items = array_merge($items, $eventItems);

        // Sort based on priority
        usort($items, function ($a, $b) {
            return ($a['priority'] ?? 0) <=> ($b['priority'] ?? 0);
        });
        
        return $items;
    }
}