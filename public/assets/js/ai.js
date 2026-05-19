/**
 * ai.js — Help assistant AJAX + AI draft loading states (works without JS via normal forms)
 */

(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function nl2br(str) {
        return escapeHtml(str).replace(/\n/g, '<br>');
    }

    function fadeInPanel(el) {
        if (!el) return;
        el.classList.add('help-answer');
        if (!prefersReducedMotion) {
            el.style.opacity = '0';
            requestAnimationFrame(function () {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '1';
            });
        }
    }

    /* --------------------------------------------------------------------------
       Smart Help Assistant
       -------------------------------------------------------------------------- */
    var helpForm = document.getElementById('help-form');
    if (helpForm) {
        var responseEl = document.getElementById('help-response');
        var url = helpForm.getAttribute('data-help-url');
        var submitBtn = helpForm.querySelector('[type="submit"]');

        helpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!url || !responseEl) return;

            var fd = new FormData(helpForm);
            responseEl.className = 'help-response is-loading';
            responseEl.innerHTML = '<p class="meta">Searching help articles…</p>';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('is-loading');
            }

            fetch(url, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    responseEl.classList.remove('is-loading');
                    if (data.error) {
                        responseEl.innerHTML = '<div class="alert alert-error">' + escapeHtml(data.error) + '</div>';
                        return;
                    }
                    var source = data.faq_matched
                        ? (data.from_api ? 'FAQ (polished)' : 'FAQ')
                        : 'No match';
                    responseEl.innerHTML =
                        '<div class="help-answer">' +
                        '<p>' + nl2br(data.answer || '') + '</p>' +
                        '<p class="meta">Source: ' + escapeHtml(source) + '</p>' +
                        '<p class="ai-disclaimer"><em>' + escapeHtml(data.disclaimer || '') + '</em></p>' +
                        '</div>';
                    fadeInPanel(responseEl.querySelector('.help-answer'));
                })
                .catch(function () {
                    responseEl.classList.remove('is-loading');
                    responseEl.innerHTML =
                        '<div class="alert alert-error">Unable to reach the assistant. Please try again.</div>';
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('is-loading');
                    }
                });
        });

        document.querySelectorAll('.faq-chip, .help-sample').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var q = btn.getAttribute('data-question') || btn.getAttribute('data-q');
                var input = document.getElementById('question');
                if (input && q) {
                    input.value = q;
                    input.focus();
                }
            });
        });
    }

    /* --------------------------------------------------------------------------
       AI draft generate — loading state on submit (form still works without JS)
       -------------------------------------------------------------------------- */
    var draftGenerateForm = document.querySelector('form[action*="ai-draft/generate"]');
    if (draftGenerateForm) {
        draftGenerateForm.addEventListener('submit', function () {
            var btn = draftGenerateForm.querySelector('[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.classList.add('is-loading');
                btn.textContent = 'Generating....';
                btn.setAttribute('aria-busy', 'true');
            }
        });
    }
})();
