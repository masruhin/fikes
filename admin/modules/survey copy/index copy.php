<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';

function slugify($text)
{
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-') ?: 'survey-' . time();
}
function redirect_ok($msg = 'Berhasil')
{
  header('Location: index.php?ok=' . rawurlencode($msg));
  exit;
}

$editSurvey = null;
if (isset($_GET['edit_survey'])) {
  $st = $pdo->prepare("SELECT * FROM survey WHERE id=?");
  $st->execute([(int)$_GET['edit_survey']]);
  $editSurvey = $st->fetch(PDO::FETCH_ASSOC);
}
$editQuestion = null;
if (isset($_GET['edit_question'])) {
  $st = $pdo->prepare("SELECT * FROM survey_pertanyaan WHERE id=?");
  $st->execute([(int)$_GET['edit_question']]);
  $editQuestion = $st->fetch(PDO::FETCH_ASSOC);
}
$editChoice = null;
if (isset($_GET['edit_choice'])) {
  $st = $pdo->prepare("SELECT * FROM survey_pilihan WHERE id=?");
  $st->execute([(int)$_GET['edit_choice']]);
  $editChoice = $st->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  try {
    if ($action === 'survey_save') {
      $id = (int)($_POST['id'] ?? 0);
      $judul = trim($_POST['judul'] ?? '');
      if (!$judul) throw new Exception('Judul survey wajib diisi.');
      $slug = slugify($_POST['slug'] ?? $judul);
      $tanggalMulai = $_POST['tanggal_mulai'] ?: null;
      $tanggalSelesai = $_POST['tanggal_selesai'] ?: null;
      if ($id) {
        $st = $pdo->prepare("UPDATE survey SET judul=?,slug=?,deskripsi=?,target_responden=?,tanggal_mulai=?,tanggal_selesai=?,status=?,nomor_urut=? WHERE id=?");
        $st->execute([$judul, $slug, $_POST['deskripsi'] ?? null, $_POST['target_responden'] ?? 'Umum', $tanggalMulai, $tanggalSelesai, $_POST['status'] ?? 'nonaktif', (int)($_POST['nomor_urut'] ?? 1), $id]);
        redirect_ok('Survey berhasil diperbarui.');
      }
      $st = $pdo->prepare("INSERT INTO survey(judul,slug,deskripsi,target_responden,tanggal_mulai,tanggal_selesai,status,nomor_urut) VALUES(?,?,?,?,?,?,?,?)");
      $st->execute([$judul, $slug, $_POST['deskripsi'] ?? null, $_POST['target_responden'] ?? 'Umum', $tanggalMulai, $tanggalSelesai, $_POST['status'] ?? 'nonaktif', (int)($_POST['nomor_urut'] ?? 1)]);
      redirect_ok('Survey berhasil ditambahkan.');
    }
    if ($action === 'survey_delete') {
      $id = (int)$_POST['id'];
      $pdo->beginTransaction();
      $q = $pdo->prepare("SELECT id FROM survey_pertanyaan WHERE survey_id=?");
      $q->execute([$id]);
      $ids = $q->fetchAll(PDO::FETCH_COLUMN);
      foreach ($ids as $pid) {
        $pdo->prepare("DELETE FROM survey_pilihan WHERE pertanyaan_id=?")->execute([$pid]);
      }
      $pdo->prepare("DELETE FROM survey_pertanyaan WHERE survey_id=?")->execute([$id]);
      $pdo->prepare("DELETE FROM survey_jawaban WHERE responden_id IN (SELECT id FROM survey_responden WHERE survey_id=?)")->execute([$id]);
      $pdo->prepare("DELETE FROM survey_responden WHERE survey_id=?")->execute([$id]);
      $pdo->prepare("DELETE FROM survey WHERE id=?")->execute([$id]);
      $pdo->commit();
      redirect_ok('Survey berhasil dihapus.');
    }
    if ($action === 'question_save') {
      $id = (int)($_POST['id'] ?? 0);
      $survey = (int)$_POST['survey_id'];
      if ($id) {
        $st = $pdo->prepare("UPDATE survey_pertanyaan SET survey_id=?,pertanyaan=?,tipe=?,wajib=?,nomor_urut=?,status=? WHERE id=?");
        $st->execute([$survey, $_POST['pertanyaan'], $_POST['tipe'], (int)($_POST['wajib'] ?? 0), (int)($_POST['nomor_urut'] ?? 1), $_POST['status'] ?? 'nonaktif', $id]);
        redirect_ok('Pertanyaan berhasil diperbarui.');
      }
      $st = $pdo->prepare("INSERT INTO survey_pertanyaan(survey_id,pertanyaan,tipe,wajib,nomor_urut,status) VALUES(?,?,?,?,?,?)");
      $st->execute([$survey, $_POST['pertanyaan'], $_POST['tipe'], (int)($_POST['wajib'] ?? 0), (int)($_POST['nomor_urut'] ?? 1), $_POST['status'] ?? 'aktif']);
      redirect_ok('Pertanyaan berhasil ditambahkan.');
    }
    if ($action === 'question_delete') {
      $id = (int)$_POST['id'];
      $pdo->prepare("DELETE FROM survey_pilihan WHERE pertanyaan_id=?")->execute([$id]);
      $pdo->prepare("DELETE FROM survey_jawaban WHERE pertanyaan_id=?")->execute([$id]);
      $pdo->prepare("DELETE FROM survey_pertanyaan WHERE id=?")->execute([$id]);
      redirect_ok('Pertanyaan berhasil dihapus.');
    }
    if ($action === 'choice_save') {
      $id = (int)($_POST['id'] ?? 0);
      $pid = (int)$_POST['pertanyaan_id'];
      if ($id) {
        $st = $pdo->prepare("UPDATE survey_pilihan SET pertanyaan_id=?,label=?,nilai=?,nomor_urut=? WHERE id=?");
        $st->execute([$pid, $_POST['label'], ($_POST['nilai'] === '' ? null : $_POST['nilai']), (int)($_POST['nomor_urut'] ?? 1), $id]);
        redirect_ok('Pilihan berhasil diperbarui.');
      }
      $st = $pdo->prepare("INSERT INTO survey_pilihan(pertanyaan_id,label,nilai,nomor_urut) VALUES(?,?,?,?)");
      $st->execute([$pid, $_POST['label'], ($_POST['nilai'] === '' ? null : $_POST['nilai']), (int)($_POST['nomor_urut'] ?? 1)]);
      redirect_ok('Pilihan berhasil ditambahkan.');
    }
    if ($action === 'choice_delete') {
      $pdo->prepare("DELETE FROM survey_pilihan WHERE id=?")->execute([(int)$_POST['id']]);
      redirect_ok('Pilihan berhasil dihapus.');
    }
  } catch (Throwable $ex) {
    header('Location: index.php?err=' . rawurlencode($ex->getMessage()));
    exit;
  }
}

