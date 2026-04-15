<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $photos = $getPhotos();
        $total  = count($photos);
    @endphp

    @if ($total === 0)
        <div class="flex items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700" style="height:420px">
            <div class="text-center">
                <svg class="mx-auto mb-3 w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M2.25 12V6a2.25 2.25 0 012.25-2.25h15A2.25 2.25 0 0121.75 6v12a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 18v-6z" />
                </svg>
                <p class="text-sm text-gray-400 dark:text-gray-500">Aucune photo</p>
            </div>
        </div>
    @elseif ($total === 1)
        <div class="rounded-2xl overflow-hidden shadow-lg" style="height:420px">
            <img src="{{ $photos[0] }}" class="w-full h-full object-cover" alt="Photo" />
        </div>
    @else
        <div
            x-data="{ current: 0 }"
            wire:ignore
            class="relative rounded-2xl overflow-hidden shadow-xl group"
            style="height:420px; background:#0f172a"
        >
            {{-- Slides --}}
            @foreach ($photos as $i => $url)
                <div x-show="current === {{ $i }}" x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute inset-0">
                    <img src="{{ $url }}" class="w-full h-full object-cover" alt="Photo {{ $i + 1 }}" />
                    {{-- Dégradé bas pour lisibilité des contrôles --}}
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
            @endforeach

            {{-- Bouton précédent --}}
            <button
                @click="current = (current - 1 + {{ $total }}) % {{ $total }}"
                style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); z-index:20;
                       width:2.75rem; height:2.75rem; border-radius:9999px;
                       background:white; border:1px solid #e5e7eb;
                       box-shadow:0 4px 12px rgba(0,0,0,0.15);
                       display:flex; align-items:center; justify-content:center;
                       cursor:pointer; transition:background 0.15s, box-shadow 0.15s;"
                onmouseover="this.style.background='#f9fafb'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)'"
                onmouseout="this.style.background='white'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'"
                aria-label="Photo précédente"
            >
                <svg width="20" height="20" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Bouton suivant --}}
            <button
                @click="current = (current + 1) % {{ $total }}"
                style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); z-index:20;
                       width:2.75rem; height:2.75rem; border-radius:9999px;
                       background:white; border:1px solid #e5e7eb;
                       box-shadow:0 4px 12px rgba(0,0,0,0.15);
                       display:flex; align-items:center; justify-content:center;
                       cursor:pointer; transition:background 0.15s, box-shadow 0.15s;"
                onmouseover="this.style.background='#f9fafb'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)'"
                onmouseout="this.style.background='white'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'"
                aria-label="Photo suivante"
            >
                <svg width="20" height="20" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            {{-- Indicateurs (points) --}}
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                @foreach ($photos as $i => $url)
                    <button
                        @click="current = {{ $i }}"
                        :class="current === {{ $i }}
                            ? 'w-6 h-2 bg-white'
                            : 'w-2 h-2 bg-white/50 hover:bg-white/80'"
                        class="rounded-full transition-all duration-300 focus:outline-none"
                        aria-label="Photo {{ $i + 1 }}"
                    ></button>
                @endforeach
            </div>

            {{-- Compteur --}}
            <div
                class="absolute top-4 right-4 z-20
                       bg-black/50 backdrop-blur-sm
                       text-white text-xs font-medium
                       px-3 py-1.5 rounded-full
                       shadow-sm"
                x-text="(current + 1) + ' / {{ $total }}'"
            ></div>
        </div>
    @endif
</x-dynamic-component>
