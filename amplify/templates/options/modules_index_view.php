<div class="amp-container">
  <h2 class="amp-header">Modules</h2>

  <div class="amp-content">
    <table class="amp-table">
      <tr>
        <th>Module</th>
        <th>Actions</th>
      </tr>

      <?php foreach ($modules as $name => $module) { ?>
      <tr class="<?php echo $this->cycle->next('default', array('odd', 'even')) ?>">
        <td><?php echo Simplify_Inflector::titleize($name) ?></td>

        <td style="width:1%;">
          <?php if ($module['active']) { ?>
            <a href="<?= Simplify_URL::make(null, array('action' => 'deactivate', 'name' => Simplify_Inflector::titleize($name))) ?>">Deactivate</a>
          <?php } else { ?>
            <a href="<?= Simplify_URL::make(null, array('action' => 'activate', 'name' => Simplify_Inflector::titleize($name))) ?>">Activate</a>
          <?php } ?>
        </td>
      </tr>
      <?php } ?>
    </table>
  </div>
</div>