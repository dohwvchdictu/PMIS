import './bootstrap';
import 'preline';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

window.Swal = Swal;
window.Chart = Chart;

window.reinitPreline = function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
}

// Re-initialize Preline on Livewire navigate
document.addEventListener('livewire:navigated', () => {
    window.reinitPreline();
});
