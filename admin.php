<?php
require __DIR__.'/db.php';
if(!is_logged()||!is_admin()) redirect('login.php');
$tab=$_GET['tab']??'users';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['create_user'])){
    $name=trim($_POST['name']); $email=trim($_POST['email']); $role=$_POST['role']; $pass=password_hash($_POST['password'], PASSWORD_DEFAULT);
    $st=$pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)'); $st->execute([$name,$email,$pass,$role]);
    redirect('admin.php?tab=users');
  }
  if(isset($_POST['create_task'])){
    $title=trim($_POST['title']); $desc=trim($_POST['description']); $ass=intval($_POST['assigned_to']); $sd=$_POST['start_date']?:NULL; $dd=$_POST['due_date']?:NULL;
    $st=$pdo->prepare('INSERT INTO tasks(title,description,assigned_to,start_date,due_date,created_by) VALUES(?,?,?,?,?,?)');
    $st->execute([$title,$desc,$ass,$sd,$dd,$_SESSION['uid']]); redirect('admin.php?tab=tasks');
  }
  if(isset($_POST['update_task_status'])){
    $id=intval($_POST['task_id']); $status=$_POST['status'];
    $st=$pdo->prepare('UPDATE tasks SET status=? WHERE id=?'); $st->execute([$status,$id]); redirect('admin.php?tab=tasks');
  }
}
$users=$pdo->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();
$tasks=$pdo->query('SELECT t.*, u.name as assignee FROM tasks t JOIN users u ON u.id=t.assigned_to ORDER BY t.created_at DESC')->fetchAll();
include 'head.php'; include 'nav.php';
?>
<main class="max-w-6xl mx-auto p-4">
  <div class="bg-white rounded-2xl shadow p-4 mb-4 flex gap-2">
    <a class="px-3 py-1.5 rounded-lg <?= $tab==='users'?'bg-slate-900 text-white':'bg-slate-100'?>" href="?tab=users">Usuarios</a>
    <a class="px-3 py-1.5 rounded-lg <?= $tab==='tasks'?'bg-slate-900 text-white':'bg-slate-100'?>" href="?tab=tasks">Tareas</a>
  </div>
  <?php if($tab==='users'): ?>
  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Crear usuario</h2>
      <form method="post" class="space-y-3">
        <input type="hidden" name="create_user" value="1">
        <div><label class="text-sm">Nombre</label><input name="name" required class="mt-1 w-full border rounded-lg px-3 py-2"></div>
        <div><label class="text-sm">Email</label><input name="email" type="email" required class="mt-1 w-full border rounded-lg px-3 py-2"></div>
        <div><label class="text-sm">Rol</label>
          <select name="role" class="mt-1 w-full border rounded-lg px-3 py-2">
            <option value="user">Usuario</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div><label class="text-sm">Contraseña</label><input name="password" type="password" required class="mt-1 w-full border rounded-lg px-3 py-2"></div>
        <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Crear</button>
      </form>
    </div>
    <div class="bg-white rounded-2xl shadow p-6 overflow-x-auto">
      <h2 class="text-lg font-semibold mb-4">Usuarios</h2>
      <table class="min-w-full text-sm">
        <thead><tr class="text-left text-slate-500"><th class="py-2">Nombre</th><th>Email</th><th>Rol</th><th>Alta</th></tr></thead>
        <tbody>
          <?php foreach($users as $u): ?>
          <tr class="border-t"><td class="py-2"><?=$u['name']?></td><td><?=$u['email']?></td><td><?=$u['role']?></td><td><?=$u['created_at']?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else: ?>
  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-lg font-semibold mb-4">Crear tarea</h2>
      <form method="post" class="space-y-3">
        <input type="hidden" name="create_task" value="1">
        <div><label class="text-sm">Título</label><input name="title" required class="mt-1 w-full border rounded-lg px-3 py-2"></div>
        <div><label class="text-sm">Observaciones</label><textarea name="description" class="mt-1 w-full border rounded-lg px-3 py-2" rows="3"></textarea></div>
        <div><label class="text-sm">Asignado a</label>
          <select name="assigned_to" class="mt-1 w-full border rounded-lg px-3 py-2"><?php foreach($users as $u){echo '<option value="'.$u['id'].'">'.htmlspecialchars($u['name']).' ('.$u['email'].')</option>'; } ?></select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="text-sm">Inicio</label><input type="date" name="start_date" class="mt-1 w-full border rounded-lg px-3 py-2"></div>
          <div><label class="text-sm">Fecha máxima</label><input type="date" name="due_date" class="mt-1 w-full border rounded-lg px-3 py-2"></div>
        </div>
        <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Crear</button>
      </form>
    </div>
    <div class="bg-white rounded-2xl shadow p-6 overflow-x-auto">
      <h2 class="text-lg font-semibold mb-4">Tareas</h2>
      <table class="min-w-full text-sm">
        <thead><tr class="text-left text-slate-500"><th class="py-2">Título</th><th>Asignado</th><th>Inicio</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
        <tbody>
          <?php foreach($tasks as $t): ?>
          <tr class="border-t">
            <td class="py-2"><?=$t['title']?></td>
            <td><?=$t['assignee']?></td>
            <td><?=$t['start_date']?></td>
            <td><?=$t['due_date']?></td>
            <td><?=$t['status']?></td>
            <td>
              <form method="post" class="inline-flex gap-2">
                <input type="hidden" name="update_task_status" value="1">
                <input type="hidden" name="task_id" value="<?=$t['id']?>">
                <select name="status" class="border rounded-lg px-2 py-1">
                  <option <?=$t['status']=='pending'?'selected':''?> value="pending">Pendiente</option>
                  <option <?=$t['status']=='in_progress'?'selected':''?> value="in_progress">En curso</option>
                  <option <?=$t['status']=='completed'?'selected':''?> value="completed">Completada</option>
                </select>
                <button class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200">Guardar</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</main>
<?php include 'footer.php'; ?>
