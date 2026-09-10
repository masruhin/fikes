<?php
$page_title = 'Daftar Dosen';
require_once __DIR__ . '/../../config/auth.php';

/* HAPUS DOSEN */
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $stmt = $pdo->prepare("DELETE FROM dosen WHERE id=?");
    $stmt->execute([$id]);

    header('Location: index.php?ok=deleted');
    exit;
}

/* SIMPAN DOSEN DILAKUKAN MELALUI AJAX */

/* FILTER + PAGINATION */
$prodi_filter = trim($_GET['prodi'] ?? '');
$search = trim($_GET['search'] ?? '');

$per_page = 10;
$page = max(1, (int)($_GET['page'] ?? 1));

$where = [];
$params = [];

if ($prodi_filter !== '') {
    $where[] = "program_studi = ?";
    $params[] = $prodi_filter;
}

if ($search !== '') {
    $where[] = "(nama LIKE ? OR nidn LIKE ? OR program_studi LIKE ? OR jabatan LIKE ? OR email LIKE ?)";
    $keyword = '%' . $search . '%';
    $params = array_merge($params, [$keyword, $keyword, $keyword, $keyword, $keyword]);
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM dosen $whereSql");
$stmt->execute($params);
$total_data = (int)$stmt->fetchColumn();

$total_pages = max(1, (int)ceil($total_data / $per_page));
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $per_page;

$stmt = $pdo->prepare("
    SELECT * FROM dosen
    $whereSql
    ORDER BY nama
    LIMIT $per_page OFFSET $offset
");
$stmt->execute($params);
$dosen = $stmt->fetchAll();

function halamanUrl($nomor) {
    $query = $_GET;
    $query['page'] = $nomor;
    return '?' . http_build_query($query);
}

require __DIR__ . '/../../includes/header.php';
?>

<div class="page-head">
  <div>
    <span class="eyebrow">AKADEMIK</span>
    <h1><?= e($prodi_filter ? "Dosen " . $prodi_filter : "Daftar Dosen") ?></h1>
    <p>Kelola tenaga pengajar berdasarkan program studi.</p>
  </div>

  <button class="btn primary" onclick="tambahDosen()">+ Tambah Dosen</button>
</div>

<?php if (isset($_GET['ok'])): ?>
<div class="alert success">Data berhasil diproses.</div>
<?php endif; ?>

<div class="panel table-panel">
  <div class="table-tools">
    <form method="get" class="search-form">
      <?php if ($prodi_filter !== ''): ?>
      <input type="hidden" name="prodi" value="<?= e($prodi_filter) ?>">
      <?php endif; ?>
      <input id="search" name="search" value="<?= e($search) ?>"
        placeholder="Cari nama, NIDN, program studi, jabatan...">
      <button type="submit" class="btn small primary">Cari</button>
      <?php if ($search !== ''): ?>
      <a href="<?= $prodi_filter !== '' ? '?prodi=' . urlencode($prodi_filter) : 'index.php' ?>"
        class="btn small">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-wrap">
    <table id="dataTable">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nama</th>
          <th>NIDN</th>
          <th>Program Studi</th>
          <th>Jabatan</th>
          <th>Email</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($dosen as $d): ?>
        <tr>
          <td>
            <?php if (!empty($d['foto'])): ?>
            <img src="../../uploads/dosen/<?= e($d['foto']) ?>" alt="Foto dosen" class="dosen-thumb">
            <?php else: ?>
            <div class="dosen-thumb dosen-placeholder">👤</div>
            <?php endif; ?>
          </td>
          <td><strong><?= e($d['nama']) ?></strong></td>
          <td><?= e($d['nidn']) ?></td>
          <td><?= e($d['program_studi']) ?></td>
          <td><?= e($d['jabatan']) ?></td>
          <td><?= e($d['email']) ?></td>
          <td>
            <span class="badge success"><?= e($d['status']) ?></span>
          </td>
          <td class="actions">
            <button class="btn small" onclick='editDosen(<?= json_encode($d) ?>)'>
              Edit
            </button>

            <a class="btn small danger-text" href="?hapus=<?= $d['id'] ?>"
              onclick="return confirm('Hapus data dosen ini?')">
              Hapus
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($total_pages > 1): ?>
  <div class="pagination-wrap">
    <div class="pagination-info">
      Menampilkan
      <strong><?= $total_data ? $offset + 1 : 0 ?></strong>
      -
      <strong><?= min($offset + $per_page, $total_data) ?></strong>
      dari <strong><?= $total_data ?></strong> data
    </div>

    <div class="pagination">
      <?php if ($page > 1): ?>
      <a class="page-btn" href="<?= e(halamanUrl($page - 1)) ?>">‹</a>
      <?php endif; ?>

      <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                if ($start_page > 1):
                ?>
      <a class="page-btn" href="<?= e(halamanUrl(1)) ?>">1</a>
      <?php if ($start_page > 2): ?><span class="page-dots">...</span><?php endif; ?>
      <?php endif; ?>

      <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
      <a class="page-btn <?= $i === $page ? 'active' : '' ?>" href="<?= e(halamanUrl($i)) ?>">
        <?= $i ?>
      </a>
      <?php endfor; ?>

      <?php if ($end_page < $total_pages): ?>
      <?php if ($end_page < $total_pages - 1): ?><span class="page-dots">...</span><?php endif; ?>
      <a class="page-btn" href="<?= e(halamanUrl($total_pages)) ?>"><?= $total_pages ?></a>
      <?php endif; ?>

      <?php if ($page < $total_pages): ?>
      <a class="page-btn" href="<?= e(halamanUrl($page + 1)) ?>">›</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- MODAL DOSEN -->
<div class="modal" id="modal">
  <div class="modal-box modal-large">

    <div class="modal-head">
      <h2 id="modalTitle">Tambah Dosen</h2>
      <button type="button" onclick="closeModal()">×</button>
    </div>

    <form method="post" id="formDosen" enctype="multipart/form-data">
      <input type="hidden" name="id" id="id">

      <div class="form-grid">

        <div>
          <label>NIDN</label>
          <input name="nidn" id="nidn">
        </div>

        <div>
          <label>Nama</label>
          <input name="nama" id="nama" required>
        </div>

        <div>
          <label>Program Studi</label>
          <select name="program_studi" id="program_studi">
            <option>Keperawatan</option>
            <option>Kebidanan</option>
            <option>Farmasi</option>
            <option>K3</option>
          </select>
        </div>

        <div>
          <label>Jabatan</label>
          <input name="jabatan" id="jabatan">
        </div>

        <div>
          <label>Email</label>
          <input type="email" name="email" id="email">
        </div>

        <div>
          <label>Foto Dosen</label>
          <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          <small class="form-help">JPG, JPEG, PNG, WEBP — maksimal 2 MB.</small>
          <div id="fotoPreviewWrap" class="foto-preview-wrap">
            <img id="fotoPreview" src="" alt="Preview foto">
            <button type="button" class="btn small danger-text" onclick="hapusFotoPreview()">Hapus Foto</button>
          </div>
          <input type="hidden" name="hapus_foto" id="hapus_foto" value="0">
        </div>

        <div>
          <label>Status</label>
          <select name="status" id="status">
            <option value="aktif">aktif</option>
            <option value="nonaktif">nonaktif</option>
          </select>
        </div>

      </div>

      <hr>

      <!-- RIWAYAT PENDIDIKAN -->
      <div class="detail-header">
        <div>
          <h3>Riwayat Pendidikan</h3>
          <small>Bisa lebih dari satu.</small>
        </div>
        <button type="button" class="btn small primary" onclick="tambahBaris('pendidikan')">
          + Tambah Pendidikan
        </button>
      </div>

      <div id="pendidikanList" class="detail-list"></div>

      <!-- BIDANG AJAR -->
      <div class="detail-header">
        <div>
          <h3>Bidang Ajar</h3>
          <small>Bisa lebih dari satu.</small>
        </div>
        <button type="button" class="btn small primary" onclick="tambahBaris('ajar')">
          + Tambah Bidang Ajar
        </button>
      </div>

      <div id="ajarList" class="detail-list"></div>

      <!-- KEILMUAN -->
      <div class="detail-header">
        <div>
          <h3>Keilmuan</h3>
          <small>Bisa lebih dari satu.</small>
        </div>
        <button type="button" class="btn small primary" onclick="tambahBaris('keilmuan')">
          + Tambah Keilmuan
        </button>
      </div>

      <div id="keilmuanList" class="detail-list"></div>

      <div id="detailStatus" class="detail-status"></div>

      <div class="modal-actions">
        <button type="button" class="btn" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn primary">Simpan Dosen</button>
      </div>

    </form>
  </div>
</div>

<style>
.modal-large {
  width: min(850px, 95vw);
  max-height: 90vh;
  overflow-y: auto;
}

.detail-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin: 25px 0 10px;
}

.detail-header h3 {
  margin: 0;
}

.detail-header small {
  color: #8a96a8;
}

.detail-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.detail-row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 8px;
}

