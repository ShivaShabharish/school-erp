<x-layouts.app>
    <x-slot:title>Onboard Faculty & Staff</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Back -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.staff.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Staff Directory
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Onboard Staff</span>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                Tamil Nadu Staff Code Matrix
            </span>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">
                        Personnel Onboarding Form
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Register a new faculty or administrative staff member with biometric linking and portal credentials.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600 text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                    <span>Suggested Code:</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">{{ $suggestedCode }}</span>
                </div>
            </div>

            <form action="{{ route('admin.staff.store') }}" method="POST" class="p-6 md:p-8 space-y-8">
                @csrf

                <!-- Section 1: Institutional & Role Details -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Institutional Role & Designation
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Employee Code <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="employee_code"
                                value="{{ old('employee_code', $suggestedCode) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('employee_code') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Designation / Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="designation"
                                value="{{ old('designation') }}"
                                required
                                placeholder="e.g. PGT Physics / Senior Accountant"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('designation') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Department <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="department"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="Teaching">Teaching Faculty</option>
                                <option value="Administration">Administration & Office</option>
                                <option value="Accounts & Finance">Accounts & Finance</option>
                                <option value="Science">Science & Laboratories</option>
                                <option value="Fine Arts">Arts & Music</option>
                                <option value="Transport">Transport Division</option>
                                <option value="School Store">School Store</option>
                                <option value="Kindergarten">Kindergarten</option>
                            </select>
                            @error('department') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Qualification
                            </label>
                            <input
                                type="text"
                                name="qualification"
                                value="{{ old('qualification', 'M.Sc., B.Ed.') }}"
                                placeholder="e.g. M.Sc., B.Ed."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Joining Date <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="joining_date"
                                value="{{ old('joining_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('joining_date') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Basic Salary (Monthly ₹) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                step="500"
                                name="basic_salary"
                                value="{{ old('basic_salary', 35000) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-bold font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('basic_salary') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Personal Biodata -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Personal Biodata & Contact
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="e.g. Sundaram K."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('name') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Official Email (Login ID) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="e.g. sundaram@school.test"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('email') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Mobile Phone <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                                placeholder="e.g. 9842100010"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            @error('phone') <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p> @enderror
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
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Date of Birth
                            </label>
                            <input
                                type="date"
                                name="dob"
                                value="{{ old('dob', '1988-04-12') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Residential Address
                            </label>
                            <textarea
                                name="address"
                                rows="2"
                                placeholder="Street, locality, town..."
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Bank Account for Payroll Disbursals -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                        <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                            Bank Account for Salary Credit
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Bank Name
                            </label>
                            <input
                                type="text"
                                name="bank_name"
                                value="{{ old('bank_name', 'State Bank of India') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Account Number
                            </label>
                            <input
                                type="text"
                                name="bank_account_no"
                                value="{{ old('bank_account_no', '38491028471') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                IFSC Code
                            </label>
                            <input
                                type="text"
                                name="bank_ifsc"
                                value="{{ old('bank_ifsc', 'SBIN0000898') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono text-xs font-bold uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('admin.staff.index') }}"
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
                        <span>Confirm & Onboard Personnel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
