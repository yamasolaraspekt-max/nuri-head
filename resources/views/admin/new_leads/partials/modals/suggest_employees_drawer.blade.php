            <div id="suggestEmployeesDrawer" class="nx-drawer">
                <div class="nx-drawer-backdrop" data-drawer-close></div>

                <div class="nx-drawer-panel">
                    <div class="nx-drawer-header">
                        <div class="nx-drawer-title">Mitarbeiter vorschlagen</div>
                        <button type="button" class="nx-drawer-close" data-drawer-close aria-label="Schließen">
                            &times;
                        </button>
                    </div>

                    <form id="suggestEmployeesForm">
                        @csrf
                        <input type="hidden" name="customer_id">
                        <input type="hidden" name="alternative_id">
                        <input type="hidden" name="product_id">
                        <input type="hidden" name="phase_id">

                        <div class="nx-drawer-body">
                            <div id="employeeRows"></div>
                        </div>

                        <div class="nx-drawer-footer">
                            <button type="submit" class="btn btn-success">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
