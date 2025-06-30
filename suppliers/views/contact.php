<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open(admin_url('suppliers/contact/' . $supplier_id . '/' . (isset($contact) ? $contact->id : '')), ['id' => 'contact-form']); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($contact) ? $contact->firstname : ''); ?>
                                <?php echo render_input('firstname', 'contact_firstname', $value, 'text', ['required' => true]); ?>
                                
                                <?php $value = (isset($contact) ? $contact->lastname : ''); ?>
                                <?php echo render_input('lastname', 'contact_lastname', $value, 'text', ['required' => true]); ?>
                                
                                <?php $value = (isset($contact) ? $contact->title : ''); ?>
                                <?php echo render_input('title', 'contact_position', $value); ?>
                            </div>
                            
                            <div class="col-md-6">
                                <?php $value = (isset($contact) ? $contact->email : ''); ?>
                                <?php echo render_input('email', 'contact_email', $value, 'email', ['required' => true]); ?>
                                
                                <?php $value = (isset($contact) ? $contact->phonenumber : ''); ?>
                                <?php echo render_input('phonenumber', 'contact_phonenumber', $value); ?>
                                
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="is_primary" id="is_primary" <?php if (isset($contact) && $contact->is_primary == 1) { echo 'checked'; } ?>>
                                        <label for="is_primary"><?php echo _l('contact_primary'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('#contact-form'), {
            firstname: 'required',
            lastname: 'required',
            email: 'required'
        });
    });
</script>