/**
 * Good Assumptions — Global Animation Layer
 * Loaded via wp_enqueue_scripts. Runs once, applies everywhere.
 *
 * Covers:
 *  1. .reveal        — fade + slide up on scroll (IntersectionObserver)
 *  2. .reveal-stagger — staggers children with CSS --delay custom prop
 *  3. Active nav state — highlights current page link in .ga-nav
 *  4. Anchor nav pills — marks active pill as page scrolls (.anchor-pill)
 */

(function () {
  'use strict';

  /* ─── 1. Reveal observer ───────────────────────────────────────────── */
  var revealObs = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('on');
          revealObs.unobserve(e.target);
        }
      });
    },
    { threshold: 0.06, rootMargin: '0px 0px -40px 0px' }
  );

  document.querySelectorAll('.reveal').forEach(function (el) {
    revealObs.observe(el);
  });

  /* ─── 2. Stagger: assign --delay to each direct child ─────────────── */
  document.querySelectorAll('.reveal-stagger').forEach(function (parent) {
    var children = Array.from(parent.children);
    children.forEach(function (child, i) {
      child.style.setProperty('--delay', i * 60 + 'ms');
    });

    var staggerObs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            Array.from(e.target.children).forEach(function (child) {
              setTimeout(function () {
                child.classList.add('on');
              }, parseInt(child.style.getPropertyValue('--delay')) || 0);
            });
            staggerObs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.04, rootMargin: '0px 0px -20px 0px' }
    );

    staggerObs.observe(parent);
  });

  /* ─── 3. Nav active state — mark current page link ────────────────── */
  var currentPath = window.location.pathname;
  document.querySelectorAll('.ga-nav a, .wp-block-navigation a').forEach(function (link) {
    try {
      var linkPath = new URL(link.href, window.location.origin).pathname;
      if (
        linkPath === currentPath ||
        (linkPath !== '/' && currentPath.startsWith(linkPath))
      ) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    } catch (e) {}
  });

  /* ─── 4. Anchor nav pills — update active on scroll ───────────────── */
  var pills = document.querySelectorAll('.anchor-pill[href^="#"]');
  if (pills.length) {
    var sections = [];
    pills.forEach(function (pill) {
      var target = document.querySelector(pill.getAttribute('href'));
      if (target) sections.push({ pill: pill, target: target });
    });

    var pillObs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            pills.forEach(function (p) { p.classList.remove('active'); });
            var match = sections.find(function (s) { return s.target === e.target; });
            if (match) match.pill.classList.add('active');
          }
        });
      },
      { threshold: 0.3, rootMargin: '-10% 0px -60% 0px' }
    );

    sections.forEach(function (s) { pillObs.observe(s.target); });
  }

})();
