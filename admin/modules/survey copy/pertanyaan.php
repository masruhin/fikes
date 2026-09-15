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
$survey_id = (int)($_GET['id'] ?? 0);

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
  header(
    'Location: index.php?err=' .
      rawurlencode('Survey tidak ditemukan.')
  );
  exit;
}


$stmt = $pdo->prepare("
    SELECT *
    FROM survey_pertanyaan
    WHERE survey_id = ?
    ORDER BY urutan ASC, id ASC
");

$stmt->execute([$survey_id]);

$pertanyaan = $stmt->fetchAll(PDO::FETCH_ASSOC);


$page_title = 'Pertanyaan Survey';

include __DIR__ . '/../../includes/header.php';

?>

<link rel="stylesheet" href="style.css">

<div class="survey-wrap">

  <div class="survey-page-head">

    <div>

      <a href="index.php" class="back-link">
        ← Kembali ke Daftar Survey
      </a>

      <div class="survey-label">
        KELOLA PERTANYAAN
      </div>

      <h1>
        <?= e($survey['judul']) ?>
      </h1>

      <p>
        <?= e($survey['deskripsi']) ?>
      </p>

    </div>


    <a href="pertanyaan-tambah.php?survey_id=<?= $survey_id ?>" class="btn-primary">
      <i class="fa-solid fa-plus"></i>
      Tambah Pertanyaan
    </a>

  </div>


  <div class="question-info">

    <div>
      <strong>
        <?= count($pertanyaan) ?>
      </strong>
      <span>Total Pertanyaan</span>
    </div>

    <div>
      <strong>
        <?= e(ucfirst($survey['status'])) ?>
      </strong>
      <span>Status Survey</span>
    </div>

  </div>


  <div class="question-list">

    <?php if (!$pertanyaan): ?>

      <div class="empty-survey">

        <div class="empty-icon">
          ❓
        </div>

        <h3>
          Belum Ada Pertanyaan
        </h3>

        <p>
          Tambahkan pertanyaan pertama untuk survey ini.
        </p>

        <a href="pertanyaan-tambah.php?survey_id=<?= $survey_id ?>" class="btn-primary">
          ＋ Tambah Pertanyaan
        </a>

      </div>

    <?php endif; ?>


    <?php foreach ($pertanyaan as $index => $p): ?>

      <article class="question-card">

        <div class="question-number">
          <?= $index + 1 ?>
        </div>


        <div class="question-content">

          <div class="question-meta">

            <?php

            $types = [
              'pilihan_ganda' => 'Pilihan Ganda',
              'ya_tidak' => 'Ya / Tidak',
              'isian' => 'Isian Singkat',
              'textarea' => 'Text Area',
              'rating' => 'Rating 1–5'
            ];

            ?>

            <span class="type-badge">
              <?= e(
                $types[$p['tipe_jawaban']]
                  ?? $p['tipe_jawaban']
              ) ?>
            </span>


            <?php if ($p['wajib']): ?>

              <span class="required-badge">
                Wajib
              </span>

            <?php else: ?>

              <span class="optional-badge">
                Opsional
              </span>

            <?php endif; ?>

          </div>


          <h3>
            <?= e($p['pertanyaan']) ?>
          </h3>


          <?php if (
            in_array(
              $p['tipe_jawaban'],
              ['pilihan_ganda', 'ya_tidak'],
              true
            )
          ): ?>

            <?php

            $op = $pdo->prepare("
                            SELECT *
                            FROM survey_opsi
                            WHERE pertanyaan_id = ?
                            ORDER BY urutan ASC, id ASC
                        ");

            $op->execute([$p['id']]);

            $opsi = $op->fetchAll(PDO::FETCH_ASSOC);

            ?>


            <div class="option-preview">

              <?php foreach ($opsi as $o): ?>

                <span>
                  ○ <?= e($o['opsi']) ?>
                </span>

              <?php endforeach; ?>

            </div>

          <?php elseif ($p['tipe_jawaban'] === 'isian'): ?>

            <div class="input-preview">
              Isian singkat
            </div>

          <?php elseif ($p['tipe_jawaban'] === 'textarea'): ?>

            <div class="textarea-preview">
              Area jawaban panjang
            </div>

          <?php elseif ($p['tipe_jawaban'] === 'rating'): ?>

            <div class="rating-preview">
              ☆ ☆ ☆ ☆ ☆
            </div>

          <?php endif; ?>

        </div>


        <div class="question-actions">

          <a href="pertanyaan-edit.php?id=<?= (int)$p['id'] ?>" class="btn-action" title="Edit">
            <i class="fa-solid fa-pen"></i>
          </a>


          <form action="pertanyaan-hapus.php" method="post" class="delete-question">

            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

            <input type="hidden" name="survey_id" value="<?= $survey_id ?>">

            <button type="submit" class="btn-action danger" title="Hapus">
              <i class="fa-solid fa-trash"></i>
            </button>

          </form>

        </div>

      </article>

    <?php endforeach; ?>

  </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.querySelectorAll('.delete-question').forEach(function(form) {

    form.addEventListener('submit', function(e) {

      e.preventDefault();

      Swal.fire({
        icon: 'warning',
        title: 'Hapus Pertanyaan?',
        text: 'Pertanyaan dan pilihan jawabannya akan dihapus.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        confirmButtonColor: '#dc3545'
      }).then(function(result) {

        if (result.isConfirmed) {

          form.submit();

        }

      });

    });

  });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
