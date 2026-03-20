@php
    /** @var \App\Models\Syllabus $syllabus */
    /** @var \Illuminate\Support\Collection|\App\Models\CompleteSyllabus[] $savedVersions */

    $openButton         = $openButton ?? 'none'; // none | floating
    $previewMode        = $previewMode ?? 'live'; // live | saved
    $previewVariant     = $previewVariant ?? 'complete'; // complete | abridged | assessment
    $activeSavedVersion = $activeSavedVersion ?? null;
    $savedVersions      = $savedVersions ?? collect();

    $isSaved = $previewMode === 'saved' && $activeSavedVersion;

    $currentLabel = $isSaved
        ? ('Saved v' . $activeSavedVersion->version . ' • ' . ($activeSavedVersion->created_at?->format('M d, Y g:i A') ?? ''))
        : 'Live preview';
@endphp

<style>
    .versions-fab {
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 100000;
        background: linear-gradient(135deg, #0e7490 0%, #0b556b 100%);
        color: #fff;
        border: none;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        cursor: pointer;
        box-shadow: 0 10px 22px rgba(14, 116, 144, 0.35);
        transition: all 150ms ease;
    }

    .versions-fab:hover {
        box-shadow: 0 12px 28px rgba(14, 116, 144, 0.45);
        transform: translateY(-2px);
    }

    .versions-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99990;
        background: rgba(0, 0, 0, 0.45);
    }

    .versions-drawer {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        width: 450px; 
        /* 360px */
        max-width: calc(100vw - 38px);
        /* max-width: calc(100vw - 28px); */
        z-index: 100000;
        background: #ffffff;
        transform: translateX(110%);
        transition: transform 160ms ease;
        box-shadow: -10px 0 28px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif;
    }

    .versions-drawer.is-open {
        transform: translateX(0);
    }

    .vd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 14px;
        border-bottom: 2px solid #0e7490;
        background: linear-gradient(90deg, rgba(14, 116, 144, 0.06) 0%, rgba(11, 85, 107, 0.06) 100%);
    }

    .vd-title {
        font-weight: 900;
        color: #0b556b;
        font-size: 13px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .vd-close {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        color: #111827;
    }

    .vd-body {
        padding: 12px 14px 16px;
        overflow: auto;
        flex: 1;
    }

    .vd-current {
        font-size: 12px;
        color: #0b1220;
        background: #eef2ff;
        border: 1px solid #e0e7ff;
        padding: 10px 12px;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    .vd-section-title {
        font-size: 11px;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin: 14px 0 8px;
    }

    .vd-links {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .vd-link,
    .vd-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        text-decoration: none;
        border: 1px solid #e5e7eb;
        padding: 9px 10px;
        border-radius: 10px;
        background: #fff;
        color: #0b1220;
        font-size: 12px;
        font-weight: 700;
    }

    .vd-link.is-active {
        border-color: #0e7490;
        background: #ecfeff;
        color: #0b556b;
        font-weight: 800;
    }

    .vd-version {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 10px;
        background: #fff;
        margin-bottom: 10px;
        transition: all 150ms ease;
    }

    .vd-version:hover {
        border-color: #0e7490;
        box-shadow: 0 2px 8px rgba(14, 116, 144, 0.12);
    }

    .vd-version.is-active {
        border-color: #0e7490;
        background: #ecfeff;
        box-shadow: 0 4px 12px rgba(14, 116, 144, 0.18);
    }

    .vd-version-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 10px;
        margin-bottom: 8px;
    }

    .vd-v {
        font-size: 13px;
        font-weight: 900;
        color: #0b1220;
    }

    .vd-date {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
    }

    .vd-actions {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
    }

    .vd-action-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .vd-action-group a {
        flex: 1;
        font-weight: 800;
        justify-content: center;
    }

    .vd-action-group a.vd-download {
        flex: 0 0 auto;
    }

    .vd-actions a {
        font-weight: 800;
        justify-content: center;
    }

    .vd-actions a.vd-download {
        background: linear-gradient(135deg, #0e7490 0%, #0b556b 100%);
        color: #fff;
        border-color: transparent;
        font-weight: 800;
        transition: all 150ms ease;
    }

    .vd-actions a.vd-download:hover {
        box-shadow: 0 4px 12px rgba(14, 116, 144, 0.3);
    }

    .vd-empty {
        font-size: 12px;
        color: #64748b;
        padding: 8px 0;
    }

    @media print {
        .versions-fab,
        .versions-backdrop,
        .versions-drawer {
            display: none !important;
        }
    }
</style>

@if ($openButton === 'floating')
    <button type="button" class="versions-fab" onclick="openSyllabusVersions()">Versions</button>
@endif

<div id="syllabusVersionsBackdrop" class="versions-backdrop" hidden onclick="closeSyllabusVersions()"></div>

<aside id="syllabusVersionsDrawer" class="versions-drawer" aria-hidden="true">
    <div class="vd-header">
        <div class="vd-title">Syllabus Versions</div>
        <button type="button" class="vd-close" onclick="closeSyllabusVersions()" aria-label="Close">×</button>
    </div>
    <div class="vd-body">
        <div class="vd-current">
            <div style="font-weight:900; margin-bottom:4px;">{{ $currentLabel }}</div>
            <div style="color:#475569;">
                {{ $syllabus->course?->course_code }} – {{ $syllabus->course?->course_title }}
            </div>
        </div>

        <div class="vd-section-title">Preview</div>
        <div class="vd-links">
            <a class="vd-link {{ $previewMode === 'live' && $previewVariant === 'complete' ? 'is-active' : '' }}"
                href="{{ route('syllabus.preview.complete', $syllabus) }}">
                Live – Complete
            </a>
            <a class="vd-link {{ $previewMode === 'live' && $previewVariant === 'abridged' ? 'is-active' : '' }}"
                href="{{ route('syllabus.preview.abridged', $syllabus) }}">
                Live – Abridged
            </a>
            <a class="vd-link {{ $previewMode === 'live' && $previewVariant === 'assessment' ? 'is-active' : '' }}"
                href="{{ route('syllabus.preview.assessment', $syllabus) }}">
                Live – Assessment Plan
            </a>
        </div>

        <div class="vd-section-title">Saved Versions</div>
        @forelse ($savedVersions as $v)
            @php
                $isActive = $isSaved && (int) $activeSavedVersion->id === (int) $v->id;
            @endphp
            <div class="vd-version {{ $isActive ? 'is-active' : '' }}">
                <div class="vd-version-top">
                    <div class="vd-v">v{{ $v->version }}</div>
                    <div class="vd-date">{{ $v->created_at?->format('M d, Y g:i A') }}</div>
                </div>
                <div class="vd-actions">
                    <div class="vd-action-group">
                        <a href="{{ route('syllabus.saved.complete.preview', $v) }}">Complete</a>
                        <a class="vd-download" href="{{ route('syllabus.saved.complete.download', $v) }}">↓</a>
                    </div>
                    <div class="vd-action-group">
                        <a href="{{ route('syllabus.saved.abridged.preview', $v) }}">Abridged</a>
                        <a class="vd-download" href="{{ route('syllabus.saved.abridged.download', $v) }}">↓</a>
                    </div>
                    <div class="vd-action-group">
                        <a href="{{ route('syllabus.saved.assessment.preview', $v) }}">Assessment</a>
                        <a class="vd-download" href="{{ route('syllabus.preview.assessment.download', $syllabus) }}">↓</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="vd-empty">No saved versions yet.</div>
        @endforelse
    </div>
</aside>

<script>
    (function() {
        const drawer = document.getElementById('syllabusVersionsDrawer');
        const backdrop = document.getElementById('syllabusVersionsBackdrop');

        window.openSyllabusVersions = function() {
            if (!drawer || !backdrop) return;
            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            backdrop.hidden = false;
        };

        window.closeSyllabusVersions = function() {
            if (!drawer || !backdrop) return;
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            backdrop.hidden = true;
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeSyllabusVersions();
            }
        });
    })();
</script>
