import './bootstrap';
import ujs from '@rails/ujs';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

ujs.start();