<?php
// if this is in the vehicle_log directory, add the functions

if (basename(__DIR__) === 'vehicle_log') {
    addHandlers();
    addFeedback();
}
?>

<footer class="footer mt-5 py-3 bg-dark">
    <div class="container">
        <span class="text-white">Copyright © Jonathan Coblentz 2026</span>
    </div>
</footer>

<!-- Global modal backdrop cleanup -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prevent sticky form inputs on page reload
        document.querySelectorAll('.modal form').forEach(function (f) {
            f.reset();
        });

        document.querySelectorAll('.modal').forEach(function (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                // Clear inputs when modal is closed
                modal.querySelectorAll('form').forEach(function (f) {
                    f.reset();
                });

                document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        });
    });
</script>

</body>

</html>