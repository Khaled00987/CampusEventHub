/**
 * main.js — Global UI: mobile nav, scroll reveal, navbar state, flash dismiss
 */

(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isMobileNav = window.matchMedia('(max-width: 767px)');

    /* --------------------------------------------------------------------------
       Sticky navbar shadow on scroll
       -------------------------------------------------------------------------- */
    var siteHeader = document.getElementById('site-header');
    if (siteHeader) {
        var onScroll = function () {
            siteHeader.classList.toggle('is-scrolled', window.scrollY > 8);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* --------------------------------------------------------------------------
       Mobile main navigation — dropdown panel (tap-friendly)
       -------------------------------------------------------------------------- */
    var navToggle = document.querySelector('.nav-toggle');
    var mainNav = document.getElementById('main-nav');

    function closeMobileNav() {
        if (!mainNav || !navToggle) {
            return;
        }
        mainNav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute('aria-label', 'Open menu');
        document.body.classList.remove('nav-open');
    }

    function openMobileNav() {
        if (!mainNav || !navToggle) {
            return;
        }
        mainNav.classList.add('is-open');
        navToggle.setAttribute('aria-expanded', 'true');
        navToggle.setAttribute('aria-label', 'Close menu');
        if (isMobileNav.matches) {
            document.body.classList.add('nav-open');
        }
    }

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            if (mainNav.classList.contains('is-open')) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });

        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeMobileNav();
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMobileNav();
            }
        });

        window.addEventListener('resize', function () {
            if (!isMobileNav.matches) {
                closeMobileNav();
            }
        });
    }

    /* --------------------------------------------------------------------------
       Scroll reveal — opacity/transform only (no layout-breaking movement)
       -------------------------------------------------------------------------- */
    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        var revealEls = document.querySelectorAll('.reveal');
        var rootMargin = isMobileNav.matches ? '0px 0px -4% 0px' : '0px 0px -40px 0px';
        var threshold = isMobileNav.matches ? 0.04 : 0.08;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: rootMargin, threshold: threshold }
        );

        revealEls.forEach(function (el) {
            if (el.closest('.home-hero--load')) {
                return;
            }
            observer.observe(el);
        });
    } else {
        document.querySelectorAll('.reveal').forEach(function (el) {
            el.classList.add('is-visible');
        });
    }

    /* --------------------------------------------------------------------------
       Dashboard sidebar — off-canvas drawer (< 1024px)
       -------------------------------------------------------------------------- */
    var dashMenuBtn = document.getElementById('dash-menu-btn');
    var dashSidebar = document.getElementById('dash-sidebar');
    var dashOverlay = document.getElementById('dash-overlay');
    var isDashDrawer = window.matchMedia('(max-width: 1023px)');

    function closeDashSidebar() {
        if (!dashSidebar) {
            return;
        }
        dashSidebar.classList.remove('is-open');
        document.body.classList.remove('dash-sidebar-open');
        if (dashMenuBtn) {
            dashMenuBtn.setAttribute('aria-expanded', 'false');
            dashMenuBtn.setAttribute('aria-label', 'Open dashboard menu');
        }
        if (dashOverlay) {
            dashOverlay.classList.remove('is-visible');
            dashOverlay.setAttribute('hidden', '');
            dashOverlay.setAttribute('aria-hidden', 'true');
        }
    }

    function openDashSidebar() {
        if (!dashSidebar || !isDashDrawer.matches) {
            return;
        }
        dashSidebar.classList.add('is-open');
        document.body.classList.add('dash-sidebar-open');
        if (dashMenuBtn) {
            dashMenuBtn.setAttribute('aria-expanded', 'true');
            dashMenuBtn.setAttribute('aria-label', 'Close dashboard menu');
        }
        if (dashOverlay) {
            dashOverlay.classList.add('is-visible');
            dashOverlay.removeAttribute('hidden');
            dashOverlay.setAttribute('aria-hidden', 'false');
        }
    }

    if (dashMenuBtn && dashSidebar) {
        dashMenuBtn.addEventListener('click', function () {
            if (dashSidebar.classList.contains('is-open')) {
                closeDashSidebar();
            } else {
                openDashSidebar();
            }
        });

        if (dashOverlay) {
            dashOverlay.addEventListener('click', closeDashSidebar);
        }

        dashSidebar.querySelectorAll('.dash-nav-item').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isDashDrawer.matches) {
                    closeDashSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && dashSidebar.classList.contains('is-open')) {
                closeDashSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (!isDashDrawer.matches) {
                closeDashSidebar();
            }
        });
    }

    /* --------------------------------------------------------------------------
       Flash message dismiss buttons
       -------------------------------------------------------------------------- */
    document.querySelectorAll('.alert-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var alert = btn.closest('.alert');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(function () {
                    alert.remove();
                }, 200);
            }
        });
    });

    /* --------------------------------------------------------------------------
       Delete confirmation for POST forms with data-confirm
       -------------------------------------------------------------------------- */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var msg = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    /* --------------------------------------------------------------------------
       Smooth scroll for same-page anchor links
       -------------------------------------------------------------------------- */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var id = anchor.getAttribute('href');
            if (id.length < 2) return;
            var target = document.querySelector(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth' });
            }
        });
    });

    /* --------------------------------------------------------------------------
       Mark active dashboard sidebar link from current URL path
       -------------------------------------------------------------------------- */
    if (dashSidebar) {
        var path = window.location.pathname;
        dashSidebar.querySelectorAll('.dash-nav-item').forEach(function (a) {
            try {
                var linkPath = new URL(a.href).pathname;
                if (path === linkPath || (linkPath !== '/' && path.endsWith(linkPath))) {
                    a.classList.add('is-active');
                    a.setAttribute('aria-current', 'page');
                }
            } catch (err) { /* ignore invalid URLs */ }
        });
    }
})();
