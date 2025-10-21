<?php
require __DIR__.'/db.php';
if(!is_logged()) redirect('login.php');
include 'head.php'; include 'nav.php';
if(is_admin()){
  $uCount=$pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
  $tCount=$pdo->query('SELECT COUNT(*) c FROM tasks')->fetch()['c'];
  $open=$pdo->query("SELECT COUNT(*) c FROM tasks WHERE status!='completed'")->fetch()['c'];
  ?>
  <main class="max-w-6xl mx-auto p-4">
    <div class="grid md:grid-cols-3 gap-4">
      <div class="bg-white rounded-2xl shadow p-5"><div class="text-slate-500 text-sm">Usuarios</div><div class="text-3xl font-semibold"><?=$uCount?></div></div>
      <div class="bg-white rounded-2xl shadow p-5"><div class="text-slate-500 text-sm">Tareas</div><div class="text-3xl font-semibold"><?=$tCount?></div></div>
      <div class="bg-white rounded-2xl shadow p-5"><div class="text-slate-500 text-sm">Pendientes</div><div class="text-3xl font-semibold"><?=$open?></div></div>
    </div>
    <div class="mt-6">
      <a href="admin.php" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-700">Ir a Admin</a>
    </div>
  </main>
  <?php
} else {
  include 'tasks.php';
}
include 'footer.php';
