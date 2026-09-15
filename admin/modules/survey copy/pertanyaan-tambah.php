<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';
function tanggal_id($v)
{
  if (!$v) return '-';
  $b = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  $t = strtotime($v);
  return $t ? date('d', $t) . ' ' . $b[(int)date('m', $t) - 1] . ' ' . date('Y', $t) : $v;
}

$survey_id = (int)($_GET['survey_id'] ?? $_POST['survey_id'] ?? 0);

if ($survey_id < 1) {
  header('Location: index.php');
  exit;
}


$stmt = $pdo->prepare(
  "SELECT * FROM survey WHERE id = ? LIMIT 1"
);

$stmt->execute([$survey_id]);

$survey = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$survey) {
  header('Location: index.php');
  exit;
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try {

    $pertanyaan = trim(
      $_POST['pertanyaan'] ?? ''
    );

    $tipe = $_POST['tipe_jawaban'] ?? '';

    $wajib = isset($_POST['wajib']) ? 1 : 0;

    $opsi = $_POST['opsi'] ?? [];

    $allowedTypes = [
      'pilihan_ganda',
      'ya_tidak',
      'isian',
      'textarea',
      'rating'
    ];

    if ($pertanyaan === '') {
      throw new Exception(
        'Pertanyaan wajib diisi.'
      );
    }

    if (!in_array($tipe, $allowedTypes, true)) {
      throw new Exception(
        'Tipe jawaban tidak valid.'
      );
    }


    /*
         * Tentukan nomor urut otomatis
         */
    $stmt = $pdo->prepare("
            SELECT COALESCE(MAX(urutan), 0) + 1
            FROM survey_pertanyaan
            WHERE survey_id = ?
        ");

    $stmt->execute([$survey_id]);

    $urutan = (int)$stmt->fetchColumn();


    $pdo->beginTransaction();


    /*
         * Simpan pertanyaan
         */
    $stmt = $pdo->prepare("
            INSERT INTO survey_pertanyaan
            (
                survey_id,
                pertanyaan,
                tipe_jawaban,
                wajib,
                urutan
            )
            VALUES (?, ?, ?, ?, ?)
        ");

    $stmt->execute([
      $survey_id,
      $pertanyaan,
      $tipe,
      $wajib,
      $urutan
    ]);

    $pertanyaan_id = (int)$pdo->lastInsertId();


    /*
         * PILIHAN GANDA
         */
    if ($tipe === 'pilihan_ganda') {

      $urutanOpsi = 1;

      foreach ($opsi as $key => $item) {

        $teks = trim($item['teks'] ?? '');
        $nilai = $item['nilai'] ?? null;

        if ($teks === '') {
          continue;
        }

        $nilai = ($nilai !== '')
          ? (float)$nilai
          : null;

        $stmt = $pdo->prepare("
                    INSERT INTO survey_opsi
                    (
                        pertanyaan_id,
                        opsi,
                        nilai,
                        urutan
                    )
                    VALUES (?, ?, ?, ?)
                ");

        $stmt->execute([
          $pertanyaan_id,
          $teks,
          $nilai,
          $urutanOpsi
        ]);

        $urutanOpsi++;
      }

      if ($urutanOpsi === 1) {
        throw new Exception(
          'Pilihan ganda minimal memiliki satu opsi.'
        );
      }
    }


    /*
         * YA / TIDAK
         * dibuat otomatis
         */
    if ($tipe === 'ya_tidak') {

      $defaultOptions = [
        ['Ya', 1],
        ['Tidak', 0]
      ];

      $urutanOpsi = 1;

      foreach ($defaultOptions as $item) {

        $stmt = $pdo->prepare("
                    INSERT INTO survey_opsi
                    (
                        pertanyaan_id,
                        opsi,
                        nilai,
                        urutan
                    )
                    VALUES (?, ?, ?, ?)
                ");

        $stmt->execute([
          $pertanyaan_id,
          $item[0],
          $item[1],
          $urutanOpsi
        ]);

        $urutanOpsi++;
      }
    }


    /*
         * RATING
         */
    if ($tipe === 'rating') {

      for ($i = 1; $i <= 5; $i++) {

        $stmt = $pdo->prepare("
                    INSERT INTO survey_opsi
                    (
                        pertanyaan_id,
                        opsi,
                        nilai,
                        urutan
                    )
                    VALUES (?, ?, ?, ?)
                ");

        $stmt->execute([
          $pertanyaan_id,
          'Rating ' . $i,
          $i,
          $i
        ]);
      }
    }


    $pdo->commit();


    header(
      'Location: pertanyaan.php?id=' .
        $survey_id .
        '&ok=' .
        rawurlencode(
          'Pertanyaan berhasil ditambahkan.'
        )
    );

    exit;
  } catch (Throwable $e) {

    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }

    $error = $e->getMessage();
  }
}


$page_title = 'Tambah Pertanyaan';

include __DIR__ . '/../../includes/header.php';

?>

<link rel="stylesheet" href="style.css">

<div class="survey-wrap">

  <div class="survey-page-head">

    <div>

      <a href="pertanyaan.php?id=<?= $survey_id ?>" class="back-link">
        ← Kembali ke Pertanyaan
      </a>

      <div class="survey-label">
        <?= e($survey['judul']) ?>
      </div>

      <h1>
        Tambah Pertanyaan
      </h1>

    </div>

  </div>


  <?php if ($error): ?>

  <div class="alert-error">
    <?= e($error) ?>
  </div>

  <?php endif; ?>


  <div class="form-panel">

    <form method="post" id="questionForm">

      <input type="hidden" name="survey_id" value="<?= $survey_id ?>">


      <div class="form-group full">

        <label>
          Pertanyaan <span>*</span>
        </label>

        <textarea name="pertanyaan" rows="4" required
          placeholder="Contoh: Apakah Anda puas dengan pelayanan FIKES?"><?= e($_POST['pertanyaan'] ?? '') ?></textarea>

      </div>


      <div class="form-grid">

        <div class="form-group">

          <label>
            Tipe Jawaban <span>*</span>
          </label>

          <select name="tipe_jawaban" id="tipeJawaban" required>

            <option value="">
              -- Pilih Tipe Jawaban --
            </option>

            <option value="pilihan_ganda">
              Pilihan Ganda
            </option>

            <option value="ya_tidak">
              Ya / Tidak
            </option>

            <option value="isian">
              Isian Singkat
            </option>

            <option value="textarea">
              Text Area
            </option>

            <option value="rating">
              Rating 1–5
            </option>

          </select>

        </div>


        <div class="form-group">

          <label>
            Status Pertanyaan
          </label>

          <label class="switch-row">

            <input type="checkbox" name="wajib" checked>

            <span>
              Pertanyaan wajib dijawab
            </span>

          </label>

        </div>

      </div>


      <!-- PILIHAN GANDA -->

      <div id="panelPilihanGanda" class="dynamic-panel" style="display:none">

        <div class="dynamic-title">

          <div>

            <h3>
              Pilihan Jawaban
            </h3>

            <p>
              Tambahkan pilihan yang dapat dipilih
              oleh responden.
            </p>

          </div>

          <button type="button" class="btn-secondary" id="btnTambahOpsi">
            ＋ Tambah Pilihan
          </button>

        </div>


        <div id="optionContainer">

          <div class="option-row">

            <input type="text" name="opsi[0][teks]" placeholder="Contoh: Sangat Tidak Puas">

            <input type="number" step="0.01" name="opsi[0][nilai]" placeholder="Nilai">

            <button type="button" class="remove-option">
              ×
            </button>

          </div>


          <div class="option-row">

            <input type="text" name="opsi[1][teks]" placeholder="Contoh: Tidak Puas">

            <input type="number" step="0.01" name="opsi[1][nilai]" placeholder="Nilai">

            <button type="button" class="remove-option">
              ×
            </button>

          </div>


          <div class="option-row">

            <input type="text" name="opsi[2][teks]" placeholder="Contoh: Cukup">

            <input type="number" step="0.01" name="opsi[2][nilai]" placeholder="Nilai">

            <button type="button" class="remove-option">
              ×
            </button>

          </div>


          <div class="option-row">

            <input type="text" name="opsi[3][teks]" placeholder="Contoh: Puas">

            <input type="number" step="0.01" name="opsi[3][nilai]" placeholder="Nilai">

            <button type="button" class="remove-option">
              ×
            </button>

          </div>


          <div class="option-row">

            <input type="text" name="opsi[4][teks]" placeholder="Contoh: Sangat Puas">

            <input type="number" step="0.01" name="opsi[4][nilai]" placeholder="Nilai">

            <button type="button" class="remove-option">
              ×
            </button>

          </div>

        </div>

      </div>


      <!-- YA TIDAK -->

      <div id="panelYaTidak" class="dynamic-panel info-panel" style="display:none">

        <div class="info-icon">
          ✓
        </div>

        <div>

          <strong>
            Jawaban otomatis
          </strong>

          <p>
            Sistem akan menyediakan pilihan:
            <b>Ya</b> dan <b>Tidak</b>.
          </p>

        </div>

      </div>


      <!-- ISIAN -->

      <div id="panelIsian" class="dynamic-panel info-panel" style="display:none">

        <div class="info-icon">
          Aa
        </div>

        <div>

          <strong>
            Isian Singkat
          </strong>

          <p>
            Responden akan mendapatkan satu
            kolom input teks.
          </p>

        </div>

      </div>


      <!-- TEXTAREA -->

      <div id="panelTextarea" class="dynamic-panel info-panel" style="display:none">

        <div class="info-icon">
          ¶
        </div>

        <div>

          <strong>
            Text Area
          </strong>

          <p>
            Cocok digunakan untuk kritik,
            saran, atau jawaban panjang.
          </p>

        </div>

      </div>


      <!-- RATING -->

      <div id="panelRating" class="dynamic-panel info-panel" style="display:none">

        <div class="info-icon">
          ★
        </div>

        <div>

          <strong>
            Rating 1–5
          </strong>

          <p>
            Sistem otomatis membuat rating
            dari 1 sampai 5.
          </p>

        </div>

      </div>


      <div class="form-actions">

        <a href="pertanyaan.php?id=<?= $survey_id ?>" class="btn-secondary">
          Batal
        </a>

        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-floppy-disk"></i>
          Simpan Pertanyaan
        </button>

      </div>

    </form>

  </div>

</div>


<script>
const tipeJawaban =
  document.getElementById('tipeJawaban');

const panels = {

  pilihan_ganda: document.getElementById('panelPilihanGanda'),

  ya_tidak: document.getElementById('panelYaTidak'),

  isian: document.getElementById('panelIsian'),

  textarea: document.getElementById('panelTextarea'),

  rating: document.getElementById('panelRating')

};


function updateAnswerPanel() {

  Object.values(panels).forEach(function(panel) {

    panel.style.display = 'none';

  });


  const selected =
    tipeJawaban.value;

  if (panels[selected]) {

    panels[selected].style.display = 'block';

  }

}


tipeJawaban.addEventListener(
  'change',
  updateAnswerPanel
);


let optionIndex = 5;


document
  .getElementById('btnTambahOpsi')
  .addEventListener('click', function() {

    const container =
      document.getElementById('optionContainer');

    const row =
      document.createElement('div');

    row.className =
      'option-row';

    row.innerHTML = `

            <input
                type="text"
                name="opsi[${optionIndex}][teks]"
                placeholder="Teks pilihan"
            >

            <input
                type="number"
                step="0.01"
                name="opsi[${optionIndex}][nilai]"
                placeholder="Nilai"
            >

            <button
                type="button"
                class="remove-option"
            >
                ×
            </button>

        `;

    container.appendChild(row);

    optionIndex++;

  });


document
  .getElementById('optionContainer')
  .addEventListener('click', function(e) {

    if (
      e.target.classList.contains(
        'remove-option'
      )
    ) {

      const rows =
        document.querySelectorAll(
          '.option-row'
        );

      if (rows.length <= 1) {

        return;

      }

      e.target
        .closest('.option-row')
        .remove();

    }

  });


updateAnswerPanel();
</script>


<?php include __DIR__ . '/../../includes/footer.php'; ?>
