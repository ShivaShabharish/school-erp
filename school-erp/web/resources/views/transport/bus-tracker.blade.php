<x-layouts.app>
    <x-slot:title>Live School Bus GPS Fleet Tracker</x-slot:title>
    <x-slot:headerTitle>Campus Transport</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ selectedBusId: 'BUS-01' }">
        <!-- Top Telematics Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg border border-emerald-700/40">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-amber-400 text-slate-950 font-black text-xs">
                        GPS LIVE
                    </span>
                    <h2 class="text-xl font-extrabold font-heading tracking-wide">Live School Bus Telematics & ETA</h2>
                </div>
                <p class="text-xs text-emerald-200/90 mt-1">
                    Real-time geofence monitoring, student boarding count & driver telematics for Test Subject HSS fleet.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 text-xs font-mono font-bold border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>2 Buses Active on Road</span>
                </span>
            </div>
        </div>

        <!-- Bus Fleet Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($buses as $bus)
                <div
                    @click="selectedBusId = '{{ $bus['id'] }}'"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-800 border-2 transition-all cursor-pointer shadow-xs hover:shadow-md"
                    :class="selectedBusId === '{{ $bus['id'] }}' ? 'border-emerald-500 ring-4 ring-emerald-500/10' : 'border-slate-200 dark:border-slate-700'"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-lg">
                                🚌
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $bus['reg_no'] }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $bus['route_name'] }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ $bus['speed_kmh'] }} km/h
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 my-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 text-xs text-center border border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="text-[10px] uppercase text-slate-400 font-bold block">Next Stop</span>
                            <strong class="text-slate-900 dark:text-white">{{ $bus['next_stop'] }}</strong>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-slate-400 font-bold block">Stop ETA</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $bus['eta_minutes'] }} Mins ({{ $bus['distance_km'] }} km)</strong>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-slate-400 font-bold block">Onboard</span>
                            <strong class="text-blue-600 dark:text-blue-400">{{ $bus['boarded_students'] }} / {{ $bus['total_students'] }}</strong>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-700/60">
                        <span class="text-slate-500">Driver: <strong>{{ $bus['driver_name'] }}</strong></span>
                        <a
                            href="tel:{{ $bus['driver_phone'] }}"
                            class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-emerald-600 hover:text-white font-bold text-[11px] transition"
                        >
                            📞 Call Driver
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Interactive Live GPS Route & Stops Map Card -->
        @foreach($buses as $bus)
            <div x-show="selectedBusId === '{{ $bus['id'] }}'" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>Live Telematics & Geofence: {{ $bus['reg_no'] }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Current Location: <strong>{{ $bus['current_location'] }}</strong> &bull; Speed: <strong>{{ $bus['speed_kmh'] }} km/h</strong>
                        </p>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold flex items-center gap-2">
                        <span>🚨 Next Arrival: <strong>{{ $bus['next_stop'] }} in {{ $bus['eta_minutes'] }} Mins</strong></span>
                    </div>
                </div>

                <!-- Visual Route Sequence Pipeline -->
                <div class="p-6 md:p-8">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Route Stop Sequence & Passenger Boarding</h4>
                    <div class="relative pl-6 space-y-8 before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700">
                        @foreach($bus['stops'] as $sIdx => $st)
                            <div class="relative flex items-center justify-between gap-4">
                                <!-- Marker Dot -->
                                @if($st['status'] === 'completed')
                                    <div class="absolute -left-6 w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shadow-xs">
                                        ✓
                                    </div>
                                @elseif($st['status'] === 'current')
                                    <div class="absolute -left-6 w-5 h-5 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-[10px] font-bold shadow-md animate-pulse ring-4 ring-amber-400/30">
                                        🚌
                                    </div>
                                @else
                                    <div class="absolute -left-6 w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                                @endif

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-xs font-extrabold {{ $st['status'] === 'current' ? 'text-amber-600 dark:text-amber-400 text-sm' : 'text-slate-800 dark:text-slate-200' }}">
                                            {{ $st['name'] }}
                                        </h5>
                                        @if($st['status'] === 'current')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-extrabold text-[9px] uppercase tracking-wider">
                                                Next Stop (4 Mins)
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        Scheduled: {{ $st['time'] }} &bull; Boarded: {{ $st['boarded'] }} Students
                                    </p>
                                </div>

                                <div>
                                    @if($st['status'] === 'completed')
                                        <span class="text-[11px] font-bold text-emerald-600">Departed</span>
                                    @elseif($st['status'] === 'current')
                                        <span class="text-[11px] font-extrabold text-amber-600 animate-pulse">Approaching</span>
                                    @else
                                        <span class="text-[11px] font-medium text-slate-400">Scheduled</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
