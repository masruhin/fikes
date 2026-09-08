
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

        /* =====================================================
       PAGINATION
    ====================================================== */

        const tableBody = document.getElementById("documentTableBody");

        const allRows = Array.from(tableBody.querySelectorAll("tr"));

        const search = document.getElementById("documentSearch");

        const program = document.getElementById("programFilter");

        const semester = document.getElementById("semesterFilter");

        const year = document.getElementById("yearFilter");

        const emptyState = document.getElementById("emptyState");

        const resultInfo = document.getElementById("resultInfo");

        const pagination = document.getElementById("pagination");

        const pageNumbers = document.getElementById("pageNumbers");

        const prevPage = document.getElementById("prevPage");

        const nextPage = document.getElementById("nextPage");

        let currentPage = 1;

        const rowsPerPage = 5;

        let filteredRows = [...allRows];

        /* =====================================================
       FILTER
    ====================================================== */

        function applyFilter() {
          const searchValue = search ? search.value.toLowerCase().trim() : "";

          const programValue = program ? program.value : "";

          const semesterValue = semester ? semester.value : "";

          const yearValue = year ? year.value : "";

          filteredRows = allRows.filter(function (row) {
            const text = row.innerText.toLowerCase();

            const rowProgram = row.dataset.program || "";

            const rowSemester = row.dataset.semester || "";

            const rowYear = row.dataset.year || "";

            const matchSearch = !searchValue || text.includes(searchValue);

            const matchProgram = !programValue || rowProgram === programValue;

            const matchSemester =
              !semesterValue || rowSemester === semesterValue;

            const matchYear = !yearValue || rowYear === yearValue;

            return matchSearch && matchProgram && matchSemester && matchYear;
          });

          currentPage = 1;

          renderTable();
        }

        /* =====================================================
       RENDER TABLE
    ====================================================== */

        function renderTable() {
          allRows.forEach(function (row) {
            row.style.display = "none";
          });

          if (filteredRows.length === 0) {
            if (emptyState) {
              emptyState.style.display = "block";
            }

            if (pagination) {
              pagination.style.display = "none";
            }

            if (resultInfo) {
              resultInfo.textContent = "Tidak ada dokumen ditemukan";
            }

            return;
          }

          if (emptyState) {
            emptyState.style.display = "none";
          }

          if (pagination) {
            pagination.style.display = "flex";
          }

          const totalRows = filteredRows.length;

          const totalPages = Math.ceil(totalRows / rowsPerPage);

          if (currentPage > totalPages) {
            currentPage = totalPages;
          }

          const start = (currentPage - 1) * rowsPerPage;

          const end = Math.min(start + rowsPerPage, totalRows);

          const currentRows = filteredRows.slice(start, end);

          currentRows.forEach(function (row) {
            row.style.display = "";
          });

          /* =================================================
           NOMOR URUT
        ================================================== */

          currentRows.forEach(function (row, index) {
            const numberCell = row.querySelector(".number");

            if (numberCell) {
              numberCell.textContent = start + index + 1;
            }
          });

          /* =================================================
           RESULT INFO
        ================================================== */

          if (resultInfo) {
            resultInfo.textContent = `Menampilkan ${start + 1}–${end} dari ${totalRows} dokumen`;
          }

          renderPagination(totalPages);
        }

        /* =====================================================
       RENDER PAGINATION BUTTON
    ====================================================== */

        function renderPagination(totalPages) {
          if (!pageNumbers) {
            return;
          }

          pageNumbers.innerHTML = "";

          /* PREVIOUS */

          if (prevPage) {
            prevPage.disabled = currentPage === 1;
          }

          /* =================================================
           PAGE NUMBERS
        ================================================== */

          let pages = [];

          if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
              pages.push(i);
            }
          } else {
            pages.push(1);

            if (currentPage > 4) {
              pages.push("...");
            }

            const start = Math.max(2, currentPage - 1);

            const end = Math.min(totalPages - 1, currentPage + 1);

            for (let i = start; i <= end; i++) {
              pages.push(i);
            }

            if (currentPage < totalPages - 3) {
              pages.push("...");
            }

            pages.push(totalPages);
          }

          pages.forEach(function (page) {
            if (page === "...") {
              const dots = document.createElement("span");

              dots.className = "pagination-dots";

              dots.textContent = "...";

              pageNumbers.appendChild(dots);

              return;
            }

            const button = document.createElement("button");

            button.type = "button";

            button.className = "page-number";

            button.textContent = page;

            if (page === currentPage) {
              button.classList.add("active");
            }

            button.addEventListener("click", function () {
              currentPage = page;

              renderTable();

              scrollToTable();
            });

            pageNumbers.appendChild(button);
          });

          /* NEXT */

          if (nextPage) {
            nextPage.disabled = currentPage === totalPages;
          }
        }

        /* =====================================================
       PREVIOUS
    ====================================================== */

        if (prevPage) {
          prevPage.addEventListener("click", function () {
            if (currentPage > 1) {
              currentPage--;

              renderTable();

              scrollToTable();
            }
          });
        }

        /* =====================================================
       NEXT
    ====================================================== */

        if (nextPage) {
          nextPage.addEventListener("click", function () {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

            if (currentPage < totalPages) {
              currentPage++;

              renderTable();

              scrollToTable();
            }
          });
        }

        /* =====================================================
       SCROLL TO TABLE
    ====================================================== */

        function scrollToTable() {
          const panel = document.querySelector(".document-panel");

          if (panel) {
            const top =
              panel.getBoundingClientRect().top + window.scrollY - 100;

            window.scrollTo({
              top: top,
              behavior: "smooth",
            });
          }
        }

        /* =====================================================
       FILTER EVENTS
    ====================================================== */

        if (search) {
          search.addEventListener("input", applyFilter);
        }

        if (program) {
          program.addEventListener("change", applyFilter);
        }

        if (semester) {
          semester.addEventListener("change", applyFilter);
        }

        if (year) {
          year.addEventListener("change", applyFilter);
        }

        /* =====================================================
       BACK TO TOP
    ====================================================== */

        const backTop = document.getElementById("backTop");

        window.addEventListener("scroll", function () {
          if (!backTop) {
            return;
          }

          if (window.scrollY > 400) {
            backTop.classList.add("show");
          } else {
            backTop.classList.remove("show");
          }


        if (backTop) {
          backTop.addEventListener("click", function () {
            window.scrollTo({
              top: 0,
              behavior: "smooth",
            });
          });
        }

        /* =====================================================
       INITIALIZE
    ====================================================== */

        renderTable();
      });
