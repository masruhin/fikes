
      /* =====================================================
       NAVBAR SCROLL
    ===================================================== */

      const navbar = document.getElementById("navbar");

      window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
          navbar.classList.add("scrolled");
        } else {
          navbar.classList.remove("scrolled");
        }
      });

      /* =====================================================
       MOBILE MENU
    ===================================================== */

      const menuToggle = document.getElementById("menuToggle");

      const navMenu = document.getElementById("navMenu");

      menuToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");

        menuToggle.innerHTML = navMenu.classList.contains("active")
          ? "✕"
          : "☰";
      });

      /* =====================================================
       MOBILE DROPDOWN
    ===================================================== */

      document
        .querySelectorAll(
          ".has-dropdown > .nav-link, " + ".has-dropdown > .dropdown-link",
        )
        .forEach((link) => {
          link.addEventListener("click", function (event) {
            if (window.innerWidth <= 900) {
              event.preventDefault();

              const parent = this.parentElement;

              parent.classList.toggle("open");
            }
          });
        });

      /* =====================================================
       CLOSE MOBILE MENU
    ===================================================== */

      document.querySelectorAll(".nav-menu a").forEach((link) => {
        link.addEventListener("click", function () {
          if (
            window.innerWidth <= 900 &&
            !this.parentElement.classList.contains("has-dropdown")
          ) {
            navMenu.classList.remove("active");

            menuToggle.innerHTML = "☰";
          }
        });
      });

      /* =====================================================
       IMAGE LIGHTBOX
    ===================================================== */

      const lightbox = document.getElementById("lightbox");

      const lightboxImage = document.getElementById("lightboxImage");

      const organizationImage = document.getElementById("organizationImage");

      const zoomButton = document.getElementById("zoomButton");

      const imageContainer = document.getElementById(
        "organizationImageContainer",
      );

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

      /* =====================================================
       BACK TO TOP
    ===================================================== */

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

      /* =====================================================
       CURRENT YEAR
    ===================================================== */

      document.getElementById("year").textContent = new Date().getFullYear();

      /* =====================================================
       CLOSE DROPDOWN OUTSIDE NAVBAR
    ===================================================== */

      document.addEventListener("click", (event) => {
        if (!event.target.closest(".navbar")) {
          document.querySelectorAll(".nav-item.open").forEach((item) => {
            item.classList.remove("open");
          });
        }
      });
