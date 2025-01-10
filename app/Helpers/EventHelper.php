<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Event;

class EventHelper
{
    public static function itemEvent($event, $items)
    {
        $eventItems = Event::dispatch($event);

        // Make multidimensional array flat
        $eventItems = array_reduce($eventItems, function ($carry, $item) {
            // Empty array or null?
            if (empty($item)) {
                return $carry;
            }
            // Is item a multidimensional array?
            if (is_array($item) && isset($item[0])) {
                return array_merge($carry, $item);
            }

            return array_merge($carry, [$item]);
        }, []);

        $items = array_merge($items, $eventItems);

        // Sort based on priority
        usort($items, function ($a, $b) {
            return ($a['priority'] ?? 0) <=> ($b['priority'] ?? 0);
        });

        return $items;
    }

    public static function renderEvent($event)
    {
        $eventItems = Event::dispatch($event);
        // Make multidimensional array flat
        $eventItems = array_reduce($eventItems, function ($carry, $item) {
            // Is item a multidimensional array?
            if (is_array($item) && isset($item[0])) {
                return array_merge($carry, $item);
            }

            return array_merge($carry, [$item]);
        }, []);

        // Sort based on priority
        usort($eventItems, function ($a, $b) {
            return ($a['priority'] ?? 0) <=> ($b['priority'] ?? 0);
        });

        $view = '';
        foreach ($eventItems as $item) {
            if (isset($item['view'])) {
                $view .= $item['view'];
            }
        }

        // Now we smash them together and return it as html
        return $view;
    }
}
