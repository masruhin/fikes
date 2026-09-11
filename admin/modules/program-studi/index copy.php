<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
include __DIR__ . '/../../includes/header.php';
?>
<style>
.ps {
  padding: 20px
}

.ps-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px
}

.ps-head h2 {
  margin: 0;
  color: #123f39
}

.muted {
  color: #6b7280;
  font-size: 13px
}

.btn {
  border: 0;
  border-radius: 9px;
  padding: 9px 13px;
  font-weight: 600;
  cursor: pointer
}

.primary {
  background: #078f78;
  color: #fff
}

.danger {
  background: #fee2e2;
  color: #b91c1c
}

.soft {
  background: #edf7f4;
  color: #087b68
}

.toolbar {
  display: flex;
  gap: 10px;
  margin-bottom: 15px
}

.toolbar input,
.toolbar select,
.form input,
.form select,
.form textarea {
  border: 1px solid #dbe4e2;
  border-radius: 9px;
  padding: 10px 12px;
  background: #fff;
  box-sizing: border-box
}

.toolbar input {
  min-width: 280px
}

.table {
  width: 100%;
  border-collapse: collapse;
  background: #fff
}

.table th,
.table td {
  padding: 12px;
  border-bottom: 1px solid #edf1f0;
  text-align: left;
  font-size: 13px
}

.table th {
  background: #f3faf8;
  color: #285b55
}

.code {
  font-weight: 700;
  color: #078f78
}

.badge {
  padding: 5px 9px;
  border-radius: 99px;
  background: #e7f8f2;
  color: #087b68
}

.modal {
  position: fixed;
  inset: 0;
  background: #0008;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px
}

.modal.open {
  display: flex
}

.box {
  background: #fff;
  width: min(1000px, 100%);
  max-height: 92vh;
  overflow: auto;
  border-radius: 16px;
  padding: 22px
}

.grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 13px
}

.full {
  grid-column: 1/-1
}

.form label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 5px;
  color: #345650
}

.form input,
.form select,
.form textarea {
  width: 100%
}

.form textarea {
  min-height: 85px
}

.tabs {
  display: flex;
  gap: 7px;
  margin: 18px 0 12px;
  flex-wrap: wrap
}

.tab {
  border: 0;
  padding: 9px 13px;
  border-radius: 8px;
  background: #f2f7f6;
  cursor: pointer
}

.tab.active {
  background: #078f78;
  color: white
}

.pane {
  display: none
}

.pane.active {
  display: block
}

.row {
  display: flex;
  gap: 7px;
  margin-bottom: 8px
}

.row>* {
  flex: 1
}

.row button {
  flex: 0 0 auto
}

.photo-upload-box {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #f8fafc;
}

.photo-preview {
  width: 220px;
  height: 140px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  overflow: hidden;

  border: 2px dashed #cbd5e1;
  border-radius: 12px;

  background: #f0fdfa;
  color: #64748b;

  font-size: 14px;
}

.photo-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.photo-upload-control {
  flex: 1;
}

.photo-upload-control input[type="file"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  cursor: pointer;
}

.photo-upload-control small {
  display: block;
  margin-top: 8px;
  color: #64748b;
  font-size: 12px;
}

@media (max-width: 700px) {

  .photo-upload-box {
    flex-direction: column;
    align-items: stretch;
  }

  .photo-preview {
    width: 100%;
    height: 200px;
  }

}

.brosur-upload-box {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #f8fafc;
}

.brosur-icon {
  width: 60px;
  height: 60px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 12px;
  background: #e6f7f2;

  font-size: 28px;
}

.brosur-upload-control {
  flex: 1;
}

.brosur-upload-control input[type="file"] {
  width: 100%;
  padding: 10px;

  border: 1px solid #d1d5db;
  border-radius: 8px;

  background: #fff;
  cursor: pointer;
}

.brosur-upload-control small {
  display: block;
  margin-top: 7px;

  color: #64748b;
  font-size: 12px;
}

