<?php
// auto-generated by sfPropelAdmin
// date: 2007/02/14 11:18:57
?>
<ul class="sf_admin_actions">
      <li><?php echo submit_tag(__('save'), array (
  'name' => 'save',
  'class' => 'sf_admin_action_save',
)) ?></li>
    <li><?php echo submit_tag(__('save and add'), array (
  'name' => 'save_and_add',
  'class' => 'sf_admin_action_save_and_add',
)) ?></li>
    <li><?php echo button_to(__('list'), 'responsable/list?id='.$responsable->getId(), array (
  'class' => 'sf_admin_action_list',
)) ?></li>
    <li><?php echo button_to(__('Ir a Cuenta'), 'cuenta/verCompleta?id='.$responsable->getFkCuentaId(), array (
  'class' => 'sf_admin_action_cuenta_go',
)) ?></li>
</ul>
