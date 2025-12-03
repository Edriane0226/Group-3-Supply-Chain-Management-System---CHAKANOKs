<?php

namespace App\Libraries;

/**
 * Route Optimizer
 * Simple distance-based route optimization using Haversine formula
 */
class RouteOptimizer
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Optimize route using Nearest Neighbor algorithm
     * Starts from a starting point and finds the nearest unvisited location
     * 
     * @param array $destinations Array of destinations with ['id', 'latitude', 'longitude', 'name']
     * @param float|null $startLat Starting latitude (if null, uses first destination)
     * @param float|null $startLon Starting longitude (if null, uses first destination)
     * @return array Optimized route with ['route' => [...], 'total_distance' => float, 'total_time' => int]
     */
    public function optimizeRoute(array $destinations, ?float $startLat = null, ?float $startLon = null): array
    {
        if (empty($destinations)) {
            return [
                'route' => [],
                'total_distance' => 0,
                'total_time' => 0
            ];
        }

        // If starting point not provided, use first destination
        if ($startLat === null || $startLon === null) {
            $startLat = (float)$destinations[0]['latitude'];
            $startLon = (float)$destinations[0]['longitude'];
        }

        $route = [];
        $unvisited = $destinations;
        $currentLat = $startLat;
        $currentLon = $startLon;
        $totalDistance = 0;
        $averageSpeed = 40; // Average speed in km/h for delivery vehicles

        while (!empty($unvisited)) {
            $nearestIndex = null;
            $nearestDistance = PHP_FLOAT_MAX;

            // Find nearest unvisited destination
            foreach ($unvisited as $index => $destination) {
                if ($destination['latitude'] === null || $destination['longitude'] === null) {
                    continue; // Skip destinations without coordinates
                }

                $distance = $this->calculateDistance(
                    $currentLat,
                    $currentLon,
                    (float)$destination['latitude'],
                    (float)$destination['longitude']
                );

                if ($distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestIndex = $index;
                }
            }

            // If no valid destination found, break
            if ($nearestIndex === null) {
                break;
            }

            // Move to nearest destination
            $nearest = $unvisited[$nearestIndex];
            $route[] = [
                'id' => $nearest['id'],
                'name' => $nearest['name'] ?? 'Unknown',
                'latitude' => $nearest['latitude'],
                'longitude' => $nearest['longitude'],
                'distance_from_previous' => $nearestDistance,
                'sequence' => count($route) + 1
            ];

            $totalDistance += $nearestDistance;
            $currentLat = (float)$nearest['latitude'];
            $currentLon = (float)$nearest['longitude'];

            // Remove from unvisited
            unset($unvisited[$nearestIndex]);
            $unvisited = array_values($unvisited); // Re-index array
        }

        // Calculate estimated time (distance / speed * 60 minutes)
        $totalTime = (int)round(($totalDistance / $averageSpeed) * 60);

        return [
            'route' => $route,
            'total_distance' => round($totalDistance, 2),
            'total_time' => $totalTime // in minutes
        ];
    }

    /**
     * Get route coordinates for all destinations in optimized order
     */
    public function getRouteCoordinates(array $optimizedRoute): array
    {
        $coordinates = [];
        foreach ($optimizedRoute['route'] as $stop) {
            $coordinates[] = [
                'lat' => $stop['latitude'],
                'lng' => $stop['longitude'],
                'name' => $stop['name'],
                'sequence' => $stop['sequence']
            ];
        }
        return $coordinates;
    }
}

