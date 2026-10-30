<x-layouts.app>
    <x-slot:title>New Student Admission</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Breadcrumb & Back -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.students.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Student Directory
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">New Admission</span>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                Academic Year: 2026-2027
            </span>
        </div>

        <!-- Admission Form Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">
                        Student Admission Form
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Register a new student into the Tamil Nadu State Board curriculum roster.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600 text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                    <span>Suggested No:</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">{{ $suggestedAdmNo }}</span>
                </div>
            </div>

            <form
                action="{{ route('admin.students.store') }}"
                method="POST"
                class="p-6 md:p-8 space-y-8"
                x-data="{
                    selectedClass: '{{ old('school_class_id', $classes->first()->id ?? '') }}',
                    classes: {{ Js::from($classes) }},
                    get sections() {
                        const c = this.classes.find(item => item.id == this.selectedClass);
                        return c ? c.sections : [];
                    }
                }"
            >
                @csrf

                <!-- Section 1: Academic & Admission Information -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Academic & Admission Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Admission Number <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="admission_no"
                                value="{{ old('admission_no', $suggestedAdmNo) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('admission_no') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Admission Date <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="admission_date"
                                value="{{ old('admission_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('admission_date') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Class / Standard <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="school_class_id"
                                x-model="selectedClass"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                            @error('school_class_id') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Section Assigned <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="section_id"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <template x-for="s in sections" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                            @error('section_id') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Roll Number
                            </label>
                            <input
                                type="text"
                                name="roll_number"
                                value="{{ old('roll_number') }}"
                                placeholder="e.g. 24"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('roll_number') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Previous School (if any)
                            </label>
                            <input
                                type="text"
                                name="previous_school"
                                value="{{ old('previous_school') }}"
                                placeholder="e.g. St. Joseph Matriculation School"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Personal Student Biodata -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Student Biodata
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                required
                                placeholder="e.g. Kaviya"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('first_name') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Last Name / Initial
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="e.g. Sundaram"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Gender <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="gender"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Date of Birth <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="dob"
                                value="{{ old('dob', '2014-05-15') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('dob') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Blood Group
                            </label>
                            <select
                                name="blood_group"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">Select Blood Group</option>
                                <option value="O+">O Positive (O+)</option>
                                <option value="O-">O Negative (O-)</option>
                                <option value="A+">A Positive (A+)</option>
                                <option value="A-">A Negative (A-)</option>
                                <option value="B+">B Positive (B+)</option>
                                <option value="B-">B Negative (B-)</option>
                                <option value="AB+">AB Positive (AB+)</option>
                                <option value="AB-">AB Negative (AB-)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Parent & Contact Details -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Parent & Residential Details
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Father / Guardian Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="father_name"
                                value="{{ old('father_name') }}"
                                required
                                placeholder="e.g. Sundaram K."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('father_name') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Mother Name
                            </label>
                            <input
                                type="text"
                                name="mother_name"
                                value="{{ old('mother_name') }}"
                                placeholder="e.g. Meenakshi S."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Primary Contact Phone <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="tel"
                                name="parent_phone"
                                value="{{ old('parent_phone') }}"
                                required
                                placeholder="e.g. 9876543210"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('parent_phone') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Parent Email (Generates Portal Login)
                            </label>
                            <input
                                type="email"
                                name="parent_email"
                                value="{{ old('parent_email') }}"
                                placeholder="e.g. parent.sundaram@test.com"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            <p class="text-[10px] text-slate-400 mt-1">Default password will be <code>Password@123</code></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                City / Town
                            </label>
                            <input
                                type="text"
                                name="city"
                                value="{{ old('city', 'Pudukkottai') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                PIN Code
                            </label>
                            <input
                                type="text"
                                name="pincode"
                                value="{{ old('pincode', '622001') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Residential Address <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                name="address"
                                rows="2"
                                required
                                placeholder="Street name, door no, landmark..."
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >{{ old('address') }}</textarea>
                            @error('address') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Document Verification -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">4</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Document Verification Checklist
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-emerald-500 transition-colors">
                            <input type="checkbox" name="doc_birth" value="1" checked class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Birth Certificate</span>
                                <span class="text-[10px] text-slate-400">Original / Copy verified</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-emerald-500 transition-colors">
                            <input type="checkbox" name="doc_aadhaar" value="1" checked class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Aadhaar Card Copy</span>
                                <span class="text-[10px] text-slate-400">UIDAI verified</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-emerald-500 transition-colors">
                            <input type="checkbox" name="doc_tc" value="1" class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600">
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Transfer Certificate (TC)</span>
                                <span class="text-[10px] text-slate-400">From previous institution</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('admin.students.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Confirm & Register Admission</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
