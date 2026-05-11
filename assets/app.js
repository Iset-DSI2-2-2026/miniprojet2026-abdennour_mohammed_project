import './stimulus_bootstrap.js';
import './styles/app.css';
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-swal-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-swal-confirm') || 'Confirmer ?';
            event.preventDefault();
            Swal.fire({
                title: 'Confirmation',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
