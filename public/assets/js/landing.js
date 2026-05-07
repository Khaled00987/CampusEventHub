/**
 * landing.js — Home page scroll effects (page-home only).
 * Staggered reveals, hero entrance, optional stat counter, subtle band parallax.
 */

(function () {
    'use strict';

    if (!document.body.classList.contains('page-home')) {
        return;
    }

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isMobile = window.matchMedia('(max-width: 768px)').matches;

    /* Hero ready state on load */
    var hero = document.querySelector('.home-hero--load');
    if (hero) {
        requestAnimationFrame(function () {
            hero.classList.add('is-ready');
        });
    }

    /* IntersectionObserver with stagger via data-reveal-delay */
    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        var revealEls = document.querySelectorAll('.page-home .reveal');
        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: '0px 0px -6% 0px', threshold: 0.06 }
        );
        revealEls.forEach(function (el) {
            if (!hero || !hero.contains(el) || (!el.closest('.home-hero__copy') && !el.classList.contains('home-hero__media'))) {
                observer.observe(el);
            }
        });
        if (hero) {
            hero.querySelectorAll('.home-hero__copy .reveal, .home-hero__media.reveal').forEach(function (el) {
                observer.unobserve(el);
            });
        }
    } else {
        document.querySelectorAll('.page-home .reveal').forEach(function (el) {
            el.classList.add('is-visible');
        });
        if (hero) {
            hero.classList.add('is-ready');
        }
    }

    /* Animate stat numbers when hero stats scroll into view */
    if (!prefersReducedMotion) {
        var statCards = document.querySelectorAll('.home-stat-card strong[data-count]');
        if (statCards.length && 'IntersectionObserver' in window) {
            var counted = false;
            var countObserver = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting || counted) {
                            return;
                        }
                        counted = true;
                        statCards.forEach(function (el) {
                            var target = parseInt(el.getAttribute('data-count'), 10) || 0;
                            var start = 0;
                            var duration = 900;
                            var startTime = null;
                            function step(ts) {
                                if (!startTime) {
                                    startTime = ts;
                                }
                                var p = Math.min((ts - startTime) / duration, 1);
                                el.textContent = String(Math.floor(start + (target - start) * p));
                                if (p < 1) {
                                    requestAnimationFrame(step);
                                }
                            }
                            requestAnimationFrame(step);
                        });
                        countObserver.disconnect();
                    });
                },
                { threshold: 0.3 }
            );
            var statsWrap = document.querySelector('.home-stats');
            if (statsWrap) {
                countObserver.observe(statsWrap);
            }
        }
    } else {
        document.querySelectorAll('.home-stat-card strong[data-count]').forEach(function (el) {
            el.textContent = el.getAttribute('data-count') || '0';
        });
    }

    /* Parallax disabled — changing background-position caused extra scrollbars in some browsers */
})();
