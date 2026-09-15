<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';

function survey_slug($text){
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'survey-' . time();
}
function survey_redirect($msg, $kind='ok', $anchor='survey'){
    header('Location: index.php?' . $kind . '=' . rawurlencode($msg) . '#' . $anchor);
    exit;
}
function survey_default_choices($tipe){
    if($tipe === 'skala') return [
        ['Sangat Tidak Puas',1], ['Tidak Puas',2], ['Cukup',3], ['Puas',4], ['Sangat Puas',5]
    ];
    if($tipe === 'ya_tidak') return [['Ya',1],['Tidak',0]];
    return [];
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    try{
        if($action === 'survey_save'){
            $id=(int)($_POST['id']??0);
            $judul=trim($_POST['judul']??'');
            if($judul==='') throw new Exception('Judul survey wajib diisi.');
            $slug=survey_slug($_POST['slug']??$judul);
            $mulai=$_POST['tanggal_mulai']!=='' ? $_POST['tanggal_mulai'] : null;
            $selesai=$_POST['tanggal_selesai']!=='' ? $_POST['tanggal_selesai'] : null;
            $target=trim($_POST['target_responden']??'Umum') ?: 'Umum';
            $status=in_array($_POST['status']??'nonaktif',['aktif','nonaktif'],true)?$_POST['status']:'nonaktif';
            $urut=max(1,(int)($_POST['nomor_urut']??1));
            if($id){
                $st=$pdo->prepare("UPDATE survey SET judul=?,slug=?,deskripsi=?,target_responden=?,tanggal_mulai=?,tanggal_selesai=?,status=?,nomor_urut=? WHERE id=?");
                $st->execute([$judul,$slug,trim($_POST['deskripsi']??''),$target,$mulai,$selesai,$status,$urut,$id]);
                survey_redirect('Survey berhasil diperbarui.','ok','survey');
            }
            $st=$pdo->prepare("INSERT INTO survey(judul,slug,deskripsi,target_responden,tanggal_mulai,tanggal_selesai,status,nomor_urut) VALUES(?,?,?,?,?,?,?,?)");
            $st->execute([$judul,$slug,trim($_POST['deskripsi']??''),$target,$mulai,$selesai,$status,$urut]);
            survey_redirect('Survey berhasil ditambahkan.','ok','survey');
        }

        if($action === 'survey_delete'){
            $id=(int)($_POST['id']??0);
            if(!$id) throw new Exception('ID survey tidak valid.');
            $pdo->beginTransaction();
            $q=$pdo->prepare('SELECT id FROM survey_pertanyaan WHERE survey_id=?'); $q->execute([$id]);
            $pids=$q->fetchAll(PDO::FETCH_COLUMN);
            foreach($pids as $pid){
                $pdo->prepare('DELETE FROM survey_pilihan WHERE pertanyaan_id=?')->execute([(int)$pid]);
                $pdo->prepare('DELETE FROM survey_jawaban WHERE pertanyaan_id=?')->execute([(int)$pid]);
            }
            $pdo->prepare('DELETE FROM survey_pertanyaan WHERE survey_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM survey_jawaban WHERE responden_id IN (SELECT id FROM survey_responden WHERE survey_id=?)')->execute([$id]);
            $pdo->prepare('DELETE FROM survey_responden WHERE survey_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM survey WHERE id=?')->execute([$id]);
            $pdo->commit();
            survey_redirect('Survey, pertanyaan, pilihan, dan responden terkait berhasil dihapus.','ok','survey');
        }

        if($action === 'question_save'){
            $id=(int)($_POST['id']??0);
            $surveyId=(int)($_POST['survey_id']??0);
            $pertanyaan=trim($_POST['pertanyaan']??'');
            $tipe=$_POST['tipe']??'skala';
            if(!$surveyId || $pertanyaan==='') throw new Exception('Survey dan pertanyaan wajib diisi.');
            if(!in_array($tipe,['skala','pilihan_ganda','ya_tidak','isian','textarea'],true)) throw new Exception('Tipe pertanyaan tidak valid.');
            $wajib=isset($_POST['wajib']) ? 1 : 0;
            $urut=max(1,(int)($_POST['nomor_urut']??1));
            $status=in_array($_POST['status']??'aktif',['aktif','nonaktif'],true)?$_POST['status']:'aktif';
            if($id){
                $st=$pdo->prepare('UPDATE survey_pertanyaan SET survey_id=?,pertanyaan=?,tipe=?,wajib=?,nomor_urut=?,status=? WHERE id=?');
                $st->execute([$surveyId,$pertanyaan,$tipe,$wajib,$urut,$status,$id]);
            }else{
                $st=$pdo->prepare('INSERT INTO survey_pertanyaan(survey_id,pertanyaan,tipe,wajib,nomor_urut,status) VALUES(?,?,?,?,?,?)');
                $st->execute([$surveyId,$pertanyaan,$tipe,$wajib,$urut,$status]);
                $id=(int)$pdo->lastInsertId();
            }
            $count=$pdo->prepare('SELECT COUNT(*) FROM survey_pilihan WHERE pertanyaan_id=?'); $count->execute([$id]);
            $choiceCount=(int)$count->fetchColumn();
            if($choiceCount===0){
                foreach(survey_default_choices($tipe) as $i=>$c){
                    $pdo->prepare('INSERT INTO survey_pilihan(pertanyaan_id,label,nilai,nomor_urut) VALUES(?,?,?,?)')->execute([$id,$c[0],$c[1],$i+1]);
                }
            }
            survey_redirect('Pertanyaan berhasil disimpan.','ok','pertanyaan');
        }

        if($action === 'question_delete'){
            $id=(int)($_POST['id']??0); if(!$id) throw new Exception('ID pertanyaan tidak valid.');
            $pdo->prepare('DELETE FROM survey_pilihan WHERE pertanyaan_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM survey_jawaban WHERE pertanyaan_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM survey_pertanyaan WHERE id=?')->execute([$id]);
            survey_redirect('Pertanyaan berhasil dihapus.','ok','pertanyaan');
        }

        if($action === 'choice_save'){
            $id=(int)($_POST['id']??0); $pid=(int)($_POST['pertanyaan_id']??0); $label=trim($_POST['label']??'');
            if(!$pid || $label==='') throw new Exception('Pertanyaan dan label jawaban wajib diisi.');
            $nilai=$_POST['nilai']!=='' ? $_POST['nilai'] : null;
            $urut=max(1,(int)($_POST['nomor_urut']??1));
            if($id){
                $st=$pdo->prepare('UPDATE survey_pilihan SET pertanyaan_id=?,label=?,nilai=?,nomor_urut=? WHERE id=?'); $st->execute([$pid,$label,$nilai,$urut,$id]);
            }else{
                $st=$pdo->prepare('INSERT INTO survey_pilihan(pertanyaan_id,label,nilai,nomor_urut) VALUES(?,?,?,?)'); $st->execute([$pid,$label,$nilai,$urut]);
            }
            survey_redirect('Pilihan jawaban berhasil disimpan.','ok','pilihan');
        }

        if($action === 'choice_delete'){
            $id=(int)($_POST['id']??0); if(!$id) throw new Exception('ID pilihan tidak valid.');
            $pdo->prepare('DELETE FROM survey_pilihan WHERE id=?')->execute([$id]);
            survey_redirect('Pilihan jawaban berhasil dihapus.','ok','pilihan');
        }
    }catch(Throwable $ex){
        if($pdo->inTransaction()) $pdo->rollBack();
        survey_redirect($ex->getMessage(),'err', $action==='choice_save'||$action==='choice_delete'?'pilihan':($action==='question_save'||$action==='question_delete'?'pertanyaan':'survey'));
    }
}

