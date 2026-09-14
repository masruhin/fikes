<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
include __DIR__ . '/../../includes/header.php';
?>
<style>
.mod {
  padding: 20px
}

.head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px
}

.head h2 {
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
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px
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
  font-size: 13px;
  vertical-align: middle
}

.table th {
  background: #f3faf8;
  color: #285b55
}

.thumb {
  width: 150px;
  height: 75px;
  object-fit: cover;
  border-radius: 9px;
  border: 1px solid #dbe4e2
}

.badge {
  padding: 5px 9px;
  border-radius: 99px;
  background: #e7f8f2;
  color: #087b68
}

.badge.off {
  background: #f3f4f6;
  color: #6b7280
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
  width: min(900px, 100%);
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
  min-height: 100px
}

.current {
  margin-top: 8px
}

.current img {
  width: 240px;
  max-height: 130px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #dbe4e2
}

.file-info {
  margin-top: 6px;
  font-size: 12px;
  color: #087b68;
  font-weight: 600
}

.swal2-container {
  z-index: 10050 !important
}

@media(max-width:800px) {
  .grid {
    grid-template-columns: 1fr
  }

  .full {
    grid-column: auto
  }

  .head {
    align-items: flex-start;
    gap: 10px;
    flex-direction: column
  }

  .toolbar {
    flex-direction: column
  }

  .table {
    display: block;
    overflow: auto;
    white-space: nowrap
  }
}
</style>
<div class="mod">
  <div class="head">
    <div>
      <h2>Slider Beranda</h2>
      <div class="muted">Kelola gambar dan konten slider yang tampil pada halaman beranda.</div>
    </div><button class="btn primary" onclick="baru()">+ Tambah Slider</button>
  </div>
  <div class="toolbar"><input id="q" placeholder="Cari judul slider..." oninput="load()"><select id="st"
      onchange="load()">
      <option value="">Semua status</option>
      <option value="aktif">Aktif</option>
      <option value="nonaktif">Nonaktif</option>
    </select></div>
  <div id="list">Memuat...</div>
</div>
<div class="modal" id="modal">
  <div class="box">
    <div class="head">
      <h2 id="title">Tambah Slider</h2><button class="btn danger" onclick="tutup()">Tutup</button>
    </div>
    <form id="f" class="form" enctype="multipart/form-data" onsubmit="simpan(event)">
      <input type="hidden" name="id" id="id">
      <div class="grid">
        <div class="full"><label>Judul *</label><input name="judul" id="judul" required></div>
        <div><label>Highlight</label><input name="highlight" id="highlight" placeholder="Contoh: Untuk Masa Depan">
        </div>
        <div><label>Label</label><input name="label" id="label" placeholder="Contoh: Pendidikan Berkualitas"></div>
        <div class="full"><label>Deskripsi</label><textarea name="deskripsi" id="deskripsi"></textarea></div>
        <div><label>Link Tombol Utama</label><input name="link_utama" id="link_utama"
            placeholder="Contoh: page/program-studi/program-studi.php"></div>
        <div><label>Teks Tombol Utama</label><input name="teks_tombol_utama" id="teks_tombol_utama"
            value="Lihat Program Studi"></div>
        <div><label>Link Tombol Kedua</label><input name="link_kedua" id="link_kedua"
            placeholder="Contoh: pendaftaran.php"></div>
        <div><label>Teks Tombol Kedua</label><input name="teks_tombol_kedua" id="teks_tombol_kedua" value="Pendaftaran">
        </div>
        <div><label>Nomor Urut *</label><input type="number" name="nomor_urut" id="nomor_urut" value="1" min="1"
            required></div>
        <div><label>Status</label><select name="status" id="status">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select></div>
        <div class="full"><label>Gambar Slider <span id="requiredMark">*</span></label><input type="file" name="gambar"
            id="gambar" accept=".jpg,.jpeg,.png,.webp">
          <div class="file-info">JPG/JPEG/PNG/WEBP, maksimal 5 MB. Rekomendasi 1920×800 px.</div>
          <div id="current" class="current"></div>
        </div>
      </div>
      <div style="text-align:right;margin-top:18px"><button class="btn primary">Simpan Slider</button></div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const api = 'ajax.php',
  $ = id => document.getElementById(id),
  esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  } [m]));
