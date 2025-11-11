import './bootstrap';
import 'preline';
import Swal from 'sweetalert2'

window.Swal = Swal

window.reinitPreline = function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
}
