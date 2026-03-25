import {InpostPointEvents} from './inpostPointEvents';

if (document.querySelectorAll('[data-bb-target="inpost-geowidget"]').length > 0) {
    new InpostPointEvents().init();
}