$surveys = $pdo->query("SELECT s.*, (SELECT COUNT(*) FROM survey_pertanyaan p WHERE p.survey_id=s.id) jumlah_pertanyaan, (SELECT COUNT(*) FROM survey_responden r WHERE r.survey_id=s.id) jumlah_responden FROM survey s ORDER BY s.nomor_urut,s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$questions = $pdo->query("SELECT p.*,s.judul survey_judul FROM survey_pertanyaan p LEFT JOIN survey s ON s.id=p.survey_id ORDER BY s.nomor_urut,p.nomor_urut,p.id")->fetchAll(PDO::FETCH_ASSOC);
$choices = $pdo->query("SELECT c.*,p.pertanyaan,s.judul survey_judul FROM survey_pilihan c LEFT JOIN survey_pertanyaan p ON p.id=c.pertanyaan_id LEFT JOIN survey s ON s.id=p.survey_id ORDER BY s.nomor_urut,p.nomor_urut,c.nomor_urut,c.id")->fetchAll(PDO::FETCH_ASSOC);
$respondents = $pdo->query("SELECT r.*,s.judul survey_judul FROM survey_responden r LEFT JOIN survey s ON s.id=r.survey_id ORDER BY r.tanggal_isi DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
// Kelompokkan pertanyaan dan pilihan berdasarkan Survey agar pengelolaan lebih mudah.
$groupedQuestions = [];
foreach ($questions as $q) {
  $sid = (int)$q['survey_id'];
  if (!isset($groupedQuestions[$sid])) {
    $groupedQuestions[$sid] = ['survey_judul' => $q['survey_judul'], 'questions' => []];
  }
  $groupedQuestions[$sid]['questions'][$q['id']] = [
    'data' => $q,
    'choices' => []
  ];
}
foreach ($choices as $c) {
  $sid = (int)($c['survey_id'] ?? 0);
  $pid = (int)$c['pertanyaan_id'];
  if (isset($groupedQuestions[$sid]['questions'][$pid])) {
    $groupedQuestions[$sid]['questions'][$pid]['choices'][] = $c;
  }
}

$results = $pdo->query("SELECT p.id,p.pertanyaan,s.judul survey_judul,COUNT(j.id) total_jawaban,ROUND(AVG(j.nilai),2) rata_nilai FROM survey_pertanyaan p LEFT JOIN survey s ON s.id=p.survey_id LEFT JOIN survey_jawaban j ON j.pertanyaan_id=p.id GROUP BY p.id,p.pertanyaan,s.judul ORDER BY s.nomor_urut,p.nomor_urut")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';
?>
<style>
  .sv-wrap {
    max-width: 1500px;
    margin: auto
  }

  .sv-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px
  }

  .sv-head h1 {
    margin: 0
  }

  .sv-grid {
    display: grid;
    gap: 20px
  }

  .sv-panel {
    background: #fff;
    border: 1px solid #e7ece9;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(20, 60, 40, .05)
  }

  .sv-panel h2 {
    margin-top: 0;
    font-size: 18px
  }

  .sv-form {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px
  }

  .sv-form .full {
    grid-column: 1/-1
  }

  .sv-form label {
    font-weight: 700;
    font-size: 13px
  }

  .sv-form input,
  .sv-form select,
  .sv-form textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #dce5df;
    border-radius: 9px;
    margin-top: 6px
  }

  .sv-form textarea {
    min-height: 90px;
    resize: vertical
  }

  .sv-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px
  }

  .sv-btn {
    border: 0;
    border-radius: 9px;
    padding: 9px 13px;
    text-decoration: none;
    cursor: pointer;
    background: #166534;
    color: #fff
  }

  .sv-btn.gray {
    background: #64748b
  }

  .sv-btn.red {
    background: #dc2626
  }

  .sv-btn.blue {
    background: #2563eb
  }

  .sv-table-wrap {
    overflow-x: auto
  }

  .sv-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse
  }

  .sv-table th,
  .sv-table td {
    padding: 11px 10px;
    border-bottom: 1px solid #edf1ef;
    text-align: left;
    vertical-align: top
  }

  .sv-table th {
    background: #f4faf6;
    font-size: 12px
  }

  .sv-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 999px;
    background: #ecfdf3;
    font-size: 12px
  }

  .sv-note {
    color: #64748b;
    font-size: 13px
  }

  .sv-section {
    scroll-margin-top: 90px
  }

  .sv-edit {
    outline: 2px solid #86efac
  }

  @media(max-width:700px) {
    .sv-form {
      grid-template-columns: 1fr
    }

    .sv-form .full {
      grid-column: auto
    }

    .sv-head {
      align-items: flex-start;
      flex-direction: column
    }

    .sv-panel {
      padding: 15px
    }

    .sv-table {
      min-width: 720px
    }
  }

  .sv-group-list {
    display: grid;
    gap: 18px;
    margin-top: 18px
  }

  .sv-group-card {
    border: 1px solid #e4ebe7;
    border-radius: 15px;
    overflow: hidden;
    background: #fff
  }

  .sv-group-head {
    padding: 16px 18px;
    background: linear-gradient(135deg, #f1faf5, #f8fbf9);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    border-bottom: 1px solid #e2ebe6
  }

  .sv-group-kicker {
    font-size: 10px;
    letter-spacing: .12em;
    font-weight: 800;
    color: #0f766e
  }

  .sv-group-head h3 {
    margin: 4px 0 0;
    font-size: 16px;
    color: #123b2e
  }

  .sv-count {
    background: #fff;
    border: 1px solid #dce9e2;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 800;
    color: #456359;
    white-space: nowrap
  }

  .sv-question-list {
    padding: 4px 18px
  }

  .sv-question-item {
    padding: 16px 0;
    border-bottom: 1px solid #edf1ef
  }

  .sv-question-item:last-child {
    border-bottom: 0
  }

  .sv-question-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px
  }

  .sv-question-title {
    display: flex;
    gap: 11px;
    align-items: flex-start;
    min-width: 0
  }

  .sv-q-number {
    width: 28px;
    height: 28px;
    border-radius: 9px;
    background: #e9f7f0;
    color: #0f766e;
    display: grid;
    place-items: center;
    font-size: 12px;
    font-weight: 800;
    flex: 0 0 auto
  }

  .sv-question-title strong {
    display: block;
    color: #173d31;
    font-size: 14px;
    line-height: 1.55
  }

  .sv-question-actions {
    display: flex;
    gap: 6px;
    flex: 0 0 auto
  }

  .sv-choice-box {
    margin: 12px 0 0 39px;
    background: #fafcfb;
    border: 1px solid #e6eeea;
    border-radius: 12px;
    padding: 10px 12px
  }

  .sv-choice-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #647870;
    font-weight: 800;
    margin-bottom: 6px
  }

  .sv-choice-title span {
    display: inline-flex;
    margin-left: 5px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 999px;
    align-items: center;
    justify-content: center;
    background: #e7f5ee;
    color: #087f5b
  }

  .sv-choice-list {
    display: grid
  }

  .sv-choice-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px dashed #e2eae5;
    font-size: 13px
  }

  .sv-choice-row:last-child {
    border-bottom: 0
  }

  .sv-choice-row>div:first-child {
    display: flex;
    align-items: center;
    gap: 8px
  }

  .sv-choice-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16a34a;
    display: inline-block
  }

  .sv-choice-right {
    display: flex;
    align-items: center;
    gap: 6px
  }

  .sv-choice-right b {
    min-width: 35px;
    text-align: right;
    color: #475569;
    font-size: 12px
  }

  .sv-btn.mini {
    padding: 6px 8px;
    border-radius: 7px;
    font-size: 11px
  }

  .sv-no-choice {
    font-size: 12px;
    color: #94a3b8;
    padding: 7px 0
  }

  .sv-no-choice a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 700
  }

  .sv-empty {
    padding: 25px;
    text-align: center;
    border: 1px dashed #d7e3dc;
    border-radius: 12px;
    color: #64748b
  }

  .sv-choice-note {
    margin-top: 18px;
    background: #f6faf8;
    padding: 11px 13px;
    border-radius: 10px
  }

  .sv-choice-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 14px
  }

  .sv-summary-card {
    border: 1px solid #e4ebe7;
    border-radius: 13px;
    padding: 15px;
    background: #fff
  }

  .sv-summary-card h3 {
    font-size: 14px;
    color: #173d31;
    margin: 5px 0 8px
  }

  .sv-summary-card>div {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px
  }

  .sv-summary-card .sv-btn {
    font-size: 11px;
    padding: 7px 9px
  }

  @media(max-width:800px) {
    .sv-question-head {
      flex-direction: column
    }

    .sv-question-actions {
      width: 100%
    }

    .sv-choice-box {
      margin-left: 0
    }

    .sv-choice-summary {
      grid-template-columns: 1fr
    }

    .sv-group-head {
      align-items: flex-start;
      flex-direction: column
    }
  }