.brosur-info {
  margin-top: 8px;

  color: #078f74;
  font-size: 12px;
  font-weight: 600;
}

@media(max-width:700px) {
  .grid {
    grid-template-columns: 1fr
  }

  .full {
    grid-column: auto
  }

  .ps-head {
    align-items: flex-start;
    gap: 10px;
    flex-direction: column
  }

  .toolbar {
    flex-direction: column
  }
}
</style>
<div class="ps">
  <div class="ps-head">
    <div>
      <h2>Program Studi</h2>
      <div class="muted">Kelola seluruh data yang tampil pada frontend detail Program Studi.</div>
    </div><button class="btn primary" onclick="baru()">+ Tambah</button>
  </div>
  <div class="toolbar"><input id="q" placeholder="Cari kode / nama..." oninput="load()"><select id="st"
      onchange="load()">
      <option value="">Semua status</option>
      <option>aktif</option>
      <option>nonaktif</option>
    </select></div>
  <div id="list">Memuat...</div>
</div>
<div class="modal" id="modal">
  <div class="box">
    <div class="ps-head">
      <h2 id="title">Tambah Program Studi</h2><button class="btn danger" onclick="tutup()">Tutup</button>
    </div>
    <form id="f" class="form" enctype="multipart/form-data" onsubmit="simpan(event)"><input type="hidden" name="id"
        id="id">
      <div class="grid">
        <div><label>Kode Prodi *</label><input name="kode_prodi" id="kode_prodi" required></div>
        <div><label>Nama *</label><input name="nama" id="nama" required></div>
        <div><label>Jenjang *</label><input name="jenjang" id="jenjang" required></div>
        <div><label>Gelar</label><input name="gelar" id="gelar"></div>
        <div><label>Status</label><select name="status" id="status">
            <option>aktif</option>
            <option>nonaktif</option>
          </select></div>
        <div><label>Durasi Studi</label><input name="durasi_studi" id="durasi_studi"></div>
        <div><label>SKS Lulus</label><input type="number" name="sks_lulus" id="sks_lulus"></div>
        <div><label>Akreditasi</label><input name="akreditasi" id="akreditasi"></div>
        <div><label>No. Akreditasi</label><input name="nomor_akreditasi" id="nomor_akreditasi"></div>
        <div><label>Tanggal Akreditasi</label><input type="date" name="tanggal_akreditasi" id="tanggal_akreditasi">
        </div>
        <div>
          <label>Kaprodi</label>
          <input name="kaprodi_nama" id="kaprodi_nama" placeholder="Nama Kepala Program Studi">
        </div>

        <div>
          <label>NIDN Kaprodi</label>
          <input name="kaprodi_nidn" id="kaprodi_nidn" placeholder="NIDN Kaprodi">
        </div>

        <div>
          <label>Email Kaprodi</label>
          <input type="email" name="kaprodi_email" id="kaprodi_email" placeholder="Email Kaprodi">
        </div>

        <div>
          <label>Sekretaris Prodi</label>
          <input name="sekretaris_nama" id="sekretaris_nama" placeholder="Nama Sekretaris Prodi">
        </div>

        <div>
          <label>NIDN Sekretaris</label>
          <input name="sekretaris_nidn" id="sekretaris_nidn" placeholder="NIDN Sekretaris">
        </div>

        <div>
          <label>Email Sekretaris</label>
          <input type="email" name="sekretaris_email" id="sekretaris_email" placeholder="Email Sekretaris">
        </div>

        <div>
          <label>Telepon</label>
          <input name="kontak_telepon" id="kontak_telepon" placeholder="Nomor telepon Prodi">
        </div>
        <div><label>Email</label><input type="email" name="kontak_email" id="kontak_email"></div>
        <div class="form-group full">
          <label>Foto Program Studi</label>

          <div class="photo-upload-box">
            <div id="previewFoto" class="photo-preview">
              <span>Belum ada foto</span>
            </div>
            <div class="photo-upload-control">
              <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp"
                onchange="previewFotoProgram(this)">
              <small>
                JPG, JPEG, PNG atau WEBP. Maksimal 2 MB.
              </small>
            </div>
          </div>

        </div>
        <div class="form-group full">
          <label>Brosur Program Studi</label>

          <div class="brosur-upload-box">

            <div class="brosur-icon">
              📄
            </div>

            <div class="brosur-upload-control">

              <input type="file" id="brosur" name="brosur" accept=".pdf,application/pdf">

              <small>
                Format PDF. Maksimal 5 MB.
              </small>

              <div id="brosurInfo" class="brosur-info">
                Belum ada brosur
              </div>

            </div>

          </div>
        </div>
        <div class="full"><label>Alamat</label><input name="alamat" id="alamat"></div>
        <div class="full"><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi"></textarea></div>
        <div class="full"><label>Visi</label><textarea name="visi" id="visi"></textarea></div>
      </div>
      <div class="tabs"><button type="button" class="tab active" onclick="tab('misi',this)">Misi</button><button
          type="button" class="tab" onclick="tab('cpl',this)">CPL</button><button type="button" class="tab"
          onclick="tab('kur',this)">Kurikulum</button><button type="button" class="tab"
          onclick="tab('fas',this)">Fasilitas</button></div>
      <div id="pane-misi" class="pane active">
        <div id="misi"></div><button type="button" class="btn soft" onclick="addMisi()">+ Misi</button>
      </div>
      <div id="pane-cpl" class="pane">
        <div id="cpl"></div><button type="button" class="btn soft" onclick="addCpl()">+ CPL</button>
      </div>
      <div id="pane-kur" class="pane">
        <div id="kur"></div><button type="button" class="btn soft" onclick="addKur()">+ Mata Kuliah</button>
      </div>
      <div id="pane-fas" class="pane">
        <div id="fas"></div><button type="button" class="btn soft" onclick="addFas()">+ Fasilitas</button>
      </div>
      <div style="text-align:right;margin-top:18px"><button class="btn primary">Simpan</button></div>
    </form>
  </div>
