jQuery(document).ready(function ($) {
    // Initialize DataTable
    $('#elm-leave-requests-table').DataTable({
        responsive: true,
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search all columns..."
        }
    });

    // Handle reject button clicks
    $(document).on('click', '.elm-reject-btn', function (e) {
        e.preventDefault();
        const leaveId = $(this).data('leave-id');
        $('#elm-rejection-leave-id').val(leaveId);
        $('#elm-rejection-modal').show();
    });

    // Handle cancel button
    $(document).on('click', '.elm-cancel-reject', function () {
        $('#elm-rejection-modal').hide();
    });

    // Close modal when clicking outside
    $('#elm-rejection-modal').click(function (e) {
        if (e.target === this) {
            $(this).hide();
        }
    });

    // Handle view reason button clicks
    $(document).on('click', '.view-reason-btn', function () {
        const reason = $(this).data('reason');
        $('#elm-reason-content').text(reason);
        $('#elm-reason-modal').show();
    });

    // Handle close reason modal
    $(document).on('click', '.elm-close-reason', function () {
        $('#elm-reason-modal').hide();
    });

    // Close reason modal when clicking outside
    $('#elm-reason-modal').click(function (e) {
        if (e.target === this) {
            $(this).hide();
        }
    });

});