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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try {

    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

    if ($judul === '') {
      throw new Exception('Judul survey wajib diisi.');
    }

    $allowedStatus = [
      'draft',
      'aktif',
      'nonaktif'
    ];

    if (!in_array($status, $allowedStatus, true)) {
      throw new Exception('Status survey tidak valid.');
    }

    $stmt = $pdo->prepare("
            INSERT INTO survey
            (
                judul,
                deskripsi,
                status,
                tanggal_mulai,
                tanggal_selesai
            )
            VALUES (?, ?, ?, ?, ?)
        ");

    $stmt->execute([
      $judul,
      $deskripsi !== '' ? $deskripsi : null,
      $status,
      $tanggal_mulai ?: null,
      $tanggal_selesai ?: null
    ]);

    header(
      'Location: index.php?ok=' .
        rawurlencode('Survey berhasil dibuat.')
    );

    exit;
  } catch (Throwable $e) {

    $error = $e->getMessage();
  }
}

$page_title = 'Tambah Survey';

include __DIR__ . '/../../includes/header.php';

?>

<link rel="stylesheet" href="style.css">

<div class="survey-wrap">

  <div class="survey-page-head">

    <div>

      <a href="index.php" class="back-link">
        ← Kembali ke Survey
      </a>

      <h1>Tambah Survey</h1>

      <p>
        Buat survey baru yang nantinya dapat memiliki
        banyak pertanyaan dengan tipe jawaban berbeda.
      </p>

    </div>

  </div>


  <?php if ($error): ?>

    <div class="alert-error">
      <?= e($error) ?>
    </div>

  <?php endif; ?>


  <div class="form-panel">

    <form method="post">

      <div class="form-group full">

        <label>
          Judul Survey
          <span>*</span>
        </label>

        <input type="text" name="judul" required maxlength="255" placeholder="Contoh: Penilaian Pelayanan FIKES"
          value="<?= e($_POST['judul'] ?? '') ?>">

      </div>


      <div class="form-group full">

        <label>
          Deskripsi
        </label>

        <textarea name="deskripsi" rows="5"
          placeholder="Jelaskan tujuan survey..."><?= e($_POST['deskripsi'] ?? '') ?></textarea>

      </div>


      <div class="form-grid">

        <div class="form-group">

          <label>
            Status
          </label>

          <select name="status">

            <option value="draft" <?= ($_POST['status'] ?? '') === 'draft'
                                    ? 'selected'
                                    : '' ?>>
              Draft
            </option>

            <option value="aktif" <?= ($_POST['status'] ?? '') === 'aktif'
                                    ? 'selected'
                                    : '' ?>>
              Aktif
            </option>

            <option value="nonaktif" <?= ($_POST['status'] ?? '') === 'nonaktif'
                                        ? 'selected'
                                        : '' ?>>
              Nonaktif
            </option>

          </select>

        </div>


        <div class="form-group">

          <label>
            Tanggal Mulai
          </label>

          <input type="datetime-local" name="tanggal_mulai" value="<?= e($_POST['tanggal_mulai'] ?? '') ?>">

        </div>


        <div class="form-group">

          <label>
            Tanggal Selesai
          </label>

          <input type="datetime-local" name="tanggal_selesai" value="<?= e($_POST['tanggal_selesai'] ?? '') ?>">

        </div>

      </div>


      <div class="form-actions">

        <a href="index.php" class="btn-secondary">
          Batal
        </a>

        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-floppy-disk"></i>
          Simpan Survey
        </button>

      </div>

    </form>

  </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
