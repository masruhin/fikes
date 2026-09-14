<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-head">
  <div>
    <span class="eyebrow">MUTU & AKREDITASI</span>
    <h1>Sertifikat Akreditasi</h1>
    <p>Kelola sertifikat akreditasi berdasarkan Program Studi yang tersimpan di tabel <strong>program_studi</strong>.
    </p>
  </div>
  <button class="btn primary" onclick="openAdd()">+ Tambah Sertifikat</button>
</div>

<div class="panel table-panel">
  <div class="table-tools">
    <input id="search" type="search" placeholder="Cari program studi, nomor SK, peringkat..." oninput="loadData()">
    <select id="statusFilter" onchange="loadData()">
      <option value="">Semua Status</option>
      <option value="1">Aktif</option>
      <option value="0">Nonaktif</option>
    </select>
  </div>
  <div class="table-wrap">
    <table id="dataTable">
      <thead>
        <tr>
          <th>Program Studi</th>
          <th>Jenjang</th>
          <th>Institusi</th>
          <th>Lembaga</th>
          <th>Nomor SK</th>
          <th>Peringkat</th>
          <th>Berlaku Sampai</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr>
          <td colspan="9" class="empty">Memuat data...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="modal" id="modal">
  <div class="modal-box cert-modal">
    <div class="modal-head">
      <div><span class="eyebrow">FORM DATA</span>
        <h2 id="modalTitle">Tambah Sertifikat Akreditasi</h2>
      </div><button type="button" onclick="closeModal()">×</button>
    </div>
    <form id="formCert" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save"><input type="hidden" name="id_sertifikat" id="id_sertifikat">
      <div class="form-grid">
        <div class="full">
          <label>Program Studi <span>*</span></label>
          <select name="id_prodi" id="id_prodi" required>
            <option value="">Memuat program studi...</option>
          </select>
          <small>Pilihan diambil langsung dari tabel <strong>program_studi</strong>.</small>
        </div>
        <div><label>ID Institusi</label><input name="id_institusi" id="id_institusi"
            placeholder="Contoh: Univ. Bhamada"></div>
        <div><label>ID Lembaga</label><input name="id_lembaga" id="id_lembaga" placeholder="Contoh: BAN-PT / LAM-PTKes">
        </div>
        <div class="full"><label>Nomor SK</label><input name="nomor_sk" id="nomor_sk" placeholder="Nomor SK akreditasi">
        </div>
        <div><label>Peringkat</label><input name="peringkat" id="peringkat" placeholder="Baik Sekali / Unggul / B">
        </div>
        <div><label>Status</label><select name="status_aktif" id="status_aktif">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select></div>
        <div><label>Tanggal SK</label><input type="date" name="tanggal_sk" id="tanggal_sk"></div>
        <div><label>Tanggal Kadaluarsa</label><input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa">
        </div>
        <div class="full"><label>File Sertifikat</label><input type="file" name="file_sertifikat" id="file_sertifikat"
            accept=".pdf,.jpg,.jpeg,.xls,.xlsx,.doc,.docx,.ppt,.pptx"><small>PDF, JPG, JPEG, XLS, XLSX, DOC, DOCX, PPT,
            PPTX. Maksimal 10 MB.</small>
          <div id="currentFile" class="current-file"></div>
        </div>
      </div>
      <div class="modal-actions"><button type="button" class="btn" onclick="closeModal()">Batal</button><button
          type="submit" class="btn primary" id="saveBtn">Simpan</button></div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .cert-modal {
    max-width: 900px
  }

  .form-grid .full {
    grid-column: 1/-1
  }

  .form-grid small {
    display: block;
    margin-top: 7px;
    color: #82918b;
    font-size: 11px
  }

  .form-grid label span {
    color: #e55353
  }

  .current-file {
    margin-top: 10px;
    font-size: 12px
  }

  .current-file a {
    color: #087f5b;
    font-weight: 700
  }

  .empty {
    text-align: center;
    padding: 30px;
    color: #8a9892
  }

  .badge.success {
    background: #e7f7f1;
    color: #087f5b
  }

  .badge.danger {
    background: #fdecec;
    color: #c23b3b
  }

  .table-tools {
    display: flex;
    gap: 10px;
    flex-wrap: wrap
  }

  .table-tools input {
    flex: 1;
    min-width: 260px
  }

  .table-tools select {
    min-width: 150px
  }

  .actions {
    white-space: nowrap
  }
