$(function() {
    'use strict';

    // Debug: Check if admin_url is defined
    console.log('Admin URL:', admin_url);

    // Initialize suppliers table if element exists
    if ($('.table-suppliers').length > 0) {
        console.log('Found suppliers table element...');

        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable('.table-suppliers')) {
            console.log('DataTable already initialized, destroying first...');
            $('.table-suppliers').DataTable().destroy();
        }

        console.log('Initializing suppliers table...');
        
        var table = $('.table-suppliers').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": admin_url + 'suppliers/table',
                "type": "GET",
                "dataType": "json",
                "data": function(d) {
                    console.log('Sending data to server:', d);
                    return d;
                },
                "dataSrc": function(json) {
                    console.log('Received data from server:', json);
                    if (json.error) {
                        console.error('Server error:', json.error);
                        alert('שגיאה בטעינת הנתונים: ' + json.error);
                        return [];
                    }
                    return json.data || [];
                },
                "error": function(xhr, error, thrown) {
                    console.error('DataTable AJAX Error Details:');
                    console.error('- Error:', error);
                    console.error('- Thrown:', thrown);
                    console.error('- Status:', xhr.status);
                    console.error('- Response Text:', xhr.responseText);
                    console.error('- Ready State:', xhr.readyState);

                    // Remove loading class on error
                    $('#DataTables_Table_0_wrapper').removeClass('table-loading');
                    $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');

                    // Try to show meaningful error
                    var errorMsg = 'שגיאה בטעינת הנתונים';
                    if (xhr.responseText) {
                        try {
                            var parsed = JSON.parse(xhr.responseText);
                            if (parsed.error) {
                                errorMsg += ': ' + parsed.error;
                            }
                        } catch (e) {
                            console.error('Failed to parse error response:', e);
                            if (xhr.responseText.length > 0) {
                                errorMsg += ': תגובה לא תקינה מהשרת';
                                console.error('Raw response:', xhr.responseText.substring(0, 500));
                            } else {
                                errorMsg += ': תגובה ריקה מהשרת';
                            }
                        }
                    }

                    // Show user-friendly error
                    $('.table-suppliers').closest('.panel-body').prepend(
                        '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        '<strong>שגיאה:</strong> ' + errorMsg +
                        '</div>'
                    );
                }
            },
            "columns": [
                { "data": 0, "title": "#", "width": "5%" },
                { "data": 1, "title": "חברה", "width": "30%" },
                { "data": 2, "title": "איש קשר ראשי", "width": "25%" },
                { "data": 3, "title": "תאריך יצירה", "width": "20%" },
                { "data": 4, "title": "פעולות", "orderable": false, "width": "20%" }
            ],
            "order": [[ 0, "desc" ]],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "processing": "טוען נתונים...",
                "emptyTable": "אין ספקים להצגה",
                "zeroRecords": "לא נמצאו תוצאות",
                "loadingRecords": "טוען...",
                "info": "מציג _START_ עד _END_ מתוך _TOTAL_ רשומות",
                "infoEmpty": "מציג 0 עד 0 מתוך 0 רשומות",
                "infoFiltered": "(מסונן מתוך _MAX_ רשומות)",
                "lengthMenu": "הצג _MENU_ רשומות",
                "search": "חיפוש:",
                "paginate": {
                    "first": "ראשון",
                    "last": "אחרון",
                    "next": "הבא",
                    "previous": "הקודם"
                }
            },
            "responsive": true,
            "autoWidth": false,
            "initComplete": function(settings, json) {
                console.log('DataTable initialization completed');
                // Remove loading class when table is fully loaded
                $('#DataTables_Table_0_wrapper').removeClass('table-loading');
                $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
                
                // Show the table
                $('.table-suppliers').show();
                $(this).show();
            }
        });

        // Debug: Log successful initialization
        table.on('init.dt', function() {
            console.log('DataTable initialized successfully');
            // Remove loading class
            $('#DataTables_Table_0_wrapper').removeClass('table-loading');
            $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
        });

        table.on('xhr.dt', function(e, settings, json, xhr) {
            console.log('DataTable XHR completed:', json);
            // Remove loading class after successful load
            $('#DataTables_Table_0_wrapper').removeClass('table-loading');
            $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
        });

        table.on('error.dt', function(e, settings, techNote, message) {
            console.error('DataTable error:', message, techNote);
            // Remove loading class on error
            $('#DataTables_Table_0_wrapper').removeClass('table-loading');
            $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
        });

        table.on('draw.dt', function() {
            console.log('DataTable draw completed');
            // Remove loading class after draw
            $('#DataTables_Table_0_wrapper').removeClass('table-loading');
            $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
        });

        console.log('Table setup completed');
    } else {
        console.log('No .table-suppliers element found');
    }

    // Supplier form validation
    if ($('#supplier-form').length > 0) {
        appValidateForm($('#supplier-form'), {
            company: 'required'
        });
    }

    // Contact form validation
    if ($('#contact-form').length > 0) {
        appValidateForm($('#contact-form'), {
            firstname: 'required',
            lastname: 'required',
            email: {
                required: true,
                email: true
            }
        });
    }

    // Force remove loading class after page load
    $(document).ready(function() {
        setTimeout(function() {
            console.log('Force removing table-loading class...');
            $('#DataTables_Table_0_wrapper').removeClass('table-loading');
            $('.table-suppliers').closest('.dataTables_wrapper').removeClass('table-loading');
            $('.dataTables_wrapper').removeClass('table-loading');
            
            // Show table if hidden
            $('.table-suppliers').show();
            $('.dataTables_wrapper').show();
        }, 2000);
    });

    console.log('Suppliers JavaScript initialized');
});