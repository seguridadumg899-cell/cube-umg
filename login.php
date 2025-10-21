<?php
require __DIR__.'/db.php';
if(is_logged()) redirect('index.php');
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=trim($_POST['email']??'');
  $pass=$_POST['password']??'';
  $st=$pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=?');
  $st->execute([$email]);
  $u=$st->fetch();
  if($u && password_verify($pass,$u['password_hash'])){
    $_SESSION['uid']=$u['id']; $_SESSION['name']=$u['name']; $_SESSION['role']=$u['role'];
    redirect('index.php');
  } else { $err='Credenciales inválidas'; }
}
include 'head.php'; ?>
<div class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-semibold mb-1">Ingresar</h1>
    <p class="text-sm text-slate-600 mb-6">Accede para continuar</p>
    <?php if($err): ?><div class="mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-lg p-3"><?=$err?></div><?php endif; ?>
    <form method="post" class="space-y-4">
      <div>
        <label class="text-sm">Email</label>
        <input name="email" type="email" required class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring" />
      </div>
      <div>
        <label class="text-sm">Contraseña</label>
        <input name="password" type="password" required class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring" />
      </div>
      <button class="w-full mt-2 bg-slate-900 text-white rounded-lg px-4 py-2 hover:bg-slate-700">Entrar</button>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
