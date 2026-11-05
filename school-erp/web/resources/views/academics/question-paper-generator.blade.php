<x-layouts.app>
    <x-slot:title>AI Exam Question Paper & Key Generator</x-slot:title>
    <x-slot:headerTitle>Academics & Examination</x-slot:headerTitle>

    <div class="space-y-6" x-data="questionPaperApp()">
        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-blue-950 via-indigo-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg border border-blue-700/40">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-amber-400 text-slate-950 font-black text-xs">
                        AI BLUEPRINT
                    </span>
                    <h2 class="text-xl font-extrabold font-heading tracking-wide">AI Exam Question Paper & Key Generator</h2>
                </div>
                <p class="text-xs text-blue-200/90 mt-1">
                    Instant Samacheer Kalvi blueprint-aligned exam papers with verified teacher scoring keys in seconds.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-white/10 text-white text-xs font-mono font-bold border border-white/20">
                    Syllabus: TN State Board
                </span>
            </div>
        </div>

        <!-- Generator Configuration Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Configuration Form Card -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-4">
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>Exam Paper Configuration</span>
                </h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Standard / Class *</label>
                    <select x-model="form.class_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Class {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Subject *</label>
                    <select x-model="form.subject_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Examination Title *</label>
                    <input type="text" x-model="form.exam_name" placeholder="e.g. Mid-Term Assessment 2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Total Marks *</label>
                        <select x-model="form.total_marks" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                            <option value="25">25 Marks (Unit Test)</option>
                            <option value="50" selected>50 Marks (Mid-Term)</option>
                            <option value="100">100 Marks (Full Board)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Duration *</label>
                        <select x-model="form.duration" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                            <option value="1 Hour">1 Hour</option>
                            <option value="1.5 Hours" selected>1.5 Hours</option>
                            <option value="2.5 Hours">2.5 Hours</option>
                            <option value="3 Hours">3 Hours</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Chapters / Syllabus Topic</label>
                    <input type="text" x-model="form.chapter" placeholder="e.g. Algebra, Trigonometry, Coordinate Geometry" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs text-slate-800 dark:text-white" />
                </div>

                <button
                    type="button"
                    @click="generatePaper()"
                    :disabled="loading"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50"
                >
                    <svg class="w-4 h-4 animate-spin" x-show="loading" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span x-text="loading ? 'AI Generating Blueprint...' : '✨ Generate Question Paper & Key'"></span>
                </button>
            </div>

            <!-- Right: Paper & Answer Key Preview Area -->
            <div class="lg:col-span-2 space-y-4">
                <template x-if="!paper">
                    <div class="bg-white dark:bg-slate-800 p-12 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 mx-auto flex items-center justify-center text-2xl font-bold">
                            📝
                        </div>
                        <h4 class="font-extrabold text-slate-800 dark:text-slate-200 text-sm">No Paper Generated Yet</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">
                            Configure standard, subject, and marks on the left, then click "Generate Question Paper & Key" to build a blueprint exam sheet.
                        </p>
                    </div>
                </template>

                <template x-if="paper">
                    <div class="space-y-4">
                        <!-- Top Action Buttons -->
                        <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-3 rounded-2xl border border-slate-200 dark:border-slate-700 no-print">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="activeTab = 'questions'" :class="activeTab === 'questions' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl font-bold text-xs transition cursor-pointer">
                                    📄 Question Paper View
                                </button>
                                <button type="button" @click="activeTab = 'answers'" :class="activeTab === 'answers' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl font-bold text-xs transition cursor-pointer">
                                    🔑 Teacher Scoring Key
                                </button>
                            </div>

                            <button type="button" @click="window.print()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Print Exam Paper</span>
                            </button>
                        </div>

                        <!-- Formal Print Sheet -->
                        <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl space-y-6 text-slate-900 dark:text-slate-100">
                            <!-- School Exam Header -->
                            <div class="text-center pb-4 border-b-2 border-slate-900 dark:border-slate-100 space-y-1">
                                <h3 class="font-black text-base uppercase tracking-wider" x-text="paper.school_name"></h3>
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400" x-html="paper.board"></p>
                                <div class="flex items-center justify-between text-xs font-bold pt-3 font-mono">
                                    <span x-text="paper.exam_name + ' &bull; ' + paper.class"></span>
                                    <span x-text="'Subject: ' + paper.subject"></span>
                                    <span x-text="'Max Marks: ' + paper.total_marks + ' | Time: ' + paper.duration"></span>
                                </div>
                            </div>

                            <!-- Questions View -->
                            <div x-show="activeTab === 'questions'" class="space-y-6 text-xs">
                                <!-- Part A -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between font-bold uppercase text-[11px] border-b pb-1">
                                        <span>Part - A: Choose the Correct Answer (1 Mark Each)</span>
                                        <span>[4 &times; 1 = 4 Marks]</span>
                                    </div>
                                    <template x-for="(item, idx) in paper.part_a" :key="idx">
                                        <div class="space-y-1.5 pl-2">
                                            <p class="font-bold"><span x-text="(idx + 1) + '. '"></span><span x-text="item.q"></span></p>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-slate-600 dark:text-slate-400 pl-4 font-mono text-[11px]">
                                                <template x-for="opt in item.options" :key="opt">
                                                    <span x-text="opt"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Part B -->
                                <div class="space-y-3 pt-3">
                                    <div class="flex items-center justify-between font-bold uppercase text-[11px] border-b pb-1">
                                        <span>Part - B: Short Answer Questions (2 Marks Each)</span>
                                        <span>[3 &times; 2 = 6 Marks]</span>
                                    </div>
                                    <template x-for="(item, idx) in paper.part_b" :key="idx">
                                        <div class="pl-2">
                                            <p class="font-bold"><span x-text="(idx + 5) + '. '"></span><span x-text="item.q"></span></p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Part C -->
                                <div class="space-y-3 pt-3">
                                    <div class="flex items-center justify-between font-bold uppercase text-[11px] border-b pb-1">
                                        <span>Part - C: Detailed Problems & Proofs (5 Marks Each)</span>
                                        <span>[2 &times; 5 = 10 Marks]</span>
                                    </div>
                                    <template x-for="(item, idx) in paper.part_c" :key="idx">
                                        <div class="pl-2">
                                            <p class="font-bold"><span x-text="(idx + 8) + '. '"></span><span x-text="item.q"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Answers View -->
                            <div x-show="activeTab === 'answers'" class="space-y-4 text-xs" style="display: none;">
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200 dark:border-indigo-800 font-bold text-indigo-900 dark:text-indigo-300">
                                    🔑 Official Teacher Evaluation Guide & Marking Scheme
                                </div>

                                <div class="space-y-3">
                                    <h5 class="font-bold uppercase text-[11px] border-b pb-1">Part A Answers:</h5>
                                    <template x-for="(item, idx) in paper.part_a" :key="idx">
                                        <p class="pl-2"><strong>Q<span x-text="idx + 1"></span>:</strong> <span class="font-mono text-emerald-600 font-bold" x-text="item.ans"></span></p>
                                    </template>
                                </div>

                                <div class="space-y-3 pt-2">
                                    <h5 class="font-bold uppercase text-[11px] border-b pb-1">Part B Model Solutions:</h5>
                                    <template x-for="(item, idx) in paper.part_b" :key="idx">
                                        <div class="pl-2 space-y-0.5">
                                            <p class="font-bold">Q<span x-text="idx + 5"></span>: <span x-text="item.q"></span></p>
                                            <p class="text-slate-600 dark:text-slate-400 pl-3 italic" x-text="item.ans"></p>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-3 pt-2">
                                    <h5 class="font-bold uppercase text-[11px] border-b pb-1">Part C Step Solutions & Rubrics:</h5>
                                    <template x-for="(item, idx) in paper.part_c" :key="idx">
                                        <div class="pl-2 space-y-0.5">
                                            <p class="font-bold">Q<span x-text="idx + 8"></span>: <span x-text="item.q"></span></p>
                                            <p class="text-slate-600 dark:text-slate-400 pl-3 italic" x-text="item.ans"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
    function questionPaperApp() {
        return {
            loading: false,
            activeTab: 'questions',
            paper: null,
            form: {
                class_id: '{{ $classes->first()?->id ?? 1 }}',
                subject_id: '{{ $subjects->first()?->id ?? 1 }}',
                exam_name: 'Mid-Term Assessment 2026',
                total_marks: 50,
                duration: '1.5 Hours',
                chapter: 'Algebra & Geometry Module'
            },
            async generatePaper() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route('admin.academics.question-paper-generator.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.paper = data.paper;
                    }
                } catch(e) {
                    alert('Error generating exam paper.');
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    </script>
</x-layouts.app>
