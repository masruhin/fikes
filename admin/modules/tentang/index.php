<?php
// Komponen reusable Navbar FIKES.
// Variabel $base_path dapat diatur dari halaman pemanggil.
// Default: '..' untuk halaman yang berada di dalam folder page/.
$base_path = $base_path ?? '..';
?>
<!-- =========================================================
     NAVBAR
========================================================= -->

<header class="navbar" id="navbar">
  <div class="container nav-inner">
    <!-- LOGO -->

    <a href="<?= $base_path ?>/tentang-fikes/index.php" class="logo">
      <div class="logo-icon">F</div>

      <div class="logo-text">
        <strong>FIKES</strong>
        <small>FAKULTAS ILMU KESEHATAN</small>
      </div>
    </a>

    <!-- MOBILE BUTTON -->

    <button class="menu-toggle" id="menuToggle">☰</button>

    <!-- NAVIGATION -->

    <nav class="nav-menu" id="navMenu">
      <!-- TENTANG FIKES -->

      <div class="nav-item has-dropdown">
        <a href="<?= $base_path ?>/tentang-fikes/visi-misi.php" class="nav-link">
          Tentang FIKES
          <span class="arrow">▾</span>
        </a>

        <div class="dropdown">
          <div class="dropdown-item">
            <a href="<?= $base_path ?>/tentang-fikes/visi-misi.php" class="dropdown-link"> Visi Misi </a>
          </div>

          <div class="dropdown-item">
            <a href="<?= $base_path ?>/tentang-fikes/struktur-organisasi.php" class="dropdown-link">
              Struktur Organisasi
            </a>
          </div>

          <div class="dropdown-item">
            <a href="<?= $base_path ?>/tentang-fikes/sertifikat-akreditasi.php" class="dropdown-link">
              Sertifikat Akreditasi
            </a>
          </div>

          <div class="dropdown-item">
            <a href="<?= $base_path ?>/tentang-fikes/unduh-logo.php" class="dropdown-link"> Unduh Logo </a>
          </div>

          <!-- DAFTAR DOSEN -->

          <div class="dropdown-item has-dropdown">
            <a href="#" class="dropdown-link">
              Daftar Dosen
              <span>›</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="<?= $base_path ?>/page/tentang-fikes/dosen.php" class="dropdown-link"> Keperawatan </a>
              </div>

              <div class="dropdown-item">
                <a href="#" class="dropdown-link"> Kebidanan </a>
              </div>

              <div class="dropdown-item">
                <a href="#" class="dropdown-link"> Farmasi </a>
              </div>

              <div class="dropdown-item">
                <a href="#" class="dropdown-link"> K3 </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KEMAHASISWAAN -->

      <div class="nav-item has-dropdown">
        <a href="#" class="nav-link">
          Kemahasiswaan
          <span class="arrow">▾</span>
        </a>

        <div class="dropdown">
          <div class="dropdown-item">
            <a href="<?= $base_path ?>/page/tentang-fikes/himpunan-mahasiswa.php" class="dropdown-link">
              Himpunan Mahasiswa
            </a>
          </div>

          <div class="dropdown-item">
            <a href="<?= $base_path ?>/page/tentang-fikes/unit-kegiatan-mahasiswa.php" class="dropdown-link">
              Unit Kegiatan Mahasiswa
            </a>
          </div>
        </div>
      </div>

      <!-- PROGRAM VOKASI -->

      <div class="nav-item has-dropdown">
        <a href="#" class="nav-link">
          Program Vokasi
          <span class="arrow">▾</span>
        </a>

        <div class="dropdown">
          <!-- PROGRAM PROFESI -->

          <div class="dropdown-item has-dropdown">
            <a href="#" class="dropdown-link">
              Program Profesi
              <span>›</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="<?= $base_path ?>/page/tentang-fikes/profesi-ners.php" class="dropdown-link">
                  Profesi Ners
                </a>
              </div>
            </div>
          </div>

          <!-- PROGRAM SARJANA -->

          <div class="dropdown-item has-dropdown">
            <a href="#" class="dropdown-link">
              Program Sarjana
              <span>›</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="<?= $base_path ?>/page/tentang-fikes/programsarjana-keperawatan.php" class="dropdown-link">
                  Ilmu Keperawatan (S.Kep)
                </a>
              </div>

              <div class="dropdown-item">
                <a href="<?= $base_path ?>/page/tentang-fikes/programsarjana-farmasi.php" class="dropdown-link">
                  Farmasi (S.Farm)
                </a>
              </div>
            </div>
          </div>

          <!-- PROGRAM DIPLOMA -->

          <div class="dropdown-item has-dropdown">
            <a href="#" class="dropdown-link">
              Program Diploma
              <span>›</span>
            </a>

            <div class="dropdown">
              <div class="dropdown-item">
                <a href="#" class="dropdown-link">
                  Keperawatan (A.Md.Kep.)
                </a>
              </div>

              <div class="dropdown-item">
                <a href="#" class="dropdown-link">
                  Kebidanan (A.Md.Keb.)
                </a>
              </div>

              <div class="dropdown-item">
                <a href="#" class="dropdown-link">
                  Keselamatan dan Kesehatan Kerja (S.Tr.KKK.)
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- AKADEMIK -->

      <div class="nav-item">
        <a href="#akademik" class="nav-link"> Akademik </a>
      </div>

      <!-- PELAYANAN -->

      <div class="nav-item">
        <a href="#pelayanan" class="nav-link"> Pelayanan FIKES </a>
      </div>

      <!-- SURVEY -->

      <div class="nav-item">
        <a href="#survey" class="nav-link"> Survey </a>
      </div>
    </nav>

    <a href="#program" class="nav-cta"> Jelajahi Program </a>
  </div>
</header>
