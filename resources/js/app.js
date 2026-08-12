import './bootstrap';
import './syllabus-wizard';
// import './syllabus-steps-ui';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Precognition from 'laravel-precognition-alpine'; // for input requests validation

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Precognition);
    Alpine.plugin(collapse);
});

Alpine.start();