      /* =========================================================
   NAVBAR SCROLL EFFECT
========================================================= */

      /* NAVBAR SCROLL */

      const navbar = document.getElementById("navbar");

      window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
          navbar.classList.add("scrolled");
        } else {
          navbar.classList.remove("scrolled");
        }
      });

      /* MOBILE MENU */

      const menuToggle = document.getElementById("menuToggle");

      const navMenu = document.getElementById("navMenu");

      menuToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");

        menuToggle.innerHTML = navMenu.classList.contains("active")
          ? "✕"
          : "☰";
      });

      /* MOBILE DROPDOWN */

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

      /* CLOSE MOBILE MENU */

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

      /* BACK TO TOP */

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

      /* YEAR */

      document.getElementById("year").textContent = new Date().getFullYear();

      /* CLOSE DROPDOWN */

      document.addEventListener("click", (event) => {
        if (!event.target.closest(".navbar")) {
          document.querySelectorAll(".nav-item.open").forEach((item) => {
            item.classList.remove("open");
          });
        }
      });
