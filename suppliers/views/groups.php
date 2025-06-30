<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('suppliers', '', 'create') || is_admin()) { ?>
                                <a href="#" class="btn btn-info pull-left display-block" data-toggle="modal" data-target="#supplier_group_modal">
                                    <?php echo _l('new_supplier_group'); ?>
                                </a>
                            <?php } ?>
                            <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-default pull-right">
                                <?php echo _l('suppliers'); ?>
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        
                        <table class="table dt-table table-suppliers-groups">
                            <thead>
                                <tr>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('name'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for groups -->
<div class="modal fade" id="supplier_group_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('supplier_group'); ?></h4>
            </div>
            <?php echo form_open(admin_url('suppliers/group')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'supplier_group_name', '', 'text', ['required' => true]); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-suppliers-groups', admin_url + 'suppliers/groups_table', [2], [2]);
});

function edit_group(invoker, id) {
    var name = $(invoker).parents('tr').find('td').eq(1).text();
    $('#supplier_group_modal').modal('show');
    $('#supplier_group_modal input[name="name"]').val(name);
    $('#supplier_group_modal form').attr('action', admin_url + 'suppliers/group/' + id);
}
</script>