</style>
<script>
  const ajaxUrl = 'ajax.php';

  function esc(v) {
    return String(v ?? '').replace(/[&<>'"]/g, s => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      "'": '&#039;',
      '"': '&quot;'
    } [s]));
  }
  async function jsonRequest(url, options = {}) {
    const r = await fetch(url, {
      credentials: 'same-origin',
      cache: 'no-store',
      ...options
    });
    const t = await r.text();
    try {
      return JSON.parse(t)
    } catch (e) {
      throw new Error(t.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 500) ||
        'Respons server tidak valid.');
    }
  }
  async function loadProdi(selected = '') {
    const r = await jsonRequest(ajaxUrl + '?action=prodi');
    if (!r.success) throw new Error(r.message);
    const s = document.getElementById('id_prodi');
    s.innerHTML = '<option value="">Pilih Program Studi</option>' + r.data.map(p =>
        `<option value="${esc(p.id)}">${esc(p.nama)} — ${esc(p.jenjang)}${p.gelar?' ('+esc(p.gelar)+')':''}</option>`)
      .join('');
    if (selected !== '') s.value = String(selected);
  }
  async function loadData() {
    const q = encodeURIComponent(document.getElementById('search').value || '');
    const st = encodeURIComponent(document.getElementById('statusFilter').value || '');
    const b = document.getElementById('tbody');
    try {
      const r = await jsonRequest(`${ajaxUrl}?action=list&search=${q}&status=${st}`);
      if (!r.success) throw new Error(r.message);
      b.innerHTML = r.data.length ? r.data.map(x =>
        `<tr><td><strong>${esc(x.nama_prodi||'-')}</strong><br><small>${esc(x.kode_prodi||'')}</small></td><td>${esc(x.jenjang||'-')}</td><td>${esc(x.id_institusi||'-')}</td><td>${esc(x.id_lembaga||'-')}</td><td>${esc(x.nomor_sk||'-')}</td><td>${esc(x.peringkat||'-')}</td><td>${esc(x.tanggal_kadaluarsa||'-')}</td><td><span class="badge ${String(x.status_aktif)==='1'?'success':'danger'}">${String(x.status_aktif)==='1'?'Aktif':'Nonaktif'}</span></td><td class="actions"><button class="btn small" onclick="editCert(${Number(x.id_sertifikat)})">Edit</button> <button class="btn small danger-text" onclick="deleteCert(${Number(x.id_sertifikat)})">Hapus</button></td></tr>`
      ).join('') : '<tr><td colspan="9" class="empty">Belum ada data sertifikat.</td></tr>';
    } catch (e) {
      b.innerHTML = `<tr><td colspan="9" class="empty">${esc(e.message)}</td></tr>`;
    }
  }

  function openAdd() {
    document.getElementById('formCert').reset();
    document.getElementById('id_sertifikat').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Sertifikat Akreditasi';
    document.getElementById('currentFile').innerHTML = '';
    loadProdi();
    document.getElementById('modal').classList.add('show');
  }
  async function editCert(id) {
    try {
      const r = await jsonRequest(ajaxUrl + '?action=get&id=' + id);
      if (!r.success) throw new Error(r.message);
      const x = r.data;
      document.getElementById('modalTitle').textContent = 'Edit Sertifikat Akreditasi';
      document.getElementById('id_sertifikat').value = x.id_sertifikat || '';
      document.getElementById('id_institusi').value = x.id_institusi || '';
      document.getElementById('id_lembaga').value = x.id_lembaga || '';
      document.getElementById('nomor_sk').value = x.nomor_sk || '';
      document.getElementById('peringkat').value = x.peringkat || '';
      document.getElementById('tanggal_sk').value = x.tanggal_sk || '';
      document.getElementById('tanggal_kadaluarsa').value = x.tanggal_kadaluarsa || '';
      document.getElementById('status_aktif').value = String(x.status_aktif ?? 1);
      document.getElementById('currentFile').innerHTML = x.file_sertifikat ?
        `File saat ini: <a href="../../uploads/upload-sertifikat/${encodeURIComponent(x.file_sertifikat)}" target="_blank">${esc(x.file_sertifikat)}</a>` :
        '';
      document.getElementById('modal').classList.add('show');
      await loadProdi(x.id_prodi);
    } catch (e) {
      Swal.fire('Gagal', e.message, 'error');
    }
  }

  function closeModal() {
    document.getElementById('modal').classList.remove('show');
  }
  async function deleteCert(id) {
    const c = await Swal.fire({
      title: 'Hapus sertifikat?',
      text: 'Data sertifikat akan dihapus.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal'
    });
    if (!c.isConfirmed) return;
    try {
      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id_sertifikat', id);
      const r = await jsonRequest(ajaxUrl, {
        method: 'POST',
        body: fd
      });
      if (!r.success) throw new Error(r.message);
      await Swal.fire('Berhasil', r.message, 'success');
      loadData();
    } catch (e) {
      Swal.fire('Gagal', e.message, 'error');
    }
  }
  document.getElementById('formCert').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    try {
      const r = await jsonRequest(ajaxUrl, {
        method: 'POST',
        body: new FormData(e.target)
      });
      if (!r.success) throw new Error(r.message);
      await Swal.fire('Berhasil', r.message, 'success');
      closeModal();
      loadData();
    } catch (err) {
      Swal.fire('Gagal', err.message, 'error');
    } finally {
      btn.disabled = false;
    }
  });
  document.getElementById('modal').addEventListener('click', e => {
    if (e.target.id === 'modal') closeModal();
  });
  loadData();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
