<?php use_helper('I18N'); ?>
<div id="sf_admin_container">
<h1>Informe: <?php echo $informe->getNombre(); ?> para <?php echo $alumno->getApellido().", ".$alumno->getNombre();?></h1>

<?php if ($sf_request->hasErrors()) {?>
<div class="form-errors">
<h2><?php echo __('There are some errors that prevent the form to validate') ?></h2>
<ul>
<?php foreach ($sf_request->getErrorNames() as $name) { ?>
  <li><?php echo $sf_request->getError($name) ?></li>
<?php } ?>
</ul>
</div>
<?php } ?> 
<?php echo form_tag('informes/mostrar', 'method=get id=sf_admin_edit_form name=sf_admin_edit_form multipart=true') ?>
<legend>Debe completar los siguiente datos</legend>

<fieldset id="sf_fieldset_none" class="">



        <?php 
            foreach($variables as $variable) {
                ?><div class="form-row"><?php
                echo label_for($variable, __($variable.':'));
                echo input_tag($variable);
                ?></div><?php
            }
        ?>
    
</fieldset>

<?php echo input_hidden_tag('alumno_id', $alumno->getId()) ?>
<?php echo input_hidden_tag('id', $informe->getId()) ?>
<?php echo input_hidden_tag('v', '1') ?>

 <ul class="sf_admin_actions">
  <li><?php echo submit_tag(__('Mostrar'), array (
  'name' => 'Mostrar',
  'class' => 'sf_admin_action_save',
)) ?></li>
</ul>
</form>
</div>