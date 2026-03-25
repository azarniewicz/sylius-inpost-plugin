import {API_POINTS} from './config';
import {DEFAULT_SELECTORS} from './config';

export class GeoWidgetPreview {
    constructor(node) {
        this.wrapper = node;
        this.apiPoints = this.wrapper?.dataset.bbApiPoints || API_POINTS;
    }

    async renderFromCode(code) {
        try {
            if (!code) return false;

            const response = await fetch(`${this.apiPoints}/${code}`);

            if (!response.ok) throw Error(response.statusText);

            const data = await response.json();

            this.renderTemplate(data);
        } catch (error) {
            console.error(error);
        }
    }

    renderTemplate(data) {
        if (!this.wrapper) {
            throw new Error('BitBagInPostPlugin - The specified wrapper node could not be found in the DOM');
        }

        if (!data) {
            return;
        }

        this.wrapper.innerHTML = '';

        const image = document.createElement('img');
        image.className = 'bb-inpost-point-img';
        image.src = data.image_url;
        image.alt = data.name;

        const description = document.createElement('div');
        description.className = 'bb-inpost-point-desc';
        description.setAttribute(DEFAULT_SELECTORS.previewRaw, '');

        const name = document.createElement('b');
        name.textContent = data.name;

        const address = document.createElement('p');
        address.append(this.createTextLine(data.address?.line1));
        address.append(document.createElement('br'));
        address.append(this.createTextLine(data.address?.line2));
        address.append(document.createElement('br'));

        const locationDescription = document.createElement('small');
        locationDescription.textContent = data.location_description || '';
        address.append(locationDescription);

        description.append(name, address);
        this.wrapper.append(image, description);
    }

    createTextLine(value) {
        return document.createTextNode(value || '');
    }
}

export default GeoWidgetPreview;
