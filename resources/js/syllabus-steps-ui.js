/**
 * syllabus-steps-ui.js
 * Premium UI micro-interactions for syllabus wizard steps 2, 4, 5.
 * Pure visual enhancements — zero functional changes to Livewire/Alpine logic.
 */

(function () {
    'use strict';

    // ── Weight input live color feedback (Step 5) ─────────────────────────────
    // Turns the weight input border/text green when valid, red when over 100.
    function applyWeightColor(input) {
        const val = parseInt(input.value) || 0;
        input.classList.remove('border-emerald-400', 'border-rose-400', 'text-emerald-700', 'text-rose-600');
        if (val > 0 && val <= 100) {
            input.classList.add('border-emerald-400', 'text-emerald-700');
        } else if (val > 100) {
            input.classList.add('border-rose-400', 'text-rose-600');
        }
    }

    function initWeightInputs(root) {
        root.querySelectorAll('input[data-weight-input]').forEach(input => {
            applyWeightColor(input);
            input.addEventListener('input', () => applyWeightColor(input));
        });
    }

    // ── Schedule row entrance animation (Step 2) ──────────────────────────────
    // Observes the schedule/consultation containers and fades in new rows.
    function observeScheduleRows(root) {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (node.nodeType !== 1) return;
                    if (node.matches?.('[data-schedule-row]') || node.querySelector?.('[data-schedule-row]')) {
                        const rows = node.matches('[data-schedule-row]')
                            ? [node]
                            : [...node.querySelectorAll('[data-schedule-row]')];
                        rows.forEach(row => {
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(-6px)';
                            requestAnimationFrame(() => {
                                row.style.transition = 'opacity 200ms ease, transform 200ms ease';
                                row.style.opacity = '1';
                                row.style.transform = 'translateY(0)';
                            });
                        });
                    }
                });
            });
        });
        observer.observe(root, { childList: true, subtree: true });
    }

    // ── Week accordion smooth height (Step 4) ────────────────────────────────
    // Adds a subtle entrance shimmer to week bodies when they open.
    function observeWeekBodies(root) {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                if (m.type !== 'attributes' || m.attributeName !== 'style') return;
                const el = m.target;
                if (!el.matches?.('[data-week-body]')) return;
                const isVisible = el.style.display !== 'none' && !el.hasAttribute('hidden');
                if (isVisible) {
                    el.style.opacity = '0';
                    requestAnimationFrame(() => {
                        el.style.transition = 'opacity 180ms ease';
                        el.style.opacity = '1';
                    });
                }
            });
        });
        observer.observe(root, { attributes: true, subtree: true, attributeFilter: ['style'] });
    }

    // ── Evaluation table row hover highlight (Step 5) ────────────────────────
    function initTableRowHover(root) {
        root.querySelectorAll('[data-eval-row]').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transition = 'background-color 120ms ease';
                if (!row.dataset.locked) row.style.backgroundColor = 'rgba(0,150,57,0.04)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.backgroundColor = '';
            });
        });
    }

    // ── Init on page load and after Livewire re-renders ───────────────────────
    function init() {
        const root = document.body;
        initWeightInputs(root);
        initTableRowHover(root);
        observeScheduleRows(root);
        observeWeekBodies(root);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('livewire:navigated', init);
    document.addEventListener('livewire:update', () => {
        requestAnimationFrame(() => {
            initWeightInputs(document.body);
            initTableRowHover(document.body);
        });
    });
})();
