import {GeoWidget} from '../../common/js/geowidget';

if (
    document.querySelectorAll('[data-bb-target="inpost-geowidget"]').length > 0 ||
    document.querySelectorAll('[data-bb-event="preview-inpost-point"]').length > 0
) {
    new GeoWidget().init();
}
