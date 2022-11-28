/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import '../templates/components/iconButton/iconButton.scss';
import '../templates/components/navbar/navbar.scss';
import '../templates/security/login.scss';
import '../templates/ticket/updateTicket/updateTicket.scss';

// start the Stimulus application
import './bootstrap';