</div>
<script>
const form = document.getElementById('formProdi');

const formData = new FormData(form);

const response = await fetch(
  'ajax.php?action=save', {
    method: 'POST',
    body: formData
  }
);

const result = await response.json();

if (!result.success) {
  alert(result.message || 'Gagal menyimpan data.');
  return;
}

alert('Data Program Studi berhasil disimpan.');

location.reload();
const inputBrosur = document.getElementById('brosur');

if (inputBrosur) {
  inputBrosur.addEventListener('change', function() {

    const info = document.getElementById('brosurInfo');

    if (!this.files || !this.files[0]) {
      info.textContent = 'Belum ada brosur';
      return;
    }

    const file = this.files[0];

    if (file.type !== 'application/pdf') {
      alert('Brosur harus berupa file PDF.');
      this.value = '';
      info.textContent = 'Belum ada brosur';
      return;
    }

    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran brosur maksimal 5 MB.');
      this.value = '';
      info.textContent = 'Belum ada brosur';
      return;
    }

    info.textContent =
      'Brosur dipilih: ' + file.name;
  });
}
const api = 'ajax.php';
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#039;'
} [m]));
async function load() {

  const list = document.getElementById('list');

  list.innerHTML = `
        <div style="padding:20px;text-align:center;color:#6b7280">
            Memuat data...
        </div>
    `;

  try {

    const search = encodeURIComponent(
      document.getElementById('q').value
    );

    const status = encodeURIComponent(
      document.getElementById('st').value
    );

    const url =
      'ajax.php?action=list&search=' +
      search +
      '&status=' +
      status;

    const response = await fetch(url);

    const text = await response.text();

    console.log('Response AJAX:', text);

    let data;

    try {
      data = JSON.parse(text);
    } catch (e) {

      list.innerHTML = `
                <div style="
                    background:#fee2e2;
                    color:#991b1b;
                    padding:15px;
                    border-radius:10px;
                ">
                    <b>Gagal membaca data dari server.</b>
                    <br><br>
                    Periksa file ajax.php.
                    <br><br>
                    <small>${escapeHtml(text)}</small>
                </div>
            `;

      return;
    }

    if (!data.success) {

      list.innerHTML = `
                <div style="
                    background:#fff7ed;
                    color:#9a3412;
                    padding:15px;
                    border-radius:10px;
                ">
                    <b>Terjadi kesalahan:</b>
                    <br>
                    ${escapeHtml(data.message)}
                </div>
            `;

      return;
    }

    if (!data.data || data.data.length === 0) {

      list.innerHTML = `
                <div style="
                    background:#fff;
                    border:1px solid #e5e7eb;
                    padding:30px;
                    text-align:center;
                    border-radius:10px;
                    color:#6b7280;
                ">
                    Belum ada data Program Studi.
                </div>
            `;

      return;
    }

    let html = `
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Program Studi</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
        `;

    data.data.forEach((item, index) => {

      html += `
                <tr>

                    <td>${index + 1}</td>

                    <td>
                        <span class="code">
                            ${escapeHtml(item.kode_prodi)}
                        </span>
                    </td>

                    <td>
                        <b>${escapeHtml(item.nama)}</b>
                        <br>
                        <span class="muted">
                            ${escapeHtml(item.gelar || '')}
                        </span>
                    </td>

                    <td>
                        ${escapeHtml(item.jenjang)}
                    </td>

                    <td>
                        <span class="badge">
                            ${escapeHtml(item.status)}
                        </span>
                    </td>

                    <td>

                        <button
                            class="btn soft"
                            onclick="edit(${item.id})">
                            Edit
                        </button>

                        <button
                            class="btn danger"
                            onclick="hapus(${item.id})">
                            Hapus
                        </button>

                        <a
                            class="btn soft"
                            target="_blank"
                            href="../../../../page/program-studi/detail-prodi.php?id=${item.id}">
                            Frontend
                        </a>

                    </td>

                </tr>
            `;
    });

    html += `
                </tbody>
            </table>
        `;

    list.innerHTML = html;

  } catch (error) {

    console.error(error);

    list.innerHTML = `
            <div style="
                background:#fee2e2;
                color:#991b1b;
                padding:15px;
                border-radius:10px;
            ">
                <b>Gagal mengambil data.</b>
                <br><br>
                ${escapeHtml(error.message)}
            </div>
        `;
  }
}

