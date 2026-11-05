<!-- AI Campus Co-Pilot with Stylish 3D Student Mascot & Voice Recognition -->
<div x-data="aiCampusMascot()" class="fixed bottom-6 right-6 z-50 font-sans" @keydown.escape.window="open = false">
    <!-- Sleek, Modern Floating Student Mascot Trigger (No cartoon orbits, clean & stylish) -->
    <div x-show="!open" class="relative flex items-center select-none" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        <button
            @click="toggle()"
            type="button"
            class="group relative flex items-center gap-3.5 pl-2 pr-5 py-2 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-emerald-500/30 dark:border-emerald-500/40 shadow-xl shadow-emerald-950/20 hover:shadow-2xl hover:shadow-emerald-600/30 hover:scale-[1.03] active:scale-95 transition-all duration-300 cursor-pointer"
            aria-label="Open Malar AI Student Co-Pilot"
        >
            <!-- High-End 3D Student Mascot Portrait in Gold-Accented Circular Ring -->
            <div class="relative w-14 h-14 shrink-0">
                <!-- Glowing Ambient Rim -->
                <div class="absolute -inset-0.5 bg-gradient-to-tr from-emerald-500 via-amber-400 to-teal-500 rounded-full blur-[3px] opacity-70 group-hover:opacity-100 transition duration-300"></div>
                
                <!-- Avatar Image Container -->
                <div class="relative w-full h-full rounded-full overflow-hidden border-2 border-white dark:border-slate-800 shadow-md">
                    <img
                        src="/images/student_mascot.jpg"
                        alt="Malar AI Student Mascot"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
                    />
                </div>

                <!-- Live Online Pulse Dot -->
                <span class="absolute bottom-0 right-0 flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
                </span>
            </div>

            <!-- Sleek Text & Voice Trigger Info -->
            <div class="text-left flex flex-col justify-center">
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Malar AI</span>
                    <span class="text-[9px] bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow-xs">
                        Voice Co-Pilot
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                    <span>Tap to Speak or Ask</span>
                </p>
            </div>

            <!-- Mic Shortcut Button Icon on the Right -->
            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-300 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 ml-1 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
            </div>
        </button>
    </div>

    <!-- Glassmorphism Assistant Modal -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="w-[94vw] sm:w-[440px] max-h-[85vh] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
        style="display: none;"
    >
        <!-- Modal Header with 3D Mascot Avatar & Language Switcher -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-900 text-white flex items-center justify-between border-b border-emerald-700/50">
            <div class="flex items-center gap-3">
                <!-- 3D Student Mascot Avatar Frame -->
                <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 p-0.5 shadow-md shrink-0">
                    <div class="w-full h-full rounded-[14px] overflow-hidden bg-slate-900">
                        <img
                            src="/images/student_mascot.jpg"
                            alt="Malar AI Student Avatar"
                            class="w-full h-full object-cover object-top"
                        />
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-extrabold text-sm tracking-wide">Malar AI Co-Pilot</h4>
                        <span class="text-[9px] bg-emerald-500/30 text-emerald-200 px-1.5 py-0.5 rounded font-mono font-bold">Student Guide</span>
                    </div>
                    <p class="text-[11px] text-emerald-200/80 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Voice & Navigation Active
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <!-- Tamil / English Language Switcher Toggle -->
                <button
                    type="button"
                    @click="toggleLang()"
                    class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold transition cursor-pointer border flex items-center gap-1"
                    :class="lang === 'ta-IN' ? 'bg-amber-400 text-slate-900 border-amber-300 shadow-xs' : 'bg-white/10 text-emerald-100 border-white/20 hover:bg-white/20'"
                    :title="'Current Speech Recognition: ' + (lang === 'ta-IN' ? 'Tamil (தமிழ்)' : 'English (Indian)')"
                >
                    <span x-text="lang === 'ta-IN' ? '🇮🇳 தமிழ்' : '🇬🇧 English'"></span>
                </button>

                <!-- Close Button -->
                <button
                    @click="open = false"
                    class="p-1.5 rounded-xl hover:bg-white/10 text-emerald-200 hover:text-white transition cursor-pointer"
                    aria-label="Close Assistant"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Live Voice Status Banner (Shown when Microphone is Active) -->
        <div x-show="listening" class="bg-rose-50 dark:bg-rose-950/70 border-b border-rose-200 dark:border-rose-900 px-4 py-2.5 flex items-center justify-between" x-transition>
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-600"></span>
                </span>
                <div>
                    <p class="text-xs font-bold text-rose-700 dark:text-rose-300">Listening to your voice...</p>
                    <p class="text-[10px] text-rose-600/80 dark:text-rose-400" x-text="lang === 'ta-IN' ? 'தமிழில் பேசவும் (Speak in Tamil)...' : 'Speak your question clearly...'"></p>
                </div>
            </div>
            <!-- Animated Sound Wave Bars -->
            <div class="flex items-end gap-1 h-5">
                <span class="w-1 bg-rose-500 rounded-full animate-soundwave-1"></span>
                <span class="w-1 bg-rose-500 rounded-full animate-soundwave-2"></span>
                <span class="w-1 bg-rose-500 rounded-full animate-soundwave-3"></span>
                <span class="w-1 bg-rose-500 rounded-full animate-soundwave-4"></span>
                <span class="w-1 bg-rose-500 rounded-full animate-soundwave-2"></span>
            </div>
        </div>

        <!-- Voice Error Alert Banner -->
        <div x-show="voiceError" class="bg-amber-50 dark:bg-amber-950/70 border-b border-amber-200 dark:border-amber-900 px-4 py-2 flex items-center justify-between text-xs text-amber-800 dark:text-amber-200" x-transition>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span x-text="voiceError"></span>
            </div>
            <button @click="voiceError = null" class="text-amber-600 font-bold ml-2 cursor-pointer">&times;</button>
        </div>

        <!-- Chat Conversation Messages -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs max-h-[380px] min-h-[220px]">
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start gap-2.5'">
                    <template x-if="msg.sender === 'ai'">
                        <div class="w-7 h-7 rounded-xl overflow-hidden shadow-sm shrink-0 mt-0.5 border border-emerald-600">
                            <img src="/images/student_mascot.jpg" alt="Malar" class="w-full h-full object-cover object-top" />
                        </div>
                    </template>
                    <div
                        :class="msg.sender === 'user'
                            ? 'bg-emerald-600 text-white rounded-2xl rounded-tr-xs px-4 py-2.5 max-w-[85%] shadow-sm'
                            : 'bg-slate-100 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-xs px-4 py-2.5 max-w-[85%] shadow-sm border border-slate-200/50 dark:border-slate-700/50'"
                    >
                        <p class="leading-relaxed whitespace-pre-line" x-text="msg.text"></p>
                        <template x-if="msg.web_url">
                            <a
                                :href="msg.web_url"
                                class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-xs"
                            >
                                <span>Navigate to Page</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="loading" class="flex items-center gap-2 text-slate-400 text-xs italic">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce"></div>
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce [animation-delay:0.2s]"></div>
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce [animation-delay:0.4s]"></div>
                <span>Malar AI is processing...</span>
            </div>
        </div>

        <!-- Quick Prompt Chips -->
        <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-800 flex gap-1.5 overflow-x-auto text-[11px] no-scrollbar">
            <button @click="send('Show my attendance percentage')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 font-semibold transition cursor-pointer">
                📊 Attendance %
            </button>
            <button @click="send('Open class timetable')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 font-semibold transition cursor-pointer">
                📅 Timetable
            </button>
            <button @click="send('Any pending fee due?')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 font-semibold transition cursor-pointer">
                💳 Fees Due
            </button>
            <button @click="send('Show today homework')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 font-semibold transition cursor-pointer">
                📝 Homework
            </button>
            <button @click="send('School bus timings and route')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 hover:bg-teal-100 font-semibold transition cursor-pointer">
                🚌 Bus Routes
            </button>
        </div>

        <!-- Voice & Text Input Box -->
        <div class="p-3 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
            <!-- Enhanced Voice Recognition Button -->
            <button
                type="button"
                @click="toggleVoice()"
                :class="listening ? 'bg-rose-600 text-white animate-pulse ring-4 ring-rose-200 dark:ring-rose-900' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-100 hover:text-emerald-700'"
                class="p-2.5 rounded-full transition cursor-pointer shrink-0"
                :title="listening ? 'Listening... Tap to Stop' : 'Tap to Speak (Voice Recognition)'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
            </button>

            <!-- Text Input (With live interim speech transcription) -->
            <input
                type="text"
                x-model="inputQuery"
                @keydown.enter="send(inputQuery)"
                :placeholder="listening ? 'Listening... Speak now...' : (lang === 'ta-IN' ? 'தமிழில் பேசவும் அல்லது தட்டச்சு செய்யவும்...' : 'Type or tap mic to speak...')"
                class="flex-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
            />

            <!-- Send Button -->
            <button
                type="button"
                @click="send(inputQuery)"
                class="p-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition cursor-pointer shadow-xs disabled:opacity-50"
                :disabled="loading || !inputQuery.trim()"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes soundwaveAnim1 { 0%, 100% { height: 6px; } 50% { height: 18px; } }
