/**
 * validation.js — Client-side validation with inline errors (mirrors server rules).
 */

(function () {
    'use strict';

    function clearFieldErrors(form) {
        form.querySelectorAll('.client-error').forEach(function (el) {
            el.remove();
        });
        form.querySelectorAll('[aria-invalid="true"]').forEach(function (el) {
            el.removeAttribute('aria-invalid');
        });
    }

    function showError(input, message) {
        var group = input.closest('.form-group');
        if (!group) return;
        var span = document.createElement('span');
        span.className = 'client-error';
        span.setAttribute('role', 'alert');
        span.textContent = message;
        group.appendChild(span);
        input.setAttribute('aria-invalid', 'true');
    }

    function isEmail(val) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
    }

    function validateRegister(form) {
        var ok = true;
        var name = form.querySelector('[name="name"]');
        var email = form.querySelector('[name="email"]');
        var password = form.querySelector('[name="password"]');
        var confirm = form.querySelector('[name="password_confirmation"]');

        if (name && name.value.trim().length < 2) {
            showError(name, 'Name must be at least 2 characters.');
            ok = false;
        }
        if (email && !isEmail(email.value.trim())) {
            showError(email, 'Enter a valid email address.');
            ok = false;
        }
        if (password) {
            var p = password.value;
            if (p.length < 8 || !/[A-Z]/.test(p) || !/[a-z]/.test(p) || !/[0-9]/.test(p)) {
                showError(password, 'Password needs 8+ characters with upper, lower, and number.');
                ok = false;
            }
        }
        if (confirm && password && confirm.value !== password.value) {
            showError(confirm, 'Password confirmation does not match.');
            ok = false;
        }
        return ok;
    }

    function validateEvent(form) {
        var ok = true;
        var title = form.querySelector('[name="title"]');
        var desc = form.querySelector('[name="description"]');
        var capacity = form.querySelector('[name="capacity"]');
        var start = form.querySelector('[name="start_time"]');
        var end = form.querySelector('[name="end_time"]');

        if (title && title.value.trim().length < 5) {
            showError(title, 'Title must be at least 5 characters.');
            ok = false;
        }
        if (desc && desc.value.trim().length < 20) {
            showError(desc, 'Description must be at least 20 characters.');
            ok = false;
        }
        if (capacity) {
            var cap = parseInt(capacity.value, 10);
            if (isNaN(cap) || cap < 1 || cap > 5000) {
                showError(capacity, 'Capacity must be between 1 and 5000.');
                ok = false;
            }
        }
        if (start && end && end.value && start.value && end.value <= start.value) {
            showError(end, 'End time must be after start time.');
            ok = false;
        }
        // Optional event image: max 2MB, JPG/PNG/WEBP only (mirrors server rules)
        var imageInput = form.querySelector('[name="image"]');
        if (imageInput && imageInput.files && imageInput.files.length > 0) {
            var file = imageInput.files[0];
            var maxBytes = 2 * 1024 * 1024;
            var allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (file.size > maxBytes) {
                showError(imageInput, 'Image is too large. Maximum size is 2MB.');
                ok = false;
            } else if (allowed.indexOf(file.type) === -1) {
                showError(imageInput, 'Upload JPG, PNG, or WEBP only.');
                ok = false;
            }
        }
        return ok;
    }

    function validateTicket(form) {
        var ok = true;
        var qty = form.querySelector('[name="quantity"]');
        if (qty) {
            var n = parseInt(qty.value, 10);
            if (isNaN(n) || n < 1 || n > 5) {
                showError(qty, 'Quantity must be between 1 and 5.');
                ok = false;
            }
        }
        var email = form.querySelector('[name="attendee_email"]');
        if (email && email.value && !isEmail(email.value.trim())) {
            showError(email, 'Enter a valid attendee email.');
            ok = false;
        }
        return ok;
    }

    function validateAnnouncement(form) {
        var ok = true;
        var title = form.querySelector('[name="title"]');
        var body = form.querySelector('[name="body"]');
        if (title && title.value.trim().length < 5) {
            showError(title, 'Title must be at least 5 characters.');
            ok = false;
        }
        if (body && body.value.trim().length < 20) {
            showError(body, 'Body must be at least 20 characters.');
            ok = false;
        }
        return ok;
    }

    function validateAiDraft(form) {
        var ok = true;
        var idea = form.querySelector('[name="event_idea"]');
        var audience = form.querySelector('[name="target_audience"]');
        if (idea && !idea.value.trim()) {
            showError(idea, 'Event idea is required.');
            ok = false;
        }
        if (audience && !audience.value.trim()) {
            showError(audience, 'Target audience is required.');
            ok = false;
        }
        return ok;
    }

    var validators = {
        register: validateRegister,
        event: validateEvent,
        ticket: validateTicket,
        announcement: validateAnnouncement,
        'ai-draft': validateAiDraft
    };

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            clearFieldErrors(form);
            var type = form.getAttribute('data-validate');
            var fn = validators[type];
            var ok = fn ? fn(form) : true;

            if (!ok) {
                e.preventDefault();
                var firstInvalid = form.querySelector('[aria-invalid="true"]');
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            /* Disable submit — keep loading until navigation (AI draft can take several seconds) */
            var submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('is-loading');
                var action = form.getAttribute('action') || '';
                if (type === 'ai-draft' && action.indexOf('ai-draft/generate') !== -1) {
                    submitBtn.textContent = 'Generating....';
                    submitBtn.setAttribute('aria-busy', 'true');
                    return;
                }
                var label = submitBtn.textContent;
                submitBtn.textContent = 'Please wait…';
                setTimeout(function () {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('is-loading');
                    submitBtn.textContent = label;
                    submitBtn.removeAttribute('aria-busy');
                }, 3000);
            }
        });
    });
})();
