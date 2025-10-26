import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

window.EasyMDE = EasyMDE;

document.dispatchEvent(new CustomEvent('easymde:ready'));
