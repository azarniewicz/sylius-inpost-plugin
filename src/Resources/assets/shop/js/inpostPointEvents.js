import triggerCustomEvent from '../../common/js/utilities/triggerCustomEvent';
import {ValidateNextBtn} from './nextBtnValidation';

export class InpostPointEvents {
    constructor(config = {}) {
        this.widget = document.querySelector('[data-bb-target="inpost-geowidget"]');
        this.shippingMethodCode = this.widget?.dataset.bbShippingMethodCode || 'inpost_point';
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

        this.watchInputChanges();
    }

    hideInpostPointSelector() {
        document.querySelector('[data-bb-target="inpost-geowidget"]')?.classList.add('d-none');
    }

    showInpostPointSelector() {
        document.querySelector('[data-bb-target="inpost-geowidget"]')?.classList.remove('d-none');
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
}

export default InpostPointEvents;
