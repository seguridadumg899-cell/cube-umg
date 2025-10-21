<?php
?><header class="border-b bg-white">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="/index.php" class="text-xl font-semibold">Cube Tasks</a>
    <nav class="flex items-center gap-4">
      <?php if(is_logged()): ?>
        <span class="text-sm text-slate-600 hidden sm:inline">Hola, <?=$_SESSION['name']?></span>
        <?php if(is_admin()): ?><a class="text-sm px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-700" href="/admin.php">Admin</a><?php endif; ?>
        <a class="text-sm px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200" href="/tasks.php">Mis tareas</a>
        <a class="text-sm px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700" href="/logout.php">Salir</a>
      <?php else: ?>
        <a class="text-sm px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-700" href="/login.php">Ingresar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
