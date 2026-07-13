import './bootstrap';
import './syllabus-wizard';
// import './syllabus-steps-ui';
// import Alpine from 'alpinejs';
// import Precognition from 'laravel-precognition-alpine'; // for input requests validation

// window.Alpine = Alpine;
// Alpine.plugin(Precognition);
// Alpine.start();
import Precognition from 'laravel-precognition-alpine';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Precognition);
});
