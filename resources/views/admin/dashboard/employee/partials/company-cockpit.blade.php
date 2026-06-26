<div class="widget-grid" id="grid-company">

    <article class="widget col-span-12 row-span-4" data-widget-id="companyOverview" data-widget-key="companyOverview"
        data-widget-title="Unternehmens-Cockpit" data-widget-tags="company analytics sales">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon green">
                    <i data-lucide="building-2"></i>
                </span>

                <span>
                    <span class="widget-title">Unternehmens-Cockpit</span>
                    <span class="widget-subtitle" id="companySubtitle">
                        Unternehmensweite Übersicht
                    </span>
                </span>
            </div>

            <div class="widget-tools">
                <span class="pill green">Live</span>
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content" style="overflow-y:auto;">
            <div class="dept-toolbar">
                <div class="dept-control-group">
                    <label>Von</label>
                    <input type="date" id="companyFromDate">
                </div>

                <div class="dept-control-group">
                    <label>Bis</label>
                    <input type="date" id="companyToDate">
                </div>

                <button class="btn btn-primary" type="button" id="refreshCompanyDashboardBtn">
                    <i data-lucide="refresh-cw"></i>
                    Aktualisieren
                </button>
            </div>

            <div class="dept-kpi-grid">
                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon blue">
                        <i data-lucide="users"></i>
                    </span>
                    <div>
                        <strong id="companyEmployees">–</strong>
                        <span>Mitarbeiter</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon green">
                        <i data-lucide="building-2"></i>
                    </span>
                    <div>
                        <strong id="companyDepartments">–</strong>
                        <span>Bereiche</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon green">
                        <i data-lucide="user-round-plus"></i>
                    </span>
                    <div>
                        <strong id="companyLeads">–</strong>
                        <span>Leads</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon blue">
                        <i data-lucide="file-text"></i>
                    </span>
                    <div>
                        <strong id="companyOffers">–</strong>
                        <span>Angebote</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon green">
                        <i data-lucide="briefcase-business"></i>
                    </span>
                    <div>
                        <strong id="companyDeals">–</strong>
                        <span>Aufträge</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon warning">
                        <i data-lucide="euro"></i>
                    </span>
                    <div>
                        <strong id="companyInvoiceTotal">–</strong>
                        <span>Umsatz</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon warning">
                        <i data-lucide="wallet"></i>
                    </span>
                    <div>
                        <strong id="companyInvoiceOpen">–</strong>
                        <span>Offen</span>
                    </div>
                </div>

                <div class="dept-kpi-card">
                    <span class="dept-kpi-icon blue">
                        <i data-lucide="ticket"></i>
                    </span>
                    <div>
                        <strong id="companyTickets">–</strong>
                        <span>Tickets</span>
                    </div>
                </div>
            </div>

            <div class="info-note" id="companyInfoNote">
                <i data-lucide="info" style="width:16px;color:var(--color-blue-dark);"></i>
                Unternehmensdaten werden geladen...
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-4 row-span-5" data-widget-id="companyMainArea" data-widget-key="companyMainArea"
        data-widget-title="Mein Hauptbereich" data-widget-tags="company department main">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon green">
                    <i data-lucide="star"></i>
                </span>

                <span>
                    <span class="widget-title">Mein Hauptbereich</span>
                    <span class="widget-subtitle">Main Bereich des aktuellen Mitarbeiters</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="company-main-area-card" id="companyMainAreaCard">
                <div class="empty-state">Hauptbereich wird geladen...</div>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-8 row-span-5" data-widget-id="companyRevenue" data-widget-key="companyRevenue"
        data-widget-title="Umsatz nach Monat" data-widget-tags="company analytics sales">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon warning">
                    <i data-lucide="bar-chart-3"></i>
                </span>

                <span>
                    <span class="widget-title">Umsatz nach Monat</span>
                    <span class="widget-subtitle">Unternehmensweite Entwicklung</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="chart-wrap">
                <canvas id="companyRevenueChart"></canvas>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-6 row-span-5" data-widget-id="companyTypes" data-widget-key="companyTypes"
        data-widget-title="Vorgänge nach Typ" data-widget-tags="company analytics">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon blue">
                    <i data-lucide="pie-chart"></i>
                </span>

                <span>
                    <span class="widget-title">Vorgänge nach Typ</span>
                    <span class="widget-subtitle">Leads, Angebote, Aufträge, Rechnungen</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="chart-wrap">
                <canvas id="companyTypesChart"></canvas>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-6 row-span-5" data-widget-id="companyDepartmentPerformance"
        data-widget-key="companyDepartmentPerformance" data-widget-title="Bereiche im Vergleich"
        data-widget-tags="company departments analytics">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon green">
                    <i data-lucide="chart-no-axes-combined"></i>
                </span>

                <span>
                    <span class="widget-title">Bereiche im Vergleich</span>
                    <span class="widget-subtitle">Leads, Angebote und Aufträge pro Bereich</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="chart-wrap">
                <canvas id="companyDepartmentChart"></canvas>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-6 row-span-6" data-widget-id="companyDepartmentList"
        data-widget-key="companyDepartmentList" data-widget-title="Bereichsübersicht"
        data-widget-tags="company department list">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon blue">
                    <i data-lucide="list"></i>
                </span>

                <span>
                    <span class="widget-title">Bereichsübersicht</span>
                    <span class="widget-subtitle">Team, Leads, Angebote und Aufträge</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="dept-recent-list" id="companyDepartmentList">
                <div class="empty-state">Bereiche werden geladen...</div>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

    <article class="widget col-span-6 row-span-6" data-widget-id="companyHistory" data-widget-key="companyHistory"
        data-widget-title="Unternehmens-Historie" data-widget-tags="company history activity">

        <div class="widget-header">
            <div class="widget-title-wrap">
                <span class="widget-icon warning">
                    <i data-lucide="history"></i>
                </span>

                <span>
                    <span class="widget-title">Unternehmens-Historie</span>
                    <span class="widget-subtitle">Letzte Aktivitäten im Unternehmen</span>
                </span>
            </div>

            <div class="widget-tools">
                <button class="widget-tool-btn danger" type="button" data-delete-widget>
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>

        <div class="widget-content">
            <div class="dept-change-list" id="companyHistoryList">
                <div class="empty-state">Historie wird geladen...</div>
            </div>
        </div>

        <div class="resize-handle"></div>
    </article>

</div>