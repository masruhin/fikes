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

              const parent = this.parentElement;

              parent.classList.toggle("open");
            }
          });
        });

      /* =========================================================
   FILTER
========================================================= */

      const filterButtons = document.querySelectorAll(".filter-button");

      const certificateCards = document.querySelectorAll(".certificate-card");

      const noResult = document.getElementById("noResult");

      filterButtons.forEach((button) => {
        button.addEventListener("click", () => {
          filterButtons.forEach((item) => {
            item.classList.remove("active");
          });

          button.classList.add("active");

          const filter = button.dataset.filter;

          let visibleCount = 0;

          certificateCards.forEach((card) => {
            const category = card.dataset.category;

            if (filter === "all" || category === filter) {
              card.style.display = "block";

              visibleCount++;
            } else {
              card.style.display = "none";
            }
          });

          if (visibleCount === 0) {
            noResult.style.display = "block";
          } else {
            noResult.style.display = "none";
          }
        });
      });

      /* =========================================================
   LIGHTBOX
========================================================= */

      const lightbox = document.getElementById("lightbox");

      const lightboxImage = document.getElementById("lightboxImage");

      const lightboxClose = document.getElementById("lightboxClose");

      document.querySelectorAll(".certificate-preview").forEach((preview) => {
        preview.addEventListener("click", () => {
          const image = preview.querySelector("img");

          lightboxImage.src = image.src;

          lightboxImage.alt = image.alt;

          lightbox.classList.add("active");

          document.body.style.overflow = "hidden";
        });
      });

      function closeLightbox() {
        lightbox.classList.remove("active");

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
   YEAR
========================================================= */

      document.getElementById("year").textContent = new Date().getFullYear();

      /* =========================================================
   CLOSE MOBILE MENU
========================================================= */

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

      async function downloadPDF(url, filename) {
        try {
          const response = await fetch(url);

          if (!response.ok) {
            throw new Error("File PDF tidak ditemukan");
          }

          const blob = await response.blob();

          const blobUrl = window.URL.createObjectURL(blob);

          const link = document.createElement("a");

          link.href = blobUrl;

          link.download = filename;

          document.body.appendChild(link);

          link.click();

          link.remove();

          window.URL.revokeObjectURL(blobUrl);
        } catch (error) {
          alert("Maaf, file PDF tidak dapat diunduh.");

          console.error(error);
        }
      }
