// Order matters: the globals must exist before Bootstrap's jQuery plugins load.
import 'bootstrap/dist/css/bootstrap.min.css';
import './globals';
import 'bootstrap';

// The game's own styles load after Bootstrap's so they win.
import '../sass/app.scss';