</style>
<div class="sv-wrap">
  <div class="sv-head">
    <div>
      <h1>Survey</h1>
      <div class="sv-note">Kelola survey, pertanyaan, pilihan, responden, dan hasil secara dinamis.</div>
    </div><a class="sv-btn" href="#survey">+ Survey Baru</a>
  </div>

  <section id="survey" class="sv-panel sv-section <?= $editSurvey ? 'sv-edit' : '' ?>">
    <h2><?= $editSurvey ? 'Edit Survey' : 'Daftar Survey' ?></h2>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="survey_save"><input type="hidden" name="id"
        value="<?= e($editSurvey['id'] ?? 0) ?>">
      <label>Judul<input name="judul" required value="<?= e($editSurvey['judul'] ?? '') ?>"></label>
      <label>Slug<input name="slug" value="<?= e($editSurvey['slug'] ?? '') ?>"
          placeholder="otomatis dari judul jika dikosongkan"></label>
      <label>Target Responden<select
          name="target_responden"><?php foreach (['Mahasiswa', 'Karyawan/Pegawai', 'Dosen', 'Alumni', 'Masyarakat', 'Umum'] as $v): ?>
            <option <?= ($editSurvey['target_responden'] ?? 'Umum') === $v ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label>Status<select name="status">
          <option value="aktif" <?= ($editSurvey['status'] ?? 'nonaktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
          <option value="nonaktif" <?= ($editSurvey['status'] ?? 'nonaktif') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select></label>
      <label>Mulai<input type="date" name="tanggal_mulai" value="<?= e($editSurvey['tanggal_mulai'] ?? '') ?>"></label>
      <label>Selesai<input type="date" name="tanggal_selesai"
          value="<?= e($editSurvey['tanggal_selesai'] ?? '') ?>"></label>
      <label>Nomor Urut<input type="number" name="nomor_urut" value="<?= e($editSurvey['nomor_urut'] ?? 1) ?>"></label>
      <label class="full">Deskripsi<textarea name="deskripsi"><?= e($editSurvey['deskripsi'] ?? '') ?></textarea></label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editSurvey ? 'Simpan Perubahan' : 'Tambah Survey' ?></button><?php if ($editSurvey): ?><a
            class="sv-btn gray" href="index.php#survey">Batal</a><?php endif; ?></div>
    </form>
    <div class="sv-table-wrap" style="margin-top:18px">
      <table class="sv-table">
        <thead>
          <tr>
            <th>Survey</th>
            <th>Target</th>
            <th>Periode</th>
            <th>Status</th>
            <th>Pertanyaan</th>
            <th>Responden</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($surveys as $s): ?><tr>
              <td><b><?= e($s['judul']) ?></b><br><span class="sv-note"><?= e($s['slug']) ?></span></td>
              <td><?= e($s['target_responden']) ?></td>
              <td><?= e($s['tanggal_mulai'] ?: '-') ?> s/d <?= e($s['tanggal_selesai'] ?: 'terbuka') ?></td>
              <td><span class="sv-badge"><?= e($s['status']) ?></span></td>
              <td><?= e($s['jumlah_pertanyaan']) ?></td>
              <td><?= e($s['jumlah_responden']) ?></td>
              <td><a class="sv-btn blue" href="?edit_survey=<?= $s['id'] ?>#survey">Edit</a> <button
                  class="sv-btn red sv-del" data-id="<?= $s['id'] ?>" data-action="survey_delete">Hapus</button></td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section id="pertanyaan" class="sv-panel sv-section <?= $editQuestion ? 'sv-edit' : '' ?>">
    <h2><?= $editQuestion ? 'Edit Pertanyaan' : 'Pertanyaan & Jawaban' ?></h2>
    <div class="sv-note">Data dikelompokkan berdasarkan <b>Survey → Pertanyaan → Pilihan Jawaban</b> agar tidak
      tercampur antar survey.</div>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="question_save"><input type="hidden" name="id"
        value="<?= e($editQuestion['id'] ?? 0) ?>">
      <label>Survey<select name="survey_id" required><?php foreach ($surveys as $s): ?><option value="<?= $s['id'] ?>"
              <?= ((int)($editQuestion['survey_id'] ?? 0) === $s['id']) ? 'selected' : '' ?>><?= e($s['judul']) ?></option>
          <?php endforeach; ?></select></label>
      <label>Tipe<select name="tipe"><?php foreach (['skala', 'pilihan_ganda', 'ya_tidak', 'isian', 'textarea'] as $v): ?>
            <option value="<?= $v ?>" <?= ($editQuestion['tipe'] ?? 'skala') === $v ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label class="full">Pertanyaan<textarea name="pertanyaan"
          required><?= e($editQuestion['pertanyaan'] ?? '') ?></textarea></label>
      <label>Wajib<select name="wajib">
          <option value="1" <?= ($editQuestion['wajib'] ?? 1) ? 'selected' : '' ?>>Ya</option>
          <option value="0" <?= isset($editQuestion['wajib']) && !$editQuestion['wajib'] ? 'selected' : '' ?>>Tidak</option>
        </select></label>
      <label>Nomor Urut<input type="number" name="nomor_urut" value="<?= e($editQuestion['nomor_urut'] ?? 1) ?>"></label>
      <label>Status<select name="status">
          <option value="aktif" <?= ($editQuestion['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
          <option value="nonaktif" <?= ($editQuestion['status'] ?? 'aktif') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select></label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editQuestion ? 'Simpan Perubahan' : 'Tambah Pertanyaan' ?></button><?php if ($editQuestion): ?><a
            class="sv-btn gray" href="index.php#pertanyaan">Batal</a><?php endif; ?></div>
    </form>
    <div class="sv-group-list">
      <?php if (!$groupedQuestions): ?>
        <div class="sv-empty">Belum ada pertanyaan.</div>
        <?php else: foreach ($groupedQuestions as $sid => $g): ?>
          <div class="sv-group-card">
            <div class="sv-group-head">
              <div><span class="sv-group-kicker">SURVEY</span>
                <h3><?= e($g['survey_judul']) ?></h3>
              </div>
              <span class="sv-count"><?= count($g['questions']) ?> Pertanyaan</span>
            </div>
            <div class="sv-question-list">
              <?php $no = 1;
              foreach ($g['questions'] as $pid => $item): $q = $item['data']; ?>
                <div class="sv-question-item">
                  <div class="sv-question-head">
                    <div class="sv-question-title"><span class="sv-q-number"><?= $no++ ?></span>
                      <div><strong><?= e($q['pertanyaan']) ?></strong>
                        <div class="sv-note"><?= e($q['tipe']) ?> · <?= ((int)$q['wajib']) ? 'Wajib' : 'Opsional' ?></div>
                      </div>
                    </div>
                    <div class="sv-question-actions"><a class="sv-btn blue"
                        href="?edit_question=<?= $q['id'] ?>#pertanyaan">Edit</a> <button class="sv-btn red sv-del"
                        data-id="<?= $q['id'] ?>" data-action="question_delete">Hapus</button></div>
                  </div>
                  <div class="sv-choice-box">
                    <div class="sv-choice-title">Pilihan Jawaban <span><?= count($item['choices']) ?></span></div>
                    <?php if ($item['choices']): ?>
                      <div class="sv-choice-list">
                        <?php foreach ($item['choices'] as $c): ?>
                          <div class="sv-choice-row">
                            <div><span class="sv-choice-dot"></span><span><?= e($c['label']) ?></span></div>
                            <div class="sv-choice-right"><b><?= e($c['nilai'] ?? '-') ?></b><a class="sv-btn blue mini"
                                href="?edit_choice=<?= $c['id'] ?>#pilihan">Edit</a><button class="sv-btn red mini sv-del"
                                data-id="<?= $c['id'] ?>" data-action="choice_delete">Hapus</button></div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <div class="sv-no-choice">Belum ada pilihan jawaban untuk pertanyaan ini. <a href="#pilihan">Tambah
                          pilihan</a></div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
      <?php endforeach;
      endif; ?>
    </div>
  </section>

  <section id="pilihan" class="sv-panel sv-section <?= $editChoice ? 'sv-edit' : '' ?>">
    <h2><?= $editChoice ? 'Edit Pilihan' : 'Pilihan Jawaban' ?></h2>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="choice_save"><input type="hidden" name="id"
        value="<?= e($editChoice['id'] ?? 0) ?>">
      <label>Pertanyaan<select name="pertanyaan_id" required><?php foreach ($questions as $q): ?><option
              value="<?= $q['id'] ?>" <?= ((int)($editChoice['pertanyaan_id'] ?? 0) === $q['id']) ? 'selected' : '' ?>>
              <?= e($q['survey_judul'] . ' — ' . $q['pertanyaan']) ?></option><?php endforeach; ?></select></label>
      <label>Label<input name="label" required value="<?= e($editChoice['label'] ?? '') ?>"></label>
      <label>Nilai<input type="number" step="0.01" name="nilai" value="<?= e($editChoice['nilai'] ?? '') ?>"
          placeholder="contoh 1-5"></label>
      <label>Nomor Urut<input type="number" name="nomor_urut" value="<?= e($editChoice['nomor_urut'] ?? 1) ?>"></label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editChoice ? 'Simpan Perubahan' : 'Tambah Pilihan' ?></button><?php if ($editChoice): ?><a
            class="sv-btn gray" href="index.php#pilihan">Batal</a><?php endif; ?></div>
    </form>
    <div class="sv-note sv-choice-note">Pilihan jawaban sekarang dikelompokkan langsung di bawah masing-masing
      pertanyaan dan survey pada bagian <b>Pertanyaan</b>. Gunakan form di atas untuk menambah atau mengedit pilihan.
    </div>
    <div class="sv-choice-summary">
      <?php foreach ($surveys as $s): ?>
        <?php
        $sid = (int)$s['id'];
        $jumlah = 0;
        if (isset($groupedQuestions[$sid])) foreach ($groupedQuestions[$sid]['questions'] as $it) $jumlah += count($it['choices']);
        ?>
        <div class="sv-summary-card">
          <span class="sv-group-kicker">SURVEY</span>
          <h3><?= e($s['judul']) ?></h3>
          <div><b><?= $jumlah ?></b> pilihan jawaban</div>
          <a class="sv-btn" href="#pertanyaan">Lihat berdasarkan pertanyaan →</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="responden" class="sv-panel sv-section">
    <h2>Responden Terbaru</h2>
    <div class="sv-table-wrap">
      <table class="sv-table">
        <thead>
          <tr>
            <th>Survey</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Kategori</th>
            <th>Identitas</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody><?php foreach ($respondents as $r): ?><tr>
              <td><?= e($r['survey_judul']) ?></td>
              <td><?= e($r['nama'] ?: '-') ?></td>
              <td><?= e($r['email'] ?: '-') ?></td>
              <td><?= e($r['kategori_responden'] ?: '-') ?></td>
              <td><?= e($r['identitas'] ?: '-') ?></td>
              <td><?= e($r['tanggal_isi']) ?></td>
            </tr><?php endforeach; ?></tbody>
      </table>
    </div>
  </section>

  <section id="hasil" class="sv-panel sv-section">
    <h2>Hasil Survey</h2>
    <div class="sv-table-wrap">
      <table class="sv-table">
        <thead>
          <tr>
            <th>Survey</th>
            <th>Pertanyaan</th>
            <th>Total Jawaban</th>
            <th>Rata-rata Nilai</th>
          </tr>
        </thead>
        <tbody><?php foreach ($results as $r): ?><tr>
              <td><?= e($r['survey_judul']) ?></td>
              <td><?= e($r['pertanyaan']) ?></td>
              <td><?= e($r['total_jawaban']) ?></td>
              <td><?= e($r['rata_nilai'] ?? '-') ?></td>
            </tr><?php endforeach; ?></tbody>
      </table>
    </div>
  </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.querySelectorAll('.sv-del').forEach(btn => {
    btn.addEventListener('click', () => {
      Swal.fire({
        title: 'Hapus data?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
      }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.createElement('form');
        f.method = 'post';
        f.innerHTML = '<input name="action" value="' + btn.dataset.action + '"><input name="id" value="' + btn
          .dataset.id + '">';
        document.body.appendChild(f);
        f.submit();
      });
    });
  });
  const p = new URLSearchParams(location.search);
  if (p.get('ok')) Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: p.get('ok'),
    timer: 1700,
    showConfirmButton: false
  });
  if (p.get('err')) Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: p.get('err')
  });
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