$editSurvey=null; $editQuestion=null; $editChoice=null;
if(isset($_GET['edit_survey'])){ $st=$pdo->prepare('SELECT * FROM survey WHERE id=?');$st->execute([(int)$_GET['edit_survey']]);$editSurvey=$st->fetch(PDO::FETCH_ASSOC); }
if(isset($_GET['edit_question'])){ $st=$pdo->prepare('SELECT * FROM survey_pertanyaan WHERE id=?');$st->execute([(int)$_GET['edit_question']]);$editQuestion=$st->fetch(PDO::FETCH_ASSOC); }
if(isset($_GET['edit_choice'])){ $st=$pdo->prepare('SELECT * FROM survey_pilihan WHERE id=?');$st->execute([(int)$_GET['edit_choice']]);$editChoice=$st->fetch(PDO::FETCH_ASSOC); }

$surveys=$pdo->query("SELECT s.*,(SELECT COUNT(*) FROM survey_pertanyaan p WHERE p.survey_id=s.id) jumlah_pertanyaan,(SELECT COUNT(*) FROM survey_responden r WHERE r.survey_id=s.id) jumlah_responden FROM survey s ORDER BY s.nomor_urut,s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$questions=$pdo->query("SELECT p.*,s.judul survey_judul FROM survey_pertanyaan p LEFT JOIN survey s ON s.id=p.survey_id ORDER BY s.nomor_urut,p.nomor_urut,p.id")->fetchAll(PDO::FETCH_ASSOC);
$choices=$pdo->query("SELECT c.*,p.pertanyaan,p.tipe,s.judul survey_judul FROM survey_pilihan c LEFT JOIN survey_pertanyaan p ON p.id=c.pertanyaan_id LEFT JOIN survey s ON s.id=p.survey_id ORDER BY s.nomor_urut,p.nomor_urut,c.nomor_urut,c.id")->fetchAll(PDO::FETCH_ASSOC);
$respondents=$pdo->query("SELECT r.*,s.judul survey_judul FROM survey_responden r LEFT JOIN survey s ON s.id=r.survey_id ORDER BY r.tanggal_isi DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);

$chartSurvey=$pdo->query("SELECT s.id,s.judul,COUNT(r.id) jumlah FROM survey s LEFT JOIN survey_responden r ON r.survey_id=s.id GROUP BY s.id,s.judul ORDER BY s.nomor_urut,s.id")->fetchAll(PDO::FETCH_ASSOC);
$chartCategory=$pdo->query("SELECT COALESCE(NULLIF(kategori_responden,''),'Tidak diisi') kategori,COUNT(*) jumlah FROM survey_responden GROUP BY COALESCE(NULLIF(kategori_responden,''),'Tidak diisi') ORDER BY jumlah DESC")->fetchAll(PDO::FETCH_ASSOC);
$chartQuestions=$pdo->query("SELECT p.id,p.survey_id,p.pertanyaan,p.tipe,p.nomor_urut,ROUND(AVG(j.nilai),2) rata_nilai FROM survey_pertanyaan p LEFT JOIN survey_jawaban j ON j.pertanyaan_id=p.id WHERE p.status='aktif' GROUP BY p.id,p.survey_id,p.pertanyaan,p.tipe,p.nomor_urut ORDER BY p.survey_id,p.nomor_urut")->fetchAll(PDO::FETCH_ASSOC);
$chartChoiceRows=$pdo->query("SELECT c.pertanyaan_id,c.id,c.label,c.nilai,COUNT(j.id) jumlah FROM survey_pilihan c LEFT JOIN survey_jawaban j ON j.pilihan_id=c.id GROUP BY c.pertanyaan_id,c.id,c.label,c.nilai ORDER BY c.pertanyaan_id,c.nomor_urut,c.id")->fetchAll(PDO::FETCH_ASSOC);
$choiceMap=[]; foreach($chartChoiceRows as $r){$choiceMap[$r['pertanyaan_id']][]=['label'=>$r['label'],'nilai'=>$r['nilai'],'jumlah'=>(int)$r['jumlah']];}
foreach($chartQuestions as &$cq){$cq['pilihan']=$choiceMap[$cq['id']]??[];} unset($cq);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<style>
.survey-page {
  max-width: 1500px;
  margin: 0 auto;
  padding: 24px 22px 60px
}

.survey-head {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: flex-start;
  margin-bottom: 20px
}

.survey-head h1 {
  margin: 0;
  color: #12372a;
  font-size: 27px
}

.survey-head p {
  margin: 6px 0 0;
  color: #64756e
}

.survey-nav {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 20px
}

.survey-nav a {
  padding: 10px 14px;
  border-radius: 10px;
  background: #f2f7f4;
  color: #174d3b;
  font-weight: 700;
  font-size: 13px;
  text-decoration: none;
  border: 1px solid #dfebe5
}

.survey-nav a:hover {
  background: #e4f3ed
}

.sv-card {
  background: #fff;
  border: 1px solid #e2ebe6;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 7px 24px rgba(18, 55, 42, .05)
}

.sv-card.editing {
  border-color: #087f5b;
  box-shadow: 0 0 0 3px rgba(8, 127, 91, .08)
}

.sv-card h2 {
  margin: 0 0 4px;
  color: #12372a;
  font-size: 20px
}

.sv-help {
  font-size: 13px;
  color: #71817b;
  margin: 0 0 16px
}

.sv-form {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px
}

.sv-form .full {
  grid-column: 1/-1
}

.sv-form label {
  font-size: 12px;
  font-weight: 800;
  color: #375048
}

.sv-form input,
.sv-form select,
.sv-form textarea {
  display: block;
  width: 100%;
  margin-top: 6px;
  padding: 11px 12px;
  border: 1px solid #d9e5df;
  border-radius: 10px;
  background: #fff;
  color: #12372a;
  font: inherit
}

.sv-form textarea {
  min-height: 90px;
  resize: vertical
}

.sv-check {
  display: flex !important;
  align-items: center;
  gap: 8px;
  margin-top: 12px
}

.sv-check input {
  width: auto !important;
  margin: 0 !important
}

.sv-actions {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-top: 4px
}

.sv-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 0;
  border-radius: 9px;
  padding: 9px 13px;
  background: #087f5b;
  color: #fff;
  font-weight: 800;
  font-size: 12px;
  cursor: pointer;
  text-decoration: none
}

.sv-btn.blue {
  background: #2563eb
}

.sv-btn.red {
  background: #dc2626
}

.sv-btn.gray {
  background: #64748b
}

.sv-btn.yellow {
  background: #d97706
}

.sv-table-wrap {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #e4ece8;
  border-radius: 12px
}

.sv-table {
  width: 100%;
  min-width: 900px;
  border-collapse: collapse;
  font-size: 13px
}

.sv-table th {
  background: #f1f7f4;
  color: #5c776d;
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: .4px
}

.sv-table th,
.sv-table td {
  padding: 12px;
  border-bottom: 1px solid #e8eeeb;
  vertical-align: top
}

.sv-table tr:last-child td {
  border-bottom: 0
}

.sv-table td {
  color: #23443a
}

.sv-badge {
  display: inline-flex;
  padding: 4px 8px;
  border-radius: 999px;
  background: #e8f6f0;
  color: #087f5b;
  font-weight: 800;
  font-size: 11px
}

.sv-badge.gray {
  background: #eef2f3;
  color: #64748b
}

.sv-badge.blue {
  background: #eaf1ff;
  color: #2563eb
}

.sv-badge.orange {
  background: #fff3df;
  color: #b45309
}

.question-builder {
  display: grid;
  gap: 14px
}

.question-card {
  border: 1px solid #dfeae5;
  border-radius: 14px;
  padding: 16px;
  background: #fbfdfc
}

.question-top {
  display: flex;
  justify-content: space-between;
  gap: 15px;
  align-items: flex-start
}

.question-title {
  font-weight: 800;
  color: #12372a
}

.question-meta {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 7px
}

.choice-box {
  margin-top: 14px;
  padding: 13px;
  border-radius: 12px;
  background: #f3f8f5;
  border: 1px dashed #cfe0d8
}

.choice-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px
}

