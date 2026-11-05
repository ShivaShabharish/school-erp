<!-- AI Campus Co-Pilot (Voice & Chat Assistant) -->
<div x-data="aiAssistant()" class="fixed bottom-6 right-6 z-50 font-sans" @keydown.escape.window="open = false">
    <!-- Floating Trigger Button -->
    <button
        @click="toggle()"
        type="button"
        class="group relative flex items-center gap-2.5 px-4 py-3 rounded-full bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white font-bold text-sm shadow-xl shadow-emerald-900/30 hover:shadow-emerald-600/40 hover:scale-105 active:scale-95 transition-all cursor-pointer border border-emerald-400/30"
        aria-label="Open AI Campus Assistant"
    >
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400"></span>
        </span>
        <svg class="w-5 h-5 text-amber-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span class="tracking-wide">AI Co-Pilot</span>
        <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded-full font-mono uppercase tracking-wider text-emerald-100">Voice & Chat</span>
    </button>

    <!-- Glassmorphism Assistant Modal -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="absolute bottom-16 right-0 w-[92vw] sm:w-[420px] max-h-[82vh] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-emerald-800 to-teal-900 text-white flex items-center justify-between border-b border-emerald-700/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 p-0.5 shadow-md flex items-center justify-center">
                    <div class="w-full h-full bg-emerald-950 rounded-[14px] flex items-center justify-center">
                        <span class="font-extrabold text-amber-300 text-sm">TS</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-extrabold text-sm tracking-wide">Malar AI Co-Pilot</h4>
                        <span class="text-[9px] bg-emerald-500/30 text-emerald-200 px-1.5 py-0.5 rounded font-mono font-bold">v2.0</span>
                    </div>
                    <p class="text-[11px] text-emerald-200/80">Voice & Smart Navigation Assistant</p>
                </div>
            </div>
            <button
                @click="open = false"
                class="p-1.5 rounded-xl hover:bg-white/10 text-emerald-200 hover:text-white transition cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Chat Conversation Messages -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs max-h-[380px] min-h-[220px]">
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start gap-2.5'">
                    <template x-if="msg.sender === 'ai'">
                        <div class="w-7 h-7 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                            AI
                        </div>
                    </template>
                    <div
                        :class="msg.sender === 'user'
                            ? 'bg-emerald-600 text-white rounded-2xl rounded-tr-xs px-4 py-2.5 max-w-[85%] shadow-sm'
                            : 'bg-slate-100 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-xs px-4 py-2.5 max-w-[85%] shadow-sm border border-slate-200/50 dark:border-slate-700/50'"
                    >
                        <p class="leading-relaxed" x-text="msg.text"></p>
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
                <span>Malar AI is thinking...</span>
            </div>
        </div>

        <!-- Quick Prompt Chips -->
        <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-800 flex gap-1.5 overflow-x-auto text-[11px] no-scrollbar">
            <button @click="send('Show my attendance percentage')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 font-semibold transition cursor-pointer">
                📊 Attendance
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
        </div>

        <!-- Voice & Text Input Box -->
        <div class="p-3 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
            <!-- Voice Recognition Button -->
            <button
                type="button"
                @click="toggleVoice()"
                :class="listening ? 'bg-rose-600 text-white animate-pulse ring-4 ring-rose-200 dark:ring-rose-900' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-100 hover:text-emerald-700'"
                class="p-2.5 rounded-full transition cursor-pointer shrink-0"
                :title="listening ? 'Listening... speak now' : 'Click to Speak (Voice Recognition)'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                </svg>
            </button>

            <!-- Text Input -->
            <input
                type="text"
                x-model="inputQuery"
                @keydown.enter="send(inputQuery)"
                :placeholder="listening ? 'Listening to voice...' : 'Type question or tap mic to speak...'"
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

<script>
function aiAssistant() {
    return {
        open: false,
        listening: false,
        loading: false,
        inputQuery: '',
        recognition: null,
        messages: [
            {
                sender: 'ai',
                text: 'Vanakkam! I am your AI Campus Co-Pilot for Test Subject HSS. You can speak to me in Tamil or English, or ask questions about attendance, timetables, fees, or navigation. How can I help you today?'
            }
        ],
        init() {
            // Setup Web Speech Recognition
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRec) {
                this.recognition = new SpeechRec();
                this.recognition.continuous = false;
                this.recognition.interimResults = false;
                this.recognition.lang = 'en-IN'; // Indian English / Tamil context

                this.recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    this.inputQuery = transcript;
                    this.listening = false;
                    this.send(transcript);
                };

                this.recognition.onerror = () => {
                    this.listening = false;
                };

                this.recognition.onend = () => {
                    this.listening = false;
                };
            }
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        toggleVoice() {
            if (!this.recognition) {
                alert('Voice speech recognition is not supported on this browser. Please use Chrome or Edge.');
                return;
            }
            if (this.listening) {
                this.recognition.stop();
                this.listening = false;
            } else {
                this.listening = true;
                this.recognition.start();
            }
        },
        async send(query) {
            const prompt = (query || '').trim();
            if (!prompt) return;

            this.messages.push({ sender: 'user', text: prompt });
            this.inputQuery = '';
            this.loading = true;
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

                    // Optional Voice speech synthesis feedback
                    if ('speechSynthesis' in window && !this.listening) {
                        const utterance = new SpeechSynthesisUtterance(data.response.split('.')[0]);
                        utterance.lang = 'en-IN';
                        utterance.rate = 1.05;
                        window.speechSynthesis.speak(utterance);
                    }

                    // Auto navigate if prompt specifically said "take me to" or "navigate"
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
                    text: 'Connection timeout. Please verify internet access.'
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