.detail-row input {
  width: 100%;
}

.detail-row button {
  min-width: 42px;
}

.detail-status {
  min-height: 20px;
  margin-top: 12px;
  color: #16835b;
  font-size: 13px;
}

.dosen-thumb {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
  border: 1px solid #e3e8ef;
}

.dosen-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f5f8;
  font-size: 20px;
}

.form-help {
  display: block;
  margin-top: 6px;
  color: #8a96a8;
  font-size: 12px;
}

.foto-preview-wrap {
  display: none;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
}

#fotoPreview {
  width: 90px;
  height: 90px;
  border-radius: 10px;
  object-fit: cover;
  border: 1px solid #e3e8ef;
}

.search-form {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.search-form input {
  flex: 1;
}

.pagination-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  padding: 16px 0 4px;
  flex-wrap: wrap;
}

.pagination-info {
  color: #6f7b8c;
  font-size: 13px;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 5px;
}

.page-btn {
  min-width: 34px;
  height: 34px;
  padding: 0 9px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e1e6ed;
  border-radius: 7px;
  background: #fff;
  color: #394557;
  text-decoration: none;
  font-size: 13px;
}

.page-btn:hover,
.page-btn.active {
  background: #1f6feb;
  border-color: #1f6feb;
  color: #fff;
}

