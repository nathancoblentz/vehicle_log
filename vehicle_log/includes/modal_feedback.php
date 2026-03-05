<?php if($feedback): ?>

<div class="modal fade" id="feedbackModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header <?= $feedback['type']=='success'?'bg-success':'bg-danger' ?> text-white">
<h5>Status</h5>
</div>

<div class="modal-body">
<?= htmlspecialchars($feedback['message']) ?>
</div>

<div class="modal-footer">
<button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
</div>

</div>
</div>
</div>

<script>
new bootstrap.Modal(document.getElementById('feedbackModal')).show();
</script>

<?php endif; ?>
