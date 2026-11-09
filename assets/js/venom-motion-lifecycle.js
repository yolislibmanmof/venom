/*! VENOM · Motion Lifecycle Coordinator */
(function () {
  'use strict';
  var subscribers = new Set();
  var pageVisible = !document.hidden;
  var reducedMotionMedia = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
  var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection || null;
  var prefersReducedMotion = function () { return reducedMotionMedia && reducedMotionMedia.matches; };
  var prefersReducedData = function () { return connection && connection.saveData === true; };

  var syncMotionCapabilities = function () {
    document.documentElement.classList.toggle('venom-reduced-motion', prefersReducedMotion());
    document.documentElement.classList.toggle('venom-save-data', prefersReducedData());
    var bg = document.querySelector('.venom-background-image-layer');
    if (bg) {
      if (prefersReducedData()) { bg.style.setProperty('filter', 'none', 'important'); bg.style.setProperty('transform', 'none', 'important'); }
      else { bg.style.removeProperty('filter'); bg.style.removeProperty('transform'); }
    }
  };

  var notify = function (visible) {
    pageVisible = visible === true && !document.hidden;
    document.documentElement.classList.toggle('venom-page-hidden', !pageVisible);
    document.dispatchEvent(new CustomEvent('venom:page-visibility', { detail: { visible: pageVisible } }));
    subscribers.forEach(function (s) { try { s(pageVisible); } catch (e) {} });
  };

  var subscribe = function (s) {
    if (typeof s !== 'function') return function () {};
    subscribers.add(s); s(pageVisible);
    return function () { subscribers.delete(s); };
  };

  window.VenomMotionLifecycle = { isVisible: function () { return pageVisible; }, subscribe: subscribe, prefersReducedMotion: prefersReducedMotion, prefersReducedData: prefersReducedData };

  document.addEventListener('visibilitychange', function () { notify(!document.hidden); }, { passive: true });
  window.addEventListener('pagehide', function () { notify(false); }, { passive: true });
  window.addEventListener('pageshow', function () { notify(!document.hidden); }, { passive: true });
  syncMotionCapabilities(); notify(!document.hidden);

  if (reducedMotionMedia && reducedMotionMedia.addEventListener) reducedMotionMedia.addEventListener('change', syncMotionCapabilities);
  if (connection && connection.addEventListener) connection.addEventListener('change', syncMotionCapabilities);
})();