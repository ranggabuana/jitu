import './bootstrap';
import Swal from 'sweetalert2';

// Make Swal available globally
window.Swal = Swal;

// Check if there's a success message in the session and show SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    // Check if success message exists in meta tag
    const successMessage = document.querySelector('meta[name="success-message"]');
    if (successMessage) {
        Swal.fire({
            title: 'Success!',
            text: successMessage.getAttribute('content'),
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }

    // Check if error message exists in meta tag
    const errorMessage = document.querySelector('meta[name="error-message"]');
    if (errorMessage) {
        Swal.fire({
            title: 'Error!',
            text: errorMessage.getAttribute('content'),
            icon: 'error',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }

    // Add event listener for logout button to show confirmation
    const logoutButtons = document.querySelectorAll('form[method="POST"][action$="/logout"] button[type="submit"]');
    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form if confirmed
                    button.closest('form').submit();
                }
            });
        });
    });

    // Add event listener for delete buttons (OPD and other resources)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = button.closest('form');
            const action = button.getAttribute('data-action');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.action = action;
                    form.submit();
                }
            });
        });
    });
});
