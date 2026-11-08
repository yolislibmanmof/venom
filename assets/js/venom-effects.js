/**
 * VENOM · Advanced Effects & Interactions
 * Particle system, cursor tracking, smooth animations
 */
(function() {
  'use strict';
  
  var D = document;
  var R = D.documentElement;
  var W = window;
  
  // Utility functions
  var $ = function(s, c) { return (c || D).querySelector(s); };
  var $$ = function(s, c) { return Array.prototype.slice.call((c || D).querySelectorAll(s)); };
  
  // Configuration
  var config = {
    particles: {
      enabled: true,
      count: 25,
      speed: 1,
      colors: ['#7c3aed', '#22d3ee', '#a855f7']
    },
    cursor: {
      enabled: !('ontouchstart' in W),
      glowSize: 400,
      sensitivity: 0.5
    },
    scroll: {
      smooth: true,
      parallax: true
    }
  };
  
  // State
  var state = {
    mouseX: 0,
    mouseY: 0,
    cursorX: 0,
    cursorY: 0,
    ticking: false
  };
  
  /**
   * PARTICLE SYSTEM
   * Creates floating particles in background
   */
  function initParticles() {
    if (!config.particles.enabled) return;
    
    var container = D.createElement('div');
    container.className = 'v-particles';
    container.setAttribute('aria-hidden', 'true');
    
    for (var i = 0; i < config.particles.count; i++) {
      var particle = D.createElement('div');
      particle.className = 'v-particle';
      
      // Random position and size
      var size = Math.random() * 6 + 2;
      var left = Math.random() * 100;
      var delay = Math.random() * 20;
      var duration = 15 + Math.random() * 20;
      
      particle.style.cssText = 
        'width:' + size + 'px;' +
        'height:' + size + 'px;' +
        'left:' + left + '%;' +
        'animation-delay:-' + delay + 's;' +
        'animation-duration:' + duration + 's;' +
        'background:' + config.particles.colors[Math.floor(Math.random() * config.particles.colors.length)];
      
      container.appendChild(particle);
    }
    
    D.body.appendChild(container);
  }
  
  /**
   * CURSOR GLOW EFFECT
   * Follows mouse with smooth interpolation
   */
  function initCursorGlow() {
    if (!config.cursor.enabled) return;
    
    var glow = D.createElement('div');
    glow.className = 'v-cursor-glow';
    glow.setAttribute('aria-hidden', 'true');
    D.body.appendChild(glow);
    
    // Show glow after user interaction
    D.addEventListener('mousemove', function() {
      glow.classList.add('is-active');
    }, { once: true });
    
    // Track mouse
    D.addEventListener('mousemove', function(e) {
      state.mouseX = e.clientX;
      state.mouseY = e.clientY;
      
      if (!state.ticking) {
        W.requestAnimationFrame(updateCursor);
        state.ticking = true;
      }
    });
    
    function updateCursor() {
      // Smooth interpolation
      state.cursorX += (state.mouseX - state.cursorX) * 0.1;
      state.cursorY += (state.mouseY - state.cursorY) * 0.1;
      
      glow.style.left = state.cursorX + 'px';
      glow.style.top = state.cursorY + 'px';
      
      state.ticking = false;
    }
    
    // Hide on leave
    D.addEventListener('mouseleave', function() {
      glow.classList.remove('is-active');
    });
    
    D.addEventListener('mouseenter', function() {
      glow.classList.add('is-active');
    });
  }
  
  /**
   * 3D TILT EFFECT FOR CARDS
   * Enhanced version with mouse tracking
   */
  function initTiltEffect() {
    var tiltElements = $$('[data-tilt]');
    
    tiltElements.forEach(function(el) {
      var rect = el.getBoundingClientRect();
      var centerX = rect.left + rect.width / 2;
      var centerY = rect.top + rect.height / 2;
      
      el.addEventListener('mousemove', function(e) {
        var x = e.clientX - centerX;
        var y = e.clientY - centerY;
        
        var rotateX = (y / (rect.height / 2)) * -10;
        var rotateY = (x / (rect.width / 2)) * 10;
        
        el.style.transform = 
          'perspective(1000px) ' +
          'rotateX(' + rotateX + 'deg) ' +
          'rotateY(' + rotateY + 'deg) ' +
          'translateY(-5px) ' +
          'scale3d(1.02, 1.02, 1.02)';
      });
      
      el.addEventListener('mouseleave', function() {
        el.style.transform = '';
      });
    });
  }
  
  /**
   * PARALLAX SCROLL EFFECT
   */
  function initParallax() {
    if (!config.scroll.parallax) return;
    
    var parallaxElements = $$('.v-parallax');
    
    if (parallaxElements.length === 0) return;
    
    W.addEventListener('scroll', function() {
      var scrolled = W.pageYOffset;
      
      parallaxElements.forEach(function(el) {
        var speed = parseFloat(el.getAttribute('data-speed')) || 0.5;
        var yPos = -(scrolled * speed);
        el.style.transform = 'translateY(' + yPos + 'px)';
      });
    }, { passive: true });
  }
  
  /**
   * SMOOTH SCROLL FOR ANCHOR LINKS
   */
  function initSmoothScroll() {
    if (!config.scroll.smooth) return;
    
    D.addEventListener('click', function(e) {
      var link = e.target.closest('a[href^="#"]');
      if (!link) return;
      
      var target = D.querySelector(link.getAttribute('href'));
      if (!target) return;
      
      e.preventDefault();
      
      var offsetTop = target.getBoundingClientRect().top + W.pageYOffset - 80;
      
      W.scrollTo({
        top: offsetTop,
        behavior: 'smooth'
      });
      
      // Update URL without scroll jump
      if (history.pushState) {
        history.pushState(null, null, link.getAttribute('href'));
      }
    });
  }
  
  /**
   * INTERSECTION OBSERVER FOR REVEAL ANIMATIONS
   */
  function initRevealAnimations() {
    if (!('IntersectionObserver' in W)) {
      // Fallback for old browsers
      $$('.v-reveal').forEach(function(el) {
        el.classList.add('is-in');
      });
      return;
    }
    
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var delay = parseInt(el.getAttribute('data-delay')) || 0;
          
          setTimeout(function() {
            el.classList.add('is-in');
          }, delay);
          
          observer.unobserve(el);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    $$('.v-reveal').forEach(function(el) {
      observer.observe(el);
    });
  }
  
  /**
   * NUMBER COUNTER ANIMATION
   */
  function initCounters() {
    var counters = $$('.v-count');
    
    if (counters.length === 0 || !('IntersectionObserver' in W)) return;
    
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    
    counters.forEach(function(el) {
      observer.observe(el);
    });
    
    function animateCounter(el) {
      var target = parseInt(el.getAttribute('data-to')) || 0;
      var duration = 1500;
      var start = 0;
      var startTime = null;
      
      function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        
        // Easing function (easeOutQuart)
        var ease = 1 - Math.pow(1 - progress, 4);
        var current = Math.floor(ease * target);
        
        el.textContent = current.toLocaleString('id-ID');
        
        if (progress < 1) {
          W.requestAnimationFrame(step);
        } else {
          el.textContent = target.toLocaleString('id-ID');
        }
      }
      
      W.requestAnimationFrame(step);
    }
  }
  
  /**
   * MAGNETIC BUTTONS
   * Buttons that attract to cursor
   */
  function initMagneticButtons() {
    var buttons = $$('.v-btn-magnetic');
    
    buttons.forEach(function(btn) {
      btn.addEventListener('mousemove', function(e) {
        var rect = btn.getBoundingClientRect();
        var x = e.clientX - rect.left - rect.width / 2;
        var y = e.clientY - rect.top - rect.height / 2;
        
        btn.style.transform = 
          'translate(' + (x * 0.3) + 'px, ' + (y * 0.3) + 'px)';
      });
      
      btn.addEventListener('mouseleave', function() {
        btn.style.transform = '';
      });
    });
  }
  
  /**
   * TEXT REVEAL ANIMATION
   */
  function initTextReveal() {
    var revealTexts = $$('.v-text-reveal');
    
    revealTexts.forEach(function(el) {
      var text = el.textContent;
      el.textContent = '';
      el.setAttribute('aria-label', text);
      
      var chars = text.split('');
      chars.forEach(function(char, i) {
        var span = D.createElement('span');
        span.textContent = char === ' ' ? '\u00A0' : char;
        span.style.cssText = 'display:inline-block;opacity:0;transform:translateY(20px);transition:all 0.4s ease ' + (i * 0.03) + 's';
        el.appendChild(span);
      });
      
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            setTimeout(function() {
              el.childNodes.forEach(function(span) {
                span.style.opacity = '1';
                span.style.transform = 'translateY(0)';
              });
            }, 200);
            observer.unobserve(el);
          }
        });
      }, { threshold: 0.5 });
      
      observer.observe(el);
    });
  }
  
  /**
   * INIT ALL EFFECTS
   */
  function init() {
    // Wait for DOM ready
    if (D.readyState === 'loading') {
      D.addEventListener('DOMContentLoaded', init);
      return;
    }
    
    initParticles();
    initCursorGlow();
    initTiltEffect();
    initParallax();
    initSmoothScroll();
    initRevealAnimations();
    initCounters();
    initMagneticButtons();
    initTextReveal();
    
    // Log initialization
    console.log('VENOM Effects initialized');
  }
  
  // Start
  init();
  
})();