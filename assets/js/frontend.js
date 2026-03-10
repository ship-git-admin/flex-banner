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
    var autoplay = el.dataset.autoplay === '1';
    var interval = parseInt(el.dataset.interval, 10) || 5000;
    var showArrows = el.dataset.arrows === '1';
    var showDots = el.dataset.dots === '1';
    var track = el.querySelector('.fb-slider-track');
    var prevBtn = el.querySelector('.fb-slider-prev');
    var nextBtn = el.querySelector('.fb-slider-next');
    var dotsEl = el.querySelector('.fb-slider-dots');
    var current = 0;
    var timer = null;
    var dots = [];

    // スライドが1枚以下の場合はナビを非表示
    if (total <= 1) {
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';
      if (dotsEl) dotsEl.style.display = 'none';
      if (animation === 'fade') slides[0] && slides[0].classList.add('is-active');
      return;
    }

    // ドット生成
    if (showDots && dotsEl) {
      for (var d = 0; d < total; d++) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'fb-slider-dot';
        dot.setAttribute('aria-label', (d + 1) + '枚目');
        dotsEl.appendChild(dot);
        dots.push(dot);
      }
    } else if (dotsEl) {
      dotsEl.style.display = 'none';
    }

    if (!showArrows) {
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';
    }

    function updateDots() {
      dots.forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === current);
      });
    }

    function goTo(idx) {
      if (idx < 0) idx = total - 1;
      if (idx >= total) idx = 0;

      if (animation === 'fade') {
        slides[current].classList.remove('is-active');
        slides[idx].classList.add('is-active');
      } else {
        track.style.transform = 'translateX(-' + (idx * 100) + '%)';
      }
      current = idx;
      updateDots();
    }

    function next() {
      goTo(current + 1);
    }

    function prev() {
      goTo(current - 1);
    }

    function startTimer() {
      if (!autoplay) return;
      timer = setInterval(next, interval);
    }

    function stopTimer() {
      clearInterval(timer);
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

    // ドットクリック
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        goTo(i);
        resetTimer();
      });
    });

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

    // 初期化
    if (animation === 'fade') {
      slides.forEach(function (s, i) {
        s.classList.toggle('is-active', i === 0);
      });
    }
    updateDots();
    startTimer();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSliders);
  } else {
    initAllSliders();
  }

})();