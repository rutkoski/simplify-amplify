<h3><?= $title ?></h3>

<?php if (! empty($filters)) { ?>
<form action="" method="GET" class="form-inline">
  <fieldset>
    <legend>Filters</legend>

    <?php foreach ($filters as $filter) { ?>
    <?= $filter['controls'] ?>
    <?php } ?>

    <input type="submit" value="Apply" class="btn"/>
  </fieldset>
</form>
<?php } ?>

<form action="" method="GET" class="form-inline">
  <?= $this->pager->show($pager) ?>

  <br/><br/>

    <ul class="thumbnails">
      <?php foreach ($data as &$row) { ?>
      <li class="img-polaroid" style="position:relative; width:170px; height:220px;">
        <p><?php echo $row['elements']['video_titulo']['controls']; ?></p>

        <div style="position:absolute; top:50px;">
          <input type="checkbox" name="<?= $row['name'] ?>" value="<?= $row['_id'] ?>" style="position:absolute; top:10px; left:14px;"/>
          <?php echo $row['elements']['video_banner']['controls']; ?>
        </div>

        <div style="position:absolute; bottom:0px;">
          <?= $this->menu->show($row['menu']) ?>
        </div>
      </li>
      <?php } ?>
    </ul>

  <?= $this->pager->show($pager) ?>

  <?php if (! empty($bulk)) { ?>
  <select name="formAction">
    <option value=""></option>
    <?php foreach ($bulk as $value => $label) { ?>
    <option value="<?= $value ?>"><?= $label ?></option>
    <?php } ?>
  </select>

  <input type="submit" value="Ok" class="btn" />
  <?php } ?>
</form>
