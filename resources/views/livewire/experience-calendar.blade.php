<div>
    @if ($totalSlots === 0)
        <div class="text-sm text-gray-400 italic py-4 text-center">
            Aucun créneau de disponibilité configuré pour cette expérience.
        </div>
    @else
        {{-- Légende --}}
        <div class="flex flex-wrap gap-x-6 gap-y-2 mb-4 text-sm text-gray-700 dark:text-gray-300">
            <span class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full border" style="background:#dcfce7;border-color:#4ade80"></span>
                Disponible
            </span>
            <span class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full border" style="background:#ffedd5;border-color:#fb923c"></span>
                Partiellement réservé
            </span>
            <span class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full border" style="background:#fee2e2;border-color:#f87171"></span>
                Complet / Groupe
            </span>
            <span class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full border" style="background:#f3f4f6;border-color:#d1d5db"></span>
                Passé
            </span>
            <span class="ml-auto text-xs text-gray-400">{{ $totalSlots }} créneau{{ $totalSlots > 1 ? 'x' : '' }} au total</span>
        </div>

        {{-- Calendrier --}}
        <div id="{{ $calId }}" wire:ignore style="min-height:480px;"></div>

        {{-- Panneau de détail --}}
        <div id="{{ $calId }}-detail" wire:ignore
             style="display:none;margin-top:16px;padding:16px;border-radius:10px;border:1px solid #e5e7eb;background:#f9fafb;">
            <div id="{{ $calId }}-detail-content"></div>
        </div>
    @endif

    @script
    <script>
        (function () {
            const CAL_ID       = @json($calId);
            const EVENTS       = @json($events);
            const INITIAL_DATE = @json($initialDate);

            function buildCalendar() {
                const el = document.getElementById(CAL_ID);
                if (!el || el.dataset.fcInit === '1') return;
                el.dataset.fcInit = '1';

                const detailPanel   = document.getElementById(CAL_ID + '-detail');
                const detailContent = document.getElementById(CAL_ID + '-detail-content');

                const calendar = new FullCalendar.Calendar(el, {
                    initialView: 'dayGridMonth',
                    initialDate: INITIAL_DATE,
                    locale: 'fr',
                    height: 'auto',
                    dayMaxEvents: 4,
                    headerToolbar: {
                        left:   'prev,next today',
                        center: 'title',
                        right:  'dayGridMonth,timeGridWeek,listMonth',
                    },
                    buttonText: {
                        today: "Aujourd'hui",
                        month: 'Mois',
                        week:  'Semaine',
                        list:  'Liste',
                    },
                    events: EVENTS,

                    eventClick: function (info) {
                        const p = info.event.extendedProps;

                        const typeLabel = p.totalReserved === 0
                            ? '<span style="color:#9ca3af">—</span>'
                            : (p.isGroup
                                ? '<span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:4px;font-size:0.75rem;">Groupe</span>'
                                : '<span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:4px;font-size:0.75rem;">Individuel</span>');

                        const remainColor = p.remaining === 0 ? '#dc2626' : (p.remaining <= 2 ? '#ea580c' : '#16a34a');

                        const statusBg = {
                            'Disponible':     '#dcfce7', 'Disponible_t':     '#16a34a',
                            'Partiel':        '#ffedd5', 'Partiel_t':        '#ea580c',
                            'Complet':        '#fee2e2', 'Complet_t':        '#dc2626',
                            'Groupe complet': '#fee2e2', 'Groupe complet_t': '#dc2626',
                            'Passé':          '#f3f4f6', 'Passé_t':          '#9ca3af',
                        };
                        const sbg = statusBg[p.statusLabel]       || '#f3f4f6';
                        const stx = statusBg[p.statusLabel + '_t'] || '#6b7280';

                        detailContent.innerHTML = `
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                <div>
                                    <div style="font-weight:700;font-size:1rem;color:#111827;">📅 ${p.dateLabel}</div>
                                    <div style="color:#6b7280;font-size:0.875rem;margin-top:2px;">🕐 ${p.horaire}</div>
                                </div>
                                <span style="background:${sbg};color:${stx};padding:4px 12px;border-radius:20px;font-size:0.8rem;font-weight:600;">
                                    ${p.statusLabel}
                                </span>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                                <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:12px;text-align:center;">
                                    <div style="font-size:1.5rem;font-weight:700;color:#111827;">${p.totalReserved}</div>
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:2px;">voyageur${p.totalReserved > 1 ? 's' : ''} réservé${p.totalReserved > 1 ? 's' : ''}</div>
                                </div>
                                <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:12px;text-align:center;">
                                    <div style="font-size:1.5rem;font-weight:700;color:${remainColor};">${p.remaining}</div>
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:2px;">place${p.remaining > 1 ? 's' : ''} restante${p.remaining > 1 ? 's' : ''}</div>
                                </div>
                                <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:12px;text-align:center;">
                                    <div style="font-size:1.1rem;font-weight:600;margin-top:2px;">${typeLabel}</div>
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:2px;">type de réservation</div>
                                </div>
                            </div>
                            <div style="margin-top:10px;font-size:0.75rem;color:#9ca3af;text-align:right;">
                                Capacité totale : ${p.capacite} voyageur${p.capacite > 1 ? 's' : ''}
                            </div>
                        `;
                        detailPanel.style.display = 'block';
                    },
                });

                calendar.render();
            }

            function tryInit() {
                if (window.FullCalendar) {
                    buildCalendar();
                } else if (!window._fcScriptAdded) {
                    window._fcScriptAdded = true;
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                    s.onload = buildCalendar;
                    document.head.appendChild(s);
                } else {
                    const t = setInterval(function () {
                        if (window.FullCalendar) { clearInterval(t); buildCalendar(); }
                    }, 100);
                }
            }

            tryInit();
        })();
    </script>
    @endscript
</div>