async function load() {
  try {
    let r = await fetch(api + '?action=list&search=' + encodeURIComponent($('q').value) + '&status=' +
      encodeURIComponent($('st').value));
    let d = await r.json();
    if (!d.success) throw Error(d.message);
    let h =
      '<table class="table"><tr><th>No</th><th>Gambar</th><th>Slider</th><th>Urut</th><th>Status</th><th>Aksi</th></tr>';
    d.data.forEach((x, i) => h +=
      `<tr><td>${i+1}</td><td><img class="thumb" src="${esc(x.gambar_url)}" alt=""></td><td><b>${esc(x.judul)}</b><br><span class="muted">${esc(x.label||'')}</span></td><td>${esc(x.nomor_urut)}</td><td><span class="badge ${x.status==='aktif'?'':'off'}">${esc(x.status)}</span></td><td><button class="btn soft" onclick="edit(${x.id})">Edit</button> <button class="btn danger" onclick="hapus(${x.id})">Hapus</button></td></tr>`
    );
    $('list').innerHTML = h + '</table>'
  } catch (e) {
    $('list').innerHTML = '<div class="muted">Gagal memuat data: ' + esc(e.message) + '</div>'
  }
}

function baru() {
  $('f').reset();
  $('id').value = '';
  $('nomor_urut').value = '1';
  $('status').value = 'aktif';
  $('teks_tombol_utama').value = 'Lihat Program Studi';
  $('teks_tombol_kedua').value = 'Pendaftaran';
  $('title').textContent = 'Tambah Slider';
  $('current').innerHTML = '';
  $('requiredMark').style.display = 'inline';
  $('modal').classList.add('open')
}

function tutup() {
  $('modal').classList.remove('open')
}
async function edit(id) {
  try {
    let d = await (await fetch(api + '?action=get&id=' + id)).json();
    if (!d.success) throw Error(d.message);
    let x = d.data;
    $('modal').classList.add('open');
    $('title').textContent = 'Edit Slider';
    ['id', 'judul', 'highlight', 'label', 'deskripsi', 'link_utama', 'teks_tombol_utama', 'link_kedua',
      'teks_tombol_kedua', 'nomor_urut', 'status'
    ].forEach(k => $(k).value = x[k] ?? '');
    $('gambar').value = '';
    $('requiredMark').style.display = 'none';
    $('current').innerHTML = x.gambar ?
      `<div class="muted">Gambar saat ini:</div><img src="${esc(x.gambar_url)}" alt="">` : ''
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: e.message
    })
  }
}
async function simpan(e) {
  e.preventDefault();
  let isEdit = Boolean($('id').value),
    fd = new FormData(e.target);
  fd.append('action', 'save');
  try {
    let d = await (await fetch(api, {
      method: 'POST',
      body: fd
    })).json();
    if (!d.success) {
      await Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: d.message
      });
      return
    }
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
    tutup();
    load()
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: e.message
    })
  }
}
async function hapus(id) {
  let c = await Swal.fire({
    icon: 'warning',
    title: 'Hapus Slider?',
    text: 'Gambar dan data slider akan dihapus.',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6b7280',
    reverseButtons: true
  });
  if (!c.isConfirmed) return;
  try {
    let fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id', id);
    let d = await (await fetch(api, {
      method: 'POST',
      body: fd
    })).json();
    if (!d.success) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: d.message
      });
      return
    }
    await Swal.fire({
      icon: 'success',
      title: 'Berhasil Dihapus',
      text: d.message,
      timer: 1500,
      showConfirmButton: false
    });
    load()
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: e.message
    })
  }
}
load();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
