<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusTrackerController extends Controller
{
    public function index(Request $request): View
    {
        $buses = [
            [
                'id' => 'BUS-01',
                'reg_no' => 'TN-55-AB-1020',
                'route_name' => 'Route 1: Pudukkottai Town to School Campus',
                'driver_name' => 'S. Murugesan',
                'driver_phone' => '9842154321',
                'current_location' => 'Near Anna Nagar Roundabout',
                'speed_kmh' => 36,
                'status' => 'in_transit',
                'total_students' => 42,
                'boarded_students' => 35,
                'next_stop' => 'Anna Nagar Corner',
                'eta_minutes' => 4,
                'distance_km' => 0.8,
                'stops' => [
                    ['name' => 'Old Bus Stand Point', 'time' => '07:45 AM', 'status' => 'completed', 'boarded' => 12],
                    ['name' => 'Sivapuram Road Junction', 'time' => '07:58 AM', 'status' => 'completed', 'boarded' => 15],
                    ['name' => 'Anna Nagar Corner', 'time' => '08:08 AM', 'status' => 'current', 'boarded' => 8],
                    ['name' => 'District Collectorate Gate', 'time' => '08:18 AM', 'status' => 'upcoming', 'boarded' => 5],
                    ['name' => 'School Campus Arrival', 'time' => '08:26 AM', 'status' => 'upcoming', 'boarded' => 0],
                ]
            ],
            [
                'id' => 'BUS-02',
                'reg_no' => 'TN-55-CD-2040',
                'route_name' => 'Route 2: Alangudi & Aranthangi Bypass Link',
                'driver_name' => 'K. Rajendran',
                'driver_phone' => '9443298765',
                'current_location' => 'Alangudi Bypass Junction',
                'speed_kmh' => 41,
                'status' => 'in_transit',
                'total_students' => 38,
                'boarded_students' => 28,
                'next_stop' => 'Alangudi Market Stop',
                'eta_minutes' => 7,
                'distance_km' => 1.4,
                'stops' => [
                    ['name' => 'Aranthangi Toll Point', 'time' => '07:40 AM', 'status' => 'completed', 'boarded' => 14],
                    ['name' => 'Alangudi Market Stop', 'time' => '08:02 AM', 'status' => 'current', 'boarded' => 10],
                    ['name' => 'Vamban Agricultural Ring', 'time' => '08:14 AM', 'status' => 'upcoming', 'boarded' => 8],
                    ['name' => 'School Campus Arrival', 'time' => '08:28 AM', 'status' => 'upcoming', 'boarded' => 0],
                ]
            ]
        ];

        return view('transport.bus-tracker', compact('buses'));
    }

    public function liveFleetData(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'active_buses_count' => 8,
            'buses' => [
                [
                    'id' => 'BUS-01',
                    'reg_no' => 'TN-55-AB-1020',
                    'route_name' => 'Route 1: Pudukkottai Town to School Campus',
                    'driver_name' => 'S. Murugesan',
                    'driver_phone' => '9842154321',
                    'current_location' => 'Near Anna Nagar Roundabout',
                    'speed_kmh' => 38,
                    'status' => 'in_transit',
                    'total_students' => 42,
                    'boarded_students' => 35,
                    'next_stop' => 'Anna Nagar Corner',
                    'eta_minutes' => 4,
                    'distance_km' => 0.8,
                ],
                [
                    'id' => 'BUS-02',
                    'reg_no' => 'TN-55-CD-2040',
                    'route_name' => 'Route 2: Alangudi & Aranthangi Bypass Link',
                    'driver_name' => 'K. Rajendran',
                    'driver_phone' => '9443298765',
                    'current_location' => 'Alangudi Bypass Junction',
                    'speed_kmh' => 42,
                    'status' => 'in_transit',
                    'total_students' => 38,
                    'boarded_students' => 28,
                    'next_stop' => 'Alangudi Market Stop',
                    'eta_minutes' => 6,
                    'distance_km' => 1.2,
                ]
            ]
        ]);
    }
}
