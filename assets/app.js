/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

//adminlte
import 'admin-lte/dist/js/adminlte.min'
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js'
import 'admin-lte/plugins/jquery/jquery.min.js'

import Swiper,{Navigation,Pagination} from "swiper";
const swiper = new Swiper('.swiper', {
    modules: [Navigation,Pagination],
    slidesPerView: 3,
    spaceBetween: 30,
    // slidesPerGroup: 3,
    loop: true,
    loopFillGroupWithBlank: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});



// start the Stimulus application
import './bootstrap';