@keyframes soundwaveAnim2 { 0%, 100% { height: 16px; } 50% { height: 8px; } }
@keyframes soundwaveAnim3 { 0%, 100% { height: 10px; } 50% { height: 20px; } }
@keyframes soundwaveAnim4 { 0%, 100% { height: 14px; } 50% { height: 6px; } }

.animate-soundwave-1 { animation: soundwaveAnim1 0.6s ease-in-out infinite; }
.animate-soundwave-2 { animation: soundwaveAnim2 0.7s ease-in-out infinite; }
.animate-soundwave-3 { animation: soundwaveAnim3 0.5s ease-in-out infinite; }
.animate-soundwave-4 { animation: soundwaveAnim4 0.8s ease-in-out infinite; }
</style>

<script>
function aiCampusMascot() {
    return {
        open: false,
        listening: false,
        loading: false,
        lang: 'ta-IN', // Default Tamil for TN School; toggles to en-IN
        inputQuery: '',
        voiceError: null,
        recognition: null,
        messages: [
            {
                sender: 'ai',
                text: 'Vanakkam! I am Malar, your Student Mascot & AI Campus Co-Pilot for Test Subject Higher Secondary School. \n\nYou can speak with me in Tamil (தமிழ்) or English, or ask about attendance %, timetable, fees, or campus navigation. Tap the mic to speak!'
            }
        ],
        init() {
            this.setupSpeechRecognition();
        },
        toggleLang() {
            this.lang = this.lang === 'ta-IN' ? 'en-IN' : 'ta-IN';
            if (this.recognition) {
                this.recognition.lang = this.lang;
            }
        },
        setupSpeechRecognition() {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRec) {
                console.warn('SpeechRecognition API not natively available in this browser.');
                return;
            }

            try {
                this.recognition = new SpeechRec();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = this.lang;
                this.recognition.maxAlternatives = 1;

                this.recognition.onstart = () => {
                    this.listening = true;
                    this.voiceError = null;
                };

                this.recognition.onresult = (event) => {
                    let interimText = '';
                    let finalText = '';

                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        const transcript = event.results[i][0].transcript;
                        if (event.results[i].isFinal) {
                            finalText += transcript;
                        } else {
                            interimText += transcript;
                        }
                    }

                    if (interimText) {
                        this.inputQuery = interimText;
                    }

                    if (finalText) {
                        this.inputQuery = finalText;
                        this.listening = false;
                        this.send(finalText);
                    }
                };

                this.recognition.onerror = (event) => {
                    console.error('Speech recognition error:', event.error);
                    this.listening = false;
                    if (event.error === 'not-allowed' || event.error === 'permission-denied') {
                        this.voiceError = 'Microphone permission blocked. Please allow microphone in browser settings.';
                    } else if (event.error === 'no-speech') {
                        this.voiceError = 'No speech detected. Please speak closer to microphone.';
                    } else if (event.error === 'network') {
                        this.voiceError = 'Network connection issue for speech service.';
                    } else {
                        this.voiceError = 'Voice error: ' + event.error;
                    }
                };

                this.recognition.onend = () => {
                    this.listening = false;
                };
            } catch (e) {
                console.error('Error initializing SpeechRecognition:', e);
            }
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        async toggleVoice() {
            this.voiceError = null;

            if (this.listening) {
                if (this.recognition) {
                    try { this.recognition.stop(); } catch (e) {}
                }
                this.listening = false;
                return;
            }

            // Check browser audio permission explicitly first
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    stream.getTracks().forEach(track => track.stop());
                } catch (micErr) {
                    console.warn('Microphone permission request error:', micErr);
                    this.voiceError = 'Microphone access is required. Please grant microphone permission.';
                    return;
                }
            }

            if (!this.recognition) {
                this.setupSpeechRecognition();
            }

            if (!this.recognition) {
                alert('Voice speech recognition is not supported on this browser. Please use Google Chrome, Edge, or Android Chrome.');
                return;
            }

            try {
                this.recognition.lang = this.lang;
                this.recognition.start();
                this.listening = true;
            } catch (err) {
                console.warn('Recognition start exception, retrying:', err);
                try {
                    this.recognition.stop();
                    setTimeout(() => {
                        this.recognition.lang = this.lang;
                        this.recognition.start();
                        this.listening = true;
                    }, 200);
                } catch (retryErr) {
                    this.voiceError = 'Could not start voice service. Please refresh page.';
                }
            }
        },
        async send(query) {
            const prompt = (query || '').trim();
            if (!prompt) return;

            this.messages.push({ sender: 'user', text: prompt });
            this.inputQuery = '';
            this.loading = true;
            this.voiceError = null;
            this.scrollToBottom();

            try {
                const res = await fetch('/api/ai/assistant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ prompt: prompt })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    this.messages.push({
                        sender: 'ai',
                        text: data.response,
                        web_url: data.web_url
                    });

                    // Voice Speech Synthesis Feedback
                    if ('speechSynthesis' in window && !this.listening) {
                        try {
                            window.speechSynthesis.cancel();
                            const cleanText = data.response.replace(/[#*_`]/g, '').split('\n')[0];
                            const utterance = new SpeechSynthesisUtterance(cleanText);
                            utterance.lang = this.lang === 'ta-IN' ? 'ta-IN' : 'en-IN';
                            utterance.rate = 1.0;
                            window.speechSynthesis.speak(utterance);
                        } catch (sErr) {}
                    }

                    // Auto navigate if prompt specifically requested navigation
                    if (data.action === 'navigate' && data.web_url && (prompt.toLowerCase().includes('take me') || prompt.toLowerCase().includes('open') || prompt.toLowerCase().includes('go to'))) {
                        setTimeout(() => {
                            window.location.href = data.web_url;
                        }, 1200);
                    }
                } else {
                    this.messages.push({
                        sender: 'ai',
                        text: data.message || 'Unable to process query at this time.'
                    });
                }
            } catch (err) {
                this.messages.push({
                    sender: 'ai',
                    text: 'Connection error. Please check your network connection.'
                });
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    };
}
</script>