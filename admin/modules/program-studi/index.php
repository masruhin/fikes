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

  .frontend-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e8f6f3;
    color: #087b68;
    text-decoration: none
  }

  .frontend-btn:hover {
    background: #078f78;
    color: #fff
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

  .swal2-container {
    z-index: 10050 !important
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

  .file-info {
    margin-top: 6px;
    font-size: 12px;
    color: #087b68;
    font-weight: 600
  }

  .current-files {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 5px
  }

  .current-files a {
    color: #078f78;
    text-decoration: none;
    font-weight: 600
  }

  .current-photo {
    width: 150px;
    height: 90px;
    object-fit: cover;
    border-radius: 9px;
    border: 1px solid #dbe4e2;
    display: block
  }

  .muted {
    display: block
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
        <div><label>Nama Kaprodi</label><input name="kaprodi_nama" id="kaprodi_nama"></div>
        <div><label>NIDN Kaprodi</label><input name="kaprodi_nidn" id="kaprodi_nidn"></div>
        <div><label>Email Kaprodi</label><input type="email" name="kaprodi_email" id="kaprodi_email"></div>
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
        <div><label>Sekretaris</label><input name="sekretaris_nama" id="sekretaris_nama"></div>
        <div><label>NIDN Sekretaris</label><input name="sekretaris_nidn" id="sekretaris_nidn"></div>
        <div><label>Telepon</label><input name="kontak_telepon" id="kontak_telepon"></div>
        <div><label>Email</label><input type="email" name="kontak_email" id="kontak_email"></div>
        <div class="full"><label>Alamat</label><input name="alamat" id="alamat"></div>
        <div><label>Foto Program Studi</label><input type="file" name="foto" id="foto"
            accept=".jpg,.jpeg,.png,.webp"><small class="muted">JPG/PNG/WEBP, maksimal 2 MB.</small>
          <div id="fotoInfo" class="file-info">Belum ada foto</div>
        </div>
        <div><label>Brosur Program Studi</label><input type="file" name="brosur" id="brosur"
            accept=".pdf,application/pdf"><small class="muted">PDF, maksimal 5 MB.</small>
          <div id="brosurInfo" class="file-info">Belum ada brosur</div>
        </div>
        <div id="currentFiles" class="full"></div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
    let r = await fetch(api + '?action=list&search=' + encodeURIComponent($('q').value) + '&status=' +
        encodeURIComponent($('st').value)),
      d = await r.json();
    let h =
      '<table class="table"><tr><th>No</th><th>Kode</th><th>Program Studi</th><th>Jenjang</th><th>Status</th><th>Aksi</th></tr>';
    d.data.forEach((x, i) => h +=
      `<tr><td>${i+1}</td><td class=code>${esc(x.kode_prodi)}</td><td><b>${esc(x.nama)}</b><br>${esc(x.gelar||'')}</td><td>${esc(x.jenjang)}</td><td><span class=badge>${esc(x.status)}</span></td><td><button class="btn soft" onclick="edit(${x.id})">Edit</button> <button class="btn danger" onclick="hapus(${x.id})">Hapus</button> <a class="btn frontend-btn" target="_blank" rel="noopener" href="../../../page/program-studi/detail-prodi.php?kode=${encodeURIComponent(x.kode_prodi)}" title="Lihat halaman frontend ${esc(x.nama)}">↗ Frontend</a></td></tr>`
    );
    $('list').innerHTML = h + '</table>'
  }

  function baru() {
    $('f').reset();
    $('id').value = '';
    $('title').textContent = 'Tambah Program Studi';
    $('currentFiles').innerHTML = '';
    $('fotoInfo').textContent = 'Belum ada foto';
    $('brosurInfo').textContent = 'Belum ada brosur';
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
    try {
      let d = await (await fetch(api + '?action=get&id=' + id)).json();
      if (!d.success) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: d.message
        });
        return;
      }
      let x = d.data;
      $('modal').classList.add('open');
      $('title').textContent = 'Edit Program Studi';
      ['id', 'kode_prodi', 'nama', 'jenjang', 'gelar', 'kaprodi_nama', 'kaprodi_nidn', 'kaprodi_email', 'status',
        'durasi_studi', 'sks_lulus', 'akreditasi', 'nomor_akreditasi', 'tanggal_akreditasi', 'sekretaris_nama',
        'sekretaris_nidn', 'kontak_telepon', 'kontak_email', 'alamat', 'deskripsi', 'visi'
      ].forEach(k => $(k).value = x[k] ?? '');
      $('foto').value = '';
      $('brosur').value = '';
      $('fotoInfo').textContent = x.foto ? 'Foto tersimpan: ' + x.foto : 'Belum ada foto';
      $('brosurInfo').textContent = x.brosur ? 'Brosur tersimpan: ' + x.brosur : 'Belum ada brosur';
      $('currentFiles').innerHTML = (x.foto ?
        '<div><div class=muted>Foto saat ini</div><img class=current-photo src="../../uploads/program-studi/' +
        encodeURIComponent(x.foto) + '" alt="Foto"></div>' : '') + (x.brosur ?
        '<div><div class=muted>Brosur saat ini</div><a target=_blank href="../../uploads/program-studi/' +
        encodeURIComponent(x.brosur) + '">📄 Lihat brosur PDF</a></div>' : '');
      resetRows();
      (x.misi || []).forEach(a => addMisi(a.isi));
      (x.cpl || []).forEach(a => addCpl(a.kategori, a.isi));
      (x.kurikulum || []).forEach(a => addKur(a.kode_mk, a.nama_mk, a.semester, a.sks));
      (x.fasilitas || []).forEach(a => addFas(a.nama_fasilitas, a.deskripsi));
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Data Program Studi tidak dapat dimuat.'
      });
    }
  }
  async function simpan(e) {
    e.preventDefault();
    const isEdit = Boolean($('id').value);
    const tombol = e.submitter;
    if (tombol) tombol.disabled = true;
    try {
      let fd = new FormData(e.target);
      fd.append('action', 'save');
      let d = await (await fetch(api, {
        method: 'POST',
        body: fd
      })).json();
      if (d.success) {
        await Swal.fire({
          icon: 'success',
          title: isEdit ? 'Berhasil Diperbarui' : 'Berhasil Ditambahkan',
          text: d.message,
          timer: 1800,
          timerProgressBar: true,
          showConfirmButton: false,
          allowOutsideClick: false,
          allowEscapeKey: false
        });
        // Modal baru ditutup SETELAH SweetAlert selesai.
        tutup();
        load();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Menyimpan',
          text: d.message
        });
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: 'Data gagal disimpan. Silakan coba lagi.'
      });
    } finally {
      if (tombol) tombol.disabled = false;
    }
  }
  async function hapus(id) {
    const konfirmasi = await Swal.fire({
      icon: 'warning',
      title: 'Hapus Program Studi?',
      text: 'Data Program Studi beserta detail misi, CPL, kurikulum, dan fasilitas akan dihapus.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      reverseButtons: true
    });
    if (!konfirmasi.isConfirmed) return;
    try {
      let fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', id);
      let d = await (await fetch(api, {
        method: 'POST',
        body: fd
      })).json();
      if (d.success) {
        await Swal.fire({
          icon: 'success',
          title: 'Berhasil Dihapus',
          text: d.message,
          timer: 1800,
          showConfirmButton: false
        });
        load();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Menghapus',
          text: d.message
        });
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: 'Data gagal dihapus. Silakan coba lagi.'
      });
    }
  }
  load();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
