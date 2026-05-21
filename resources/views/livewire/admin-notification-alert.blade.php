<div wire:poll.30000ms="checkNew" x-data="adminNotifToast()" x-init="init()">

    {{-- Conteneur des toasts --}}
    <div
        class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
        style="min-width:340px;max-width:400px;"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                class="pointer-events-auto rounded-xl shadow-2xl border border-orange-200 bg-white dark:bg-gray-900 overflow-hidden"
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
            >
                {{-- Barre de progression --}}
                <div class="h-1 bg-orange-500" :style="`width:${toast.progress}%;transition:width 0.1s linear`"></div>

                <div class="flex items-start gap-3 p-4">
                    {{-- Icône --}}
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    {{-- Contenu --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug" x-text="toast.title"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed" x-text="toast.body" x-show="toast.body"></p>

                        <template x-if="toast.url">
                            <a
                                :href="toast.url"
                                target="_blank"
                                class="inline-block mt-2 text-xs font-medium text-orange-600 hover:text-orange-700 underline"
                            >Voir →</a>
                        </template>
                    </div>

                    {{-- Fermer --}}
                    <button
                        @click="dismiss(toast.id)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
                        title="Fermer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function adminNotifToast() {
    return {
        toasts: [],
        timers: {},

        init() {
            window.addEventListener('admin-notif-toast', (e) => {
                const data = e.detail[0] ?? e.detail;
                this.show(data);
            });
        },

        show(data) {
            const id = data.id ?? Date.now();
            this.toasts.push({ ...data, id, visible: true, progress: 100 });
            this.playSound();

            const DURATION = 6000;
            const TICK     = 100;
            let elapsed    = 0;

            this.timers[id] = setInterval(() => {
                elapsed += TICK;
                const toast = this.toasts.find(t => t.id === id);
                if (toast) toast.progress = Math.max(0, 100 - (elapsed / DURATION) * 100);
                if (elapsed >= DURATION) this.dismiss(id);
            }, TICK);
        },

        dismiss(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) toast.visible = false;
            clearInterval(this.timers[id]);
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        },

        playSound() {
            try {
                const ctx  = new (window.AudioContext || window.webkitAudioContext)();
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type            = 'sine';
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            } catch (_) {}
        },
    };
}
</script>
