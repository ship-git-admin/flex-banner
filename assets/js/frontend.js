(function () {
  'use strict';

  function initAllSliders() {
    var sliders = document.querySelectorAll('.fb-slider');
    sliders.forEach(function (el) {
      initSlider(el);
    });
  }

  function initSlider(el) {
    var slides = el.querySelectorAll('.fb-slide');
    var total = slides.length;
    if (!total) return;

    var animation = el.dataset.animation || 'slide';
    var slidesToShowPC = parseInt(el.dataset.slidesToShowPc, 10) || 1;
    var slidesToShowTab = parseInt(el.dataset.slidesToShowTab, 10) || 1;
    var slidesToShowSP = parseInt(el.dataset.slidesToShowSp, 10) || 1;
    var loop = el.dataset.loop !== '0';
    var autoplay = el.dataset.autoplay === '1';
    var interval = parseInt(el.dataset.interval, 10) || 5000;
    var showArrows = el.dataset.arrows === '1';
    var showDots = el.dataset.dots === '1';
    var track = el.querySelector('.fb-slider-track');
    var prevBtn = el.querySelector('.fb-slider-prev');
    var nextBtn = el.querySelector('.fb-slider-next');
    var dotsEl = el.querySelector('.fb-slider-dots');
    var navEl = el.querySelector('.fb-slider-nav');
    var current = 0;
    var timer = null;
    var slidesToShow = 0;
    var maxIndex = -1;
    var dots = [];

    function configuredSlidesToShow() {
      var viewportWidth = window.innerWidth || document.documentElement.clientWidth || 1025;
      var configured;

      if (viewportWidth <= 767) {
        configured = slidesToShowSP;
      } else if (viewportWidth <= 1024) {
        configured = slidesToShowTab;
      } else {
        configured = slidesToShowPC;
      }

      if (animation === 'fade') configured = 1;
      return Math.min(total, Math.max(1, configured));
    }

    function updateDots() {
      dots.forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === current);
      });
    }

    function updateControls() {
      var canMove = maxIndex > 0;
      if (navEl) navEl.style.display = canMove && (showArrows || showDots) ? '' : 'none';
      if (prevBtn) {
        prevBtn.style.display = showArrows && canMove ? '' : 'none';
        prevBtn.disabled = !loop && current === 0;
      }
      if (nextBtn) {
        nextBtn.style.display = showArrows && canMove ? '' : 'none';
        nextBtn.disabled = !loop && current >= maxIndex;
      }
      if (dotsEl) dotsEl.style.display = showDots && canMove ? '' : 'none';
    }

    function rebuildDots() {
      if (!dotsEl) return;
      dotsEl.innerHTML = '';
      dots = [];
      if (!showDots || maxIndex <= 0) return;

      for (var d = 0; d <= maxIndex; d++) {
        (function (index) {
          var dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'fb-slider-dot';
          dot.setAttribute('aria-label', (index + 1) + '枚目から表示');
          dot.addEventListener('click', function () {
            goTo(index);
            resetTimer();
          });
          dotsEl.appendChild(dot);
          dots.push(dot);
        })(d);
      }
    }

    function updateTrackPosition() {
      var offset = slides[current].offsetLeft - slides[0].offsetLeft;
      track.style.transform = offset ? 'translateX(-' + offset + 'px)' : 'translateX(0px)';
    }

    function goTo(idx) {
      if (loop) {
        if (idx < 0) idx = maxIndex;
        if (idx > maxIndex) idx = 0;
      } else {
        idx = Math.max(0, Math.min(maxIndex, idx));
      }

      if (animation === 'fade') {
        slides[current].classList.remove('is-active');
        slides[idx].classList.add('is-active');
      }
      current = idx;
      if (animation !== 'fade') updateTrackPosition();
      updateDots();
      updateControls();
      if (!loop && current >= maxIndex) stopTimer();
    }

    function next() {
      if (!loop && current >= maxIndex) {
        stopTimer();
        return;
      }
      goTo(current + 1);
    }

    function prev() {
      if (!loop && current <= 0) return;
      goTo(current - 1);
    }

    function startTimer() {
      if (!autoplay || timer !== null || maxIndex <= 0 || (!loop && current >= maxIndex)) return;
      timer = setInterval(next, interval);
    }

    function stopTimer() {
      if (timer !== null) clearInterval(timer);
      timer = null;
    }

    function resetTimer() {
      stopTimer();
      startTimer();
    }

    // 矢印
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        prev();
        resetTimer();
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        next();
        resetTimer();
      });
    }

    // ホバーで一時停止
    el.addEventListener('mouseenter', stopTimer);
    el.addEventListener('mouseleave', startTimer);

    // タッチスワイプ
    var touchStartX = 0;
    el.addEventListener('touchstart', function (e) {
      touchStartX = e.touches[0].clientX;
    }, {
      passive: true
    });
    el.addEventListener('touchend', function (e) {
      var diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) {
        if (diff > 0) {
          next();
        } else {
          prev();
        }
        resetTimer();
      }
    }, {
      passive: true
    });

    function updateResponsiveSettings() {
      var nextSlidesToShow = configuredSlidesToShow();
      var nextMaxIndex = total - nextSlidesToShow;
      var slideCountChanged = nextSlidesToShow !== slidesToShow;

      slidesToShow = nextSlidesToShow;
      maxIndex = nextMaxIndex;
      current = Math.min(current, maxIndex);

      if (animation === 'fade') {
        slides.forEach(function (slide, i) {
          slide.classList.toggle('is-active', i === current);
        });
      } else {
        updateTrackPosition();
      }

      if (slideCountChanged) rebuildDots();
      updateDots();
      updateControls();
      if (maxIndex <= 0 || (!loop && current >= maxIndex)) {
        stopTimer();
      } else {
        startTimer();
      }
    }

    window.addEventListener('resize', updateResponsiveSettings);
    updateResponsiveSettings();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSliders);
  } else {
    initAllSliders();
  }

})();
