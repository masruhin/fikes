<?php
require_once __DIR__ . '/../../config/auth.php';
wajib_login();
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
ob_start();
function out($success,$message='',$data=null){while(ob_get_level()>0)ob_end_clean();echo json_encode(['success'=>$success,'message'=>$message,'data'=>$data],JSON_UNESCAPED_UNICODE);exit;}
try{
 $action=$_GET['action']??$_POST['action']??'';
 if($action==='prodi'){
   $q=$pdo->query("SELECT id,kode_prodi,nama,jenjang,gelar FROM program_studi WHERE status='aktif' ORDER BY nama ASC");
   out(true,'Data program studi berhasil dimuat.',$q->fetchAll());
 }
 if($action==='list'){
   $search=trim($_GET['search']??''); $status=$_GET['status']??'';
   $sql="SELECT s.*, p.kode_prodi,p.nama AS nama_prodi,p.jenjang,p.gelar FROM sertifikat_akreditasi s LEFT JOIN program_studi p ON p.id=CAST(s.id_prodi AS UNSIGNED) WHERE 1=1";
   $params=[];
   if($search!==''){ $sql.=" AND (p.nama LIKE :q OR p.kode_prodi LIKE :q OR s.nomor_sk LIKE :q OR s.peringkat LIKE :q OR s.id_institusi LIKE :q OR s.id_lembaga LIKE :q)"; $params['q']='%'.$search.'%'; }
   if($status!==''){ $sql.=" AND s.status_aktif=:status"; $params['status']=(int)$status; }
   $sql.=" ORDER BY s.tanggal_kadaluarsa DESC,s.id_sertifikat DESC";
   $q=$pdo->prepare($sql);$q->execute($params);out(true,'',$q->fetchAll());
 }
 if($action==='get'){
   $id=(int)($_GET['id']??0);$q=$pdo->prepare("SELECT * FROM sertifikat_akreditasi WHERE id_sertifikat=? LIMIT 1");$q->execute([$id]);$x=$q->fetch();if(!$x)out(false,'Data sertifikat tidak ditemukan.');out(true,'',$x);
 }
 if($action==='save'){
   $id=(int)($_POST['id_sertifikat']??0);$idProdi=(int)($_POST['id_prodi']??0);if($idProdi<=0)out(false,'Program Studi wajib dipilih.');
   $q=$pdo->prepare("SELECT id FROM program_studi WHERE id=? AND status='aktif' LIMIT 1");$q->execute([$idProdi]);if(!$q->fetchColumn())out(false,'Program Studi tidak ditemukan atau tidak aktif.');
   $fields=['id_institusi','id_lembaga','nomor_sk','peringkat','tanggal_sk','tanggal_kadaluarsa','status_aktif'];$v=[];foreach($fields as $f)$v[$f]=trim($_POST[$f]??'');
   $v['status_aktif']=(int)($v['status_aktif']!==''?$v['status_aktif']:1);$v['tanggal_sk']=$v['tanggal_sk']?:null;$v['tanggal_kadaluarsa']=$v['tanggal_kadaluarsa']?:null;
   $oldFile='';if($id){$q=$pdo->prepare("SELECT file_sertifikat FROM sertifikat_akreditasi WHERE id_sertifikat=?");$q->execute([$id]);$oldFile=(string)$q->fetchColumn();}
   $newFile=$oldFile;
   if(isset($_FILES['file_sertifikat']) && $_FILES['file_sertifikat']['error']!==UPLOAD_ERR_NO_FILE){$f=$_FILES['file_sertifikat'];if($f['error']!==UPLOAD_ERR_OK)out(false,'Upload file gagal.');if($f['size']>10*1024*1024)out(false,'Ukuran file maksimal 10 MB.');$ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));$allow=['pdf','jpg','jpeg','xls','xlsx','doc','docx','ppt','pptx'];if(!in_array($ext,$allow,true))out(false,'Format file tidak diizinkan.');$dir=__DIR__.'/../../uploads/upload-sertifikat';if(!is_dir($dir))mkdir($dir,0775,true);$newFile=date('Ymd_His').'_'.bin2hex(random_bytes(4)).'.'.$ext;if(!move_uploaded_file($f['tmp_name'],$dir.'/'.$newFile))out(false,'File tidak dapat disimpan.');if($oldFile && is_file($dir.'/'.$oldFile))@unlink($dir.'/'.$oldFile);}
   if($id){$sql="UPDATE sertifikat_akreditasi SET id_prodi=?,id_institusi=?,id_lembaga=?,nomor_sk=?,peringkat=?,tanggal_sk=?,tanggal_kadaluarsa=?,file_sertifikat=?,status_aktif=? WHERE id_sertifikat=?";$pdo->prepare($sql)->execute([$idProdi,$v['id_institusi'],$v['id_lembaga'],$v['nomor_sk'],$v['peringkat'],$v['tanggal_sk'],$v['tanggal_kadaluarsa'],$newFile,$v['status_aktif'],$id]);$msg='Sertifikat akreditasi berhasil diperbarui.';}else{$sql="INSERT INTO sertifikat_akreditasi(id_prodi,id_institusi,id_lembaga,nomor_sk,peringkat,tanggal_sk,tanggal_kadaluarsa,file_sertifikat,status_aktif) VALUES(?,?,?,?,?,?,?,?,?)";$pdo->prepare($sql)->execute([$idProdi,$v['id_institusi'],$v['id_lembaga'],$v['nomor_sk'],$v['peringkat'],$v['tanggal_sk'],$v['tanggal_kadaluarsa'],$newFile,$v['status_aktif']]);$msg='Sertifikat akreditasi berhasil ditambahkan.';}
   out(true,$msg);
 }
 if($action==='delete'){$id=(int)($_POST['id_sertifikat']??0);$q=$pdo->prepare("SELECT file_sertifikat FROM sertifikat_akreditasi WHERE id_sertifikat=?");$q->execute([$id]);$file=$q->fetchColumn();$pdo->prepare("DELETE FROM sertifikat_akreditasi WHERE id_sertifikat=?")->execute([$id]);if($q->rowCount()===0){}$dir=__DIR__.'/../../uploads/upload-sertifikat';if($file&&is_file($dir.'/'.$file))@unlink($dir.'/'.$file);out(true,'Sertifikat akreditasi berhasil dihapus.');}
 out(false,'Aksi tidak dikenali.');
}catch(Throwable $e){out(false,$e->getMessage());}
