import './bootstrap';
import Alpine from 'alpinejs';
import Precognition from 'laravel-precognition-alpine'; // for input requests validation

window.Alpine = Alpine;
Alpine.plugin(Precognition);
Alpine.start();
