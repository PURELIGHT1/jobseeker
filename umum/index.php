<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        title: 'Lamaran berhasil dikirim!',
        text: 'Kami akan menghubungi Anda jika lolos seleksi.',
        icon: 'success',
        confirmButtonColor: '#e91e63'
    });
</script>
<?php endif; ?>
