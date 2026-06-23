import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './layout/sidebar.js';

import './utils/dom-ready.js';

import './pages/catalog.js';
import './pages/auth.js';
import './pages/book-details-panel.js';
import './pages/facilities.js';
import './pages/password.js'; 
import './pages/profile.js';
import './pages/services.js';
import './pages/admin-member-management.js';
import './pages/admin-website-information.js';
import './pages/admin-services-management.js';
import './pages/sidebar-notification.js';

import './auth/auth-background.js';
import './auth/auth-panel.js';
import './auth/auth-role-fields.js';
import './auth/auth-validation.js';
