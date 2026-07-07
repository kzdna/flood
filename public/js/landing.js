(function () {
  'use strict';

  /* ---------- Reveal on scroll ---------- */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Draggable comparison slider ---------- */
  var frame = document.getElementById('compareFrame');
  var handle = document.getElementById('compareHandle');
  if (frame && handle) {
    var rightLayer = frame.querySelector('.layer.right');
    var dragging = false;

    function setSplit(clientX) {
      var rect = frame.getBoundingClientRect();
      var x = Math.max(0, Math.min(clientX - rect.left, rect.width));
      var pct = (x / rect.width) * 100;
      handle.style.left = pct + '%';
      rightLayer.style.clipPath = 'inset(0 0 0 ' + pct + '%)';
    }

    function start(e) {
      dragging = true;
      move(e);
    }
    function move(e) {
      if (!dragging) return;
      var clientX = e.touches ? e.touches[0].clientX : e.clientX;
      setSplit(clientX);
    }
    function end() { dragging = false; }

    handle.addEventListener('mousedown', start);
    window.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    handle.addEventListener('touchstart', start, { passive: true });
    window.addEventListener('touchmove', move, { passive: true });
    window.addEventListener('touchend', end);

    // Click anywhere on the frame to jump the split point
    frame.addEventListener('click', function (e) {
      if (e.target === handle) return;
      setSplit(e.clientX);
    });
  }

  /* ---------- AI Prediction Widget ---------- */

var AI_API = "http://127.0.0.1:5000/api/predict";

var imageInput = document.getElementById("imageInput");
var previewImage = document.getElementById("previewImage");

var predictBtn = document.getElementById("predictBtn");

var predictStatus = document.getElementById("predictStatus");

var prediction = document.getElementById("prediction");

var confidence = document.getElementById("confidence");

var floodArea = document.getElementById("floodArea");


if (imageInput) {

    imageInput.addEventListener("change", function () {

        var file = this.files[0];

        if (!file) return;

        previewImage.src = URL.createObjectURL(file);

        previewImage.style.display = "block";

        predictStatus.textContent = "Gambar siap dianalisis.";

        prediction.textContent = "-";

        confidence.textContent = "-";

        if (floodArea)
            floodArea.textContent = "-";

    });

}


if (predictBtn) {

    predictBtn.addEventListener("click", function () {

        if (!imageInput.files.length) {

            predictStatus.textContent =
                "Silakan pilih gambar terlebih dahulu.";

            return;

        }

        predictStatus.textContent =
            "Sedang menganalisis gambar...";

        prediction.textContent = "...";

        confidence.textContent = "...";

        if (floodArea)
            floodArea.textContent = "...";

        var formData = new FormData();

        formData.append(
            "image",
            imageInput.files[0]
        );

        fetch(AI_API, {

            method: "POST",

            body: formData

        })

        .then(function (response) {

            return response.json();

        })

        .then(function (data) {

            prediction.textContent =
                data.prediction;

            confidence.textContent =
                data.confidence + "%";

            if (floodArea)
                floodArea.textContent =
                    data.flood_area + "%";

            predictStatus.textContent =
                "Analisis selesai.";

        })

        .catch(function () {

            predictStatus.textContent =
                "Tidak dapat terhubung ke layanan AI.";

        });

    });

}
})();
