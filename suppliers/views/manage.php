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
                                <a href="<?php echo admin_url('suppliers/supplier'); ?>" class="btn btn-info pull-left display-block">
                                    <?php echo _l('new_supplier'); ?>
                                </a>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        
                        <!-- Suppliers Table -->
                        <table class="table dt-table table-suppliers" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo _l('the_number_sign'); ?></th>
                                    <th><?php echo _l('company'); ?></th>
                                    <th><?php echo _l('primary_contact'); ?></th>
                                    <th><?php echo _l('date_created'); ?></th>
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
<?php init_tail(); ?>

<script>
$(function() {
    console.log('Manage.php script loaded - suppliers table should be handled by suppliers.js');
    
    // Force remove loading class immediately
    setTimeout(function() {
        console.log('Force removing table-loading class from manage.php...');
        $('#DataTables_Table_0_wrapper').removeClass('table-loading');
        $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
        $('.dataTables_wrapper').removeClass('table-loading');
        
        // Show table elements
        $('.table-suppliers').show();
        $('.dataTables_wrapper').show();
        $('#DataTables_Table_0_wrapper').show();
    }, 500);
    
    // Additional check after 3 seconds
    setTimeout(function() {
        console.log('Final check - removing table-loading class...');
        $('#DataTables_Table_0_wrapper').removeClass('table-loading');
        $('.dataTables_wrapper').removeClass('table-loading');
        $('[id*="DataTables_Table"]').closest('.dataTables_wrapper').removeClass('table-loading');
        
        // Force display
        $('.table-suppliers').css({
            'opacity': '1',
            'visibility': 'visible',
            'display': 'table'
        });
        
        $('.dataTables_wrapper').css({
            'opacity': '1',
            'visibility': 'visible',
            'display': 'block'
        });
        
        $('#DataTables_Table_0_wrapper').css({
            'opacity': '1',
            'visibility': 'visible',
            'display': 'block'
        });
    }, 3000);
});
</script>