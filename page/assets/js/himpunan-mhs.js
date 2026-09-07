
      /* =========================================================
   NAVBAR SCROLL
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
          ? "✕"
          : "☰";
      });

      /* =========================================================
   MOBILE DROPDOWN
========================================================= */

      document
        .querySelectorAll(
          ".has-dropdown > .nav-link, " + ".has-dropdown > .dropdown-link",
        )
        .forEach((link) => {
          link.addEventListener("click", function (event) {
            if (window.innerWidth <= 900) {
              event.preventDefault();

              this.parentElement.classList.toggle("open");
            }
          });
        });

      /* =========================================================
   LIGHTBOX
========================================================= */

      const lightbox = document.getElementById("lightbox");

      const lightboxImage = document.getElementById("lightboxImage");

      const lightboxClose = document.getElementById("lightboxClose");

      function previewLogo(image, title) {
        lightboxImage.src = image;

        lightboxImage.alt = title;

        lightbox.classList.add("active");

        document.body.style.overflow = "hidden";
      }

      function closeLightbox() {
        lightbox.classList.remove("active");

        lightboxImage.src = "";

        document.body.style.overflow = "";
      }

      lightboxClose.addEventListener("click", closeLightbox);

      lightbox.addEventListener("click", (event) => {
        if (event.target === lightbox) {
          closeLightbox();
        }
      });

      document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
          closeLightbox();
        }
      });

      /* =========================================================
   YEAR
========================================================= */

      document.getElementById("year").textContent = new Date().getFullYear();

