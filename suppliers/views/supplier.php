<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open(admin_url('suppliers/supplier/' . (isset($supplier) ? $supplier->userid : '')), ['id' => 'supplier-form']); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        
                        <!-- Main Supplier Form -->
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($supplier) ? $supplier->company : ''); ?>
                                <?php echo render_input('company', 'supplier_company', $value, 'text', ['required' => true]); ?>

                                <?php $value = (isset($supplier) ? $supplier->vat : ''); ?>
                                <?php echo render_input('vat', 'supplier_vat_number', $value); ?>

                                <?php $value = (isset($supplier) ? $supplier->phonenumber : ''); ?>
                                <?php echo render_input('phonenumber', 'supplier_phonenumber', $value); ?>

                                <?php $value = (isset($supplier) ? $supplier->website : ''); ?>
                                <?php echo render_input('website', 'supplier_website', $value); ?>

                                <?php $value = (isset($supplier) ? $supplier->address : ''); ?>
                                <?php echo render_textarea('address', 'supplier_address', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($supplier) ? $supplier->city : ''); ?>
                                <?php echo render_input('city', 'supplier_city', $value); ?>

                                <?php $value = (isset($supplier) ? $supplier->state : ''); ?>
                                <?php echo render_input('state', 'supplier_state', $value); ?>

                                <?php $value = (isset($supplier) ? $supplier->zip : ''); ?>
                                <?php echo render_input('zip', 'supplier_zip', $value); ?>

                                <?php $selected = (isset($supplier) ? $supplier->country : ''); ?>
                                <?php echo render_select('country', get_all_countries(), ['country_id', 'short_name'], 'supplier_country', $selected); ?>

                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="active" id="active" <?php if (isset($supplier) && $supplier->active == 1) { echo 'checked'; } ?>>
                                        <label for="active"><?php echo _l('supplier_active'); ?></label>
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

        <?php if (isset($supplier)) { ?>
        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                    <?php echo _l('supplier_profile'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                                    <?php echo _l('supplier_contacts'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                                    <?php echo _l('billing_shipping'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#customers" aria-controls="customers" role="tab" data-toggle="tab">
                                    <?php echo _l('related_customers'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">
                                    <?php echo _l('notes'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                                    <?php echo _l('attachments'); ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Profile Tab -->
                            <div role="tabpanel" class="tab-pane active" id="profile">
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <h4><?php echo _l('supplier_profile'); ?></h4>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><strong><?php echo _l('supplier_company'); ?>:</strong></td>
                                                    <td><?php echo $supplier->company; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo _l('supplier_vat_number'); ?>:</strong></td>
                                                    <td><?php echo $supplier->vat; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo _l('supplier_phonenumber'); ?>:</strong></td>
                                                    <td><?php echo $supplier->phonenumber; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo _l('supplier_website'); ?>:</strong></td>
                                                    <td>
                                                        <?php if (!empty($supplier->website)) { ?>
                                                            <a href="<?php echo $supplier->website; ?>" target="_blank"><?php echo $supplier->website; ?></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo _l('date_created'); ?>:</strong></td>
                                                    <td><?php echo _d($supplier->datecreated); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><?php echo _l('address'); ?></h4>
                                        <address>
                                            <?php echo $supplier->address; ?><br>
                                            <?php echo $supplier->city; ?>, <?php echo $supplier->state; ?> <?php echo $supplier->zip; ?><br>
                                            <?php if (isset($supplier->country_name)) { echo $supplier->country_name; } ?>
                                        </address>
                                    </div>
                                </div>
                            </div>

                            <!-- Contacts Tab -->
                            <div role="tabpanel" class="tab-pane" id="contacts">
                                <div class="_buttons" style="margin-top: 20px;">
                                    <a href="<?php echo admin_url('suppliers/contact/' . $supplier->userid); ?>" class="btn btn-info pull-left">
                                        <?php echo _l('new_supplier_contact'); ?>
                                    </a>
                                    <div class="clearfix"></div>
                                </div>

                                <?php if (!empty($supplier->contacts)) { ?>
                                <table class="table dt-table" style="margin-top: 20px;">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('contact_firstname'); ?></th>
                                            <th><?php echo _l('contact_lastname'); ?></th>
                                            <th><?php echo _l('contact_email'); ?></th>
                                            <th><?php echo _l('contact_phonenumber'); ?></th>
                                            <th><?php echo _l('contact_position'); ?></th>
                                            <th><?php echo _l('primary_contact'); ?></th>
                                            <th><?php echo _l('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($supplier->contacts as $contact) { ?>
                                        <tr>
                                            <td><?php echo $contact['firstname']; ?></td>
                                            <td><?php echo $contact['lastname']; ?></td>
                                            <td><?php echo $contact['email']; ?></td>
                                            <td><?php echo $contact['phonenumber']; ?></td>
                                            <td><?php echo $contact['title']; ?></td>
                                            <td>
                                                <?php if ($contact['is_primary'] == 1) { ?>
                                                    <span class="label label-success"><?php echo _l('yes'); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-default"><?php echo _l('no'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('suppliers/contact/' . $supplier->userid . '/' . $contact['id']); ?>" class="btn btn-default btn-icon">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                                <a href="<?php echo admin_url('suppliers/delete_contact/' . $supplier->userid . '/' . $contact['id']); ?>" class="btn btn-danger btn-icon _delete">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php } else { ?>
                                <p style="margin-top: 20px;"><?php echo _l('no_contacts_found'); ?></p>
                                <?php } ?>
                            </div>

                            <!-- Billing and Shipping Tab -->
                            <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
                                <?php echo form_open(admin_url('suppliers/supplier/' . $supplier->userid), ['id' => 'billing-shipping-form']); ?>
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <h4><?php echo _l('supplier_billing_address'); ?></h4>
                                        <?php $value = (isset($supplier) ? $supplier->billing_street : ''); ?>
                                        <?php echo render_textarea('billing_street', 'supplier_billing_street', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->billing_city : ''); ?>
                                        <?php echo render_input('billing_city', 'supplier_billing_city', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->billing_state : ''); ?>
                                        <?php echo render_input('billing_state', 'supplier_billing_state', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->billing_zip : ''); ?>
                                        <?php echo render_input('billing_zip', 'supplier_billing_zip', $value); ?>

                                        <?php $selected = (isset($supplier) ? $supplier->billing_country : ''); ?>
                                        <?php echo render_select('billing_country', get_all_countries(), ['country_id', 'short_name'], 'supplier_billing_country', $selected); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><?php echo _l('supplier_shipping_address'); ?></h4>
                                        <?php $value = (isset($supplier) ? $supplier->shipping_street : ''); ?>
                                        <?php echo render_textarea('shipping_street', 'supplier_shipping_street', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->shipping_city : ''); ?>
                                        <?php echo render_input('shipping_city', 'supplier_shipping_city', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->shipping_state : ''); ?>
                                        <?php echo render_input('shipping_state', 'supplier_shipping_state', $value); ?>

                                        <?php $value = (isset($supplier) ? $supplier->shipping_zip : ''); ?>
                                        <?php echo render_input('shipping_zip', 'supplier_shipping_zip', $value); ?>

                                        <?php $selected = (isset($supplier) ? $supplier->shipping_country : ''); ?>
                                        <?php echo render_select('shipping_country', get_all_countries(), ['country_id', 'short_name'], 'supplier_shipping_country', $selected); ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                                <?php echo form_close(); ?>
                            </div>

                            <!-- Customers Tab -->
                            <div role="tabpanel" class="tab-pane" id="customers">
                                <div style="margin-top: 20px;">
                                    <h4><?php echo _l('add_customer_relation'); ?></h4>
                                    <form id="customer-relation-form" data-supplier-id="<?php echo $supplier->userid; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo render_select('customer_id', $customers, ['userid', 'company'], 'select_customer', '', [], [], '', '', false); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-info" style="margin-top: 25px;">
                                                    <?php echo _l('add_relation'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Alert container for AJAX messages -->
                                <div id="customer-relation-alerts" style="margin-top: 15px;"></div>

                                <div id="customers-table-container">
                                    <?php if (!empty($supplier->customers)) { ?>
                                    <table class="table dt-table" style="margin-top: 20px;" id="customers-relations-table">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('customer_name'); ?></th>
                                                <th><?php echo _l('relation_date'); ?></th>
                                                <th><?php echo _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="customers-relations-tbody">
                                            <?php foreach ($supplier->customers as $customer) { ?>
                                            <tr data-relation-id="<?php echo $customer['relation_id']; ?>">
                                                <td>
                                                    <a href="<?php echo admin_url('clients/client/' . $customer['userid']); ?>">
                                                        <?php echo $customer['company']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo _d($customer['relation_date']); ?></td>
                                                <td>
                                                    <button class="btn btn-danger btn-icon remove-relation-btn" data-relation-id="<?php echo $customer['relation_id']; ?>" data-supplier-id="<?php echo $supplier->userid; ?>">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php } else { ?>
                                    <p id="no-relations-message" style="margin-top: 20px;"><?php echo _l('no_customer_relations_found'); ?></p>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div role="tabpanel" class="tab-pane" id="notes">
                                <div style="margin-top: 20px;">
                                    <h4><?php echo _l('notes'); ?></h4>
                                    <div class="notes-area">
                                        <textarea class="form-control" rows="10" placeholder="<?php echo _l('add_note'); ?>"></textarea>
                                        <button class="btn btn-info pull-right" style="margin-top: 10px;"><?php echo _l('add_note'); ?></button>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments Tab -->
                            <div role="tabpanel" class="tab-pane" id="attachments">
                                <div style="margin-top: 20px;">
                                    <h4><?php echo _l('attachments'); ?></h4>
                                    <div class="attachments-area">
                                        <div class="dropzone" id="supplier-attachments">
                                            <div class="dz-message">
                                                <?php echo _l('drop_files_here_to_upload'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php init_tail(); ?>

<script>
$(function() {
    appValidateForm($('#supplier-form'), {
        company: 'required'
    });

    // AJAX form for adding customer relations
    $('#customer-relation-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var supplierId = $form.data('supplier-id');
        var customerId = $form.find('select[name="customer_id"]').val();

        if (!customerId) {
            showAlert('danger', '<?php echo _l("select_customer"); ?>');
            return;
        }

        // Disable submit button
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l("processing"); ?>');

        $.ajax({
            url: admin_url + 'suppliers/add_customer_relation/' + supplierId,
            type: 'POST',
            data: { customer_id: customerId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Add new row to table or create table if it doesn't exist
                    if ($('#customers-relations-table').length === 0) {
                        $('#no-relations-message').remove();
                        var tableHtml = '<table class="table dt-table" style="margin-top: 20px;" id="customers-relations-table">' +
                            '<thead><tr>' +
                            '<th><?php echo _l("customer_name"); ?></th>' +
                            '<th><?php echo _l("relation_date"); ?></th>' +
                            '<th><?php echo _l("options"); ?></th>' +
                            '</tr></thead>' +
                            '<tbody id="customers-relations-tbody"></tbody>' +
                            '</table>';
                        $('#customers-table-container').html(tableHtml);
                    }
                    
                    // Add new row
                    var newRow = '<tr data-relation-id="' + response.customer.relation_id + '">' +
                        '<td><a href="' + admin_url + 'clients/client/' + response.customer.userid + '">' + response.customer.company + '</a></td>' +
                        '<td>' + moment(response.customer.relation_date).format('<?php echo get_option("dateformat"); ?>') + '</td>' +
                        '<td><button class="btn btn-danger btn-icon remove-relation-btn" data-relation-id="' + response.customer.relation_id + '" data-supplier-id="' + supplierId + '"><i class="fa fa-remove"></i></button></td>' +
                        '</tr>';
                    $('#customers-relations-tbody').append(newRow);
                    
                    // Reset form
                    $form.find('select[name="customer_id"]').val('').trigger('change');
                } else {
                    showAlert('warning', response.message);
                }
            },
            error: function() {
                showAlert('danger', '<?php echo _l("something_went_wrong"); ?>');
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false).html('<?php echo _l("add_relation"); ?>');
            }
        });
    });

    // AJAX for removing customer relations
    $(document).on('click', '.remove-relation-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var relationId = $btn.data('relation-id');
        var supplierId = $btn.data('supplier-id');
        var $row = $btn.closest('tr');

        if (!confirm('<?php echo _l("are_you_sure"); ?>')) {
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: admin_url + 'suppliers/remove_customer_relation/' + supplierId + '/' + relationId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#customers-relations-tbody tr').length === 0) {
                            $('#customers-relations-table').remove();
                            $('#customers-table-container').html('<p id="no-relations-message" style="margin-top: 20px;"><?php echo _l("no_customer_relations_found"); ?></p>');
                        }
                    });
                } else {
                    showAlert('danger', response.message);
                    $btn.prop('disabled', false).html('<i class="fa fa-remove"></i>');
                }
            },
            error: function() {
                showAlert('danger', '<?php echo _l("something_went_wrong"); ?>');
                $btn.prop('disabled', false).html('<i class="fa fa-remove"></i>');
            }
        });
    });

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            message +
            '</div>';
        $('#customer-relation-alerts').html(alertHtml);

        // Auto hide after 5 seconds
        setTimeout(function() {
            $('#customer-relation-alerts .alert').fadeOut();
        }, 5000);
    }
});
</script>