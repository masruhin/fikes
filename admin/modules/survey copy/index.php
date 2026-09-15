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
$page_title = 'Survey';

$survey = $pdo->query("
    SELECT
        s.*,
        COUNT(DISTINCT sp.id) AS jumlah_pertanyaan,
        COUNT(DISTINCT sr.id) AS jumlah_responden
    FROM survey s
    LEFT JOIN survey_pertanyaan sp
        ON sp.survey_id = s.id
    LEFT JOIN survey_responden sr
        ON sr.survey_id = s.id
    GROUP BY s.id
    ORDER BY s.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';

$ok  = $_GET['ok'] ?? '';
$err = $_GET['err'] ?? '';

?>

<link rel="stylesheet" href="style.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="survey-wrap">

  <div class="survey-header">

    <div>
      <div class="survey-label">
        SISTEM SURVEY
      </div>

      <h1>Survey</h1>

      <p>
        Kelola berbagai survey dan pertanyaan penilaian
        melalui Dashboard Admin.
      </p>
    </div>

    <a href="tambah.php" class="btn-primary">
      <span>＋</span>
      Tambah Survey
    </a>

  </div>


  <?php if ($ok): ?>

    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: <?= json_encode($ok) ?>,
        confirmButtonColor: '#008f72'
      });
    </script>

  <?php endif; ?>


  <?php if ($err): ?>

    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: <?= json_encode($err) ?>,
        confirmButtonColor: '#d33'
      });
    </script>

  <?php endif; ?>


  <div class="survey-grid">

    <?php if (!$survey): ?>

      <div class="empty-survey">

        <div class="empty-icon">
          📋
        </div>

        <h3>Belum Ada Survey</h3>

        <p>
          Silakan buat survey pertama Anda.
        </p>

        <a href="tambah.php" class="btn-primary">
          ＋ Buat Survey
        </a>

      </div>

    <?php endif; ?>


    <?php foreach ($survey as $s): ?>

      <article class="survey-card">

        <div class="survey-card-top">

          <?php
          $statusClass = match ($s['status']) {
            'aktif' => 'status-active',
            'nonaktif' => 'status-off',
            default => 'status-draft'
          };
          ?>

          <span class="survey-status <?= $statusClass ?>">
            <?= e(ucfirst($s['status'])) ?>
          </span>

        </div>


        <div class="survey-icon">
          <i class="fa-solid fa-clipboard-question"></i>
        </div>


        <h2>
          <?= e($s['judul']) ?>
        </h2>


        <p class="survey-description">

          <?= e(
            strlen($s['deskripsi'] ?? '') > 140
              ? substr($s['deskripsi'], 0, 140) . '...'
              : ($s['deskripsi'] ?? '')
          ) ?>

        </p>


        <div class="survey-stat">

          <div>
            <strong>
              <?= (int)$s['jumlah_pertanyaan'] ?>
            </strong>

            <span>Pertanyaan</span>
          </div>

          <div>
            <strong>
              <?= (int)$s['jumlah_responden'] ?>
            </strong>

            <span>Responden</span>
          </div>

        </div>


        <div class="survey-actions">

          <a href="pertanyaan.php?id=<?= (int)$s['id'] ?>" class="btn-action primary">
            <i class="fa-solid fa-list-check"></i>
            Pertanyaan
          </a>

          <a href="edit.php?id=<?= (int)$s['id'] ?>" class="btn-action">
            <i class="fa-solid fa-pen"></i>
            Edit
          </a>

          <form action="hapus.php" method="post" class="delete-form" data-title="<?= e($s['judul']) ?>">

            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">

            <button type="submit" class="btn-action danger">
              <i class="fa-solid fa-trash"></i>
              Hapus
            </button>

          </form>

        </div>

      </article>

    <?php endforeach; ?>

  </div>

</div>


<script>
  document.querySelectorAll('.delete-form').forEach(function(form) {

    form.addEventListener('submit', function(e) {

      e.preventDefault();

      const title = form.dataset.title;

      Swal.fire({
        icon: 'warning',
        title: 'Hapus Survey?',
        text: 'Survey "' + title + '" beserta pertanyaan dan data terkait akan dihapus.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
      }).then(function(result) {

        if (result.isConfirmed) {

          form.submit();

        }

      });

    });

  });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
