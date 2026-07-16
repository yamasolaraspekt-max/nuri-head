<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WERK STUDIO / SOLAR ASPEKT Energiekonzept</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            counter-reset: page-counter;
        }

        /* Die Variablen werden nun dynamisch durch JS (updateThemeCSS) überschrieben */
        :root {
            --color-primary: #97937c;
            --color-secondary: #72436b;
            --color-inactive: #97937cb3;
        }

        /* Einheitliche Formularfelder: feste Höhe, damit Inputs & Selects pixelgleich sind.
       Native Selects ignorieren Padding teils – appearance:none + eigener Pfeil behebt das. */
        .field {
            height: 3rem;
        }

        .field-sm {
            height: 2.375rem;
        }

        select.field,
        select.field-sm {
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.65rem center;
            background-size: 1rem;
            padding-right: 2.25rem !important;
        }

        /* Page Counter */
        .a4-page {
            counter-increment: page-counter;
        }

        .page-number::after {
            content: counter(page-counter);
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .a4-page {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 15mm 20mm 25mm 20mm !important;
                /* Adjusted bottom padding for footer */
                page-break-after: always;
                box-shadow: none !important;
                border: none !important;
                overflow: hidden !important;
            }

            .a4-page:last-child {
                page-break-after: auto;
            }
        }

        @media screen {
            .a4-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 2rem auto;
                padding: 20mm 20mm 25mm 20mm;
                /* Adjusted bottom padding for footer */
                background: white;
                box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
                position: relative;
                overflow: hidden;
            }
        }

        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--color-primary);
            border-color: transparent;
        }

        .animate-fade-in {
            animation: fadeIn .25s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-wrap {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .sidebar-transition {
            transition: transform 0.3s ease;
        }

        .rot-180 {
            transform: rotate(180deg);
        }

        .custom-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .icon-stroke {
            width: 1em;
            height: 1em;
            display: block;
            flex-shrink: 0;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-box>svg {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="bg-white text-dark-600">

    <div id="app"></div>

    <script>
        const backendCustomer = @json($customer ?? null);
        const backendProducts = @json($products ?? []);
        const backendRoofs = @json($roofs ?? []);
        const backendPreset = @json($calculatorPreset ?? []);
        const backendFrontendConfig = @json($frontendConfig ?? null);
        const backendMeta = {
            calculationId: @json($pageMeta['calculation_id'] ?? ($existingCalculation->id ?? null)),
            customerId: @json($pageMeta['customer_id'] ?? ($customer->customer_id ?? null)),
            alternativeId: @json($pageMeta['alternative_id'] ?? ($customer->alternative_id ?? null)),
            productId: @json($pageMeta['product_id'] ?? ($customer->product_id ?? null)),
            serviceId: @json($pageMeta['service_id'] ?? ($customer->service_id ?? null)),
            saveUrl: @json(route('profitability-calculations.save-report')),
        };
        const existingCalculation = @json($existingCalculation ?? null);
    </script>
    <script>
        // =========================================================
        // THEME & LOGO CONFIGURATION
        // =========================================================
        const THEMES = {
            'Werkstudio': {
                name: 'WERK STUDIO BAUKONZEPT',
                logo: "{{ asset('logo/werk-studio.png') }}",
                primary: '#97937c',       // Base/Title
                secondary: '#72436b',     // Subtitles
                inactive: '#97937cb3',    // 70% of primary for inactive
                bgLight: '#97937c1a',     // Light background tint
                phone: '06081 - 53 25',
                email: 'anfrage@solar-aspekt.de',
                web: 'www.solar-aspekt.de',

            },
            'Solar Aspekt': {
                name: 'SOLAR ASPEKT',
                logo: "{{ asset('logo/logo.png') }}",
                primary: '#93c21c',       // Base/Title
                secondary: '#74b2d4',     // Subtitles
                inactive: '#c0d8ea',      // Inactive/Ring chart empty
                bgLight: '#cfe09b',       // Alternate inactive/light
                phone: '06081 – 68 288 78',
                email: 'anfrage@solar-aspekt.de',
                web: 'www.solar-aspekt.de',
            }
        };

        function getActiveTheme() {
            return THEMES[state.config.company] || THEMES['Werkstudio'];
        }

        function updateThemeCSS() {
            const t = getActiveTheme();
            document.documentElement.style.setProperty('--color-primary', t.primary);
            document.documentElement.style.setProperty('--color-secondary', t.secondary);
            document.documentElement.style.setProperty('--color-inactive', t.inactive);
        }

        // =========================================================
        // ICONS (Lucide-like inline SVG helpers)
        // =========================================================
        const Icons = {
            sun: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>`,
            thermoSnow: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path><path d="M9 17h6"></path><path d="M17 8l1 1 1-1"></path><path d="M17 14l1 1 1-1"></path></svg>`,
            zap: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"></path></svg>`,
            mapPin: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 22s7-4.35 7-12a7 7 0 1 0-14 0c0 7.65 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>`,
            info: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>`,
            home: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path></svg>`,
            users: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`,
            euro: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M4 10h10"></path><path d="M4 14h10"></path><path d="M14.5 6.5a5.5 5.5 0 1 0 0 11"></path></svg>`,
            checkSquare: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 12l2 2 4-4"></path></svg>`,
            checkCircle2: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>`,
            shieldCheck: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>`,
            printer: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v5a2 2 0 0 1-2 2h-2"></path><path d="M6 14h12v8H6z"></path></svg>`,
            arrowLeft: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>`,
            arrowRight: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>`,
            activity: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M22 12h-4l-3 9-6-18-3 9H2"></path></svg>`,
            leaf: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M11 20A7 7 0 0 1 4 13C4 7 9 4 20 4c0 11-3 16-9 16z"></path><path d="M11 20c1.5-5 4.5-8 9-11"></path></svg>`,
            trendingUp: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M22 7 13.5 15.5l-5-5L2 17"></path><path d="M16 7h6v6"></path></svg>`,
            alertTriangle: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94A2 2 0 0 0 22.18 18L13.71 3.86a2 2 0 0 0-3.42 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>`,
            x: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>`,
            sliders: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M4 21v-7"></path><path d="M4 10V3"></path><path d="M12 21v-9"></path><path d="M12 8V3"></path><path d="M20 21v-5"></path><path d="M20 12V3"></path><path d="M2 14h4"></path><path d="M10 8h4"></path><path d="M18 16h4"></path></svg>`,
            save: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path></svg>`,
            chevronDown: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="m6 9 6 6 6-6"></path></svg>`,
            lightbulb: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M12 2a7 7 0 0 0-4 12c.7.6 1 1.4 1 2h6c0-.6.3-1.4 1-2A7 7 0 0 0 12 2z"></path></svg>`,
            award: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="8" r="7"></circle><path d="M8.21 13.89 7 22l5-3 5 3-1.21-8.11"></path></svg>`,
            wrench: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-3-3 3-3z"></path></svg>`,
            star: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"></path></svg>`,
            battery: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="2" y="7" width="18" height="10" rx="2"></rect><path d="M22 11v2"></path></svg>`,
            car: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 16H9m10 0h1a1 1 0 0 0 1-1v-3l-2-5a2 2 0 0 0-2-1H7a2 2 0 0 0-2 1l-2 5v3a1 1 0 0 0 1 1h1"></path><circle cx="6.5" cy="16.5" r="2.5"></circle><circle cx="17.5" cy="16.5" r="2.5"></circle></svg>`,
            network: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><path d="M10 6.5h4"></path><path d="M17.5 10v4"></path><path d="M7 10v8h7"></path></svg>`,
            checkCircle: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>`,
            tag: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M20 10 10 20 2 12V2h10l8 8z"></path><circle cx="7" cy="7" r="1"></circle></svg>`,
            piggyBank: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 5c1.5 0 2 1 2 2 0 1.5-1 2-2 2"></path><path d="M3 11a7 7 0 0 1 7-7h5a5 5 0 0 1 5 5v2a6 6 0 0 1-6 6H8l-2 3H4l1-3a6 6 0 0 1-2-4v-2z"></path><path d="M12 7v3"></path></svg>`,
            cpu: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="7" y="7" width="10" height="10" rx="2"></rect><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"></path></svg>`,
            infinity: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M18.18 8c-2.43 0-4.18 4-6.18 4s-3.75-4-6.18-4A3.82 3.82 0 0 0 2 11.82 3.82 3.82 0 0 0 5.82 15c2.43 0 4.18-4 6.18-4s3.75 4 6.18 4A3.82 3.82 0 0 0 22 11.18 3.82 3.82 0 0 0 18.18 8z"></path></svg>`,
            calculator: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M8 6h8"></path><path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path></svg>`,
            thermometer: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path></svg>`,
            maximize: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M16 3h3a2 2 0 0 1 2 2v3"></path><path d="M8 21H5a2 2 0 0 1-2-2v-3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path></svg>`,
            droplet: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 2s7 7 7 12a7 7 0 0 1-14 0c0-5 7-12 7-12z"></path></svg>`
        };

        function icon(name, classes = "w-4 h-4", color = "") {
            return `<span class="icon-box ${classes} ${color}">${Icons[name]()}</span>`;
        }

        // =========================================================
        // CONSTANTS
        // =========================================================
        const ASSUMPTIONS = {
            version: '2026-06-12-v2',
            label: 'Solar/WP-Wirtschaftlichkeit - Beratungsmodell',
            legalStatus: '06/2026',
            note: 'Zentrale Annahmen fuer Beratung und Simulation. Vor Angebot/Foerderantrag aktuelle Werte pruefen.',

            profiles: {
                months: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                heatingDegreeDayDistribution: [0.18, 0.15, 0.12, 0.08, 0.04, 0.015, 0.015, 0.015, 0.03, 0.06, 0.10, 0.195],
                pvDistribution: [0.03, 0.05, 0.08, 0.10, 0.12, 0.13, 0.13, 0.11, 0.10, 0.08, 0.04, 0.03],
                householdDistribution: [0.095, 0.085, 0.085, 0.080, 0.075, 0.070, 0.070, 0.070, 0.080, 0.085, 0.095, 0.110],
                evDistribution: [0.09, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.09, 0.10],
                daylightRatio: [0.35, 0.40, 0.50, 0.55, 0.60, 0.65, 0.65, 0.60, 0.50, 0.45, 0.35, 0.30],
                monthDays: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
            },

            technical: {
                pvDegradationPerYear: 0.005,
                pvSystemLossDefaultPercent: 14,
                pvMarketValueAfterEeg: 0.04,
                inverterReplacementYear: 15,
                inverterReplacementCostDefault: 1500,
                evConsumptionKwhPerKm: 0.2,
                evDayChargingShare: 0.20,
                batteryRoundtripEfficiency: 0.90,
                minRecommendedBatteryKwh: 5,
                pvSizingFactor: 1.35,
                solarThermalCollectorAreaM2: 2.5,
                solarThermalYieldFlatKwhM2: 350,
                solarThermalYieldTubeKwhM2: 500,
                woodStoveKwhPerRm: 2100,
                woodStoveEfficiency: 0.75,
                warmWaterKwhPerPerson: 800,
                circulationKwhDefault: 600,
                heatingLimitCelsius: 15,
                indoorDesignTempCelsius: 20,
                climateCacheDays: 30,
                co2TreesPerTon: 80,
                co2ForestM2PerTon: 1250,
                oldHeatingFastTrackAgeYears: 20,
                mediumHeatingAgeYears: 10,
                systemLossByHeatingAge: { over20: 0.20, over10: 0.15, default: 0.10 },
                bivalencePoint: { mildNatThreshold: -10, mild: -5, cold: -7 },
                existingElectricCarKwhPer100Km: 20
            },

            finance: {
                analysisYears: 30,
                reportYears: [10, 20, 30],
                lcoeYears: 30,
                eegRemunerationYears: 20,
                electricityInflationDefaultPercent: 3.0,
                fossilInflationDefaultPercent: 4.5,
                maintenanceOldDefault: 300,
                maintenanceNewDefault: 300,
                gridFeeDefault: 0.10,
                defaultPrices: { electricity: 0.35, feedIn: 0.08, heatingMedium: 0.11, fuel: 1.80, wood: 120 },
                defaultInvestment: { wp: 30000, pv: 16000, battery: 8000, wallbox: 1500 },
                defaultDiscount: { wp: 1000, pv: 750, battery: 250, wallbox: 150 },
                heatingMediumPrices: { gas: 0.11, oil: 1.05, pelletsPerTon: 280, pelletConsumptionDefaultTons: 4, fossilConsumptionDefaultKwh: 20000 }
            },

            eeg: {
                sourceStatus: 'Stand 02/2026, vor Angebot gegen BNetzA pruefen',
                partialFeedIn: { upTo10Kwp: 0.0778, upTo40Kwp: 0.0673 },
                fullFeedIn: { upTo10Kwp: 0.1235, upTo40Kwp: 0.1035 }
            },

            tenantPower: {
                sourceStatus: 'Stand 06/2026, monatliche Degression vor Angebot pruefen',
                surcharge: { upTo10Kwp: 0.02571, upTo40Kwp: 0.02389, upTo100Kwp: 0.0228 },
                priceCapShareOfBaseTariff: 0.90,
                defaultTenantDemandKwhPerUnit: 2500
            },

            funding: {
                kfw458: {
                    sourceStatus: 'Beratungsannahme 06/2026, Bewilligung im Einzelfall pruefen',
                    baseGrantPercent: 30,
                    efficiencyBonusPercent: 5,
                    climateSpeedBonusPercent: 20,
                    incomeBonusPercent: 30,
                    maxGrantPercent: 70,
                    lowIncomeThresholdEuro: 40000,
                    costCapFirstUnit: 30000,
                    costCapUnits2To6: 15000,
                    costCapFrom7: 8000
                }
            },

            regulatory: {
                enwg14a: { flatSavingEuro: 160, variableGridFeeReductionShare: 0.60 },
                co2CostSplit: {
                    pricePerTonDefault: 60,
                    levels: [[12, 0], [17, 10], [22, 20], [27, 30], [32, 40], [37, 50], [42, 60], [47, 70], [52, 80], [Infinity, 95]]
                },
                landlord: {
                    defaultLivingAreaPerUnitM2: 85,
                    defaultHeatBasePricePerUnitMonth: 15,
                    modernizationMaintenanceDeductionShare: 0.15,
                    modernizationAnnualShare: 0.10,
                    modernizationCapEuroPerM2Month: 0.50
                }
            },

            emissionFactorsKgPerKwh: { gas: 0.202, oil: 0.266, pellets: 0.02, gridElectricity: 0.4, directElectricHeating: 0.4, woodStove: 0.02, petrolKgPerLiter: 2.37 },

            scenarios: {
                // Angebotsstufen: "Zukunft" = Empfehlung + Speicher-Reserve (+ Wallbox)
                futureBatteryFactor: 1.5,
                maxFutureBatteryKwh: 30,
                // Sensitivität: Preissteigerungs-Spreizung für die Amortisations-Bandbreite
                inflationSpreadPercentagePoints: 1.5
            },

            plausibility: {
                householdKwhMin: 1000, householdKwhMax: 12000,
                householdTypicalText: '2.000-6.000 kWh',
                jazMin: 2.5, jazMax: 5.5, jazTypicalText: '3,0-4,8',
                electricityPriceMin: 0.20, electricityPriceMax: 0.60, electricityPriceTypicalText: '0,25-0,45 €/kWh',
                feedInDeviationWarnEuroPerKwh: 0.005, pvKwpRegulatoryWarning: 30,
                heatingConsumptionRanges: {
                    'Gas': [5000, 60000], 'Öl': [500, 6000], 'Holz / Pellets': [2, 15],
                    'Nachtspeicher': [4000, 40000], 'Stromdirektheizung': [4000, 40000]
                }
            }
        };

        const MONTHS = ASSUMPTIONS.profiles.months;
        const HGT_DISTRIBUTION = ASSUMPTIONS.profiles.heatingDegreeDayDistribution;
        const PV_DISTRIBUTION = ASSUMPTIONS.profiles.pvDistribution;
        const HH_DISTRIBUTION = ASSUMPTIONS.profiles.householdDistribution;
        const EV_DISTRIBUTION = ASSUMPTIONS.profiles.evDistribution;
        const DAYLIGHT_RATIO = ASSUMPTIONS.profiles.daylightRatio;

        const PV_DEGRADATION = ASSUMPTIONS.technical.pvDegradationPerYear;
        const EEG_DAUER = ASSUMPTIONS.finance.eegRemunerationYears;
        const MARKTWERT_SOLAR = ASSUMPTIONS.technical.pvMarketValueAfterEeg;
        const WR_ERSATZ_JAHR = ASSUMPTIONS.technical.inverterReplacementYear;
        const EEG_SATZ_BIS_10 = ASSUMPTIONS.eeg.partialFeedIn.upTo10Kwp;
        const EEG_SATZ_BIS_40 = ASSUMPTIONS.eeg.partialFeedIn.upTo40Kwp;
        const EEG_VOLL_BIS_10 = ASSUMPTIONS.eeg.fullFeedIn.upTo10Kwp;
        const EEG_VOLL_BIS_40 = ASSUMPTIONS.eeg.fullFeedIn.upTo40Kwp;
        const MIETERSTROM_ZUSCHLAG_BIS_10 = ASSUMPTIONS.tenantPower.surcharge.upTo10Kwp;
        const MIETERSTROM_ZUSCHLAG_BIS_40 = ASSUMPTIONS.tenantPower.surcharge.upTo40Kwp;
        const MIETERSTROM_ZUSCHLAG_BIS_100 = ASSUMPTIONS.tenantPower.surcharge.upTo100Kwp;
        const CO2_STUFEN = ASSUMPTIONS.regulatory.co2CostSplit.levels;

        function getAssumptionsSnapshot() {
            return structuredClone(ASSUMPTIONS);
        }

        function getEmissionFactorForHeating(heatingType) {
            if (heatingType === 'Öl') return ASSUMPTIONS.emissionFactorsKgPerKwh.oil;
            if (heatingType === 'Holz / Pellets') return ASSUMPTIONS.emissionFactorsKgPerKwh.pellets;
            if (heatingType === 'Nachtspeicher' || heatingType === 'Stromdirektheizung') return ASSUMPTIONS.emissionFactorsKgPerKwh.directElectricHeating;
            return ASSUMPTIONS.emissionFactorsKgPerKwh.gas;
        }

        function getKfwEligibleCostCap(unitCount) {
            const k = ASSUMPTIONS.funding.kfw458;
            const n = Math.max(1, Number(unitCount || 1));
            if (n === 1) return k.costCapFirstUnit;
            if (n <= 6) return k.costCapFirstUnit + (n - 1) * k.costCapUnits2To6;
            return k.costCapFirstUnit + (5 * k.costCapUnits2To6) + (n - 6) * k.costCapFrom7;
        }

        function num(val, fallback = 0) {
            if (val === null || val === undefined || val === '') return fallback;
            const n = Number(val);
            return Number.isFinite(n) ? n : fallback;
        }

        function str(val, fallback = '') {
            if (val === null || val === undefined) return fallback;
            return String(val);
        }

        function boolish(val, fallback = false) {
            if (typeof val === 'boolean') return val;
            if (val === 1 || val === '1' || val === 'true' || val === 'ja' || val === 'Ja') return true;
            if (val === 0 || val === '0' || val === 'false' || val === 'nein' || val === 'Nein') return false;
            return fallback;
        }
        // =========================================================
        // STATE
        // =========================================================
        const preset = backendPreset || {};
        const customerData = backendCustomer || {};
        const productList = Array.isArray(backendProducts) ? backendProducts : [];

        const customerFullName = [
            str(preset.vorname || customerData.first_name || ''),
            str(preset.nachname || customerData.last_name || '')
        ].filter(Boolean).join(' ').trim();

        const detectedCompany = 'Solar Aspekt'; // or decide dynamically

        const defaultRoofDirection = str(customerData.roof_direction || 'Süd');
        const defaultRoofType = str(customerData.roof_type || 'Ziegel');
        const defaultRoofPitch = num(customerData.roof_pitch, 35);

        const hasWallboxProduct = productList.some(p =>
            str(p.article_group).toLowerCase().includes('wallbox')
        );

        const hasSolarProduct = productList.some(p => {
            const name = str(p.article_group).toLowerCase();
            return name.includes('pv') || name.includes('photovoltaik') || name.includes('solar') || name.includes('speicher');
        });

        const hasWpProduct = productList.some(p => {
            const name = str(p.article_group).toLowerCase();
            return name.includes('wärmepumpe') || name.includes('waermepumpe') || /\bwp\b/.test(name);
        });

        const state = {
            view: 'dashboard', // or 'wizard' if you still want editing first
            wizardStep: 'projekt',
            wizardUI: {
                installDetails: false,
                advancedPrices: false
            },
            isSidebarOpen: false,
            sidebarSections: {
                kunde: false,
                dach: false,
                altsystem: false,
                kaminSolar: false,
                preise: false,
                investitionen: false
            },
            config: (typeof backendFrontendConfig !== 'undefined' && backendFrontendConfig)
                ? structuredClone(backendFrontendConfig)
                : {
                    company: 'Solar Aspekt',
                    modulePV: backendPreset.includeSolar ?? hasSolarProduct,
                    moduleWP: backendPreset.includeWp ?? hasWpProduct,
                    moduleWB: backendPreset.includeWallbox ?? hasWallboxProduct,

                    name: `${backendPreset.vorname ?? ''} ${backendPreset.nachname ?? ''}`.trim() || customerFullName || 'Kunde',
                    gebaeudeArt: backendPreset.gebaeudeArt || 'Einfamilienhaus',
                    wohneinheiten: Number(backendPreset.wohneinheitenGesamt ?? 1),
                    selbstbewohnteWE: Number(backendPreset.wohneinheitenBewohnt ?? 1),
                    weUnter40k: Number(backendPreset.eigentuemerUnter40k ?? 0),
                    plz: backendPreset.standortPlz || '',
                    fullAddress: backendPreset.objektadresse || customerData.object_full_address || customerData.customer_full_address || '',
                    street: backendPreset.objektstrasse || customerData.object_street || customerData.customer_street || '',
                    city: backendPreset.objektort || customerData.object_city || customerData.customer_city || '',

                    dachseiten: [
                        {
                            id: 1,
                            designation: 'Hauptdach',
                            roofForm: 'Satteldach',
                            ausrichtung: defaultRoofDirection,
                            neigung: defaultRoofPitch,
                            eindeckung: defaultRoofType,
                            eindeckungTyp: '',
                            customKwp: backendPreset.customPvSize || ''
                        }
                    ],
                    pvSystemVerlust: ASSUMPTIONS.technical.pvSystemLossDefaultPercent,   // % Systemverluste inkl. Verschattung
                    einspeiseArt: 'ueberschuss',   // 'ueberschuss' (Eigenverbrauch) | 'voll' (Volleinspeisung)

                    heizungArt: backendPreset.heizungsArt === 'Heizöl' ? 'Öl'
                        : backendPreset.heizungsArt === 'Pellets' ? 'Holz / Pellets'
                            : backendPreset.heizungsArt === 'Nachtspeicher' ? 'Nachtspeicher'
                                : 'Gas',

                    heizungAlter: Number(backendPreset.heizungsAlter ?? 20),
                    heizVerbrauch: Number(backendPreset.heizVerbrauch ?? 20000),
                    heizSystem: backendPreset.heizsystem || 'Heizkörper',
                    warmwasserArt: 'Zentral',
                    personen: Number(backendPreset.personenAnzahl ?? 3),
                    zirkulation: false,

                    rohrHeizungMaterial: 'Kupfer',
                    rohrHeizungDN: '28',
                    rohrWWMaterial: 'Kupfer',
                    rohrWWDN: '18',
                    rohrZirkulationMaterial: 'Kupfer',
                    rohrZirkulationDN: '15',

                    kaminVorhanden: false,
                    kaminWeiterBetreiben: false,
                    holzVerbrauch: 3,
                    preisHolz: ASSUMPTIONS.finance.defaultPrices.wood,

                    solarthermieVorhanden: false,
                    solarthermieWeiterBetreiben: false,
                    solarthermieArt: 'Flachkollektor',
                    solarKollektoren: 2,

                    hhStrom: Number(backendPreset.stromverbrauch ?? 4000),
                    autoArt: 'Verbrenner',
                    fahrleistung: Number(backendPreset.kmProJahr ?? 15000),
                    verbrennerVerbrauch: 7,
                    preisSprit: Number(backendPreset.spritPreis ?? ASSUMPTIONS.finance.defaultPrices.fuel),

                    preisStrom: Number(backendPreset.evuPreis ?? ASSUMPTIONS.finance.defaultPrices.electricity),
                    preisEinspeisung: ASSUMPTIONS.finance.defaultPrices.feedIn,
                    preisHeizMedium: Number(backendPreset.heizPreis ?? ASSUMPTIONS.finance.defaultPrices.heatingMedium),
                    inflationRate: ASSUMPTIONS.finance.electricityInflationDefaultPercent,            // Strompreis-Steigerung %/Jahr
                    inflationRateFossil: ASSUMPTIONS.finance.fossilInflationDefaultPercent,      // fossile Energie %/Jahr
                    wartungOld: Number(backendPreset.wartungAlt_pa_input ?? ASSUMPTIONS.finance.maintenanceOldDefault),
                    wartungNeu: ASSUMPTIONS.finance.maintenanceNewDefault,               // WP-Wartung + PV-Sichtprüfung
                    wrErsatzKosten: ASSUMPTIONS.technical.inverterReplacementCostDefault,          // Rücklage Wechselrichter-Tausch
                    netzentgelt: ASSUMPTIONS.finance.gridFeeDefault,

                    costWP: Number(backendPreset.customWpKosten ?? ASSUMPTIONS.finance.defaultInvestment.wp),
                    costPV: Number(backendPreset.customPvKosten ?? ASSUMPTIONS.finance.defaultInvestment.pv),
                    costBattery: Number(backendPreset.customSpeicherKosten ?? ASSUMPTIONS.finance.defaultInvestment.battery),
                    costWallbox: Number(backendPreset.customWallboxKosten ?? ASSUMPTIONS.finance.defaultInvestment.wallbox),

                    customWpKw: backendPreset.customWpSize || '',
                    customPvKwp: backendPreset.customPvSize || '',
                    customBatteryKwh: backendPreset.customSpeicherSize || '',
                    customJAZ: backendPreset.customJaz || '',

                    discountWP: ASSUMPTIONS.finance.defaultDiscount.wp,
                    discountPV: ASSUMPTIONS.finance.defaultDiscount.pv,
                    discountBattery: ASSUMPTIONS.finance.defaultDiscount.battery,
                    discountWallbox: ASSUMPTIONS.finance.defaultDiscount.wallbox,

                    extraGrantWP: backendPreset.wpZusatzFoerderSumme || '',
                    extraGrantPV: backendPreset.pvZusatzFoerderSumme || '',
                    extraGrantBattery: backendPreset.speicherZusatzFoerderSumme || '',
                    extraGrantWallbox: backendPreset.wallboxZusatzFoerderSumme || '',

                    extraGrantSourceWP: backendPreset.wpZusatzFoerderName || '',
                    extraGrantSourcePV: backendPreset.pvZusatzFoerderName || '',
                    extraGrantSourceBattery: backendPreset.speicherZusatzFoerderName || '',
                    extraGrantSourceWallbox: backendPreset.wallboxZusatzFoerderName || '',

                    // Vermieter-Modul (Contracting/Umlage + Mieterstrom + CO2-Split)
                    vermieterModus: false,
                    wohnflaeche: '',
                    waermeModell: 'contracting',     // 'contracting' | 'umlage559e' | 'aus'
                    waermeArbeitspreis: '',          // '' = kostenneutraler Vorschlag
                    waermeGrundpreis: ASSUMPTIONS.regulatory.landlord.defaultHeatBasePricePerUnitMonth,            // €/Monat je vermieteter WE
                    mieterstromModell: '42b',        // '42b' | 'klassisch' | 'aus'
                    mieterStromBedarf: '',           // '' = 2.500 kWh je vermieteter WE
                    mieterstromPreis: '',            // '' = 90 % des Strompreises (§ 42a-Deckel)
                    co2PreisTonne: ASSUMPTIONS.regulatory.co2CostSplit.pricePerTonDefault
                }
        };

        const charts = {};

        // =========================================================
        // HELPERS
        // =========================================================
        function getRegionalFactors(plzStr) {
            // Achtung: nicht `parseInt(...) || 5` – PLZ-Gebiet 0 (Sachsen/Thüringen) ist eine gültige 0
            const firstChar = String(plzStr).charAt(0);
            const firstDigit = /\d/.test(firstChar) ? Number(firstChar) : 5;
            let pvBaseFactor = 950;
            let wpFactor = 1.0;

            if (firstDigit >= 8) {
                pvBaseFactor = 1050;
                wpFactor = 1.05;
            } else if (firstDigit >= 6) {
                pvBaseFactor = 1000;
                wpFactor = 1.0;
            } else if (firstDigit <= 2) {
                pvBaseFactor = 900;
                wpFactor = 0.95;
            }

            return { pvBaseFactor, wpFactor };
        }

        // EEG-Sätze werden zentral in ASSUMPTIONS.eeg gepflegt und per Snapshot gespeichert.

        // Über 10 kWp gilt anteilig je Leistungsstufe ein niedrigerer Satz (Mischvergütung).
        // art: 'ueberschuss' (Teileinspeisung, Standard) oder 'voll' (Volleinspeisung)
        function getEegMischverguetung(kwp, art) {
            const s10 = art === 'voll' ? EEG_VOLL_BIS_10 : EEG_SATZ_BIS_10;
            const s40 = art === 'voll' ? EEG_VOLL_BIS_40 : EEG_SATZ_BIS_40;
            const k = Number(kwp || 0);
            if (k <= 10) return s10;
            const begrenzt = Math.min(k, 40);
            return Math.round(((10 * s10 + (begrenzt - 10) * s40) / begrenzt) * 10000) / 10000;
        }

        // Mieterstromzuschläge werden zentral in ASSUMPTIONS.tenantPower gepflegt.

        function getMieterstromzuschlag(kwp) {
            const k = Math.min(Number(kwp || 0), 100);
            if (k <= 0) return MIETERSTROM_ZUSCHLAG_BIS_10;
            const a = Math.min(k, 10);
            const b = Math.max(0, Math.min(k, 40) - 10);
            const c = Math.max(0, k - 40);
            return Math.round(((a * MIETERSTROM_ZUSCHLAG_BIS_10 + b * MIETERSTROM_ZUSCHLAG_BIS_40 + c * MIETERSTROM_ZUSCHLAG_BIS_100) / k) * 100000) / 100000;
        }

        // CO2-Kostenaufteilungsgesetz: Stufen werden zentral in ASSUMPTIONS.regulatory.co2CostSplit gepflegt.
        function getCo2VermieterAnteil(kgProM2) {
            for (const [grenze, anteil] of CO2_STUFEN) {
                if (kgProM2 < grenze) return anteil;
            }
            return 95;
        }

        // =========================================================
        // STANDORTDATEN (empirisch): Ort & Koordinaten via Zippopotam,
        // Klima (Heiztage/Gradtage) und Solarertrag je Dachfläche via PVGIS.
        // PVGIS erlaubt keine Browser-Direktzugriffe (CORS) – die Aufrufe
        // laufen über einen Proxy auf eigener Domain (Route /api/pvgis/...).
        // Ohne Proxy oder offline fällt alles auf die internen Tabellen zurück.
        // =========================================================
        const PVGIS_PROXY_URL = '/api/pvgis';
        const standort = {
            plz: null, lat: null, lon: null, ort: null,
            klimaStatus: 'idle', // idle | laden | ok | fehler
            heiztage: null, gtz: null, natTmy: null, hgtMonat: null,
            pv: {} // "lat|lon|neigung|aspect" -> { status, ey, monat[12] }
        };

        function getPvgisAspect(ausrichtung) {
            // PVGIS-Konvention: 0 = Süd, 90 = West, -90 = Ost, ±180 = Nord
            const map = { 'Süd': 0, 'Süd-Ost': -45, 'Süd-West': 45, 'Ost': -90, 'West': 90, 'Nord-Ost': -135, 'Nord-West': 135, 'Nord': 180 };
            return map[ausrichtung] ?? 0;
        }

        function getPvVerlust() {
            return clamp(Number(state.config.pvSystemVerlust ?? 14) || 14, 0, 40);
        }

        function dachPvKey(d) {
            return `${standort.lat}|${standort.lon}|${Math.round(Number(d.neigung) || 0)}|${getPvgisAspect(d.ausrichtung)}|${getPvVerlust()}`;
        }

        // PVGIS-Ergebnisse 30 Tage im Browser cachen: Wetterdaten ändern sich nicht,
        // der zweite Aufruf am selben Standort ist sofort da und schont die EU-API
        const PVGIS_CACHE_TAGE = ASSUMPTIONS.technical.climateCacheDays;
        function pvgisCacheGet(key) {
            try {
                const raw = localStorage.getItem('pvgis:' + key);
                if (!raw) return null;
                const obj = JSON.parse(raw);
                if (!obj.ts || Date.now() - obj.ts > PVGIS_CACHE_TAGE * 86400000) return null;
                return obj.daten;
            } catch (e) { return null; }
        }
        function pvgisCacheSet(key, daten) {
            try { localStorage.setItem('pvgis:' + key, JSON.stringify({ ts: Date.now(), daten })); } catch (e) { }
        }

        // Wird bei jedem Render aufgerufen – Statusflags verhindern Doppel-Anfragen
        function syncStandortDaten() {
            if (typeof document === 'undefined') return; // Rechenkern-Tests in Node
            const plz = String(state.config.plz || '');
            if (!/^\d{5}$/.test(plz)) return;

            if (standort.plz !== plz) {
                Object.assign(standort, {
                    plz, lat: null, lon: null, ort: null, klimaStatus: 'laden',
                    heiztage: null, gtz: null, natTmy: null, hgtMonat: null, pv: {}
                });
                const cachedOrt = pvgisCacheGet('ort|' + plz);
                if (cachedOrt) {
                    Object.assign(standort, cachedOrt);
                    ladeKlimaTmy();
                    syncStandortDaten(); // Dachflächen nachladen
                    return;
                }
                fetch('https://api.zippopotam.us/de/' + plz)
                    .then(r => (r.ok ? r.json() : null))
                    .then(d => {
                        if (standort.plz !== plz) return;
                        const p = d && d.places && d.places[0];
                        if (!p) { standort.klimaStatus = 'fehler'; renderApp(); return; }
                        standort.ort = p['place name'];
                        standort.lat = Number(p.latitude).toFixed(3);
                        standort.lon = Number(p.longitude).toFixed(3);
                        pvgisCacheSet('ort|' + plz, { ort: standort.ort, lat: standort.lat, lon: standort.lon });
                        ladeKlimaTmy();
                        syncStandortDaten(); // Dachflächen nachladen
                    })
                    .catch(() => { if (standort.plz === plz) standort.klimaStatus = 'fehler'; });
                return;
            }

            if (standort.lat && state.config.modulePV) {
                state.config.dachseiten.forEach(d => {
                    const key = dachPvKey(d);
                    if (standort.pv[key] === undefined) ladeDachPvgis(d, key);
                });
            }
        }

        function ladeKlimaTmy() {
            const plz = standort.plz;
            const cacheKey = `klima|${standort.lat}|${standort.lon}`;
            const cached = pvgisCacheGet(cacheKey);
            if (cached) {
                Object.assign(standort, cached, { klimaStatus: 'ok' });
                renderApp();
                return;
            }
            fetch(`${PVGIS_PROXY_URL}/tmy?lat=${standort.lat}&lon=${standort.lon}&outputformat=json`)
                .then(r => (r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status))))
                .then(d => {
                    if (standort.plz !== plz) return;
                    const h = d.outputs.tmy_hourly;
                    const tage = [];
                    for (let i = 0; i + 24 <= h.length; i += 24) {
                        let s = 0;
                        for (let j = i; j < i + 24; j++) s += h[j].T2m;
                        tage.push(s / 24);
                    }
                    // Heizgrenze 15 °C; Gradtagzahl nach VDI-Konvention 20/15
                    const heiz = tage.filter(t => t < ASSUMPTIONS.technical.heatingLimitCelsius);
                    standort.heiztage = heiz.length;
                    standort.gtz = Math.round(heiz.reduce((s, t) => s + (ASSUMPTIONS.technical.indoorDesignTempCelsius - t), 0));
                    let nat = 99;
                    for (let i = 0; i < tage.length - 1; i++) nat = Math.min(nat, (tage[i] + tage[i + 1]) / 2);
                    standort.natTmy = Math.round(nat * 10) / 10;
                    const mlen = ASSUMPTIONS.profiles.monthDays;
                    let idx = 0;
                    const anteile = mlen.map(ml => {
                        let s = 0;
                        for (let k = idx; k < Math.min(idx + ml, tage.length); k++) {
                            if (tage[k] < ASSUMPTIONS.technical.heatingLimitCelsius) s += ASSUMPTIONS.technical.indoorDesignTempCelsius - tage[k];
                        }
                        idx += ml;
                        return s;
                    });
                    const summe = anteile.reduce((a, b) => a + b, 0) || 1;
                    standort.hgtMonat = anteile.map(a => a / summe);
                    standort.klimaStatus = 'ok';
                    pvgisCacheSet(cacheKey, {
                        heiztage: standort.heiztage, gtz: standort.gtz,
                        natTmy: standort.natTmy, hgtMonat: standort.hgtMonat
                    });
                    renderApp();
                })
                .catch(() => {
                    if (standort.plz === plz) { standort.klimaStatus = 'fehler'; renderApp(); }
                });
        }

        function ladeDachPvgis(d, key) {
            const cached = pvgisCacheGet('pv|' + key);
            if (cached) {
                standort.pv[key] = { status: 'ok', ey: cached.ey, monat: cached.monat };
                renderApp();
                return;
            }
            standort.pv[key] = { status: 'laden' };
            const neigung = Math.round(Number(d.neigung) || 0);
            const aspect = getPvgisAspect(d.ausrichtung);
            fetch(`${PVGIS_PROXY_URL}/PVcalc?lat=${standort.lat}&lon=${standort.lon}&peakpower=1&loss=${getPvVerlust()}&angle=${neigung}&aspect=${aspect}&outputformat=json`)
                .then(r => (r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status))))
                .then(p => {
                    const ey = Number(p.outputs.totals.fixed.E_y);
                    const em = p.outputs.monthly.fixed.map(m => Number(m.E_m));
                    const summe = em.reduce((a, b) => a + b, 0) || 1;
                    standort.pv[key] = { status: 'ok', ey: Math.round(ey), monat: em.map(x => x / summe) };
                    pvgisCacheSet('pv|' + key, { ey: standort.pv[key].ey, monat: standort.pv[key].monat });
                    renderApp();
                })
                .catch(() => { standort.pv[key] = { status: 'fehler' }; });
        }

        function getLiveKlima() {
            return (standort.klimaStatus === 'ok' && standort.plz === String(state.config.plz || ''))
                ? standort : null;
        }

        function getLivePvDach(d) {
            if (!standort.lat || standort.plz !== String(state.config.plz || '')) return null;
            const e = standort.pv[dachPvKey(d)];
            return (e && e.status === 'ok') ? e : null;
        }


        function getPvgisSnapshot() {
            return {
                plz: standort.plz,
                lat: standort.lat,
                lon: standort.lon,
                ort: standort.ort,
                klimaStatus: standort.klimaStatus,
                heiztage: standort.heiztage,
                gtz: standort.gtz,
                natTmy: standort.natTmy,
                hgtMonat: standort.hgtMonat,
                pvStatus: Object.fromEntries(Object.entries(standort.pv || {}).map(([key, val]) => [key, {
                    status: val?.status || 'idle',
                    ey: val?.ey || null,
                    monat: val?.monat || null
                }]))
            };
        }

        function sleepPvgis(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        async function ensurePvgisReadyBeforeSave(maxWaitMs = 7000) {
            syncStandortDaten();

            const plzOk = /^\d{5}$/.test(String(state.config.plz || ''));
            if (!plzOk) return false;

            const started = Date.now();
            while (Date.now() - started < maxWaitMs) {
                const klimaReady = !state.config.moduleWP || standort.klimaStatus === 'ok' || standort.klimaStatus === 'fehler';
                const pvReady = !state.config.modulePV || state.config.dachseiten.every(d => {
                    const entry = standort.pv[dachPvKey(d)];
                    return entry && (entry.status === 'ok' || entry.status === 'fehler');
                });

                if (klimaReady && pvReady) return true;
                await sleepPvgis(250);
            }

            return false;
        }

        // =========================================================
        // ENTWURFS-SICHERUNG: spiegelt die Eingaben nach localStorage,
        // damit ein versehentlicher Reload im Beratungsgespräch nichts
        // verwirft. In der DB gespeichert wird weiterhin nur über "Speichern".
        // =========================================================
        const ENTWURF_KEY = 'profitCalcEntwurf';
        let entwurfZumWiederherstellen = null;

        function speichereEntwurf() {
            try {
                localStorage.setItem(ENTWURF_KEY, JSON.stringify({
                    ts: Date.now(),
                    wizardStep: state.wizardStep,
                    config: state.config
                }));
            } catch (e) { }
        }

        function loescheEntwurf() {
            try { localStorage.removeItem(ENTWURF_KEY); } catch (e) { }
            entwurfZumWiederherstellen = null;
        }

        function pruefeEntwurf() {
            try {
                const raw = localStorage.getItem(ENTWURF_KEY);
                if (!raw) return;
                const e = JSON.parse(raw);
                if (!e.config || Date.now() - e.ts > 7 * 86400000) return;
                // Eine aus der DB geladene Berechnung hat Vorrang vor dem lokalen Entwurf
                if (typeof backendFrontendConfig !== 'undefined' && backendFrontendConfig) return;
                if (JSON.stringify(e.config) === JSON.stringify(state.config)) return;
                entwurfZumWiederherstellen = e;
            } catch (err) { }
        }

        function stelleEntwurfWieder() {
            if (!entwurfZumWiederherstellen) return;
            state.config = entwurfZumWiederherstellen.config;
            if (entwurfZumWiederherstellen.wizardStep) state.wizardStep = entwurfZumWiederherstellen.wizardStep;
            entwurfZumWiederherstellen = null;
            renderApp();
        }

        function entwurfBannerHtml() {
            if (!entwurfZumWiederherstellen) return '';
            const wann = new Date(entwurfZumWiederherstellen.ts)
                .toLocaleString('de-DE', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
            return `
        <div class="no-print fixed bottom-4 right-4 z-50 bg-white border border-slate-200 rounded-xl shadow-2xl p-4 max-w-sm animate-fade-in">
          <div class="text-sm font-bold text-dark-600 mb-1">Nicht gespeicherte Sitzung gefunden</div>
          <div class="text-xs text-slate-500 mb-3">Eingaben vom ${wann} Uhr wiederherstellen?</div>
          <div class="flex gap-2">
            <button onclick="stelleEntwurfWieder()"
              class="flex-1 px-3 py-2 text-white text-xs font-bold rounded-lg"
              style="background:var(--color-primary)">Wiederherstellen</button>
            <button onclick="loescheEntwurf(); renderApp();"
              class="px-3 py-2 text-xs font-bold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200">Verwerfen</button>
          </div>
        </div>
      `;
        }

        function getOrientationFactor(ausrichtung) {
            switch (ausrichtung) {
                case 'Süd': return 1.0;
                case 'Süd-Ost':
                case 'Süd-West': return 0.95;
                case 'Ost':
                case 'West': return 0.85;
                case 'Nord-Ost':
                case 'Nord-West': return 0.65;
                case 'Nord': return 0.55;
                default: return 1.0;
            }
        }

        function getKlimaDaten(plzStr) {
            const firstChar = String(plzStr).charAt(0);
            const start = /\d/.test(firstChar) ? Number(firstChar) : 5;
            if (start === 8 || start === 9) return { nat: -14, hgt: 4000, vbh: 2200 };
            if (start === 1 || start === 2) return { nat: -10, hgt: 3200, vbh: 1900 };
            if (start === 0 || start === 3) return { nat: -12, hgt: 3600, vbh: 2100 };
            if (start === 4 || start === 5) return { nat: -10, hgt: 3000, vbh: 1850 };
            return { nat: -12, hgt: 3500, vbh: 2000 };
        }

        function getHeizEinheit(art) {
            if (art === 'Öl') return 'Liter';
            if (art === 'Holz / Pellets') return 'Tonnen';
            return 'kWh';
        }

        function getHeizMediumKwh(menge, art) {
            if (art === 'Öl') return menge * 10;
            if (art === 'Holz / Pellets') return menge * 4800;
            return menge;
        }

        function formatDE(num, decimals = 0) {
            return Number(num || 0).toLocaleString('de-DE', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        function clamp(val, min, max) {
            return Math.max(min, Math.min(max, val));
        }

        // Plausibilitätsprüfung der Eingaben – Warnungen blockieren nicht, machen den
        // Berater aber auf unrealistische Werte aufmerksam, bevor der Report erstellt wird
        function getPlausibilityWarnings(config, derivedParams) {
            const w = [];

            if (!/^\d{5}$/.test(String(config.plz || ''))) {
                w.push({ step: 'projekt', field: 'plz', text: 'PLZ unvollständig – Klima- & Solarertrags-Faktoren nutzen den Bundesdurchschnitt.' });
            }

            const hh = Number(config.hhStrom || 0);
            if (hh < ASSUMPTIONS.plausibility.householdKwhMin || hh > ASSUMPTIONS.plausibility.householdKwhMax) {
                w.push({ step: 'gebaeude', field: 'hhStrom', text: `Haushaltsstrom ${formatDE(hh)} kWh/a ist ungewöhnlich (typisch ${ASSUMPTIONS.plausibility.householdTypicalText}).` });
            }

            if (config.moduleWP) {
                const v = Number(config.heizVerbrauch || 0);
                const r = ASSUMPTIONS.plausibility.heatingConsumptionRanges[config.heizungArt];
                if (r && (v < r[0] || v > r[1])) {
                    w.push({ step: 'heizung', field: 'heizVerbrauch', text: `Heizverbrauch ${formatDE(v)} ${getHeizEinheit(config.heizungArt)} (${config.heizungArt}) liegt außerhalb des typischen Bereichs ${formatDE(r[0])}–${formatDE(r[1])}.` });
                }
                const jaz = Number(derivedParams.jaz || 0);
                if (jaz > 0 && (jaz < ASSUMPTIONS.plausibility.jazMin || jaz > ASSUMPTIONS.plausibility.jazMax)) {
                    w.push({ step: 'heizung', text: `JAZ ${jaz} ist unplausibel (üblich ${ASSUMPTIONS.plausibility.jazTypicalText}) – Heizsystem & Verbrauch prüfen.` });
                }
            }

            const ps = Number(config.preisStrom || 0);
            if (ps < ASSUMPTIONS.plausibility.electricityPriceMin || ps > ASSUMPTIONS.plausibility.electricityPriceMax) {
                w.push({ step: 'invest', field: 'preisStrom', text: `Strompreis ${formatDE(ps, 2)} €/kWh prüfen (üblich ${ASSUMPTIONS.plausibility.electricityPriceTypicalText}).` });
            }
            if (config.modulePV) {
                const pe = Number(config.preisEinspeisung || 0);
                const misch = getEegMischverguetung(derivedParams.pvKwp, config.einspeiseArt);
                if (config.einspeiseArt === 'voll' && Number(derivedParams.batteryCapacity) > 0) {
                    w.push({ step: 'invest', text: 'Volleinspeisung gewählt: Ein Batteriespeicher ist dabei wirkungslos (kein Eigenverbrauch zulässig) – Speichergröße auf 0 setzen.' });
                }
                if (Math.abs(pe - misch) > ASSUMPTIONS.plausibility.feedInDeviationWarnEuroPerKwh) {
                    w.push({ step: 'invest', field: 'preisEinspeisung', text: `Einspeisevergütung ${formatDE(pe * 100, 2)} ct/kWh weicht vom EEG-Mischsatz für ${formatDE(derivedParams.pvKwp, 1)} kWp ab (~${formatDE(misch * 100, 2)} ct/kWh).` });
                }
                if (Number(derivedParams.pvKwp) > ASSUMPTIONS.plausibility.pvKwpRegulatoryWarning) {
                    w.push({ step: 'dach', text: `${formatDE(derivedParams.pvKwp, 1)} kWp überschreitet ${ASSUMPTIONS.plausibility.pvKwpRegulatoryWarning} kWp – abweichende Vergütungs-/Steuerregeln möglich.` });
                }
            }

            return w;
        }

        function destroyChart(id) {
            if (charts[id]) {
                charts[id].destroy();
                delete charts[id];
            }
        }

        // =========================================================
        // CALCULATIONS
        // =========================================================
        function getDerivedParams() {
            const config = state.config;

            // Klima: Heiztage & Gradtagzahl empirisch aus PVGIS-TMY, sobald geladen.
            // NAT und Vollbenutzungsstunden bleiben bewusst Normwerte aus der Tabelle
            // (Auslegung nach DIN braucht Extremwerte, das TMY ist ein Durchschnittsjahr).
            const klimaTabelle = getKlimaDaten(config.plz);
            const liveKlima = getLiveKlima();
            const klima = {
                ...klimaTabelle,
                hgt: liveKlima ? liveKlima.gtz : klimaTabelle.hgt,
                heiztage: liveKlima ? liveKlima.heiztage : null,
                klimaQuelle: liveKlima ? 'PVGIS-TMY' : 'Tabelle'
            };

            const activeHeizVerbrauch = config.moduleWP ? config.heizVerbrauch : 0;
            const activeFahrleistung = config.moduleWB ? config.fahrleistung : 0;
            const activeKamin = config.moduleWP ? config.kaminVorhanden : false;
            const activeSolar = config.moduleWP ? config.solarthermieVorhanden : false;

            let systemVerlust = config.heizungAlter > ASSUMPTIONS.technical.oldHeatingFastTrackAgeYears
                ? ASSUMPTIONS.technical.systemLossByHeatingAge.over20
                : (config.heizungAlter > ASSUMPTIONS.technical.mediumHeatingAgeYears ? ASSUMPTIONS.technical.systemLossByHeatingAge.over10 : ASSUMPTIONS.technical.systemLossByHeatingAge.default);
            let thermischHauptsystem = getHeizMediumKwh(activeHeizVerbrauch, config.heizungArt) * (1 - systemVerlust);

            let thermischKaminPotenziell = activeKamin ? config.holzVerbrauch * ASSUMPTIONS.technical.woodStoveKwhPerRm * ASSUMPTIONS.technical.woodStoveEfficiency : 0;
            let thermischSolarPotenziell = activeSolar ? config.solarKollektoren * ASSUMPTIONS.technical.solarThermalCollectorAreaM2 * (config.solarthermieArt === 'Flachkollektor' ? ASSUMPTIONS.technical.solarThermalYieldFlatKwhM2 : ASSUMPTIONS.technical.solarThermalYieldTubeKwhM2) : 0;

            const gesamtWaermeBedarfHaus = thermischHauptsystem + thermischKaminPotenziell + thermischSolarPotenziell;
            const berechneteHeizlast = (gesamtWaermeBedarfHaus / klima.vbh).toFixed(1);

            let empfohleneWpKw = config.moduleWP ? Math.ceil(gesamtWaermeBedarfHaus / klima.vbh) : 0;
            let wpLeistungKW = config.moduleWP ? (config.customWpKw !== '' ? Number(config.customWpKw) : empfohleneWpKw) : 0;

            const bivalenzpunkt = klima.nat >= ASSUMPTIONS.technical.bivalencePoint.mildNatThreshold
                ? ASSUMPTIONS.technical.bivalencePoint.mild
                : ASSUMPTIONS.technical.bivalencePoint.cold;

            let wwBedarfThermisch = (config.moduleWP && config.warmwasserArt === 'Zentral') ? config.personen * ASSUMPTIONS.technical.warmWaterKwhPerPerson : 0;
            if (config.moduleWP && config.zirkulation && config.warmwasserArt === 'Zentral') wwBedarfThermisch += ASSUMPTIONS.technical.circulationKwhDefault;

            let heizWärmeBedarf = Math.max(0, thermischHauptsystem - wwBedarfThermisch);

            let heizWärmeNachAbzug = heizWärmeBedarf;
            let wwBedarfNachAbzug = wwBedarfThermisch;

            if (config.moduleWP && config.kaminWeiterBetreiben) heizWärmeNachAbzug -= thermischKaminPotenziell;

            if (config.moduleWP && config.solarthermieWeiterBetreiben) {
                let solarRest = thermischSolarPotenziell;
                let wwAbzug = Math.min(wwBedarfNachAbzug, solarRest);
                wwBedarfNachAbzug -= wwAbzug;
                solarRest -= wwAbzug;
                heizWärmeNachAbzug -= solarRest;
            }

            heizWärmeNachAbzug = Math.max(0, heizWärmeNachAbzug);
            wwBedarfNachAbzug = Math.max(0, wwBedarfNachAbzug);

            let copSH = config.heizSystem === 'Fußbodenheizung' ? 4.2 : (config.heizSystem === 'Beides' ? 3.8 : 3.2);
            let copWW = 3.0;

            let wpStromHeizung = heizWärmeNachAbzug / copSH;
            let wpStromWW = wwBedarfNachAbzug / copWW;
            let berechneterWpStrombedarf = Math.round(wpStromHeizung + wpStromWW);

            let realeWpWaermeBedarf = heizWärmeNachAbzug + wwBedarfNachAbzug;
            let berechneteJaz = berechneterWpStrombedarf > 0 ? (realeWpWaermeBedarf / berechneterWpStrombedarf).toFixed(2) : copSH.toFixed(2);

            let jaz = config.customJAZ !== '' ? Number(config.customJAZ).toFixed(2) : berechneteJaz;
            let cop = Number(jaz);

            let wpStrombedarf = config.moduleWP ? (config.customJAZ !== '' ? Math.round(realeWpWaermeBedarf / cop) : berechneterWpStrombedarf) : 0;
            let umweltEnergie = config.moduleWP ? (realeWpWaermeBedarf - wpStrombedarf) : 0;

            const evStrombedarf = config.moduleWB ? Math.round(activeFahrleistung * ASSUMPTIONS.technical.evConsumptionKwhPerKm) : 0;
            const gesamtStrombedarf = config.hhStrom + wpStrombedarf + evStrombedarf;

            const { pvBaseFactor, wpFactor } = getRegionalFactors(config.plz);
            const avgYieldFactor = config.dachseiten.reduce((acc, curr) => acc + getOrientationFactor(curr.ausrichtung), 0) / config.dachseiten.length;

            // Ertrag je Dachfläche: empirisch aus PVGIS (berücksichtigt Neigung & Ausrichtung
            // am echten Standort), solange nicht geladen Schätzung aus Regionalfaktor
            const verlustSkala = (100 - clamp(Number(config.pvSystemVerlust ?? 14) || 14, 0, 40)) / 86;
            const dachErtraege = config.dachseiten.map(d => {
                const live = getLivePvDach(d);
                return {
                    id: d.id,
                    ey: live ? live.ey : Math.round(pvBaseFactor * getOrientationFactor(d.ausrichtung) * verlustSkala),
                    monat: live ? live.monat : null,
                    quelle: live ? 'PVGIS' : 'Schätzwert'
                };
            });
            const dachGewichte = config.dachseiten.map(d => (d.customKwp && d.customKwp !== '') ? Number(d.customKwp) : 1);
            const gewichtSumme = dachGewichte.reduce((a, b) => a + b, 0) || 1;
            const effectiveYieldPvKwp = config.modulePV
                ? dachErtraege.reduce((s, e, i) => s + e.ey * dachGewichte[i], 0) / gewichtSumme
                : 0;
            const pvQuelle = dachErtraege.every(e => e.quelle === 'PVGIS') ? 'PVGIS'
                : (dachErtraege.some(e => e.quelle === 'PVGIS') ? 'PVGIS + Schätzwert' : 'Schätzwert');

            // Monatsprofile: PV gewichtet aus den PVGIS-Profilen der Dächer,
            // Heizung aus der TMY-Gradtag-Verteilung – Fallback sind die Pauschalprofile
            const pvMonatsAnteile = MONTHS.map((_, mi) => {
                let s = 0;
                dachErtraege.forEach((e, i) => { s += (e.monat ? e.monat[mi] : PV_DISTRIBUTION[mi]) * dachGewichte[i]; });
                return s / gewichtSumme;
            });
            const wpMonatsAnteile = (liveKlima && liveKlima.hgtMonat) ? liveKlima.hgtMonat : null;

            const hasOst = config.dachseiten.some(d => d.ausrichtung.includes('Ost'));
            const hasWest = config.dachseiten.some(d => d.ausrichtung.includes('West'));
            const hasSued = config.dachseiten.some(d => d.ausrichtung === 'Süd');
            const isEastWestProfile = hasOst && hasWest;

            let baseBattery = gesamtStrombedarf / 1000;
            let batterySpreadFactor = isEastWestProfile ? 0.8 : (hasSued ? 1.2 : 1.0);
            let empfohleneBatterie = config.modulePV ? Math.max(ASSUMPTIONS.technical.minRecommendedBatteryKwh, Math.round(baseBattery * batterySpreadFactor)) : 0;
            let batteryCapacity = config.modulePV ? (config.customBatteryKwh !== '' ? Number(config.customBatteryKwh) : empfohleneBatterie) : 0;

            const pvDimensionierungsFaktor = ASSUMPTIONS.technical.pvSizingFactor;
            let empfohlenePv = config.modulePV ? Math.max(3, Math.round((gesamtStrombedarf * pvDimensionierungsFaktor) / effectiveYieldPvKwp * 10) / 10) : 0;

            let manualPvKwpSum = 0;
            config.dachseiten.forEach(d => {
                if (d.customKwp && d.customKwp !== '') manualPvKwpSum += Number(d.customKwp);
            });

            let pvKwp = config.modulePV ? (manualPvKwpSum > 0 ? manualPvKwpSum : (config.customPvKwp !== '' ? Number(config.customPvKwp) : Math.ceil(empfohlenePv))) : 0;

            const distributedDachseiten = config.dachseiten.map(d => ({
                ...d,
                calculatedKwp: config.modulePV ? ((d.customKwp && d.customKwp !== '') ? Number(d.customKwp) : Number((empfohlenePv / config.dachseiten.length).toFixed(1))) : 0
            }));

            const verbrennerLiterKosten = (config.moduleWB && config.autoArt === 'Verbrenner')
                ? (activeFahrleistung / 100) * config.verbrennerVerbrauch * config.preisSprit
                : 0;

            const verbrennerKwhEquivalent = (config.moduleWB && config.autoArt === 'Verbrenner')
                ? (activeFahrleistung / 100) * config.verbrennerVerbrauch * 9
                : 0;

            return {
                klima, wpStrombedarf, cop, jaz, berechneteJaz, copSH, copWW, evStrombedarf, gesamtStrombedarf, realeWpWaermeBedarf, umweltEnergie,
                wpLeistungKW, pvKwp, batteryCapacity, empfohleneWpKw, empfohlenePv, empfohleneBatterie, berechneteHeizlast, gesamtWaermeBedarfHaus, bivalenzpunkt,
                verbrennerLiterKosten, verbrennerKwhEquivalent, wpFactor, effectiveYieldPvKwp, batterySpreadFactor,
                dachErtraege, pvQuelle, pvMonatsAnteile, wpMonatsAnteile,
                isEastWestProfile, hasSued, avgYieldFactor, distributedDachseiten, manualPvKwpSum,
                kaminKosten: activeKamin ? config.holzVerbrauch * config.preisHolz : 0,
                thermischKaminPotenziell, thermischSolarPotenziell, thermischHauptsystem, heizVerbrauchKwh: getHeizMediumKwh(activeHeizVerbrauch, config.heizungArt),
                systemVerlust, wwBedarfThermisch, heizWärmeBedarf, wwBedarfNachAbzug, heizWärmeNachAbzug
            };
        }

        function getSimulation(derivedParams) {
            const config = state.config;
            const theme = getActiveTheme();

            const kwp = Number(derivedParams?.pvKwp || 0);
            const batteryCapacity = Number(derivedParams?.batteryCapacity || 0);
            const wpJahresVerbrauch = Number(derivedParams?.wpStrombedarf || 0);
            const evStrombedarf = Number(derivedParams?.evStrombedarf || 0);

            // Sektor-Deckung wird verursachergerecht aus der Monatssimulation akkumuliert
            // (statt fester Autarkie-Annahmen) – reagiert damit auf PV-Größe, Speicher & Ausrichtung
            let hhDeckung = 0;
            let wpDeckung = 0;
            let evDeckung = 0;

            let totalPV = 0;
            let totalBedarf = 0;
            let totalDirekt = 0;
            let totalBatterie = 0;
            let totalNetzbezug = 0;
            let totalNetzeinspeisung = 0;

            const seasonalAgg = {
                Winter: { name: 'Winter', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
                Frühling: { name: 'Frühling', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
                Sommer: { name: 'Sommer', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
                Herbst: { name: 'Herbst', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 }
            };

            const monthDays = ASSUMPTIONS.profiles.monthDays;

            const getSeasonByMonthIndex = (index) => {
                if (index === 11 || index <= 1) return 'Winter';
                if (index >= 2 && index <= 4) return 'Frühling';
                if (index >= 5 && index <= 7) return 'Sommer';
                return 'Herbst';
            };

            const chartData = MONTHS.map((month, index) => {
                const days = monthDays[index];

                const pvErtragMo = config.modulePV
                    ? kwp * Number(derivedParams.effectiveYieldPvKwp || 0) * ((derivedParams.pvMonatsAnteile || PV_DISTRIBUTION)[index])
                    : 0;

                const hhBedarfMo = Number(config.hhStrom || 0) * HH_DISTRIBUTION[index];
                // Kein wpFactor hier: das Regionalklima steckt bereits im realen Heizverbrauch
                // des Kunden – sonst summieren die Monate nicht auf wpStrombedarf auf
                const wpBedarfMo = config.moduleWP
                    ? wpJahresVerbrauch * ((derivedParams.wpMonatsAnteile || HGT_DISTRIBUTION)[index])
                    : 0;
                const evBedarfMo = config.moduleWB
                    ? evStrombedarf * EV_DISTRIBUTION[index]
                    : 0;

                const gesamtBedarfMo = hhBedarfMo + wpBedarfMo + evBedarfMo;

                const hhTag = (hhBedarfMo / days) * DAYLIGHT_RATIO[index];
                const wpTag = (wpBedarfMo / days) * DAYLIGHT_RATIO[index];
                const evTag = (evBedarfMo / days) * ASSUMPTIONS.technical.evDayChargingShare;

                const bedarfTagDaily = hhTag + wpTag + evTag;
                const bedarfNachtDaily = Math.max(0, (gesamtBedarfMo / days) - bedarfTagDaily);

                const pvDaily = pvErtragMo / days;
                // Volleinspeisung (§ 21 EEG): rechtlich getrennte Anlage ohne Eigenverbrauch –
                // gesamte Produktion wird vergütet, der Bedarf voll aus dem Netz gedeckt
                const vollEinspeisung = config.einspeiseArt === 'voll';
                const direktDaily = vollEinspeisung ? 0 : Math.max(0, Math.min(pvDaily, bedarfTagDaily));
                // Speicher: nur so viel laden, wie nachts (inkl. 10 % Wirkungsgradverlust)
                // tatsächlich gebraucht wird – ist der Nachtbedarf gedeckt oder der Speicher
                // voll, wird der Überschuss eingespeist statt im Tagesmodell zu verfallen
                const ladungRoh = vollEinspeisung ? 0 : Math.max(0, Math.min(pvDaily - direktDaily, batteryCapacity));
                const chargeDaily = Math.min(ladungRoh, bedarfNachtDaily / ASSUMPTIONS.technical.batteryRoundtripEfficiency);
                const dischargeDaily = chargeDaily * ASSUMPTIONS.technical.batteryRoundtripEfficiency;

                const direktDeckung = Math.round(direktDaily * days);
                const batterieLadung = Math.round(chargeDaily * days);
                const batterieDeckung = Math.round(dischargeDaily * days);

                // Direktdeckung anteilig auf die Tag-Bedarfe, Batteriedeckung anteilig
                // auf die Nacht-Bedarfe der Sektoren verteilen
                if (bedarfTagDaily > 0) {
                    const direktMo = direktDaily * days;
                    hhDeckung += direktMo * (hhTag / bedarfTagDaily);
                    wpDeckung += direktMo * (wpTag / bedarfTagDaily);
                    evDeckung += direktMo * (evTag / bedarfTagDaily);
                }
                const hhNacht = Math.max(0, (hhBedarfMo / days) - hhTag);
                const wpNacht = Math.max(0, (wpBedarfMo / days) - wpTag);
                const evNacht = Math.max(0, (evBedarfMo / days) - evTag);
                const nachtSum = hhNacht + wpNacht + evNacht;
                if (nachtSum > 0) {
                    const batterieMo = dischargeDaily * days;
                    hhDeckung += batterieMo * (hhNacht / nachtSum);
                    wpDeckung += batterieMo * (wpNacht / nachtSum);
                    evDeckung += batterieMo * (evNacht / nachtSum);
                }

                const gesamtDeckungMo = direktDeckung + batterieDeckung;
                const netzbezug = Math.max(0, Math.round(gesamtBedarfMo - gesamtDeckungMo));
                const netzeinspeisung = Math.max(0, Math.round(pvErtragMo - direktDeckung - batterieLadung));

                const season = getSeasonByMonthIndex(index);

                seasonalAgg[season].Solarertrag += pvErtragMo;
                seasonalAgg[season].Gesamtbedarf += gesamtBedarfMo;
                seasonalAgg[season].DirektDeckung += direktDeckung;
                seasonalAgg[season].BatterieDeckung += batterieDeckung;
                seasonalAgg[season].Netzbezug += netzbezug;
                seasonalAgg[season].NetzeinspeisungNeg -= netzeinspeisung;

                totalPV += pvErtragMo;
                totalBedarf += gesamtBedarfMo;
                totalDirekt += direktDeckung;
                totalBatterie += batterieDeckung;
                totalNetzbezug += netzbezug;
                totalNetzeinspeisung += netzeinspeisung;

                return {
                    name: month,
                    Solarertrag: Math.round(pvErtragMo),
                    Gesamtbedarf: Math.round(gesamtBedarfMo),
                    DirektDeckung: direktDeckung,
                    BatterieDeckung: batterieDeckung,
                    Netzbezug: netzbezug,
                    BatterieLadungNeg: -batterieLadung,
                    NetzeinspeisungNeg: -netzeinspeisung,
                    GesamtDeckung: gesamtDeckungMo
                };
            });

            const seasonalData = ['Winter', 'Frühling', 'Sommer', 'Herbst'].map((seasonKey) => {
                const item = seasonalAgg[seasonKey];
                const totalSeasonDeckung = item.DirektDeckung + item.BatterieDeckung;

                const calcSeasonAutarkie = item.Gesamtbedarf > 0
                    ? Math.round((totalSeasonDeckung / item.Gesamtbedarf) * 100)
                    : (config.modulePV ? 100 : 0);

                return {
                    ...item,
                    Solarertrag: Math.round(item.Solarertrag),
                    Gesamtbedarf: Math.round(item.Gesamtbedarf),
                    DirektDeckung: Math.round(item.DirektDeckung),
                    BatterieDeckung: Math.round(item.BatterieDeckung),
                    Netzbezug: Math.round(item.Netzbezug),
                    NetzeinspeisungNeg: Math.round(item.NetzeinspeisungNeg),
                    autarkie: config.modulePV ? calcSeasonAutarkie : 0
                };
            });

            let fossilFactor = getEmissionFactorForHeating(config.heizungArt);

            const activeHeizVerbrauchKwh = config.moduleWP ? Number(derivedParams.heizVerbrauchKwh || 0) : 0;
            const activeEvFossilCo2 = (config.moduleWB && config.autoArt === 'Verbrenner')
                ? ((Number(config.fahrleistung || 0) / 100) * Number(config.verbrennerVerbrauch || 0) * ASSUMPTIONS.emissionFactorsKgPerKwh.petrolKgPerLiter)
                : 0;
            const activeKaminCo2 = (config.moduleWP && config.kaminVorhanden)
                ? Number(config.holzVerbrauch || 0) * ASSUMPTIONS.technical.woodStoveKwhPerRm * ASSUMPTIONS.emissionFactorsKgPerKwh.woodStove
                : 0;

            const oldCo2 = (
                (activeHeizVerbrauchKwh * fossilFactor) +
                (Number(config.hhStrom || 0) * ASSUMPTIONS.emissionFactorsKgPerKwh.gridElectricity) +
                activeEvFossilCo2 +
                activeKaminCo2
            );

            const newCo2 = totalNetzbezug * ASSUMPTIONS.emissionFactorsKgPerKwh.gridElectricity;

            let co2SavingsYear = (oldCo2 - newCo2) / 1000;
            if (co2SavingsYear < 0) co2SavingsYear = 0;

            const activeVerbrennerKwhEquivalent = config.moduleWB
                ? Number(derivedParams.verbrennerKwhEquivalent || 0)
                : 0;

            const activeThermischKaminPotenziell = config.moduleWP
                ? Number(derivedParams.thermischKaminPotenziell || 0)
                : 0;

            const oldEnergyKwh =
                activeHeizVerbrauchKwh +
                Number(config.hhStrom || 0) +
                activeVerbrennerKwhEquivalent +
                activeThermischKaminPotenziell;

            const energeticSavingsKwh = Math.round(oldEnergyKwh - totalNetzbezug);

            const bedarfsMix = [
                {
                    name: 'Haushalt',
                    value: Number(config.hhStrom || 0),
                    fill: theme.inactive
                }
            ];

            if (config.moduleWP) {
                bedarfsMix.push({
                    name: 'Wärmepumpe',
                    value: Number(derivedParams.wpStrombedarf || 0),
                    fill: theme.secondary
                });
            }

            if (config.moduleWB && Number(config.fahrleistung || 0) > 0) {
                bedarfsMix.push({
                    name: 'E-Auto',
                    value: Number(derivedParams.evStrombedarf || 0),
                    fill: theme.primary
                });
            }

            const totalDeckung = totalDirekt + totalBatterie;

            const totalPVRounded = Math.round(totalPV);
            const totalBedarfRounded = Math.round(totalBedarf);
            const totalNetzbezugRounded = Math.round(totalNetzbezug);
            const totalNetzeinspeisungRounded = Math.round(totalNetzeinspeisung);
            const totalDirektRounded = Math.round(totalDirekt);
            const totalBatterieRounded = Math.round(totalBatterie);

            const autarkie = (config.modulePV && totalBedarf > 0)
                ? Math.round((totalDeckung / totalBedarf) * 100)
                : 0;

            // Eigenverbrauch = selbst genutzte PV-Energie inkl. Batterieladung (= PV − Einspeisung),
            // damit gehen Batterieladeverluste korrekt zulasten des Eigenverbrauchs
            const eigenverbrauchQuote = (config.modulePV && totalPV > 0)
                ? Math.round(((totalPV - totalNetzeinspeisung) / totalPV) * 100)
                : 0;

            return {
                chartData,
                seasonalData,
                bedarfsMix,
                kpis: {
                    totalPV: totalPVRounded,
                    totalBedarf: totalBedarfRounded,
                    totalNetzbezug: totalNetzbezugRounded,
                    totalNetzeinspeisung: totalNetzeinspeisungRounded,
                    totalDirekt: totalDirektRounded,
                    totalBatterie: totalBatterieRounded,
                    autarkie,
                    eigenverbrauchQuote,
                    hhDeckung: Math.round(hhDeckung),
                    wpDeckung: Math.round(wpDeckung),
                    evDeckung: Math.round(evDeckung),
                    hhAutarkie: (config.modulePV && Number(config.hhStrom || 0) > 0)
                        ? Math.round((hhDeckung / Number(config.hhStrom)) * 100) : 0,
                    wpAutarkie: (config.modulePV && wpJahresVerbrauch > 0)
                        ? Math.round((wpDeckung / wpJahresVerbrauch) * 100) : 0,
                    evAutarkie: (config.modulePV && evStrombedarf > 0)
                        ? Math.round((evDeckung / evStrombedarf) * 100) : 0,
                    spezErtrag: Number(derivedParams.effectiveYieldPvKwp || 0),
                    oldEnergyKwh: Math.round(oldEnergyKwh),
                    energeticSavingsKwh: Math.max(0, energeticSavingsKwh)
                },
                co2: {
                    year: co2SavingsYear.toFixed(1),
                    tenYears: (co2SavingsYear * ASSUMPTIONS.finance.reportYears[0]).toFixed(1),
                    twentyYears: (co2SavingsYear * ASSUMPTIONS.finance.reportYears[1]).toFixed(1),
                    thirtyYears: (co2SavingsYear * ASSUMPTIONS.finance.reportYears[2]).toFixed(1),
                    trees: Math.round(co2SavingsYear * ASSUMPTIONS.technical.co2TreesPerTon),
                    forestArea: Math.round(co2SavingsYear * ASSUMPTIONS.technical.co2ForestM2PerTon),
                    oldKg: Math.round(oldCo2),
                    newKg: Math.round(newCo2),
                    savedKg: Math.round(Math.max(0, oldCo2 - newCo2))
                }
            };
        }

        function getFinance(derivedParams, kpis) {
            const config = state.config;

            const cWP = config.moduleWP ? config.costWP : 0;
            const cPV = config.modulePV ? config.costPV : 0;
            const cBat = config.modulePV ? config.costBattery : 0;
            const cWB = config.moduleWB ? config.costWallbox : 0;

            const isOldFossil = ['Öl', 'Kohle', 'Nachtspeicher'].includes(config.heizungArt) ||
                (['Gas', 'Holz / Pellets'].includes(config.heizungArt) && config.heizungAlter >= ASSUMPTIONS.technical.oldHeatingFastTrackAgeYears);

            const kfw = ASSUMPTIONS.funding.kfw458;
            const grundFoerderung = kfw.baseGrantPercent;
            const effizienzBonus = kfw.efficiencyBonusPercent;
            const klimaBonus = isOldFossil ? kfw.climateSpeedBonusPercent : 0;
            const einkommenBonus = kfw.incomeBonusPercent;

            let weDeckelung = getKfwEligibleCostCap(config.wohneinheiten);

            const effectiveWPCost = Math.max(0, cWP - (Number(config.discountWP) || 0));
            const foerderfaehigeKostenWP = Math.min(effectiveWPCost, weDeckelung);
            const costPerWE = foerderfaehigeKostenWP / config.wohneinheiten;

            const rentedWE = config.wohneinheiten - config.selbstbewohnteWE;
            const ownerNoLowIncWE = config.selbstbewohnteWE - config.weUnter40k;
            const ownerLowIncWE = config.weUnter40k;

            const baseProzent = grundFoerderung + effizienzBonus;
            const ownerNoLowIncProzent = Math.min(kfw.maxGrantPercent, baseProzent + klimaBonus);
            const ownerLowIncProzent = Math.min(kfw.maxGrantPercent, baseProzent + klimaBonus + einkommenBonus);

            const kfwZuschuss = config.moduleWP ? Math.round(
                costPerWE * ((rentedWE * (baseProzent / 100)) + (ownerNoLowIncWE * (ownerNoLowIncProzent / 100)) + (ownerLowIncWE * (ownerLowIncProzent / 100)))
            ) : 0;

            const maxZuschussProzent = foerderfaehigeKostenWP > 0
                ? Math.round((kfwZuschuss / foerderfaehigeKostenWP) * 100)
                : 0;

            const discountWPNum = (config.moduleWP && cWP > 0) ? (Number(config.discountWP) || 0) : 0;
            const discountPVNum = (config.modulePV && cPV > 0) ? (Number(config.discountPV) || 0) : 0;
            const discountBatteryNum = (config.modulePV && cBat > 0) ? (Number(config.discountBattery) || 0) : 0;
            const discountWallboxNum = (config.moduleWB && cWB > 0) ? (Number(config.discountWallbox) || 0) : 0;

            const isKombiBonusActive = (
                discountWPNum > 0 ||
                discountPVNum > 0 ||
                discountBatteryNum > 0 ||
                discountWallboxNum > 0
            );
            const extraGrantWPNum = config.moduleWP ? (Number(config.extraGrantWP) || 0) : 0;
            const extraGrantPVNum = config.modulePV ? (Number(config.extraGrantPV) || 0) : 0;
            const extraGrantBatteryNum = config.modulePV ? (Number(config.extraGrantBattery) || 0) : 0;
            const extraGrantWallboxNum = config.moduleWB ? (Number(config.extraGrantWallbox) || 0) : 0;

            const totalInvest = cWP + cPV + cBat + cWB;
            const totalDiscount = discountWPNum + discountPVNum + discountBatteryNum + discountWallboxNum;
            const totalExtraGrant = extraGrantWPNum + extraGrantPVNum + extraGrantBatteryNum + extraGrantWallboxNum;
            const totalFoerderung = kfwZuschuss + totalExtraGrant;

            const nettoWP = cWP - discountWPNum - extraGrantWPNum - kfwZuschuss;
            const nettoPV = cPV - discountPVNum - extraGrantPVNum;
            const nettoBattery = cBat - discountBatteryNum - extraGrantBatteryNum;
            const nettoWallbox = cWB - discountWallboxNum - extraGrantWallboxNum;

            const nettoInvest = nettoWP + nettoPV + nettoBattery + nettoWallbox;

            const effPvCost = cPV - discountPVNum - extraGrantPVNum;
            const effBatCost = cBat - discountBatteryNum - extraGrantBatteryNum;
            const lcoe = (config.modulePV && kpis.totalPV > 0) ? ((effPvCost + effBatCost) / (kpis.totalPV * ASSUMPTIONS.finance.lcoeYears)).toFixed(2) : '0.00';

            const hhKostenOhne = Math.round(config.hhStrom * config.preisStrom);
            const wpKostenOhne = config.moduleWP ? Math.round(derivedParams.wpStrombedarf * config.preisStrom) : 0;
            const evKostenOhne = config.moduleWB ? Math.round(derivedParams.evStrombedarf * config.preisStrom) : 0;

            const hhNetz = Math.max(0, config.hhStrom - kpis.hhDeckung);
            const wpNetz = Math.max(0, derivedParams.wpStrombedarf - kpis.wpDeckung);
            const evNetz = Math.max(0, derivedParams.evStrombedarf - kpis.evDeckung);

            const evOldCost = config.moduleWB ? (config.autoArt === 'Verbrenner'
                ? derivedParams.verbrennerLiterKosten
                : (config.fahrleistung > 0 ? (config.fahrleistung / 100) * ASSUMPTIONS.technical.existingElectricCarKwhPer100Km * config.preisStrom : 0)) : 0;

            const heizkostenOld = config.moduleWP ? (config.heizVerbrauch * config.preisHeizMedium) : 0;
            const kaminCostOld = (config.moduleWP && config.kaminVorhanden) ? derivedParams.kaminKosten : 0;
            const stromCostOld = hhKostenOhne;

            const costOldBase = heizkostenOld + stromCostOld + config.wartungOld + evOldCost + kaminCostOld;

            const hasSteuVE = (config.moduleWP && derivedParams.wpStrombedarf > 0) || (config.moduleWB && cWB > 0);
            const steuVeBedarfAllElectric = derivedParams.wpStrombedarf + derivedParams.evStrombedarf;
            const ersparnis14aAllElectric = hasSteuVE
                ? Math.round(Math.max(ASSUMPTIONS.regulatory.enwg14a.flatSavingEuro, steuVeBedarfAllElectric * config.netzentgelt * ASSUMPTIONS.regulatory.enwg14a.variableGridFeeReductionShare))
                : 0;

            const steuVeNetz = wpNetz + evNetz;
            const ersparnis14a = hasSteuVE
                ? Math.round(Math.max(ASSUMPTIONS.regulatory.enwg14a.flatSavingEuro, steuVeNetz * config.netzentgelt * ASSUMPTIONS.regulatory.enwg14a.variableGridFeeReductionShare))
                : 0;

            const futureKaminCosts = (config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben) ? derivedParams.kaminKosten : 0;

            // Wartung des Neusystems (Fallback auf alte Pauschale für ältere gespeicherte Konfigurationen)
            const wartungNeu = Number(config.wartungNeu ?? config.wartungOld ?? 0);

            const costAllElectricBase = (derivedParams.gesamtStrombedarf * config.preisStrom) + wartungNeu - ersparnis14aAllElectric + futureKaminCosts;
            const costNewBase = (kpis.totalNetzbezug * config.preisStrom) - (kpis.totalNetzeinspeisung * config.preisEinspeisung) - ersparnis14a + futureKaminCosts + wartungNeu;

            const ersparnisJahr1 = Math.round(costOldBase - costNewBase);
            const ersparnisNurElektrisch = Math.round(costOldBase - costAllElectricBase);
            const ersparnisDurchPV = Math.round(costAllElectricBase - costNewBase);

            const finUnabhProzent = costOldBase > 0 ? Math.round((ersparnisJahr1 / costOldBase) * 100) : 0;

            const cashflow = [];
            let cumulativeCashflow = -nettoInvest;
            let cumulativeErsparnis = 0;

            let oldCostCumulative10 = 0, oldCostCumulative20 = 0, oldCostCumulative30 = 0;
            let electricCostCumulative10 = 0, electricCostCumulative20 = 0, electricCostCumulative30 = 0;
            let newCostCumulative10 = 0, newCostCumulative20 = 0, newCostCumulative30 = 0;

            let amortisationYear = null;
            let cumulativeOldCosts = 0, cumulativeElectricCosts = 0, cumulativeNewCosts = nettoInvest;

            // Getrennte Preispfade: Strom vs. fossile Energieträger (CO2-Bepreisung BEHG/ETS2
            // treibt Gas/Öl/Sprit stärker als Strom)
            const inflStrom = 1 + (Number(config.inflationRate ?? 3) / 100);
            const inflFossil = 1 + (Number(config.inflationRateFossil ?? config.inflationRate ?? 3) / 100);
            const wrErsatzKosten = config.modulePV ? Number(config.wrErsatzKosten ?? 0) : 0;

            const evOldIsFossil = config.moduleWB && config.autoArt === 'Verbrenner';
            const oldFossilBase = heizkostenOld + kaminCostOld + (evOldIsFossil ? evOldCost : 0);
            const oldStromBase = stromCostOld + (evOldIsFossil ? 0 : evOldCost);

            const totalDeckungSim = kpis.totalDirekt + kpis.totalBatterie;

            for (let i = 1; i <= ASSUMPTIONS.finance.analysisYears; i++) {
                const fStrom = Math.pow(inflStrom, i - 1);
                const fFossil = Math.pow(inflFossil, i - 1);

                const oldCostYear = oldFossilBase * fFossil + oldStromBase * fStrom + config.wartungOld;

                // PV-Degradation: Solar-Deckung & Einspeisung sinken, Netzbezug steigt entsprechend
                const degF = config.modulePV ? Math.pow(1 - PV_DEGRADATION, i - 1) : 1;
                const netzbezugYear = kpis.totalNetzbezug + totalDeckungSim * (1 - degF);
                const einspeisungYear = kpis.totalNetzeinspeisung * degF;

                // EEG-Vergütung: 20 Jahre nominal fest, danach konservativer Marktwert.
                // §14a-Rabatt bleibt nominal konstant. WR-Tausch einmalig zu heutigen Preisen.
                const verguetungSatz = i <= EEG_DAUER ? config.preisEinspeisung : Math.min(config.preisEinspeisung, MARKTWERT_SOLAR);
                const wrErsatz = i === WR_ERSATZ_JAHR ? wrErsatzKosten : 0;

                const electricCostYear = (derivedParams.gesamtStrombedarf * config.preisStrom) * fStrom + futureKaminCosts * fFossil + wartungNeu - ersparnis14aAllElectric;
                const newCostYear = netzbezugYear * config.preisStrom * fStrom + futureKaminCosts * fFossil - (einspeisungYear * verguetungSatz) - ersparnis14a + wartungNeu + wrErsatz;

                const currentYearSavings = oldCostYear - newCostYear;

                cumulativeOldCosts += oldCostYear;
                cumulativeElectricCosts += electricCostYear;
                cumulativeNewCosts += newCostYear;

                if (i === ASSUMPTIONS.finance.reportYears[0]) { oldCostCumulative10 = cumulativeOldCosts; electricCostCumulative10 = cumulativeElectricCosts; newCostCumulative10 = cumulativeNewCosts; }
                if (i === ASSUMPTIONS.finance.reportYears[1]) { oldCostCumulative20 = cumulativeOldCosts; electricCostCumulative20 = cumulativeElectricCosts; newCostCumulative20 = cumulativeNewCosts; }
                if (i === ASSUMPTIONS.finance.reportYears[2]) { oldCostCumulative30 = cumulativeOldCosts; electricCostCumulative30 = cumulativeElectricCosts; newCostCumulative30 = cumulativeNewCosts; }

                cumulativeCashflow += currentYearSavings;
                cumulativeErsparnis += currentYearSavings;

                if (cumulativeOldCosts > cumulativeNewCosts && !amortisationYear) amortisationYear = i;

                cashflow.push({
                    year: `${i}`,
                    yearLabel: `Jahr ${i}`,
                    kostenOhne: Math.round(cumulativeOldCosts),
                    kostenMit: Math.round(cumulativeNewCosts),
                    cashflow: Math.round(cumulativeCashflow),
                    ersparnis: Math.round(currentYearSavings),
                    kumulierteErsparnis: Math.round(cumulativeErsparnis)
                });
            }

            const avgSavings20 = cashflow[ASSUMPTIONS.finance.reportYears[1] - 1].kumulierteErsparnis / ASSUMPTIONS.finance.reportYears[1];
            const roi = nettoInvest > 0 ? ((avgSavings20 / nettoInvest) * 100).toFixed(1) : '0.0';

            return {
                maxZuschussProzent, weDeckelung, kfwZuschuss, totalInvest, totalDiscount, totalExtraGrant, totalFoerderung, nettoInvest, lcoe,
                nettoWP, nettoPV, nettoBattery, nettoWallbox,
                discountWPNum, discountPVNum, discountBatteryNum, discountWallboxNum,
                extraGrantWPNum, extraGrantPVNum, extraGrantBatteryNum, extraGrantWallboxNum,
                isKombiBonusActive,
                costOldTotal: costOldBase, costNewTotal: costNewBase, costAllElectricBase, ersparnisJahr1,
                ersparnisNurElektrisch, ersparnisDurchPV, amortisationYear, roi, finUnabhProzent, evOldCost,
                ersparnis10: cashflow[ASSUMPTIONS.finance.reportYears[0] - 1].kumulierteErsparnis,
                ersparnis20: cashflow[ASSUMPTIONS.finance.reportYears[1] - 1].kumulierteErsparnis,
                ersparnis30: cashflow[ASSUMPTIONS.finance.reportYears[2] - 1].kumulierteErsparnis,
                oldCostCumulative10: Math.round(oldCostCumulative10),
                oldCostCumulative20: Math.round(oldCostCumulative20),
                oldCostCumulative30: Math.round(oldCostCumulative30),
                electricCostCumulative10: Math.round(electricCostCumulative10),
                electricCostCumulative20: Math.round(electricCostCumulative20),
                electricCostCumulative30: Math.round(electricCostCumulative30),
                newCostCumulative10: Math.round(newCostCumulative10),
                newCostCumulative20: Math.round(newCostCumulative20),
                newCostCumulative30: Math.round(newCostCumulative30),
                cashflow,
                kfwDetails: { baseProzent, isOldFossil, klimaBonus, einkommenBonus, costPerWE },
                heizkostenOld, ersparnis14aAllElectric, ersparnis14a, futureKaminCosts, wartungNeu,
                hhKostenOhne, wpKostenOhne, evKostenOhne,
                hhNetz, wpNetz, evNetz,
                einspeiseVerguetung: Math.round(kpis.totalNetzeinspeisung * config.preisEinspeisung)
            };
        }


        // =========================================================
        // VERMIETER-PERSPEKTIVE: Eigentümer bewohnt 0..N Einheiten selbst und
        // refinanziert die Maßnahme über Wärmelieferung/Contracting (§ 556c BGB,
        // WärmeLV-Kostenneutralität) oder Modernisierungsumlage (§ 559e BGB)
        // plus Mieterstrom (§ 42a/42b EnWG) und entfallenden CO2-Vermieteranteil
        // (CO2KostAufG). Rein additiv – die Bestandsrechnung bleibt unberührt.
        // =========================================================
        function getVermieterFinance(derivedParams, simulation, finance) {
            const config = state.config;
            const N = Math.max(1, Number(config.wohneinheiten || 1));
            const E = clamp(Number(config.selbstbewohnteWE || 0), 0, N);
            const V = N - E;
            if (!config.vermieterModus || V <= 0) return null;

            const kpis = simulation.kpis;
            const anteilV = V / N;
            const anteilE = E / N;
            const wohnflaeche = Number(config.wohnflaeche) > 0 ? Number(config.wohnflaeche) : N * ASSUMPTIONS.regulatory.landlord.defaultLivingAreaPerUnitM2;
            const flaecheVermietet = wohnflaeche * anteilV;
            const r4 = x => Math.round(x * 10000) / 10000;

            // ---- Wärme: Contracting (§ 556c BGB) oder Umlage (§ 559e BGB) ----
            const waermeModell = config.waermeModell ?? 'contracting';
            const waermeGesamt = config.moduleWP ? Number(derivedParams.realeWpWaermeBedarf || 0) : 0;
            const waermeVermietet = waermeGesamt * anteilV;
            const grundpreisJahr = Number(config.waermeGrundpreis ?? ASSUMPTIONS.regulatory.landlord.defaultHeatBasePricePerUnitMonth) * 12 * V;
            const alteMieterHeizkosten = Number(finance.heizkostenOld || 0) * anteilV;
            // Kostenneutralität (§ 556c BGB i.V.m. WärmeLV): Wärmekosten der Mieter dürfen
            // die bisherigen Betriebskosten der Eigenversorgung nicht übersteigen
            const arbeitspreisNeutral = waermeVermietet > 0
                ? Math.max(0, r4((alteMieterHeizkosten - grundpreisJahr) / waermeVermietet)) : 0;
            const arbeitspreis = (config.waermeArbeitspreis !== '' && config.waermeArbeitspreis != null)
                ? Number(config.waermeArbeitspreis) : arbeitspreisNeutral;
            const contractingAktiv = waermeModell === 'contracting' && config.moduleWP && waermeVermietet > 0;
            const waermeErloes = contractingAktiv ? Math.round(waermeVermietet * arbeitspreis + grundpreisJahr) : 0;
            const mieterWaermeKostenNeu = Math.round(waermeVermietet * arbeitspreis + grundpreisJahr);
            const kostenneutral = !contractingAktiv || mieterWaermeKostenNeu <= Math.round(alteMieterHeizkosten) + 1;

            // § 559e BGB: 10 % der um Förderung gekürzten Kosten, pauschal −15 % für
            // ersparte Erhaltungskosten, Kappgrenze 0,50 €/m² monatlich
            const umlageBasis = Math.max(0, Number(finance.nettoWP || 0)) * (1 - ASSUMPTIONS.regulatory.landlord.modernizationMaintenanceDeductionShare);
            const umlageRoh = ASSUMPTIONS.regulatory.landlord.modernizationAnnualShare * umlageBasis * anteilV;
            const umlageKappe = ASSUMPTIONS.regulatory.landlord.modernizationCapEuroPerM2Month * flaecheVermietet * 12;
            const umlageAktiv = waermeModell === 'umlage559e' && config.moduleWP;
            const umlage = umlageAktiv ? Math.round(Math.min(umlageRoh, umlageKappe)) : 0;
            const umlageGekappt = umlageAktiv && umlageRoh > umlageKappe;

            // ---- Mieterstrom (§ 42a klassisch mit Zuschlag / § 42b GGV) ----
            const msModell = config.mieterstromModell ?? '42b';
            const mieterStromBedarf = (config.mieterStromBedarf !== '' && config.mieterStromBedarf != null)
                ? Number(config.mieterStromBedarf) : ASSUMPTIONS.tenantPower.defaultTenantDemandKwhPerUnit * V;
            // § 42a Abs. 4 EnWG: max. 90 % des örtlichen Grundversorgertarifs (Näherung: Arbeitspreis)
            const msPreisDeckel = r4(Number(config.preisStrom || 0) * ASSUMPTIONS.tenantPower.priceCapShareOfBaseTariff);
            const msPreis = (config.mieterstromPreis !== '' && config.mieterstromPreis != null)
                ? Number(config.mieterstromPreis) : msPreisDeckel;
            let mieterPvLieferung = 0;
            if (msModell !== 'aus' && config.modulePV && config.einspeiseArt !== 'voll' && mieterStromBedarf > 0) {
                simulation.chartData.forEach((m, i) => {
                    const ueberschuss = Math.max(0, -m.NetzeinspeisungNeg);
                    const bedarfTag = mieterStromBedarf * HH_DISTRIBUTION[i] * DAYLIGHT_RATIO[i];
                    mieterPvLieferung += Math.min(ueberschuss, bedarfTag);
                });
                mieterPvLieferung = Math.round(mieterPvLieferung);
            }
            const zuschlagSatz = msModell === 'klassisch' ? getMieterstromzuschlag(derivedParams.pvKwp) : 0;
            const mieterstromErloes = Math.round(mieterPvLieferung * (msPreis + zuschlagSatz));
            const resteinspeisung = Math.max(0, Number(kpis.totalNetzeinspeisung || 0) - mieterPvLieferung);
            const einspeiseErloes = Math.round(resteinspeisung * Number(config.preisEinspeisung || 0));

            // ---- CO2-Kostenaufteilung (CO2KostAufG): Vermieteranteil entfällt mit der WP ----
            let fossilFaktor = getEmissionFactorForHeating(config.heizungArt);
            if (config.heizungArt === 'Nachtspeicher' || config.heizungArt === 'Stromdirektheizung') fossilFaktor = 0;
            const co2KgJahr = config.moduleWP ? Number(derivedParams.heizVerbrauchKwh || 0) * fossilFaktor : 0;
            const co2KgProM2 = wohnflaeche > 0 ? co2KgJahr / wohnflaeche : 0;
            const co2VermieterProzent = getCo2VermieterAnteil(co2KgProM2);
            const co2Preis = Number(config.co2PreisTonne ?? ASSUMPTIONS.regulatory.co2CostSplit.pricePerTonDefault);
            const co2Ersparnis = Math.round((co2KgJahr / 1000) * co2Preis * (co2VermieterProzent / 100) * anteilV);

            // ---- Eigene Vorteile (selbstbewohnter Anteil) und Betriebskosten ----
            const eigeneHeizAlt = Math.round(Number(finance.heizkostenOld || 0) * anteilE);
            const eigenerStromVorteil = Math.round(Number(kpis.hhDeckung || 0) * Number(config.preisStrom || 0));
            const evVorteil = config.moduleWB
                ? Math.round(Number(finance.evOldCost || 0) - Number(finance.evNetz || 0) * Number(config.preisStrom || 0))
                : 0;
            const wpStromKosten = config.moduleWP
                ? Math.round(Number(finance.wpNetz || 0) * Number(config.preisStrom || 0))
                : 0;
            const wartungDiff = Math.round(Number(finance.wartungNeu || 0) - Number(config.wartungOld || 0));
            const ersparnis14a = Math.round(Number(finance.ersparnis14a || 0));

            const einnahmen = waermeErloes + umlage + mieterstromErloes + einspeiseErloes
                + eigeneHeizAlt + eigenerStromVorteil + evVorteil + co2Ersparnis + ersparnis14a;
            const ausgaben = wpStromKosten + wartungDiff;
            const cashflowJahr1 = einnahmen - ausgaben;
            const nettoInvest = Number(finance.nettoInvest || 0);
            const rendite = nettoInvest > 0 ? Math.round((cashflowJahr1 / nettoInvest) * 1000) / 10 : 0;
            const amortisationStatisch = cashflowJahr1 > 0 ? Math.ceil(nettoInvest / cashflowJahr1) : null;

            return {
                N, E, V, anteilV, anteilE, wohnflaeche, flaecheVermietet,
                waermeModell, contractingAktiv, waermeVermietet: Math.round(waermeVermietet),
                arbeitspreis, arbeitspreisNeutral, grundpreisJahr: Math.round(grundpreisJahr),
                waermeErloes, mieterWaermeKostenNeu, alteMieterHeizkosten: Math.round(alteMieterHeizkosten), kostenneutral,
                umlage, umlageGekappt, umlageKappe: Math.round(umlageKappe),
                msModell, mieterStromBedarf, msPreis, msPreisDeckel, zuschlagSatz,
                mieterPvLieferung, mieterstromErloes, resteinspeisung: Math.round(resteinspeisung), einspeiseErloes,
                co2KgProM2: Math.round(co2KgProM2 * 10) / 10, co2VermieterProzent, co2Preis, co2Ersparnis,
                eigeneHeizAlt, eigenerStromVorteil, evVorteil, wpStromKosten, wartungDiff, ersparnis14a,
                einnahmen, ausgaben, cashflowJahr1, rendite, amortisationStatisch
            };
        }

        // =========================================================
        // SZENARIEN: Angebotsstufen (Basis/Empfehlung/Zukunft) und
        // Amortisations-Bandbreite – rechnen den kompletten Kern mit
        // temporär abgewandelter Config und stellen sie danach wieder her.
        // =========================================================
        function berechneMitConfig(overrides) {
            const original = state.config;
            state.config = { ...original, ...overrides };
            try {
                return getComputed();
            } finally {
                state.config = original;
            }
        }

        // Drei Angebotsstufen aus der aktuellen Konfiguration:
        //   Basis      – wie Empfehlung, aber ohne Speicher und ohne Wallbox
        //   Empfehlung – exakt die konfigurierte Anlage
        //   Zukunft    – Speicher-Reserve (×1,5, max. 30 kWh) + Wallbox
        function getAngebotsStufen(aktuell) {
            const cfg = state.config;
            if (!cfg.modulePV) return null;

            const kapazitaet = Number(aktuell.derivedParams.batteryCapacity || 0);
            const hatAbstufung = kapazitaet > 0 || cfg.moduleWB;
            if (!hatAbstufung) return null; // Basis wäre identisch mit der Empfehlung

            const s = ASSUMPTIONS.scenarios;
            const zukunftKwh = Math.min(Math.round((kapazitaet || Number(aktuell.derivedParams.empfohleneBatterie || 5)) * s.futureBatteryFactor), s.maxFutureBatteryKwh);
            const speicherKostenZukunft = kapazitaet > 0
                ? Math.round(Number(cfg.costBattery || 0) * (zukunftKwh / kapazitaet))
                : ASSUMPTIONS.finance.defaultInvestment.battery;

            const basis = berechneMitConfig({ customBatteryKwh: '0', costBattery: 0, moduleWB: false });
            const zukunft = berechneMitConfig({
                customBatteryKwh: String(zukunftKwh),
                costBattery: speicherKostenZukunft,
                moduleWB: true,
                costWallbox: Number(cfg.costWallbox || 0) > 0 ? cfg.costWallbox : ASSUMPTIONS.finance.defaultInvestment.wallbox
            });

            return {
                basis: { name: 'Basis', untertitel: 'Der solide Einstieg', computed: basis },
                empfehlung: { name: 'Empfehlung', untertitel: 'Ihre konfigurierte Anlage', computed: aktuell },
                zukunft: { name: 'Zukunft', untertitel: `Speicher-Reserve ${zukunftKwh} kWh${cfg.moduleWB ? '' : ' + Wallbox'}`, computed: zukunft }
            };
        }

        // Amortisations-Bandbreite über die Preissteigerungs-Szenarien:
        // konservativ (geringere Steigerung) … optimistisch (höhere Steigerung)
        function getAmortisationsBandbreite(aktuell) {
            const spread = ASSUMPTIONS.scenarios.inflationSpreadPercentagePoints;
            const rate = Number(state.config.inflationRate ?? 3);
            const rateFossil = Number(state.config.inflationRateFossil ?? 4.5);

            const konservativ = berechneMitConfig({
                inflationRate: Math.max(0, rate - spread),
                inflationRateFossil: Math.max(0, rateFossil - spread)
            }).finance.amortisationYear;
            const optimistisch = berechneMitConfig({
                inflationRate: rate + spread,
                inflationRateFossil: rateFossil + spread
            }).finance.amortisationYear;
            const basis = aktuell.finance.amortisationYear;

            const werte = [konservativ, optimistisch, basis].filter(v => v !== null);
            if (basis === null || werte.length < 3) return { basis, min: null, max: null };
            return { basis, min: Math.min(...werte), max: Math.max(...werte) };
        }

        function getComputed() {
            const derivedParams = getDerivedParams();
            const simulation = getSimulation(derivedParams);
            const finance = getFinance(derivedParams, simulation.kpis);
            const vermieter = getVermieterFinance(derivedParams, simulation, finance);

            return {
                derivedParams,
                chartData: simulation.chartData,
                seasonalData: simulation.seasonalData,
                kpis: simulation.kpis,
                co2: simulation.co2,
                bedarfsMix: simulation.bedarfsMix,
                finance,
                vermieter
            };
        }

        function round2(val) {
            const n = Number(val || 0);
            return Number.isFinite(n) ? Number(n.toFixed(2)) : 0;
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function buildProfitabilityPayload() {
            const computed = getComputed();
            computed.pvgis_snapshot = getPvgisSnapshot();
            const { finance, co2 } = computed;

            const customerSnapshot = {
                customer_id: backendMeta.customerId,
                alternative_id: backendMeta.alternativeId,
                product_id: backendMeta.productId,
                service_id: backendMeta.serviceId,
                customer: backendCustomer || null,
                products: backendProducts || [],
                preset: backendPreset || {}
            };

            return {
                id: backendMeta.calculationId || null,

                customer_id: backendMeta.customerId,
                alternative_id: backendMeta.alternativeId,
                product_id: backendMeta.productId,
                service_id: backendMeta.serviceId,

                title: `Wirtschaftlichkeitsberechnung ${state.config.name || 'Kunde'} - ${new Date().toLocaleDateString('de-DE')}`,
                status: 'draft',

                // ALT
                current_electricity_cost: round2(finance.hhKostenOhne || 0),
                current_heating_cost: round2(finance.heizkostenOld || 0),
                current_fuel_cost: round2(finance.evOldCost || 0),
                current_total_yearly_cost: round2(finance.costOldTotal || 0),
                current_total_25y_cost: round2(finance.cashflow?.[24]?.kostenOhne || 0),

                // NEU
                future_electricity_cost: round2((computed.kpis.totalNetzbezug || 0) * (state.config.preisStrom || 0)),
                future_heating_cost: round2(0),
                future_ev_cost: round2(state.config.moduleWB ? ((computed.derivedParams.evStrombedarf || 0) * (state.config.preisStrom || 0)) : 0),
                future_total_yearly_cost: round2(finance.costNewTotal || 0),
                future_total_25y_cost: round2(finance.cashflow?.[24]?.kostenMit || 0),

                // savings
                savings_per_year: round2(finance.ersparnisJahr1 || 0),
                savings_over_25_years: round2(
                    ((finance.cashflow?.[24]?.kostenOhne || 0) - (finance.cashflow?.[24]?.kostenMit || 0))
                ),

                // investment
                investment_cost: round2(finance.nettoInvest || 0),
                amortisation_years: round2(finance.amortisationYear || 0),
                roi_percent: round2(finance.roi || 0),

                // emissions
                co2_emission_before: round2((Number(co2.year || 0) + ((computed.kpis.totalNetzbezug || 0) * ASSUMPTIONS.emissionFactorsKgPerKwh.gridElectricity / 1000)) * 1000),
                co2_emission_after: round2(((computed.kpis.totalNetzbezug || 0) * ASSUMPTIONS.emissionFactorsKgPerKwh.gridElectricity)),
                co2_saved_trees_equiv: Math.round(co2.trees || 0),

                notes: '',
                electricity_price_note: `EVU Preis ${Number(state.config.preisStrom || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €/kWh`,

                config_snapshot: structuredClone(state.config),
                computed_snapshot: structuredClone(computed),
                assumptions_snapshot: getAssumptionsSnapshot(),
                customer_snapshot: customerSnapshot
            };
        }

        async function saveProfitabilityCalculation() {
            try {
                await ensurePvgisReadyBeforeSave();
                const payload = buildProfitabilityPayload();

                const res = await fetch(backendMeta.saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok || !data.ok) {
                    console.error('Save failed:', data);
                    alert(data.message || 'Speichern fehlgeschlagen.');
                    return false;
                }

                backendMeta.calculationId = data.id;
                loescheEntwurf();
                alert('Wirtschaftlichkeitsberechnung erfolgreich gespeichert.');
                return true;
            } catch (error) {
                console.error(error);
                alert('Beim Speichern ist ein Fehler aufgetreten.');
                return false;
            }
        }

        // =========================================================
        // STATE MUTATIONS
        // =========================================================
        function handleConfigChange(key, value, options = {}) {
            const { render = true, delay = 0 } = options;

            const prevConfig = structuredClone(state.config);
            const nextConfig = {
                ...state.config,
                [key]: value
            };

            if (key === 'gebaeudeArt' && value === 'Einfamilienhaus') {
                nextConfig.wohneinheiten = 1;
                nextConfig.selbstbewohnteWE = 1;
                nextConfig.weUnter40k = 0;
            }

            if (key === 'wohneinheiten') {
                const totalUnits = Math.max(1, Number(value) || 1);

                nextConfig.wohneinheiten = totalUnits;

                if (Number(nextConfig.selbstbewohnteWE || 0) > totalUnits) {
                    nextConfig.selbstbewohnteWE = totalUnits;
                }

                if (Number(nextConfig.weUnter40k || 0) > Number(nextConfig.selbstbewohnteWE || 0)) {
                    nextConfig.weUnter40k = Number(nextConfig.selbstbewohnteWE || 0);
                }
            }

            if (key === 'selbstbewohnteWE') {
                const ownUnits = Math.max(0, Number(value) || 0);
                const cappedOwnUnits = Math.min(Number(nextConfig.wohneinheiten || 0), ownUnits);

                nextConfig.selbstbewohnteWE = cappedOwnUnits;

                if (Number(nextConfig.weUnter40k || 0) > cappedOwnUnits) {
                    nextConfig.weUnter40k = cappedOwnUnits;
                }
            }

            if (key === 'weUnter40k') {
                const lowIncomeUnits = Math.max(0, Number(value) || 0);
                nextConfig.weUnter40k = Math.min(Number(nextConfig.selbstbewohnteWE || 0), lowIncomeUnits);
            }

            if (key === 'heizungArt') {
                switch (value) {
                    case 'Gas':
                        nextConfig.preisHeizMedium = ASSUMPTIONS.finance.heatingMediumPrices.gas;
                        break;

                    case 'Öl':
                        nextConfig.preisHeizMedium = ASSUMPTIONS.finance.heatingMediumPrices.oil;
                        break;

                    case 'Holz / Pellets':
                        nextConfig.preisHeizMedium = ASSUMPTIONS.finance.heatingMediumPrices.pelletsPerTon;
                        nextConfig.heizVerbrauch = ASSUMPTIONS.finance.heatingMediumPrices.pelletConsumptionDefaultTons;
                        break;

                    case 'Stromdirektheizung':
                    case 'Nachtspeicher':
                        nextConfig.preisHeizMedium = Number(nextConfig.preisStrom || 0);
                        break;
                }

                if (value !== 'Holz / Pellets' && prevConfig.heizungArt === 'Holz / Pellets') {
                    nextConfig.heizVerbrauch = ASSUMPTIONS.finance.heatingMediumPrices.fossilConsumptionDefaultKwh;
                }
            }

            if (key === 'preisStrom') {
                const strompreis = Number(value || 0);

                if (
                    nextConfig.heizungArt === 'Stromdirektheizung' ||
                    nextConfig.heizungArt === 'Nachtspeicher'
                ) {
                    nextConfig.preisHeizMedium = strompreis;
                }
            }

            if (key === 'kaminVorhanden' && !value) {
                nextConfig.kaminWeiterBetreiben = false;
            }

            if (key === 'solarthermieVorhanden' && !value) {
                nextConfig.solarthermieWeiterBetreiben = false;
            }

            state.config = nextConfig;
            updateThemeCSS();
            speichereEntwurf();

            if (!render) return;

            if (typeof queueRender === 'function') {
                queueRender(delay);
            } else {
                renderApp();
            }
        }

        function toggleSidebarSection(section) {
            state.sidebarSections[section] = !state.sidebarSections[section];
            renderApp();
        }

        function addDachseite() {
            if (state.config.dachseiten.length >= 4) return;

            state.config.dachseiten.push({
                id: Date.now(),
                roof_db_id: null,
                designation: `Dachfläche ${state.config.dachseiten.length + 1}`,
                ausrichtung: 'Ost',
                neigung: 35,
                eindeckung: 'Ziegel',
                eindeckungTyp: '',
                customKwp: '',
                roofArea: '',
                roofType: '',
                roofForm: 'Satteldach',
                roofOrientationRaw: '',
                roofAzimuth: null,
                moduleCount: null,
                modulePower: null,
                moutingType: '',
                roofHeight: null,
                notes: ''
            });

            speichereEntwurf();

            renderApp();
        }

        function updateDachseite(id, field, value) {
            state.config.dachseiten = state.config.dachseiten.map(d => {
                if (d.id !== id) return d;
                const neu = { ...d, [field]: value };
                // Flachdach: Module werden aufgeständert – typische Neigung 10° vorschlagen
                if (field === 'roofForm' && value === 'Flachdach' && Number(d.neigung) > 15) neu.neigung = 10;
                return neu;
            });
            speichereEntwurf();
            renderApp();
        }

        function removeDachseite(id) {
            state.config.dachseiten = state.config.dachseiten.filter(d => d.id !== id);
            speichereEntwurf();
            renderApp();
        }

        // Dynamische Schrittliste: Schritte abgewählter Systeme erscheinen gar nicht erst
        function getWizardSteps() {
            const c = state.config;
            const steps = [
                { key: 'projekt', label: 'Projekt & Systeme' },
                { key: 'gebaeude', label: 'Gebäude & Haushalt' }
            ];
            if (c.modulePV) steps.push({ key: 'dach', label: 'Dach (PV)' });
            if (c.moduleWP) steps.push({ key: 'heizung', label: 'Heizung & WW' });
            if (c.moduleWB) steps.push({ key: 'mobilitaet', label: 'Mobilität' });
            steps.push({ key: 'invest', label: 'Preise & Investition' });
            return steps;
        }

        // PLZ-Feedback unter dem Eingabefeld – Ortsname & Klima kommen aus dem
        // Standort-Modul (Zippopotam/PVGIS), bis dahin interne Tabellenwerte
        function plzFeedbackHtml(config, theme) {
            const plz = String(config.plz || '');
            if (!/^\d{5}$/.test(plz)) {
                return `<p class="text-xs text-slate-400 min-h-4">Für regionale Klima- &amp; Ertragsdaten</p>`;
            }
            const ort = (standort.plz === plz && standort.ort) ? standort.ort : null;
            const live = getLiveKlima();
            if (live) {
                return `<p class="text-xs font-semibold min-h-4" style="color:${theme.primary}">
          ✓ ${ort ? `${ort} · ` : ''}${live.heiztage} Heiztage · ${formatDE(live.gtz)} Gradtage · NAT ${formatDE(getKlimaDaten(plz).nat)} °C
          <span class="text-slate-400 font-normal">· Quelle: PVGIS</span>
        </p>`;
            }
            const rf = getRegionalFactors(plz);
            const klima = getKlimaDaten(plz);
            const laedt = standort.plz === plz && standort.klimaStatus === 'laden';
            return `<p class="text-xs font-semibold min-h-4" style="color:${theme.primary}">
        ✓ ${ort ? `${ort} · ` : ''}PV-Basis ${formatDE(rf.pvBaseFactor)} kWh/kWp · ${formatDE(klima.hgt)} Heizgradtage
        <span class="text-slate-400 font-normal">${laedt ? '· PVGIS lädt…' : '· Schätzwerte'}</span>
      </p>`;
        }

        function setWizardStep(step) {
            state.wizardStep = step;
            speichereEntwurf();
            renderApp();
        }

        function gotoWizardStep(offset) {
            const steps = getWizardSteps();
            const idx = Math.max(0, steps.findIndex(s => s.key === state.wizardStep));
            state.wizardStep = steps[clamp(idx + offset, 0, steps.length - 1)].key;
            speichereEntwurf();
            renderApp();
        }

        function toggleWizardPanel(name) {
            state.wizardUI[name] = !state.wizardUI[name];
            renderApp();
        }

        function setView(view) {
            state.view = view;
            renderApp();
        }

        function setSidebarOpen(value) {
            state.isSidebarOpen = value;
            renderApp();
        }

        // =========================================================
        // REUSABLE HTML
        // =========================================================
        function ReportHeader(text) {
            const theme = getActiveTheme();
            return `
        <div class="text-[13px] font-bold text-slate-400 tracking-widest mb-6 border-b border-[${theme.primary}] pb-2 flex justify-between items-center" style="border-bottom: 2px solid ${theme.primary};">
          <span class="text-slate-500 uppercase">${text}</span>
          <span><img src="${theme.logo}" style="width: 190px;"></span>
        </div>
      `;
        }

        function ReportFooter() {
            const theme = getActiveTheme();
            return `
        <div class="absolute bottom-[10mm] left-[20mm] right-[20mm] pt-2 pb-1 flex justify-between items-center text-[13px] text-slate-500 bg-white z-50"
             style="border-top: 3px solid ${theme.primary};">
          <span class="uppercase tracking-widest font-bold"></span>
          <span class="font-bold">Seite <span class="page-number"></span></span>
        </div>
      `;
        }

        function sidebarInput({ label, value, type = "text", step = "", rightLabel = "", placeholder = "", disabled = false, onchange = "" }) {
            const theme = getActiveTheme();
            return `
        <div class="flex flex-col gap-1.5 mb-3 ${disabled ? 'opacity-50 pointer-events-none' : ''}">
          <div class="flex justify-between items-end">
            <label class="text-xs font-bold text-slate-700">${label}</label>
            ${rightLabel ? `<span class="text-[13px] font-bold px-1.5 py-0.5 rounded" style="color:${theme.primary};background:${theme.bgLight}">${rightLabel}</span>` : ''}
          </div>
          <input
            type="${type}"
            ${step !== "" ? `step="${step}"` : ""}
            value="${value ?? ''}"
            placeholder="${placeholder}"
            ${disabled ? 'disabled' : ''}
            onchange="${onchange}"
            class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 transition-shadow placeholder:text-slate-400 focus-ring"
          />
        </div>
      `;
        }

        // =========================================================
        // RENDER: WIZARD
        // =========================================================
        function renderWizard() {
            const config = state.config;
            const { derivedParams, kpis, finance, vermieter } = getComputed();
            const theme = getActiveTheme();
            const warnings = getPlausibilityWarnings(config, derivedParams);

            const steps = getWizardSteps();
            let curIdx = steps.findIndex(s => s.key === state.wizardStep);
            if (curIdx === -1) { curIdx = 0; state.wizardStep = steps[0].key; }
            const cur = steps[curIdx].key;
            const isLast = curIdx === steps.length - 1;
            const stepLabelOf = key => (getWizardSteps().find(s => s.key === key) || { label: key }).label;

            // Felder mit Plausibilitäts-Warnung bekommen direkt am Eingabefeld einen gelben
            // Rahmen samt Hinweistext – im Gespräch scrollt niemand zur Warnbox unten
            const warnFields = new Set(warnings.map(x => x.field).filter(Boolean));
            const warnBorder = f => warnFields.has(f) ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200';
            const warnHint = f => warnFields.has(f)
                ? `<p class="text-[11px] text-amber-700 font-semibold">${(warnings.find(x => x.field === f) || {}).text || ''}</p>`
                : '';

            const keinSystem = !config.modulePV && !config.moduleWP && !config.moduleWB;
            const hhVorschlag = clamp(Math.round((1300 + 750 * Number(config.personen || 0)) / 100) * 100, 1500, 9000);
            const eegMisch = getEegMischverguetung(derivedParams.pvKwp, config.einspeiseArt);

            const systemCards = [
                { key: 'modulePV', title: 'Photovoltaik & Speicher', desc: 'Eigenen Strom erzeugen & speichern', ic: Icons.sun() },
                { key: 'moduleWP', title: 'Wärmepumpe', desc: 'Unabhängig von Öl & Gas heizen', ic: Icons.thermoSnow() },
                { key: 'moduleWB', title: 'Wallbox (E-Mobilität)', desc: 'E-Auto günstig zu Hause laden', ic: Icons.car() }
            ];

            return `
        <div class="min-h-screen bg-white flex items-center justify-center p-4 font-sans text-dark-600">
          <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-4xl overflow-hidden">
            <div class="bg-[${theme.primary}] text-white p-8">
              <div class="flex justify-between items-start gap-4">
                <div>
                  <div class="font-bold text-sm tracking-widest mb-1" style="color:${theme.secondary}">${theme.name}</div>
                  <h1 class="text-3xl font-bold mb-2">IHR WEG ZUR EIGENEN ENERGIEAUTARKIE</h1>
                  <p class="text-slate-300 text-sm">Konfigurieren Sie Ihr System für den finalen Beratungsbericht.</p>
                </div>
                <label class="shrink-0 flex flex-col items-end gap-1" title="Interne Einstellung: bestimmt Logo & Farbschema des Reports">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-white/60">Design</span>
                  <select onchange="handleConfigChange('company', this.value)"
                    class="text-xs font-semibold bg-white/10 text-white border border-white/30 rounded-lg px-2 py-1.5 outline-none cursor-pointer hover:bg-white/20 transition-colors">
                    <option value="Werkstudio" class="text-slate-800" ${config.company === 'Werkstudio' ? 'selected' : ''}>Werkstudio</option>
                    <option value="Solar Aspekt" class="text-slate-800" ${config.company === 'Solar Aspekt' ? 'selected' : ''}>Solar Aspekt</option>
                  </select>
                </label>
              </div>

              <div class="flex items-center mt-8 gap-2">
                ${steps.map((s, i) => `
                  <button onclick="setWizardStep('${s.key}')" class="flex-1 flex flex-col items-center cursor-pointer group" title="${s.label}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm mb-1.5 transition-colors group-hover:ring-2 group-hover:ring-white/40 ${i <= curIdx ? 'text-white' : 'bg-slate-800 text-slate-500'}"
                      style="${i <= curIdx ? `background-color:${theme.primary}` : ''}">
                      ${i < curIdx ? icon('checkCircle2', 'w-4 h-4') : i + 1}
                    </div>
                    <div class="text-[10px] font-semibold mb-1.5 text-center ${i <= curIdx ? 'text-white' : 'text-slate-500'}">${s.label}</div>
                    <div class="h-1 w-full rounded-full ${i <= curIdx ? '' : 'bg-slate-800'}"
                      style="${i <= curIdx ? `background-color:${theme.primary}` : ''}"></div>
                  </button>
                `).join('')}
              </div>
            </div>

            <div class="p-8">
              ${cur === 'projekt' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.home()}</span>
                    Projekt & Systemauswahl
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Name des Kunden</label>
                      <input type="text" value="${config.name}"
                        onchange="handleConfigChange('name', this.value)"
                        class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">PLZ (Standort)</label>
                      <input type="text" maxlength="5" value="${config.plz}"
                        onchange="handleConfigChange('plz', this.value.replace(/\\D/g, ''))"
                        class="field w-full p-3 bg-white border ${warnBorder('plz')} rounded-xl outline-none focus-ring" />
                      ${plzFeedbackHtml(config, theme)}
                    </div>
                  </div>

                  <div class="space-y-3">
                    <h4 class="text-sm font-bold">Welche Systeme sollen berechnet werden?</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      ${systemCards.map(m => `
                        <button onclick="handleConfigChange('${m.key}', ${config[m.key] ? 'false' : 'true'})"
                          class="p-5 border-2 rounded-2xl text-left transition-all ${config[m.key] ? 'shadow-md' : 'border-slate-200 bg-white opacity-70 hover:opacity-100'}"
                          style="${config[m.key] ? `border-color:${theme.primary};background:${theme.bgLight}` : ''}">
                          <div class="flex items-center justify-between mb-3">
                            <span class="w-6 h-6" style="color:${theme.primary}">${m.ic}</span>
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center ${config[m.key] ? 'text-white' : 'border-slate-300'}"
                              style="${config[m.key] ? `background:${theme.primary};border-color:${theme.primary}` : ''}">
                              ${config[m.key] ? icon('checkCircle2', 'w-3.5 h-3.5') : ''}
                            </span>
                          </div>
                          <div class="font-bold text-sm">${m.title}</div>
                          <div class="text-xs text-slate-500 mt-1">${m.desc}</div>
                        </button>
                      `).join('')}
                    </div>
                    ${keinSystem ? `
                      <p class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
                        Bitte wählen Sie mindestens ein System aus, um die Wirtschaftlichkeit zu berechnen.
                      </p>
                    ` : `
                      <p class="text-xs text-slate-400">Die folgenden Schritte passen sich automatisch an Ihre Auswahl an.</p>
                    `}
                  </div>
                </div>
              ` : ''}

              ${cur === 'gebaeude' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.users()}</span>
                    Gebäude & Haushalt
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold text-slate-700">Gebäudeart</label>
                      <select onchange="handleConfigChange('gebaeudeArt', this.value)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                        <option ${config.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                      </select>
                    </div>

                    ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                      <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nutzungsart des Hauses</label>
                        <select onchange="handleConfigChange('selbstbewohnteWE', this.value === 'Selbstbewohnt' ? 1 : 0)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                          <option ${(config.selbstbewohnteWE === 1) ? 'selected' : ''}>Selbstbewohnt</option>
                          <option ${(config.selbstbewohnteWE !== 1) ? 'selected' : ''}>Vermietet</option>
                        </select>
                      </div>
                    ` : `
                        <div class="space-y-2">
                          <label class="text-sm font-semibold text-slate-700">Wohneinheiten gesamt</label>
                          <input type="number" min="2" value="${config.wohneinheiten}"
                            onchange="handleConfigChange('wohneinheiten', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>
                        <div class="space-y-2">
                          <label class="text-sm font-semibold text-slate-700">Davon selbst bewohnt (Eigentümer)</label>
                          <input type="number" min="0" max="${config.wohneinheiten}" value="${config.selbstbewohnteWE}"
                            onchange="handleConfigChange('selbstbewohnteWE', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <p class="text-xs text-slate-500">${config.wohneinheiten - config.selbstbewohnteWE} WE gelten als vermietet.</p>
                        </div>
                    `}
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Personen im Haushalt</label>
                      <input type="number" min="1" value="${config.personen}"
                        onchange="handleConfigChange('personen', Number(this.value))"
                        class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex justify-between items-end h-6">
                        <span>Haushaltsstrom (kWh/a)</span>
                        ${Number(config.hhStrom) !== hhVorschlag ? `
                          <button onclick="handleConfigChange('hhStrom', ${hhVorschlag})"
                            title="Typischer Verbrauch für ${config.personen} Personen übernehmen"
                            class="text-[11px] font-bold px-2 py-0.5 rounded-md border transition-colors hover:opacity-80"
                            style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                            Vorschlag: ${formatDE(hhVorschlag)}
                          </button>
                        ` : ''}
                      </label>
                      <div class="relative">
                        <input type="text" inputmode="numeric" value="${formatDE(config.hhStrom)}"
                          onchange="handleConfigChange('hhStrom', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                          class="field w-full p-3 pr-16 bg-white border ${warnBorder('hhStrom')} rounded-xl outline-none focus-ring" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">kWh/a</span>
                      </div>
                      ${warnHint('hhStrom')}
                    </div>
                  </div>
                </div>
              ` : ''}

              ${cur === 'dach' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.sun()}</span>
                    Geplante Dachflächen
                  </h2>

                  <div class="p-4 border rounded-xl bg-white border-slate-200">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="text-sm font-bold flex items-center gap-2">
                        <span class="w-4 h-4" style="color:${theme.primary}">${Icons.sun()}</span>
                        Dachflächen für die PV-Belegung
                      </h4>
                      <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''}
                        class="text-xs font-bold px-3 py-1.5 rounded-lg text-white bg-slate-800 hover:bg-slate-700 disabled:opacity-50">
                        + Weitere Fläche
                      </button>
                    </div>

                    <div class="space-y-3">
                      ${config.dachseiten.map(dach => `
                        <div class="flex gap-3 items-end p-3 bg-white border border-slate-100 rounded-lg  flex-wrap">
                          <div class="w-full flex gap-3 items-end">
                            <div class="flex-1 space-y-1">
                              <label class="text-xs text-slate-500 font-semibold">Bezeichnung</label>
                              <input type="text" value="${dach.designation || ''}" placeholder="z.B. Hauptdach Süd, Garage"
                                onchange="updateDachseite(${dach.id}, 'designation', this.value)"
                                class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring" />
                            </div>
                            <div class="w-[38%] space-y-1">
                              <label class="text-xs text-slate-500 font-semibold">Dachform</label>
                              <select onchange="updateDachseite(${dach.id}, 'roofForm', this.value)"
                                class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring">
                                ${['Satteldach', 'Walmdach', 'Pultdach', 'Flachdach', 'Zeltdach', 'Krüppelwalmdach', 'Sonstige'].map(opt => `
                                  <option ${((dach.roofForm || 'Satteldach') === opt) ? 'selected' : ''}>${opt}</option>
                                `).join('')}
                              </select>
                            </div>
                          </div>

                          <div class="w-[28%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Ausrichtung</label>
                            <select
                              onchange="updateDachseite(${dach.id}, 'ausrichtung', this.value)"
                              class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring">
                              ${['Süd', 'Süd-Ost', 'Süd-West', 'Ost', 'West', 'Nord-Ost', 'Nord-West', 'Nord'].map(opt => `
                                <option ${dach.ausrichtung === opt ? 'selected' : ''}>${opt}</option>
                              `).join('')}
                            </select>
                          </div>

                          <div class="w-[18%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Neigung (°)</label>
                            <input type="number" value="${dach.neigung}"
                              onchange="updateDachseite(${dach.id}, 'neigung', Number(this.value))"
                              class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring" />
                          </div>

                          <div class="w-[28%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Eindeckung</label>
                            <select
                              onchange="updateDachseite(${dach.id}, 'eindeckung', this.value)"
                              class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring">
                              ${['Ziegel', 'Blech', 'Trapezblech', 'Flachdach/Folie', 'Schiefer'].map(opt => `
                                <option ${((dach.eindeckung || 'Ziegel') === opt) ? 'selected' : ''}>${opt}</option>
                              `).join('')}
                            </select>
                          </div>

                          <div class="w-[18%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">kWp (opt)</label>
                            <input type="number" step="0.1" value="${dach.customKwp || ''}" placeholder="Auto"
                              onchange="updateDachseite(${dach.id}, 'customKwp', this.value)"
                              class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring" />
                          </div>

                          <div class="w-full flex gap-3 items-end mt-1">
                            <div class="flex-1 space-y-1">
                              <label class="text-xs text-slate-500 font-semibold">Material / Typ (z.B. Beton, Frankfurter Pfanne)</label>
                              <input type="text" value="${dach.eindeckungTyp || ''}"
                                onchange="updateDachseite(${dach.id}, 'eindeckungTyp', this.value)"
                                placeholder="z.B. Beton (Frankfurter Pfanne)"
                                class="field-sm w-full p-2 border rounded-lg text-sm outline-none focus-ring" />
                            </div>
                            ${config.dachseiten.length > 1 ? `
                              <button onclick="removeDachseite(${dach.id})"
                                class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-md hover:bg-red-100 transition-colors mb-0.5 shrink-0">
                                <span class="w-4 h-4">${Icons.x()}</span>
                              </button>
                            ` : ''}
                          </div>

                          ${(() => {
                    const ertrag = derivedParams.dachErtraege.find(e => e.id === dach.id) || {};
                    const laden = standort.plz === String(config.plz || '') && standort.lat && standort.pv[dachPvKey(dach)] && standort.pv[dachPvKey(dach)].status === 'laden';
                    return `
                              <div class="w-full flex justify-between items-center mt-1">
                                <span class="text-[11px] text-slate-400">
                                  ${ertrag.quelle === 'PVGIS'
                            ? 'Ertrag empirisch für diesen Standort, Neigung & Ausrichtung (PVGIS)'
                            : (laden ? 'PVGIS-Daten werden geladen…' : 'Ertrag geschätzt (Regionalfaktor × Ausrichtung)')}
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-md border shrink-0"
                                  style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                                  ≈ ${formatDE(ertrag.ey || 0)} kWh/kWp
                                </span>
                              </div>
                            `;
                })()}
                        </div>
                      `).join('')}
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex justify-between items-end h-6">
                        <span>Systemverluste inkl. Verschattung (%)</span>
                        ${Number(config.pvSystemVerlust ?? 14) !== 14 ? `
                          <button onclick="handleConfigChange('pvSystemVerlust', 14)"
                            class="text-[11px] font-bold px-2 py-0.5 rounded-md border hover:opacity-80"
                            style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                            Standard: 14 %
                          </button>
                        ` : ''}
                      </label>
                      <input type="number" step="1" min="0" max="40" value="${config.pvSystemVerlust ?? 14}"
                        onchange="handleConfigChange('pvSystemVerlust', Number(this.value))"
                        class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                      <p class="text-xs text-slate-400">Kabel, Wechselrichter, Verschmutzung, Verschattung – gilt für alle Dachflächen. Bei stärkerer Verschattung erhöhen (z.B. 20–25 %).</p>
                    </div>
                  </div>
                </div>
              ` : ''}

              ${cur === 'heizung' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                    Heizung & Warmwasser (Bestand)
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelle Heizung</label>
                      <select onchange="handleConfigChange('heizungArt', this.value)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Gas', 'Öl', 'Holz / Pellets', 'Nachtspeicher'].map(opt => `
                          <option ${config.heizungArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Alter (Jahre)</label>
                      <input type="number" value="${config.heizungAlter}"
                        onchange="handleConfigChange('heizungAlter', Number(this.value))"
                        class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="text-sm font-semibold flex justify-between">Verbrauch <span class="text-slate-400 font-normal">in ${getHeizEinheit(config.heizungArt)}</span></label>
                      <input type="text" inputmode="numeric" value="${formatDE(config.heizVerbrauch)}"
                        onchange="handleConfigChange('heizVerbrauch', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                        class="field w-full p-3 bg-white border ${warnBorder('heizVerbrauch')} rounded-xl outline-none focus-ring" />
                      ${warnHint('heizVerbrauch')}
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Heizsystem (Übergabe)</label>
                      <select onchange="handleConfigChange('heizSystem', this.value)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Heizkörper', 'Fußbodenheizung', 'Beides'].map(opt => `
                          <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Zentral', 'Dezentral'].map(opt => `
                          <option ${config.warmwasserArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2 flex flex-col justify-center mt-6">
                      <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''}
                          onchange="handleConfigChange('zirkulation', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-dark-600">Zirkulation vorhanden</span>
                      </label>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-4 border rounded-xl bg-white border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('kaminVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-dark-600">Holz-Kamin vorhanden</span>
                      </label>

                      ${config.kaminVorhanden ? `
                        <div class="space-y-3">
                          <div class="flex gap-3">
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-dark-600">Bedarf (Raummeter)</label>
                              <input type="number" value="${config.holzVerbrauch}"
                                onchange="handleConfigChange('holzVerbrauch', Number(this.value))"
                                class="field-sm w-full p-2 border rounded-lg" />
                            </div>
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-dark-600">Preis (€/RM)</label>
                              <input type="number" value="${config.preisHolz}"
                                onchange="handleConfigChange('preisHolz', Number(this.value))"
                                class="field-sm w-full p-2 border rounded-lg" />
                            </div>
                          </div>
                          <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                            <input type="checkbox" ${config.kaminWeiterBetreiben ? 'checked' : ''}
                              onchange="handleConfigChange('kaminWeiterBetreiben', this.checked)"
                              class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                            <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>

                    <div class="p-4 border rounded-xl bg-white border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('solarthermieVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-dark-600">Solarthermie vorhanden</span>
                      </label>

                      ${config.solarthermieVorhanden ? `
                        <div class="space-y-3">
                          <div class="flex gap-3">
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-dark-600">Kollektor-Art</label>
                              <select onchange="handleConfigChange('solarthermieArt', this.value)" class="field-sm w-full p-2 border rounded-lg bg-white">
                                <option ${config.solarthermieArt === 'Flachkollektor' ? 'selected' : ''}>Flachkollektor</option>
                                <option ${config.solarthermieArt === 'Röhrenkollektor' ? 'selected' : ''}>Röhrenkollektor</option>
                              </select>
                            </div>
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-dark-600">Anzahl Kollektoren</label>
                              <input type="number" value="${config.solarKollektoren}"
                                onchange="handleConfigChange('solarKollektoren', Number(this.value))"
                                class="field-sm w-full p-2 border rounded-lg" />
                            </div>
                          </div>
                          <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                            <input type="checkbox" ${config.solarthermieWeiterBetreiben ? 'checked' : ''}
                              onchange="handleConfigChange('solarthermieWeiterBetreiben', this.checked)"
                              class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                            <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>
                  </div>

                  <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button onclick="toggleWizardPanel('installDetails')"
                      class="w-full flex justify-between items-center p-4 bg-slate-50 hover:bg-slate-100 transition-colors text-left">
                      <span class="text-sm font-bold text-slate-600 flex items-center gap-2">
                        <span class="w-4 h-4">${Icons.wrench()}</span>
                        Installations-Details (optional)
                        <span class="text-xs font-normal text-slate-400 hidden md:inline">– Aufmaß-Daten, ohne Einfluss auf die Berechnung</span>
                      </span>
                      <span class="w-4 h-4 text-slate-400 transition-transform ${state.wizardUI.installDetails ? 'rotate-180' : ''}">${Icons.chevronDown()}</span>
                    </button>

                    ${state.wizardUI.installDetails ? `
                      <div class="p-4 space-y-4 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div class="space-y-2">
                            <label class="text-sm font-semibold">Rohrsystem Heizung</label>
                            <div class="flex gap-2">
                              <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="field w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                                ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                                  <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                                `).join('')}
                              </select>
                              <div class="w-1/3 relative">
                                <input type="text" value="${config.rohrHeizungDN}" placeholder="DN"
                                  onchange="handleConfigChange('rohrHeizungDN', this.value)"
                                  class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                                <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                              </div>
                            </div>
                          </div>

                          <div class="space-y-2">
                            <label class="text-sm font-semibold">Rohrsystem Warmwasser</label>
                            <div class="flex gap-2">
                              <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="field w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                                ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                                  <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                                `).join('')}
                              </select>
                              <div class="w-1/3 relative">
                                <input type="text" value="${config.rohrWWDN}" placeholder="DN"
                                  onchange="handleConfigChange('rohrWWDN', this.value)"
                                  class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                                <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        ${config.zirkulation ? `
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                              <label class="text-sm font-semibold">Rohrsystem Zirkulation</label>
                              <div class="flex gap-2">
                                <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="field w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                                  ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                                    <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                                  `).join('')}
                                </select>
                                <div class="w-1/3 relative">
                                  <input type="text" value="${config.rohrZirkulationDN}" placeholder="DN"
                                    onchange="handleConfigChange('rohrZirkulationDN', this.value)"
                                    class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                                  <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        ` : ''}
                      </div>
                    ` : ''}
                  </div>
                </div>
              ` : ''}

              ${cur === 'mobilitaet' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.car()}</span>
                    Elektromobilität
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelles Fahrzeug</label>
                      <select onchange="handleConfigChange('autoArt', this.value)" class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.autoArt === 'Verbrenner' ? 'selected' : ''}>Verbrenner</option>
                        <option ${config.autoArt === 'E-Auto' ? 'selected' : ''}>E-Auto</option>
                      </select>
                    </div>
                  </div>

                  <div class="p-5 border rounded-xl bg-white border-slate-200">
                    <h4 class="font-bold text-sm mb-4">Fahrzeugnutzung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="space-y-2">
                        <label class="text-xs font-semibold">Fahrleistung (km/a)</label>
                        <input type="text" inputmode="numeric" value="${formatDE(config.fahrleistung)}"
                          onchange="handleConfigChange('fahrleistung', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                          class="field-sm w-full p-2 border rounded-lg" />
                      </div>

                      ${config.autoArt === 'Verbrenner' ? `
                        <div class="space-y-2">
                          <label class="text-xs font-semibold">Verbrauch (l/100km)</label>
                          <input type="number" step="0.5" value="${config.verbrennerVerbrauch}"
                            onchange="handleConfigChange('verbrennerVerbrauch', Number(this.value))"
                            class="field-sm w-full p-2 border rounded-lg" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-xs font-semibold">Spritpreis (€/l)</label>
                          <input type="number" step="0.05" value="${config.preisSprit}"
                            onchange="handleConfigChange('preisSprit', Number(this.value))"
                            class="field-sm w-full p-2 border rounded-lg" />
                        </div>
                      ` : ''}
                    </div>

                    ${config.autoArt === 'Verbrenner' ? `
                      <p class="text-xs text-slate-500 mt-4 italic">
                        Für die spätere Anlagen-Dimensionierung kalkulieren wir direkt den Strombedarf für ein zukünftiges E-Auto mit ein, um Sie zukunftssicher aufzustellen.
                      </p>
                    ` : ''}
                  </div>
                </div>
              ` : ''}

              ${cur === 'invest' ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.euro()}</span>
                    Preise, Förderung & Investition
                  </h2>

                  <div class="space-y-3">
                    <h4 class="text-sm font-bold text-slate-600 uppercase tracking-wide">Energiepreise</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div class="space-y-2">
                        <label class="text-sm font-semibold">Strompreis (€/kWh)</label>
                        <input type="number" step="0.01" value="${config.preisStrom}"
                          onchange="handleConfigChange('preisStrom', Number(this.value))"
                          class="field w-full p-3 bg-white border ${warnBorder('preisStrom')} rounded-xl outline-none focus-ring" />
                        ${warnHint('preisStrom')}
                      </div>

                      ${config.moduleWP ? `
                      <div class="space-y-2">
                        <label class="text-sm font-semibold">Preis ${config.heizungArt} (€/${getHeizEinheit(config.heizungArt)})</label>
                        <input type="number" step="0.01" value="${config.preisHeizMedium}"
                          onchange="handleConfigChange('preisHeizMedium', Number(this.value))"
                          class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                      </div>
                      ` : ''}

                      ${config.modulePV ? `
                      <div class="space-y-2">
                        <label class="text-sm font-semibold h-6 flex items-end">Einspeise-Modell</label>
                        <select onchange="handleConfigChange('einspeiseArt', this.value)"
                          class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                          <option value="ueberschuss" ${(config.einspeiseArt ?? 'ueberschuss') === 'ueberschuss' ? 'selected' : ''}>Überschusseinspeisung (Eigenverbrauch, Standard)</option>
                          <option value="voll" ${config.einspeiseArt === 'voll' ? 'selected' : ''}>Volleinspeisung (gesamte Produktion, höherer Satz)</option>
                        </select>
                      </div>

                      <div class="space-y-2">
                        <label class="text-sm font-semibold flex justify-between items-end h-6">
                          <span>Einspeisevergütung (€/kWh)</span>
                          ${Math.abs(Number(config.preisEinspeisung) - eegMisch) > 0.0005 ? `
                            <button onclick="handleConfigChange('preisEinspeisung', ${eegMisch})"
                              title="EEG-Mischvergütung (${config.einspeiseArt === 'voll' ? 'Volleinspeisung' : 'Teileinspeisung'}) für ${formatDE(derivedParams.pvKwp, 1)} kWp übernehmen"
                              class="text-[11px] font-bold px-2 py-0.5 rounded-md border transition-colors hover:opacity-80"
                              style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                              EEG ${formatDE(derivedParams.pvKwp, 1)} kWp: ${formatDE(eegMisch * 100, 2)} ct
                            </button>
                          ` : ''}
                        </label>
                        <input type="number" step="0.001" value="${config.preisEinspeisung}"
                          onchange="handleConfigChange('preisEinspeisung', Number(this.value))"
                          class="field w-full p-3 bg-white border ${warnBorder('preisEinspeisung')} rounded-xl outline-none focus-ring" />
                        ${warnHint('preisEinspeisung')}
                      </div>
                      ` : ''}
                    </div>
                  </div>

                  <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button onclick="toggleWizardPanel('advancedPrices')"
                      class="w-full flex justify-between items-center p-4 bg-slate-50 hover:bg-slate-100 transition-colors text-left">
                      <span class="text-sm font-bold text-slate-600 flex items-center gap-2">
                        <span class="w-4 h-4">${Icons.sliders()}</span>
                        Erweiterte Annahmen
                        <span class="text-xs font-normal text-slate-400 hidden md:inline">– Steigerungsraten, Wartung, Netzentgelt</span>
                      </span>
                      <span class="w-4 h-4 text-slate-400 transition-transform ${state.wizardUI.advancedPrices ? 'rotate-180' : ''}">${Icons.chevronDown()}</span>
                    </button>

                    ${state.wizardUI.advancedPrices ? `
                      <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 animate-fade-in">
                        <div class="space-y-2">
                          <label class="text-sm font-semibold flex items-center gap-2">Netzentgelt (Arbeitspreis)</label>
                          <input type="number" step="0.01" value="${config.netzentgelt}"
                            onchange="handleConfigChange('netzentgelt', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-sm font-semibold flex items-center gap-2">Strompreis-Steigerung (%/Jahr)</label>
                          <input type="number" step="0.5" value="${config.inflationRate}"
                            onchange="handleConfigChange('inflationRate', Number(this.value))"
                            class="field w-full p-3 border rounded-xl outline-none font-bold focus-ring"
                            style="background:${theme.bgLight};border-color:${theme.secondary}50;color:${theme.primary}" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-sm font-semibold flex items-center gap-2">Fossile Preis-Steigerung (%/Jahr)
                            <span class="text-[11px] text-slate-400 font-normal">Gas/Öl/Sprit inkl. CO₂-Preis</span>
                          </label>
                          <input type="number" step="0.5" value="${config.inflationRateFossil ?? config.inflationRate}"
                            onchange="handleConfigChange('inflationRateFossil', Number(this.value))"
                            class="field w-full p-3 border rounded-xl outline-none font-bold focus-ring"
                            style="background:${theme.bgLight};border-color:${theme.secondary}50;color:${theme.primary}" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-sm font-semibold">Wartung Altsystem (€/Jahr)</label>
                          <input type="number" step="10" value="${config.wartungOld}"
                            onchange="handleConfigChange('wartungOld', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-sm font-semibold">Wartung Neusystem (€/Jahr)</label>
                          <input type="number" step="10" value="${config.wartungNeu ?? config.wartungOld}"
                            onchange="handleConfigChange('wartungNeu', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>

                        ${config.modulePV ? `
                        <div class="space-y-2">
                          <label class="text-sm font-semibold flex items-center gap-2">Rücklage Wechselrichter (€)
                            <span class="text-[11px] text-slate-400 font-normal">einmalig, Jahr 15</span>
                          </label>
                          <input type="number" step="100" value="${config.wrErsatzKosten ?? 1500}"
                            onchange="handleConfigChange('wrErsatzKosten', Number(this.value))"
                            class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>
                        ` : ''}
                      </div>
                    ` : ''}
                  </div>

                  ${config.moduleWP ? `
                    ${config.selbstbewohnteWE > 0 ? `
                      <div class="space-y-3">
                        <h4 class="text-sm font-bold text-slate-600 uppercase tracking-wide">Förderung</h4>
                        ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                          <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors" style="border-color:${theme.secondary}50;background:${theme.bgLight}">
                            <input type="checkbox" ${config.weUnter40k === 1 ? 'checked' : ''}
                              onchange="handleConfigChange('weUnter40k', this.checked ? 1 : 0)"
                              class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                            <span class="text-sm font-medium" style="color:${theme.primary}">
                              Haushalts-Einkommen liegt unter 40.000 € (Aktiviert 30% KfW-Bonus)
                            </span>
                          </label>
                        ` : `
                          <div class="space-y-2 p-4 border rounded-xl" style="border-color:${theme.secondary}50;background:${theme.bgLight}">
                            <label class="text-sm font-semibold" style="color:${theme.primary}">
                              Wie viele der ${config.selbstbewohnteWE} selbstbewohnten Einheiten haben ein Haushaltseinkommen &lt; 40.000 €?
                            </label>
                            <input type="number" min="0" max="${config.selbstbewohnteWE}" value="${config.weUnter40k}"
                              onchange="handleConfigChange('weUnter40k', Number(this.value))"
                              class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                            <p class="text-xs" style="color:${theme.primary}">Aktiviert den 30% Einkommensbonus anteilig.</p>
                          </div>
                        `}
                      </div>
                    ` : `
                      <div class="p-4 border border-slate-200 bg-slate-100 rounded-xl flex items-center gap-3 text-slate-500">
                        <span class="w-5 h-5">${Icons.info()}</span>
                        <span class="text-sm">Für voll vermietete Objekte entfallen Klima-/Einkommensbonus (max. 35%).</span>
                      </div>
                    `}
                  ` : ''}

                  ${(Number(config.wohneinheiten || 1) - Number(config.selbstbewohnteWE || 0)) > 0 ? `
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                      <label class="w-full flex items-center gap-3 p-4 cursor-pointer ${config.vermieterModus ? '' : 'bg-slate-50 hover:bg-slate-100'} transition-colors">
                        <input type="checkbox" ${config.vermieterModus ? 'checked' : ''}
                          onchange="handleConfigChange('vermieterModus', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold">Vermieter-Rendite berechnen
                          <span class="text-xs font-normal text-slate-400">– Wärmelieferung &amp; Mieterstrom für die ${Number(config.wohneinheiten || 1) - Number(config.selbstbewohnteWE || 0)} vermietete(n) WE</span>
                        </span>
                      </label>

                      ${config.vermieterModus && vermieter ? `
                        <div class="p-4 space-y-4 border-t border-slate-100 animate-fade-in">
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                              <label class="text-sm font-semibold flex justify-between items-end h-6">
                                <span>Wohnfläche ges. (m²)</span>
                                ${!config.wohnflaeche ? `<span class="text-[11px] text-slate-400">Annahme: ${formatDE(vermieter.wohnflaeche)} m²</span>` : ''}
                              </label>
                              <input type="number" step="5" value="${config.wohnflaeche}" placeholder="${vermieter.wohnflaeche}"
                                onchange="handleConfigChange('wohnflaeche', this.value)"
                                class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                            </div>

                            <div class="space-y-2">
                              <label class="text-sm font-semibold h-6 flex items-end">Wärme-Refinanzierung</label>
                              <select onchange="handleConfigChange('waermeModell', this.value)"
                                class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                                <option value="contracting" ${(config.waermeModell ?? 'contracting') === 'contracting' ? 'selected' : ''}>Wärmelieferung / Contracting (§ 556c BGB)</option>
                                <option value="umlage559e" ${config.waermeModell === 'umlage559e' ? 'selected' : ''}>Modernisierungsumlage 10 % (§ 559e BGB)</option>
                                <option value="aus" ${config.waermeModell === 'aus' ? 'selected' : ''}>keine</option>
                              </select>
                            </div>

                            <div class="space-y-2">
                              <label class="text-sm font-semibold h-6 flex items-end">Mieterstrom-Modell</label>
                              <select onchange="handleConfigChange('mieterstromModell', this.value)"
                                class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                                <option value="42b" ${(config.mieterstromModell ?? '42b') === '42b' ? 'selected' : ''}>Gebäudeversorgung (§ 42b EnWG)</option>
                                <option value="klassisch" ${config.mieterstromModell === 'klassisch' ? 'selected' : ''}>Mieterstrom klassisch (mit Zuschlag)</option>
                                <option value="aus" ${config.mieterstromModell === 'aus' ? 'selected' : ''}>kein Mieterstrom</option>
                              </select>
                            </div>

                            ${vermieter.contractingAktiv || (config.waermeModell ?? 'contracting') === 'contracting' ? `
                              <div class="space-y-2">
                                <label class="text-sm font-semibold flex justify-between items-end h-6">
                                  <span>Wärme-Arbeitspreis (€/kWh)</span>
                                  ${Math.abs(vermieter.arbeitspreis - vermieter.arbeitspreisNeutral) > 0.0005 ? `
                                    <button onclick="handleConfigChange('waermeArbeitspreis', ${vermieter.arbeitspreisNeutral})"
                                      title="Kostenneutraler Preis nach § 556c BGB / WärmeLV"
                                      class="text-[11px] font-bold px-2 py-0.5 rounded-md border hover:opacity-80"
                                      style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                                      neutral: ${formatDE(vermieter.arbeitspreisNeutral * 100, 2)} ct
                                    </button>
                                  ` : ''}
                                </label>
                                <input type="number" step="0.001" value="${config.waermeArbeitspreis}" placeholder="${vermieter.arbeitspreisNeutral}"
                                  onchange="handleConfigChange('waermeArbeitspreis', this.value)"
                                  class="field w-full p-3 bg-white border ${vermieter.kostenneutral ? 'border-slate-200' : 'border-amber-400 ring-2 ring-amber-100'} rounded-xl outline-none focus-ring" />
                                ${!vermieter.kostenneutral ? `<p class="text-[11px] text-amber-700 font-semibold">Nicht kostenneutral: Mieter zahlen ${formatDE(vermieter.mieterWaermeKostenNeu - vermieter.alteMieterHeizkosten)} €/a mehr als bisher (§ 556c BGB gefährdet die Umstellung).</p>` : ''}
                              </div>

                              <div class="space-y-2">
                                <label class="text-sm font-semibold h-6 flex items-end">Wärme-Grundpreis (€/Monat je WE)</label>
                                <input type="number" step="1" value="${config.waermeGrundpreis ?? ASSUMPTIONS.regulatory.landlord.defaultHeatBasePricePerUnitMonth}"
                                  onchange="handleConfigChange('waermeGrundpreis', Number(this.value))"
                                  class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                              </div>
                            ` : ''}

                            ${(config.mieterstromModell ?? '42b') !== 'aus' ? `
                              <div class="space-y-2">
                                <label class="text-sm font-semibold flex justify-between items-end h-6">
                                  <span>Strombedarf Mieter (kWh/a)</span>
                                  ${!config.mieterStromBedarf ? `<span class="text-[11px] text-slate-400">Annahme: ${formatDE(vermieter.mieterStromBedarf)}</span>` : ''}
                                </label>
                                <input type="number" step="100" value="${config.mieterStromBedarf}" placeholder="${vermieter.mieterStromBedarf}"
                                  onchange="handleConfigChange('mieterStromBedarf', this.value)"
                                  class="field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                              </div>

                              <div class="space-y-2">
                                <label class="text-sm font-semibold flex justify-between items-end h-6">
                                  <span>Mieterstrompreis (€/kWh)</span>
                                  <span class="text-[11px] text-slate-400">Deckel: ${formatDE(vermieter.msPreisDeckel * 100, 1)} ct (§ 42a)</span>
                                </label>
                                <input type="number" step="0.01" value="${config.mieterstromPreis}" placeholder="${vermieter.msPreisDeckel}"
                                  onchange="handleConfigChange('mieterstromPreis', this.value)"
                                  class="field w-full p-3 bg-white border ${vermieter.msPreis > vermieter.msPreisDeckel + 0.0005 ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200'} rounded-xl outline-none focus-ring" />
                                ${vermieter.msPreis > vermieter.msPreisDeckel + 0.0005 ? `<p class="text-[11px] text-amber-700 font-semibold">Über der Preisobergrenze von 90 % des Grundversorgertarifs (§ 42a Abs. 4 EnWG).</p>` : ''}
                              </div>
                            ` : ''}
                          </div>

                          <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-3 border-t border-slate-100">
                            <div class="text-center px-2 py-2 rounded-lg" style="background:${theme.bgLight}">
                              <div class="text-[15px] font-black leading-none" style="color:${theme.primary}">${formatDE(vermieter.cashflowJahr1)} €</div>
                              <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Cashflow / Jahr</div>
                            </div>
                            <div class="text-center px-2 py-2 rounded-lg bg-slate-50">
                              <div class="text-[15px] font-black leading-none text-slate-700">${formatDE(vermieter.rendite, 1)} %</div>
                              <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Rendite Jahr 1</div>
                            </div>
                            <div class="text-center px-2 py-2 rounded-lg bg-slate-50">
                              <div class="text-[15px] font-black leading-none text-slate-700">${vermieter.amortisationStatisch ? vermieter.amortisationStatisch + ' J.' : '–'}</div>
                              <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Amortisation</div>
                            </div>
                            <div class="text-center px-2 py-2 rounded-lg ${vermieter.kostenneutral ? 'bg-slate-50' : 'bg-amber-50'}">
                              <div class="text-[15px] font-black leading-none ${vermieter.kostenneutral ? 'text-slate-700' : 'text-amber-600'}">${vermieter.kostenneutral ? '✓' : '!'}</div>
                              <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Kostenneutral</div>
                            </div>
                          </div>

                          <p class="text-[10px] text-slate-400 leading-relaxed">
                            Rechtsgrundlagen: § 556c BGB i.V.m. WärmeLV (Kostenneutralität, Ankündigung 3 Monate vorab in Textform),
                            § 559e BGB (10 % nach Abzug der Förderung, −15 % Erhaltungspauschale, Kappgrenze 0,50 €/m² mtl.),
                            § 42a/42b EnWG, Mieterstromzuschlag Stand 06/2026 (monatliche Degression – vor Angebot prüfen),
                            CO2KostAufG-Stufenmodell. Prognose ohne Gewähr, keine Rechtsberatung.
                          </p>
                        </div>
                      ` : ''}
                    </div>
                  ` : ''}

                  <div class="space-y-3 pt-2">
                    <h4 class="text-sm font-bold text-slate-600 uppercase tracking-wide">Geplante Investitionen (Brutto)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                      ${config.moduleWP ? `
                      <div class="space-y-2 relative">
                        <label class="flex justify-between items-end text-sm font-semibold">
                          <span>Wärmepumpe</span>
                          <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                            Empfehlung: ${derivedParams.empfohleneWpKw} kW
                          </span>
                        </label>
                        <div class="relative">
                          <input type="text" inputmode="numeric" value="${formatDE(config.costWP)}"
                            onchange="handleConfigChange('costWP', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                            class="field w-full p-4 pr-10 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 pointer-events-none">€</span>
                        </div>
                      </div>
                      ` : ''}

                      ${config.modulePV ? `
                      <div class="space-y-2 relative">
                        <label class="flex justify-between items-end text-sm font-semibold">
                          <span>PV-Anlage</span>
                          <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                            Empfehlung: ${derivedParams.empfohlenePv} kWp
                          </span>
                        </label>
                        <div class="relative">
                          <input type="text" inputmode="numeric" value="${formatDE(config.costPV)}"
                            onchange="handleConfigChange('costPV', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                            class="field w-full p-4 pr-10 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 pointer-events-none">€</span>
                        </div>
                      </div>

                      <div class="space-y-2 relative">
                        <label class="flex justify-between items-end text-sm font-semibold">
                          <span>Batteriespeicher</span>
                          <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                            Empfehlung: ${derivedParams.empfohleneBatterie} kWh
                          </span>
                        </label>
                        <div class="relative">
                          <input type="text" inputmode="numeric" value="${formatDE(config.costBattery)}"
                            onchange="handleConfigChange('costBattery', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                            class="field w-full p-4 pr-10 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 pointer-events-none">€</span>
                        </div>
                      </div>
                      ` : ''}

                      ${config.moduleWB ? `
                      <div class="space-y-2 relative">
                        <label class="flex justify-between items-end text-sm font-semibold">
                          <span>Wallbox</span>
                          <span class="text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md text-[11px] font-bold">Optional</span>
                        </label>
                        <div class="relative">
                          <input type="text" inputmode="numeric" value="${formatDE(config.costWallbox)}"
                            onchange="handleConfigChange('costWallbox', Number(this.value.replace(/[^0-9]/g, '')) || 0)"
                            class="field w-full p-4 pr-10 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 pointer-events-none">€</span>
                        </div>
                      </div>
                      ` : ''}
                    </div>
                  </div>
                </div>
              ` : ''}

              ${warnings.length ? `
                <div class="mt-8 p-4 border rounded-xl bg-amber-50 border-amber-200 animate-fade-in">
                  <div class="text-sm font-bold text-amber-700 mb-2 flex items-center gap-2">
                    <span class="w-4 h-4">${Icons.info()}</span>
                    Plausibilität prüfen (${warnings.length})
                  </div>
                  <ul class="space-y-1.5">
                    ${warnings.map(x => `
                      <li class="text-xs text-amber-800 flex gap-2 items-start">
                        <button onclick="setWizardStep('${x.step}')" class="underline font-bold shrink-0 hover:text-amber-900">${stepLabelOf(x.step)}</button>
                        <span>${x.text}</span>
                      </li>
                    `).join('')}
                  </ul>
                </div>
              ` : ''}

              ${isLast ? `
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3">
                  <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Ersparnis Jahr 1</div>
                    <div class="text-lg font-black" style="color:${theme.primary}">${formatDE(finance.ersparnisJahr1)} €</div>
                  </div>
                  <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Autarkie</div>
                    <div class="text-lg font-black text-slate-700">${config.modulePV ? kpis.autarkie + ' %' : '–'}</div>
                  </div>
                  <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Netto-Investition</div>
                    <div class="text-lg font-black text-slate-700">${formatDE(finance.nettoInvest)} €</div>
                  </div>
                  <div class="p-3 rounded-xl border border-slate-200 bg-white">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Amortisation</div>
                    <div class="text-lg font-black text-slate-700">${finance.amortisationYear ? finance.amortisationYear + ' Jahre' : '> 30 J.'}</div>
                  </div>
                </div>
              ` : `
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3 opacity-50" title="Wird im letzten Schritt berechnet">
                  ${['Ersparnis Jahr 1', 'Autarkie', 'Netto-Investition', 'Amortisation'].map(label => `
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                      <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">${label}</div>
                      <div class="text-lg font-black text-slate-300">–</div>
                    </div>
                  `).join('')}
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Die Ergebnis-Kennzahlen werden im letzten Schritt „Preise &amp; Investition" berechnet.</p>
              `}

              <div class="flex justify-between mt-6 pt-6 border-t border-slate-100">
                <button onclick="gotoWizardStep(-1)"
                  ${curIdx === 0 ? 'disabled' : ''}
                  class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all ${curIdx === 0 ? 'opacity-0 cursor-default' : 'bg-slate-100 text-dark-600 hover:bg-slate-200'}">
                  <span class="w-4 h-4">${Icons.arrowLeft()}</span>
                  Zurück
                </button>

                ${!isLast ? `
                  <button onclick="gotoWizardStep(1)" ${keinSystem ? 'disabled title="Bitte zuerst mindestens ein System auswählen"' : ''}
                    class="flex items-center gap-2 px-6 py-3 text-white rounded-xl font-semibold transition-all shadow-md ${keinSystem ? 'opacity-40 cursor-not-allowed' : ''}"
                    style="background:${theme.primary}">
                    Weiter: ${steps[curIdx + 1].label}
                    <span class="w-4 h-4">${Icons.arrowRight()}</span>
                  </button>
                ` : `
                  <button onclick="setView('dashboard')"
                    class="flex items-center gap-2 px-8 py-3 text-white rounded-xl font-bold transition-all"
                    style="background:${theme.primary};box-shadow:0 10px 15px -3px ${theme.primary}40">
                    Druckvorschau Report
                    <span class="w-4 h-4">${Icons.printer()}</span>
                  </button>
                `}
              </div>
            </div>
          </div>
        </div>
      `;
        }

        // =========================================================
        // PART 1 ENDPOINT:
        // dashboard render + charts start in part 2
        // =========================================================
        function renderDashboardPlaceholder() {
            const theme = getActiveTheme();
            return `
        <div class="min-h-screen bg-slate-200 text-dark-600 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-[${theme.primary}] text-white p-4 z-[80] flex justify-between items-center no-print">
            <div class="font-bold flex items-center gap-3" style="color:${theme.secondary}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: ${theme.name} Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>

              <button
                onclick="openCustomerProfile()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.secondary}">
                <span class="w-4 h-4">${Icons.users()}</span>
                Kundenprofil
              </button>

              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors" style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>

              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors ml-2" style="background:${theme.primary}">
                PDF-Vorschau
              </button>

              <button onclick="saveProfitabilityCalculation()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.save()}</span>
                Speichern
              </button>
            </div>
          </div>

          <div class="max-w-5xl mx-auto p-10">
            <div class="bg-white rounded-2xl border border-slate-200  p-10 text-center">
              <div class="text-lg font-bold mb-2">Dashboard wird in Teil 2 fortgesetzt</div>
              <div class="text-sm text-slate-500">Hier beginnt direkt der Druckreport, die Sidebar, alle A4-Seiten und die Chart-Initialisierung.</div>
            </div>
          </div>
        </div>
      `;
        }

        // =========================================================
        // ROOT RENDER
        // =========================================================
        // Debounced Re-Render: bündelt schnell aufeinanderfolgende Eingaben zu einem
        // einzigen DOM-Update (renderApp ist im zweiten Script-Block definiert)
        let renderTimer = null;
        function queueRender(delay = 0) {
            clearTimeout(renderTimer);
            renderTimer = setTimeout(() => renderApp(), delay);
        }
    </script>
    <script>
        // =========================================================
        // DASHBOARD / REPORT RENDER
        // =========================================================
        function renderSidebar(computed) {
            const config = state.config;
            const { derivedParams } = computed;
            const theme = getActiveTheme();

            return `
        ${state.isSidebarOpen ? `
          <div class="fixed inset-0 bg-[${theme.primary}]/40 backdrop-blur-sm z-[90] no-print transition-opacity" onclick="setSidebarOpen(false)"></div>
        ` : ''}

        <div class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl sidebar-transition z-[100] flex flex-col no-print ${state.isSidebarOpen ? 'translate-x-0' : 'translate-x-full'}">
          <div class="p-5 bg-[${theme.primary}] text-white flex justify-between items-center z-10 shadow-md">
            <h2 class="text-lg font-bold flex items-center gap-2" style="color:${theme.secondary}">
              <span class="w-4 h-4">${Icons.sliders()}</span>
              Live-Editor
            </h2>
            <button onclick="setSidebarOpen(false)" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-1.5 rounded-lg">
              <span class="w-5 h-5">${Icons.x()}</span>
            </button>
          </div>

          <div id="sidebar-scroll-container" class="p-4 flex-1 space-y-4 overflow-y-auto custom-scroll">

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('kunde')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.home()}</span>
                  Kunde & Gebäude
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kunde ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kunde ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Unternehmen / Design</label>
                    <select onchange="handleConfigChange('company', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                      <option ${config.company === 'Werkstudio' ? 'selected' : ''}>Werkstudio</option>
                      <option ${config.company === 'Solar Aspekt' ? 'selected' : ''}>Solar Aspekt</option>
                    </select>
                  </div>
                  
                  ${sidebarInput({
                label: 'Kundenname',
                type: 'text',
                value: config.name,
                onchange: `handleConfigChange('name', this.value)`
            })}
                  ${sidebarInput({
                label: 'PLZ',
                type: 'text',
                value: config.plz,
                onchange: `handleConfigChange('plz', this.value)`
            })}

                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Gebäudeart</label>
                    <select onchange="handleConfigChange('gebaeudeArt', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                      <option ${config.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                      <option ${config.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                    </select>
                  </div>

                  ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Nutzung</label>
                      <select onchange="handleConfigChange('selbstbewohnteWE', this.value === 'Selbstbewohnt' ? 1 : 0)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                        <option ${config.selbstbewohnteWE === 1 ? 'selected' : ''}>Selbstbewohnt</option>
                        <option ${config.selbstbewohnteWE !== 1 ? 'selected' : ''}>Vermietet</option>
                      </select>
                    </div>
                  ` : `
                    ${sidebarInput({
                label: 'Wohneinheiten gesamt',
                type: 'number',
                value: config.wohneinheiten,
                onchange: `handleConfigChange('wohneinheiten', Number(this.value)); if (state.config.selbstbewohnteWE > Number(this.value)) handleConfigChange('selbstbewohnteWE', Number(this.value))`
            })}
                    ${sidebarInput({
                label: 'Davon selbst bewohnt',
                type: 'number',
                value: config.selbstbewohnteWE,
                onchange: `handleConfigChange('selbstbewohnteWE', Math.min(state.config.wohneinheiten, Number(this.value)))`
            })}
                  `}
                  ` : ''}
                </div>
              ` : ''}
            </div>

            ${config.modulePV ? `
            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('dach')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.maximize()}</span>
                  Dachflächen
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.dach ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.dach ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${config.dachseiten.map((dach) => `
                    <div class="flex gap-2 mb-3 pb-3 border-b border-slate-100 last:border-0 last:pb-0 last:mb-0 items-end flex-wrap">
                      <div class="w-full flex gap-2 items-end">
                        <div class="flex-1">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Bezeichnung</label>
                          <input type="text" value="${dach.designation || ''}" placeholder="z.B. Hauptdach Süd"
                            onchange="updateDachseite(${dach.id}, 'designation', this.value)"
                            class="field-sm w-full p-2 border rounded-lg text-xs outline-none" />
                        </div>
                        <div class="w-[40%]">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Dachform</label>
                          <select onchange="updateDachseite(${dach.id}, 'roofForm', this.value)"
                            class="field-sm w-full p-2 border rounded-lg text-xs outline-none">
                            ${['Satteldach', 'Walmdach', 'Pultdach', 'Flachdach', 'Zeltdach', 'Krüppelwalmdach', 'Sonstige'].map(opt => `
                              <option ${((dach.roofForm || 'Satteldach') === opt) ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                        </div>
                      </div>
                      <div class="w-[45%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">Ausrichtung</label>
                        <select onchange="updateDachseite(${dach.id}, 'ausrichtung', this.value)" class="field-sm w-full p-2 border rounded-lg text-xs outline-none">
                          ${['Süd', 'Süd-Ost', 'Süd-West', 'Ost', 'West', 'Nord-Ost', 'Nord-West', 'Nord'].map(opt => `
                            <option ${dach.ausrichtung === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                      </div>
                      <div class="w-[20%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">Neigung</label>
                        <input type="number" value="${dach.neigung}" onchange="updateDachseite(${dach.id}, 'neigung', Number(this.value))" class="field-sm w-full p-2 border rounded-lg text-xs outline-none" />
                      </div>
                      <div class="w-[25%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">kWp</label>
                        <input type="number" step="0.1" value="${dach.customKwp || ''}" placeholder="Auto" onchange="updateDachseite(${dach.id}, 'customKwp', this.value)" class="field-sm w-full p-2 border rounded-lg text-xs outline-none focus-ring" />
                      </div>
                      <div class="w-full flex gap-2 mt-1 items-end">
                        <div class="flex-1">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Eindeckung</label>
                          <select onchange="updateDachseite(${dach.id}, 'eindeckung', this.value)" class="field-sm w-full p-2 border rounded-lg text-xs outline-none">
                            ${['Ziegel', 'Blech', 'Trapezblech', 'Flachdach/Folie', 'Schiefer'].map(opt => `
                              <option ${((dach.eindeckung || 'Ziegel') === opt) ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                        </div>
                        <div class="flex-1">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Typ / Material</label>
                          <input type="text" value="${dach.eindeckungTyp || ''}" onchange="updateDachseite(${dach.id}, 'eindeckungTyp', this.value)" placeholder="z.B. Beton" class="field-sm w-full p-2 border rounded-lg text-xs outline-none" />
                        </div>
                        ${config.dachseiten.length > 1 ? `
                          <button onclick="removeDachseite(${dach.id})" class="text-red-500 hover:bg-red-50 p-2 rounded shrink-0 border border-transparent">
                            <span class="w-3.5 h-3.5 inline-block">${Icons.x()}</span>
                          </button>
                        ` : ''}
                      </div>
                      ${(() => {
                    const ertrag = (computed.derivedParams.dachErtraege || []).find(e => e.id === dach.id) || {};
                    return `
                          <div class="w-full flex justify-between items-center mt-1">
                            <span class="text-[10px] text-slate-400">${ertrag.quelle === 'PVGIS' ? 'PVGIS (Standort & Neigung)' : 'Schätzwert (Regionalfaktor)'}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border"
                              style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                              ≈ ${formatDE(ertrag.ey || 0)} kWh/kWp
                            </span>
                          </div>
                        `;
                })()}
                    </div>
                  `).join('')}

                  <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''} class="w-full mt-2 py-2 border border-dashed border-slate-300 rounded-lg text-xs font-bold text-slate-500 hover:bg-white disabled:opacity-50">
                    + Seite hinzufügen
                  </button>
                </div>
              ` : ''}
            </div>
            ` : ''}

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('altsystem')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.users()}</span>
                  Altsystem & Bedarf
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.altsystem ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.altsystem ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Hauptheizung</label>
                    <select onchange="handleConfigChange('heizungArt', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                      ${['Gas', 'Öl', 'Holz / Pellets', 'Nachtspeicher'].map(opt => `
                        <option ${config.heizungArt === opt ? 'selected' : ''}>${opt}</option>
                      `).join('')}
                    </select>
                  </div>

                  <div class="flex gap-2">
                    <div class="w-1/2">
                      ${sidebarInput({
                    label: 'Alter (Jahre)',
                    type: 'number',
                    value: config.heizungAlter,
                    onchange: `handleConfigChange('heizungAlter', Number(this.value))`
                })}
                    </div>
                    <div class="w-1/2">
                      ${sidebarInput({
                    label: `Verbrauch ${getHeizEinheit(config.heizungArt)}`,
                    type: 'number',
                    step: '500',
                    value: config.heizVerbrauch,
                    onchange: `handleConfigChange('heizVerbrauch', Number(this.value))`
                })}
                    </div>
                  </div>
                  ` : ''}

                  <div class="flex gap-2">
                    ${config.moduleWP ? `
                    <div class="w-1/2">
                      ${sidebarInput({
                    label: 'Personen',
                    type: 'number',
                    value: config.personen,
                    onchange: `handleConfigChange('personen', Number(this.value))`
                })}
                    </div>
                    ` : ''}
                    <div class="${config.moduleWP ? 'w-1/2' : 'w-full'}">
                      ${sidebarInput({
                    label: 'Haushaltsstrom',
                    type: 'number',
                    step: '100',
                    value: config.hhStrom,
                    rightLabel: 'kWh',
                    onchange: `handleConfigChange('hhStrom', Number(this.value))`
                })}
                    </div>
                  </div>

                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Heizsystem (Übergabe)</label>
                    <select onchange="handleConfigChange('heizSystem', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                      ${['Heizkörper', 'Fußbodenheizung', 'Beides'].map(opt => `
                        <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                      `).join('')}
                    </select>
                  </div>

                  <div class="flex gap-2 mb-3">
                    <div class="w-1/2">
                      <label class="text-xs font-bold text-slate-700 block mb-1.5">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                        <option ${config.warmwasserArt === 'Zentral' ? 'selected' : ''}>Zentral</option>
                        <option ${config.warmwasserArt === 'Dezentral' ? 'selected' : ''}>Dezentral</option>
                      </select>
                    </div>
                    <div class="w-1/2 flex items-center justify-center pt-5">
                      <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''} onchange="handleConfigChange('zirkulation', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-bold text-slate-700">Zirkulation</span>
                      </label>
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem Heizung</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="field-sm w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                        ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                          <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrHeizungDN}" onchange="handleConfigChange('rohrHeizungDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring" />
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem WW</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="field-sm w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                        ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                          <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrWWDN}" onchange="handleConfigChange('rohrWWDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring" />
                    </div>
                  </div>

                  ${config.zirkulation ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Rohrsystem Zirkulation</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="field-sm w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                          ${['Kupfer', 'Eisenrohr', 'Kunststoff', 'Verbundrohr', 'Edelstahl'].map(opt => `
                            <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <input type="text" value="${config.rohrZirkulationDN}" onchange="handleConfigChange('rohrZirkulationDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring" />
                      </div>
                    </div>
                  ` : ''}
                  ` : ''}

                  ${config.moduleWB ? `
                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Fahrzeug</label>
                    <select onchange="handleConfigChange('autoArt', this.value)" class="field-sm w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-dark-600 focus-ring">
                      <option ${config.autoArt === 'Verbrenner' ? 'selected' : ''}>Verbrenner</option>
                      <option ${config.autoArt === 'E-Auto' ? 'selected' : ''}>E-Auto</option>
                    </select>
                  </div>

                  ${sidebarInput({
                    label: 'Fahrleistung',
                    type: 'number',
                    step: '1000',
                    value: config.fahrleistung,
                    rightLabel: 'km/a',
                    onchange: `handleConfigChange('fahrleistung', Number(this.value))`
                })}

                  ${config.autoArt === 'Verbrenner' ? `
                    <div class="flex gap-2">
                      <div class="w-1/2">
                        ${sidebarInput({
                    label: 'Verbrauch',
                    type: 'number',
                    step: '0.5',
                    value: config.verbrennerVerbrauch,
                    rightLabel: 'l/100km',
                    onchange: `handleConfigChange('verbrennerVerbrauch', Number(this.value))`
                })}
                      </div>
                      <div class="w-1/2">
                        ${sidebarInput({
                    label: 'Spritpreis',
                    type: 'number',
                    step: '0.05',
                    value: config.preisSprit,
                    rightLabel: '€/l',
                    onchange: `handleConfigChange('preisSprit', Number(this.value))`
                })}
                      </div>
                    </div>
                  ` : ''}
                  ` : ''}
                </div>
              ` : ''}
            </div>

            ${config.moduleWP ? `
            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('kaminSolar')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.thermometer()}</span>
                  Zusatzheizung
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kaminSolar ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kaminSolar ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <label class="flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''} onchange="handleConfigChange('kaminVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                    <span class="text-xs font-bold text-slate-700">Kaminfeuer / Stückholz</span>
                  </label>
                  ${config.kaminVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-dark-600">Bedarf (Raummeter)</label><input type="number" value="${config.holzVerbrauch}" onchange="handleConfigChange('holzVerbrauch', Number(this.value))" class="field-sm w-full p-2 border rounded-lg" /></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-dark-600">Preis (€/RM)</label><input type="number" value="${config.preisHolz}" onchange="handleConfigChange('preisHolz', Number(this.value))" class="field-sm w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.kaminWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('kaminWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                      </label>
                    </div>
                  ` : ''}

                  <label class="flex items-center gap-2 cursor-pointer mb-2 mt-4 pt-4 border-t border-slate-100">
                    <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''} onchange="handleConfigChange('solarthermieVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                    <span class="text-xs font-bold text-slate-700">Solarthermie vorhanden</span>
                  </label>
                  ${config.solarthermieVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-dark-600">Kollektor-Art</label><select onchange="handleConfigChange('solarthermieArt', this.value)" class="field-sm w-full p-2 border rounded-lg bg-white"><option ${config.solarthermieArt === 'Flachkollektor' ? 'selected' : ''}>Flachkollektor</option><option ${config.solarthermieArt === 'Röhrenkollektor' ? 'selected' : ''}>Röhrenkollektor</option></select></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-dark-600">Anzahl Kollektoren</label><input type="number" value="${config.solarKollektoren}" onchange="handleConfigChange('solarKollektoren', Number(this.value))" class="field-sm w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.solarthermieWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('solarthermieWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                      </label>
                    </div>
                  ` : ''}
                </div>
              ` : ''}
            </div>
            ` : ''}

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('preise')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                  Energiepreise
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.preise ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.preise ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${sidebarInput({
                    label: 'Strompreis',
                    type: 'number',
                    step: '0.01',
                    value: config.preisStrom,
                    rightLabel: '€/kWh',
                    onchange: `handleConfigChange('preisStrom', Number(this.value))`
                })}
                  ${sidebarInput({
                    label: 'Netzentgelt (AP)',
                    type: 'number',
                    step: '0.01',
                    value: config.netzentgelt,
                    rightLabel: '€/kWh',
                    onchange: `handleConfigChange('netzentgelt', Number(this.value))`
                })}
                  ${config.moduleWP ? sidebarInput({
                    label: `Preis ${config.heizungArt}`,
                    type: 'number',
                    step: '0.01',
                    value: config.preisHeizMedium,
                    rightLabel: `€/${getHeizEinheit(config.heizungArt)}`,
                    onchange: `handleConfigChange('preisHeizMedium', Number(this.value))`
                }) : ''}
                  ${config.modulePV ? sidebarInput({
                    label: 'Einspeisevergütung',
                    type: 'number',
                    step: '0.01',
                    value: config.preisEinspeisung,
                    rightLabel: '€/kWh',
                    onchange: `handleConfigChange('preisEinspeisung', Number(this.value))`
                }) : ''}
                  ${sidebarInput({
                    label: 'Energie-Inflation',
                    type: 'number',
                    step: '0.5',
                    value: config.inflationRate,
                    rightLabel: '%/a',
                    onchange: `handleConfigChange('inflationRate', Number(this.value))`
                })}
                  ${sidebarInput({
                    label: 'Wartung & Fixkosten (Altsystem)',
                    type: 'number',
                    step: '10',
                    value: config.wartungOld,
                    rightLabel: '€/Jahr',
                    onchange: `handleConfigChange('wartungOld', Number(this.value))`
                })}
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden mb-4">
              <button onclick="toggleSidebarSection('investitionen')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-dark-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.euro()}</span>
                  Investitionen
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.investitionen ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.investitionen ? `
                <div class="p-4 border-t border-slate-100 bg-white space-y-5">
                  
                  ${config.moduleWP ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-dark-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.thermoSnow()}</span> Wärmepumpe</h4>
                    ${sidebarInput({ label: 'Preis (Brutto)', type: 'number', step: '1000', value: config.costWP, rightLabel: '€', onchange: `handleConfigChange('costWP', Number(this.value))` })}
                    ${sidebarInput({ label: 'kW (Manuell)', type: 'number', step: '1', value: config.customWpKw, rightLabel: `Empf: ${derivedParams.empfohleneWpKw} kW`, placeholder: 'Auto', onchange: `handleConfigChange('customWpKw', this.value)` })}
                    ${sidebarInput({ label: 'JAZ (Manuell)', type: 'number', step: '0.1', value: config.customJAZ, rightLabel: `Auto: ${derivedParams.berechneteJaz}`, placeholder: 'Auto', onchange: `handleConfigChange('customJAZ', this.value)` })}
                    ${sidebarInput({ label: 'Kombi-Rabatt', type: 'number', step: '100', value: config.discountWP, rightLabel: '€', placeholder: '1000', onchange: `handleConfigChange('discountWP', this.value)` })}
                    ${sidebarInput({ label: 'Zusätzl. Förderung', type: 'number', step: '100', value: config.extraGrantWP, rightLabel: '€', placeholder: '0', onchange: `handleConfigChange('extraGrantWP', this.value)` })}
                    ${sidebarInput({ label: 'Förderquelle WP', type: 'text', value: config.extraGrantSourceWP, placeholder: 'z.B. Stadt Bad Homburg', onchange: `handleConfigChange('extraGrantSourceWP', this.value)` })}
                  </div>
                  ` : ''}

                  ${config.modulePV ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-dark-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.sun()}</span> PV & Speicher</h4>
                    ${sidebarInput({ label: 'Preis PV (Brutto)', type: 'number', step: '1000', value: config.costPV, rightLabel: '€', onchange: `handleConfigChange('costPV', Number(this.value))` })}
${sidebarInput({ label: 'Gesamt kWp (Manuell)', type: 'number', step: '1', value: config.customPvKwp, rightLabel: `Empf: ${derivedParams.empfohlenePv} kWp`, placeholder: 'Auto', onchange: `handleConfigChange('customPvKwp', this.value)` })}                    ${sidebarInput({ label: 'Kombi-Rabatt PV', type: 'number', step: '100', value: config.discountPV, rightLabel: '€', placeholder: '750', onchange: `handleConfigChange('discountPV', this.value)` })}
                    ${sidebarInput({ label: 'Zusätzl. Förderung PV', type: 'number', step: '100', value: config.extraGrantPV, rightLabel: '€', placeholder: '0', onchange: `handleConfigChange('extraGrantPV', this.value)` })}
                    ${sidebarInput({ label: 'Förderquelle PV', type: 'text', value: config.extraGrantSourcePV, placeholder: 'z.B. Kommune', onchange: `handleConfigChange('extraGrantSourcePV', this.value)` })}
                    <div class="mt-3 pt-3 border-t border-slate-200"></div>
                    ${sidebarInput({ label: 'Preis Akku (Brutto)', type: 'number', step: '500', value: config.costBattery, rightLabel: '€', onchange: `handleConfigChange('costBattery', Number(this.value))` })}
                    ${sidebarInput({ label: 'kWh (Manuell)', type: 'number', step: '1', value: config.customBatteryKwh, rightLabel: `Empf: ${derivedParams.empfohleneBatterie} kWh`, placeholder: 'Auto', onchange: `handleConfigChange('customBatteryKwh', this.value)` })}
                    ${sidebarInput({ label: 'Kombi-Rabatt Akku', type: 'number', step: '100', value: config.discountBattery, rightLabel: '€', placeholder: '250', onchange: `handleConfigChange('discountBattery', this.value)` })}
                    ${sidebarInput({ label: 'Zusätzl. Förderung Akku', type: 'number', step: '100', value: config.extraGrantBattery, rightLabel: '€', placeholder: '0', onchange: `handleConfigChange('extraGrantBattery', this.value)` })}
                    ${sidebarInput({ label: 'Förderquelle Akku', type: 'text', value: config.extraGrantSourceBattery, placeholder: 'z.B. Land Hessen', onchange: `handleConfigChange('extraGrantSourceBattery', this.value)` })}
                  </div>
                  ` : ''}

                  ${config.moduleWB ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-dark-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.car()}</span> Wallbox</h4>
                    ${sidebarInput({ label: 'Preis (Brutto)', type: 'number', step: '100', value: config.costWallbox, rightLabel: '€', onchange: `handleConfigChange('costWallbox', Number(this.value))` })}
                    ${sidebarInput({ label: 'Kombi-Rabatt', type: 'number', step: '100', value: config.discountWallbox, rightLabel: '€', placeholder: '150', onchange: `handleConfigChange('discountWallbox', this.value)` })}
                    ${sidebarInput({ label: 'Zusätzl. Förderung', type: 'number', step: '100', value: config.extraGrantWallbox, rightLabel: '€', placeholder: '0', onchange: `handleConfigChange('extraGrantWallbox', this.value)` })}
                    ${sidebarInput({ label: 'Förderquelle Wallbox', type: 'text', value: config.extraGrantSourceWallbox, placeholder: 'z.B. KfW', onchange: `handleConfigChange('extraGrantSourceWallbox', this.value)` })}
                  </div>
                  ` : ''}

                </div>
              ` : ''}
            </div>
          </div>

          <div class="p-5 bg-white border-t border-slate-200 sticky bottom-0 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)]">
            <button onclick="setSidebarOpen(false)" class="w-full flex justify-center items-center gap-2 px-4 py-3 text-white rounded-xl text-sm font-bold transition-colors" style="background:${theme.primary};box-shadow:0 10px 15px -3px ${theme.primary}40">
              <span class="w-4 h-4">${Icons.save()}</span>
              Speichern & Schließen
            </button>
          </div>
        </div>
      `;
        }

        function renderDashboard() {
            const config = state.config;
            const computed = getComputed();
            const { derivedParams, seasonalData, chartData, kpis, bedarfsMix, finance } = computed;
            const theme = getActiveTheme();
            const angebotsStufen = getAngebotsStufen(computed);
            const amortBand = getAmortisationsBandbreite(computed);

            const bedarfTotal = bedarfsMix.reduce((sum, item) => sum + Number(item.value || 0), 0);

            const hhPercent = bedarfTotal > 0 ? (config.hhStrom / bedarfTotal) * 100 : 0;
            const wpPercent = (config.moduleWP && bedarfTotal > 0) ? (derivedParams.wpStrombedarf / bedarfTotal) * 100 : 0;
            const evPercent = (config.moduleWB && config.fahrleistung > 0 && bedarfTotal > 0)
                ? (derivedParams.evStrombedarf / bedarfTotal) * 100
                : 0;

            const percentSum = hhPercent + wpPercent + evPercent;

            const activeModulesCount = (config.modulePV ? 1 : 0) + (config.moduleWP ? 1 : 0) + (config.moduleWB ? 1 : 0);
            const showMiddleStep = config.modulePV && (config.moduleWP || config.moduleWB);
            const gridColsMiddle = showMiddleStep ? 'grid-cols-3' : 'grid-cols-2';

            return `
        <div class="min-h-screen bg-slate-200 text-dark-600 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-white text-white p-4 z-[80] flex justify-between items-center no-print">
            <div class="font-bold flex items-center gap-3" style="color:${theme.secondary}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: ${theme.name} Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>

              <button
                onclick="openCustomerProfile()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.secondary}">
                <span class="w-4 h-4">${Icons.users()}</span>
                Kundenprofil
              </button>

              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors" style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>

              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors ml-2" style="background:${theme.primary}">
                PDF-Vorschau
              </button>

              <button onclick="saveProfitabilityCalculation()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.save()}</span>
                Speichern
              </button>
            </div>
          </div>

          ${renderSidebar(computed)}

          <div class="w-full transition-all duration-300 print:m-0 ${state.isSidebarOpen ? 'md:mr-[360px] lg:mx-auto lg:translate-x-[-180px]' : ''}">

            <div class="a4-page flex flex-col relative bg-white justify-center items-center print:bg-white" style="WebkitPrintColorAdjust:exact;printColorAdjust:exact">
              <div class="absolute top-0 inset-x-0 h-[35%] rounded-b-[40%] shadow-2xl" style="background:${theme.primary}"></div>
              <div class="bg-white p-16 rounded-[40px] shadow-2xl border border-slate-100 text-center z-10 w-[85%] mt-10" style="justify-self:center;">
               <img src="${theme.logo}" alt="Logo" class="h-14 object-contain drop-shadow-md"  style="justify-self:center"/>
                <h1 class="text-4xl md:text-5xl font-black text-[${theme.primary}] mb-6 tracking-tight leading-tight">
                  IHR INDIVIDUELLES<br/>ENERGIEKONZEPT
                </h1>
                <div class="w-20 h-2 mx-auto rounded-full mb-10" style="background:${theme.primary}"></div>
                <p class="text-2xl text-dark-600 font-medium mb-3">Für Familie ${config.name}</p>
                <p class="text-base text-slate-400 flex items-center justify-center gap-2 text-center">
                   
                   ${[config.street, config.plz, config.city].filter(Boolean).join(', ')}
                </p>
                <p class="text-sm text-slate-400 mt-6 pt-4 border-t border-slate-100">
                  Erstellt am ${new Date().toLocaleDateString('de-DE', { day: '2-digit', month: 'long', year: 'numeric' })}
                  &nbsp;·&nbsp; ${backendMeta.calculationId ? `Berechnung Nr. ${backendMeta.calculationId}` : 'Entwurf (noch nicht gespeichert)'}
                  &nbsp;·&nbsp; Rechenmodell ${ASSUMPTIONS.version}
                </p>
              </div>
              <div class="mt-auto mb-16 text-center z-10">
                <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3">Ausgearbeitet von</div>
                <div class="text-xl font-black text-dark-600 tracking-wide">${theme.name}</div>
                <div class="text-sm text-slate-500 mt-2">Meisterbetrieb für Gebäudetechnik & erneuerbare Energien</div>
              </div>
              <div class="absolute bottom-[-100px] left-[-100px] w-[500px] h-[500px] rounded-full blur-[120px] opacity-10" style="background:${theme.primary}"></div>
            </div>

            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('IHR KONZEPT')}

              <div class="absolute top-0 right-0 w-52 h-52 rounded-full blur-[100px] opacity-10 print:opacity-5" style="background:${theme.primary}"></div>

              <h1 class="text-3xl font-black mb-2 leading-tight" style="color:${theme.primary}">
                IHR WEG ZUR EIGENEN
              </h1>
              <h1 class="text-3xl font-black mb-3 leading-tight" style="color:${theme.secondary}">
                ENERGIEAUTARKIE
              </h1>

              <p class="text-sm font-bold mb-8 tracking-wide leading-relaxed" style="color:${theme.secondary}">
                WENIGER NETZ. MEHR KONTROLLE.<br/>MAXIMALE EFFIZIENZ – JEDEN TAG
              </p>

              <div class="space-y-3 text-[12px] leading-relaxed text-slate-700 mb-5 flex-1">
                <p>Sehr geehrte(r) ${config.name},</p>

                <p>
                  vielen Dank für Ihr Interesse an einer zukunftssicheren und autarken Energieversorgung. Gerne stellen wir Ihnen Ihr maßgeschneidertes Energiekonzept vor.
                </p>

                <p>
                  Auf den folgenden Seiten sehen Sie transparent, wie sich Ihr Energieprofil durch die intelligente Vernetzung Ihrer gewählten Systeme optimieren lässt. Zudem haben wir alle relevanten staatlichen Förderungen integriert, um Ihre Netto-Investition so effizient wie möglich zu gestalten.
                </p>

                <div class="bg-white p-4 rounded-xl border border-slate-200 mt-3">
                  <p class="font-bold mb-3 text-[13px]" style="color:${theme.primary}">
                    Ihr Energiekonzept im Überblick:
                  </p>

                  ${(() => {
                    const overviewItems = [
                        ['Ausgangslage:', 'Ihre heutigen Energiekosten'],
                        ['Lösungsarchitektur:', 'Dimensionierung Ihres Systems'],
                        ['Saisonale Auswertung:', 'Ihre Autarkie im Jahresverlauf'],
                        [`${theme.name.split(' ')[0]} Expertise:`, 'Ihre System-Vorteile'],
                        config.modulePV ? ['Sonnenenergie:', 'Photovoltaik & Batteriespeicher'] : null,
                        config.moduleWP ? ['Wärmepumpen-Technologie:', 'Elektrifizierung der Wärme'] : null,
                        config.moduleWB ? ['E-Mobilität:', 'Zapfen Sie die Sonne an'] : null,
                        activeModulesCount > 1 ? ['Sektorenkopplung:', 'Das intelligente Gesamtsystem'] : null,
                        ['Wirtschaftlichkeit:', 'Investition, Break-Even & ROI'],
                        ['Transparenz Teil I:', 'Technische Berechnungen'],
                        ['Transparenz Teil II:', 'Kennzahlen & Effizienz'],
                        ['Klimaschutz & Ablauf:', 'Nächste Schritte']
                    ].filter(Boolean);

                    const splitIndex = Math.ceil(overviewItems.length / 2);
                    const leftColumn = overviewItems.slice(0, splitIndex);
                    const rightColumn = overviewItems.slice(splitIndex);

                    const renderItem = (item, index) => `
                      <div class="flex gap-2.5 items-start">
                        <span class="w-3.5 h-3.5 shrink-0 mt-0.5" style="color:${theme.primary}">
                          ${Icons.checkSquare()}
                        </span>
                        <span class="leading-relaxed">
                          <strong class="block" style="color:${theme.primary}">
                            ${index + 1}. ${item[0]}
                          </strong>
                          ${item[1]}
                        </span>
                      </div>
                    `;

                    return `
                      <div class="grid grid-cols-2 gap-x-6 text-[11px] text-slate-700">
                        <div class="space-y-2">
                          ${leftColumn.map((item, index) => renderItem(item, index)).join('')}
                        </div>

                        <div class="space-y-2">
                          ${rightColumn.map((item, index) => renderItem(item, index + splitIndex)).join('')}
                        </div>
                      </div>
                    `;
                })()}
                </div>
              </div>

              <div class="mt-auto text-[12px] text-slate-700 leading-relaxed">
                <p class="mb-4">
                  Für Ihre Fragen und die nächsten Schritte stehen wir Ihnen jederzeit gerne in einem persönlichen Beratungsgespräch zur Verfügung.
                </p>
                <p>Mit freundlichen Grüßen</p>
                <p class="mt-2 font-bold font-serif text-[15px]" style="color:${theme.primary}">
                  Ihr ${theme.name}-Team
                </p>
              </div>
 
            </div>

            <!-- PAGE 1 -->
            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('1. AUSGANGSLAGE')}

              <h2 class="text-lg font-black mb-3" style="color:${theme.primary}">
                1. AUSGANGSLAGE & ENERGIE-TRANSFORMATION
              </h2>

              <div class="mb-3">
                <h3 class="text-xl font-black mb-1 leading-tight" style="color:${theme.primary}">
                  ${showMiddleStep ? 'Der 3-Stufen-Vergleich: Warum nur die Komplettlösung schützt' : 'Ihr Vergleich: Altsystem vs. Neues System'}
                </h3>
                <p class="text-[13px] text-dark-600 leading-relaxed">
                  Um die wahre Effizienz unseres Konzepts zu verstehen, betrachten wir ${showMiddleStep ? 'drei' : 'zwei'} Szenarien: Ihr <strong>heutiges ${config.moduleWP ? 'fossiles ' : ''}System</strong>${showMiddleStep ? ', eine <strong>reine Elektrifizierung</strong> (ohne eigene Solar)' : ''} und die <strong>${theme.name} ${config.modulePV ? 'Lösung inkl. PV' : 'Lösung'}</strong>.
                </p>
              </div>

              <div class="grid ${gridColsMiddle} gap-3 mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-col">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-100 pb-1" style="color:${theme.primary}">
                    1. Altsystem (Bestand)
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-dark-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5">Hausstrom<br><span class="text-[8.5px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom, 2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP ? `
                      <tr>
                        <td class="py-0.5">Heizung (${config.heizungArt})<br><span class="text-[8.5px] text-slate-400">${formatDE(config.heizVerbrauch)} ${getHeizEinheit(config.heizungArt)} × ${formatDE(config.preisHeizMedium, 2)} €/${getHeizEinheit(config.heizungArt)}</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(finance.heizkostenOld))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWP && config.kaminVorhanden ? `
                      <tr>
                        <td class="py-0.5">Kaminholz<br><span class="text-[8.5px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz, 2)} €/RM</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWB && config.fahrleistung > 0 ? `
                      <tr>
                        <td class="py-0.5">
                          Auto (${config.autoArt === 'Verbrenner' ? 'Verbrenner' : 'E-Auto'})<br>
                          <span class="text-[8.5px] text-slate-400">
                            ${config.autoArt === 'Verbrenner'
                        ? `${formatDE(Math.round((config.fahrleistung / 100) * config.verbrennerVerbrauch))} l × ${formatDE(config.preisSprit, 2)} €/l`
                        : `${formatDE(Math.round((config.fahrleistung / 100) * 20))} kWh × ${formatDE(config.preisStrom, 2)} €/kWh`
                    }
                          </span>
                        </td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(finance.evOldCost))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[8.5px] text-slate-400">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-0.5">${config.wartungOld} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="mt-auto pt-1.5 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[13px]">
                    <span>Kosten/Jahr</span>
                    <span>${formatDE(Math.round(finance.costOldTotal))} €</span>
                  </div>
                </div>

                ${showMiddleStep ? `
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-col">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-100 pb-1" style="color:${theme.primary}">
                    2. Elektrisch (Ohne Solar)
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-dark-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5">Hausstrom<br><span class="text-[8.5px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom, 2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP ? `
                      <tr>
                        <td class="py-0.5">Wärmepumpe<br><span class="text-[8.5px] text-slate-400">${formatDE(derivedParams.wpStrombedarf)} kWh × ${formatDE(config.preisStrom, 2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.wpStrombedarf * config.preisStrom))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                      <tr>
                        <td class="py-0.5">Kaminholz (Weiterbetrieb)<br><span class="text-[8.5px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz, 2)} €/RM</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWB && config.fahrleistung > 0 ? `
                      <tr>
                        <td class="py-0.5">E-Auto Laden<br><span class="text-[8.5px] text-slate-400">${formatDE(derivedParams.evStrombedarf)} kWh × ${formatDE(config.preisStrom, 2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.evStrombedarf * config.preisStrom))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[8.5px] text-slate-400">Neusystem</span></td>
                        <td class="text-right font-medium align-top py-0.5">${finance.wartungNeu} €</td>
                      </tr>
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">§14a EnWG Rabatt<br><span class="text-[8.5px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-0.5">-${finance.ersparnis14aAllElectric} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="text-[8.5px] text-slate-400 italic mb-1.5 leading-tight">Ohne Solar machen Sie sich komplett abhängig vom Netzstrompreis.</div>
                  <div class="mt-auto pt-1.5 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[13px]">
                    <span>Kosten/Jahr</span>
                    <span>${formatDE(Math.round(finance.costAllElectricBase))} €</span>
                  </div>
                </div>
                ` : ''}

                <div class="bg-white border-2 rounded-xl p-3 flex flex-col" style="border-color:${theme.primary}">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-200 pb-1" style="color:${theme.primary}">
                    ${showMiddleStep ? '3. Komplettlösung (Mit Solar)' : '2. Neues System'}
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-slate-700 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5 font-semibold">Gesamtstrombedarf<br><span class="text-[8.5px] text-slate-400 font-normal">Alle gewählten Sektoren</span></td>
                        <td class="text-right font-bold align-top py-0.5">${formatDE(kpis.totalBedarf)} kWh</td>
                      </tr>
                      ${config.modulePV ? `
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">Kostenlos durch Solar<br><span class="text-[8.5px] opacity-70">Direktverbrauch & Speicher</span></td>
                        <td class="text-right font-bold align-top py-0.5">-${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Rest-Netzbezug<br><span class="text-[8.5px] text-slate-500">${formatDE(kpis.totalNetzbezug)} kWh × ${formatDE(config.preisStrom, 2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(kpis.totalNetzbezug * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                      <tr>
                        <td class="py-0.5">Kaminholz<br><span class="text-[8.5px] text-slate-500">Bleibt im System</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[8.5px] text-slate-500">Neusystem</span></td>
                        <td class="text-right font-medium align-top py-0.5">${finance.wartungNeu} €</td>
                      </tr>
                      ${(config.moduleWP || config.moduleWB) ? `
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">§14a EnWG Rabatt<br><span class="text-[8.5px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-0.5">-${finance.ersparnis14a} €</td>
                      </tr>
                      ` : ''}
                      ${config.modulePV ? `
                      <tr style="color:${theme.secondary}">
                        <td class="py-0.5 font-bold">Einspeisevergütung<br><span class="text-[8.5px] font-normal">${formatDE(kpis.totalNetzeinspeisung)} kWh × ${formatDE(config.preisEinspeisung, 2)} €/kWh</span></td>
                        <td class="text-right font-bold align-top py-0.5">-${formatDE(Math.round(kpis.totalNetzeinspeisung * config.preisEinspeisung))} €</td>
                      </tr>
                      ` : ''}
                    </tbody>
                  </table>
                  <div class="mt-auto pt-1.5 border-t border-slate-300 flex justify-between font-black text-[13px]" style="color:${theme.primary}">
                    <span>Restkosten/Jahr</span>
                    <span>${formatDE(Math.round(finance.costNewTotal))} €</span>
                  </div>
                </div>
              </div>

              <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-auto flex flex-col">
                <div class="bg-white p-2.5 border-b border-slate-200">
                  <h4 class="font-bold text-[11px] flex items-center gap-2 mb-0.5" style="color:${theme.primary}">
                    <span class="w-4 h-4" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                    Prognose der Kostenentwicklung (Strom +${config.inflationRate}%/J., fossil +${config.inflationRateFossil ?? config.inflationRate}%/J., inkl. PV-Degradation)
                  </h4>
                  <p class="text-[9px] text-slate-500 leading-relaxed">
                    Diese Tabelle zeigt die Kraft der Kostenersparnis über die Zeit.
                  </p>
                </div>

                <div class="p-0">
                  ${(() => {
                    const pct1 = finance.costOldTotal > 0 ? Math.round(((finance.costOldTotal - finance.costNewTotal) / finance.costOldTotal) * 100) : 0;
                    const pct10 = finance.oldCostCumulative10 > 0 ? Math.round((finance.ersparnis10 / finance.oldCostCumulative10) * 100) : 0;
                    const pct20 = finance.oldCostCumulative20 > 0 ? Math.round((finance.ersparnis20 / finance.oldCostCumulative20) * 100) : 0;
                    const pct30 = finance.oldCostCumulative30 > 0 ? Math.round((finance.ersparnis30 / finance.oldCostCumulative30) * 100) : 0;
                    return `
                      <table class="w-full text-[9px] text-left">
                        <thead class="bg-white text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                          <tr>
                            <th class="p-2 font-semibold">Zeitraum</th>
                            <th class="p-2 font-semibold">Altsystem</th>
                            ${showMiddleStep ? `<th class="p-2 font-semibold">Elektrisch</th>` : ''}
                            <th class="p-2 font-black" style="color:${theme.primary}">${showMiddleStep ? 'Neusystem' : 'Neues System'}</th>
                            <th class="p-2 font-black" style="color:${theme.primary}">Ersparnis</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                          <tr>
                            <td class="p-2 font-bold">1. Jahr</td>
                            <td class="p-2 font-medium">${formatDE(Math.round(finance.costOldTotal))} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(Math.round(finance.costAllElectricBase))} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(Math.round(finance.costNewTotal))} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(Math.round(finance.costOldTotal - finance.costNewTotal))} € <span class="text-[11px]">(${pct1}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">10 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative10)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative10)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative10)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis10)} € <span class="text-[11px]">(${pct10}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">20 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative20)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative20)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative20)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis20)} € <span class="text-[11px]">(${pct20}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">30 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative30)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative30)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative30)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis30)} € <span class="text-[11px]">(${pct30}%)</span></td>
                          </tr>
                        </tbody>
                      </table>
                    `;
                })()}
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <!-- PAGE 2 -->
            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('1. AUSGANGSLAGE')}

              <h2 class="text-lg font-black mb-3" style="color:${theme.primary}">
                1. AUSGANGSLAGE & ENERGIE-TRANSFORMATION
              </h2>

              <div class="w-full mb-3">
                <h4 class="font-bold text-[11px] mb-1 uppercase tracking-wide" style="color:${theme.primary}">
                  Ihre System-Dimensionierung
                </h4>
                <p class="text-[9px] text-dark-600 leading-relaxed mb-2">
                  Die Systemauslegung orientiert sich punktgenau an Ihrem zukünftigen Gesamtstrombedarf. So bleibt Ihr Netzbezug minimal.
                </p>
              </div>

              <div class="w-full bg-white p-1 rounded-xl border border-slate-200 mb-3">
                <div class="flex items-stretch gap-1">

                  <!-- LEFT: Verbrauch + Chart -->
                  <div class="flex items-center gap-1 flex-1 min-w-0">
                    <div class="w-20 h-20 relative shrink-0">
                      <div class="chart-wrap">
                        <canvas id="bedarfsmixChart"></canvas>
                      </div>
                    </div>

                    <div class="flex-1 min-w-0 space-y-1.5 text-[9px]">
                      <div class="flex justify-between items-center gap-3">
                        <span class="flex items-center gap-1.5 font-medium text-dark-600 min-w-0">
                          <div class="w-2 h-2 rounded-full shrink-0" style="background:${theme.inactive}"></div>
                          <span class="truncate">Haushalt</span>
                        </span>
                        <span class="font-bold text-dark-600 text-right shrink-0">
                          ${formatDE(config.hhStrom)} kWh - ${formatDE(hhPercent, 1)}%
                        </span>
                      </div>

                      ${config.moduleWP ? `
                      <div class="flex justify-between items-center gap-3">
                        <span class="flex items-center gap-1.5 font-medium text-dark-600 min-w-0">
                          <div class="w-2 h-2 rounded-full shrink-0" style="background:${theme.secondary}"></div>
                          <span class="truncate">Wärmepumpe</span>
                        </span>
                        <span class="font-bold text-dark-600 text-right shrink-0">
                          ${formatDE(derivedParams.wpStrombedarf)} kWh - ${formatDE(wpPercent, 1)}%
                        </span>
                      </div>
                      ` : ''}

                      ${config.moduleWB && config.fahrleistung > 0 ? `
                      <div class="flex justify-between items-center gap-3">
                        <span class="flex items-center gap-1.5 font-medium text-dark-600 min-w-0">
                          <div class="w-2 h-2 rounded-full shrink-0" style="background:${theme.primary}"></div>
                          <span class="truncate">Auto</span>
                        </span>
                        <span class="font-bold text-dark-600 text-right shrink-0">
                          ${formatDE(derivedParams.evStrombedarf)} kWh - ${formatDE(evPercent, 1)}%
                        </span>
                      </div>
                      ` : ''}

                      <div class="mt-2 pt-2 border-t border-slate-200 flex justify-between items-center gap-3">
                        <span class="text-[11px] font-bold uppercase tracking-wide text-dark-600">
                          Gesamtverbrauch
                        </span>
                        <span class="text-[11px] font-black text-right shrink-0" style="color:${theme.primary}">
                          ${formatDE(bedarfTotal)} kWh · ${formatDE(percentSum, 1)}%
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- RIGHT: KPI values -->
                  <div class="shrink-0 w-[260px] border-l border-slate-200 pl-1 flex items-center">
                    <div class="grid grid-cols-3 gap-1 w-full">
                      ${config.moduleWP ? `
                      <div class="text-center">
                        <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Wärmepumpe</span>
                        <span class="font-black text-1xl leading-none" style="color:${theme.primary}">
                          ${derivedParams.wpLeistungKW} kW
                        </span>
                      </div>
                      ` : `<div class="text-center opacity-0 pointer-events-none">
                            <span class="font-semibold text-[9px] uppercase mb-1 block">-</span>
                            <span class="font-black text-1xl leading-none">-</span>
                          </div>`}

                      ${config.modulePV ? `
                      <div class="text-center border-l border-slate-200 pl-3">
                        <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Photovoltaik</span>
                        <span class="font-black text-1xl leading-none" style="color:${theme.primary}">
                          ${derivedParams.pvKwp} kWp
                        </span>
                      </div>

                      <div class="text-center border-l border-slate-200 pl-3">
                        <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Speicher</span>
                        <span class="font-black text-1xl leading-none" style="color:${theme.primary}">
                          ${derivedParams.batteryCapacity} kWh
                        </span>
                      </div>
                      ` : `
                      <div class="text-center opacity-0 pointer-events-none">
                        <span class="font-semibold text-[9px] uppercase mb-1 block">-</span>
                        <span class="font-black text-lg leading-none">-</span>
                      </div>
                      <div class="text-center opacity-0 pointer-events-none">
                        <span class="font-semibold text-[9px] uppercase mb-1 block">-</span>
                        <span class="font-black text-lg leading-none">-</span>
                      </div>
                      `}
                    </div>
                  </div>

                </div>
              </div>

              ${(config.moduleWP || config.moduleWB) ? `
              <div class="text-[11px] text-slate-400 mb-3 pt-2 border-t border-slate-200 leading-relaxed">
                <strong>Hinweis zu §14a EnWG:</strong> Mit Wärmepumpe oder Wallbox profitieren Sie von reduzierten Netzentgelten. Das System berechnet automatisch das günstigste Modell. In Ihrer neuen Anlage beträgt die Netzentgelt-Ersparnis ${finance.ersparnis14a} € pro Jahr.
              </div>
              ` : ''}

              <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-col mb-auto">
                <div class="flex items-end justify-between border-b border-slate-200 pb-1.5 mb-2">
                  <div>
                    <h3 class="text-[13px] font-black text-dark-600 uppercase tracking-[0.16em]">
                      Produktion vs. Verbrauch
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                      Überschüsse entstehen, wenn der Solarertrag höher als der Bedarf liegt.
                    </p>
                  </div>
                  <div class="text-[11px] font-bold px-2 py-0.5 rounded-full border"
                      style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                    Monatsvergleich
                  </div>
                </div>

                <div class="h-[300px] w-full mt-2">
                  <div class="chart-wrap">
                    <canvas id="monthlyCompareChart"></canvas>
                  </div>
                </div>

                <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-slate-100">
                  <div class="text-center px-2 py-2 rounded-lg" style="background:${theme.bgLight}">
                    <div class="text-[15px] font-black leading-none" style="color:${theme.primary}">${formatDE(kpis.totalPV)}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Solarertrag kWh/a</div>
                  </div>
                  <div class="text-center px-2 py-2 rounded-lg bg-slate-50">
                    <div class="text-[15px] font-black leading-none text-slate-700">${formatDE(kpis.totalBedarf)}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Gesamtbedarf kWh/a</div>
                  </div>
                  <div class="text-center px-2 py-2 rounded-lg bg-slate-50">
                    <div class="text-[15px] font-black leading-none text-slate-700">${formatDE(kpis.totalDirekt + kpis.totalBatterie)}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Selbst gedeckt kWh/a</div>
                  </div>
                  <div class="text-center px-2 py-2 rounded-lg bg-slate-50">
                    <div class="text-[15px] font-black leading-none text-slate-700">${formatDE(kpis.totalNetzeinspeisung)}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wide mt-1">Einspeisung kWh/a</div>
                  </div>
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('2. LÖSUNGSARCHITEKTUR')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">

               
                
                <h2 class="text-[17px] font-black mb-2 leading-tight" style="color:${theme.primary}">
                  2. SYSTEMAUSLEGUNG & UNABHÄNGIGKEIT
                </h2>

                <p class="text-[9px] text-dark-600 mb-3 leading-relaxed">
                  ${(config.modulePV && config.moduleWP)
                    ? 'Das Geheimnis maximaler Ersparnis liegt in der cleveren Sektorenkopplung: Haushalt, Wärme und Mobilität verschmelzen zu einem intelligenten Kreislauf. Ihr selbst produzierter Solarstrom wird direkt dorthin geleitet, wo er wirtschaftlich den größten Effekt erzielt.'
                    : 'Im Folgenden sehen Sie die detaillierte Systemauslegung, optimal dimensioniert für Ihren Bedarf und auf maximale Effizienz abgestimmt.'}
                </p>

                <h3 class="font-black text-[15px] mb-2.5" style="color:${theme.primary}">
                  Ihre Gesamtbilanz auf einen Blick
                </h3>

                <div class="grid grid-cols-3 gap-3 mb-3">
                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutAutarkie"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${kpis.autarkie}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Autarkiegrad
                      </h4>
                      <p class="text-[8.5px] text-dark-600 font-bold">Gesamte Bedarfsdeckung</p>
                    </div>

                    <div class="w-full text-[7.5px] text-dark-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="flex space-x-[-4px]">
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                          </div>
                          Deckung
                        </span>
                        <span class="font-bold" style="color:${theme.primary}">
                          ${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh
                        </span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Netzbezug
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(kpis.totalNetzbezug)} kWh</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutEigenverbrauch"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${kpis.eigenverbrauchQuote}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Eigenverbrauch
                      </h4>
                      <p class="text-[8.5px] text-dark-600 font-bold">Nutzung des Solar-Stroms</p>
                    </div>

                    <div class="w-full text-[7.5px] text-dark-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="flex space-x-[-4px]">
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                          </div>
                          Genutzt
                        </span>
                        <span class="font-bold" style="color:${theme.secondary}">
                          ${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh
                        </span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Einspeisung
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(kpis.totalNetzeinspeisung)} kWh</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutFinanz"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${finance.finUnabhProzent}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Finanz-Unabhängigkeit
                      </h4>
                      <p class="text-[8.5px] text-dark-600 font-bold">Schutz vor Preisanstieg</p>
                    </div>

                    <div class="w-full text-[7.5px] text-dark-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.primary}"></div>
                          Ersparnis
                        </span>
                        <span class="font-bold text-slate-700">+${formatDE(finance.ersparnisJahr1)} €</span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Vorher
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(Math.round(finance.costOldTotal))} €</span>
                      </div>
                    </div>
                  </div>
                </div>

                ${config.moduleWB ? `
                  <div class="mb-3 text-[8.5px] text-slate-500 uppercase tracking-[0.14em] text-center">
                    Inklusive intelligenter Lade-Infrastruktur für Elektromobilität
                  </div>
                ` : ''}

                <div class="mb-2.5">
                  <h2 class="text-[11px] font-black text-slate-400 mb-0.5 uppercase tracking-[0.16em]">
                    3. Saisonale Verteilung & Autarkie
                  </h2>
                  <h3 class="font-black text-[16px] uppercase tracking-wide leading-tight" style="color:${theme.primary}">
                    Der Verlauf über das Jahr
                  </h3>
                  <p class="text-[7.5px] text-slate-500 mt-1 leading-relaxed">
                    Die vier Jahreszeiten zeigen, wie stark Ihr System Lasten verschiebt, Eigenstrom direkt nutzt und den Netzbezug reduziert.
                  </p>
                </div>

                <div class="grid grid-cols-2 gap-3 flex-1">
                  ${seasonalData.map((season, i) => {
                        const total = season.Gesamtbedarf;
                        const pDeckung = season.autarkie;
                        const pZukauf = Math.max(0, 100 - pDeckung);
                        const pEinspeisung = season.Solarertrag > 0
                            ? Math.round((Math.abs(season.NetzeinspeisungNeg) / season.Solarertrag) * 100)
                            : 0;

                        return `
                      <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                        <div class="relative w-[94px] h-[94px] mb-2">
                          <div class="chart-wrap"><canvas id="seasonChart${i}"></canvas></div>
                          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span class="text-[16px] font-black text-slate-700">${pDeckung}%</span>
                          </div>
                        </div>

                        <div class="text-center mb-1.5">
                          <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                            ${season.name}
                          </h4>
                          <p class="text-[8.5px] text-dark-600 font-bold">Saisonale Deckung</p>
                        </div>

                        <div class="w-full text-[7.5px] text-dark-600 space-y-1 border-t border-slate-300 pt-1.5">
                          <div class="flex justify-between gap-2">
                            <span>Bedarf</span>
                            <span class="font-bold text-slate-700">${formatDE(total)} kWh</span>
                          </div>

                          <div class="flex justify-between gap-2">
                            <span class="flex items-center gap-1">
                              <div class="flex space-x-[-4px]">
                                <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                                <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                              </div>
                              Solar / Stromspeicher
                            </span>
                            <span class="font-bold" style="color:${theme.primary}">
                              ${formatDE(season.DirektDeckung + season.BatterieDeckung)} kWh
                            </span>
                          </div>

                          <div class="flex justify-between gap-2">
                            <span class="flex items-center gap-1">
                              <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                              Netzbezug
                            </span>
                            <span class="font-bold text-slate-700">
                              ${formatDE(season.Netzbezug)} kWh (${pZukauf}%)
                            </span>
                          </div>

                          <div class="flex justify-between gap-2 pt-1 mt-0.5 border-t border-slate-200" style="color:${theme.secondary}">
                            <span>Einspeisung</span>
                            <span class="font-bold">
                              ${formatDE(Math.abs(season.NetzeinspeisungNeg))} kWh (${pEinspeisung}%*)
                            </span>
                          </div>
                        </div>
                      </div>
                    `;
                    }).join('')}
                </div>

                <div class="text-[6px] text-center text-slate-400 font-medium mt-2">
                  * Der Prozentwert der Einspeisung bezieht sich auf den gesamten Solarertrag der jeweiligen Jahreszeit.
                </div>
              </div>

              ${ReportFooter()}
            </div>
 

            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('4. IHR PARTNER FÜR DIE ENERGIEWENDE')}

              <h2 class="text-[16px] font-black mb-3 leading-tight" style="color:${theme.primary}">
                4. ${theme.name} & IHRE TECHNOLOGIE-VORTEILE
              </h2>

              <h3 class="text-[11px] font-bold text-dark-600 mb-2 border-b border-slate-200 pb-1">
                Darum ${theme.name} – Ihr Partner für die Energiewende
              </h3>

              <div class="grid grid-cols-2 gap-2 mb-3">
                ${[
                    ['award', 'Meisterbetrieb SHK & Elektro', 'Höchste handwerkliche Präzision durch unsere gewerkeübergreifende Meisterkompetenz.'],
                    ['shieldCheck', 'Alles aus einer Hand', 'Ein einziger, verlässlicher Ansprechpartner für Beratung, Planung, Fördermittelservice und Installation.'],
                    ['star', 'Premium Produktqualität', 'Wir verbauen ausschließlich marktführende, langlebige und erprobte Komponenten.'],
                    ['wrench', 'Langjährige Erfahrung', 'Hunderte erfolgreich realisierte Projekte und tiefes technisches Know-how.']
                ].map(item => `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 min-h-[88px]">
                    <span class="w-5 h-5 shrink-0 mt-0.5" style="color:${theme.primary}">
                      ${Icons[item[0]]()}
                    </span>
                    <div>
                      <h4 class="font-bold text-dark-600 text-[13px] mb-1">${item[1]}</h4>
                      <p class="text-[11px] text-dark-600 leading-relaxed">${item[2]}</p>
                    </div>
                  </div>
                `).join('')}
              </div>

              <h3 class="text-[11px] font-bold text-dark-600 mb-2 border-b border-slate-200 pb-1">
                Die Bausteine Ihres intelligenten Systems
              </h3>

              <div class="space-y-2 flex-1">
                ${config.moduleWP ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-dark-600 text-[13px] mb-0.5">Wärmepumpe (Heizen & Kühlen)</h4>
                      <p class="text-[11px] text-dark-600 leading-relaxed">Nutzt kostenlose Umweltenergie hochgradig effizient.</p>
                    </div>
                  </div>
                ` : ''}

                ${config.modulePV ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.sun()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-dark-600 text-[13px] mb-0.5">Photovoltaik & Batteriespeicher</h4>
                      <p class="text-[11px] text-dark-600 leading-relaxed">Macht Ihr Dach zum eigenen Kraftwerk und speichert Sonnenstrom für die Nacht.</p>
                    </div>
                  </div>
                ` : ''}

                ${config.moduleWB ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.car()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-dark-600 text-[13px] mb-0.5">E-Mobilität (Wallbox)</h4>
                      <p class="text-[11px] text-dark-600 leading-relaxed">Ihre private Tankstelle direkt vor der Tür. Tanken Sie Ihr E-Auto bequem zu Hause.</p>
                    </div>
                  </div>
                ` : ''}

                ${activeModulesCount > 1 ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.network()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-dark-600 text-[13px] mb-0.5">Intelligente Sektorenkopplung</h4>
                      <p class="text-[11px] text-dark-600 leading-relaxed">Strom, Wärme und Mobilität werden intelligent vernetzt, damit Ihr Eigenverbrauch maximiert wird.</p>
                    </div>
                  </div>
                ` : ''}
              </div>

              ${ReportFooter()}
            </div>

            ${config.modulePV ? `
                <div class="a4-page flex flex-col bg-white relative overflow-hidden">
                  ${ReportHeader('5. PHOTOVOLTAIK & SPEICHER')}

                  <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      5. PHOTOVOLTAIK & BATTERIESPEICHER
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[19px] font-black mb-1.5 leading-tight" style="color:${theme.primary}">
                        Für alle, die die Sonne optimal nutzen wollen.
                      </h3>
                      <p class="text-[10.5px] text-dark-600 leading-relaxed bg-white px-4 py-3 rounded-xl border border-slate-200">
                        <strong class="text-slate-700 block mb-0.5">Eine schlaue Entscheidung:</strong>
                        Solarstrom zum Eigenverbrauch ist die Energielösung der Zukunft. Mit einer Photovoltaikanlage nutzen Sie den erzeugten Strom tagsüber direkt. Unsere Systeme können durch einen Batteriespeicher sinnvoll ergänzt werden, um die Eigenversorgung weiter zu steigern.
                      </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="rounded-xl p-4 border bg-white flex flex-col" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.battery()}</span>
                        </div>

                        <h3 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Maximaler Nutzen durch Batteriespeicher und Energiemanagement
                        </h3>

                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          <strong class="text-slate-700 block mb-0.5">Effizienter geht’s nicht:</strong>
                          In Kombination mit einem Batteriespeicher wird ein größerer Anteil Ihres Solarstroms direkt im eigenen Haus genutzt. Das steigert den Eigenverbrauch und reduziert die Abhängigkeit vom öffentlichen Netz deutlich.
                        </p>

                        <p class="text-[10px] text-dark-600 leading-relaxed mt-1.5">
                          So entsteht ein modernes Energiesystem, das Erzeugung, Verbrauch und Speicherung intelligent miteinander verbindet.
                        </p>
                      </div>

                      <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.sun()}</span>
                        </div>

                        <h3 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug uppercase tracking-[0.05em]">
                          Mehr Eigenverbrauch bringt mehr Unabhängigkeit
                        </h3>

                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          <strong class="text-slate-700 block mb-0.5">Selbst produziert für die eigene Steckdose:</strong>
                          Der Eigenverbrauch zeigt, wie viel Ihres Strombedarfs direkt durch selbst erzeugten Solarstrom gedeckt wird. Er hängt von Haushaltsgröße, Geräten und Nutzungsgewohnheiten ab.
                        </p>

                        <p class="text-[10px] text-dark-600 leading-relaxed mt-1.5">
                          Die Strommenge Ihrer PV-Anlage wird durch Leistung, Dachausrichtung, Komponentenqualität, Montage, Standort und Wetter beeinflusst.
                        </p>
                      </div>
                    </div>

                    <div class="mt-auto">
                      <div class="rounded-xl p-4 border relative overflow-hidden bg-white" style="border-color:${theme.primary}">
                        <div class="absolute top-0 left-0 w-24 h-24 rounded-full blur-[40px] opacity-20" style="background:${theme.primary}"></div>

                        <div class="relative z-10">
                          <h4 class="font-bold text-[11px] text-slate-700 mb-1.5 uppercase tracking-[0.08em]">
                            Ihr individuelles Dachpotenzial
                          </h4>

                          <p class="text-[10.2px] text-slate-700 leading-relaxed font-medium">
                            ${derivedParams.isEastWestProfile
                        ? `<span>Mit einer <strong style="color:${theme.primary}">Ost-West-Belegung</strong> profitieren Sie von einem breiten Erzeugungsprofil. Der Solarstrom steht morgens früher und abends länger zur Verfügung. Dadurch sinkt der Bedarf an später Batterieleistung und Ihr direkter Eigenverbrauch steigt spürbar.</span>`
                        : derivedParams.hasSued
                            ? `<span>Mit einer starken Ausrichtung nach <strong style="color:${theme.primary}">Süden</strong> erzielen Sie hohe Stromerträge rund um die Mittagszeit. Diese Überschüsse werden gezielt im Batteriespeicher gepuffert, damit Ihr Haus auch in den Abend- und Nachtstunden möglichst lange mit eigenem Strom versorgt wird.</span>`
                            : `<span>Durch die optimale Belegung der verfügbaren Dachflächen holen wir das Beste aus der Sonne für Sie heraus. ${theme.name} berechnet die Modulverteilung so, dass Ihr Speicher auch in schwächeren Zeiten möglichst effektiv geladen wird.</span>`
                    }
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

            ${config.moduleWP ? `
                <div class="a4-page flex flex-col bg-white relative">
                  ${ReportHeader('WÄRMEPUMPEN-TECHNOLOGIE')}

                  <div class="flex-1 pb-[22mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      DAS PERFEKTE TEAM: INTELLIGENTE AUTARKIE
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[18px] font-black mb-2 leading-tight" style="color:${theme.primary}">
                        Ein perfektes Team: Wärmepumpen ${config.modulePV ? 'mit Solarstrom versorgen und ' : ''}Heizkosten minimieren.
                      </h3>

                      <div class="text-[10px] text-dark-600 leading-relaxed space-y-1.5">
                        <p>
                          Die optimale Nutzung der Umweltenergie wird durch die Wärmepumpe realisiert. Sie gewinnt Wärme aus Luft oder Boden, um Gebäude zu heizen und Trinkwasser zu erwärmen. Für den Betrieb benötigt sie elektrischen Strom.
                        </p>
                        ${config.modulePV ? `
                          <p>
                            Wird dieser Strom durch die eigene Photovoltaikanlage erzeugt und zusätzlich gespeichert, lässt sich ein Einfamilienhaus in weiten Teilen energieautark versorgen.
                          </p>
                        ` : ''}
                        <p>
                          Eine Wärmepumpe macht unabhängig von fossilen Brennstoffen und trägt aktiv zur Reduzierung des CO₂-Ausstoßes bei.
                        </p>
                        <p class="font-bold px-3 py-2.5 rounded-xl border border-slate-200" style="color:${theme.primary}">
                          Wir zeigen Ihnen, wie leicht man Öl, Gas und Kohle in den Schatten stellen kann – effizient, modern und alltagstauglich mit Wärmepumpensystemen aus dem Hause ${theme.name}.
                        </p>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col">
                        <div class="mb-2 p-2 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.piggyBank()}</span>
                        </div>
                        <h3 class="text-[14px] font-black text-slate-700 mb-1 leading-snug">
                          Wie Sie mit effizienter Technik sparen können
                        </h3>
                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          Eine Wärmepumpe produziert einen Großteil der Energie aus der Umgebungsluft. Für den elektrischen Antrieb wird nur ein vergleichsweise kleiner Anteil Strom benötigt.
                        </p>
                        <p class="text-[10px] text-dark-600 leading-relaxed mt-1">
                          Die hocheffiziente Nutzung dieser Technologie bringt ökologische und wirtschaftliche Vorteile. Sie senken Ihre laufenden Betriebskosten und machen sich langfristig unabhängiger von steigenden Energiepreisen.
                        </p>
                      </div>

                      <div class="rounded-xl p-3.5 border flex flex-col" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.cpu()}</span>
                        </div>
                        <h3 class="text-[14px] font-black text-slate-700 mb-1 leading-snug">
                          Wärmepumpen sind intelligente Heizsysteme
                        </h3>
                        <div class="text-[8px] font-bold tracking-[0.12em] uppercase mb-1.5" style="color:${theme.primary}">
                          Diese Systemeffizienz nennen wir intelligente Autarkie
                        </div>
                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          Eine Wärmepumpe kann mehr als nur umweltfreundlich heizen und Warmwasser bereiten. Je nach Technologie und Ausführung sind zusätzliche Funktionen wie Kühlen oder Lüften möglich.
                        </p>
                        <p class="text-[10px] text-dark-600 leading-relaxed mt-1">
                          So machen Sie Ihr Objekt zukunftssicher – mit einer Technik, die Komfort, Effizienz und Nachhaltigkeit verbindet.
                        </p>
                      </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col gap-2.5">
                      <h3 class="font-bold text-[11px] text-slate-700 border-b border-slate-100 pb-1.5 flex items-center gap-2">
                        <span class="w-4 h-4" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                        Witterung & saisonale Verteilung am Standort ${config.plz}
                      </h3>

                      <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                          <div>
                            <h4 class="text-[10px] font-bold text-slate-700 mb-0.5">
                              Normaußentemperatur (NAT: ${derivedParams.klima.nat} °C)
                            </h4>
                            <p class="text-[8.5px] text-dark-600 leading-relaxed">
                              Die NAT ist die tiefste Temperatur, die an Ihrem Wohnort an zwei aufeinanderfolgenden Tagen zu erwarten ist. Sie ist ein zentraler Wert für die sichere Auslegung Ihrer Wärmepumpe.
                            </p>
                          </div>

                          <div>
                            <h4 class="text-[10px] font-bold text-slate-700 mb-0.5">
                              Heizgradtage (HGT: ${derivedParams.klima.hgt} Kd)
                            </h4>
                            <p class="text-[8.5px] text-dark-600 leading-relaxed">
                              Dieser Wert beschreibt, wie streng der Winter an Ihrem Standort ausfällt. Er hilft dabei, den tatsächlichen Energiebedarf regional realistisch einzuordnen.
                            </p>
                          </div>
                        </div>

                        <div class="flex flex-col justify-center">
                          <div class="flex justify-between items-end mb-1">
                            <span class="text-[10px] font-bold text-slate-700">
                              Verteilung Heizbedarf/Jahr
                            </span>
                            <span class="text-[8px] font-medium text-slate-500">
                              ${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus))} kWh
                            </span>
                          </div>

                          ${(() => {
                        // Saisonverteilung aus den echten Monats-Gradtaganteilen
                        // (PVGIS-TMY wenn geladen, sonst Pauschalprofil)
                        const a = derivedParams.wpMonatsAnteile || HGT_DISTRIBUTION;
                        const saisons = [
                            { name: 'Winter', wert: a[11] + a[0] + a[1], farbe: '#cbd5e1' },
                            { name: 'Frühling', wert: a[2] + a[3] + a[4], farbe: theme.secondary },
                            { name: 'Sommer', wert: a[5] + a[6] + a[7], farbe: theme.primary },
                            { name: 'Herbst', wert: a[8] + a[9] + a[10], farbe: '#64748b' }
                        ];
                        return `
                              <div class="flex w-full h-3.5 rounded-full overflow-hidden shadow-inner mb-1">
                                ${saisons.map(s => `
                                  <div class="flex items-center justify-center text-[8.5px] text-white font-bold"
                                    style="width:${(s.wert * 100).toFixed(1)}%;background:${s.farbe}">
                                    ${s.wert >= 0.07 ? Math.round(s.wert * 100) + '%' : ''}
                                  </div>
                                `).join('')}
                              </div>

                              <div class="grid grid-cols-4 gap-1 text-[8.5px] text-dark-600 font-medium">
                                ${saisons.map(s => `
                                  <div class="text-center">
                                    <span class="block">${s.name}</span>
                                    <span class="opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * s.wert))} kWh</span>
                                  </div>
                                `).join('')}
                              </div>
                            `;
                    })()}
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

            ${config.moduleWB ? `
                <div class="a4-page flex flex-col bg-white relative overflow-hidden">
                  ${ReportHeader('INTELLIGENTE E-MOBILITÄT')}

                  <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      E-MOBILITÄT: ZAPFEN SIE DIE SONNE AN
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[19px] font-black mb-1.5 leading-tight uppercase" style="color:${theme.primary}">
                        Die Zukunft fährt elektrisch
                      </h3>

                      <p class="text-[10.5px] text-dark-600 leading-relaxed bg-white px-4 py-3 rounded-xl border border-slate-200">
                        <strong class="text-slate-700 block mb-0.5">
                          Wie Sie die Energie ${config.modulePV ? 'der Sonne sogar ' : ''}auf die Straße bringen
                        </strong>
                        Mit einer Ladestation für Elektroautos wird moderne Mobilität Teil Ihres Gesamtsystems. Wir planen Ihre Wallbox passend zu Ihrem Bedarf – idealerweise als komfortable „Zapfsäule“ für den eigenen ${config.modulePV ? 'Solarstrom' : 'Hausstrom'}.
                      </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="rounded-xl p-4 border flex flex-col bg-white">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.leaf()}</span>
                        </div>

                        <h4 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Ökologisch & wirtschaftlich
                        </h4>

                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          Elektromobilität verbindet Klimaschutz mit Komfort. Wer zu Hause lädt, nutzt Energie bewusster und macht einen wichtigen Schritt in Richtung einer sauberen, CO₂-armen Zukunft.
                        </p>

                        <p class="text-[10px] text-dark-600 leading-relaxed mt-1.5">
                          Gleichzeitig laden Sie Ihr Fahrzeug bequem am eigenen Standort. Das ist alltagstauglich, spart Wege zu öffentlichen Ladesäulen und macht Ihr Zuhause zum eigenen Energiepunkt.
                        </p>
                      </div>

                      <div class="rounded-xl p-4 border flex flex-col bg-white" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                        </div>

                        <h4 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Rentabilität maximieren
                        </h4>

                        <p class="text-[10px] text-dark-600 leading-relaxed">
                          Mit einem Elektroauto erhöhen Sie den wirtschaftlichen Nutzen Ihres Gesamtsystems. Anstelle schwankender Kraftstoffpreise laden Sie Ihr Fahrzeug günstig über Ihre eigene Ladeinfrastruktur.
                        </p>

                        ${config.modulePV ? `
                          <div class="mt-2 px-3 py-2 rounded-lg text-[9.5px] font-bold leading-relaxed" style="background:${theme.primary};color:white">
                            Besonders effizient: Das Fahrzeug wird bevorzugt mit überschüssigem Sonnenstrom geladen.
                          </div>
                        ` : `
                          <p class="text-[10px] text-dark-600 leading-relaxed mt-1.5">
                            Schon Ohne Solar schafft eine eigene Wallbox mehr Komfort, mehr Kontrolle und eine verlässliche Grundlage für zukünftige Mobilität.
                          </p>
                        `}
                      </div>
                    </div>

                    <div class="mt-auto">
                      <div class="rounded-xl p-4 border flex gap-4 items-start relative overflow-hidden bg-white" style="border-color:${theme.primary}">
                        <div class="absolute top-0 right-0 w-24 h-24 rounded-full blur-[40px] opacity-20" style="background:${theme.primary}"></div>

                        <div class="p-2.5 rounded-full shrink-0 relative z-10" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.car()}</span>
                        </div>

                        <div class="relative z-10">
                          <h4 class="font-bold text-[11px] text-slate-700 mb-1.5 uppercase tracking-[0.08em]">
                            Intelligentes Laden für den Alltag
                          </h4>

                          <p class="text-[10.2px] text-slate-700 leading-relaxed font-medium">
                            <strong style="color:${theme.primary}">
                              Mit intelligenten Ladelösungen, durchdachter Vernetzung und effizienter Steuerung
                            </strong>
                            entsteht eine komfortable, wirtschaftliche und zukunftssichere Ladeumgebung direkt bei Ihnen zu Hause.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

           ${activeModulesCount > 1 ? `
              <div class="a4-page flex flex-col bg-white overflow-hidden relative">
                ${ReportHeader('DAS GESAMTSYSTEM')}

                <div class="flex-1 flex flex-col justify-between min-h-0 pb-[18mm]">
                  <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                    SEKTORENKOPPLUNG: EIGENE ENERGIE
                  </h2>

                  <div class="mb-4">
                    <h3 class="text-[24px] font-black leading-tight" style="color:${theme.primary}">
                      Eigener Strom.
                    </h3>
                    <h3 class="text-[24px] font-black leading-tight" style="color:${theme.inactive}">
                      Eigene Wärme.
                    </h3>
                    <h3 class="text-[24px] font-black mb-2 leading-tight" style="color:${theme.secondary}">
                      Eigene Energie.
                    </h3>

                    <p class="text-[10.5px] text-dark-600 leading-relaxed font-medium bg-white px-4 py-3 rounded-xl border border-slate-200">
                      Ganz autark sein, frei von teuren Energieanbietern und dabei ohne Komfortverlust:
                      Durch die intelligente Kombination Ihrer Systeme entsteht ein persönliches
                      Energie-Effizienzhaus mit klarer Struktur, hoher Eigenversorgung und dauerhaft mehr Kontrolle.
                    </p>
                  </div>

                  <div class="grid grid-cols-1 gap-3 mb-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                      <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                        <span class="w-5 h-5" style="color:${theme.primary}">${Icons.network()}</span>
                      </div>

                      <h4 class="text-[14px] font-black text-slate-700 mb-1.5">
                        Synergien nutzen & Kosten minimieren
                      </h4>

                      <p class="text-[10px] text-dark-600 leading-relaxed">
                        Die ganzheitliche Verbindung von Strom, Wärme und Mobilität hebt die Gesamtenergieeffizienz deutlich an.
                        Energie wird dort eingesetzt, wo sie den größten Nutzen bringt – wirtschaftlich, nachhaltig und technisch sauber abgestimmt.
                      </p>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                      <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                        <span class="w-5 h-5" style="color:${theme.primary}">${Icons.sun()}</span>
                      </div>

                      <h4 class="text-[14px] font-black text-slate-700 mb-1.5">
                        Das ideale Zusammenspiel
                      </h4>

                      <p class="text-[10px] text-dark-600 leading-relaxed">
                        Das abgestimmte Gesamtsystem steuert Energieflüsse exakt dorthin, wo sie im Alltag gebraucht werden.
                        So werden Eigenverbrauch, Komfort und Unabhängigkeit gleichzeitig verbessert.
                      </p>
                    </div>
                  </div>

                  <div class="mt-auto">
                    <div class="rounded-xl px-5 py-4 border relative overflow-hidden bg-white" style="border-color:${theme.secondary}50">
                      <div class="absolute -right-6 -top-6 opacity-10">
                        <span class="w-[150px] h-[150px] inline-block" style="color:${theme.primary}">
                          ${Icons.infinity()}
                        </span>
                      </div>

                      <div class="relative z-10">
                        <h4 class="text-[14px] font-black mb-1.5" style="color:${theme.primary}">
                          Sektorkopplung funktioniert wie ein starkes Team
                        </h4>
                        <p class="text-[10px] text-slate-700 leading-relaxed">
                          Erst wenn alle Zahnräder präzise ineinandergreifen, entsteht das beste Ergebnis für Ihr Zuhause:
                          mehr Eigenverbrauch, weniger externe Energiebezüge und ein System, das wirtschaftlich wie technisch aus einem Guss arbeitet.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                ${ReportFooter()}
              </div>
            ` : ''}

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('WIRTSCHAFTLICHKEIT')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black mb-3 text-[${theme.primary}] uppercase tracking-[0.14em]">
                  INVESTITION & WIRTSCHAFTLICHKEIT (BREAK-EVEN)
                </h2>

                <div class="bg-white rounded-xl border border-slate-200 mb-2.5 overflow-hidden shrink-0">
                  <div class="px-4 py-2 border-b border-slate-100 bg-white flex justify-between items-center">
                    <h3 class="text-[11px] font-bold text-dark-600 uppercase tracking-[0.12em]">
                      Ihre Netto-Investition im Detail
                    </h3>
                  </div>

                  <div class="p-0 overflow-hidden">
                    <table class="w-full text-[8px] text-left leading-tight">
                      <thead class="bg-white text-slate-500 text-[8px] uppercase tracking-[0.1em] border-b border-slate-100">
                        <tr>
                          <th class="px-3 py-1.5 pl-4 font-semibold">Komponente</th>
                          <th class="px-2 py-1.5 font-semibold text-right">Brutto</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">KfW</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">Zusatz</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">Rabatt*</th>
                          <th class="px-3 py-1.5 pr-4 font-black text-right text-slate-700">Netto</th>
                        </tr>
                      </thead>

                      <tbody class="divide-y divide-slate-100 text-slate-700">
                        ${config.moduleWP ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Wärmepumpe</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costWP)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.kfwZuschuss)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantWPNum)} €
                            ${config.extraGrantSourceWP ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceWP}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountWPNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoWP)} €</td>
                        </tr>
                        ` : ''}

                        ${config.modulePV ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Photovoltaik (${derivedParams.pvKwp} kWp)</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costPV)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantPVNum)} €
                            ${config.extraGrantSourcePV ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourcePV}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountPVNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoPV)} €</td>
                        </tr>

                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Speicher (${derivedParams.batteryCapacity} kWh)</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costBattery)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantBatteryNum)} €
                            ${config.extraGrantSourceBattery ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceBattery}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountBatteryNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoBattery)} €</td>
                        </tr>
                        ` : ''}

                        ${config.moduleWB ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Wallbox</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costWallbox)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantWallboxNum)} €
                            ${config.extraGrantSourceWallbox ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceWallbox}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountWallboxNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoWallbox)} €</td>
                        </tr>
                        ` : ''}
                      </tbody>

                      <tfoot class="bg-white font-black text-[10px] border-t-2 border-slate-200">
                        <tr>
                          <td class="px-3 py-1.5 pl-4">Gesamtinvestition</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(finance.totalInvest)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.kfwZuschuss)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.totalExtraGrant)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.totalDiscount)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right" style="color:${theme.primary}">${formatDE(finance.nettoInvest)} €</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <div class="text-[8.5px] text-slate-500 mb-2.5 leading-relaxed shrink-0">
                 <strong>Bonus Bedingung:</strong>
                    Die ausgewiesenen ${theme.name} Rabatte gelten ausschließlich für die im Angebot gemeinsam beauftragten Systemkomponenten. Maßgeblich sind Angebot und Auftragsbestätigung. Bei Einzelbeauftragungen oder Änderungen des Leistungsumfangs können Sonderkonditionen entfallen.
                    ${finance.totalDiscount > 0
                    ? `<span class="font-bold" style="color:${theme.primary}"> In Ihrer aktuellen Konfiguration ist ein Preisvorteil aktiv.</span>`
                    : `<span class="text-red-500 font-bold"> In Ihrer aktuellen Konfiguration ist derzeit kein Preisvorteil aktiv.</span>`
                }
                </div>

                ${(config.moduleWP && finance.kfwZuschuss > 0) ? `
                  <div class="text-[8.5px] text-slate-500 mb-2.5 leading-relaxed shrink-0">
                    <strong>Förderhinweis (KfW 458):</strong>
                    Der ausgewiesene Zuschuss ist eine Prognose nach Wohneinheiten-Staffelung. Klimageschwindigkeits-Bonus (20&nbsp;%)
                    und Einkommens-Bonus (30&nbsp;%, Haushaltseinkommen &lt; 40.000&nbsp;€) gelten je <strong>selbstnutzendem Eigentümer</strong>
                    für dessen Wohneinheit; der Effizienz-Bonus (5&nbsp;%) setzt u.&nbsp;a. ein natürliches Kältemittel voraus.
                    Maßgeblich ist die Förderzusage der KfW – Antragstellung muss vor Vorhabensbeginn erfolgen.
                  </div>
                ` : ''}

                <div class="bg-white px-4 py-3 rounded-xl border border-slate-200 mb-2.5 shrink-0">
                  <h3 class="text-[10px] font-bold text-slate-700 mb-1">
                    Ihr finanzieller Break-Even
                  </h3>
                  <p class="text-[8.5px] text-slate-500 mb-2 leading-relaxed">
                    Die farbige Linie zeigt Ihre Kosten im neuen System. Die graue Linie zeigt das heutige System mit Inflation.
                    Der Schnittpunkt markiert den <strong>Break-Even-Point</strong>.
                  </p>

                  <div class="h-[235px] w-full">
                    <div class="chart-wrap"><canvas id="financeLineChart"></canvas></div>
                  </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-auto shrink-0">
                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${finance.amortisationYear ? `${finance.amortisationYear} J.` : '&gt; 30 J.'}
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Amortisation
                    </div>
                    ${amortBand.min !== null && amortBand.max > amortBand.min ? `
                      <div class="text-[7.5px] text-slate-400 mt-0.5">Bandbreite ${amortBand.min}–${amortBand.max} J. je Preisentwicklung</div>
                    ` : ''}
                  </div>

                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${finance.roi}%
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Rendite/Jahr
                    </div>
                  </div>

                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white relative overflow-hidden flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${config.modulePV ? finance.lcoe : '-'} €
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Stromgestehungskosten / kWh
                    </div>
                    <div class="absolute top-0 right-0 text-[6px] font-bold px-1.5 py-0.5 rounded-bl-lg" style="background:${theme.bgLight};color:${theme.primary}">
                      Basis: ${ASSUMPTIONS.finance.lcoeYears} Jahre
                    </div>
                  </div>
                </div>
              </div>

              ${ReportFooter()}
            </div>

            ${computed.vermieter ? `
            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('VERMIETER-PERSPEKTIVE')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black text-[${theme.primary}] mb-1 uppercase tracking-[0.14em]">
                  Ihre Rendite als Eigentümer
                </h2>
                <p class="text-[9.5px] text-slate-500 mb-3 leading-relaxed">
                  Sie bewohnen ${computed.vermieter.E} von ${computed.vermieter.N} Wohneinheiten selbst und vermieten ${computed.vermieter.V}.
                  Die Maßnahme refinanziert sich aus Ihrer eigenen Energieersparnis plus
                  ${computed.vermieter.contractingAktiv ? 'Wärmelieferung an Ihre Mieter (§ 556c BGB)' : (computed.vermieter.umlage > 0 ? 'Modernisierungsumlage (§ 559e BGB)' : 'den Erlösen der Anlage')}${computed.vermieter.mieterstromErloes > 0 ? ' und Mieterstrom' : ''}.
                </p>

                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-3">
                  <div class="px-4 py-2 border-b border-slate-200 text-[10px] font-black uppercase tracking-[0.14em] text-slate-600">Jährlicher Cashflow aus Eigentümersicht (Jahr 1)</div>
                  <table class="w-full text-[9px] text-slate-700">
                    <tbody>
                      ${computed.vermieter.waermeErloes > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Wärmeerlöse Mieter (${formatDE(computed.vermieter.waermeVermietet)} kWh × ${formatDE(computed.vermieter.arbeitspreis * 100, 2)} ct + Grundpreis)</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.waermeErloes)} €</td></tr>` : ''}
                      ${computed.vermieter.umlage > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Modernisierungsumlage § 559e BGB${computed.vermieter.umlageGekappt ? ' (Kappgrenze 0,50 €/m² greift)' : ''}</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.umlage)} €</td></tr>` : ''}
                      ${computed.vermieter.mieterstromErloes > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Mieterstrom (${formatDE(computed.vermieter.mieterPvLieferung)} kWh PV-Lieferung × ${formatDE(computed.vermieter.msPreis * 100, 1)} ct${computed.vermieter.zuschlagSatz > 0 ? ` + ${formatDE(computed.vermieter.zuschlagSatz * 100, 2)} ct Zuschlag` : ''})</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.mieterstromErloes)} €</td></tr>` : ''}
                      ${computed.vermieter.einspeiseErloes > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Einspeisevergütung Rest (${formatDE(computed.vermieter.resteinspeisung)} kWh)</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.einspeiseErloes)} €</td></tr>` : ''}
                      ${computed.vermieter.eigeneHeizAlt > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Entfallene eigene Heizkosten (${computed.vermieter.E} selbstbewohnte WE)</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.eigeneHeizAlt)} €</td></tr>` : ''}
                      ${computed.vermieter.eigenerStromVorteil > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Vermiedener Strombezug eigener Haushalt (PV-Deckung)</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.eigenerStromVorteil)} €</td></tr>` : ''}
                      ${computed.vermieter.evVorteil > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Vorteil E-Mobilität</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.evVorteil)} €</td></tr>` : ''}
                      ${computed.vermieter.co2Ersparnis > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Entfallener CO₂-Vermieteranteil (CO2KostAufG: ${formatDE(computed.vermieter.co2KgProM2, 1)} kg/m²a → ${computed.vermieter.co2VermieterProzent} % Vermieter, ${formatDE(computed.vermieter.co2Preis)} €/t)</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.co2Ersparnis)} €</td></tr>` : ''}
                      ${computed.vermieter.ersparnis14a > 0 ? `
                        <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Reduzierte Netzentgelte § 14a EnWG</td><td class="px-4 py-1.5 text-right font-bold" style="color:${theme.primary}">+ ${formatDE(computed.vermieter.ersparnis14a)} €</td></tr>` : ''}
                      <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Strombezug Wärmepumpe (zentral, Netzanteil)</td><td class="px-4 py-1.5 text-right font-bold text-red-500">− ${formatDE(computed.vermieter.wpStromKosten)} €</td></tr>
                      <tr class="border-b border-slate-100"><td class="px-4 py-1.5">Wartungsdifferenz Neu- vs. Altsystem</td><td class="px-4 py-1.5 text-right font-bold ${computed.vermieter.wartungDiff >= 0 ? 'text-red-500' : ''}" ${computed.vermieter.wartungDiff < 0 ? `style="color:${theme.primary}"` : ''}>${computed.vermieter.wartungDiff >= 0 ? '−' : '+'} ${formatDE(Math.abs(computed.vermieter.wartungDiff))} €</td></tr>
                    </tbody>
                    <tfoot>
                      <tr class="bg-slate-50 font-black"><td class="px-4 py-2">Cashflow Jahr 1</td><td class="px-4 py-2 text-right" style="color:${theme.primary}">${formatDE(computed.vermieter.cashflowJahr1)} €</td></tr>
                    </tfoot>
                  </table>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-3">
                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">${formatDE(computed.vermieter.rendite, 1)} %</div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">Rendite auf Netto-Investition</div>
                  </div>
                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">${computed.vermieter.amortisationStatisch ? computed.vermieter.amortisationStatisch + ' J.' : '–'}</div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">Amortisation (statisch)</div>
                  </div>
                  <div class="text-center px-3 py-2.5 border ${computed.vermieter.kostenneutral ? 'border-slate-200' : 'border-amber-300 bg-amber-50'} rounded-xl bg-white">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${computed.vermieter.kostenneutral ? theme.primary : '#d97706'}">${computed.vermieter.kostenneutral ? '✓' : '!'}</div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">Kostenneutral für Mieter</div>
                  </div>
                </div>

                ${computed.vermieter.contractingAktiv ? `
                  <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 mb-3">
                    <div class="text-[10px] font-black uppercase tracking-[0.14em] text-slate-600 mb-1.5">Kostenneutralitäts-Nachweis (§ 556c BGB / WärmeLV)</div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-[9px] text-slate-700">
                      <div class="flex justify-between border-b border-slate-100 pb-1"><span>Bisherige Heizkosten der Mieter:</span><strong>${formatDE(computed.vermieter.alteMieterHeizkosten)} €/a</strong></div>
                      <div class="flex justify-between border-b border-slate-100 pb-1"><span>Künftige Wärmekosten der Mieter:</span><strong style="color:${computed.vermieter.kostenneutral ? theme.primary : '#d97706'}">${formatDE(computed.vermieter.mieterWaermeKostenNeu)} €/a</strong></div>
                    </div>
                    <p class="text-[8.5px] text-slate-500 mt-1.5">${computed.vermieter.kostenneutral
                            ? 'Die Umstellung auf Wärmelieferung ist kostenneutral – Ihre Mieter zahlen nicht mehr als bisher.'
                            : 'Achtung: Die Wärmelieferkosten übersteigen die bisherigen Betriebskosten – die Umlagefähigkeit nach § 556c BGB ist so nicht gegeben. Arbeits- oder Grundpreis senken.'}</p>
                  </div>
                ` : ''}

                <div class="text-[8.5px] text-slate-500 leading-relaxed mt-auto">
                  <strong>Rechtliche Hinweise:</strong>
                  Wärmelieferung: Umstellung mind. 3 Monate vorab in Textform ankündigen (§ 556c Abs. 2 BGB, WärmeLV §§ 8–11); Abrechnung nach Heizkostenverordnung.
                  Modernisierungsumlage § 559e BGB: 10 % der um Fördermittel gekürzten Kosten, pauschal −15 % Erhaltungsanteil, Kappgrenze 0,50 €/m² monatlich.
                  Mieterstrom: Preisobergrenze 90 % des örtlichen Grundversorgertarifs (§ 42a Abs. 4 EnWG); Zuschlagssätze Stand 06/2026 mit monatlicher Degression – vor Angebotserstellung gegen die aktuellen BNetzA-Werte prüfen. Gemeinschaftliche Gebäudeversorgung nach § 42b EnWG ohne Zuschlag und ohne Lieferantenpflichten; Mieter benötigen einen Reststromvertrag.
                  CO₂-Kostenaufteilung nach CO2KostAufG (Einstufung lt. Anlage zu § 5 Abs. 2). Alle Werte sind Prognosen auf Basis Ihrer Angaben; dieses Dokument ersetzt keine Rechts- oder Steuerberatung.
                </div>
              </div>

              ${ReportFooter()}
            </div>
            ` : ''}

            ${angebotsStufen ? `
            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('IHRE OPTIONEN')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black text-[${theme.primary}] mb-1 uppercase tracking-[0.14em]">
                  Drei Wege zu Ihrer Energiewende
                </h2>
                <p class="text-[9.5px] text-slate-500 mb-4 leading-relaxed">
                  Vom soliden Einstieg bis zum Vollausbau mit Reserven – alle drei Optionen sind mit
                  identischen Annahmen durchgerechnet und jederzeit erweiterbar.
                </p>

                <div class="grid grid-cols-3 gap-3 flex-1 min-h-0">
                  ${Object.values(angebotsStufen).map(stufe => {
                                const c = stufe.computed;
                                const istEmpfehlung = stufe.name === 'Empfehlung';
                                const f = c.finance;
                                const zeile = (label, wert, betont = false) => `
                      <div class="flex justify-between items-baseline gap-2 border-b border-slate-100 py-1.5">
                        <span class="text-[8.5px] text-slate-500">${label}</span>
                        <span class="text-[10px] font-bold ${betont ? '' : 'text-slate-700'} shrink-0" ${betont ? `style="color:${theme.primary}"` : ''}>${wert}</span>
                      </div>`;
                                return `
                      <div class="relative flex flex-col rounded-2xl border-2 p-4 ${istEmpfehlung ? 'shadow-lg' : 'border-slate-200'}"
                        style="${istEmpfehlung ? `border-color:${theme.primary};background:${theme.bgLight}40` : ''}">
                        ${istEmpfehlung ? `
                          <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 text-[8px] font-black text-white px-3 py-1 rounded-full uppercase tracking-wider"
                            style="background:${theme.primary}">Unsere Empfehlung</div>
                        ` : ''}

                        <div class="text-[13px] font-black ${istEmpfehlung ? '' : 'text-slate-700'} mt-1" ${istEmpfehlung ? `style="color:${theme.primary}"` : ''}>${stufe.name}</div>
                        <div class="text-[8px] text-slate-500 mb-2">${stufe.untertitel}</div>

                        <div class="text-[8.5px] text-slate-600 leading-relaxed mb-2 pb-2 border-b border-slate-200">
                          PV ${formatDE(c.derivedParams.pvKwp, 1)} kWp
                          ${c.derivedParams.batteryCapacity > 0 ? ` · Speicher ${formatDE(c.derivedParams.batteryCapacity)} kWh` : ' · ohne Speicher'}
                          ${config.moduleWP ? ` · WP ${c.derivedParams.wpLeistungKW} kW` : ''}
                          ${stufe.name === 'Basis' ? '' : (stufe.name === 'Zukunft' || config.moduleWB ? ' · Wallbox' : '')}
                        </div>

                        <div class="text-center my-1">
                          <div class="text-[19px] font-black leading-none" style="color:${theme.primary}">${formatDE(f.nettoInvest)} €</div>
                          <div class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wide mt-1">Netto-Investition</div>
                        </div>

                        <div class="mt-2">
                          ${zeile('Förderung + Rabatte', `−${formatDE(f.totalFoerderung + f.totalDiscount)} €`)}
                          ${zeile('Ersparnis im 1. Jahr', `${formatDE(f.ersparnisJahr1)} €`, true)}
                          ${zeile('Autarkiegrad', `${c.kpis.autarkie} %`)}
                          ${zeile('Amortisation', f.amortisationYear ? `${f.amortisationYear} Jahre` : '> 30 Jahre')}
                          ${zeile('Ersparnis nach 20 Jahren', `${formatDE(f.ersparnis20)} €`, true)}
                          ${zeile('CO₂-Einsparung pro Jahr', `${formatDE(Number(c.co2.year), 1)} t`)}
                        </div>
                      </div>
                    `;
                            }).join('')}
                </div>

                <p class="text-[8px] text-slate-400 leading-relaxed mt-3">
                  Alle Optionen mit identischen Energiepreis-Annahmen gerechnet (Rechenmodell-Stand ${ASSUMPTIONS.version}).
                  Basis = Empfehlung ohne Speicher/Wallbox; Zukunft = Empfehlung mit Speicher-Reserve${config.moduleWB ? '' : ' und Wallbox'}.
                  Jede Stufe ist später zum Vollausbau erweiterbar. Prognosen ohne Gewähr.
                </p>
              </div>

              ${ReportFooter()}
            </div>
            ` : ''}

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('TRANSPARENZ: TECHNISCHE BERECHNUNGEN')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                  Transparenz: Technische Berechnungen
                </h2>

                <p class="text-[9px] text-dark-600 mb-2.5 leading-relaxed">
                  Vertrauen erfordert Nachvollziehbarkeit. Auf dieser Seite legen wir die wichtigsten Berechnungsgrundlagen,
                  Formeln und regionalen Klimadaten für das Objekt in <strong>${config.plz}</strong> offen, die zu Ihrer
                  Systemauslegung geführt haben.
                </p>

                <div class="space-y-2.5 flex-1 min-h-0">

                  ${config.moduleWP ? `
                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.thermometer()}</span>
                      1. Ermittlung des Systemverlusts (Altsystem)
                    </h3>

                    <p class="text-[8.5px] text-dark-600 mb-1.5 leading-relaxed">
                      Jede Heizanlage verliert mit den Jahren an Effizienz. Dieser Verlust wird berücksichtigt, um den
                      <strong>tatsächlichen thermischen Nutzenergiebedarf</strong> des Hauses zu ermitteln.
                    </p>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Aktueller Heizungs-Typ:</span>
                        <strong class="shrink-0">${config.heizungArt}</strong>
                      </div>
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Alter der Heizung:</span>
                        <strong class="shrink-0">${config.heizungAlter} Jahre</strong>
                      </div>

                      <div class="col-span-2 bg-white p-2 border border-slate-200 rounded mt-0.5">
                        <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">
                          Formel: Systemverlust durch Alterung
                        </span>
                        <div class="text-[8.5px] font-bold text-dark-600 leading-relaxed">
                          Bisheriger Verbrauch (${config.heizVerbrauch} ${getHeizEinheit(config.heizungArt)}) ×
                          angenommener Verlust (${derivedParams.systemVerlust * 100}%) = tatsächlicher Wärmebedarf
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.calculator()}</span>
                      2. Wärmebedarf & Heizlast
                    </h3>

                    <div class="space-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between gap-3">
                        <span>Tatsächlicher Wärmebedarf gesamt (inkl. Warmwasser):</span>
                        <strong class="shrink-0">${formatDE(Math.round(derivedParams.thermischHauptsystem))} kWh</strong>
                      </div>

                      <div class="flex justify-between gap-3 text-slate-500">
                        <span class="flex items-center gap-1.5">
                          <span class="w-3 h-3 shrink-0">${Icons.users()}</span>
                          Anteil Warmwasser (${config.personen} Personen á 800 kWh)
                        </span>
                        <strong class="shrink-0">- ${config.warmwasserArt === 'Zentral' ? config.personen * 800 : 0} kWh</strong>
                      </div>

                      ${config.zirkulation && config.warmwasserArt === 'Zentral' ? `
                        <div class="flex justify-between gap-3 text-slate-500">
                          <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 shrink-0">${Icons.droplet()}</span>
                            Zirkulationsverlust Warmwasser
                          </span>
                          <strong class="shrink-0">- 600 kWh</strong>
                        </div>
                      ` : ''}

                      <div class="flex justify-between gap-3 text-dark-600 border-b border-slate-200 pb-1">
                        <span>Verbleibender Bedarf reine Raumheizung:</span>
                        <strong class="shrink-0">= ${formatDE(Math.round(derivedParams.heizWärmeBedarf))} kWh</strong>
                      </div>

                      <div class="bg-white p-2 border border-slate-200 rounded mt-1">
                        <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">
                          Dimensionierung der Wärmepumpe (Schweizer Formel für ${config.plz})
                        </span>
                        <div class="text-[8.5px] mb-1 leading-relaxed text-slate-700">
                          ${Math.round(derivedParams.gesamtWaermeBedarfHaus)} kWh / ${derivedParams.klima.vbh} Vollbenutzungsstunden =
                          <strong style="color:${theme.primary}">${derivedParams.berechneteHeizlast} kW Heizlast</strong>
                        </div>
                        <div class="text-[8.5px] text-slate-500 leading-relaxed">
                          Klimabasis: NAT ${derivedParams.klima.nat} °C und Heizgradtage ${derivedParams.klima.hgt} Kd.
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                      3. Ermittlung der Jahresarbeitszahl (JAZ)
                    </h3>

                    <p class="text-[8.5px] text-dark-600 mb-1.5 leading-relaxed">
                      Die Jahresarbeitszahl (JAZ) beschreibt, wie viele Kilowattstunden Wärme die Anlage aus einer
                      Kilowattstunde Strom erzeugt.
                    </p>

                    <div class="space-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between gap-3">
                        <span>COP Raumheizung (System: ${config.heizSystem})</span>
                        <strong class="shrink-0">${derivedParams.copSH.toFixed(2)}</strong>
                      </div>
                      <div class="flex justify-between gap-3">
                        <span>COP Warmwasserbereitung</span>
                        <strong class="shrink-0">${derivedParams.copWW.toFixed(2)}</strong>
                      </div>
                      <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1">
                        <span>Gewichtete Jahresarbeitszahl (JAZ)</span>
                        <span class="shrink-0" style="color:${theme.primary}">
                          ${config.customJAZ !== '' ? `${derivedParams.jaz} (Manuell)` : derivedParams.jaz}
                        </span>
                      </div>
                    </div>
                  </div>
                  ` : ''}

                  ${config.modulePV ? `
                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.sun()}</span>
                      4. Berechnung der PV- und Speichergröße <span>Ertrag Basisfaktor (PLZ ${config.plz}):</span>
                    </h3>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Summe Strombedarf (Sektoren):</span>
                        <strong class="shrink-0">${formatDE(derivedParams.gesamtStrombedarf)} kWh</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        
                        <strong class="shrink-0">${getRegionalFactors(config.plz).pvBaseFactor} kWh/kWp</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 col-span-2 items-start gap-4">
                        <span>Dachausrichtungen & Modul-Verteilung:</span>
                        <div class="text-right text-[8.5px] leading-relaxed">
                          ${derivedParams.distributedDachseiten.map(d => `
                            <div class="font-bold">
                              ${d.designation ? `${d.designation} – ` : ''}${d.roofForm ? `${d.roofForm}, ` : ''}${d.ausrichtung} (${d.neigung}°, ${d.eindeckung}):
                              <span style="color:${theme.primary}">${d.calculatedKwp} kWp</span>
                            </div>
                          `).join('')}
                        </div>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Gewichteter Dachfaktor:</span>
                        <strong class="shrink-0">${(derivedParams.avgYieldFactor * 100).toFixed(0)} %</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Effektiver Ertrag Objekt (Quelle: ${derivedParams.pvQuelle}):</span>
                        <strong class="shrink-0">${Math.round(derivedParams.effectiveYieldPvKwp)} kWh/kWp</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 font-bold col-span-2 gap-3">
                        <span>Installierte Gesamt-PV-Leistung:</span>
                        <span class="shrink-0" style="color:${theme.primary}">${derivedParams.pvKwp} kWp</span>
                      </div>
                    </div>
                  </div>
                  ` : ''}

                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.activity()}</span>
                        5. Eigenverbrauchsquote
                      </h3>

                      <p class="text-[8.5px] text-dark-600 mb-2 leading-relaxed">
                        Wie viel Prozent Ihres <strong>selbst produzierten Solarstroms</strong> nutzen Sie direkt im Haus oder im
                        Speicher, statt ihn ins Netz einzuspeisen?
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Direktnutzung + Batterieladung) / Solar-Gesamtertrag × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-dark-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${kpis.eigenverbrauchQuote} %</span>
                        </div>
                      </div>
                    </div>

                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.shieldCheck()}</span>
                        6. Autarkiegrad
                      </h3>

                      <p class="text-[8.5px] text-dark-600 mb-2 leading-relaxed">
                        Wie viel Prozent Ihres <strong>gesamten Strombedarfs</strong> werden durch die eigene Solar-Anlage und den
                        Speicher gedeckt?
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Direktnutzung + Batterieentladung) / Gesamtstrombedarf × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-dark-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${kpis.autarkie} %</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('TRANSPARENZ: KENNZAHLEN & EFFIZIENZ')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[16px] font-black mb-2 uppercase tracking-[0.14em]" style="color:${theme.primary}">
                  Transparenz: Kennzahlen & Effizienz
                </h2>

                <p class="text-[9px] text-dark-600 leading-relaxed mb-2.5">
                  Die folgenden Kennzahlen machen Ihr Energiesystem transparent und vergleichbar. Sie zeigen, wie effizient Ihr
                  selbst erzeugter Strom genutzt wird, wie hoch Ihre Unabhängigkeit vom Netz ausfällt und welche finanziellen
                  Vorteile sich daraus ergeben.
                </p>

                <div class="space-y-2.5 flex-1 min-h-0">
                  

                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5" style="color:${theme.primary}">
                        7. Saisonale Autarkie
                      </h3>

                      <p class="text-[8.5px] text-dark-600 leading-relaxed">
                        Ein Jahresdurchschnitt kann täuschen: Im Sommer entstehen hohe Überschüsse, während im Winter Lastspitzen
                        auftreten. Die saisonale Autarkie zeigt, wie stabil Ihr System über das Jahr arbeitet und wie stark der
                        Speicher besonders in den dunkleren Monaten entlastet.
                      </p>
                    </div>

                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5" style="color:${theme.primary}">
                        8. Finanzielle Unabhängigkeit
                      </h3>

                      <p class="text-[8.5px] text-dark-600 leading-relaxed mb-2">
                        Diese Kennzahl zeigt Ihre Ersparnis im Verhältnis zu Ihren bisherigen jährlichen Energiekosten.
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Ersparnis Jahr 1 / alte Energiekosten Jahr 1) × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-dark-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${finance.finUnabhProzent} %</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.secondary}">${Icons.zap()}</span>
                        9. §14a EnWG (Netzentgelte)
                      </h3>

                      <div class="space-y-1.5 text-[8.5px] text-slate-700">
                        <div class="flex justify-between gap-3">
                          <span>Angesetztes Netzentgelt (AP):</span>
                          <strong class="shrink-0">${config.netzentgelt.toLocaleString('de-DE')} €/kWh</strong>
                        </div>

                        <div class="flex justify-between gap-3 text-slate-500 border-b border-slate-200 pb-1.5">
                          <span>Steuerbare Einheit (SteuVE):</span>
                          <strong class="shrink-0">${finance.evKostenOhne > 0 || finance.wpKostenOhne > 0 ? 'Ja (WP/Wallbox)' : 'Keine'}</strong>
                        </div>

                        <div class="bg-white p-2 border border-slate-200 rounded-lg mt-0.5 space-y-1">
                          <span class="block text-[8.5px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Modul 1 vs. Modul 2</span>

                          <div class="flex justify-between gap-3">
                            <span>Modul 1 (Pauschale):</span>
                            <strong class="shrink-0">${ASSUMPTIONS.regulatory.enwg14a.flatSavingEuro} €/Jahr</strong>
                          </div>

                          <div class="flex justify-between gap-3">
                            <span>Modul 2 (60% auf Netzbezug):</span>
                            <strong class="shrink-0">${Math.round((finance.wpNetz + finance.evNetz) * config.netzentgelt * ASSUMPTIONS.regulatory.enwg14a.variableGridFeeReductionShare).toLocaleString('de-DE')} €/Jahr</strong>
                          </div>

                          <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1.5" style="color:${theme.primary}">
                            <span>Angewandter Rabatt (Best-of):</span>
                            <span class="shrink-0">${finance.ersparnis14a.toLocaleString('de-DE')} €/Jahr</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    ${config.moduleWP ? `
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.euro()}</span>
                        10. KfW-Fördermittelaufbau
                      </h3>

                      <div class="space-y-1 text-[8.5px] text-slate-700">
                        <div class="flex justify-between gap-3">
                          <span>Basisförderung (Wärmepumpe):</span>
                          <strong class="shrink-0">${ASSUMPTIONS.funding.kfw458.baseGrantPercent} %</strong>
                        </div>

                        <div class="flex justify-between gap-3">
                          <span>Effizienzbonus (natürliches Kältemittel):</span>
                          <strong class="shrink-0">+ ${ASSUMPTIONS.funding.kfw458.efficiencyBonusPercent} %</strong>
                        </div>

                        <div class="flex justify-between gap-3">
                          <span>Klimageschwindigkeitsbonus (Ersatz ${config.heizungArt}):</span>
                          <strong class="shrink-0">${finance.kfwDetails.klimaBonus > 0 ? '+ ' + ASSUMPTIONS.funding.kfw458.climateSpeedBonusPercent + ' %' : '+ 0 %'}</strong>
                        </div>

                        ${config.weUnter40k > 0 ? `
                          <div class="flex justify-between gap-3 font-medium" style="color:${theme.primary}">
                            <span>Einkommensbonus (Haushalt &lt; 40k €):</span>
                            <strong class="shrink-0">+ ${ASSUMPTIONS.funding.kfw458.incomeBonusPercent} %</strong>
                          </div>
                        ` : ''}

                        <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1.5 mt-0.5">
                          <span>Gesamter Fördersatz (max. ${ASSUMPTIONS.funding.kfw458.maxGrantPercent}%):</span>
                          <span class="shrink-0" style="color:${theme.primary}">${finance.maxZuschussProzent} %</span>
                        </div>

                        <div class="text-[8.5px] text-slate-500 mt-0.5 leading-relaxed">
                          Der Fördersatz wird auf die maximal förderfähigen Kosten von
                          ${finance.weDeckelung.toLocaleString('de-DE')} € bei ${config.wohneinheiten} Wohneinheiten angewendet.
                        </div>
                      </div>
                    </div>
                    ` : `
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white flex items-center justify-center">
                      <div class="text-center">
                        <div class="text-[9px] font-bold mb-1" style="color:${theme.primary}">Förderübersicht</div>
                        <p class="text-[8.5px] text-slate-500 leading-relaxed">
                          Die KfW-Förderstruktur wird nur angezeigt, wenn das Wärmepumpenmodul aktiv ist.
                        </p>
                      </div>
                    </div>
                    `}
                  </div>
                </div>
              </div>

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                  KLIMASCHUTZ & WIE ES WEITERGEHT
                </h2>

                <div class="rounded-xl px-4 py-3 border flex items-start gap-4 mb-3" style="border-color:${theme.secondary}50">
                  <span class="w-9 h-9 shrink-0 mt-0.5" style="color:${theme.primary}">
                    ${Icons.leaf()}
                  </span>

                  <div class="flex-1 min-w-0">
                    <h3 class="text-[13px] font-bold mb-1" style="color:${theme.primary}">
                      Ihr aktiver Klimaschutz
                    </h3>

                    <p class="text-[9px] leading-relaxed mb-2.5 text-dark-600">
                      Neben der finanziellen Ersparnis leisten Sie einen spürbaren Beitrag für die nächste Generation.
                      Ihre jährliche Einsparung an CO₂-Emissionen entspricht der Speicherkraft von ca.
                      <strong style="color:${theme.primary}">${computed.co2.trees} Bäumen</strong>
                      oder einer Mischwaldfläche von
                      <strong style="color:${theme.primary}">${computed.co2.forestArea.toLocaleString('de-DE')} m²</strong>.
                    </p>

                    <div class="grid grid-cols-3 gap-2 text-center">
                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          Pro Jahr
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.year} t
                        </span>
                      </div>

                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          10 Jahre
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.tenYears} t
                        </span>
                      </div>

                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          20 Jahre
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.twentyYears} t
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <h3 class="text-[11px] font-bold text-dark-600 mb-2 border-b border-slate-200 pb-1.5">
                  Wie es jetzt für Sie weitergeht
                </h3>

                <div class="grid grid-cols-3 gap-3 mb-3">
                  <div class="flex flex-col items-center text-center px-1">
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-black text-[15px] mb-2"
                        style="background:${theme.primary}">
                      1
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Vor-Ort-Analyse</h4>
                    <p class="text-[8px] text-dark-600 leading-relaxed">
                      Wir prüfen die baulichen Gegebenheiten vor Ort und erstellen das finale, verbindliche Festpreisangebot.
                    </p>
                  </div>

                  <div class="flex flex-col items-center text-center relative px-1">
                    <div class="hidden md:block absolute top-[18px] -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-black text-[15px] mb-2"
                        style="background:${theme.primary}">
                      2
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Fördermittelservice</h4>
                    <p class="text-[8px] text-dark-600 leading-relaxed">
                      Wir übernehmen die Beantragung aller KfW-Zuschüsse, damit Ihre Förderung optimal ausgeschöpft wird.
                    </p>
                  </div>

                  <div class="flex flex-col items-center text-center relative px-1">
                    <div class="hidden md:block absolute top-[18px] -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center mb-2"
                        style="background:${theme.primary};box-shadow:0 8px 14px -4px ${theme.primary}40">
                      <span class="w-4 h-4">${Icons.checkCircle()}</span>
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Fachgerechte Installation</h4>
                    <p class="text-[8px] text-dark-600 leading-relaxed">
                      Unsere Meister montieren Ihr System schlüsselfertig. Nach der Inbetriebnahme produzieren Sie sofort eigenen Strom.
                    </p>
                  </div>
                </div>

                <div class="mt-auto rounded-xl border border-slate-200 px-4 py-3 bg-white">
                  <h3 class="text-[15px] font-black uppercase leading-tight" style="color:${theme.primary}">
                    Von der Sonne bekommen Sie die Energie.
                  </h3>
                  <h3 class="text-[15px] font-black mb-1 uppercase leading-tight" style="color:${theme.primary}">
                    Alles andere von uns.
                  </h3>

                  <h4 class="text-[8px] font-bold text-dark-600 mb-1.5 tracking-[0.12em] uppercase">
                    Solar + Wärmepumpe + Wallbox – als abgestimmtes Gesamtsystem aus einer Hand
                  </h4>

                  <p class="text-[8.5px] text-dark-600 leading-relaxed">
                    Die Kombination aus Photovoltaik, Wärmepumpe und Ladestation macht aus einzelnen Lösungen ein stimmiges
                    Gesamtsystem. Ihr Eigenstrom kann Ihren Bedarf sinnvoll abdecken und Sie gewinnen mehr Kontrolle über Ihre
                    Energieversorgung. Wir setzen das System fachgerecht um und begleiten Sie vom ersten Schritt bis zur
                    Inbetriebnahme.
                  </p>
                </div>
              </div>

              ${ReportFooter()}
            </div>
 

           <div class="a4-page flex flex-col bg-white overflow-hidden relative">
            ${ReportHeader('FINANZIERUNG & HINWEISE')}

            <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
              <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                IHR FINANZIERUNGSVORTEIL & RECHTLICHE HINWEISE
              </h2>

              @include('admin.checklist.profitablity_calculation.bonus')

              <div class="mt-auto text-[10px] text-slate-500 leading-relaxed text-justify border-t border-slate-200 pt-2">
                  <strong>BONUS:</strong>
                  Der aktuell ausgewiesene Preisvorteil beträgt ${finance.totalDiscount > 0 ? finance.totalDiscount.toLocaleString('de-DE') : '0'} € brutto.
                  <br><br>
                  <strong>Preisvorteil / Systemrabatt:</strong>
                  Maßgeblich für Art, Umfang und Höhe eines gewährten Preisvorteils sind ausschließlich Angebot und Auftragsbestätigung. Der Preisvorteil wird als Nachlass auf Angebotspreis bzw. Schlussrechnung angerechnet; Barauszahlung, Teilabarauszahlung, Übertragung, Verrechnung mit anderen Projekten oder Umtausch sind ausgeschlossen. Er gilt nur für das bezeichnete Objekt, innerhalb der Angebotsfrist und vorbehaltlich technischer Umsetzbarkeit, Verfügbarkeit, erforderlicher Freigaben und unveränderter Projektgrundlagen. Bei Änderungen des Leistungsumfangs oder der Kalkulationsgrundlagen behalten wir uns eine Anpassung oder den Wegfall vor.
                  <br><br>
                  <strong>Wirtschaftlichkeitsberechnung:</strong>
                  Rechenmodell: ${ASSUMPTIONS.version}, Annahmenstand: ${ASSUMPTIONS.legalStatus}.
                  Die beigefügte Berechnung dient ausschließlich der unverbindlichen Vorabinformation und Orientierung. Sie stellt keine Garantie für Beschaffenheit, Einsparung, Ertrag, Förderung oder Amortisationsdauer dar. Abweichungen sind insbesondere durch Wetter, Nutzerverhalten, Verbrauch, Energiepreise, technische Randbedingungen, regulatorische Änderungen, Netzvorgaben oder Förderentscheidungen möglich. Verbindlich sind ausschließlich die vertraglich vereinbarten Leistungen.
                  <br><br>
                  <strong>Urheberrecht:</strong>
                  Dieses Konzept ist urheberrechtlich geschützt und geistiges Eigentum der WERK STUDIO BAUKONZEPT. Jede Nutzung, Weitergabe oder Vervielfältigung – auch auszugsweise – bedarf unserer vorherigen ausdrücklichen schriftlichen Zustimmung.
                </div>
            </div>

            ${ReportFooter()}
          </div>

          </div>
        </div>
      `;
        }
        // =========================================================
        // CHARTS
        // =========================================================
        function makeDoughnutChart(id, values, colors, cutout = '68%', showSliceLabels = false) {
            destroyChart(id);
            const el = document.getElementById(id);
            if (!el) return;

            const sliceLabelPlugin = {
                id: `sliceLabelPlugin_${id}`,
                afterDatasetsDraw(chart) {
                    if (!showSliceLabels) return;

                    const { ctx } = chart;
                    const dataset = chart.data.datasets[0];
                    const meta = chart.getDatasetMeta(0);
                    const total = dataset.data.reduce((sum, val) => sum + Number(val || 0), 0);

                    if (!total) return;

                    ctx.save();
                    ctx.font = 'bold 10px Inter';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    meta.data.forEach((arc, index) => {
                        const value = Number(dataset.data[index] || 0);
                        if (value <= 0) return;

                        const percent = Math.round((value / total) * 100);

                        // Kleine Segmente nicht beschriften
                        if (percent < 7) return;

                        const angle = (arc.startAngle + arc.endAngle) / 2;
                        const radius = (arc.innerRadius + arc.outerRadius) / 2;
                        const x = arc.x + Math.cos(angle) * radius;
                        const y = arc.y + Math.sin(angle) * radius;

                        ctx.fillStyle = '#ffffff';
                        ctx.fillText(`${percent}%`, x, y);
                    });

                    ctx.restore();
                }
            };

            charts[id] = new Chart(el, {
                type: 'doughnut',
                plugins: [sliceLabelPlugin],
                data: {
                    labels: values.map((_, i) => `item-${i}`),
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (ctx) {
                                    const total = ctx.dataset.data.reduce((sum, val) => sum + Number(val || 0), 0);
                                    const value = Number(ctx.raw || 0);
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                    return `${formatDE(value)} kWh (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function makeBarChart(id, labels, data1, data2) {
            destroyChart(id);
            const el = document.getElementById(id);
            if (!el) return;

            const theme = getActiveTheme();

            charts[id] = new Chart(el, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Solar-Produktion (kWh)',
                            data: data1,
                            backgroundColor: theme.primary,
                            borderRadius: { topLeft: 5, topRight: 5 },
                            maxBarThickness: 22,
                            categoryPercentage: 0.72,
                            barPercentage: 0.85
                        },
                        {
                            label: 'Gesamtbedarf (kWh)',
                            data: data2,
                            backgroundColor: theme.secondary,
                            borderRadius: { topLeft: 5, topRight: 5 },
                            maxBarThickness: 22,
                            categoryPercentage: 0.72,
                            barPercentage: 0.85
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    layout: { padding: { top: 4, right: 4 } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 12, weight: '600' }
                            },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 },
                                maxTicksLimit: 7,
                                callback: function (val) { return formatDE(val); }
                            },
                            border: { display: false },
                            title: {
                                display: true,
                                text: 'kWh / Monat',
                                color: '#94a3b8',
                                font: { size: 10, weight: '600' }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 14,
                                color: '#475569',
                                font: { size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return `${ctx.dataset.label}: ${formatDE(ctx.raw)} kWh`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function makeLineChart(id, labels, oldData, newData, amortisationYear) {
            destroyChart(id);
            const el = document.getElementById(id);
            if (!el) return;
            const theme = getActiveTheme();

            const annotationPlugin = {
                id: 'breakEvenLine',
                afterDatasetsDraw(chart) {
                    if (!amortisationYear) return;
                    const xScale = chart.scales.x;
                    const yScale = chart.scales.y;
                    const idx = labels.indexOf(String(amortisationYear));
                    if (idx === -1) return;

                    const x = xScale.getPixelForValue(idx);
                    const ctx = chart.ctx;
                    ctx.save();
                    ctx.strokeStyle = theme.primary;
                    ctx.lineWidth = 2;
                    ctx.setLineDash([4, 4]);
                    ctx.beginPath();
                    ctx.moveTo(x, yScale.top);
                    ctx.lineTo(x, yScale.bottom);
                    ctx.stroke();
                    ctx.setLineDash([]);
                    ctx.fillStyle = theme.primary;
                    ctx.font = 'bold 11px Inter';
                    ctx.fillText(`Break-Even (Jahr ${amortisationYear})`, x + 6, yScale.top + 14);
                    ctx.restore();
                }
            };

            charts[id] = new Chart(el, {
                type: 'line',
                plugins: [annotationPlugin],
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Ohne Systemwechsel (Laufende Kosten)',
                            data: oldData,
                            borderColor: '#94a3b8',
                            backgroundColor: '#94a3b81a',
                            fill: true,
                            borderWidth: 2.5,
                            borderDash: [6, 4],
                            tension: 0.35,
                            pointRadius: 0
                        },
                        {
                            label: 'Mit Systemwechsel (Investition + Restkosten)',
                            data: newData,
                            borderColor: theme.primary,
                            backgroundColor: theme.primary + '22',
                            fill: true,
                            borderWidth: 3,
                            tension: 0.35,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    layout: { padding: { top: 4, right: 8 } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { size: 10 }, maxTicksLimit: 16 },
                            border: { display: false },
                            title: {
                                display: true,
                                text: 'Jahre',
                                color: '#94a3b8',
                                font: { size: 10, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10 },
                                maxTicksLimit: 7,
                                callback: function (val) {
                                    return val >= 1000 ? `${formatDE(val / 1000)} Tsd. €` : `${formatDE(val)} €`;
                                }
                            },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 14,
                                color: '#475569',
                                font: { size: 11, weight: '600' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return `${ctx.dataset.label}: ${formatDE(ctx.raw)} €`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function initDashboardCharts() {
            const computed = getComputed();
            const { kpis, bedarfsMix, seasonalData, chartData, finance } = computed;
            const theme = getActiveTheme();

            makeDoughnutChart('donutAutarkie', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzbezug], [theme.primary, theme.secondary, theme.inactive]);
            makeDoughnutChart('donutEigenverbrauch', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzeinspeisung], [theme.primary, theme.secondary, theme.inactive]);
            makeDoughnutChart('donutFinanz', [finance.finUnabhProzent, Math.max(0, 100 - finance.finUnabhProzent)], [theme.primary, theme.inactive]);

            makeDoughnutChart(
                'bedarfsmixChart',
                bedarfsMix.map(i => i.value),
                bedarfsMix.map(i => i.fill),
                '62%',
                false
            );

            seasonalData.forEach((season, i) => {
                const pieData = [
                    season.DirektDeckung,
                    season.BatterieDeckung,
                    season.Netzbezug
                ].filter(v => v > 0);

                const pieColors = [
                    theme.primary,
                    theme.secondary,
                    theme.inactive
                ].slice(0, pieData.length);

                makeDoughnutChart(`seasonChart${i}`, pieData, pieColors);
            });

            makeBarChart(
                'monthlyCompareChart',
                chartData.map(i => i.name),
                chartData.map(i => i.Solarertrag),
                chartData.map(i => i.Gesamtbedarf)
            );

            makeLineChart(
                'financeLineChart',
                finance.cashflow.map(i => i.year),
                finance.cashflow.map(i => i.kostenOhne),
                finance.cashflow.map(i => i.kostenMit),
                finance.amortisationYear
            );
        }

        function openCustomerProfile() {
            const customerId = backendMeta.customerId || backendCustomer?.customer_id || null;

            if (!customerId) {
                alert('Keine Kunden-ID gefunden.');
                return;
            }

            window.location.href = `/new_lead_profile/${customerId}`;
        }
        // =========================================================
        // ROOT RENDER OVERRIDE
        // =========================================================
        function renderApp() {
            // 0. Standortdaten (PVGIS/Zippopotam) bei Bedarf nachladen – idempotent
            syncStandortDaten();

            // 1. Capture scroll positions before re-render
            const sidebarEl = document.getElementById('sidebar-scroll-container');
            const sidebarScrollPos = sidebarEl ? sidebarEl.scrollTop : 0;
            const windowScrollPos = window.scrollY;

            // Fokus über den Re-Render retten: das Formular wird komplett neu aufgebaut,
            // sonst landet der Fokus nach jeder Eingabe auf <body> (kein Durch-Tabben möglich)
            const focusables = () => [...document.querySelectorAll('#app input, #app select, #app textarea, #app button')];
            const activeEl = document.activeElement;
            const prevFocusIdx = focusables().indexOf(activeEl);
            let selStart = null, selEnd = null;
            if (prevFocusIdx >= 0 && typeof activeEl.selectionStart === 'number') {
                try { selStart = activeEl.selectionStart; selEnd = activeEl.selectionEnd; } catch (e) { }
            }

            updateThemeCSS();
            const app = document.getElementById('app');
            if (!app) return;

            // 2. Re-render DOM
            app.innerHTML = (state.view === 'wizard'
                ? renderWizard()
                : renderDashboard()) + entwurfBannerHtml();

            if (prevFocusIdx >= 0) {
                const el = focusables()[prevFocusIdx];
                if (el) {
                    el.focus({ preventScroll: true });
                    if (selStart !== null && typeof el.selectionStart === 'number') {
                        try { el.setSelectionRange(selStart, selEnd); } catch (e) { }
                    }
                }
            }

            // 3. Restore scroll positions and init charts
            if (state.view === 'dashboard') {
                requestAnimationFrame(() => {
                    initDashboardCharts();
                    const newSidebarEl = document.getElementById('sidebar-scroll-container');
                    if (newSidebarEl) newSidebarEl.scrollTop = sidebarScrollPos;
                    window.scrollTo(0, windowScrollPos);
                });
            } else {
                requestAnimationFrame(() => {
                    window.scrollTo(0, windowScrollPos);
                });
            }
        }
        pruefeEntwurf();
        renderApp();
    </script>
</body>

</html>