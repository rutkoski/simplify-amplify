<h2>Modules</h2>

<table class="table">
  <tr>
    <th>Module</th>
    <th>Actions</th>
  </tr>

  <?php foreach ($modules as $path => $module) { ?>
  <tr>
    <td><?= $module->getName() ?></td>

    <td style="width:1%;">
      <?php if (Amplify_Modules::isActive($path)) { ?>
        <a href="<?= Simplify_URL::make(null, array('deactivate' => 1, 'module' => $path)) ?>">Deactivate</a>
      <?php } else { ?>
        <a href="<?= Simplify_URL::make(null, array('activate' => 1, 'module' => $path)) ?>">Activate</a>
      <?php } ?>
    </td>
  </tr>
  <?php } ?>
</table>