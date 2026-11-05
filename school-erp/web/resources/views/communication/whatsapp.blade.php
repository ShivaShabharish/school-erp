<x-layouts.app>
    <x-slot:title>Automated WhatsApp Parent Communication & Fee Recovery</x-slot:title>
    <x-slot:headerTitle>Communication & Alerts</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ activeTab: 'attendance', previewMsg: null }">
        <!-- Banner Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-green-950 p-6 rounded-2xl text-white shadow-lg border border-emerald-700/50">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-green-500 text-white font-black text-xs shadow-md">
                        WA Engine
                    </span>
                    <h2 class="text-xl font-extrabold font-heading tracking-wide">Automated WhatsApp Parent Dispatcher</h2>
                </div>
                <p class="text-xs text-emerald-200/90 mt-1">
                    1-Click zero-cost automated parent messaging: Instant attendance absence alerts & UPI payment recovery reminders.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-white/10 text-white text-xs font-mono font-bold border border-white/20">
                    🟢 WhatsApp Gateway: Active
                </span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-700 pb-2">
            <button
                type="button"
                @click="activeTab = 'attendance'"
                :class="activeTab === 'attendance' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Daily Absence Alerts (8:45 AM)</span>
                <span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px]">{{ count($demoAbsentees) }}</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'fees'"
                :class="activeTab === 'fees' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Fee Overdue Recovery with UPI</span>
                <span class="px-1.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px]">{{ count($feeDues) }}</span>
            </button>
        </div>

        <!-- TAB 1: Attendance Absence Alerts -->
        <div x-show="activeTab === 'attendance'" class="space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-rose-50/40 dark:bg-rose-950/20">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                            <span>Today's Unexcused Absentees &bull; Date: {{ $today }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Notify parents immediately via WhatsApp to verify student safety and reason for absence.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3">Student Name</th>
                                <th class="px-5 py-3">Class & Section</th>
                                <th class="px-5 py-3">Parent / Guardian</th>
                                <th class="px-5 py-3">WhatsApp Number</th>
                                <th class="px-5 py-3">Notification Status</th>
                                <th class="px-5 py-3 text-right">Instant Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            @foreach($demoAbsentees as $item)
                                @php
                                    $msg = "Vanakkam " . $item->parent_name . ", Innaiku (" . $today . ") ungal magan/magal " . $item->student->full_name . " (Class " . ($item->student->schoolClass?->name ?? '10') . " - " . ($item->student->section?->name ?? 'A') . ") school attendance-la ABSENT aagi irukaar. Varukai kurithu vivaram theriya school office-ku contact seiyavum. - Principal, Test Subject HSS, Pudukkottai.";
                                    $waUrl = "https://wa.me/91" . preg_replace('/[^0-9]/', '', $item->parent_phone) . "?text=" . urlencode($msg);
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40">
                                    <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                        {{ $item->student->full_name }}
                                        <span class="block text-[10px] text-slate-400 font-normal">Adm: {{ $item->student->admission_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold">
                                        Class {{ $item->student->schoolClass?->name ?? '10' }} - {{ $item->student->section?->name ?? 'A' }}
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold">
                                        {{ $item->parent_name }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                        +91 {{ $item->parent_phone }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold text-[10px]">
                                            Absent at 8:30 AM
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a
                                            href="{{ $waUrl }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs shadow-xs transition cursor-pointer"
                                        >
                                            <span>💬 Send WhatsApp</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Fee Overdue Recovery with UPI -->
        <div x-show="activeTab === 'fees'" class="space-y-4" style="display: none;">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-amber-50/40 dark:bg-amber-950/20">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Fee Collection Recovery Blast &bull; Direct UPI Settlement</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Sends formatted invoice summary with 1-click GPay / PhonePe UPI payment link directly to parents.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3">Student Name</th>
                                <th class="px-5 py-3">Class</th>
                                <th class="px-5 py-3">Fee Term</th>
                                <th class="px-5 py-3">Outstanding Due</th>
                                <th class="px-5 py-3">Due Date</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            @foreach($feeDues as $due)
                                @php
                                    $feeMsg = "Vanakkam " . $due->parent_name . ", Test Subject HSS-il padikkum ungal pillai " . $due->student->full_name . " (Class " . ($due->student->schoolClass?->name ?? '10') . ")-oda " . $due->term . " niluvai thogai Rs." . number_format($due->due_amount) . " aagum. Due Date: " . $due->due_date . ". Neenga GPay / PhonePe / Paytm moolamaaga udane selutha intha direct UPI link-ai use seiyavum: upi://pay?pa=testsubjecthss@sbi&pn=Test+Subject+HSS&am=" . $due->due_amount . "&cu=INR. Nandri. - Accounts Dept, Test Subject HSS, Pudukkottai.";
                                    $feeWaUrl = "https://wa.me/91" . preg_replace('/[^0-9]/', '', $due->parent_phone) . "?text=" . urlencode($feeMsg);
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40">
                                    <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                        {{ $due->student->full_name }}
                                        <span class="block text-[10px] text-slate-400 font-normal">Adm: {{ $due->student->admission_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold">
                                        Class {{ $due->student->schoolClass?->name ?? '10' }} - {{ $due->student->section?->name ?? 'A' }}
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-slate-600 dark:text-slate-300">
                                        {{ $due->term }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono font-extrabold text-amber-600 dark:text-amber-400 text-sm">
                                        ₹{{ number_format($due->due_amount) }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-slate-500">
                                        {{ $due->due_date }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a
                                            href="{{ $feeWaUrl }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs shadow-xs transition cursor-pointer"
                                        >
                                            <span>💳 Send UPI WhatsApp</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
