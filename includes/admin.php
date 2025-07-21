<?php
// Admin menu for HR
function elm_register_hr_leave_menu()
{
    add_menu_page(
        'Employee Leave Requests',
        'Leave Requests',
        'manage_options',
        'employee-leave-requests',
        'elm_render_leave_requests_page',
        'dashicons-calendar-alt',
        26
    );

    // Enqueue DataTables and other scripts
    add_action('admin_enqueue_scripts', 'elm_enqueue_admin_scripts');
}
add_action('admin_menu', 'elm_register_hr_leave_menu');

function elm_enqueue_admin_scripts($hook)
{
    if ($hook === 'toplevel_page_employee-leave-requests') {
        // DataTables CSS
        wp_enqueue_style('elm-datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');

        // DataTables JS
        wp_enqueue_script('elm-datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);

        // SweetAlert for nice alerts
        wp_enqueue_script('elm-sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), null, true);

        // Custom admin JS
        wp_enqueue_script('elm-admin-js', ELM_PLUGIN_URL . 'assets/admin.js', array('jquery', 'elm-datatables-js'), '1.0', true);
    }
}

function elm_render_leave_requests_page()
{
    global $wpdb;
    $table = $wpdb->prefix . 'employee_leaves_data';

    // Handle approval
    if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['leave_id'])) {
        $leave_id = intval($_GET['leave_id']);
        $wpdb->update($table, ['status' => 'Approved'], ['id' => $leave_id]);

        $leave = $wpdb->get_row("SELECT user_id FROM $table WHERE id = $leave_id");
        $user = get_userdata($leave->user_id);

        $subject = "Avaptech | Your Leave Request Has Been Approved";
        $message = "Dear {$user->display_name},\n\n";
        $message .= "We are pleased to inform you that your leave request has been approved.\n";
        $message .= "Please ensure all pending work is handed over before your leave begins.\n\n";
        $message .= "Best regards,\nHR Department";

        wp_mail($user->user_email, $subject, $message);

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Leave Approved",
                text: "The leave request has been approved and the employee has been notified.",
                icon: "success"
            });
        });
        </script>';
    }

    // Handle rejection with reason
    if (isset($_POST['elm_reject_leave']) && isset($_POST['leave_id']) && isset($_POST['rejection_reason'])) {
        $leave_id = intval($_POST['leave_id']);
        $rejection_reason = sanitize_textarea_field($_POST['rejection_reason']);

        $wpdb->update($table, ['status' => 'Rejected'], ['id' => $leave_id]);

        $leave = $wpdb->get_row("SELECT user_id FROM $table WHERE id = $leave_id");
        $user = get_userdata($leave->user_id);

        $subject = "Avaptech | Your Leave Request Has Been Rejected";
        $message = "Dear {$user->display_name},\n\n";
        $message .= "We regret to inform you that your leave request has been rejected.\n\n";
        $message .= "Reason for rejection:\n{$rejection_reason}\n\n";
        $message .= "Please contact HR if you have any questions.\n\n";
        $message .= "Best regards,\nHR Department";

        wp_mail($user->user_email, $subject, $message);

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Leave Rejected",
                text: "The leave request has been rejected and the employee has been notified.",
                icon: "success"
            });
        });
        </script>';
    }

    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    $leave_label = [
        'single' => 'One Day',
        'multiple' => 'Multiple Days',
        'half_day' => 'Half Day'
    ];
?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Employee Leave Requests</h1>
        <hr class="wp-header-end">

        <div id="elm-rejection-modal" style="display:none;">
            <div class="elm-modal-content">
                <h3>Reject Leave Request</h3>
                <form method="post">
                    <input type="hidden" name="leave_id" id="elm-rejection-leave-id">
                    <div class="elm-form-group">
                        <label for="rejection_reason">Reason for rejection:</label>
                        <textarea name="rejection_reason" rows="4" required class="widefat"></textarea>
                    </div>
                    <div class="elm-modal-footer">
                        <button type="button" class="button elm-cancel-reject">Cancel</button>
                        <button type="submit" name="elm_reject_leave" class="button button-primary">Submit Rejection</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="elm-reason-modal" style="display:none;">
            <div class="elm-modal-content">
                <h3>Leave Request Reason</h3>
                <div id="elm-reason-content" style="max-height: 60vh; overflow-y: auto; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;"></div>
                <div class="elm-modal-footer">
                    <button type="button" class="button elm-close-reason">Close</button>
                </div>
            </div>
        </div>
        <table id="elm-leave-requests-table" class="display stripe hover" style="width:100%">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Date(s)</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) :
                    $user = get_userdata($row->user_id);
                    $type_display = $leave_label[$row->leave_type] ?? ucfirst($row->leave_type);
                    $status_class = strtolower($row->status);
                ?>
                    <tr>
                        <td><?php echo esc_html($user->display_name); ?></td>
                        <td><?php echo esc_html($row->leave_date); ?></td>
                        <td><?php echo esc_html($type_display); ?></td>
                        <td>
                            <?php if (!empty($row->reason)) : ?>
                                <button class="button button-small view-reason-btn" data-reason="<?php echo esc_attr($row->reason); ?>">View Reason</button>
                            <?php else : ?>
                                <em>No reason provided</em>
                            <?php endif; ?>
                        </td>
                        <td><span class="elm-status-badge elm-status-<?php echo $status_class; ?>"><?php echo esc_html($row->status); ?></span></td>
                        <td><?php echo date('M j, Y g:i a', strtotime($row->created_at)); ?></td>
                        <td>
                            <?php if ($row->status === 'Pending') : ?>
                                <a href="?page=employee-leave-requests&action=approve&leave_id=<?php echo $row->id; ?>" class="button button-small elm-approve-btn">Approve</a>
                                <button data-leave-id="<?php echo $row->id; ?>" class="button button-small button-danger elm-reject-btn">Reject</button>
                            <?php else : ?>
                                <span class="elm-action-complete">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        #elm-reason-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .view-reason-btn {
            background: #2271b1;
            border-color: #2271b1;
            color: white;
            padding: 2px 8px;
            font-size: 12px;
        }

        #elm-leave-requests-table {
            margin-top: 20px;
			margin-bottom: 20px;
        }

        .elm-status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .elm-status-pending {
            background: #fff8e5;
            color: #dba617;
        }

        .elm-status-approved {
            background: #ecf7ed;
            color: #4ab866;
        }

        .elm-status-rejected {
            background: #fbebeb;
            color: #d63638;
        }

        .elm-approve-btn {
            background: #4ab866;
            border-color: #4ab866;
            color: white;
        }

        .elm-reject-btn {
            background: #d63638;
            border-color: #d63638;
            color: white;
        }

        .elm-action-complete {
            color: #757575;
            font-style: italic;
        }

        #elm-rejection-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .elm-modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 500px;
            max-width: 90%;
        }

        .elm-modal-footer {
            margin-top: 15px;
            text-align: right;
        }

        th {
            text-align: left;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0px 30px !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-bottom: 10px !important;
        }
    </style>
<?php
}
