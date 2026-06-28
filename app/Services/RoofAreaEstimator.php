<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RoofAreaEstimator
{
    /**
     * Estimate roof area in m² from:
     * 1) Stored DB values (polygon_area or height*width)
     * 2) OSM Overpass building polygon nearest to (lat,lon)
     * 3) Fallback from heated area + stories + pitch
     */
    public function estimate(?float $lat, ?float $lon, ?string $address, array $stored = []): array
    {
        // 1) DB direct
        if (!empty($stored['polygon_area'])) {
            return $this->result((float)$stored['polygon_area'], 'db_polygon', [
                'note' => 'polygon_area aus new_leads',
            ]);
        }
        if (!empty($stored['polygon_height']) && !empty($stored['polygon_width'])) {
            $area = (float)$stored['polygon_height'] * (float)$stored['polygon_width'];
            return $this->result($area, 'db_rect', [
                'note' => 'polygon_height × polygon_width',
            ]);
        }

        // 2) OSM Overpass (if coords present)
        if ($lat && $lon) {
            $osm = $this->queryOverpassForBuilding($lat, $lon);
            if ($osm && !empty($osm['area_m2'])) {
                return $this->result($osm['area_m2'], 'osm_building', [
                    'distance_m' => $osm['distance_m'] ?? null,
                    'nodes'      => count($osm['coords'] ?? []),
                ]);
            }
        }

        // 3) Fallback from heated area + stories + pitch
        $heated   = (float)($stored['heated_area'] ?? 0);
        $stories  = max(1, (int)($stored['stories'] ?? 1));
        $pitchStr = (string)($stored['roof_pitch'] ?? '');
        $pitchDeg = $this->parsePitchDegrees($pitchStr) ?? 30.0; // default 30°
        $cos      = cos(deg2rad($pitchDeg));
        $cos      = $cos > 0 ? $cos : 0.7; // guard

        if ($heated > 0) {
            // very rough: floor area ≈ heated_area / stories; roof surface ~ floor_area / cos(pitch)
            $floor = $heated / $stories;
            $area  = $floor / $cos;
            return $this->result($area, 'fallback_heated_area', [
                'assumptions' => [
                    'stories' => $stories,
                    'pitch_deg' => $pitchDeg,
                ],
            ]);
        }

        return [
            'area_m2'     => null,
            'source'      => 'unknown',
            'assumptions' => ['note' => 'Insufficient data'],
        ];
    }

    protected function parsePitchDegrees(string $pitch): ?float
    {
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*°?/', $pitch, $m)) {
            return (float)str_replace(',', '.', $m[1]);
        }
        // sometimes strings like "30deg"
        if (preg_match('/(\d+(?:[.,]\d+)?)/', $pitch, $m)) {
            return (float)str_replace(',', '.', $m[1]);
        }
        return null;
    }

    protected function result(float $area, string $source, array $assumptions = []): array
    {
        return [
            'area_m2'     => (int) round($area), // whole m²
            'source'      => $source,
            'assumptions' => $assumptions,
        ];
    }

    /**
     * Query Overpass API for nearest building polygon around given point.
     * Returns ['coords'=>[[lat,lon],...], 'area_m2'=>..., 'distance_m'=>...]
     */
    protected function queryOverpassForBuilding(float $lat, float $lon): ?array
    {
        $cacheKey = "roof-osm-".round($lat,6).",".round($lon,6);
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($lat, $lon) {
            $radius = 60; // meters
            $query = <<<QL
                    [out:json][timeout:25];
                    (
                    way["building"](around:$radius,$lat,$lon);
                    relation["building"](around:$radius,$lat,$lon);
                    );
                    out geom;
                    QL;

            $res = Http::asForm()->timeout(20)
                ->post(config('services.overpass.url', 'https://overpass-api.de/api/interpreter'), [
                    'data' => $query
                ]);

            if (!$res->ok()) {
                return null;
            }
            $json = $res->json();
            $best = null;
            foreach (($json['elements'] ?? []) as $el) {
                $coords = [];
                if (!empty($el['geometry'])) {
                    foreach ($el['geometry'] as $g) {
                        $coords[] = [$g['lat'], $g['lon']];
                    }
                }
                if (count($coords) < 3) continue;

                $area = $this->polygonAreaMeters($coords);
                $centroid = $this->centroidMeters($coords);
                $pMeters  = $this->lonLatToMeters($lon, $lat);
                $dist = hypot($centroid[0]-$pMeters[0], $centroid[1]-$pMeters[1]);

                if (!$best || $dist < $best['distance_m']) {
                    $best = [
                        'coords'      => $coords,
                        'area_m2'     => $area,
                        'distance_m'  => (int) round($dist),
                    ];
                }
            }
            return $best;
        });
    }

    // --- geometry helpers (Web Mercator projection + shoelace) ---

    protected function lonLatToMeters(float $lon, float $lat): array
    {
        $originShift = 2 * pi() * 6378137 / 2.0;
        $mx = $lon * $originShift / 180.0;
        $my = log(tan((90 + $lat) * pi()/360.0)) * 6378137;
        return [$mx, $my];
    }

    protected function polygonAreaMeters(array $coords): float
    {
        // coords = [[lat,lon], ...]
        $pts = array_map(fn($c) => $this->lonLatToMeters($c[1], $c[0]), $coords);
        $n = count($pts);
        $sum = 0.0;
        for ($i=0; $i<$n; $i++) {
            $j = ($i+1)%$n;
            $sum += $pts[$i][0]*$pts[$j][1] - $pts[$j][0]*$pts[$i][1];
        }
        return abs($sum) / 2.0;
    }

    protected function centroidMeters(array $coords): array
    {
        $pts = array_map(fn($c) => $this->lonLatToMeters($c[1], $c[0]), $coords);
        $n = count($pts);
        $cx = 0.0; $cy = 0.0; $a = 0.0;
        for ($i=0; $i<$n; $i++) {
            $j = ($i+1)%$n;
            $cross = $pts[$i][0]*$pts[$j][1] - $pts[$j][0]*$pts[$i][1];
            $a += $cross;
            $cx += ($pts[$i][0]+$pts[$j][0]) * $cross;
            $cy += ($pts[$i][1]+$pts[$j][1]) * $cross;
        }
        $a *= 0.5;
        if (abs($a) < 1e-9) return $pts[0];
        $cx /= (6*$a); $cy /= (6*$a);
        return [$cx, $cy];
    }
}
