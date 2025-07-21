<?php
// Shortcode to display leave form
function elm_leave_form_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Please login to request leave.</p>';
    }

    wp_enqueue_style('elm-styles', ELM_PLUGIN_URL . 'assets/style.css');
    
    ob_start();
    include ELM_PLUGIN_PATH . 'templates/leave-form.php';
    return ob_get_clean();
}
add_shortcode('employee_leave_request', 'elm_leave_form_shortcode');

// Handle leave form submission
function elm_handle_leave_form_submission() {
    if (isset($_POST['submit_leave_request']) && is_user_logged_in()) {
        global $wpdb;

        $user_id = get_current_user_id();
        $leave_type = sanitize_text_field($_POST['leave_type']);
        $reason = sanitize_textarea_field($_POST['reason']);
        $table = $wpdb->prefix . 'employee_leaves_data';

        if ($leave_type === 'multiple') {
            $start_date = $_POST['from_date'];
            $end_date = $_POST['to_date'];

            if ($start_date == $end_date) {
                $leave_date = date('d M Y', strtotime($start_date));
            } else {
                $leave_date = date('d M', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date));
            }

            $wpdb->insert($table, [
                'user_id' => $user_id,
                'leave_date' => $leave_date,
                'reason' => $reason,
                'leave_type' => $leave_type
            ]);
        } else {
            $leave_date = sanitize_text_field($_POST['leave_date']);
            $leave_date = date('d M Y', strtotime($leave_date));
            $wpdb->insert($table, [
                'user_id' => $user_id,
                'leave_date' => $leave_date,
                'reason' => $reason,
                'leave_type' => $leave_type
            ]);
        }

        // Notify HR
        wp_mail(get_option('admin_email'), 'New Leave Request', 'An employee has submitted a new leave request.');

        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        title: "Leave Request Submitted",
                        text: "Your leave request has been submitted successfully!",
                        icon: "success"
                    });
                }
            });
        </script>';
    }
}
add_action('init', 'elm_handle_leave_form_submission');