.choice-list {
  display: grid;
  gap: 8px
}

.choice-row {
  display: grid;
  grid-template-columns: 1fr 110px 80px auto;
  gap: 8px;
  align-items: center
}

.choice-row input {
  padding: 8px 9px;
  border: 1px solid #d7e4dd;
  border-radius: 8px;
  width: 100%
}

.type-note {
  font-size: 12px;
  color: #6b7e75;
  margin-top: 6px
}

.type-note strong {
  color: #087f5b
}

.empty {
  padding: 28px;
  text-align: center;
  color: #84938e;
  background: #f8fbfa;
  border-radius: 12px
}

.chart-grid {
  display: grid;
  grid-template-columns: 1.2fr .8fr;
  gap: 18px
}

.chart-card {
  border: 1px solid #e2ebe6;
  border-radius: 14px;
  padding: 16px;
  background: #fff
}

.chart-box {
  height: 310px;
  position: relative
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 18px
}

.kpi {
  padding: 15px;
  border-radius: 13px;
  background: #f5faf7;
  border: 1px solid #e0ebe5
}

.kpi strong {
  display: block;
  font-size: 26px;
  color: #087f5b
}

.kpi span {
  font-size: 12px;
  color: #708078
}

@media(max-width:900px) {
  .sv-form {
    grid-template-columns: 1fr
  }

  .sv-form .full {
    grid-column: auto
  }

  .chart-grid {
    grid-template-columns: 1fr
  }

  .kpi-grid {
    grid-template-columns: 1fr 1fr
  }
}