function previewFotoProgram(input) {

  const preview = document.getElementById('previewFoto');

  if (!input.files || !input.files[0]) {
    preview.innerHTML = '<span>Belum ada foto</span>';
    return;
  }

  const file = input.files[0];

  // Maksimal 2 MB
  if (file.size > 2 * 1024 * 1024) {

    alert('Ukuran foto maksimal 2 MB.');

    input.value = '';

    preview.innerHTML = '<span>Belum ada foto</span>';

    return;
  }

  // Format yang diperbolehkan
  const allowedTypes = [
    'image/jpeg',
    'image/png',
    'image/webp'
  ];

  if (!allowedTypes.includes(file.type)) {

    alert('Format foto harus JPG, JPEG, PNG atau WEBP.');

    input.value = '';

    preview.innerHTML = '<span>Belum ada foto</span>';

    return;
  }

  const reader = new FileReader();

  reader.onload = function(e) {

    preview.innerHTML = `
            <img
                src="${e.target.result}"
                alt="Preview Foto Program Studi"
            >
        `;

  };

  reader.readAsDataURL(file);
}

function escapeHtml(value) {

  return String(value ?? '').replace(
    /[&<>"']/g,
    function(match) {

      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      } [match];

    }
  );

}

function baru() {
  $('f').reset();
  $('id').value = '';
  $('title').textContent = 'Tambah Program Studi';
  resetRows();
  $('modal').classList.add('open')
}

