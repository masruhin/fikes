      /* =====================================================
   ELEMENT
===================================================== */

      const dosenGrid = document.getElementById("dosenGrid");

      const emptyState = document.getElementById("emptyState");

      const searchInput = document.getElementById("searchInput");

      const filterButtons = document.querySelectorAll(".filter-button");

      let currentFilter = "all";

      let currentSearch = "";

      /* =====================================================
   RENDER DOSEN
===================================================== */

      function renderDosen() {
        dosenGrid.innerHTML = "";

        const hasil = dataDosen.filter((dosen) => {
          const cocokKategori =
            currentFilter === "all" || dosen.kategori === currentFilter;

          const keyword = currentSearch.toLowerCase().trim();

          const cocokSearch =
            !keyword ||
            dosen.nama.toLowerCase().includes(keyword) ||
            dosen.program.toLowerCase().includes(keyword) ||
            dosen.keahlian.toLowerCase().includes(keyword);

          return cocokKategori && cocokSearch;
        });

        /* =================================================
       EMPTY STATE
    ================================================= */

        if (hasil.length === 0) {
          emptyState.style.display = "block";

          return;
        }

        emptyState.style.display = "none";

        /* =================================================
       CARD
    ================================================= */

        hasil.forEach((dosen, index) => {
          const card = document.createElement("article");

          card.className = "dosen-card";

          card.style.animationDelay = `${index * 0.05}s`;

          card.innerHTML = `

                <div
                    class="dosen-photo"
                >

                    <span
                        class="program-badge"
                    >
                        ${namaKategori(dosen.kategori)}
                    </span>

                    <img
                        src="${dosen.foto}"
                        alt="${dosen.nama}"
                        loading="lazy"
                    >

                </div>


                <div
                    class="dosen-content"
                >

                    <h3>
                        ${dosen.nama}
                    </h3>


                    <div
                        class="dosen-position"
                    >
                        ${dosen.jabatan}
                    </div>


                    <div
                        class="dosen-info"
                    >

                        <div
                            class="info-row"
                        >

                            <strong>
                                Program
                            </strong>

                            <span>
                                ${dosen.program}
                            </span>

                        </div>


                        <div
                            class="info-row"
                        >

                            <strong>
                                Keahlian
                            </strong>

                            <span>
                                ${dosen.keahlian}
                            </span>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="profile-button"
                        onclick="lihatProfil(${dosen.id})"
                    >
                        Lihat Profil →
                    </button>

                </div>

            `;

          dosenGrid.appendChild(card);
        });
      }

      /* =====================================================
   NAMA KATEGORI
===================================================== */

      function namaKategori(kategori) {
        const kategoriMap = {
          keperawatan: "Keperawatan",

          kebidanan: "Kebidanan",

          farmasi: "Farmasi",

          k3: "K3",
        };

        return kategoriMap[kategori] || kategori;
      }

      /* =====================================================
   FILTER
===================================================== */

      filterButtons.forEach((button) => {
        button.addEventListener("click", function () {
          filterButtons.forEach((item) => item.classList.remove("active"));

          this.classList.add("active");

          currentFilter = this.dataset.filter;

          renderDosen();
        });
      });

      /* =====================================================
   SEARCH
===================================================== */

      searchInput.addEventListener("input", function () {
        currentSearch = this.value;

        renderDosen();
      });

      /* =====================================================
   MODAL
===================================================== */

      const modal = document.getElementById("profileModal");

      const modalClose = document.getElementById("modalClose");

      function lihatProfil(id) {
        const dosen = dataDosen.find((item) => item.id === id);

        if (!dosen) {
          return;
        }

        document.getElementById("modalPhoto").src = dosen.foto;

        document.getElementById("modalPhoto").alt = dosen.nama;

        document.getElementById("modalProgram").textContent = dosen.program;

        document.getElementById("modalName").textContent = dosen.nama;

        document.getElementById("modalPosition").textContent = dosen.jabatan;

        document.getElementById("modalNidn").textContent = dosen.nidn;

        document.getElementById("modalEducation").textContent =
          dosen.pendidikan;

        document.getElementById("modalExpertise").textContent = dosen.keahlian;

        document.getElementById("modalEmail").textContent = dosen.email;

        document.getElementById("modalDescription").textContent =
          dosen.deskripsi;

        modal.classList.add("active");

        document.body.style.overflow = "hidden";
      }

      /* =====================================================
   CLOSE MODAL
===================================================== */

      function tutupModal() {
        modal.classList.remove("active");

        document.body.style.overflow = "";
      }

      modalClose.addEventListener("click", tutupModal);

      modal.addEventListener("click", function (event) {
        if (event.target === modal) {
          tutupModal();
        }
      });

      document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
          tutupModal();
        }
      });

      /* =====================================================
   MOBILE MENU
===================================================== */

      const menuToggle = document.getElementById("menuToggle");

      const navMenu = document.getElementById("navMenu");

      menuToggle.addEventListener("click", function () {
        navMenu.classList.toggle("active");

        if (navMenu.classList.contains("active")) {
          navMenu.style.display = "flex";

          navMenu.style.flexDirection = "column";

          navMenu.style.position = "absolute";

          navMenu.style.top = "100%";

          navMenu.style.left = "0";

          navMenu.style.right = "0";

          navMenu.style.background = "white";

          navMenu.style.padding = "20px";
        } else {
          navMenu.style.display = "";
        }
      });

      /* =====================================================
   YEAR
===================================================== */

      document.getElementById("year").textContent = new Date().getFullYear();

      /* =====================================================
   INITIAL RENDER
===================================================== */

      renderDosen();
