import triggerCustomEvent from '../../common/js/utilities/triggerCustomEvent';
import {GeoWidgetPreview} from '../../common/js/geowidget';
import {ValidateNextBtn} from './nextBtnValidation';

export class InpostPointEvents {
    constructor(config = {}) {
        this.widget = document.querySelector('[data-bb-target="inpost-geowidget"]');
        this.geowidget = this.widget?.querySelector('inpost-geowidget');
        this.preview = this.widget?.querySelector('[data-bb-role="inpost-preview-wrapper"]');
        this.shippingMethodCode = this.widget?.dataset.bbShippingMethodCode || 'inpost_point';
        this.saveUrl = this.widget?.dataset.bbSaveUrl || '';
        this.csrfToken = this.widget?.dataset.bbCsrfToken || '';
        this.inputs = [...document.querySelectorAll(`[value="${this.shippingMethodCode}"]`)];
        this.shippingGroups = this.inputs.map((input) => [...document.querySelectorAll(`[name="${input.name}"]`)]);
        this.defaultConfig = {
            validateNextBtn: true,
        };
        this.finalConfig = {
            ...this.defaultConfig,
            ...config,
        };
    }

    init() {
        if (this.shippingGroups.length === 0) {
            throw new Error('InPostPlugin - Couldnt find any nodes in the DOM, regarding inpost points');
        }

        let pointSelected = false;
        this.inputs.forEach((input) => {
            if (input.checked === true) {
                pointSelected = true;
            }
        });

        if (pointSelected === true) {
            this.showInpostPointSelector();
        }

        this.watchPointSelection();
        this.watchInputChanges();
    }

    hideInpostPointSelector() {
        document.querySelector('[data-bb-target="inpost-geowidget"]')?.classList.add('d-none');
    }

    showInpostPointSelector() {
        document.querySelector('[data-bb-target="inpost-geowidget"]')?.classList.remove('d-none');
    }

    watchPointSelection() {
        document.addEventListener('azarniewicz-inpost-point-select', async (event) => {
            const detail = event.detail || event.details || {};
            const point = detail.point || detail;
            const pointName = point.name || point.code || point.id || null;

            if (pointName === null || this.isCurrentMethodSelected() === false) {
                return;
            }

            await this.savePoint(pointName);
        });
    }

    watchInputChanges() {
        this.shippingGroups.forEach((groupFields) => {
            groupFields.forEach((field) => {
                field.addEventListener('change', () => {
                    if (field.value === this.shippingMethodCode && field.checked === true) {
                        this.showInpostPointSelector();
                    } else {
                        this.hideInpostPointSelector();
                    }

                    triggerCustomEvent(
                        field,
                        `inpost.point.${field.value === this.shippingMethodCode ? 'selected' : 'deselected'}`
                    );
                });

                if (!ValidateNextBtn) {
                    return;
                }

                new ValidateNextBtn({node: field}).init();
            });
        });
    }

    isCurrentMethodSelected() {
        return this.inputs.some((input) => input.checked === true);
    }

    async savePoint(pointName) {
        triggerCustomEvent(this.widget, 'inpost.point.save.before');

        try {
            const response = await fetch(this.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    name: pointName,
                    _token: this.csrfToken,
                }).toString(),
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw Error(response.statusText);
            }

            const data = await response.json();

            if (this.preview) {
                new GeoWidgetPreview(this.preview).renderTemplate(data);
            }

            triggerCustomEvent(this.widget, 'inpost.point.save.completed', data);
        } catch (error) {
            triggerCustomEvent(this.widget, 'inpost.point.save.error', error);
        } finally {
            triggerCustomEvent(this.widget, 'inpost.point.save.after');
        }
    }
}

export default InpostPointEvents;
