<?php
require_once __DIR__.'/../../config/auth.php';
wajib_login();
require_once __DIR__.'/../../config/database.php';
header('Content-Type: application/json; charset=utf-8');
$uploadDir=__DIR__.'/../../uploads/berita/';$uploadWeb='../../uploads/berita/';if(!is_dir($uploadDir))@mkdir($uploadDir,0777,true);
function out($success,$message='',$data=[]){echo json_encode(['success'=>$success,'message'=>$message,'data'=>$data],JSON_UNESCAPED_UNICODE);exit;}
function safeName($name){$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));return date('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$ext;}
function uploadGambar($file,$dir){if(!$file||($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;if($file['error']!==UPLOAD_ERR_OK)throw new Exception('Upload gambar gagal.');if($file['size']>5*1024*1024)throw new Exception('Ukuran gambar maksimal 5 MB.');$allowed=['jpg','jpeg','png','webp'];$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));if(!in_array($ext,$allowed,true))throw new Exception('Format gambar harus JPG, JPEG, PNG, atau WEBP.');if(!@getimagesize($file['tmp_name']))throw new Exception('File bukan gambar yang valid.');$name=safeName($file['name']);if(!move_uploaded_file($file['tmp_name'],$dir.$name))throw new Exception('Gambar gagal disimpan.');return $name;}
$action=$_GET['action']??$_POST['action']??'';
try{
 if($action==='list'){$search=trim($_GET['search']??'');$status=trim($_GET['status']??'');$sql='SELECT id,judul,slug,kategori,gambar,penulis,tanggal_terbit,status FROM berita WHERE 1=1';$p=[];if($search!==''){$sql.=' AND (judul LIKE :s1 OR kategori LIKE :s2)';$p[':s1']="%$search%";$p[':s2']="%$search%";}if($status!==''){$sql.=' AND status=:st';$p[':st']=$status;}$sql.=' ORDER BY tanggal_terbit DESC,id DESC';$q=$pdo->prepare($sql);$q->execute($p);$rows=$q->fetchAll(PDO::FETCH_ASSOC);foreach($rows as &$r){$r['gambar_url']=$r['gambar']?$uploadWeb.rawurlencode(basename($r['gambar'])):'';$r['tanggal_tampil']=date('d-m-Y H:i',strtotime($r['tanggal_terbit']));}out(true,'',$rows);}
 if($action==='get'){$id=(int)($_GET['id']??0);$q=$pdo->prepare('SELECT * FROM berita WHERE id=?');$q->execute([$id]);$r=$q->fetch(PDO::FETCH_ASSOC);if(!$r)out(false,'Berita tidak ditemukan.');$r['tanggal_terbit']=date('Y-m-d\TH:i',strtotime($r['tanggal_terbit']));$r['gambar_url']=$r['gambar']?$uploadWeb.rawurlencode(basename($r['gambar'])):'';out(true,'',$r);}
 if($action==='save'){
  $id=(int)($_POST['id']??0);$judul=trim($_POST['judul']??'');$slug=trim($_POST['slug']??'');$isi=$_POST['isi']??'';if($judul==='')out(false,'Judul berita wajib diisi.');if($slug==='')out(false,'Slug wajib diisi.');if(trim(strip_tags($isi))==='')out(false,'Isi berita wajib diisi.');
  $kategori=trim($_POST['kategori']??'Berita')?:'Berita';$ringkasan=trim($_POST['ringkasan']??'');$penulis=trim($_POST['penulis']??'Admin FIKES');$tanggal=trim($_POST['tanggal_terbit']??'');$tanggal=$tanggal?date('Y-m-d H:i:s',strtotime($tanggal)):date('Y-m-d H:i:s');$status=($_POST['status']??'draft')==='terbit'?'terbit':'draft';
  $check=$pdo->prepare('SELECT id FROM berita WHERE slug=? AND id<>?');$check->execute([$slug,$id]);if($check->fetch())out(false,'Slug sudah digunakan. Silakan gunakan slug lain.');
  $q=$pdo->prepare('SELECT gambar FROM berita WHERE id=?');$q->execute([$id]);$old=$q->fetchColumn();$new=null;if(isset($_FILES['gambar']))$new=uploadGambar($_FILES['gambar'],$uploadDir);
  if($id){$sql='UPDATE berita SET judul=?,slug=?,kategori=?,ringkasan=?,isi=?,penulis=?,tanggal_terbit=?,status=?';$params=[$judul,$slug,$kategori,$ringkasan,$isi,$penulis,$tanggal,$status];if($new){$sql.=',gambar=?';$params[]=$new;}$sql.=' WHERE id=?';$params[]=$id;$pdo->prepare($sql)->execute($params);if($new&&$old&&is_file($uploadDir.basename($old)))@unlink($uploadDir.basename($old));out(true,'Berita berhasil diperbarui.');}
  else{ $st=$pdo->prepare('INSERT INTO berita (judul,slug,kategori,ringkasan,isi,gambar,penulis,tanggal_terbit,status) VALUES (?,?,?,?,?,?,?,?,?)');$st->execute([$judul,$slug,$kategori,$ringkasan,$isi,$new,$penulis,$tanggal,$status]);out(true,'Berita berhasil ditambahkan.');}
 }
 if($action==='delete'){$id=(int)($_POST['id']??0);$q=$pdo->prepare('SELECT gambar FROM berita WHERE id=?');$q->execute([$id]);$old=$q->fetchColumn();if(!$old&&$q->rowCount()===0)out(false,'Berita tidak ditemukan.');$pdo->prepare('DELETE FROM berita WHERE id=?')->execute([$id]);if($old&&is_file($uploadDir.basename($old)))@unlink($uploadDir.basename($old));out(true,'Berita berhasil dihapus.');}
 out(false,'Aksi tidak dikenal.');
}catch(Throwable $e){out(false,$e->getMessage());}
