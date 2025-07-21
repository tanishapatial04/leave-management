<form method="post" class="elm-leave-form">
    <h3>Apply for Leave</h3>

    <label for="leave_type">Leave Type:</label>
    <select name="leave_type" id="leave_type" required>
        <option value="single">One Day</option>
        <option value="multiple">Multiple Days</option>
        <option value="half_day">Half Day</option>
    </select>

    <div id="single-date">
        <label for="leave_date">Leave Date:</label>
        <input type="date" name="leave_date">
    </div>

    <div id="multiple-dates" style="display:none;">
        <label for="from_date">From Date:</label>
        <input type="date" name="from_date">
        
        <label for="to_date">To Date:</label>
        <input type="date" name="to_date">
    </div>

    <label for="reason">Reason:</label>
    <textarea name="reason" rows="4" placeholder="Briefly explain your reason..." required></textarea>

    <input type="submit" name="submit_leave_request" value="Submit Leave Request">
    <img src="https://avapteam-hr.avaptech.in/wp-content/uploads/2025/06/Frame-1-2.png" class="form-img">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaveType = document.getElementById('leave_type');
    const singleDate = document.getElementById('single-date');
    const multipleDates = document.getElementById('multiple-dates');

    leaveType.addEventListener('change', function () {
        if (this.value === 'multiple') {
            singleDate.style.display = 'none';
            multipleDates.style.display = 'block';
        } else {
            singleDate.style.display = 'block';
            multipleDates.style.display = 'none';
        }
    });
});
</script>