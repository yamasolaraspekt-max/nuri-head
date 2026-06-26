(function () {
    'use strict';

    const App = {
        root: null,
        mode: 'create',
        saveUrl: '',
        indexUrl: '',
        payload: {},
        items: [],

        init() {
            this.root = document.getElementById('invoice-canvas-app');
            if (!this.root) return;

            this.mode = this.root.dataset.mode || 'create';
            this.saveUrl = this.root.dataset.saveUrl || '';
            this.indexUrl = this.root.dataset.indexUrl || '';

            try {
                this.payload = JSON.parse(this.root.dataset.payload || '{}');
            } catch (e) {
                console.error('[InvoiceCanvas] Invalid payload', e);
                this.payload = {};
            }

            this.items = Array.isArray(this.payload.items)
                ? JSON.parse(JSON.stringify(this.payload.items))
                : [];

            this.bindEvents();
            this.applyInitialFields();
            this.renderAll();
        },

        bindEvents() {
            this.root.addEventListener('click', (event) => {
                const actionBtn = event.target.closest('[data-action]');
                if (!actionBtn) return;

                const action = actionBtn.dataset.action;

                if (action === 'toggle-sidebar') {
                    this.root.classList.toggle('sidebar-open');
                }

                if (action === 'print') {
                    window.print();
                }

                if (action === 'save') {
                    this.save();
                }

                if (action === 'apply-percentage') {
                    this.applyPercentageMode();
                }

                if (action === 'reload-full') {
                    this.reloadFullItems();
                }

                if (action === 'add-row') {
                    this.addEmptyRow();
                }

                if (action === 'delete-row') {
                    const row = actionBtn.closest('[data-row]');
                    if (!row) return;

                    const index = Number(row.dataset.index);
                    this.items.splice(index, 1);
                    this.renderAll();
                }
            });

            this.root.addEventListener('input', (event) => {
                const field = event.target.closest('[data-field]');
                if (field) {
                    this.handleDocumentFieldChange(field);
                }

                const rowField = event.target.closest('[data-row-field]');
                if (rowField) {
                    this.handleRowFieldChange(rowField);
                }
            });

            this.root.addEventListener('change', (event) => {
                const field = event.target.closest('[data-field]');
                if (!field) return;

                this.handleDocumentFieldChange(field);

                if (field.dataset.field === 'invoice_mode') {
                    this.toggleInvoiceMode();
                }
            });
        },

        applyInitialFields() {
            const doc = this.payload.document || {};

            this.setField('type', doc.type || 'Rechnung');
            this.setField('invoice_mode', 'full');
            this.setField('percentage', 30);

            this.setField('issue_date', doc.issue_date || this.today());
            this.setField('due_date', doc.due_date || this.addDays(8));
            this.setField('service_from', doc.service_from || '');
            this.setField('service_to', doc.service_to || '');

            this.setField('payment_note', doc.payment_note || this.defaultPaymentNote());
            this.setField('notes', doc.notes || '');

            this.toggleInvoiceMode();
        },

        renderAll() {
            this.renderBranding();
            this.renderHeaderText();
            this.renderItems();
            this.renderTotals();
        },

        renderBranding() {
            const company = this.payload.company || {};
            const color = company.brand_color || '#7b2d73';

            document.documentElement.style.setProperty('--invoice-brand', color);
            document.documentElement.style.setProperty('--invoice-line', color);

            const logo = this.root.querySelector('[data-company-logo]');
            const logoText = this.root.querySelector('[data-company-logo-text]');

            if (company.logo_url && logo) {
                logo.src = company.logo_url;
                logo.classList.remove('hidden');
                if (logoText) logoText.classList.add('hidden');
            } else {
                if (logo) logo.classList.add('hidden');
                if (logoText) {
                    logoText.classList.remove('hidden');
                    logoText.textContent = company.name || 'SOLAR ASPEKT';
                }
            }

            const senderLine = this.root.querySelector('[data-sender-line]');
            if (senderLine) {
                senderLine.textContent = this.senderLine();
            }

            const companyName = this.root.querySelector('[data-preview-company-name]');
            if (companyName) {
                companyName.textContent = company.name || 'SOLAR ASPEKT';
            }

            const footer = this.root.querySelector('[data-preview-footer]');
            if (footer) {
                footer.innerHTML = this.footerHtml();
            }
        },

        renderHeaderText() {
            const doc = this.getDocumentValues();
            const customer = this.payload.customer || {};
            const object = this.payload.object || {};
            const company = this.payload.company || {};

            this.text('[data-doc-title]', doc.type || 'Rechnung');
            this.text('[data-doc-number]', doc.invoice_no ? '#' + doc.invoice_no : '');
            this.text('[data-preview-type]', (doc.type || 'Rechnung').toUpperCase());
            this.text('[data-preview-invoice-no]', doc.invoice_no || 'Entwurf');
            this.text('[data-preview-project-title]', this.projectTitle());
            this.text('[data-preview-issue-date]', this.formatDate(doc.issue_date));
            this.text('[data-preview-contact-person]', this.footerValue('contact_person') || company.contact_person || '');
            this.text('[data-preview-customer-no]', customer.customer_no || '');
            this.text('[data-preview-service-period]', this.servicePeriodText(doc));
            this.text('[data-preview-greeting]', this.greetingText());

            const address = this.root.querySelector('[data-customer-address]');
            if (address) {
                address.innerHTML = this.customerAddressHtml(customer);
            }

            const payment = this.root.querySelector('[data-preview-payment-note]');
            if (payment) {
                payment.textContent = doc.payment_note || this.defaultPaymentNote();
            }
        },

        renderItems() {
            const root = this.root.querySelector('[data-items-root]');
            const tpl = document.getElementById('invoice-row-template');
            if (!root || !tpl) return;

            root.innerHTML = '';

            this.items.forEach((item, index) => {
                const node = tpl.content.firstElementChild.cloneNode(true);
                node.dataset.index = String(index);

                node.querySelector('[data-row-no]').textContent = index + 1;

                this.setRowInput(node, 'title', item.title || '');
                this.setRowInput(node, 'description', this.stripHtml(item.description || ''));
                this.setRowInput(node, 'qty', this.numberValue(item.qty, 1));
                this.setRowInput(node, 'unit', item.unit || '');
                this.setRowInput(node, 'unit_price', this.numberValue(item.unit_price, 0));
                this.setRowInput(node, 'line_total', this.numberValue(item.line_total, 0));

                root.appendChild(node);
            });
        },

        renderTotals() {
            const totals = this.calculateTotals();

            this.text('[data-preview-subtotal]', this.money(totals.subtotal));
            this.text('[data-preview-tax-rate]', this.numberValue(totals.taxRate, 19).replace('.', ','));
            this.text('[data-preview-tax-amount]', this.money(totals.taxAmount));
            this.text('[data-preview-total]', this.money(totals.total));
        },

        handleDocumentFieldChange(field) {
            if (field.dataset.field === 'type') {
                this.renderHeaderText();
            }

            if ([
                'issue_date',
                'due_date',
                'service_from',
                'service_to',
                'payment_note',
                'notes'
            ].includes(field.dataset.field)) {
                this.renderHeaderText();
            }
        },

        handleRowFieldChange(input) {
            const row = input.closest('[data-row]');
            if (!row) return;

            const index = Number(row.dataset.index);
            const key = input.dataset.rowField;

            if (!this.items[index]) return;

            let value = input.value;

            if (['qty', 'unit_price', 'line_total'].includes(key)) {
                value = this.parseNumber(value);
            }

            this.items[index][key] = value;

            if (key === 'qty' || key === 'unit_price') {
                const qty = this.parseNumber(this.items[index].qty);
                const price = this.parseNumber(this.items[index].unit_price);
                this.items[index].line_total = this.round(qty * price);

                const totalInput = row.querySelector('[data-row-field="line_total"]');
                if (totalInput) totalInput.value = this.numberValue(this.items[index].line_total, 0);
            }

            this.renderTotals();
        },

        toggleInvoiceMode() {
            const mode = this.getField('invoice_mode') || 'full';
            const wrap = this.root.querySelector('[data-percentage-wrap]');

            if (wrap) {
                wrap.classList.toggle('hidden', mode !== 'percentage');
            }
        },

        applyPercentageMode() {
            const percentage = this.parseNumber(this.getField('percentage') || 0);
            const auftrag = this.payload.auftrag || {};
            const doc = this.payload.document || {};

            const totalNet = this.parseNumber(auftrag.total_net || 0);
            const lineTotal = this.round(totalNet * (percentage / 100));

            const offerNo = auftrag.offer_no || doc.offer_no || '';
            const titleSuffix = this.projectTitle() ? ' - ' + this.projectTitle() : '';

            this.items = [{
                product_id: null,
                title: percentage + '% Anzahlung für Auftrag ' + offerNo + titleSuffix,
                description: '',
                qty: 1,
                unit: 'psch',
                unit_price: lineTotal,
                line_total: lineTotal
            }];

            this.setField('invoice_mode', 'percentage');
            this.renderAll();
        },

        reloadFullItems() {
            const auftragItems = Array.isArray(this.payload.auftrag_items)
                ? this.payload.auftrag_items
                : (Array.isArray(this.payload.items) ? this.payload.items : []);

            this.items = JSON.parse(JSON.stringify(auftragItems));
            this.setField('invoice_mode', 'full');
            this.toggleInvoiceMode();
            this.renderAll();
        },

        addEmptyRow() {
            this.items.push({
                product_id: null,
                title: 'Neue Position',
                description: '',
                qty: 1,
                unit: 'psch',
                unit_price: 0,
                line_total: 0
            });

            this.renderAll();
        },

        async save() {
            const doc = this.getDocumentValues();
            const mode = this.getField('invoice_mode') || 'full';

            const payload = {
                type: doc.type || 'Rechnung',
                status: doc.status || 'draft',
                invoice_mode: mode,
                percentage: this.parseNumber(this.getField('percentage') || 0),
                issue_date: doc.issue_date || null,
                due_date: doc.due_date || null,
                service_from: doc.service_from || null,
                service_to: doc.service_to || null,
                payment_note: doc.payment_note || '',
                notes: doc.notes || '',
                items: this.items.map((item) => ({
                    product_id: item.product_id || null,
                    title: item.title || 'Position',
                    description: item.description || '',
                    qty: this.parseNumber(item.qty || 1),
                    unit: item.unit || '',
                    unit_price: this.parseNumber(item.unit_price || 0),
                    line_total: this.parseNumber(item.line_total || 0)
                }))
            };

            const btn = this.root.querySelector('[data-action="save"]');
            this.setButtonLoading(btn, true);

            try {
                const response = await fetch(this.saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken()
                    },
                    body: JSON.stringify(payload)
                });

                const json = await response.json().catch(() => ({}));

                if (!response.ok || !json.ok) {
                    throw new Error(json.message || 'Rechnung konnte nicht gespeichert werden.');
                }

                this.toast(json.message || 'Gespeichert.');

                if (json.redirect_url) {
                    window.location.href = json.redirect_url;
                }
            } catch (error) {
                console.error('[InvoiceCanvas] Save failed', error);
                alert(error.message || 'Fehler beim Speichern.');
            } finally {
                this.setButtonLoading(btn, false);
            }
        },

        calculateTotals() {
            const subtotal = this.items.reduce((sum, item) => {
                return sum + this.parseNumber(item.line_total || 0);
            }, 0);

            const taxRate = this.parseNumber((this.payload.document || {}).tax_rate || (this.payload.auftrag || {}).tax_rate || 19);
            const taxAmount = this.round(subtotal * (taxRate / 100));
            const total = this.round(subtotal + taxAmount);

            return {
                subtotal: this.round(subtotal),
                taxRate,
                taxAmount,
                total
            };
        },

        getDocumentValues() {
            const doc = this.payload.document || {};

            return {
                id: doc.id || null,
                type: this.getField('type') || doc.type || 'Rechnung',
                status: doc.status || 'draft',
                invoice_no: doc.invoice_no || '',
                offer_no: doc.offer_no || '',
                issue_date: this.getField('issue_date') || doc.issue_date || '',
                due_date: this.getField('due_date') || doc.due_date || '',
                service_from: this.getField('service_from') || doc.service_from || '',
                service_to: this.getField('service_to') || doc.service_to || '',
                payment_note: this.getField('payment_note') || doc.payment_note || '',
                notes: this.getField('notes') || doc.notes || ''
            };
        },

        projectTitle() {
            const object = this.payload.object || {};
            const auftrag = this.payload.auftrag || {};

            return object.name
                || object.full_address
                || [object.street, [object.postcode, object.city].filter(Boolean).join(' ')].filter(Boolean).join(', ')
                || auftrag.offer_no
                || 'Projekt';
        },

        greetingText() {
            const customer = this.payload.customer || {};
            const name = customer.name || customer.firma || '';

            if (!name) return 'Sehr geehrte Damen und Herren,';

            return 'Sehr geehrte Damen und Herren,';
        },

        customerAddressHtml(customer) {
            const lines = [];

            lines.push('An die');

            if (customer.firma) {
                lines.push(customer.firma);
            }

            if (customer.name) {
                lines.push(customer.name);
            }

            if (customer.street) {
                lines.push(customer.street);
            }

            const cityLine = [customer.postcode, customer.city].filter(Boolean).join(' ');
            if (cityLine) {
                lines.push(cityLine);
            }

            if (lines.length <= 1) {
                lines.push('Kunde');
            }

            return lines.map((line) => '<div>' + this.escapeHtml(line) + '</div>').join('');
        },

        senderLine() {
            const footer = this.footerData();
            const company = this.payload.company || {};

            const name = footer.company || company.name || '';
            const street = footer.street || '';
            const city = [footer.postcode, footer.city].filter(Boolean).join(' ');

            return [name, street, city].filter(Boolean).join(', ');
        },

        footerHtml() {
            const footer = this.footerData();
            const company = this.payload.company || {};

            const row1 = [
                footer.company || company.name,
                footer.street,
                [footer.postcode, footer.city].filter(Boolean).join(' '),
                footer.phone ? 'Tel. ' + footer.phone : ''
            ].filter(Boolean).join(' | ');

            const row2 = [
                footer.web,
                footer.email,
                footer.tax ? 'USt-IdNr. ' + footer.tax : '',
                footer.register
            ].filter(Boolean).join(' | ');

            const row3 = [
                footer.gf ? 'Geschäftsführer: ' + footer.gf : '',
                footer.bank,
                footer.iban ? 'IBAN ' + footer.iban : '',
                footer.bic ? 'BIC: ' + footer.bic : ''
            ].filter(Boolean).join(' | ');

            return [row1, row2, row3]
                .filter(Boolean)
                .map((line) => '<div>' + this.escapeHtml(line) + '</div>')
                .join('');
        },

        footerData() {
            const company = this.payload.company || {};
            const footer = company.footer || {};

            if (Array.isArray(footer)) return {};
            if (typeof footer === 'object' && footer !== null) return footer;

            return {};
        },

        footerValue(key) {
            const footer = this.footerData();
            return footer[key] || '';
        },

        servicePeriodText(doc) {
            if (doc.service_from && doc.service_to) {
                return this.formatDate(doc.service_from) + ' - ' + this.formatDate(doc.service_to);
            }

            if (doc.service_from) {
                return this.formatDate(doc.service_from);
            }

            if (doc.service_to) {
                return this.formatDate(doc.service_to);
            }

            return '';
        },

        defaultPaymentNote() {
            return 'Zahlbar ohne Abzug bis zum angegebenen Fälligkeitsdatum.';
        },

        setRowInput(row, key, value) {
            const input = row.querySelector('[data-row-field="' + key + '"]');
            if (input) input.value = value ?? '';
        },

        setField(key, value) {
            const field = this.root.querySelector('[data-field="' + key + '"]');
            if (field) field.value = value ?? '';
        },

        getField(key) {
            const field = this.root.querySelector('[data-field="' + key + '"]');
            return field ? field.value : '';
        },

        text(selector, value) {
            const el = this.root.querySelector(selector);
            if (el) el.textContent = value ?? '';
        },

        money(value) {
            const amount = this.parseNumber(value);

            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        },

        formatDate(value) {
            if (!value) return '';

            const parts = String(value).split('-');
            if (parts.length !== 3) return value;

            return parts[2] + '.' + parts[1] + '.' + parts[0];
        },

        today() {
            return new Date().toISOString().slice(0, 10);
        },

        addDays(days) {
            const date = new Date();
            date.setDate(date.getDate() + Number(days || 0));
            return date.toISOString().slice(0, 10);
        },

        parseNumber(value) {
            if (value === null || value === undefined || value === '') return 0;

            if (typeof value === 'number') return Number.isFinite(value) ? value : 0;

            const clean = String(value)
                .replace(/\./g, '')
                .replace(',', '.')
                .replace(/[^\d.-]/g, '');

            const number = Number(clean);
            return Number.isFinite(number) ? number : 0;
        },

        numberValue(value, fallback) {
            const number = this.parseNumber(value);
            return String(Number.isFinite(number) ? number : fallback);
        },

        round(value) {
            return Math.round((this.parseNumber(value) + Number.EPSILON) * 100) / 100;
        },

        stripHtml(html) {
            if (!html) return '';

            const div = document.createElement('div');
            div.innerHTML = html;

            return div.textContent || div.innerText || '';
        },

        escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        setButtonLoading(button, loading) {
            if (!button) return;

            if (loading) {
                button.disabled = true;
                button.dataset.originalHtml = button.innerHTML;
                button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Speichern...';
            } else {
                button.disabled = false;
                if (button.dataset.originalHtml) {
                    button.innerHTML = button.dataset.originalHtml;
                }
            }
        },

        toast(message) {
            const el = document.createElement('div');
            el.textContent = message;
            el.style.position = 'fixed';
            el.style.right = '20px';
            el.style.bottom = '20px';
            el.style.zIndex = '9999';
            el.style.background = '#0f172a';
            el.style.color = '#fff';
            el.style.padding = '12px 16px';
            el.style.borderRadius = '12px';
            el.style.boxShadow = '0 20px 40px rgba(15,23,42,.22)';
            el.style.fontSize = '13px';
            el.style.fontWeight = '800';

            document.body.appendChild(el);

            setTimeout(() => {
                el.remove();
            }, 2600);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        App.init();
        window.InvoiceCanvas = App;
    });
})();