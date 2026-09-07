
      /* =========================================================
   NAVBAR SCROLL EFFECT
========================================================= */

      const navbar = document.getElementById("navbar");

      window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
          navbar.classList.add("scrolled");
        } else {
          navbar.classList.remove("scrolled");
        }
      });

      /* =========================================================
   MOBILE MENU
========================================================= */

      const menuToggle = document.getElementById("menuToggle");
      const navMenu = document.getElementById("navMenu");

      menuToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");

        menuToggle.innerHTML = navMenu.classList.contains("active")
          ? "âœ•"
          : "â˜°";
      });

      /* =========================================================
   MOBILE DROPDOWN
========================================================= */

      document
        .querySelectorAll(
          ".has-dropdown > .nav-link, .has-dropdown > .dropdown-link",
        )
        .forEach((link) => {
          link.addEventListener("click", function (e) {
            if (window.innerWidth <= 900) {
              e.preventDefault();

              const parent = this.parentElement;

              parent.classList.toggle("open");
            }
          });
        });

      /* =========================================================
   CLOSE MOBILE MENU AFTER CLICK
========================================================= */

      document.querySelectorAll(".nav-menu a").forEach((link) => {
        link.addEventListener("click", function () {
          if (
            window.innerWidth <= 900 &&
            !this.parentElement.classList.contains("has-dropdown")
          ) {
            navMenu.classList.remove("active");

            menuToggle.innerHTML = "â˜°";
          }
        });
      });

      /* =========================================================
   COUNTER ANIMATION
========================================================= */

      const counters = document.querySelectorAll(".stat-number");

      let counterStarted = false;

      function startCounters() {
        if (counterStarted) return;

        counterStarted = true;

        counters.forEach((counter) => {
          const target = parseInt(counter.getAttribute("data-target"));

          let current = 0;

          const increment = Math.max(1, Math.ceil(target / 60));

          const updateCounter = () => {
            current += increment;

            if (current >= target) {
              counter.innerText = target + "+";
            } else {
              counter.innerText = current;

              requestAnimationFrame(updateCounter);
            }
          };

          updateCounter();
        });
      }

      const statsSection = document.querySelector(".stats");

      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              startCounters();
            }
          });
        },
        {
          threshold: 0.4,
        },
      );

      observer.observe(statsSection);

      /* =========================================================
   BACK TO TOP
========================================================= */

      const backTop = document.getElementById("backTop");

      window.addEventListener("scroll", () => {
        if (window.scrollY > 500) {
          backTop.classList.add("show");
        } else {
          backTop.classList.remove("show");
        }
      });

      backTop.addEventListener("click", () => {
        window.scrollTo({
          top: 0,
          behavior: "smooth",
        });
      });

      /* =========================================================
   CURRENT YEAR
========================================================= */

      document.getElementById("year").textContent = new Date().getFullYear();

      /* =========================================================
   CLOSE DROPDOWN WHEN CLICKING OUTSIDE
========================================================= */

      document.addEventListener("click", (event) => {
        if (!event.target.closest(".navbar")) {
          document.querySelectorAll(".nav-item.open").forEach((item) => {
            item.classList.remove("open");
          });
        }
      });

      document.addEventListener("DOMContentLoaded", function () {
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".slider-dot");

        let currentIndex = 0;

        // Durasi perpindahan slide
        const slideDuration = 8000;

        function showSlide(index) {
          // Reset semua slide
          slides.forEach((slide) => {
            slide.classList.remove("active");
          });

          // Reset semua dot
          dots.forEach((dot) => {
            dot.classList.remove("active");
          });

          // Aktifkan slide
          slides[index].classList.add("active");

          // Aktifkan dot
          if (dots[index]) {
            dots[index].classList.add("active");
          }
        }

        function nextSlide() {
          currentIndex++;

          if (currentIndex >= slides.length) {
            currentIndex = 0;
          }

          showSlide(currentIndex);
        }

        // Jalankan slide otomatis
        setInterval(nextSlide, slideDuration);

        // Tampilkan slide pertama
        showSlide(currentIndex);

        // ==========================================
        // TOMBOL NEXT
        // ==========================================

        window.changeSlide = function (direction) {
          currentIndex += direction;

          if (currentIndex >= slides.length) {
            currentIndex = 0;
          }

          if (currentIndex < 0) {
            currentIndex = slides.length - 1;
          }

          showSlide(currentIndex);
        };

        // ==========================================
        // DOT NAVIGATION
        // ==========================================

        window.currentSlide = function (index) {
          currentIndex = index - 1;

          showSlide(currentIndex);
        };
      });

/*INI AKHIR JS UNTUK STRUKTUR ORGANISASI*/
/* =====================================================
       IMAGE LIGHTBOX
    ===================================================== */

const lightbox = document.getElementById("lightbox");

const lightboxImage = document.getElementById("lightboxImage");

const organizationImage = document.getElementById("organizationImage");

const zoomButton = document.getElementById("zoomButton");

const imageContainer = document.getElementById("organizationImageContainer");

const lightboxClose = document.getElementById("lightboxClose");

function openLightbox() {
  lightboxImage.src = organizationImage.src;

  lightboxImage.alt = organizationImage.alt;

  lightbox.classList.add("active");

  lightbox.setAttribute("aria-hidden", "false");

  document.body.style.overflow = "hidden";
}

function closeLightbox() {
  lightbox.classList.remove("active");

  lightbox.setAttribute("aria-hidden", "true");

  document.body.style.overflow = "";
}

zoomButton.addEventListener("click", openLightbox);

imageContainer.addEventListener("click", openLightbox);

lightboxClose.addEventListener("click", closeLightbox);

lightbox.addEventListener("click", (event) => {
  if (event.target === lightbox) {
    closeLightbox();
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && lightbox.classList.contains("active")) {
    closeLightbox();
  }
});

/*INI AKHIR JS UNTUK STRUKTUR ORGANISASI*/
