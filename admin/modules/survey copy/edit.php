<?php

require_once __DIR__ . '/../config/auth.php';
wajib_login();

require_once __DIR__ . '/../config/database.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id < 1) {
  header('Location: index.php?err=' . rawurlencode('Survey tidak ditemukan.'));
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM survey WHERE id = ? LIMIT 1");
$stmt->execute([$id]);

$survey = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$survey) {
  header('Location: index.php?err=' . rawurlencode('Survey tidak ditemukan.'));
  exit;
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

    if (
      !in_array(
        $status,
        ['draft', 'aktif', 'nonaktif'],
        true
      )
    ) {
      throw new Exception('Status survey tidak valid.');
    }

    if (
      $tanggal_mulai &&
      $tanggal_selesai &&
      $tanggal_selesai < $tanggal_mulai
    ) {
      throw new Exception(
        'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
      );
    }

    $stmt = $pdo->prepare("
            UPDATE survey
            SET
                judul = ?,
                deskripsi = ?,
                status = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?
            WHERE id = ?
        ");

    $stmt->execute([
      $judul,
      $deskripsi !== '' ? $deskripsi : null,
      $status,
      $tanggal_mulai ?: null,
      $tanggal_selesai ?: null,
      $id
    ]);

    header(
      'Location: index.php?ok=' .
        rawurlencode('Survey berhasil diperbarui.')
    );

    exit;
  } catch (Throwable $e) {

    $error = $e->getMessage();

    $survey['judul'] = $_POST['judul'] ?? $survey['judul'];
    $survey['deskripsi'] = $_POST['deskripsi'] ?? $survey['deskripsi'];
    $survey['status'] = $_POST['status'] ?? $survey['status'];
    $survey['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? $survey['tanggal_mulai'];
    $survey['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? $survey['tanggal_selesai'];
  }
}

$page_title = 'Edit Survey';

include __DIR__ . '/../includes/header.php';

?>

<link rel="stylesheet" href="style.css">

<div class="survey-wrap">

  <div class="survey-page-head">

    <div>

      <a href="index.php" class="back-link">
        ← Kembali ke Survey
      </a>

      <h1>Edit Survey</h1>

      <p>
        Perbarui informasi survey.
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

      <input type="hidden" name="id" value="<?= $id ?>">


      <div class="form-group full">

        <label>
          Judul Survey <span>*</span>
        </label>

        <input type="text" name="judul" required maxlength="255" value="<?= e($survey['judul']) ?>">

      </div>


      <div class="form-group full">

        <label>
          Deskripsi
        </label>

        <textarea name="deskripsi" rows="5"><?= e($survey['deskripsi']) ?></textarea>

      </div>


      <div class="form-grid">

        <div class="form-group">

          <label>
            Status
          </label>

          <select name="status">

            <?php foreach (
              [
                'draft' => 'Draft',
                'aktif' => 'Aktif',
                'nonaktif' => 'Nonaktif'
              ] as $value => $label
            ): ?>

              <option value="<?= $value ?>" <?= $survey['status'] === $value
                                              ? 'selected'
                                              : '' ?>>
                <?= $label ?>
              </option>

            <?php endforeach; ?>

          </select>

        </div>


        <div class="form-group">

          <label>
            Tanggal Mulai
          </label>

          <input type="datetime-local" name="tanggal_mulai" value="<?= e(
                                                                      $survey['tanggal_mulai']
                                                                        ? date(
                                                                          'Y-m-d\TH:i',
                                                                          strtotime($survey['tanggal_mulai'])
                                                                        )
                                                                        : ''
                                                                    ) ?>">

        </div>


        <div class="form-group">

          <label>
            Tanggal Selesai
          </label>

          <input type="datetime-local" name="tanggal_selesai" value="<?= e(
                                                                        $survey['tanggal_selesai']
                                                                          ? date(
                                                                            'Y-m-d\TH:i',
                                                                            strtotime($survey['tanggal_selesai'])
                                                                          )
                                                                          : ''
                                                                      ) ?>">

        </div>

      </div>


      <div class="form-actions">

        <a href="index.php" class="btn-secondary">
          Batal
        </a>

        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-floppy-disk"></i>
          Simpan Perubahan
        </button>

      </div>

    </form>

  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
