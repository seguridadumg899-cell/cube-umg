<?php
require __DIR__.'/db.php';
if(!is_logged()) redirect('login.php');

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['complete_task'])){
    $id=intval($_POST['task_id']);
    $st=$pdo->prepare('UPDATE tasks SET status="completed" WHERE id=? AND assigned_to=?');
    $st->execute([$id,$_SESSION['uid']]);
  }
  if(isset($_POST['add_note'])){
    $id=intval($_POST['task_id']);
    $note=trim($_POST['note']);
    if($note!==''){
      $st=$pdo->prepare('INSERT INTO task_notes(task_id,user_id,note) VALUES(?,?,?)');
      $st->execute([$id,$_SESSION['uid'],$note]);
    }
  }
}

$tasks=$pdo->prepare('SELECT * FROM tasks WHERE assigned_to=? ORDER BY created_at DESC');
$tasks->execute([$_SESSION['uid']]);
$tasks=$tasks->fetchAll();

$pending=array_filter($tasks, fn($t)=>$t['status']!='completed');
$completed=array_filter($tasks, fn($t)=>$t['status']=='completed');

include 'head.php'; include 'nav.php';
?>
<main class="max-w-6xl mx-auto p-4">
  <h1 class="text-2xl font-semibold mb-4">Mis tareas</h1>

  <div x-data="{tab:'pending'}" class="space-y-6" x-cloak>
    <div class="flex gap-2">
      <button @click="tab='pending'" :class="tab==='pending'?'bg-slate-900 text-white':'bg-slate-100 text-slate-700'" class="px-4 py-2 rounded-lg font-medium">Pendientes</button>
      <button @click="tab='completed'" :class="tab==='completed'?'bg-slate-900 text-white':'bg-slate-100 text-slate-700'" class="px-4 py-2 rounded-lg font-medium">Completadas</button>
    </div>

    <!-- Pendientes -->
    <div x-show="tab==='pending'" class="grid md:grid-cols-2 gap-6">
      <?php if(!$pending): ?>
        <div class="text-slate-500 col-span-2">No tienes tareas pendientes 🎉</div>
      <?php endif; ?>
      <?php foreach($pending as $t): 
        $notes=$pdo->prepare('SELECT n.*, u.name FROM task_notes n JOIN users u ON u.id=n.user_id WHERE n.task_id=? ORDER BY n.created_at DESC');
        $notes->execute([$t['id']]);
        $notes=$notes->fetchAll();
      ?>
      <div class="bg-white rounded-2xl shadow p-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold"><?=$t['title']?></h2>
            <div class="text-sm text-slate-600"><?=$t['description']?></div>
          </div>
          <div class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700"><?=$t['status']?></div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
          <div><span class="text-slate-500">Inicio:</span> <?=$t['start_date']?></div>
          <div><span class="text-slate-500">Vence:</span> <?=$t['due_date']?></div>
        </div>

        <!-- Botón completar -->
        <form method="post" class="mt-4 flex gap-2">
          <input type="hidden" name="task_id" value="<?=$t['id']?>">
          <input type="hidden" name="complete_task" value="1">
          <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Marcar completada</button>
        </form>

        <!-- Agregar nota -->
        <div class="mt-4">
          <form method="post" class="flex gap-2">
            <input type="hidden" name="task_id" value="<?=$t['id']?>">
            <input type="hidden" name="add_note" value="1">
            <input name="note" placeholder="Agregar nota" class="flex-1 border rounded-lg px-3 py-2"/>
            <button class="px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700">Guardar</button>
          </form>
        </div>

        <!-- Notas -->
        <?php if($notes): ?>
        <div class="mt-4 border-t pt-3">
          <div class="text-sm font-medium mb-2">Notas</div>
          <ul class="space-y-2">
            <?php foreach($notes as $n): ?>
            <li class="text-sm"><span class="font-medium"><?=$n['name']?></span>: <?=htmlspecialchars($n['note'])?> <span class="text-xs text-slate-500">(<?=$n['created_at']?>)</span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Completadas -->
    <div x-show="tab==='completed'" class="grid md:grid-cols-2 gap-6">
      <?php if(!$completed): ?>
        <div class="text-slate-500 col-span-2">No tienes tareas completadas.</div>
      <?php endif; ?>
      <?php foreach($completed as $t): 
        $notes=$pdo->prepare('SELECT n.*, u.name FROM task_notes n JOIN users u ON u.id=n.user_id WHERE n.task_id=? ORDER BY n.created_at DESC');
        $notes->execute([$t['id']]);
        $notes=$notes->fetchAll();
      ?>
      <div class="bg-white rounded-2xl shadow p-5 opacity-90">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold line-through"><?=$t['title']?></h2>
            <div class="text-sm text-slate-600"><?=$t['description']?></div>
          </div>
          <div class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Completada</div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
          <div><span class="text-slate-500">Inicio:</span> <?=$t['start_date']?></div>
          <div><span class="text-slate-500">Finalizó:</span> <?=$t['due_date']?></div>
        </div>

        <?php if($notes): ?>
        <div class="mt-4 border-t pt-3">
          <div class="text-sm font-medium mb-2">Notas anteriores</div>
          <ul class="space-y-2">
            <?php foreach($notes as $n): ?>
            <li class="text-sm"><span class="font-medium"><?=$n['name']?></span>: <?=htmlspecialchars($n['note'])?> <span class="text-xs text-slate-500">(<?=$n['created_at']?>)</span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php include 'footer.php'; ?>
