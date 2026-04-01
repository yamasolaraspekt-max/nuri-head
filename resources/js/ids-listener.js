import axios from "axios";

// ------------------------------------------------------
// 1) Helper: Fetch Form Options (Brands/Groups)
// ------------------------------------------------------
async function fetchPromoteOptions() {
    try {
        // NOTE: You must create a route in Laravel: Route::get('/ids/promote-options', ...)
        // that returns: { brands: [{id, name}, ...], articleGroups: [{id, article_group}, ...] }
        const response = await axios.get("/ids/promote-options");
        return response.data;
    } catch (error) {
        console.warn(
            "⚠️ Could not fetch promote options. Ensure the route '/ids/promote-options' exists and returns { brands, articleGroups }.",
            error
        );
        return { brands: [], articleGroups: [] };
    }
}

// ------------------------------------------------------
// 2) Helper: Fetch Sub-Groups
// ------------------------------------------------------
async function fetchSubGroups(groupId) {
    if (!groupId) return [];
    try {
        const response = await axios.get(
            `/article-groups/${groupId}/sub-groups`
        );
        return response.data; // Expects [{id, sub_article}, ...]
    } catch (error) {
        console.error("Error fetching sub-groups:", error);
        return [];
    }
}

// ------------------------------------------------------
// 3) Create modal HTML dynamically if not in DOM
// ------------------------------------------------------
function ensureIdsModalExists() {
    if (document.getElementById("idsModalOverlay")) return;

    const modalHtml = `
        <div id="idsModalOverlay" style="display:none;">
            <div id="idsModal">
                <div class="ids-modal-header">
                    <h2 id="idsModalTitle">IDS Artikel importiert</h2>
                    <span id="idsCloseBtn">&times;</span>
                </div>
                <div id="idsModalContent"></div>
            </div>
        </div>
        <style>
            #idsModalOverlay {
                position: fixed; inset: 0; background: rgba(0,0,0,0.6);
                display: flex; justify-content: center; align-items: center; z-index: 9999;
                backdrop-filter: blur(2px);
            }
            #idsModal {
                background: white; border-radius: 12px; width: 600px; max-width: 95%;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;
            }
            .ids-modal-header {
                padding: 16px 24px; border-bottom: 1px solid #e5e7eb;
                display: flex; justify-content: space-between; align-items: center; background: #f9fafb;
            }
            #idsModalTitle { margin: 0; font-size: 18px; font-weight: 600; color: #111827; }
            #idsCloseBtn { cursor: pointer; font-size: 24px; color: #6b7280; line-height: 1; }
            #idsCloseBtn:hover { color: #111827; }
            #idsModalContent { padding: 24px; overflow-y: auto; }
            
            /* Item List Styles */
            .idsItem { border-bottom: 1px solid #f3f4f6; padding: 12px 0; }
            .idsItem:last-child { border-bottom: none; }
            .idsItem strong { color: #1f2937; }

            /* Form Styles */
            .ids-form-group { margin-bottom: 16px; }
            .ids-form-label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px; }
            .ids-form-select {
                width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;
                background-color: #fff; font-size: 14px; color: #111827;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .ids-form-select:focus { border-color: #3b82f6; outline: none; ring: 2px solid #93c5fd; }
            .ids-btn-primary {
                width: 100%; display: inline-flex; justify-content: center; align-items: center;
                padding: 10px 16px; border: none; border-radius: 6px;
                background-color: #2563eb; color: white; font-weight: 500; font-size: 14px;
                cursor: pointer; transition: background-color 0.2s; margin-top: 8px;
            }
            .ids-btn-primary:hover { background-color: #1d4ed8; }
            .ids-btn-primary:disabled { background-color: #9ca3af; cursor: not-allowed; }
            .ids-loading { text-align: center; color: #6b7280; font-size: 14px; padding: 20px; }
        </style>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHtml);
    document.getElementById("idsCloseBtn").onclick = closeIdsModal;
}

function closeIdsModal() {
    const overlay = document.getElementById("idsModalOverlay");
    if (overlay) overlay.style.display = "none";
}

// ------------------------------------------------------
// 4) Main Logic: Load Results & Decide View
// ------------------------------------------------------
function loadIdsResults(batchId) {
    // Check if the "Auto Promote" checkbox is checked on the search page
    const autoPromoteCheckbox = document.getElementById("gcAutoPromote");
    const isAutoPromote = autoPromoteCheckbox && autoPromoteCheckbox.checked;

    axios
        .get(`/ids/results/${batchId}`)
        .then(async (res) => {
            const items = res.data;
            if (!items || items.length === 0) return;

            ensureIdsModalExists();
            const contentDiv = document.getElementById("idsModalContent");
            const titleEl = document.getElementById("idsModalTitle");

            // Show overlay immediately
            document.getElementById("idsModalOverlay").style.display = "flex";

            if (isAutoPromote) {
                // --- FORM VIEW ---
                titleEl.textContent = "Produkte kategorisieren & speichern";
                contentDiv.innerHTML =
                    '<div class="ids-loading">Lade Optionen...</div>';

                // Fetch options for dropdowns
                const options = await fetchPromoteOptions();
                renderPromoteForm(contentDiv, items, options);
            } else {
                // --- STANDARD LIST VIEW ---
                titleEl.textContent = "IDS Artikel importiert";
                let html = "";
                items.forEach((i) => {
                    html += `
                        <div class="idsItem">
                            <strong>${i.article_no}</strong><br>
                            ${i.short_text || ""}<br>
                            <span style="font-size:0.85em; color:#6b7280;">${
                                i.qty
                            } ${i.unit}</span>
                        </div>`;
                });
                contentDiv.innerHTML = html;
            }
        })
        .catch((err) => console.error("IDS load error:", err));
}

// ------------------------------------------------------
// 5) Render the Batch Promote Form
// ------------------------------------------------------
function renderPromoteForm(container, items, { brands, articleGroups }) {
    // Generate Options HTML
    const brandOptions = brands
        .map((b) => `<option value="${b.id}">${b.name}</option>`)
        .join("");
    const groupOptions = articleGroups
        .map((g) => `<option value="${g.id}">${g.article_group}</option>`)
        .join("");

    const html = `
        <div style="margin-bottom: 20px; padding: 12px; background: #eff6ff; border-radius: 6px; color: #1e40af; font-size: 14px;">
            <strong>${items.length} Artikel importiert.</strong><br>
            Bitte wähle die globalen Eigenschaften für diese Artikel aus.
        </div>
        
        <form id="idsPromoteForm">
            <div class="ids-form-group">
                <label class="ids-form-label">Hersteller / Marke</label>
                <select id="idsBrandSelect" class="ids-form-select" required>
                    <option value="">Bitte wählen...</option>
                    ${brandOptions}
                </select>
            </div>

            <div class="ids-form-group">
                <label class="ids-form-label">Artikelgruppe</label>
                <select id="idsGroupSelect" class="ids-form-select" required>
                    <option value="">Bitte wählen...</option>
                    ${groupOptions}
                </select>
            </div>

            <div class="ids-form-group">
                <label class="ids-form-label">Untergruppe</label>
                <select id="idsSubGroupSelect" class="ids-form-select" disabled>
                    <option value="">Erst Artikelgruppe wählen...</option>
                </select>
            </div>

            <button type="submit" id="idsPromoteBtn" class="ids-btn-primary">
                Speichern & Artikel anlegen
            </button>
        </form>
    `;

    container.innerHTML = html;

    // --- Add Event Listeners ---
    const groupSelect = document.getElementById("idsGroupSelect");
    const subGroupSelect = document.getElementById("idsSubGroupSelect");
    const form = document.getElementById("idsPromoteForm");

    // 1. Handle Article Group Change -> Fetch SubGroups
    groupSelect.addEventListener("change", async function () {
        const groupId = this.value;
        subGroupSelect.innerHTML = '<option value="">Lade...</option>';
        subGroupSelect.disabled = true;

        if (groupId) {
            const subGroups = await fetchSubGroups(groupId);
            if (subGroups.length > 0) {
                subGroupSelect.innerHTML =
                    '<option value="">Bitte wählen (optional)...</option>' +
                    subGroups
                        .map(
                            (sg) =>
                                `<option value="${sg.id}">${sg.sub_article}</option>`
                        )
                        .join("");
                subGroupSelect.disabled = false;
            } else {
                subGroupSelect.innerHTML =
                    '<option value="">Keine Untergruppen verfügbar</option>';
            }
        } else {
            subGroupSelect.innerHTML =
                '<option value="">Erst Artikelgruppe wählen...</option>';
        }
    });

    // 2. Handle Submit -> Batch Update
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const brandId = document.getElementById("idsBrandSelect").value;
        const groupId = document.getElementById("idsGroupSelect").value;
        const subGroupId = document.getElementById("idsSubGroupSelect").value;
        const btn = document.getElementById("idsPromoteBtn");

        if (!brandId || !groupId) {
            alert("Bitte Hersteller und Artikelgruppe wählen.");
            return;
        }

        btn.disabled = true;
        btn.textContent = "Speichere...";

        try {
            // Iterate over all items and send promote request
            // We use Promise.all to send requests in parallel
            const promises = items.map((item) => {
                return axios.post(`/ids/promote/${item.id}`, {
                    brand_id: brandId,
                    article_group_id: groupId,
                    sub_article_group_id: subGroupId || null,
                    product_name: item.short_text || item.article_no, // Fallback name
                    measure_unit: item.unit,
                });
            });

            await Promise.all(promises);

            // Success UI
            container.innerHTML = `
                <div style="text-align:center; padding: 40px 0;">
                    <div style="color: #10b981; font-size: 48px; margin-bottom: 16px;">✓</div>
                    <h3 style="margin:0; color:#111827;">Erfolgreich gespeichert!</h3>
                    <p style="color:#6b7280;">${items.length} Artikel wurden angelegt/aktualisiert.</p>
                    <button class="ids-btn-primary" onclick="document.getElementById('idsModalOverlay').style.display='none'">Schließen</button>
                </div>
            `;

            // Reload page or trigger refresh if needed
            // location.reload();
        } catch (error) {
            console.error("Batch promote error:", error);
            alert("Fehler beim Speichern. Bitte Konsole prüfen.");
            btn.disabled = false;
            btn.textContent = "Speichern & Artikel anlegen";
        }
    });
}

// ------------------------------------------------------
// 6) Subscribe to Reverb channel "ids"
// ------------------------------------------------------
export default function initializeIdsListener() {
    console.log("🔵 Initializing IDS Listener…");

    if (!window.Echo) {
        console.error("❌ Echo not loaded (window.Echo is undefined)");
        return;
    }

    // Connect to the 'ids' channel
    window.Echo.channel("ids")
        .listen(".IdsItemsImported", (e) => {
            console.log("📦 IDS EVENT RECEIVED:", e);
            if (e.batchId) {
                loadIdsResults(e.batchId);
            }
        })
        .error((err) => {
            console.error("❌ Channel Error:", err);
        });
}