.page-dots {
  padding: 0 3px;
  color: #8a96a8;
}

@media(max-width:600px) {
  .search-form {
    flex-wrap: wrap;
  }

  .search-form input {
    min-width: 100%;
  }

  .detail-header {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>

<script>
var currentDosenId = 0;

/* TAMBAH DOSEN BARU */
function tambahDosen() {
  currentDosenId = 0;

  document.getElementById('formDosen').reset();
  document.getElementById('id').value = '';
  document.getElementById('modalTitle').textContent = 'Tambah Dosen';

  document.getElementById('pendidikanList').innerHTML = '';
  document.getElementById('ajarList').innerHTML = '';
  document.getElementById('keilmuanList').innerHTML = '';
  document.getElementById('detailStatus').innerHTML = '';
  document.getElementById('foto').value = '';
  document.getElementById('hapus_foto').value = '0';
  document.getElementById('fotoPreviewWrap').style.display = 'none';
  document.getElementById('fotoPreview').src = '';

  tambahBaris('pendidikan');
  tambahBaris('ajar');
  tambahBaris('keilmuan');

  openModal();
}

/* EDIT DOSEN */
function editDosen(d) {
  currentDosenId = d.id;

  openModal();

  document.getElementById('modalTitle').textContent = 'Edit Dosen';

  var fields = [
    'id',
    'nidn',
    'nama',
    'program_studi',
    'jabatan',
    'email',
    'status'
  ];

  fields.forEach(function(k) {
    var el = document.getElementById(k);
    if (el) el.value = d[k] || '';
  });

  document.getElementById('foto').value = '';
  document.getElementById('hapus_foto').value = '0';
  if (d.foto) {
    document.getElementById('fotoPreview').src = '../../uploads/dosen/' + encodeURIComponent(d.foto);
    document.getElementById('fotoPreviewWrap').style.display = 'flex';
  } else {
    document.getElementById('fotoPreviewWrap').style.display = 'none';
    document.getElementById('fotoPreview').src = '';
  }

  document.getElementById('pendidikanList').innerHTML = '';
  document.getElementById('ajarList').innerHTML = '';
  document.getElementById('keilmuanList').innerHTML = '';
  document.getElementById('detailStatus').innerHTML = 'Memuat data tambahan...';

  muatDetail('pendidikan');
  muatDetail('ajar');
  muatDetail('keilmuan');
}

/* MODAL */
function openModal() {
  document.getElementById('modal').classList.add('show');
}

function closeModal() {
  document.getElementById('modal').classList.remove('show');
}

/* TAMBAH BARIS DETAIL */
function tambahBaris(type, value, detailId) {
  var container;

  if (type === 'pendidikan') {
    container = document.getElementById('pendidikanList');
  } else if (type === 'ajar') {
    container = document.getElementById('ajarList');
  } else {
    container = document.getElementById('keilmuanList');
  }

  var row = document.createElement('div');
  row.className = 'detail-row';

  var input = document.createElement('input');
  input.type = 'text';
  input.className = 'detail-input';
  input.value = value || '';
  input.placeholder =
    type === 'pendidikan' ?
    'Contoh: S2 Magister Keperawatan - Universitas...' :
    type === 'ajar' ?
    'Contoh: Keperawatan Medikal Bedah' :
    'Contoh: Keperawatan Gawat Darurat';

  var button = document.createElement('button');
  button.type = 'button';
  button.className = 'btn small danger-text';
  button.innerHTML = '×';

  button.onclick = function() {
    if (detailId) {
      hapusDetail(type, detailId, row);
    } else {
      row.remove();
    }
  };

  row.appendChild(input);
  row.appendChild(button);

  container.appendChild(row);
}

/* AJAX LOAD DETAIL */
function muatDetail(type) {
  if (!currentDosenId) {
    tambahBaris(type);
    return;
  }

  fetch('ajax_detail.php?action=list&type=' + encodeURIComponent(type) +
      '&dosen_id=' + encodeURIComponent(currentDosenId))
    .then(function(response) {
      return response.json();
    })
    .then(function(data) {

      var container =
        type === 'pendidikan' ?
        document.getElementById('pendidikanList') :
        type === 'ajar' ?
        document.getElementById('ajarList') :
        document.getElementById('keilmuanList');

      container.innerHTML = '';

      if (data.success && data.data.length > 0) {
        data.data.forEach(function(item) {
          tambahBaris(type, item.nilai, item.id);
        });
      } else {
        tambahBaris(type);
      }

      document.getElementById('detailStatus').innerHTML =
        'Data pendidikan, bidang ajar, dan keilmuan siap diedit.';
    })
    .catch(function(error) {
      console.error(error);
      document.getElementById('detailStatus').innerHTML =
        'Gagal memuat data tambahan.';
    });
}

/*
 * AJAX SIMPAN SEMUA DETAIL
 * Dipanggil setelah form dosen tersimpan.
 */
document.getElementById('formDosen').addEventListener('submit', function(e) {
  e.preventDefault();

  var form = this;
  var submitButton = form.querySelector('button[type="submit"]');
  submitButton.disabled = true;
  submitButton.textContent = 'Menyimpan...';

  var formData = new FormData(form);

  simpanDosenAJAX(formData)
    .then(function(result) {
      if (!result.success) {
        throw new Error(result.message || 'Gagal menyimpan dosen');
      }

      currentDosenId = result.id;

      return Promise.all([
        simpanDetail('pendidikan'),
        simpanDetail('ajar'),
        simpanDetail('keilmuan')
      ]);
    })
    .then(function() {
      window.location.href = 'index.php?ok=saved';
    })
    .catch(function(error) {
      console.error(error);
      document.getElementById('detailStatus').innerHTML =
        'Terjadi kesalahan: ' + error.message;

      submitButton.disabled = false;
      submitButton.textContent = 'Simpan Dosen';
    });
});

/* AJAX SIMPAN DATA UTAMA */
function simpanDosenAJAX(formData) {
  return fetch('ajax_detail.php?action=save_dosen', {
    method: 'POST',
    body: formData
  }).then(function(response) {
    return response.json();
  });
}

/* PREVIEW FOTO */
document.getElementById('foto').addEventListener('change', function() {
  var file = this.files[0];
  if (!file) return;

  var allowed = ['image/jpeg', 'image/png', 'image/webp'];
  if (allowed.indexOf(file.type) === -1) {
    alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
    this.value = '';
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran foto maksimal 2 MB.');
    this.value = '';
    return;
  }

  document.getElementById('hapus_foto').value = '0';
  var reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('fotoPreview').src = e.target.result;
    document.getElementById('fotoPreviewWrap').style.display = 'flex';
  };
  reader.readAsDataURL(file);
});

function hapusFotoPreview() {
  document.getElementById('foto').value = '';
  document.getElementById('fotoPreview').src = '';
  document.getElementById('fotoPreviewWrap').style.display = 'none';
  if (currentDosenId) {
    document.getElementById('hapus_foto').value = '1';
  }
}

/* AMBIL NILAI INPUT DETAIL */
function ambilNilai(type) {
  var container =
    type === 'pendidikan' ?
    document.getElementById('pendidikanList') :
    type === 'ajar' ?
    document.getElementById('ajarList') :
    document.getElementById('keilmuanList');

  var hasil = [];

  container.querySelectorAll('.detail-input').forEach(function(input) {
    var nilai = input.value.trim();

    if (nilai !== '') {
      hasil.push(nilai);
    }
  });

  return hasil;
}

/* AJAX SIMPAN DETAIL */
function simpanDetail(type) {
  var nilai = ambilNilai(type);

  return fetch('ajax_detail.php?action=save', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        dosen_id: currentDosenId,
        type: type,
        nilai: nilai
      })
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (!result.success) {
        throw new Error(result.message || 'Gagal menyimpan ' + type);
      }

      return result;
    });
}

/* AJAX HAPUS SATU DETAIL */
function hapusDetail(type, id, row) {
  if (!confirm('Hapus data ini?')) return;

  fetch('ajax_detail.php?action=delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        type: type,
        id: id
      })
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.success) {
        row.remove();
      } else {
        alert(result.message || 'Gagal menghapus data.');
      }
    })
    .catch(function() {
      alert('Koneksi AJAX gagal.');
    });
}
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