@media(max-width:600px) {
  .survey-page {
    padding: 18px 12px 40px
  }

  .survey-head {
    flex-direction: column
  }

  .survey-head h1 {
    font-size: 23px
  }

  .sv-card {
    padding: 15px
  }

  .choice-row {
    grid-template-columns: 1fr 90px auto
  }

  .choice-row .choice-order {
    display: none
  }

  .kpi-grid {
    grid-template-columns: 1fr
  }

  .sv-actions {
    flex-wrap: wrap
  }
}
</style>

<div class="survey-page">
  <div class="survey-head">
    <div>
      <h1>Survey FIKES</h1>
      <p>Kelola survey, pertanyaan, pilihan jawaban, responden, dan hasil dari satu halaman.</p>
    </div>
  </div>
  <nav class="survey-nav">
    <a href="#survey">01 Survey</a><a href="#pertanyaan">02 Pertanyaan</a><a href="#pilihan">03 Pilihan Jawaban</a><a
      href="#hasil-grafik">04 Hasil & Grafik</a><a href="#responden">05 Responden</a>
  </nav>

  <section id="survey" class="sv-card <?= $editSurvey?'editing':'' ?>">
    <h2><?= $editSurvey?'Edit Survey':'Tambah Survey' ?></h2>
    <p class="sv-help">Satu survey dapat memiliki banyak pertanyaan. Anda bisa menambah survey baru kapan saja.</p>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="survey_save"><input type="hidden" name="id"
        value="<?= (int)($editSurvey['id']??0) ?>">
      <label>Judul Survey<input name="judul" required value="<?=e($editSurvey['judul']??'')?>"
          placeholder="Contoh: Penilaian Pelayanan FIKES"></label>
      <label>Slug<input name="slug" value="<?=e($editSurvey['slug']??'')?>"
          placeholder="otomatis dari judul jika dikosongkan"></label>
      <label>Target Responden<select
          name="target_responden"><?php foreach(['Mahasiswa','Karyawan/Pegawai','Dosen','Alumni','Masyarakat','Umum'] as $v): ?>
          <option <?=($editSurvey['target_responden']??'Umum')===$v?'selected':''?>><?=e($v)?></option>
          <?php endforeach;?>
        </select></label>
      <label>Nomor Urut<input type="number" name="nomor_urut" min="1"
          value="<?=e($editSurvey['nomor_urut']??1)?>"></label>
      <label>Tanggal Mulai<input type="date" name="tanggal_mulai"
          value="<?=e($editSurvey['tanggal_mulai']??'')?>"></label>
      <label>Tanggal Selesai<input type="date" name="tanggal_selesai"
          value="<?=e($editSurvey['tanggal_selesai']??'')?>"></label>
      <label>Status<select name="status">
          <option value="aktif" <?=($editSurvey['status']??'nonaktif')==='aktif'?'selected':''?>>Aktif</option>
          <option value="nonaktif" <?=($editSurvey['status']??'nonaktif')==='nonaktif'?'selected':''?>>Nonaktif</option>
        </select></label>
      <label class="full">Deskripsi<textarea name="deskripsi"
          placeholder="Jelaskan tujuan survey..."><?=e($editSurvey['deskripsi']??'')?></textarea></label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editSurvey?'Simpan Perubahan':'Tambah Survey' ?></button><?php if($editSurvey): ?><a
          class="sv-btn gray" href="index.php#survey">Batal</a><?php endif;?></div>
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
          <?php if(!$surveys): ?><tr>
            <td colspan="7">
              <div class="empty">Belum ada survey.</div>
            </td>
          </tr><?php else: foreach($surveys as $s): ?><tr>
            <td><strong><?=e($s['judul'])?></strong><br><small><?=e($s['slug'])?></small></td>
            <td><?=e($s['target_responden'])?></td>
            <td><?=e($s['tanggal_mulai']?:'-')?> s/d <?=e($s['tanggal_selesai']?:'-')?></td>
            <td><span class="sv-badge <?= $s['status']==='aktif'?'':'gray' ?>"><?=e($s['status'])?></span></td>
            <td><?=e($s['jumlah_pertanyaan'])?></td>
            <td><?=e($s['jumlah_responden'])?></td>
            <td><a class="sv-btn blue" href="?edit_survey=<?=$s['id']?>#survey">Edit</a> <a class="sv-btn"
                href="?survey_focus=<?=$s['id']?>#pertanyaan">Pertanyaan</a> <button class="sv-btn red sv-delete"
                data-action="survey_delete" data-id="<?=$s['id']?>">Hapus</button></td>
          </tr><?php endforeach; endif; ?></tbody>
      </table>
    </div>
  </section>

  <section id="pertanyaan" class="sv-card <?= $editQuestion?'editing':'' ?>">
    <h2><?= $editQuestion?'Edit Pertanyaan':'Tambah Pertanyaan' ?></h2>
    <p class="sv-help">Tipe jawaban menentukan kontrol yang tampil di frontend. <strong>Skala</strong> dan
      <strong>Ya/Tidak</strong> memiliki pilihan awal otomatis. <strong>Pilihan Ganda</strong> Anda isi sendiri.
      <strong>Isian</strong> dan <strong>Uraian</strong> tidak membutuhkan pilihan.
    </p>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="question_save"><input type="hidden" name="id"
        value="<?= (int)($editQuestion['id']??0) ?>">
      <label>Survey<select name="survey_id" required><?php foreach($surveys as $s): ?><option value="<?=$s['id']?>"
            <?=((int)($editQuestion['survey_id']??($_GET['survey_focus']??0))===$s['id'])?'selected':''?>>
            <?=e($s['judul'])?></option><?php endforeach;?></select></label>
      <label>Tipe Pertanyaan<select name="tipe" id="questionType">
          <option value="skala" <?=($editQuestion['tipe']??'skala')==='skala'?'selected':''?>>Skala Kepuasan (5 pilihan)
          </option>
          <option value="pilihan_ganda" <?=($editQuestion['tipe']??'')==='pilihan_ganda'?'selected':''?>>Pilihan Ganda
          </option>
          <option value="ya_tidak" <?=($editQuestion['tipe']??'')==='ya_tidak'?'selected':''?>>Ya / Tidak</option>
          <option value="isian" <?=($editQuestion['tipe']??'')==='isian'?'selected':''?>>Isian Singkat</option>
          <option value="textarea" <?=($editQuestion['tipe']??'')==='textarea'?'selected':''?>>Uraian / Textarea
          </option>
        </select>
        <div id="typeNote" class="type-note"></div>
      </label>
      <label class="full">Pertanyaan<textarea name="pertanyaan" required
          placeholder="Contoh: Apakah Anda puas dengan pelayanan FIKES?"><?=e($editQuestion['pertanyaan']??'')?></textarea></label>
      <label>Nomor Urut<input type="number" name="nomor_urut" min="1"
          value="<?=e($editQuestion['nomor_urut']??1)?>"></label>
      <label>Status<select name="status">
          <option value="aktif" <?=($editQuestion['status']??'aktif')==='aktif'?'selected':''?>>Aktif</option>
          <option value="nonaktif" <?=($editQuestion['status']??'aktif')==='nonaktif'?'selected':''?>>Nonaktif</option>
        </select></label>
      <label class="sv-check"><input type="checkbox" name="wajib" value="1"
          <?=(!isset($editQuestion['wajib']) || $editQuestion['wajib'])?'checked':''?>> Jawaban wajib diisi</label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editQuestion?'Simpan Perubahan':'Tambah Pertanyaan' ?></button><?php if($editQuestion): ?><a
          class="sv-btn gray" href="index.php#pertanyaan">Batal</a><?php endif;?></div>
    </form>
    <div class="question-builder" style="margin-top:18px">
      <?php if(!$questions): ?><div class="empty">Belum ada pertanyaan.</div><?php else: foreach($questions as $q): ?>
      <div class="question-card">
        <div class="question-top">
          <div>
            <div class="question-title"><?=e($q['pertanyaan'])?></div>
            <div class="question-meta"><span class="sv-badge"><?=e($q['survey_judul'])?></span><span
                class="sv-badge blue"><?=e($q['tipe'])?></span><span
                class="sv-badge <?=((int)$q['wajib'])?'':'gray'?>"><?=((int)$q['wajib'])?'Wajib':'Opsional'?></span>
            </div>
          </div>
          <div><a class="sv-btn blue" href="?edit_question=<?=$q['id']?>#pertanyaan">Edit</a> <button
              class="sv-btn red sv-delete" data-action="question_delete" data-id="<?=$q['id']?>">Hapus</button></div>
        </div>
        <?php if(in_array($q['tipe'],['skala','pilihan_ganda','ya_tidak'],true)): ?><div class="choice-box">
          <div class="choice-head"><strong>Pilihan jawaban</strong><a class="sv-btn"
              href="?add_choice_for=<?=$q['id']?>#pilihan">+ Tambah pilihan</a></div>
          <div class="choice-list">
            <?php $found=false; foreach($choices as $c): if((int)$c['pertanyaan_id']===(int)$q['id']): $found=true; ?>
            <div class="choice-row"><input value="<?=e($c['label'])?>" readonly><input value="<?=e($c['nilai']??'')?>"
                readonly><input class="choice-order" value="<?=e($c['nomor_urut'])?>" readonly><span><a
                  class="sv-btn blue" href="?edit_choice=<?=$c['id']?>#pilihan">Edit</a> <button
                  class="sv-btn red sv-delete" data-action="choice_delete" data-id="<?=$c['id']?>">Hapus</button></span>
            </div><?php endif; endforeach; if(!$found): ?><div class="type-note">Belum ada pilihan. Gunakan tombol
              <strong>+ Tambah pilihan</strong>.
            </div><?php endif; ?>
          </div>
        </div><?php else: ?><div class="type-note" style="margin-top:12px">Kontrol frontend:
          <strong><?= $q['tipe']==='isian'?'input satu baris':'textarea / uraian panjang' ?></strong>. Tidak membutuhkan
          pilihan jawaban.
        </div><?php endif; ?>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </section>

  <?php
  $choiceQuestionId=(int)($_GET['add_choice_for']??($editChoice['pertanyaan_id']??0));
  $choiceQuestion=null; if($choiceQuestionId){$st=$pdo->prepare('SELECT p.*,s.judul survey_judul FROM survey_pertanyaan p LEFT JOIN survey s ON s.id=p.survey_id WHERE p.id=?');$st->execute([$choiceQuestionId]);$choiceQuestion=$st->fetch(PDO::FETCH_ASSOC);}
  ?>
  <section id="pilihan" class="sv-card <?= $editChoice?'editing':'' ?>">
    <h2><?= $editChoice?'Edit Pilihan Jawaban':'Tambah Pilihan Jawaban' ?></h2>
    <p class="sv-help">Pilihan disimpan langsung di bawah pertanyaan. Gunakan <strong>Nilai</strong> untuk perhitungan
      rata-rata, misalnya 1–5.</p>
    <form method="post" class="sv-form">
      <input type="hidden" name="action" value="choice_save"><input type="hidden" name="id"
        value="<?= (int)($editChoice['id']??0) ?>">
      <label class="full">Pertanyaan<select name="pertanyaan_id" required><?php foreach($questions as $q): ?><option
            value="<?=$q['id']?>"
            <?=((int)($editChoice['pertanyaan_id']??$choiceQuestionId)===(int)$q['id'])?'selected':''?>>
            <?=e($q['survey_judul'].' → '.$q['pertanyaan'].' ['.$q['tipe'].']')?></option>
          <?php endforeach;?></select></label>
      <label>Label Jawaban<input name="label" required value="<?=e($editChoice['label']??'')?>"
          placeholder="Contoh: Sangat Puas"></label>
      <label>Nilai<input type="number" step="0.01" name="nilai" value="<?=e($editChoice['nilai']??'')?>"
          placeholder="Contoh: 5"></label>
      <label>Nomor Urut<input type="number" min="1" name="nomor_urut"
          value="<?=e($editChoice['nomor_urut']??1)?>"></label>
      <div class="full sv-actions"><button class="sv-btn"
          type="submit"><?= $editChoice?'Simpan Perubahan':'Tambah Pilihan' ?></button><?php if($editChoice): ?><a
          class="sv-btn gray" href="index.php#pilihan">Batal</a><?php endif;?></div>
    </form>
  </section>

  <section id="hasil-grafik" class="sv-card">
    <h2>Hasil & Grafik</h2>
    <p class="sv-help">Ringkasan hasil responden dari database. Grafik mengikuti survey dan pertanyaan yang aktif.</p>
    <div class="kpi-grid">
      <div class="kpi"><strong><?=array_sum(array_column($chartSurvey,'jumlah'))?></strong><span>Total Responden</span>
      </div>
      <div class="kpi"><strong><?=count($surveys)?></strong><span>Total Survey</span></div>
      <div class="kpi">
        <strong><?=count(array_filter($questions,fn($q)=>$q['status']==='aktif'))?></strong><span>Pertanyaan
          Aktif</span>
      </div>
    </div>
    <div class="chart-grid">
      <div class="chart-card">
        <h3>Responden per Survey</h3>
        <div class="chart-box"><canvas id="surveyChart"></canvas></div>
      </div>
      <div class="chart-card">
        <h3>Kategori Responden</h3>
        <div class="chart-box"><canvas id="categoryChart"></canvas></div>
      </div>
    </div>
    <div class="chart-card" style="margin-top:18px">
      <h3>Analisis Pertanyaan</h3><label style="font-size:12px;font-weight:800;color:#375048">Survey<select
          id="chartSurvey"
          style="margin-top:6px;padding:10px;border:1px solid #d9e5df;border-radius:9px;width:100%;max-width:520px">
          <option value="">Pilih Survey</option><?php foreach($surveys as $s): ?><option value="<?=$s['id']?>">
            <?=e($s['judul'])?></option><?php endforeach;?>
        </select></label>
      <div class="chart-grid" style="margin-top:14px">
        <div class="chart-card">
          <h3>Rata-rata Nilai</h3>
          <div class="chart-box"><canvas id="avgChart"></canvas></div>
        </div>
        <div class="chart-card">
          <h3>Distribusi Pilihan</h3>
          <div class="chart-box"><canvas id="distChart"></canvas></div>
        </div>
      </div>
    </div>
  </section>

  <section id="responden" class="sv-card">
    <h2>Responden Terbaru</h2>
    <p class="sv-help">100 responden terakhir ditampilkan untuk pemantauan cepat.</p>
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
        <tbody><?php if(!$respondents): ?><tr>
            <td colspan="6">
              <div class="empty">Belum ada responden.</div>
            </td>
          </tr><?php else: foreach($respondents as $r): ?><tr>
            <td><?=e($r['survey_judul']??'-')?></td>
            <td><?=e($r['nama']?:'-')?></td>
            <td><?=e($r['email']?:'-')?></td>
            <td><?=e($r['kategori_responden']?:'-')?></td>
            <td><?=e($r['identitas']?:'-')?></td>
            <td><?=e($r['tanggal_isi'])?></td>
          </tr><?php endforeach; endif;?></tbody>
      </table>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const chartSurveyData = <?=json_encode($chartSurvey,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const chartCategoryData = <?=json_encode($chartCategory,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const chartQuestionData = <?=json_encode($chartQuestions,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
(function() {
  const type = document.getElementById('questionType'),
    note = document.getElementById('typeNote');

  function updateType() {
    if (!type || !note) return;
    const v = type.value;
    const m = {
      skala: 'Menampilkan pilihan standar: Sangat Tidak Puas → Sangat Puas. Pilihan tetap bisa diedit.',
      pilihan_ganda: 'Admin menentukan sendiri semua pilihan jawaban.',
      ya_tidak: 'Menampilkan pilihan standar Ya dan Tidak. Pilihan tetap bisa diedit.',
      isian: 'Menampilkan input teks satu baris. Tidak ada pilihan jawaban.',
      textarea: 'Menampilkan textarea untuk jawaban panjang. Tidak ada pilihan jawaban.'
    };
    note.textContent = m[v] || '';
  }
  if (type) {
    type.addEventListener('change', updateType);
    updateType();
  }
  document.querySelectorAll('.sv-delete').forEach(btn => btn.addEventListener('click', () => Swal.fire({
    title: 'Hapus data?',
    text: 'Data yang dihapus tidak dapat dikembalikan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal'
  }).then(r => {
    if (!r.isConfirmed) return;
    const f = document.createElement('form');
    f.method = 'post';
    f.innerHTML = '<input type="hidden" name="action" value="' + btn.dataset.action +
      '"><input type="hidden" name="id" value="' + btn.dataset.id + '">';
    document.body.appendChild(f);
    f.submit();
  })));
  const params = new URLSearchParams(location.search);
  if (params.get('ok')) Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: params.get('ok'),
    timer: 1600,
    showConfirmButton: false
  });
  if (params.get('err')) Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: params.get('err')
  });
  const canv = id => document.getElementById(id),
    charts = {};
  const destroy = k => {
    if (charts[k]) {
      charts[k].destroy();
      delete charts[k]
    }
  };
  if (canv('surveyChart')) charts.s = new Chart(canv('surveyChart'), {
    type: 'bar',
    data: {
      labels: chartSurveyData.map(x => x.judul),
      datasets: [{
        label: 'Responden',
        data: chartSurveyData.map(x => +x.jumlah),
        borderWidth: 1,
        borderRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
  if (canv('categoryChart')) charts.c = new Chart(canv('categoryChart'), {
    type: 'doughnut',
    data: {
      labels: chartCategoryData.map(x => x.kategori),
      datasets: [{
        data: chartCategoryData.map(x => +x.jumlah)
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
  const ss = document.getElementById('chartSurvey');

  function draw() {
    destroy('a');
    destroy('d');
    const sid = ss.value;
    const qs = chartQuestionData.filter(x => String(x.survey_id) === String(sid));
    if (!qs.length) return;
    charts.a = new Chart(canv('avgChart'), {
      type: 'bar',
      data: {
        labels: qs.map(x => x.pertanyaan),
        datasets: [{
          label: 'Rata-rata',
          data: qs.map(x => x.rata_nilai === null ? 0 : +x.rata_nilai),
          borderWidth: 1,
          borderRadius: 7
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            max: 5
          }
        }
      }
    });
    const q = qs[0];
    if (q.pilihan?.length) charts.d = new Chart(canv('distChart'), {
      type: 'bar',
      data: {
        labels: q.pilihan.map(x => x.label),
        datasets: [{
          label: 'Jawaban',
          data: q.pilihan.map(x => +x.jumlah),
          borderWidth: 1,
          borderRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  }
  if (ss) {
    ss.addEventListener('change', draw);
    if (ss.options.length > 1) {
      ss.selectedIndex = 1;
      draw();
    }
  }
})();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