function tutup() {
  $('modal').classList.remove('open')
}

function resetRows() {
  ['misi', 'cpl', 'kur', 'fas'].forEach(x => $(x).innerHTML = '')
}

function tab(n, b) {
  document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
  document.querySelectorAll('.pane').forEach(x => x.classList.remove('active'));
  b.classList.add('active');
  $('pane-' + n).classList.add('active')
}

function addMisi(v = '') {
  let d = document.createElement('div');
  d.className = 'row';
  d.innerHTML =
    `<textarea name=misi[] placeholder="Isi misi">${esc(v)}</textarea><button type=button class="btn danger" onclick="this.parentNode.remove()">×</button>`;
  $('misi').append(d)
}

function addCpl(k = '', v = '') {
  let d = document.createElement('div');
  d.className = 'row';
  d.innerHTML =
    `<input name=cpl_kategori[] placeholder=Kategori value="${esc(k)}"><textarea name=cpl_isi[] placeholder="Isi CPL">${esc(v)}</textarea><button type=button class="btn danger" onclick="this.parentNode.remove()">×</button>`;
  $('cpl').append(d)
}

function addKur(k = '', n = '', s = '', x = '') {
  let d = document.createElement('div');
  d.className = 'row';
  d.innerHTML =
    `<input name=kur_kode[] placeholder=Kode value="${esc(k)}"><input name=kur_nama[] placeholder="Mata Kuliah" value="${esc(n)}"><input name=kur_semester[] placeholder=Semester value="${esc(s)}"><input name=kur_sks[] type=number step=.1 placeholder=SKS value="${esc(x)}"><button type=button class="btn danger" onclick="this.parentNode.remove()">×</button>`;
  $('kur').append(d)
}

function addFas(n = '', v = '') {
  let d = document.createElement('div');
  d.className = 'row';
  d.innerHTML =
    `<input name=fas_nama[] placeholder="Nama fasilitas" value="${esc(n)}"><textarea name=fas_desc[] placeholder=Deskripsi>${esc(v)}</textarea><button type=button class="btn danger" onclick="this.parentNode.remove()">×</button>`;
  $('fas').append(d)
}
async function edit(id) {
  let d = await (await fetch(api + '?action=get&id=' + id)).json();
  if (!d.success) return alert(d.message);
  let x = d.data;
  $('modal').classList.add('open');
  $('title').textContent = 'Edit Program Studi';
  [
    'id',
    'kode_prodi',
    'nama',
    'jenjang',
    'gelar',

    'kaprodi_nama',
    'kaprodi_nidn',
    'kaprodi_email',

    'status',

    'durasi_studi',
    'sks_lulus',
    'akreditasi',
    'nomor_akreditasi',
    'tanggal_akreditasi',

    'sekretaris_nama',
    'sekretaris_nidn',

    'kontak_telepon',
    'kontak_email',
    'alamat',
    'deskripsi',
    'visi'
  ].forEach(k => $(k).value = x[k] ?? '');
  resetRows();
  (x.misi || []).forEach(a => addMisi(a.isi));
  (x.cpl || []).forEach(a => addCpl(a.kategori, a.isi));
  (x.kurikulum || []).forEach(a => addKur(a.kode_mk, a.nama_mk, a.semester, a.sks));
  (x.fasilitas || []).forEach(a => addFas(a.nama_fasilitas, a.deskripsi))
}
async function simpan(e) {
  e.preventDefault();
  let fd = new FormData(e.target);
  fd.append('action', 'save');
  let d = await (await fetch(api, {
    method: 'POST',
    body: fd
  })).json();
  alert(d.message);
  if (d.success) {
    tutup();
    load()
  }
}
async function hapus(id) {
  if (!confirm('Hapus Program Studi beserta detailnya?')) return;
  let fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);
  let d = await (await fetch(api, {
    method: 'POST',
    body: fd
  })).json();
  alert(d.message);
  if (d.success) load()
}
load();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
