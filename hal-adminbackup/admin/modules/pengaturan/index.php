<?php
$page_title='Pengaturan Website'; require_once __DIR__.'/../../config/auth.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $d=[trim($_POST['nama_kampus']),trim($_POST['email']),trim($_POST['telepon']),trim($_POST['alamat']),trim($_POST['instagram']),trim($_POST['facebook']),trim($_POST['youtube']),trim($_POST['maps_embed'])];
 $s=$pdo->prepare("UPDATE pengaturan SET nama_kampus=?,email=?,telepon=?,alamat=?,instagram=?,facebook=?,youtube=?,maps_embed=? WHERE id=1");$s->execute($d);
 header('Location:index.php?saved=1');exit;
}
$d=$pdo->query("SELECT * FROM pengaturan WHERE id=1")->fetch();require __DIR__.'/../../includes/header.php';?>
<div class="page-head"><div><span class="eyebrow">PENGATURAN</span><h1>Pengaturan Website</h1><p>Kelola identitas, kontak, media sosial, dan lokasi FIKES.</p></div></div>
<?php if(isset($_GET['saved'])):?><div class="alert success">Pengaturan berhasil disimpan.</div><?php endif;?>
<form method="post"><div class="panel"><div class="panel-head"><div><span class="eyebrow">IDENTITAS</span><h2>Informasi Utama</h2></div></div><div class="form-grid"><div><label>Nama Kampus</label><input name="nama_kampus" value="<?=e($d['nama_kampus'])?>"></div><div><label>Email</label><input name="email" type="email" value="<?=e($d['email'])?>"></div><div><label>Telepon</label><input name="telepon" value="<?=e($d['telepon'])?>"></div></div><label>Alamat</label><textarea name="alamat" rows="4"><?=e($d['alamat'])?></textarea></div>
<div class="panel"><div class="panel-head"><div><span class="eyebrow">MEDIA SOSIAL</span><h2>Link Sosial Media</h2></div></div><div class="form-grid"><div><label>Instagram</label><input name="instagram" value="<?=e($d['instagram'])?>"></div><div><label>Facebook</label><input name="facebook" value="<?=e($d['facebook'])?>"></div><div><label>YouTube</label><input name="youtube" value="<?=e($d['youtube'])?>"></div></div></div>
<div class="panel"><div class="panel-head"><div><span class="eyebrow">LOKASI</span><h2>Google Maps Embed</h2></div></div><textarea name="maps_embed" rows="5"><?=e($d['maps_embed'])?></textarea></div><button class="btn primary">Simpan Pengaturan</button></form>
<?php require __DIR__.'/../../includes/footer.php';?>
