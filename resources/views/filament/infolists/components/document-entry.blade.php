<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $url   = $entry->getDocumentUrl();
        $isPdf = $entry->getIsPdf();
        $icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';
    @endphp

    @if (!$url)
        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
    @else
        <div style="display:flex;flex-direction:column;gap:8px;">
            @if ($isPdf)
                <div style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:#f9fafb;">
                    <embed src="{{ $url }}" type="application/pdf" width="100%" height="340" style="display:block;" />
                </div>
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;font-size:12px;font-weight:500;color:#374151;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,.05);">
                    {!! $icon !!} Ouvrir le PDF
                </a>
            @else
                <img src="{{ $url }}" alt="Document"
                     style="max-height:220px;max-width:100%;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;" />
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;font-size:12px;font-weight:500;color:#374151;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,.05);">
                    {!! $icon !!} Voir en plein écran
                </a>
            @endif
        </div>
    @endif
</x-dynamic-component>
