<?php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;
use App\Models\Chat;
use Illuminate\Support\Carbon;
use App\Models\CustomerNote;
use App\Models\LeadAlternativeAdd;
use App\Events\TestNotificationEvent;

use App\Http\Controllers\Admin\SystemWarningController;
use App\Http\Controllers\Admin\GarbageController;
use App\Http\Controllers\Admin\AttendanceAnalyticsController;
// User Management 
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserRollController;
use App\Http\Controllers\User\UserPreferenceController; //Offer Designer Column Display

use App\Http\Controllers\System\FeedbackController;
use App\Http\Controllers\Dashboard\SidebarCountController;
use App\Http\Controllers\Dashboard\DashboardWidgetController;
use App\Http\Controllers\Dashboard\UserDashboardShortcutController;
use App\Http\Controllers\Dashboard\DashboardAbsenceRequestController;
use App\Http\Controllers\Dashboard\DashboardDepartmentController;
use App\Http\Controllers\Dashboard\DashboardCompanyController;
use App\Http\Controllers\Dashboard\DashboardCalendarWidgetController;
use App\Http\Controllers\Dashboard\DashboardEmployeeStatusController;
use App\Http\Controllers\Dashboard\DashboardLiveInboxController;

// House Managmenet  
use App\Http\Controllers\BranchContractDetailsController;
use App\Http\Controllers\BranchInsuranceController;
use App\Http\Controllers\BranchRentController;
use App\Http\Controllers\BranchRentInfoController;
use App\Http\Controllers\BuildingTypeController;
use App\Http\Controllers\BranchExpenseController;
use App\Http\Controllers\RentExtraCostController;
use App\Http\Controllers\HeatingTypeController;
use App\Http\Controllers\ChecklistApartmentController;
use App\Http\Controllers\ChecklistRoomController;
use App\Http\Controllers\CustomerHeatingCircuitController;
use App\Http\Controllers\CustomerRoomDimensionController; 
use App\Http\Controllers\BranchExpenseRentController;
use App\Http\Controllers\BranchExpenseInsuranceController;
use App\Http\Controllers\BranchExpenseOtherCostController;

// Contacts / Globale Search Controller 
use App\Http\Controllers\Contacts\AllContactController;
// Branch Management
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\Branch\BranchAddressController;
// Inquiry Controller 
use App\Http\Controllers\Inquiry\InquiryController;
use App\Http\Controllers\Inquiry\InquiryVerificationController;
use App\Http\Controllers\Inquiry\InquiryTypeController;
use App\Http\Controllers\Inquiry\InquiryCommentController;
use App\Http\Controllers\Inquiry\InquiryReportController;
// Customer Controller s
use App\Http\Controllers\Customer\NewLeadsController;
use App\Http\Controllers\Customer\LeadHistoryController;
use App\Http\Controllers\Customer\CustomerContactPersonController;
use App\Http\Controllers\Customer\CustomerObjectProductModalController;
use App\Http\Controllers\Customer\CustomProcessController;
use App\Http\Controllers\Customer\ImageController;
use App\Http\Controllers\Customer\CustomerCardNoteController;
use App\Http\Controllers\Customer\CustomerHistoryController;
use App\Http\Controllers\Customer\CustomerHistoryImportController;
use App\Http\Controllers\Customer\CustomerNoteController;
use App\Http\Controllers\Customer\CustomerProductInfoController;
use App\Http\Controllers\Customer\LeadActivityLogsController;
use App\Http\Controllers\Customer\PVRoofController;
use App\Http\Controllers\Customer\PVRoofPlanController;
use App\Http\Controllers\Customer\BegFundingsController;
use App\Http\Controllers\Admin\FoerderungController;
use App\Http\Controllers\Customer\CustomerReviewController;
use App\Http\Controllers\Customer\CustomerContextFeedController;

use App\Http\Controllers\Customer\Climate\ClimateStationController;
use App\Http\Controllers\Customer\Climate\ClimateImportController;
use App\Http\Controllers\Customer\Climate\WeatherStationController;


use App\Http\Controllers\Customer\MassManagerController;
use App\Http\Controllers\Customer\Moser\MoserWpImportController;
use App\Http\Controllers\Customer\Moser\MoserWpInvoiceImportController;
use App\Http\Controllers\Customer\Kanban\LeadOverviewController;
use App\Http\Controllers\Customer\Kanban\KanbanPersonalTaskPanelController;
use App\Http\Controllers\Customer\Kanban\LeadStageSubStageController;
use App\Http\Controllers\Customer\Kanban\LeadStageController;
use App\Http\Controllers\Customer\Kanban\LeadReminderController;
use App\Http\Controllers\Customer\Kanban\KanbanLeadTaskController;
use App\Http\Controllers\Customer\Kanban\KanbanCustomerPanelController;
use App\Http\Controllers\Customer\Kanban\LeadStageBulkMoveController;



use App\Http\Controllers\Customer\Offer\OfferWizardController;
use App\Http\Controllers\Customer\Offer\OfferController;
use App\Http\Controllers\Customer\Offer\OfferFolderController;
use App\Http\Controllers\Customer\Offer\OfferDocumentController;
use App\Http\Controllers\Customer\Offer\OfferTemplateController;
use App\Http\Controllers\Customer\Offer\ClipboardController;
use App\Http\Controllers\Customer\Offer\DealMaterialListController;
use App\Http\Controllers\Customer\Offer\OfferDetailsController;
use App\Http\Controllers\Customer\Offer\OfferCommentController;
use App\Http\Controllers\Customer\Offer\OfferKanbanStageController;
use App\Http\Controllers\Customer\Offer\OfferTemplatePickerController;
use App\Http\Controllers\Customer\Offer\OfferSupplierSearchController;
use App\Http\Controllers\Customer\Offer\OfferTemplateSupplierController;
use App\Http\Controllers\Customer\Offer\OfferRoofLayoutConfigurationController;
use App\Http\Controllers\Customer\Offer\OfferPageLibraryController;





use App\Http\Controllers\Customer\Deal\DealController;
use App\Http\Controllers\Customer\Deal\DealInvoiceController;
use App\Http\Controllers\Customer\Deal\DealMeasurementController;
use App\Http\Controllers\Customer\Deal\DealMeasurementMaterialController;
use App\Http\Controllers\Customer\Deal\DealMeasurementImageController;
use App\Http\Controllers\Customer\Maintenance\MaintenanceChecklistController;
use App\Http\Controllers\Customer\Maintenance\CustomerMaintenanceContractController;
use App\Http\Controllers\Customer\ProfitCalculator\ProfitabilityDataController;
use App\Http\Controllers\Customer\ProfitCalculator\ProfitabilityCalculationController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\InvoiceCanvasController;




// Phase and Task Phases 
use App\Http\Controllers\Phase\TaskPhaseController;
use App\Http\Controllers\Phase\TaskSubTaskController;
use App\Http\Controllers\Phase\PhaseCopyController;
use App\Http\Controllers\Phase\PhaseSectionController;
use App\Http\Controllers\Phase\PhaseActivitiesController;
 use App\Http\Controllers\Phase\LeadTaskPhaseManagementController;
use App\Http\Controllers\Phase\LeadStageAdminController;



use App\Http\Controllers\InstallmentPaymentController;
use App\Http\Controllers\OfferGreetingController;

use App\Http\Controllers\Product\ProductWPController;
 use App\Http\Controllers\RentPropertyController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\WPChecklistController; 
use App\Http\Controllers\CustomerMeasureController;
use App\Http\Controllers\BuildingTypeValueController;

use App\Http\Controllers\EmployeeDashboardController;
 
use App\Http\Controllers\TaskToDoController; 
 use App\Http\Controllers\AdminController;
 

use App\Http\Controllers\CustomerPhaseListController;
use App\Http\Controllers\PVToolsController;
use App\Http\Controllers\BitrixController;
use App\Http\Controllers\PlaningController;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\Customer\Offer\OffersController;

use App\Http\Controllers\KnowledgeCategoryController;
use App\Http\Controllers\KnowledgeQuestionController;
// Ticket System Controllers 
use App\Http\Controllers\Ticket\ProblemController;
use App\Http\Controllers\Ticket\TicketImageController;
use App\Http\Controllers\Ticket\TicketTaskController;
use App\Http\Controllers\Ticket\TicketReportController;
use App\Http\Controllers\Ticket\TicketReportCommentController;
use App\Http\Controllers\Ticket\ProblemCommentController;
use App\Http\Controllers\Ticket\TicketFileController;
use App\Http\Controllers\Ticket\ErrorController;
use App\Http\Controllers\Ticket\TicketAppointmentController;
use App\Http\Controllers\Ticket\TicketEmployeeController;

// Project Management Controllers   
use App\Http\Controllers\ProjectControlPersonController;
use App\Http\Controllers\ProjectTaskCommentController; 
use App\Http\Controllers\ProjectTaskAttachmentController; 

// Appointment Controller 
use App\Http\Controllers\Appointment\MainAppointmentController;
use App\Http\Controllers\Appointment\MainAppointmentReminderController;
use App\Http\Controllers\Appointment\CustomerMainAppointmentController;
use App\Http\Controllers\Appointment\AppointmentReportController;

// Task Management 
use App\Http\Controllers\Task\PersonalTaskController;
use App\Http\Controllers\Task\PersonalTaskStepController;
use App\Http\Controllers\Task\PersonalTaskAttachmentController;
use App\Http\Controllers\Task\PersonalTaskCommentController;
use App\Http\Controllers\Task\PersonalTaskBoardController;
use App\Http\Controllers\Task\GeneralTaskController;
use App\Http\Controllers\Task\GeneralTaskStepController;

use App\Http\Controllers\DashboardIconController;

// Employee Managemnet 
// 1.Position - Qualification - Hirarkey Controllers
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeCapacityStateController;
use App\Http\Controllers\Employee\Profile\EmployeeAddressController;
use App\Http\Controllers\Employee\Profile\CountryController;
use App\Http\Controllers\Employee\Profile\LeaveController;
use App\Http\Controllers\Employee\Profile\LeaveDayController;
use App\Http\Controllers\Employee\Profile\EmployeeClothController;
use App\Http\Controllers\Employee\Profile\EmployeeDocumentController;
use App\Http\Controllers\Employee\Profile\EmployeeLicenseController;
use App\Http\Controllers\Employee\Profile\EmployeePostcodeListController;
use App\Http\Controllers\Employee\Profile\EmployeeRecurringLeaveController;
use App\Http\Controllers\Employee\Profile\EmployeeSickController;
use App\Http\Controllers\Employee\Profile\HolidayBirthdayWidgetController;
use App\Http\Controllers\Employee\Profile\PublicHolidayController;
use App\Http\Controllers\Employee\Profile\HolidayController;
use App\Http\Controllers\Employee\Profile\SalaryController;
use App\Http\Controllers\Employee\Profile\SalarySheetController;
use App\Http\Controllers\Employee\Profile\SkillController;
use App\Http\Controllers\Employee\Profile\EmergencyContactController;
use App\Http\Controllers\Employee\Profile\OtherSkillController;
use App\Http\Controllers\Employee\Profile\TeamController;
use App\Http\Controllers\Employee\Profile\ContractTypeController;
use App\Http\Controllers\Employee\Profile\FurtherEducationController;
use App\Http\Controllers\Employee\Profile\LanguagesController;
use App\Http\Controllers\Employee\Profile\TaxController;
use App\Http\Controllers\Employee\Department\DepartmentController;
use App\Http\Controllers\Employee\Department\DepartmentChartController;
use App\Http\Controllers\Employee\Department\DepartmentPositionController;
use App\Http\Controllers\Employee\Position\PositionController;
use App\Http\Controllers\Employee\Position\ProductPositionController;
use App\Http\Controllers\Employee\Position\QualificationController;
use App\Http\Controllers\Employee\TimeManagement\TimeManagementController;
use App\Http\Controllers\Employee\Calendar\PersonalSettingsController;
use App\Http\Controllers\Employee\Note\PersonalNoteReminderController;
use App\Http\Controllers\Employee\Note\PersonalNoteController;
use App\Http\Controllers\Employee\Note\NoteCategoryController;
use App\Http\Controllers\Employee\Position\EmployeeOrganizationController;



use App\Http\Controllers\Report\DailyReportController;
use App\Http\Controllers\Report\DailyReportWorkPlaceController;
use App\Http\Controllers\Report\DailyReportNoteController;
use App\Http\Controllers\Report\DailyReportAttachmentController;
use App\Http\Controllers\Report\OverdueCenterController;


// Wordpress API 
use App\Http\Controllers\Wordpress\FusionFormSubmissionController;
use App\Http\Controllers\FusionWebhookController;

// Email Controller 
use App\Http\Controllers\Email\LeadEmailAccountsController;
use App\Http\Controllers\Email\LeadEmailDomainFilterController;
use App\Http\Controllers\Email\LeadEmailReaderController;
use App\Http\Controllers\Email\LeadsController;
use App\Http\Controllers\Email\EmailConfigurationController;

//Article Groups COntroller 
use App\Http\Controllers\ArticleGroup\ArticleGroupController;
use App\Http\Controllers\ArticleGroup\SubArticleGroupController;

// Products Controllers 
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTypeController;
use App\Http\Controllers\Product\ProductDifferenceController;
use App\Http\Controllers\Product\ProductFavoriteListController;
use App\Http\Controllers\Product\StampArticleListController;
use App\Http\Controllers\Product\ProductDescriptionController;
use App\Http\Controllers\Product\ProductInstallationCaseController;
use App\Http\Controllers\Product\ProductDocumentsController;
use App\Http\Controllers\Product\ProductImageController;
use App\Http\Controllers\Product\DiscountGroupController;
use App\Http\Controllers\Product\ProductFormulaController;
use App\Http\Controllers\Product\ProductImageCsvImportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Http\Controllers\Product\ProductPVController;
use App\Http\Controllers\Product\ProductCsvImportController;
use App\Http\Controllers\Product\PurchaseRequestController;
use App\Http\Controllers\Product\TemperatureController;
use App\Http\Controllers\Product\TilesController;
use App\Http\Controllers\Product\MeasureController;
use App\Http\Controllers\Product\Stage\StageController;
use App\Http\Controllers\Product\PV\RadiatorController;
use App\Http\Controllers\Product\PV\BatteryController;
use App\Http\Controllers\Product\PV\BatterySystemController;
use App\Http\Controllers\Product\PV\BatteryInverterController;
use App\Http\Controllers\Product\PV\ElectricVehicleController;
use App\Http\Controllers\Product\PV\PowerOptimizerController;
use App\Http\Controllers\Product\PV\InverterController;
use App\Http\Controllers\Product\PV\BackupGeneratorController;
use App\Http\Controllers\Product\PV\RadiatorInstallationController;

use App\Http\Controllers\Product\Brand\BrandController;
use App\Http\Controllers\Product\Brand\BrandDepartmentController;
use App\Http\Controllers\Product\Brand\ExternalPersonalController;
use App\Http\Controllers\Product\Brand\ExternalDepartmentsController;
use App\Http\Controllers\Product\Distributor\DistributorController;
use App\Http\Controllers\Product\Distributor\DistributorPriceController;
use App\Http\Controllers\Product\Distributor\DistributorDepartmentController;
use App\Http\Controllers\Product\IDS\gconline\IdsController;
use App\Http\Controllers\Product\IDS\gconline\IdsSearchController;
use App\Http\Controllers\Product\IDS\SupplierConnectionController;

use App\Http\Controllers\Product\MasterSet\TaskWizardController;
use App\Http\Controllers\Product\MasterSet\MasterSetController;
use App\Http\Controllers\Product\MasterSet\MasterSetComponentDescriptionController;
use App\Http\Controllers\Product\MasterSet\MasterSetGroupController;
use App\Http\Controllers\Product\MasterSet\MasterSetDistributorCompareController;
use App\Http\Controllers\Product\MasterSet\MasterSetCartController;

// Inventory Management 
use App\Http\Controllers\Inventory\AssetInstallmentController;
use App\Http\Controllers\Inventory\AssetController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\InventoryRequestOutController;
use App\Http\Controllers\Inventory\DeliveryNotes\GoodsReceiptController;
use App\Http\Controllers\Inventory\DeliveryNotes\DeliveryNoteController;
use App\Http\Controllers\Inventory\DeliveryNotes\DeliveryNoteImageController;
use App\Http\Controllers\Inventory\AssetSetController;
use App\Http\Controllers\Inventory\MachineController;
use App\Http\Controllers\Inventory\MachineServiceController;
use App\Http\Controllers\Inventory\MachineInstallmentController;


use App\Http\Controllers\EconomicCalculationController;
use App\Http\Controllers\Api\ApiLinkController;
use App\Http\Controllers\LeadProductChecklistValueController;

use App\Http\Controllers\CustomerReportController;
use App\Http\Controllers\CustomerReportCommentController;
use App\Http\Controllers\CustomerSuggestEmployeeController;
 

// Laravel Reverb Chat
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatGroupController;
use App\Http\Controllers\Chat\ChatAttachmentController;
use App\Http\Controllers\Chat\PinnedPrivateChatController;
use App\Http\Controllers\Chat\Learning\LearningTopicController;
use App\Http\Controllers\Chat\Feed\NewsFeedController;
use App\Http\Controllers\BreakingNews\BreakingNewsController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationListController;
use App\Http\Controllers\Chat\ChatMentionController;


use App\Http\Controllers\CustomerStageController;
// AI Chat Bot 
use App\Http\Controllers\Ai\ChatPageController;
use App\Http\Controllers\Ai\ShareController;
use App\Http\Controllers\Ai\AiMessageController; 


use App\Http\Controllers\RealtimeNotificationDebugController;
use App\Http\Controllers\ProjectTimeRequestController;
use App\Http\Controllers\LeadImportController;

use App\Http\Controllers\AdminPersonalNoteController;
use App\Http\Controllers\MobileCalendarController;

use App\Http\Controllers\NewLeadsInvoiceController;
use App\Http\Controllers\Planner\PlannerPlanController; 
use App\Http\Controllers\Planner\PlannerItemStateController;
use App\Http\Controllers\Planner\PlannerMasterSetController;
use App\Http\Controllers\Planner\PlannerAttendanceController;
use App\Http\Controllers\Planner\PlannerEmployeeApiController;
use App\Http\Controllers\Planner\PlannerApiAuthController;




use App\Http\Controllers\CostingSetController;


use App\Http\Controllers\CustomProcessStageController;
use App\Http\Controllers\WebsiteController;
 
use App\Http\Controllers\ProjectTimelineController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

 

 Route::get('/api/live-activities/recent', [LeadActivityLogsController::class, 'recentLiveActivities']);
 Route::post('/api/live-activities/save-filters', [LeadActivityLogsController::class, 'saveLiveActivityFilters']);
 Route::post('/api/live-activities/{id}/read', [LeadActivityLogsController::class, 'markAsRead']);

Route::get('/fix-notes', function () {
        // 1. Find all product notes that are currently "Hidden" (NULL list ID)
    $notes = CustomerNote::where('type', 'product')
                ->whereNull('lead_product_list_id')
                ->whereNotNull('product_id') // Must have a generic product ID
                ->get();

    $count = 0;

    foreach ($notes as $note) {
        // 2. Find the project (Alternative)
        $alternative = LeadAlternativeAdd::find($note->alternative_id);

        if ($alternative) {
            // 3. Find the specific product in that project that matches the generic ID
            // We look for the first match.
            $matchingProduct = $alternative->products()
                                ->where('product_id', $note->product_id)
                                ->first();

            if ($matchingProduct) {
                // 4. Link the note to this specific product ID
                $note->lead_product_list_id = $matchingProduct->id;
                $note->save();
                $count++;
            }
        }
    }

    return "Fixed $count notes! You can now delete this route.";
})->middleware('auth');

Route::get('/notAdmin', [AdminController::class, 'notAdmin'])->name('notweb');
// FIX P0-05: Systemhinweis nicht anonym (wird nicht vor dem Login gebraucht).
Route::get('/system-warning/current', [SystemWarningController::class, 'current'])->name('system-warning.current')->middleware('auth');
Route::get('/browser-info', [AdminController::class, 'showDetails'])->name('browser.info');
Route::middleware(['auth'])->get('/api/sidebar-counts', [SidebarCountController::class, 'index'])->name('api.sidebar.counts');
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/widgets/load', [DashboardWidgetController::class, 'load'])->name('widgets.load');
    Route::post('/widgets/save', [DashboardWidgetController::class, 'save'])->name('widgets.save');
    Route::post('/widgets/reset', [DashboardWidgetController::class, 'reset'])->name('widgets.reset');
    Route::get('/widgets/registry', [DashboardWidgetController::class, 'registry'])->name('widgets.registry');  
    Route::get('/shortcuts', [UserDashboardShortcutController::class, 'index'])->name('shortcuts.index');
    Route::get('/shortcuts/available', [UserDashboardShortcutController::class, 'available'])->name('shortcuts.available');
    Route::post('/shortcuts', [UserDashboardShortcutController::class, 'store'])->name('shortcuts.store');
    Route::put('/shortcuts/{shortcut}', [UserDashboardShortcutController::class, 'update'])->name('shortcuts.update');
    Route::post('/shortcuts/reorder', [UserDashboardShortcutController::class, 'reorder'])->name('shortcuts.reorder');
    Route::delete('/shortcuts/{shortcut}', [UserDashboardShortcutController::class, 'destroy'])->name('shortcuts.destroy');
});
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/company/overview', [DashboardCompanyController::class, 'overview'])
        ->name('company.overview');
});
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/employee-status', [DashboardEmployeeStatusController::class, 'index'])
        ->name('employee-status.index');
});
Route::middleware(['auth'])->prefix('dashboard/live-inbox')->name('dashboard.live-inbox.')->group(function () {
    Route::get('/', [DashboardLiveInboxController::class, 'index'])->name('index');
    Route::post('/{id}/read', [DashboardLiveInboxController::class, 'markRead'])->name('read');
    Route::post('/{id}/unread', [DashboardLiveInboxController::class, 'markUnread'])->name('unread');
    Route::post('/read-all', [DashboardLiveInboxController::class, 'markAllRead'])->name('read-all');
});
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/calendar/employees', [DashboardCalendarWidgetController::class, 'employees'])->name('calendar.employees');
    Route::get('/calendar/month', [DashboardCalendarWidgetController::class, 'month'])->name('calendar.month');
    Route::get('/calendar/day', [DashboardCalendarWidgetController::class, 'day'])->name('calendar.day');
    Route::get('/calendar/appointments/{appointment}', [DashboardCalendarWidgetController::class, 'show'])->name('calendar.appointments.show');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/absence-request/data', [DashboardAbsenceRequestController::class, 'data'])->name('dashboard.absence-request.data');
    Route::post('/dashboard/absence-request/store', [DashboardAbsenceRequestController::class, 'store'])->name('dashboard.absence-request.store');
});
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/department/departments', [DashboardDepartmentController::class, 'departments'])->name('department.departments');
    Route::get('/department/overview', [DashboardDepartmentController::class, 'overview'])->name('department.overview');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/system-warning', [SystemWarningController::class, 'index'])->name('system-warning.index');
    Route::post('/system-warning/update', [SystemWarningController::class, 'update'])->name('system-warning.update'); 
    Route::post('/system-warning/toggle', [SystemWarningController::class, 'toggle'])->name('system-warning.toggle');
});
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/garbage', [GarbageController::class, 'index'])->name('garbage.index');
    Route::delete('/garbage/table', [GarbageController::class, 'deleteTable'])->name('garbage.table.delete');
    Route::delete('/garbage/bulk', [GarbageController::class, 'bulkDelete'])->name('garbage.bulk.delete');
    Route::delete('/garbage/all', [GarbageController::class, 'deleteAll'])->name('garbage.all.delete');
});

// GC Online browser return target
Route::post('/ids/callback', [IdsController::class, 'callback'])->name('ids.callback');

// 2. Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/ids/back', [IdsSearchController::class, 'back'])->name('ids.back');
    Route::get('/ids/inline-back', [IdsSearchController::class, 'inlineBack'])->name('ids.inline_back');
    Route::get('/ids/search', [IdsSearchController::class, 'form'])->name('ids.search.form');
    Route::get('/ids/search/inline', [IdsSearchController::class, 'forms'])->name('ids.search.form.inline');
    Route::get('/ids/local-search', [IdsController::class, 'localSearch'])->name('ids.local_search');
    Route::post('/ids/search/inline-forward', [IdsSearchController::class, 'forwardToShopInline'])->name('ids.search.forward.inline');
    Route::get('/admin/offers/folders/{folder}/ids/request-price', [IdsSearchController::class, 'requestPriceForMaterial'])->name('admin.offers.folders.ids.request-price');
    Route::post('/ids/search/forward', [IdsSearchController::class, 'forwardToShop'])->name('ids.search.forward');
    Route::get('/ids/results/{batchId}', [IdsController::class, 'results'])->name('ids.results');
    // Promotion Routes
    Route::get('/ids/promote/{item}', [IdsController::class, 'showPromoteForm'])->name('ids.items.promote.form');
    Route::post('/ids/promote/{item}', [IdsController::class, 'promoteToProduct'])->name('ids.items.promote');
    Route::get('/article-groups/{articleGroup}/sub-groups', [IdsController::class, 'getSubArticleGroups'])->name('article-groups.sub-groups');
});

Route::middleware(['web', 'auth'])
    ->prefix('admin/supplier-connectors')
    ->name('admin.supplier-connectors.')
    ->group(function () {
        Route::get('/', [SupplierConnectionController::class, 'index'])->name('index');
        Route::get('/create', [SupplierConnectionController::class, 'create'])->name('create');
        Route::post('/', [SupplierConnectionController::class, 'store'])->name('store');

        Route::get('/ajax/brands', [SupplierConnectionController::class, 'select2Brands'])->name('ajax.brands');
        Route::get('/ajax/distributors', [SupplierConnectionController::class, 'select2Distributors'])->name('ajax.distributors');
        Route::get('/ajax/article-groups', [SupplierConnectionController::class, 'select2ArticleGroups'])->name('ajax.article-groups');
        Route::get('/ajax/sub-article-groups', [SupplierConnectionController::class, 'select2SubArticleGroups'])->name('ajax.sub-article-groups');

        Route::get('/{supplierConnector}/edit', [SupplierConnectionController::class, 'edit'])->name('edit');
        Route::put('/{supplierConnector}', [SupplierConnectionController::class, 'update'])->name('update');
        Route::delete('/{supplierConnector}', [SupplierConnectionController::class, 'destroy'])->name('destroy');

        Route::post('/{supplierConnector}/test', [SupplierConnectionController::class, 'test'])->name('test');
        Route::post('/{supplierConnector}/duplicate', [SupplierConnectionController::class, 'duplicate'])->name('duplicate');
        Route::post('/{supplierConnector}/apply-preset', [SupplierConnectionController::class, 'applyPreset'])->name('apply-preset');

        Route::get('/{supplierConnector}/open', [SupplierConnectionController::class, 'open'])->name('open');
        Route::get('/{supplierConnector}/search', [SupplierConnectionController::class, 'search'])->name('search');
        Route::post('/{supplierConnector}/forward', [SupplierConnectionController::class, 'forward'])->name('forward');
        Route::get('/{supplierConnector}/latest-logs', [SupplierConnectionController::class, 'latestLogs'])->name('latest-logs');

        Route::get('/{supplierConnector}/logs/{log}/preview', [SupplierConnectionController::class, 'previewReturn'])->name('logs.preview');
        Route::post('/{supplierConnector}/logs/{log}/import', [SupplierConnectionController::class, 'importReturn'])->name('logs.import');

        Route::post('/{supplierConnector}/mappings', [SupplierConnectionController::class, 'storeMapping'])->name('mappings.store');
        Route::put('/mappings/{mapping}', [SupplierConnectionController::class, 'updateMapping'])->name('mappings.update');
        Route::delete('/mappings/{mapping}', [SupplierConnectionController::class, 'destroyMapping'])->name('mappings.destroy');
    });

Route::match(['GET', 'POST'], 'admin/supplier-connectors/{supplierConnector}/return', [SupplierConnectionController::class, 'handleReturn'])
    ->middleware(['web'])
    ->name('admin.supplier-connectors.return');
 
Route::prefix('admin/products')->name('admin.products.')->middleware(['auth'])->group(function () {
    Route::get('/csv-import', [ProductCsvImportController::class, 'index'])->name('csv-import.index');
    Route::post('/csv-import/preview', [ProductCsvImportController::class, 'preview'])->name('csv-import.preview');
    Route::post('/csv-import/confirm', [ProductCsvImportController::class, 'confirm'])->name('csv-import.confirm');
    Route::post('/csv-import/reset', [ProductCsvImportController::class, 'resetPreview'])->name('csv-import.reset');
});
Route::group(['prefix' => 'admin/leads/moser-wp', 'as' => 'admin.leads.moser_wp.', 'middleware' => ['web', 'auth']], function () {
    // The Import Page
    Route::get('/', [MoserWpImportController::class, 'index'])->name('index'); 
    // AJAX: Upload file and preview table
    Route::post('/preview', [MoserWpImportController::class, 'preview'])->name('preview'); 
    // AJAX: Confirm import and save to DB
    Route::post('/store', [MoserWpImportController::class, 'store'])->name('store');
});
 
Route::group(['prefix' => 'admin/leads/moser-wp-invoice', 'as' => 'admin.leads.moser_wp_invoice.', 'middleware' => ['web', 'auth']], function () {
    Route::get('/', [MoserWpInvoiceImportController::class, 'index'])->name('index');
    Route::post('/preview', [MoserWpInvoiceImportController::class, 'preview'])->name('preview');
    Route::post('/store', [MoserWpInvoiceImportController::class, 'store'])->name('store');
});
 
Route::middleware(['auth'])  
->prefix('admin')
->name('admin.')
->group(function () {
    Route::get('leads/import', [LeadImportController::class, 'index'])->name('leads.import');
    Route::post('leads/import/preview', [LeadImportController::class, 'preview'])->name('leads.import.preview');
    Route::post('leads/import/confirm', [LeadImportController::class, 'confirm'])->name('leads.import.confirm');
});

Route::middleware(['web','auth'])
->prefix('admin/tools/imports')
->name('admin.imports.')
->group(function () {
    Route::get('customer-histories', [CustomerHistoryImportController::class, 'create'])->name('customer_histories.create');
    Route::post('customer-histories', [CustomerHistoryImportController::class, 'store'])->name('customer_histories.store');
});
 

Route::get('/employee/qr/create', [WebsiteController::class, 'createQR'])->name('employee.qr.create');
Route::get('/employee/qr/code/reader/{type}', [WebsiteController::class, 'readQR'])->name('employee.qr.reader');
Route::post('/employee/qr/code/check/', [WebsiteController::class, 'checkQR'])->name('employee.qr.check');
Route::get('/employee/qr/code/plan/{employee_id}', [WebsiteController::class, 'getPlan'])->name('employee.qr.get.plan');
Route::get('/employee/qr/code/form/{employee_id}/{type}', [WebsiteController::class, 'getPlanForm'])->name('employee.qr.get.plan.form');
Route::get('/employee/qr/code/checkout', [WebsiteController::class, 'autoCheckout'])->name('employee.qr.check.out');
Route::get('/employee/qr/code/employee/checkout', [WebsiteController::class, 'autoCheckoutEmp'])->name('employee.qr.check.out.emp');
Route::post('/employee/qr/employee/start/work', [WebsiteController::class, 'startWork'])->name('employee.qr.start.work');
Route::post('/receive-fusion-form', [FusionFormSubmissionController::class, 'store']);
Route::get('lead/email/api/{id}', [WebsiteController::class, 'getEmailDetails'])->middleware('auth');

Route::get('employee/qr/get/daily/report/{daily_report_id}/{daily_times_id}', [WebsiteController::class, 'getTime'])->name('employee.qr.get.daily.report');
Auth::routes(['register' => false]);

Route::get('/', [EmployeeDashboardController::class, 'index'])->name('home');
Route::get('/home', [EmployeeDashboardController::class, 'index'])->name('dashbaord'); 
Route::match(['get', 'post'], '/dashboard/load-tab', [EmployeeDashboardController::class, 'loadTabContent'])->name('dashboard.tab');
Route::get('/dashboard/tab-counts', [EmployeeDashboardController::class, 'getTabCounts']);
Route::get('/my/due-today', [EmployeeDashboardController::class, 'getDueToday'])
    ->middleware('auth')
    ->name('my.due.today'); 
Route::post('/my/mark-done', [EmployeeDashboardController::class, 'markAsDone'])
    ->middleware('auth')
    ->name('my.mark.done'); 
Route::get('/employee/dashboard/hr-widget', [EmployeeDashboardController::class, 'hrWidget'])
    ->name('employee.dashboard.hr_widget')
    ->middleware('auth');
Route::get('/employee/dashboard/personal-hours-chart', [EmployeeDashboardController::class, 'personalHoursChart'])
    ->middleware('auth')
    ->name('employee.dashboard.personal_hours_chart');
Route::get('/employee/dashboard/mini-analytics-chart', [EmployeeDashboardController::class, 'miniAnalyticsChart'])
    ->middleware('auth')
    ->name('employee.dashboard.mini_analytics_chart');
Route::post('/my/save-appointment-report', [EmployeeDashboardController::class, 'saveAppointmentReport']);
Route::get('/quick/departments', [EmployeeDashboardController::class, 'quickDepartments'])->name('quick.departments');
Route::get('/employee/my-dashboard-data', [EmployeeDashboardController::class, 'getMyData'])->name('employee.my_data');
Route::get('/employee/dashboard/overdue48h-partial', [EmployeeDashboardController::class, 'overdue48hPartial'])->name('employee.dashboard.overdue48h.partial');

Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {
        Route::get('/reports', [OverdueCenterController::class, 'reportIndex'])->name('report.index');
        Route::get('/overdue-center', [OverdueCenterController::class, 'index'])->name('overdue.index');
        Route::get('/overdue-center/fetch', [OverdueCenterController::class, 'fetch'])->name('overdue.fetch');
        Route::get('/overdue-center/history', [OverdueCenterController::class, 'history'])->name('overdue.history');
        Route::get('/overdue-center/reports', [OverdueCenterController::class, 'reportsList'])->name('overdue.reports.list');
        Route::post('/overdue-center/reports', [OverdueCenterController::class, 'reportStore'])->name('overdue.reports.store');
        Route::post('/overdue-center/reports/skip', [OverdueCenterController::class, 'reportSkip'])->name('overdue.skip');
        Route::post('/overdue-center/reports/bulk', [OverdueCenterController::class, 'reportBulkStore'])->name('overdue.reports.bulk');
        Route::get('/overdue-center/recent', [OverdueCenterController::class, 'recentReportsIndex'])->name('overdue-center.reports.index');
        Route::get('/overdue-center/reports/fetch', [OverdueCenterController::class, 'recentReportsFetch'])->name('overdue-center.reports.fetch');
        Route::post('/overdue-center/report-store', [OverdueCenterController::class, 'reportStore'])->name('overdue-center.report.store');
        Route::get('/recent-reports/record-reports', [OverdueCenterController::class, 'recentReportsRecordReports'])->name('recent-reports.record-reports');
        Route::get('/recent-reports/source-details', [OverdueCenterController::class, 'recentReportSourceDetails'])->name('recent-reports.source-details');
        Route::post('/overdue/reminders/upsert', [OverdueCenterController::class, 'reminderUpsert'])->name('overdue.reminders.upsert');
        Route::post('/overdue/reminders/bulk', [OverdueCenterController::class, 'reminderBulkUpsert'])->name('overdue.reminders.bulk');
        Route::get('/overdue-report-notifications', [OverdueCenterController::class, 'reportNotifications'])->name('overdue.reports.notifications');
        Route::post('/overdue-report-notifications/{report}/read', [OverdueCenterController::class, 'markReportNotificationRead'])->name('overdue.reports.notifications.read');
        Route::post('/overdue-report-notifications/read-all', [OverdueCenterController::class, 'markAllReportNotificationsRead'])->name('overdue.reports.notifications.readAll');
        Route::get('/recent-reports/employee-summary', [OverdueCenterController::class, 'recentReportsEmployeeSummary'])->name('recent-reports.employee-summary');
    });


// FIX P0-01: interne Fusion-Admin-Endpunkte hinter auth (anonymes Leck der Website-Leads).
// Der eingehende Webhook /receive-fusion-form (oben, ausserhalb dieser Gruppe) bleibt public,
// ist aber per Shared-Secret X-Fusion-Form-Token geschuetzt.
Route::group(['middleware' => ['web', 'auth']], function(){
    Route::get('/admin/fusion-forms', [FusionFormSubmissionController::class, 'index'])->name('fusion.forms.index');
    Route::get('/admin/fusion-forms/{id}', [FusionFormSubmissionController::class, 'show'])->name('fusion.forms.show');
    Route::get('/admin/fusion-forms/import', [FusionFormSubmissionController::class, 'importFromGoneo'])->name('fusion.forms.import');
    Route::get('/admin/fusion-forms/import/ajax', [FusionFormSubmissionController::class, 'ajaxImportFromGoneo'])->name('fusion.forms.import.ajax');

    Route::post('/admin/fusion/sync/forms', [FusionFormSubmissionController::class, 'syncForms'])->name('fusion.sync.forms');
    Route::post('/admin/fusion/sync/fields', [FusionFormSubmissionController::class, 'syncFields'])->name('fusion.sync.fields');
    Route::post('/admin/fusion/sync/entries', [FusionFormSubmissionController::class, 'syncEntries'])->name('fusion.sync.entries');
    Route::post('/admin/fusion/sync/submissions', [FusionFormSubmissionController::class, 'syncSubmissions'])->name('fusion.sync.submissions');
    Route::get('fusion/list', [FusionFormSubmissionController::class, 'listForms'])->name('fusion.forms.list');
    Route::get('/admin/fusion-forms/entries/{form_id}', [FusionFormSubmissionController::class, 'getEntriesByForm']); 
    Route::post('/fusion/webhook/ajax', [FusionFormSubmissionController::class, 'webhookAjax'])->name('fusion.webhook.ajax');
    Route::post('/fusion/webhook/ajax', [FusionWebhookController::class, 'handleAjax'])->name('fusion.webhook.ajax');
     Route::post('/fusion/import/one', [FusionFormSubmissionController::class, 'importFusionEntryToInquiry'])->name('fusion.import.one');
    Route::post('/fusion/import/single', [FusionFormSubmissionController::class, 'importSingle'])->name('fusion.import.single');
    Route::post('/fusion/import/all', [FusionFormSubmissionController::class, 'importAll'])->name('fusion.import.all');
});

// Dashboards 
Route::group(['middleware' => 'web'], function () {
    Route::get('/employee_dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/employee_dashboard/mobile', [EmployeeDashboardController::class, 'mobile'])->name('employee.dashboard.mobile');
    Route::get('/get-weather-data', [EmployeeDashboardController::class, 'getWeatherData']);
    Route::post('dashboard/save/order', [DashboardIconController::class, 'saveOrder'])->name('dashboard.saveOrder');  
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.info');
    Route::get('/branch_create', [BranchController::class, 'create'])->name('branch.create');
    Route::get('/branch_destroy/{id}', [BranchController::class, 'destroy'])->name('branch.destroy');

    Route::post('/branch_save', [BranchController::class, 'store'])->name('branch.store');
    Route::post('/branch_update', [BranchController::class, 'update'])->name('branch.update');
    Route::get('branch_profile/{id}', [BranchController::class, 'profile'])->name('branch.profile');
    Route::get('branch_profile/status/{id}', [BranchController::class, 'active'])->name('branch.profile.active');
    Route::get('offer/branches', [BranchController::class, 'offerIndex'])->name('offer.branches.index');
    Route::post('/branch_address_update', [BranchController::class, 'addressUpdate'])->name('branch.address.update');
    // Update a specific branch (using POST for easy file upload)
    Route::post('offer/branches/{id}', [BranchController::class, 'offerUpdate'])->name('offer.branches.update');
});

//Branch Frenchise 
Route::group(['middleware' => 'web'], function () {
    Route::get('/branch_address_view', [BranchAddressController::class, 'index'])->name('branch.address.view');  
    Route::post('/store-address-branch', [BranchAddressController::class, 'store'])->name('branch.address.store');

    Route::get('/branch_address_destroy/{id}', [BranchAddressController::class, 'destroy'])->name('branch.address.destroy');
    Route::get('/branch_address_edit/{id}', [BranchAddressController::class, 'edit'])->name('branch.address.edit');
    Route::post('/branch_address_update', [BranchAddressController::class, 'update'])->name('branch.address.update');

});

/*
|--------------------------------------------------------------------------
| Branch / House Expense Management
|--------------------------------------------------------------------------
| Keeps old URLs working and adds AJAX endpoints for dashboard, filters,
| pagination, detail drawer, rents, insurances and other branch costs.
*/


Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Branch Expense main list
    |--------------------------------------------------------------------------
    */
    Route::get('/branch_expense', [BranchExpenseController::class, 'index'])->name('branch.expense');
    Route::get('/branch_expense/data', [BranchExpenseController::class, 'data'])->name('branch.expense.data');
    Route::get('/branch_expense/analytics', [BranchExpenseController::class, 'analytics'])->name('branch.expense.analytics');
    Route::get('/branch_expense/{branchExpense}/show', [BranchExpenseController::class, 'show'])->name('branch.expense.show');
    Route::post('/branch_expense_save', [BranchExpenseController::class, 'store'])->name('branch.expense.store');
    Route::post('/branch_expense_update', [BranchExpenseController::class, 'update'])->name('branch.expense.update');
    Route::get('/branch_expense_delete/{branchExpense}', [BranchExpenseController::class, 'destroy'])->name('branch.expense.destroy');

    /*
    |--------------------------------------------------------------------------
    | Branch Expense profile pages. These are separate blades, not drawers.
    |--------------------------------------------------------------------------
    */
    Route::get('/branch_expense/{branchExpense}/profile/{tab?}', [BranchExpenseController::class, 'profile'])
        ->whereIn('tab', ['overview', 'rents', 'insurances', 'other-costs', 'linked'])
        ->name('branch.expense.profile');

    Route::get('/branch_expense/{branchExpense}/profile-data', [BranchExpenseController::class, 'profileData'])
        ->name('branch.expense.profile.data');

    /* Legacy details URL now opens profile page */
    Route::get('/expense_details/{id}/{branch}/{year}', [BranchExpenseController::class, 'legacyDetails'])
        ->name('branch.expense.details');

    /* Rent / House objects */
    Route::get('/branch_expense/{branchExpense}/rents', [BranchExpenseRentController::class, 'index'])->name('branch.expense.rents.data');
    Route::post('/branch_expense/{branchExpense}/rents', [BranchExpenseRentController::class, 'store'])->name('branch.expense.rents.store');
    Route::get('/branch_expense/{branchExpense}/rents/{branchRent}/show', [BranchExpenseRentController::class, 'show'])->name('branch.expense.rents.show');
    Route::post('/branch_expense/{branchExpense}/rents/{branchRent}/update', [BranchExpenseRentController::class, 'update'])->name('branch.expense.rents.update');
    Route::get('/branch_expense/{branchExpense}/rents/{branchRent}/delete', [BranchExpenseRentController::class, 'destroy'])->name('branch.expense.rents.destroy');

    /* Insurance */
    Route::get('/branch_expense/{branchExpense}/insurances', [BranchExpenseInsuranceController::class, 'index'])->name('branch.expense.insurances.data');
    Route::post('/branch_expense/{branchExpense}/insurances', [BranchExpenseInsuranceController::class, 'store'])->name('branch.expense.insurances.store');
    Route::get('/branch_expense/{branchExpense}/insurances/{branchInsurance}/show', [BranchExpenseInsuranceController::class, 'show'])->name('branch.expense.insurances.show');
    Route::post('/branch_expense/{branchExpense}/insurances/{branchInsurance}/update', [BranchExpenseInsuranceController::class, 'update'])->name('branch.expense.insurances.update');
    Route::get('/branch_expense/{branchExpense}/insurances/{branchInsurance}/delete', [BranchExpenseInsuranceController::class, 'destroy'])->name('branch.expense.insurances.destroy');

    /* Other branch / house costs */
    Route::get('/branch_expense/{branchExpense}/other-costs', [BranchExpenseOtherCostController::class, 'index'])->name('branch.expense.other.data');
    Route::post('/branch_expense/{branchExpense}/other-costs', [BranchExpenseOtherCostController::class, 'store'])->name('branch.expense.other.store');
    Route::get('/branch_expense/{branchExpense}/other-costs/{otherCost}/show', [BranchExpenseOtherCostController::class, 'show'])->name('branch.expense.other.show');
    Route::post('/branch_expense/{branchExpense}/other-costs/{otherCost}/update', [BranchExpenseOtherCostController::class, 'update'])->name('branch.expense.other.update');
    Route::get('/branch_expense/{branchExpense}/other-costs/{otherCost}/delete', [BranchExpenseOtherCostController::class, 'destroy'])->name('branch.expense.other.destroy');
});

//NEW LEADS CRUD
Route::group(['middleware'   =>  'web'], function(){
    Route::get('/new_lead_create', [NewLeadsController::class, 'create'])->name('new.lead.create');
    Route::get('/admin/roofs/partial/{index}', [NewLeadsController::class, 'getRoofPartial']);
    Route::get('/admin/roofs/partial-edit/{index}', [NewLeadsController::class, 'getRoofPartialEdit']);
    Route::get('/admin/roofs/partial-edit-profile/{index}', [NewLeadsController::class, 'getRoofPartialEditProfile']);
    Route::get('/customer/{id}/quick-sidebar', [NewLeadsController::class, 'getQuickSidebarInfo'])->name('customer.quick.sidebar');
    Route::get('/new_lead_product/{id}', [NewLeadsController::class, 'product'])->name('new.lead.product');
    Route::get('/new_lead_details_edit/{id}', [NewLeadsController::class, 'details_edit'])->name('new.lead.details.edit');
    Route::post('/new_lead_details_update', [NewLeadsController::class, 'details_update'])->name('new.lead.details.update');
    Route::get('/new_lead_edit/{id}/{alternative}', [NewLeadsController::class, 'edit'])->name('new.lead.edit');
    Route::prefix('lead/objects')->name('lead.objects.')->group(function () {
        Route::post('/{object}/junk', [NewLeadsController::class, 'junkObject'])->name('junk');
        Route::post('/{object}/restore-junk', [NewLeadsController::class, 'restoreJunkObject'])->name('restore-junk');
        Route::delete('/{object}', [NewLeadsController::class, 'deleteObject'])->name('delete');
    });
    Route::get('/customers/{customer}/history', [LeadHistoryController::class, 'show'])->name('customers.history.show');
    Route::get('/new_lead_responsible/{customer}/{alternative}', [NewLeadsController::class, 'getResponsible'])->name('new.lead.get.responsible');
    Route::post('/new_lead_update', [NewLeadsController::class, 'update'])->name('new.lead.update');
    Route::post('/new_lead_save', [NewLeadsController::class, 'store'])->name('new.lead.store'); 
    Route::get('/new_lead_view', [NewLeadsController::class, 'index'])->name('new.lead.view');
    Route::get('/new_lead_profile/{id}', [NewLeadsController::class, 'view'])->name('new.lead.profile');
    Route::get('/customers/{customer}/price-history',[NewLeadsController::class, 'priceHistoryForCustomer'])->name('customers.price-history');
    Route::get('lead/customer-profile-feed/{id}', [NewLeadsController::class, 'customerProfileFeed'])->name('lead.customer.profile-feed');
    Route::get( '/lead/customer-feed/{customerId}', [NewLeadsController::class, 'customerFeed'])->name('lead.customerFeed');
    Route::get('/customer/partial/{customer_id}/{alternative_id}/{product}/{section}', [NewLeadsController::class, 'loadSectionPartial']);
    Route::post('/admin/offers/quick-open', [NewLeadsController::class, 'quickOpen'])->name('admin.offers.quick-open');
    Route::get('/customer/partial/{customer_id}/{alternative_id}/{product_id}/angebote', [NewLeadsController::class, 'loadPartial'])->name('customer.angebote.partial');
    Route::post('/new_lead_delete/{id}', [NewLeadsController::class, 'destroyWithReason'])->name('leads.delete.reason'); 
    Route::post('/lead_junk/{id}', [NewLeadsController::class, 'junk'])->name('leads.junk.reason'); 
    Route::post('/lead_unjunk/{id}', [NewLeadsController::class, 'unjunk'])->name('leads.unjunk.reason');
    Route::get('/customer/alternative/partials/{customer_id}/{alternative_id}/{product_id}/{section}', [NewLeadsController::class, 'loadAlternativePartials'])->name('lead.partial.load');
    Route::post('/new_lead_profile/alternative/object/save', [NewLeadsController::class, 'saveObjectData'])->name('alternative.object.update');
    Route::get('/new-leads/neighbor', [NewLeadsController::class, 'neighbor'])->name('new_leads.neighbor');
    Route::get('/new-leads/neighbor/data', [NewLeadsController::class, 'neighborData'])->name('new_leads.neighbor.data');
    Route::get('/new-leads/neighbor-products', [NewLeadsController::class, 'neighborProducts'])->name('new_leads.neighbor_products');
    Route::get('/new-leads/{id}/history-feed', [NewLeadsController::class, 'loadHistoryFeed'])->name('new_leads.history_feed');
    Route::get('/lead_pv_edit/{customer}/{alternative}', [NewLeadsController::class, 'pv_edit'])->name('lead.pv.edit');
    Route::get('/new_lead_profile_object/{id}/{alternative}', [NewLeadsController::class, 'object_profile'])->name('new.lead.profile.object');
    Route::post('/update-field-pv', [NewLeadsController::class, 'updateFieldPV'])->name('update-field.pv');
    Route::get('/new_lead_delete/{id}', [NewLeadsController::class, 'destroy'])->name('new.lead.delete');
    Route::get('/get_customer_details/{id}', [NewLeadsController::class, 'getCustomer'])->name('new.lead.get.customer');
    Route::get('/lead/get/products/{id}/{alternative_id}', [NewLeadsController::class, 'getCustomerProduct'])->name('lead.get.products');
    Route::delete('/lead/product-list/delete', [NewLeadsController::class, 'deleteProduct'])->name('lead.products.delete');
    Route::post('lead/department/employees', [NewLeadsController::class, 'getLeadEmployee'])->name('lead.department.employees');
    Route::post('/lead/product-list/store', [NewLeadsController::class, 'saveProduct'])->name('lead.products.save');
    Route::post('/lead/products/update', [NewLeadsController::class, 'updateProduct'])->name('lead.products.update');
    Route::get('/lead/roof/view/{customer}/{alternative}', [PVRoofController::class, 'index'])->name('lead.roof.view');
    Route::delete('/lead/roof/delete/{roof_id}', [PVRoofController::class, 'destroy'])->name('lead.roof.delete');
    Route::post('/lead/roof/store', [PVRoofController::class, 'store'])->name('lead.roof.store');
    Route::post('/lead/roof/update', [PVRoofController::class, 'update'])->name('lead.roof.update');
    Route::post('/lead/roof/edit', [PVRoofController::class, 'edit'])->name('lead.roof.edit');
    Route::get('/search-employees', [NewLeadsController::class, 'searchEmployees']);
    Route::post('/checkEmployeeAvailability', [NewLeadsController::class, 'checkEmployeeAvailability']);
    Route::post('/getEmployees', [NewLeadsController::class, 'getEmployee']);
    Route::get('/employee/{id}/main-department', [EmployeeController::class, 'getMainDepartment']);
    Route::patch('/employee_passcode/{id}', [EmployeeController::class, 'updatePasscode']);
    Route::post('/employees/generate-all-passcodes', [EmployeeController::class, 'generateAllPasscodes'])->name('emp.generate_all');
    Route::post('/lead_product_list', [NewLeadsController::class, 'product_list']);
    Route::get('/lead_product_lists', [NewLeadsController::class, 'product_list']);
    Route::get('/lead_qualified/{id}',[NewLeadsController::class, 'qualified'])->name('lead.qualified');
    Route::post('/delete-responsible',[NewLeadsController::class, 'deleteResponsible'])->name('delete.responsible');
    Route::post('/saveSelectedEmployees', [NewLeadsController::class, 'saveSelectedEmployees'])->name('save.selectedEmployees');
    Route::post('/saveSelectedEmployee', [NewLeadsController::class, 'saveSelectedEmployee'])->name('save.selectedEmployee');
    Route::get('/lead_new_sort', [NewLeadsController::class, 'new'])->name('lead.sort.new');
    Route::get('/lead_qualified_sort', [NewLeadsController::class, 'qualified_sort'])->name('lead.sort.qualified');
    Route::get('/lead_not_qualified_sort', [NewLeadsController::class, 'not_qualified_sort'])->name('lead.sort.not.qualified');
    Route::get('/lead_incomplete_sort', [NewLeadsController::class, 'incomplete_sort'])->name('lead.sort.incomplete');
    Route::get('/lead_junk_sort', [NewLeadsController::class, 'junk_sort'])->name('lead.sort.junk');
    Route::get('/lead_junk/{id}', [NewLeadsController::class, 'junk'])->name('lead.junk');
    Route::get('/lead_unjunk/{id}', [NewLeadsController::class, 'unjunk'])->name('lead.unjunk');
    Route::get('/lead_junks', [NewLeadsController::class, 'junks'])->name('lead.junks');
    Route::get('/new_leads', [NewLeadsController::class, 'new_lead'])->name('new.leads');
    Route::get('/my_leads', [NewLeadsController::class, 'my_lead'])->name('my.leads');
    Route::get('/wating_leads', [NewLeadsController::class, 'waiting_leads'])->name('waiting.loop.leads');
    Route::get('/deleted_leads', [NewLeadsController::class, 'deleted_lead'])->name('deleted.leads');
    Route::get('/restore_leads/{id}', [NewLeadsController::class, 'restore'])->name('restore.leads');
    Route::get('/new_object/{id}', [NewLeadsController::class, 'new_object'])->name('new.object.leads');
    Route::post('/new_object_store', [NewLeadsController::class, 'object_store'])->name('store.object.leads');
    Route::get('/customer/phase/get/{customer}/{alternative}/{product}', [NewLeadsController::class, 'get_phase'])->name('lead.phase.get.status');
    Route::post('/lead_reason_update/{lead}', [NewLeadsController::class, 'updateReason'])->name('leads.reason.update');
    Route::get('/calendar-settings', [PersonalSettingsController::class, 'get'])->name('calendar.settings.get');
    Route::post('/calendar-settings/save', [PersonalSettingsController::class, 'save'])->name('calendar.settings.save');
    Route::get('/new-leads/sidebar-counts', [NewLeadsController::class, 'sidebarCounts'])->name('new-leads.sidebar-counts');

    Route::get('/customer-profile/workflow/stages',[NewLeadsController::class, 'customerProfileWorkflowConfig'])->name('customer.profile.workflow.stages');
    Route::post('/customer-profile/lead-products/{leadProduct}/move-stage',[NewLeadsController::class, 'customerProfileMoveStage'])->name('customer.profile.lead-products.move-stage');
    Route::post('/customer-profile/lead-products/{leadProduct}/move-product-stage-forward',[NewLeadsController::class, 'customerProfileMoveProductStageForward'])->name('customer.profile.lead-products.move-product-stage-forward');

    // Budget FUnd slidebar 
    Route::get('/funding/sidebar/{lead}/{alternative}/{product}', [NewLeadsController::class, 'showSidebar']);
    // Budget Funding
    // Förderprogramm-Verwaltung (übernommen aus playground; ersetzt das kaputte BEG-Förderungen-Standalone-Modul).
    // Der Lead-Förderrechner weiter unten (saveFunding / update-beg-funding / funding/sidebar) bleibt unberührt.
    Route::get('/foerderungen', [FoerderungController::class, 'index'])->name('foerderungen.index');
    Route::post('/foerderungen', [FoerderungController::class, 'store'])->name('foerderungen.store');
    Route::put('/foerderungen/{foerderung}', [FoerderungController::class, 'update'])->name('foerderungen.update');
    Route::delete('/foerderungen/{foerderung}', [FoerderungController::class, 'destroy'])->name('foerderungen.destroy');
    Route::post('/foerderungen/{foerderung}/restore', [FoerderungController::class, 'restore'])->name('foerderungen.restore');
    // DEAKTIVIERT (kaputtes Standalone-Modul, BegFunding-Fatal-Error, Tier-C): Route::resource('beg-fundings', BegFundingsController::class);
    Route::post('/funding/save/{customer_id}/{alternative_id}/{product_id}', [NewLeadsController::class, 'saveFunding']);
    Route::get('/activity/carousel', [NewLeadsController::class, 'nextStep'])->name('activity.carousel');
    Route::get('/activity/nextStep', [NewLeadsController::class, 'nextSteps'])->name('activity.nextStep');
    Route::post('/update-beg-funding', [NewLeadsController::class, 'updateField']);
    Route::get('/lead-product/{cid}/{aid}/{pid}', [CustomerProductInfoController::class, 'loadProductBlade']);
    Route::get('/lead-product/{id}', [CustomerProductInfoController::class, 'showProduct'])->name('lead-product.show');
    Route::post('/lead-product/store', [CustomerProductInfoController::class, 'storeProduct']);
    Route::put('/lead-product/update/{id}', [CustomerProductInfoController::class, 'updateProduct']);
    Route::delete('/lead-product/delete/{id}', [CustomerProductInfoController::class, 'deleteProduct']);
    Route::get('/customer/load/product/{pid}', [CustomerProductInfoController::class, 'getProduct']);
    Route::get('/lead-product/media/{productInfoId}', [CustomerProductInfoController::class, 'mediaIndex']);
    Route::post('/lead-product/media/{productInfoId}/upload', [CustomerProductInfoController::class, 'mediaUpload']);
    Route::delete('/lead-product/media/file/{mediaId}', [CustomerProductInfoController::class, 'mediaDelete']);
    Route::get('/load/task/view', [NewLeadsController::class, 'loadTaskView'])->name('load.task.view');
    Route::get('/lead/overview', [LeadOverviewController::class, 'index'])->name('lead.overview');
    Route::get('/lead/kanban', [LeadOverviewController::class, 'kanban'])->name('lead.kanban');
    Route::get('/lead/kanban/get', [LeadOverviewController::class, 'getLead'])->name('lead.kanban.get.lead');
    Route::get('/lead/kanban/search', [LeadOverviewController::class, 'search'])->name('lead.kanban.search');
    Route::post('/lead/kanban/{customer}/{alternative}/{product}/{employee?}/{service}/{stage}/{service_id}/{department_id}', [LeadOverviewController::class, 'changeStage']);
    Route::get('/customer/process/kanban/{customer_id}/{alternative_id}/{product_id}/{employee_id}', [LeadOverviewController::class, 'getCustomer'])->name('lead.get.customer.kanban');
    Route::post('/customer/process/kanban/view', [LeadOverviewController::class, 'getKanbanView'])->name('lead.get.customer.kanban.view');
    Route::get('/lead/kanban/feed', [LeadOverviewController::class, 'kanbanFeed'])->name('lead.kanban.feed');
    Route::post('/lead/kanban/filter-settings/{setting}/default', [LeadOverviewController::class, 'kanbanFilterSettingsDefault'])->name('kanban.filter-settings.default');
    Route::delete('/lead/kanban/filter-settings/{setting}', [LeadOverviewController::class, 'kanbanFilterSettingsDestroy'])->name('kanban.filter-settings.destroy');
    Route::match(['GET', 'POST'], 'kanban/appointments/reports', [MainAppointmentController::class, 'reports'])->name('kanban.appointments.reports');
    Route::post('kanban/appointments/reports/{report}/react', [MainAppointmentController::class, 'reactReport'])->name('kanban.appointments.reports.react');
    Route::post('kanban/appointments/reports/{report}/comment', [MainAppointmentController::class, 'commentReport'])->name('kanban.appointments.reports.comment');
    Route::post('kanban/appointments/{appointment}/reports', [MainAppointmentController::class, 'storeReport'])->name('kanban.appointments.reports.store');
    Route::get('/lead/kanban/value-analytics', [LeadOverviewController::class, 'valueAnalytics'])->name('lead.kanban.value-analytics');
    Route::prefix('kanban/customer-reports')
        ->name('kanban.customer-reports.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/', [CustomerReportController::class, 'kanbanIndex'])->name('index');   
            Route::post('/', [CustomerReportController::class, 'kanbanStore'])->name('store');
            Route::post('{report}/comment', [CustomerReportController::class, 'kanbanComment'])->name('comment');
    });
    Route::prefix('lead')->group(function () {
        Route::get('appointments/customer-search', [LeadOverviewController::class, 'appointmentCustomerSearch']);
        Route::get('appointments/index',           [LeadOverviewController::class, 'appointmentsIndex']);
        Route::post('appointments/store',          [LeadOverviewController::class, 'appointmentsStore']);
        Route::put('appointments/{appointment}/update',    [LeadOverviewController::class, 'appointmentsUpdate']);
        Route::delete('appointments/{appointment}/destroy',[LeadOverviewController::class, 'appointmentsDestroy']);
    });
    Route::post('/lead-product/change-stage/{customer_id}/{alternative_id}/{product_id}',[LeadOverviewController::class,'changeStage'])->whereNumber(['customer_id','alternative_id','product_id']);
    Route::post('/lead-product/ticketize/{id}', [LeadOverviewController::class, 'ticketize'])->whereNumber('id')->name('lead-product.ticketize');
    Route::get('/lead/kanban/archive', [LeadOverviewController::class, 'archivePartial'])->name('kanban.archive');
    Route::get('/lead/kanban/junk', [LeadOverviewController::class, 'junkPartial'])->name('kanban.junk');
    Route::get('/lead/kanban/tickets', [LeadOverviewController::class, 'ticketsPartial'])->name('kanban.tickets');
    Route::get('/lead/kanban/investment', [LeadOverviewController::class, 'investment'])->name('lead.kanban.investment');
    Route::post('/kanban/lead-product/{leadProduct}/sub-stage', [LeadOverviewController::class, 'updateLeadSubStage'])->name('kanban.lead-product.sub-stage.update');
 
    Route::post('/lead/kanban/update-stage/{leadProductId}/{stage}', [LeadOverviewController::class, 'updateStage']);
    Route::get('/lead/kanban/ajax', [LeadOverviewController::class, 'searchForm']);
    Route::get('/lead/process/history/{customer_id}/{alternative_id}/{product_id}', [LeadOverviewController::class, 'showStageHistory'])->name('lead.history');
    Route::post('/lead/restore/{id}', [LeadOverviewController::class, 'restoreLeadStage'])->name('lead.restore');
    Route::get('/lead/archive', [LeadOverviewController::class, 'archiveLeads'])->name('leads.archive');
    Route::get('/lead/junk', [LeadOverviewController::class, 'junkLeads'])->name('leads.junk'); 
    Route::post('/lead-product/progress/{leadProductId}/{state}', [LeadOverviewController::class, 'updateProgress'])->where(['leadProductId' => '[0-9]+','state' => 'playing|paused|stopped'])->name('lead.progress');
    Route::middleware(['auth', 'web'])->group(function () { 
        Route::delete('/lead-product/purge/{id}', [LeadOverviewController::class, 'purge'])->name('lead-product.purge');
        // Fallback for environments that block DELETE or older  front-ends
        Route::post('/lead-product/purge/{id}', [LeadOverviewController::class, 'purge'])->name('lead-product.purge.post');
        // Legacy GET (only if you really need it)
        Route::get('/delete_lead_product/{id}', [LeadOverviewController::class, 'purgeLegacy'])->name('lead-product.purge.legacy');
    });

    Route::prefix('personal-tasks')->middleware(['auth'])->group(function () {
        Route::get('/index', [PersonalTaskController::class, 'personalTasksIndex'])->name('personal_tasks.index');
        Route::post('/store', [PersonalTaskController::class, 'personalTasksStore'])->name('personal_tasks.store');
        Route::put('/{task}/update', [PersonalTaskController::class, 'personalTasksUpdate'])->name('personal_tasks.update');
        Route::delete('/{task}/destroy', [PersonalTaskController::class, 'personalTasksDestroy'])->name('personal_tasks.destroy');
        Route::post('/{task}/employees/sync', [PersonalTaskController::class, 'personalTasksSyncEmployees'])->name('personal_tasks.employees.sync');
    });

    Route::middleware(['auth'])
        ->prefix('kanban/personal-task-panel')
        ->name('kanban.personal-task-panel.')
        ->group(function () {
            Route::get('/tasks', [KanbanPersonalTaskPanelController::class, 'tasks'])
                ->name('tasks');

            Route::get('/tasks/{task}', [KanbanPersonalTaskPanelController::class, 'show'])
                ->name('tasks.show');

            Route::post('/tasks/{task}/comments', [KanbanPersonalTaskPanelController::class, 'storeComment'])
                ->name('tasks.comments.store');

            Route::post('/comments/{comment}/reply', [KanbanPersonalTaskPanelController::class, 'storeReply'])
                ->name('comments.reply');

            Route::post('/keys/{key}/toggle', [KanbanPersonalTaskPanelController::class, 'toggleKey'])
                ->name('keys.toggle');
        });


    Route::prefix('admin/kanban/tasks')
        ->name('admin.kanban.tasks.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/context/{leadProduct}', [KanbanLeadTaskController::class, 'context'])
                ->name('context');

            Route::post('/summaries', [KanbanLeadTaskController::class, 'summaries'])
                ->name('summaries');

            Route::post('/manual', [KanbanLeadTaskController::class, 'storeManual'])
                ->name('manual.store');

            Route::post('/template', [KanbanLeadTaskController::class, 'storeFromTemplate'])
                ->name('template.store');

            Route::patch('/{task}/status', [KanbanLeadTaskController::class, 'updateStatus'])
                ->name('status.update');

            Route::delete('/{task}', [KanbanLeadTaskController::class, 'destroy'])
                ->name('destroy');
        });
        Route::prefix('admin/kanban/tasks')
            ->name('admin.kanban.tasks.')
            ->middleware(['auth'])
            ->group(function () {
                Route::get('/context/{leadProduct}', [KanbanLeadTaskController::class, 'context'])
                    ->name('context');

                // Add this
                Route::post('/summaries', [KanbanLeadTaskController::class, 'summaries'])
                    ->name('summaries');

                Route::post('/manual', [KanbanLeadTaskController::class, 'storeManual'])
                    ->name('manual.store');

                Route::post('/template', [KanbanLeadTaskController::class, 'storeFromTemplate'])
                    ->name('template.store');

                Route::patch('/{task}/status', [KanbanLeadTaskController::class, 'updateStatus'])
                    ->name('status.update');

                Route::delete('/{task}', [KanbanLeadTaskController::class, 'destroy'])
                    ->name('destroy');
            });

    Route::middleware(['auth'])
        ->prefix('admin/kanban')
        ->name('admin.kanban.')
        ->group(function () {
            Route::get('/stages/{stage}/sub-stages', [LeadStageSubStageController::class, 'index'])
                ->name('stages.sub-stages.index');

            Route::post('/stages/{stage}/sub-stages', [LeadStageSubStageController::class, 'store'])
                ->name('stages.sub-stages.store');

            Route::put('/sub-stages/{subStage}', [LeadStageSubStageController::class, 'update'])
                ->name('sub-stages.update');

            Route::delete('/sub-stages/{subStage}', [LeadStageSubStageController::class, 'destroy'])
                ->name('sub-stages.destroy');

            Route::post('/stages/{stage}/sub-stages/reorder', [LeadStageSubStageController::class, 'reorder'])
                ->name('stages.sub-stages.reorder');

            Route::patch('/sub-stages/{subStage}/toggle', [LeadStageSubStageController::class, 'toggle'])
                ->name('sub-stages.toggle');

            Route::patch('/sub-stages/{subStage}/default', [LeadStageSubStageController::class, 'makeDefault'])
                ->name('sub-stages.default');
        });


    /*
    |--------------------------------------------------------------------------
    | Lead Main Stages
    |--------------------------------------------------------------------------
    | Important:
    | - GET /admin/lead-stages/{stage} is required for edit mode.
    | - PUT /admin/lead-stages/{stage} updates name/color/icon/active/closed.
    | - Protected/default stages can be edited, but their key must not change.
    */
    Route::middleware(['auth'])
        ->prefix('admin/lead-stages')
        ->name('lead-stages.')
        ->group(function () {
            Route::get('/', [LeadStageController::class, 'index'])
                ->name('index');

            Route::post('/', [LeadStageController::class, 'store'])
                ->name('store');

            Route::post('/reorder', [LeadStageController::class, 'reorder'])
                ->name('reorder');

            // REQUIRED FOR EDIT BUTTON
            Route::get('/{stage}', [LeadStageController::class, 'show'])
                ->name('show');

            Route::put('/{stage}', [LeadStageController::class, 'update'])
                ->name('update');

            Route::delete('/{stage}', [LeadStageController::class, 'destroy'])
                ->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Bulk Stage Transfer
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])
        ->prefix('task-phase/ajax/stage-admin')
        ->group(function () {
            // REQUIRED FOR SOURCE/TARGET PHASE DROPDOWNS
            Route::get('/stage-transfer/options', [LeadStageBulkMoveController::class, 'options'])
                ->name('kanban.stage-transfer.options');

            Route::get('/stage-transfer/summary', [LeadStageBulkMoveController::class, 'summary'])
                ->name('kanban.stage-transfer.summary');

            Route::post('/stage-transfer/move', [LeadStageBulkMoveController::class, 'move'])
                ->name('kanban.stage-transfer.move');
        });

        Route::middleware(['auth'])
            ->prefix('kanban/customer-panel')
            ->name('kanban.customer-panel.')
            ->group(function () {
                Route::get('/counts', [KanbanCustomerPanelController::class, 'counts'])->name('counts');

                Route::get('/customer-reports', [KanbanCustomerPanelController::class, 'customerReportsIndex'])->name('customer-reports.index');
                Route::post('/customer-reports', [KanbanCustomerPanelController::class, 'customerReportsStore'])->name('customer-reports.store');

                Route::get('/appointments', [KanbanCustomerPanelController::class, 'appointmentsIndex'])->name('appointments.index');
                Route::get('/appointments/{appointment}/reports', [KanbanCustomerPanelController::class, 'appointmentReportsIndex'])->name('appointments.reports.index');
                Route::post('/appointments/{appointment}/reports', [KanbanCustomerPanelController::class, 'appointmentReportsStore'])->name('appointments.reports.store');
            });

     

   

    Route::middleware(['auth'])->group(function () {
        Route::post('/kanban/reminders', [LeadReminderController::class, 'store'])->name('kanban.reminders.store');
        Route::get('/kanban/reminders/due', [LeadReminderController::class, 'due'])->name('kanban.reminders.due');
        Route::get('/kanban/reminders/context', [LeadReminderController::class, 'context'])->name('kanban.reminders.context');
        Route::post('/kanban/reminders/{reminder}/done', [LeadReminderController::class, 'done'])->name('kanban.reminders.done');
        Route::post('/kanban/reminders/cards-summary', [LeadReminderController::class, 'cardSummaries'])->name('kanban.reminders.cards-summary');
        Route::get('/kanban/reminders/overdue-center', [LeadReminderController::class, 'overdueCenter'])->name('kanban.reminders.overdue-center');
    });
    Route::prefix('kanban-stage-workflow')->name('kanban-stage-workflow.')->group(function () {
        Route::get('/config', [LeadOverviewController::class, 'stageWorkflowConfig'])->name('config');
        Route::post('/move/{leadProduct}', [LeadOverviewController::class, 'moveStageWorkflow'])->name('move');
        Route::post('/move-next/{leadProduct}', [LeadOverviewController::class, 'moveToNextProductStage'])->name('move-next');
    });

    Route::get('/lead-product/stage-history/{customer}/{alternative}/{product}', [LeadOverviewController::class, 'stageHistory']);
    Route::post('/new/lead/stage', [CustomProcessController::class, 'store'])->name('custom.lead.stage');
    Route::get('/load/lead/stage', [CustomProcessController::class, 'index'])->name('custom.load.stage');
    Route::delete('/delete/lead/stage/{id}', [CustomProcessController::class, 'destroy'])->name('custom.destroy.stage');
    Route::get('/delete_lead_responsible/{id}', [NewLeadsController::class, 'delete_responsible'])->name('delete.lead.responsible');
    Route::get('/delete_lead_product/{id}', [NewLeadsController::class, 'delete_product'])->name('delete.lead.product');
    Route::get('/delete_lead_alternative/{id}', [NewLeadsController::class, 'delete_alternative'])->name('delete.lead.alternative');
    Route::get('/junk_lead_alternative/{id}', [NewLeadsController::class, 'junk_alternative'])->name('junk.lead.alternative');
    Route::get('/unjunk_lead_alternative/{id}', [NewLeadsController::class, 'unjunk_alternative'])->name('unjunk.lead.alternative');
    Route::post('/lead-product-lists/bulk-store', [NewLeadsController::class, 'bulkStore'])->name('lead_product_lists.bulk.store');
    Route::delete('/lead-product-lists/{id}', [NewLeadsController::class, 'productDelete']);
    Route::post('new-leads/{id}/ajax-update-basic', [NewLeadsController::class, 'ajaxUpdateBasic'])->name('new-leads.ajax-update-basic');
    Route::get('new-leads/{id}/ajax-load-basic',  [NewLeadsController::class, 'ajaxLoadBasic'])->name('new-leads.ajax-load-basic');  
    // Purchase summary
    Route::get('/customers/{customer}/purchase-summary', [NewLeadsController::class, 'purchaseSummary'])->name('customers.purchase-summary');
    Route::post('/customers/{customer}/total-purchase', [NewLeadsController::class, 'updateTotalPurchase'])->name('customers.update-total');
    //Profile CRUD 
    Route::get('/get_object_data/{customer}/{alternative}/{product}', [NewLeadsController::class, 'getObject'])->name('get.object.data'); 
    Route::post('lead_info_data', [NewLeadsController::class, 'updatedata'])->name('lead.info.data');
    //Auto Complete and Customer Duplicate Check
    Route::get('/api/lead-name-suggestions', [NewLeadsController::class, 'getnameSuggestions']);
    Route::get('/api/lead-lastname-suggestions', [NewLeadsController::class, 'getLastnameSuggestions']);
    Route::get('/check-new-leads', [NewLeadsController::class, 'checkCustomer'])->name('check.customer');
    Route::post('/update-lead-employee', [NewLeadsController::class, 'updateLeadEmployee']);
    Route::get('/send_data_image', [NewLeadsController::class, 'matchCustomerWithLeadAndAttachImage']);
    Route::post('accept_lead', [NewLeadsController::class, 'accept'])->name('accept.lead');
    Route::get('/get_lead_product_list/{id}/{alternative}', [NewLeadsController::class, 'product_lists'])->name('get.lead.product.list');
        // New Leads Notification System 
    Route::get('/notifications/lead/{user}', [NewLeadsController::class, 'getTaskNotifications'])->name('lead.notifications.inquiry');
    Route::post('lead/notifications/{id}/mark-read', [NewLeadsController::class, 'markAsRead']);
    Route::get('/notifications/timeline/{leadId}/{responsibleId}', [NewLeadsController::class, 'getTimelineNotifications'])->name('notifications.timeline'); 
    Route::get('customer/calendar/view', [NewLeadsController::class, 'calendarView']); 
    Route::post('/phase-activities/update-duration', [PhaseActivitiesController::class, 'updateDuration']);
    Route::post('/lead-product/update-price', [NewLeadsController::class, 'updateProductPrice'])->name('leadProduct.updatePrice');
    Route::post('/new-leads/invoices/sync-product-price', [NewLeadsController::class, 'syncProductInvoicePrice'])->name('new-leads.invoices.sync-product-price');
    //Refrences
    Route::get('/lead/reference', [NewLeadsController::class, 'reference'])->name('lead.references');
    Route::get('/leads-nearby', [NewLeadsController::class, 'nearby']);
    Route::get('/api/objects-with-products', [NewLeadsController::class, 'dashboardLoad']);
    Route::get('/dashboard/customer/{customerId}/alternative/{alternativeId}', [NewLeadsController::class, 'dashboard']);
    Route::get('/modal/history', [NewLeadsController::class, 'loadHistoryModal']);
    Route::get('/customer-activity-document/{filename}', [NewLeadsController::class, 'showActivityDocument'])->name('customer.activity-document.show');
    Route::post('/customer-history/save', [CustomerHistoryController::class, 'save'])->name('save.customer.history');
    Route::post('/ajax/customer-history/save', [CustomerHistoryController::class, 'saveFromAjax'])->name('ajax.save.customer.history');
    Route::post('/activity-document-upload', [CustomerHistoryController::class, 'uploadActivityDocument']);
    Route::get('/ajax/get-done-history', [CustomerHistoryController::class, 'getDoneHistory']);
    Route::get('/ajax/times-summary', [CustomerHistoryController::class, 'getTimeSummary']);
    Route::post('/verify-unlock', [NewLeadsController::class, 'verifyUnlock']);
    Route::post('/dashboard/product-card', [NewLeadsController::class, 'loadProductCard']);
    //Customer Card Note
    Route::post('/save-customer-card-note', [CustomerCardNoteController::class, 'save']);
    Route::get('/ajax/load-stages', [NewLeadsController::class, 'loadStages'])->name('customer-stages.load');
    Route::post('/ajax/save-customer-stage', [NewLeadsController::class, 'saveCustomerStage'])->name('customer-stages.save');
    Route::get('/ajax/load-version-stages', [NewLeadsController::class, 'loadVersionStages']);
    Route::post('/customer_card_notes/store', [CustomerCardNoteController::class, 'store'])->name('customer_card_notes.store');
    Route::delete('/customer_card_notes/delete', [CustomerCardNoteController::class, 'destroy'])->name('customer_card_notes.delete');
    Route::post('/phase/suggest-employees', [CustomerSuggestEmployeeController::class, 'store'])->name('suggest.employees.store');
    Route::get('/get/employee-departments/{id}', [CustomerSuggestEmployeeController::class, 'loadDepartment']);
    Route::get('/phase/suggested-employees', [CustomerSuggestEmployeeController::class, 'get'])->name('suggest.employees.get');
    Route::post('/suggest-employees/update', [CustomerSuggestEmployeeController::class, 'update'])->name('suggest.employees.update');
    Route::delete('/suggest-employees/{id}', [CustomerSuggestEmployeeController::class, 'destroy'])->name('suggest.employees.destroy');
    Route::post('/initialize-customer-stage', [CustomerStageController::class, 'initializeCustomerStage']);
    Route::get('/check-customer-stage', [CustomerStageController::class, 'check']);
    Route::get('/get-stages-and-versions', [CustomerStageController::class, 'getStagesAndVersions']);
    Route::post('/update-customer-stage', [CustomerStageController::class, 'updateCustomerStage']);
    Route::post('/update-single-customer-stage', [CustomerStageController::class, 'updateSingleCustomerStage']);
});
Route::middleware(['auth'])
    ->prefix('customer-context-feed')
    ->name('customer.context-feed.')
    ->group(function () {
        Route::get('/{type}', [CustomerContextFeedController::class, 'index'])->name('index');

        Route::post('/ticket/{problem}/comment', [CustomerContextFeedController::class, 'storeTicketComment'])->name('ticket.comment');
        Route::post('/appointment/{appointment}/report', [CustomerContextFeedController::class, 'storeAppointmentReport'])->name('appointment.report');
        Route::post('/task/{task}/comment', [CustomerContextFeedController::class, 'storeTaskComment'])->name('task.comment');
        Route::post('/deal/{deal}/note', [CustomerContextFeedController::class, 'storeDealNote'])->name('deal.note');
        Route::post('/customer-report', [CustomerContextFeedController::class, 'storeCustomerReport'])->name('customer-report.store');
    });
// Review crude 
Route::middleware(['auth'])->group(function () {
    Route::get('/customer-reviews', [CustomerReviewController::class, 'index'])->name('customer-reviews.index'); 
    Route::post('/customer-reviews', [CustomerReviewController::class, 'store'])->name('customer-reviews.store'); 
    Route::put('/customer-reviews/{customerReview}', [CustomerReviewController::class, 'update'])->name('customer-reviews.update'); 
    Route::delete('/customer-reviews/{customerReview}', [CustomerReviewController::class, 'destroy'])->name('customer-reviews.destroy');
});
Route::prefix('admin/new-leads')->name('admin.new_leads.')->group(function () {
    Route::get('/', [NewLeadsController::class, 'index'])->name('index');
    // Drawer data
    Route::get('/duplicates', [NewLeadsController::class, 'duplicates'])->name('duplicates');
    // Delete one duplicate customer
    Route::delete('/{id}/duplicate', [NewLeadsController::class, 'destroyDuplicate'])->name('duplicates.destroy');
    Route::post('/duplicates/merge', [NewLeadsController::class, 'mergeDuplicate'])
        ->name('duplicates.merge');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/lead/references', [NewLeadsController::class, 'references'])
        ->name('lead.reference');

    Route::get('/lead/references/nearby', [NewLeadsController::class, 'nearby'])
        ->name('lead.reference.nearby');
});
Route::middleware(['auth'])->prefix('lead/mass-manager')->group(function () {
    Route::get('/mass-manager/load', [MassManagerController::class, 'load'])->name('mass.load');
    Route::post('/mass-manager/suggest', [MassManagerController::class, 'suggest'])->name('mass.suggest');
    Route::post('/mass-manager/store', [MassManagerController::class, 'store'])->name('mass.store');
    Route::delete('/mass-manager/delete/{id}', [MassManagerController::class, 'delete'])->name('mass.delete');
});
 
// Appointment Reminder 
Route::middleware(['auth'])->group(function () {
    Route::get('/main-appointments/reminders/upcoming', [MainAppointmentReminderController::class, 'upcoming'])->name('main-appointments.reminders.upcoming');
    Route::post('/main-appointments/{appointment}/reminders/seen', [MainAppointmentReminderController::class, 'markSeen'])->name('main-appointments.reminders.seen');
    Route::post('/main-appointments/{appointment}/reminders/test', [MainAppointmentReminderController::class, 'test'])->name('main-appointments.reminders.test');
});

// Climate Station 
Route::get('/admin/climate/{customer_id}/{alternative_id}', [ClimateStationController::class, 'show'])->name('admin.climate.show');
Route::get('/admin/climate/{customer_id}/{alternative_id}/data', [ClimateStationController::class, 'data'])->name('admin.climate.data');
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/climate/import', [ClimateImportController::class, 'create'])->name('climate.import.create');
    Route::post('/climate/import', [ClimateImportController::class, 'store'])->name('climate.import.store');
});

Route::prefix('ajax')->middleware(['web','auth'])->group(function () {
    Route::get('/customers/{customer}/object-product-tree', [CustomerObjectProductModalController::class, 'tree'])->name('ajax.customer.objectProductTree');
    Route::post('/customers/{customer}/objects', [CustomerObjectProductModalController::class, 'createObject'])->name('ajax.customer.createObject');
    Route::post('/lead-products/{leadProduct}/move', [CustomerObjectProductModalController::class, 'moveProduct'])->name('ajax.leadProduct.move');
    Route::post('/objects/{object}/delete', [CustomerObjectProductModalController::class, 'deleteObject'])->name('ajax.object.delete');
    Route::post('/lead-products/{leadProduct}/delete', [CustomerObjectProductModalController::class, 'deleteProduct'])->name('ajax.leadProduct.delete');
});


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/employees/{employee}/recurring-leaves', [EmployeeRecurringLeaveController::class, 'index'])
        ->name('employees.recurring.index');

    Route::post('/employees/{employee}/recurring-leaves', [EmployeeRecurringLeaveController::class, 'store'])
        ->name('employees.recurring.store');

    Route::put('/employees/{employee}/recurring-leaves/{leave}', [EmployeeRecurringLeaveController::class, 'update'])
        ->name('employees.recurring.update');

    Route::delete('/employees/{employee}/recurring-leaves/{leave}', [EmployeeRecurringLeaveController::class, 'destroy'])
        ->name('employees.recurring.destroy');

    Route::get('/employees/{employee}/recurring-leaves/{leave}/occurrences', [EmployeeRecurringLeaveController::class, 'occurrences'])
        ->name('employees.recurring.occurrences');

    Route::post('/employees/{employee}/recurring-leaves/{leave}/exdate', [EmployeeRecurringLeaveController::class, 'exdateAdd'])
        ->name('employees.recurring.exdate.add');

    Route::delete('/employees/{employee}/recurring-leaves/{leave}/exdate', [EmployeeRecurringLeaveController::class, 'exdateRemove'])
        ->name('employees.recurring.exdate.remove');

    Route::post('/employees/{employee}/recurring-leaves/{leave}/override', [EmployeeRecurringLeaveController::class, 'upsertOverride'])
        ->name('employees.recurring.override.upsert');

    Route::delete('/employees/{employee}/recurring-leaves/{leave}/override', [EmployeeRecurringLeaveController::class, 'deleteOverride'])
        ->name('employees.recurring.override.delete');
});

// Customer Report 
Route::group(['middleware' => 'web'], function () {
    Route::get('/customer-reports/list', [CustomerReportController::class, 'list']);
    Route::post('/customer-reports/store', [CustomerReportController::class, 'store']);
    Route::delete('/customer-reports/delete/{id}', [CustomerReportController::class, 'destroy']);
    Route::get('/customer-reports/show/{id}', [CustomerReportController::class, 'show']);
    Route::put('/customer-reports/update/{id}', [CustomerReportController::class, 'update']);

    //Customer Report Comments 
    Route::prefix('customer-report-comments')->group(function () {
    Route::get('/{report}', [CustomerReportCommentController::class, 'index']);
    Route::post('/', [CustomerReportCommentController::class, 'store']);
    Route::put('/{comment}', [CustomerReportCommentController::class, 'update']);
    Route::delete('/{comment}', [CustomerReportCommentController::class, 'destroy']);
});



});
     // Berechnungsübersicht
Route::group(['middleware' => 'web'], function () {
    Route::prefix('admin/economic-calculations')->middleware(['auth'])->group(function () {
        Route::get('/', [EconomicCalculationController::class, 'index'])->name('economic_calculations.index');
        Route::post('/store', [EconomicCalculationController::class, 'store'])->name('economic_calculations.store');
        Route::get('/edit/{id}', [EconomicCalculationController::class, 'edit'])->name('economic_calculations.edit');
        Route::put('/update/{id}', [EconomicCalculationController::class, 'update'])->name('economic_calculations.update');
        Route::delete('/delete/{id}', [EconomicCalculationController::class, 'destroy'])->name('economic_calculations.destroy');
    });
});


 

// Customer Notes
Route::group(['middleware' => 'web'], function () {
    Route::get('/customer-notes/{customer}/{alternative}/{product}', [CustomerNoteController::class, 'getNotesHtml']);

    Route::get(
        '/customer-notes/context/{customer}/{alternative}/{product}/{leadProductList?}',
        [CustomerNoteController::class, 'getNotesHtmlByContext']
    )->name('customer-notes.context');

    Route::post('/customer-notes', [CustomerNoteController::class, 'store'])->name('customer-notes.store');
    Route::post('/customer-notes/store', [CustomerNoteController::class, 'store']);

    Route::get('/customer-notes/edit/{id}', [CustomerNoteController::class, 'edit']);
    Route::post('/customer-notes/inline-update/{id}', [CustomerNoteController::class, 'inlineUpdate']);

    Route::delete('/customer-notes/{note}', [CustomerNoteController::class, 'destroy'])->name('customer-notes.destroy');
    Route::delete('/customer-notes/delete/{id}', [CustomerNoteController::class, 'delete']);

    Route::post('/customer-notes/reorder', [CustomerNoteController::class, 'reorder']);
    Route::post('/customer-notes/{note}/reply', [CustomerNoteController::class, 'reply'])->name('customer-notes.reply');
    Route::delete('/customer-notes/reply/{id}/delete', [CustomerNoteController::class, 'deleteReply']);
    Route::post('/customer-notes/reply/{id}/update', [CustomerNoteController::class, 'updateReply']);
    Route::post('/customer-notes/{id}/update', [CustomerNoteController::class, 'update'])->name('customer-notes.update');

    Route::post('/save_customer_note_process', [CustomerNoteController::class, 'processStore']);

    Route::get('/notes/deleted/{parentId}', [CustomerNoteController::class, 'getDeletedNotes']);
    Route::post('/notes/restore/{id}', [CustomerNoteController::class, 'restore']);
    Route::post('/notes/delete-permanent/{id}', [CustomerNoteController::class, 'forceDelete']);
    Route::get('/notes/deleted-all', [CustomerNoteController::class, 'getAllDeletedNotes']);

    Route::post('/ajax/save-customer-note', [CustomerNoteController::class, 'saveAjax']);
});


Route::middleware(['auth'])
->prefix('customer-notes')      
->name('customer.notes.')       
->group(function () {
    Route::get   ('/',       [CustomerNoteController::class, 'noteIndex'])  ->name('index');
    Route::post  ('/',       [CustomerNoteController::class, 'noteStore'])  ->name('store');
    Route::put   ('/{note}', [CustomerNoteController::class, 'noteUpdate']) ->name('update');
    Route::delete('/{note}', [CustomerNoteController::class, 'noteDestroy'])->name('destroy');
});


// Contact People
Route::group(['middleware' => 'web'], function () {
    Route::get('/customer-contact/fetch/{customer}/{alternative}', [CustomerContactPersonController::class, 'fetch']);
    Route::post('/customer-contact/update', [CustomerContactPersonController::class, 'updateAll'])->name('customer.contact.update');
    Route::delete('/customer-contact/delete/{id}', [CustomerContactPersonController::class, 'delete']);
    Route::get('/contact-people/{customerId}/{alternativeId}', [CustomerContactPersonController::class, 'list']);
    Route::post('/contact-people/save', [CustomerContactPersonController::class, 'storeOrUpdate'])->name('contact.people.save');
    Route::delete('/contact-people/delete/{id}', [CustomerContactPersonController::class, 'destroy'])->name('contact.people.delete'); 
});

Route::prefix('customers/{customer}')->group(function () {
    Route::get('contact-people', [CustomerContactPersonController::class, 'index'])->name('customers.contact-people.index');
    Route::post('contact-people', [CustomerContactPersonController::class, 'saves'])->name('customers.contact-people.store');
});
Route::put('customer-contact-people/{person}', [CustomerContactPersonController::class, 'update'])->name('customer-contact-people.update');
Route::delete('customer-contact-people/{person}', [CustomerContactPersonController::class, 'deletes']) ->name('customer-contact-people.destroy');
Route::group(['middleware' => 'web'], function () {
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::resource('lead-email-accounts', LeadEmailAccountsController::class);
        Route::get('/lead-email-accounts/realtime-data', [LeadEmailAccountsController::class, 'unreadRealtimeData']) ->name('lead-email-accounts.realtime-data');
        Route::post('/lead-emails/{id}/mark-read', [LeadEmailAccountsController::class, 'markEmailAsRead'])->name('lead-emails.mark-read');
    });
    Route::post('/admin/lead-email-accounts/toggle-status/{id}', [LeadEmailAccountsController::class, 'toggleStatus']); 
    Route::post('/admin/lead-email-accounts/test/{id}', [LeadEmailAccountsController::class, 'testConnection']);
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('lead-email-domain-filters', [LeadEmailDomainFilterController::class, 'index'])->name('lead.email.domain.filters.index');
        Route::post('lead-email-domain-filters', [LeadEmailDomainFilterController::class, 'store'])->name('lead.email.domain.filters.store');
        Route::delete('lead-email-domain-filters/{id}', [LeadEmailDomainFilterController::class, 'destroy'])->name('lead.email.domain.filters.destroy');
    });
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('email-inbox/fetch', [LeadEmailReaderController::class, 'fetchAndStore'])->name('lead.email.fetch');
        Route::get('email-inbox', [LeadEmailReaderController::class, 'inbox'])->name('lead.email.inbox');
        Route::get('email-inbox/realtime-list', [LeadEmailReaderController::class, 'realtimeList'])->name('lead.email.realtime.list');
        Route::get('email-inbox/export/csv', fn() => 'TODO: CSV Export')->name('lead.email.export.csv');
        Route::get('email-inbox/export/pdf', fn() => 'TODO: PDF Export')->name('lead.email.export.pdf');
        Route::get('lead-email/show/{id}', [LeadEmailReaderController::class, 'show'])->name('lead.email.show');
        Route::post('lead-email/mark-read/{id}', [LeadEmailReaderController::class, 'markAsRead'])->name('lead.email.mark.read');
    });

});


Route::group(['middleware' => 'web'], function () {

    Route::post('/images', [ImageController::class, 'store'])->name('images.store');

    Route::post('/upload-screenshot', [ImageController::class, 'uploadScreenshot'])
        ->name('screenshot.upload');

    Route::post('/save-screenshot', [ImageController::class, 'saveScreenshot'])
        ->name('screenshot.save');

    Route::get('/load-images/{alternativeId}', [ImageController::class, 'loadScreenshot'])
        ->name('screenshot.load');

    Route::post('/delete-screenshot', [ImageController::class, 'deleteScreenshot'])
        ->name('screenshot.delete');

    Route::get('/secure-image/id/{id}', [ImageController::class, 'secureImage'])
        ->name('secure.image');

    Route::get('/secure-image/file/{filename}', [ImageController::class, 'secureImageByFilename'])
        ->where('filename', '.*')
        ->name('secure.image.byFilename');

    Route::get('/secure-download/{id}', [ImageController::class, 'secureDownload'])
        ->name('document.secureDownload');

    Route::get('/image/secure/{id}', [ImageController::class, 'secureDownloadScreenshot'])
        ->name('image.secure.download');

    Route::prefix('document')->group(function () {
        Route::post('/load', [ImageController::class, 'load'])->name('document.load');
        Route::post('/upload', [ImageController::class, 'upload'])->name('document.upload');
        Route::post('/rename', [ImageController::class, 'rename'])->name('document.rename');
        Route::get('/get-by-filter', [ImageController::class, 'getByFilter'])->name('document.filter');
        Route::delete('/delete/{id}', [ImageController::class, 'delete'])->name('document.delete');
        Route::get('/download/{id}', [ImageController::class, 'download'])->name('document.download');
        Route::post('/update-details', [ImageController::class, 'updateDetails'])->name('document.updateDetails');
    });

});
Route::prefix('admin/products/images')->name('admin.products.images.')->group(function () {
    Route::get('/csv-import', [ProductImageCsvImportController::class, 'index'])->name('csv-import.index');
    Route::post('/csv-import', [ProductImageCsvImportController::class, 'store'])->name('csv-import.store');
});
Route::group(['middlware'=>'auth'], function(){
    Route::get('/customer_phase_manage', [CustomerPhaseListController::class, 'show'])->name('customer.phase.managment.show');
    Route::post('/customer/phase/manage', [CustomerPhaseListController::class, 'create'])->name('customer.phase.managment.create');
    Route::get('/customer_phase_management/edit', [CustomerPhaseListController::class, 'create'])->name('customer.phase.manage.edit');
    Route::get('/customer_phase_get/{customer}/{product}/{service}/{alternative}', [CustomerPhaseListController::class, 'getPhase'])->name('customer.phase.managment.get');
    Route::get('/customer_phase_get_new/{customer}/{product}/{service}/{alternative}', [CustomerPhaseListController::class, 'getPhaseNew'])->name('customer.phase.managment.get.new');
    Route::post('/customer_phase_management_store', [CustomerPhaseListController::class, 'store'])->name('customer.phase.management.store');
    Route::post('/customer_phase_management/color', [CustomerPhaseListController::class, 'color'])->name('customer.phase.management.color');
    Route::delete('/customer_phase_management_delete/{id}', [CustomerPhaseListController::class, 'deletePhase'])->name('customer.phase.management.delete');
});  
Route::group(['middleware'=>'auth'], function () {
    Route::get('product_positions', [ProductPositionController::class, 'index'])->name('product.position.view');
    Route::get('/product-position/article-groups', [ProductPositionController::class, 'getArticleGroups']);
    Route::get('/product-position/departments', [ProductPositionController::class, 'getDepartments']);
    Route::get('/product-position/positions/{department_id}', [ProductPositionController::class, 'getPositions']);
    Route::post('/product-position/save', [ProductPositionController::class, 'save'])->name('product-position.save');
    Route::get('/product-position/records', [ProductPositionController::class, 'getSavedRecords'])->name('product-position.records');
    Route::delete('/product-position/delete/{id}', [ProductPositionController::class, 'delete'])->name('product-position.delete');
    Route::post('/product-position/bulk-delete', [ProductPositionController::class, 'bulkDelete'])->name('product-position.bulk-delete');
    Route::post('/product-position/bulk-duplicate', [ProductPositionController::class, 'bulkDuplicate'])->name('product-position.bulk-duplicate');
    // Employees working in selected positions
    Route::post('/product-position/employees', [ProductPositionController::class, 'getEmployeesByPositions'])->name('product-position.employees');
    Route::post('/product-position/update/{id}', [ProductPositionController::class, 'update'])->name('product-position.update');
    Route::get('/product-position/services/{product_id}', [ProductPositionController::class, 'getPhaseSections'])->name('product-position.services');
    //Assigning the positions to products
    Route::prefix('admin/settings')->group(function () {
        Route::get('costing-sets', [CostingSetController::class, 'index'])->name('admin.costing_sets.index');
        Route::get('costing-sets/list', [CostingSetController::class, 'list'])->name('admin.costing_sets.list');
        Route::post('costing-sets', [CostingSetController::class, 'store'])->name('admin.costing_sets.store');
        Route::put('costing-sets/{set}', [CostingSetController::class, 'update'])->name('admin.costing_sets.update');
        Route::delete('costing-sets/{set}', [CostingSetController::class, 'destroy'])->name('admin.costing_sets.destroy');
        Route::post('costing-sets/{set}/make-default', [CostingSetController::class, 'makeDefault'])->name('admin.costing_sets.make_default');
        Route::get('costing-sets/{set}/roles', [CostingSetController::class, 'roles'])->name('admin.costing_sets.roles');
        Route::post('costing-sets/{set}/roles/bulk', [CostingSetController::class, 'rolesBulkUpdate'])->name('admin.costing_sets.roles.bulk');
        Route::post('costing-sets/{set}/roles/sync', [CostingSetController::class, 'rolesSyncFromQualifications'])->name('admin.costing_sets.roles.sync');
        Route::post('/costing-sets/{set}/roles/apply-defaults', [CostingSetController::class, 'rolesApplyDefaults'])->name('admin.costing_sets.roles.apply_defaults');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/employee-organization', [EmployeeOrganizationController::class, 'index'])->name('employee.organization.index');
    Route::get('/employee-organization/data', [EmployeeOrganizationController::class, 'data'])->name('employee.organization.data');
    Route::post('/employee-organization/assign', [EmployeeOrganizationController::class, 'assign'])->name('employee.organization.assign');
    Route::post('/employee-organization/bulk-assign', [EmployeeOrganizationController::class, 'bulkAssign'])->name('employee.organization.bulk-assign');
    Route::post('/employee-organization/update/{departmentPosition}', [EmployeeOrganizationController::class, 'update'])->name('employee.organization.update');
    Route::delete('/employee-organization/remove/{departmentPosition}', [EmployeeOrganizationController::class, 'remove'])->name('employee.organization.remove');
    Route::post('/employee-organization/assign-multiple', [EmployeeOrganizationController::class, 'assignMultiple'])
        ->name('employee.organization.assign-multiple');
});


// Route::group(['middleware' => 'auth'], function() {
//     Route::post('customer_pv_save', [PVChecklistController::class, 'store'])->name('customer.pv.store');
//     Route::get('customer_pv_show', [PVChecklistController::class, 'show'])->name('customer.pv.show');
//     //Saving WP Checklist
//     Route::post('customer_wp_save', [WPChecklistController::class, 'store'])->name('customer.wp.store');
// });
//Customer Measurement

Route::group(['middleware' => 'web'], function () {

    Route::post('/customer_measure', [CustomerMeasureController::class, 'store'])->name('customer.measure');


});

// Customer Details

Route::group(['middleware' => 'web'], function () {
    //Building Type
    Route::get('/building_type_view', [BuildingTypeController::class, 'index'])->name('building.type.view');
    Route::post('/building_type_store', [BuildingTypeController::class, 'store'])->name('building.type.store');
    Route::post('/building_type_update', [BuildingTypeController::class, 'update'])->name('building.type.update');
    Route::get('/building_type_destroy/{id}', [BuildingTypeController::class, 'destroy'])->name('building.type.destroy');
    // Adding value to Building

    Route::get('building_type_value/{id}', [BuildingTypeValueController::class, 'index'])->name('building.type.value.load');
    Route::post('building_type_value_store', [BuildingTypeValueController::class, 'store'])->name('building.type.value.store');
    Route::post('building_type_value_update', [BuildingTypeValueController::class, 'update'])->name('building.type.value.update');
    Route::get('building_type_value_destroy/{id}', [BuildingTypeValueController::class, 'delete'])->name('building.type.value.destroy');
    Route::get('/heating_type_view', [HeatingTypeController::class, 'index'])->name('heating.type.view');
    Route::post('/heating_type_store', [HeatingTypeController::class, 'store'])->name('heating.type.store');
    Route::post('/heating_type_update', [HeatingTypeController::class, 'update'])->name('heating.type.update');
    Route::get('/heating_type_destroy/{id}', [HeatingTypeController::class, 'destroy'])->name('heating.type.destroy');

});

//Project CRUD

 
//Employee Managment

Route::group(['middleware' => 'web'], function () {
    Route::get('/emp', [EmployeeController::class, 'index'])->name('emp.info');
    Route::get('/emp_create', [EmployeeController::class, 'create'])->name('emp.create');
    Route::get('/emp_destroy/{id}', [EmployeeController::class, 'destroy'])->name('emp.destroy');
    Route::post('/emp_save', [EmployeeController::class, 'store'])->name('emp.store');
    Route::post('/emp_update', [EmployeeController::class, 'update'])->name('emp.update');
    Route::post('/emp_add', [EmployeeController::class, 'add'])->name('emp.add');
    Route::get('/next_employee/{id}', [EmployeeController::class, 'next_employee'])->name('emp.next');
    Route::get('/employee_active/{id}', [EmployeeController::class, 'active'])->name('emp.active');
    Route::get('/employee_deactive/{id}', [EmployeeController::class, 'deactive'])->name('emp.deactive');
    Route::get('/employee_profile/{id}', [EmployeeController::class, 'profile'])->name('emp.profile');
    Route::get('/employee_cv/{id}', [EmployeeController::class, 'cv'])->name('emp.cv');
    Route::post('/employee_profile_update', [EmployeeController::class, 'profile_update'])->name('emp.profile.update');
    Route::post('/employee_profile_picture', [EmployeeController::class, 'profile_picture'])->name('emp.profile.picture');
    Route::get('/get-positions', [EmployeeController::class, 'getPositions'])->name('get.position');
    Route::get('/get-position/{department}/{employee}', [EmployeeController::class, 'getPosition'])->name('get.positions');
    Route::get('/get-remaining-percentage/{id}', [EmployeeController::class, 'Remaining_percentage'])->name('get.dept.remaining.percentage');
    Route::post('employee/add/department', [EmployeeController::class, 'add_department'])->name('emp.add.department'); 
    Route::get('/employee/{id}/department/table', [EmployeeController::class, 'departmentTable'])->name('emp.department.table');  
    Route::post('/employee/update/department', [EmployeeController::class, 'edit_department'])->name('emp.update.department');
    Route::delete('/employee/department/{dpId}', [EmployeeController::class, 'delete_department'])->name('emp.delete.department');
    Route::get('/employee/department/position/main/{id}/{employee_id}',        [EmployeeController::class, 'mainPositionActive'])->name('emp.main.main.position');
    Route::get('/employee/department/position/main/deactive/{id}/{employee_id}', [EmployeeController::class, 'mainPositionDeactive'])->name('emp.main.deactive.position');
    Route::get('/employee/sickness-holiday-analyser', [EmployeeController::class, 'sicknessHolidayAnalyser'])->name('employee.sickness-holiday-analyser');
    Route::get('/get-departments-positions', [EmployeeController::class, 'getDepartmentsAndPositions'])->name('get.departments.positions');
    Route::get('/employee/remaining/days/{emp_id}', [EmployeeController::class, 'getRemainingLeave'])->name('get.employee.remaining.day');
    Route::get('/check_holiday', [EmployeeController::class, 'holidayStatus'])->name('employee.check.holiday');
    Route::get('/check_end_holiday', [EmployeeController::class, 'holidayEndStatus'])->name('employee.check.holiday.end');
    Route::get('/employee-status', [EmployeeController::class, 'employeeStatus'])->name('employee.check.status');
    Route::get('/get_employee_calendar/{emp_id}', [EmployeeController::class, 'calendar'])->name('get.employee.calendar');
    Route::patch('/employee_color/{employee}', [EmployeeController::class, 'updateColor'])->name('employee.color');
    Route::get('/getDepartment/leader/{department_id}', [EmployeeController::class, 'getEmployeeLeader'])->name('get.employee.department.leader');
    Route::get('/check/department-holidays/{employee_id}/{start_date}/{end_date}', [EmployeeController::class, 'checkDepartmentHolidays'])->name('check.department.holidays');
    Route::post('/set-active-tab', [EmployeeController::class, 'activeTab'])->name('setActiveTab');
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::resource('teams', TeamController::class); 
        Route::post('teams/{team}/members/sync', [TeamController::class, 'syncMembers'])->name('teams.members.sync'); 
        Route::post('teams/{team}/promote', [TeamController::class, 'promoteReserve'])->name('teams.promote.reserve');
    });
});
// Contract Type CRUD
 
Route::group(['middleware' => 'web'], function(){  
    Route::get('capacity/list', [EmployeeCapacityStateController::class, 'index'])->name('employee.capacity.list'); 
    Route::get('capacity/index', [EmployeeCapacityStateController::class, 'view'])->name('employee.capacity.view'); 
    Route::get('/admin/employee/capacity/summary', [EmployeeCapacityStateController::class, 'summary'])->name('employee.capacity.summary');
    Route::get('/terminal', [EmployeeCapacityStateController::class, 'terminal'])->name('terminal');
});    
Route::middleware(['auth'])
->prefix('admin')
->group(function () { 
    // Employee’s own time management page
    Route::get('employees/{employee}/time-management', [TimeManagementController::class, 'index'])->name('time_management.index');
    // AJAX for calendar builder
    Route::get('time-management/month', [TimeManagementController::class, 'loadMonth'])->name('time_management.load');
    Route::post('time-management/save', [TimeManagementController::class, 'save'])->name('time_management.save');
    Route::post('time-management/submit', [TimeManagementController::class, 'submit'])->name('time_management.submit');
    // Approve / reject single plan (used both in employee view + cards view)
    Route::post('time-management/{plan}/status', [TimeManagementController::class, 'updateStatus'])->name('time_management.status'); 
    Route::get('time-management/slots', [TimeManagementController::class, 'slotsIndex'])->name('time_management.slots');
});
Route::prefix('time-management')->name('time_managements.')->group(function () {
    Route::get('/load',   [TimeManagementController::class, 'load'])->name('load');
    Route::post('/save',  [TimeManagementController::class, 'save'])->name('save');
    Route::post('/submit',[TimeManagementController::class, 'submit'])->name('submit');
    Route::post('/{plan}/status', [TimeManagementController::class, 'updateStatus'])->name('status');
}); 
Route::get('/employee-availability', [EmployeeController::class, 'availabilityView'])->name('employee.availability');
Route::post('/employee-availability/{employee}', [EmployeeController::class, 'getAvailability']);
Route::post('/book-appointment', [EmployeeController::class, 'bookAppointment']);

// Contract Type CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/contract_type', [ContractTypeController::class, 'index'])->name('contract.type.info');
    Route::get('/contract_type_destroy/{id}', [ContractTypeController::class, 'destroy'])->name('contract.type.destroy');
    Route::post('/contract_type_save', [ContractTypeController::class, 'store'])->name('contract.type.store');
    Route::post('/contract_type_update', [ContractTypeController::class, 'update'])->name('contract.type.update');
});

//Employee Capicity State

    Route::group(['middleware' => 'web'], function(){ 
        Route::prefix('admin/employee/employee')->group(function() {
            Route::get('{employee}/postcodes', [EmployeePostcodeListController::class, 'index'])->name('employee-postcodes.index');
            Route::post('postcodes', [EmployeePostcodeListController::class, 'store'])->name('employee-postcodes.store');
            Route::get('postcodes/{id}', [EmployeePostcodeListController::class, 'edit'])->name('employee-postcodes.edit');
            Route::put('postcodes/{id}', [EmployeePostcodeListController::class, 'update'])->name('employee-postcodes.update');
            Route::delete('postcodes/{id}', [EmployeePostcodeListController::class, 'destroy'])->name('employee-postcodes.destroy');
        }); 
    });

    Route::get('/my-notifications', [NotificationController::class, 'index'])
        ->name('dashboard.notifications.index');

    Route::post('/my-notifications/read/{id}', [NotificationController::class, 'markAsRead'])
        ->name('dashboard.notifications.read');
        
    //Notification List of Employees
    Route::group(['middleware' => 'web'], function () {
        Route::get('/employee_notifications/{user}', [NotificationListController::class, 'index'])->name('employee.notification.index'); 
        Route::get('/get/employee/notification/', [NotificationListController::class, 'view'])->name('employee.notification.view'); 
        Route::get('/get/employee/response/', [NotificationListController::class, 'response'])->name('employee.notification.response'); 
        Route::get('/get/notification/list', [NotificationListController::class, 'list'])->name('get.notification.list');

        Route::post('/notification/mark-as-read/{id}', function ($id) {
            DB::table('notifications')
                ->where('id', $id)
                ->where('notifiable_id', Auth::id())
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        })->middleware('auth');

        Route::post('/notification/mark-all-read', function () {
            DB::table('notifications')
                ->where('notifiable_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        })->middleware('auth');


        Route::post('/notifications/{id}/read', fn($id) => tap(
            auth()->user()->notifications()->where('id',$id)->firstOrFail()
        )->markAsRead())->name('notifications.read');

        Route::post('/notifications/read-all', fn() => auth()->user()->unreadNotifications->markAsRead())
        ->name('notifications.read_all');



    });
 
//Salary Management
Route::group(['middleware' => ['auth']], function () {
    Route::get('/salary_management', [SalaryController::class, 'index'])->name('salary.index');
    Route::get('/salary_sheet/{id}', [SalarySheetController::class, 'index'])->name('salary.sheet');

    Route::get('/refresh_salary', [SalaryController::class, 'salary'])->name('salary.refresh');
    Route::get('/upload_salary/{id}', [SalarySheetController::class, 'salary'])->name('salary.employee.upload');
    Route::post('/salary-sheets/upsert', [SalaryController::class, 'upsert'])->name('salary_sheets.upsert');
    Route::get('/employees/{id}', [SalaryController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/tax-defaults', [SalaryController::class, 'taxDefaults'])
    ->name('employees.tax_defaults');
});


// Holiday  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/holiday_view', [HolidayController::class, 'index'])->name('holiday.info');
    Route::get('/holiday_destroy/{id}', [HolidayController::class, 'destroy'])->name('holiday.destroy');
    Route::post('/holiday_create', [HolidayController::class, 'store'])->name('holiday.create');
    Route::post('/holiday_update', [HolidayController::class, 'update'])->name('holiday.update');
    Route::get('/holiday_active/{id}', [HolidayController::class, 'active'])->name('holiday.active');
    Route::get('/holiday_deactive/{id}', [HolidayController::class, 'deactive'])->name('holiday.deactive');

});

// Holiday  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/leave_day_view', [LeaveDayController::class, 'index'])->name('leave.day.info');
    Route::get('/leave_day_destroy/{id}', [LeaveDayController::class, 'destroy'])->name('leave.day.destroy');
    Route::post('/leave_day_create', [LeaveDayController::class, 'store'])->name('leave.day.create');
    Route::post('/leave_day_update', [LeaveDayController::class, 'update'])->name('leave.day.update');
    Route::get('/leave_day_active/{id}', [LeaveDayController::class, 'active'])->name('leave.day.active');
    Route::get('/leave_day_deactive/{id}', [LeaveDayController::class, 'deactive'])->name('leave.day.deactive'); 

});

// License  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/license_view', [EmployeeLicenseController::class, 'index'])->name('license.info');
    Route::get('/license_destroy/{id}', [EmployeeLicenseController::class, 'destroy'])->name('license.destroy');
    Route::post('/license_create', [EmployeeLicenseController::class, 'store'])->name('license.create');
    Route::post('/license_update', [EmployeeLicenseController::class, 'update'])->name('license.update');
    Route::post('/license_suspend', [EmployeeLicenseController::class, 'suspend'])->name('license.suspend');

});
 

// Cloths  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/cloth_view', [EmployeeClothController::class, 'index'])->name('cloth.info');
    Route::delete('/cloth_destroy/{id}', [EmployeeClothController::class, 'destroy'])->name('cloth.destroy');
    Route::post('/cloth_create', [EmployeeClothController::class, 'store'])->name('cloth.create');
    Route::post('/cloth_update', [EmployeeClothController::class, 'update'])->name('cloth.update');

});

//Employee Documents 
Route::group(['middleware' =>   'web'], function(){
    Route::delete('/employee_image_destroy/{id}', [EmployeeDocumentController::class, 'destroy'])->name('employee.image.destroy'); 
    Route::post('/employee_upload', [EmployeeDocumentController::class, 'upload'])->name('employee.upload');
    Route::post('/employee_image_name', [EmployeeDocumentController::class, 'update'])->name('employee.image.rename'); 
    Route::get('employee_image_get/{employee_id}/{type}', [EmployeeDocumentController::class, 'getImage'])->name('employee.image.get');

    Route::get('employee_license_get/{employee_id}/{type}', [EmployeeDocumentController::class, 'getLicense'])->name('employee.license.get');
    Route::get('employee_document/{employee_id}', [EmployeeDocumentController::class, 'getDocument'])->name('employee.docum.get');



});


//Public Holidays
Route::group(['middleware' => 'web'], function () {
    Route::prefix('public-holidays')->name('public-holidays.')->group(function () {
        Route::get('/', [PublicHolidayController::class, 'index'])->name('index');
        Route::get('/fetch', [PublicHolidayController::class, 'fetch'])->name('fetch');
        Route::post('/store', [PublicHolidayController::class, 'store'])->name('store');
        Route::post('/update/{id}', [PublicHolidayController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PublicHolidayController::class, 'destroy'])->name('delete');
        Route::post('/import', [PublicHolidayController::class, 'importCSV'])->name('import');
        Route::get('/sample', [PublicHolidayController::class, 'downloadSample'])->name('download-sample');

        // ✅ This is what was missing
        Route::get('/{id}', [PublicHolidayController::class, 'show'])->name('show');
    });
});


//Address Employee CRUD
Route::group(['middleware' => 'web'], function () {

    Route::post('/emp_address_save', [EmployeeAddressController::class, 'store'])->name('emp.address.save');
    Route::post('/emp_address_update', [EmployeeAddressController::class, 'update'])->name('emp.address.update');
    Route::get('/emp_address_delete/{id}', [EmployeeAddressController::class, 'destroy'])->name('emp.address.delete');
    Route::get('/emp_address_main/{id}', [EmployeeAddressController::class, 'active'])->name('emp.address.active');
    Route::get('/emp_address_main_deactive/{id}', [EmployeeAddressController::class, 'deactive'])->name('emp.address.deactive');
    
    //Employee Skills CRUD
    Route::post('/skill_save', [SkillController::class, 'store'])->name('skills.save');
    Route::post('/skill_update', [SkillController::class, 'update'])->name('skills.update');
    Route::get('/skill_delete/{id}', [SkillController::class, 'destroy'])->name('skills.delete');
    //Emergency Contact CRUD
    Route::post('/emergency_save', [EmergencyContactController::class, 'store'])->name('emergency.save');
    Route::post('/emergency_update', [EmergencyContactController::class, 'update'])->name('emergency.update');
    Route::get('/emergency_delete/{id}', [EmergencyContactController::class, 'destroy'])->name('emergency.delete');
    //Employee Other Skills CRUD
    Route::post('/other_skill_save', [OtherSkillController::class, 'store'])->name('other.skills.save');
    Route::post('/other_skill_update', [OtherSkillController::class, 'update'])->name('other.skills.update');
    Route::get('/other_skill_delete/{id}', [OtherSkillController::class, 'destroy'])->name('other.skills.delete');
});

  

//Employee Leave CRUD
Route::group(['middleware' => ['web', 'auth']], function () {

    Route::post('/leave_save', [LeaveController::class, 'store'])->name('leave.store');
    Route::post('/leave_update', [LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave_delete/{id}', [LeaveController::class, 'destroy'])->name('leave.delete');

    Route::get('/leave_approve/{id}', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/go_representer', [LeaveController::class, 'representer'])->name('leave.representer');
    Route::post('/accept_leave_date', [LeaveController::class, 'accept'])->name('accept.leave.date');
    Route::post('/change_leave_date', [LeaveController::class, 'change'])->name('change.leave.date');

    Route::get('/get-employee-usernames', [LeaveController::class, 'getEmployees'])->name('leave.get.employee');
    Route::post('/getEmployees', [LeaveController::class, 'getEmployees'])->name('leave.employees');

    Route::get('/employee/{employeeId}/main-department', [LeaveController::class, 'getEmployeeMainDepartment'])->name('leave.employee.main.department');
    Route::get('/getDepartment/leader/{departmentId}', [LeaveController::class, 'getDepartmentLeaders'])->name('leave.department.leaders');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/leaves/overview', [LeaveController::class, 'overview'])
        ->name('employee.leaves.overview');

    Route::post('/employee/leaves/request', [LeaveController::class, 'save'])
        ->name('employee.leaves.request');
});

Route::prefix('leaves')->middleware(['web', 'auth'])->group(function () {
    Route::get('{id}/notes', [LeaveController::class, 'getNotes']);
    Route::post('{id}/notes/store',[LeaveController::class, 'storeNote']);
    Route::put('{id}/notes/update/{index}',[LeaveController::class, 'updateNote']);
    Route::delete('{id}/notes/delete/{index}', [LeaveController::class, 'deleteNote']);
});



//Employee Sick CRUD
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/employee-sick', [EmployeeSickController::class, 'index'])->name('employee.sick.index');
    // AJAX: get sick records for current employee profile
    Route::get('/employee-sick/employee/{employeeId}', [EmployeeSickController::class, 'byEmployee'])->name('employee.sick.by.employee');
    Route::post('/employee-sick/store', [EmployeeSickController::class, 'store'])->name('employee.sick.store');
    Route::get('/employee-sick/edit/{id}', [EmployeeSickController::class, 'edit'])->name('employee.sick.edit');
    Route::post('/employee-sick/update/{id}', [EmployeeSickController::class, 'update'])->name('employee.sick.update');
    Route::delete('/employee-sick/destroy/{id}', [EmployeeSickController::class, 'destroy'])->name('employee.sick.destroy');
    Route::delete('/employee-sick/{id}/document/{index}', [EmployeeSickController::class, 'destroyDocument'])->name('employee.sick.document.destroy');
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/employee_details', [EmployeeController::class, 'view'])->name('employee.info');
});

Route::middleware('auth')->group(function () {
    Route::get ('/daily-notes', [DailyReportNoteController::class,'index'])->name('daily.notes.index');
    Route::post('/daily-notes', [DailyReportNoteController::class,'store'])->name('daily.notes.store');
});
Route::middleware('auth')->group(function () {
    Route::get('/daily-attachments',  [DailyReportAttachmentController::class, 'indexByContext'])->name('daily.attach.index');
    Route::post('/daily-attachments', [DailyReportAttachmentController::class, 'storeByContext'])->name('daily.attach.store');
    Route::delete('/daily-attachments/{attachment}', [DailyReportAttachmentController::class, 'destroy'])->name('daily.attach.destroy');
});
// Daily Report 
Route::group(['middleware'=>'web'], function(){
    Route::get('/daily_report', [DailyReportController::class, 'index'])->name('daily.report');
    Route::get('/employee_daily_report/{employee_id}/{start_date}/{end_date}', [DailyReportController::class, 'report'])
    ->name('employee.daily.report');
});

Route::group(['middleware'=>'web'], function(){
   Route::prefix('admin/daily_report/work_place')->name('work.place.')->group(function () {
        Route::get('/', [DailyReportWorkPlaceController::class, 'index'])->name('index');
        Route::post('/', [DailyReportWorkPlaceController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DailyReportWorkPlaceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DailyReportWorkPlaceController::class, 'update'])->name('update');
        Route::delete('/{id}', [DailyReportWorkPlaceController::class, 'destroy'])->name('destroy');
        Route::get('/branch-address/{id}', [DailyReportWorkPlaceController::class, 'getBranchAddress']);
    });
    Route::get('/daily_report_month_analytics/{employee_id}', [DailyReportController::class, 'monthAnalytics'])
        ->name('daily.report.month.analytics');

    Route::get('/get/work-places', [DailyReportWorkPlaceController::class, 'getReport']);
    Route::post('/daily-reports/start', [DailyReportController::class, 'storeStart']);
    Route::post('/daily-reports/end', [DailyReportController::class, 'storeEnd']);
    Route::get('/daily-reports/get/time', [DailyReportController::class, 'getTime']);
    Route::delete('/daily_report_time/{id}', [DailyReportController::class, 'delete'])->name('daily.report.delete');
    
    Route::get('/check-attendance', [DailyReportController::class, 'checkTodayAttendance']);
    Route::post('/start-attendance', [DailyReportController::class, 'startAttendance']);

    Route::get('/employee_daily_search', [DailyReportController::class, 'EmployeeListSearch'])->name('daily.report.employee.list.search');
    Route::get('/employee_daily_list', [DailyReportController::class, 'EmployeeList'])->name('daily.report.employee.list');

    Route::get('/employee_daily_plan', [DailyReportController::class, 'daily_report'])->name('employee.daily.plan');
    Route::get('/employee/get/daily/plan/{employee_id}', [DailyReportController::class, 'getPlan'])->name('employee.get.daily.plan'); 
    Route::get('/weekly_report/{employee_id}', [DailyReportController::class, 'weeklyReport'])->name('weekly.report');

    Route::get('/daily_report_details/{employee_id}/{date}', [DailyReportController::class, 'dailyDetails'])->name('daily.report.details');
    Route::post('/daily_report_add_missing', [DailyReportController::class, 'storeMissingTime'])->name('daily.report.add_missing');
    Route::post('/daily_report/save', [DailyReportController::class, 'store'])->name('daily.report.save');
    Route::get('/daily_report_reload/{employee_id}/{date}', [DailyReportController::class, 'reload'])->name('daily.report.reload');
     Route::delete('/daily_report_time/{id}', [DailyReportController::class, 'delete'])->name('daily.report.delete');
    Route::post('/daily_report_complete', [DailyReportController::class, 'completeAndExport'])->name('daily.report.complete');
    Route::post('/verify-admin', [DailyReportController::class, 'verifyAdmin']);
    Route::post('/exit-admin', function () {
        Session::forget('force_admin_view');
        Session::forget('admin_verified_user_id');
        return response()->json(['success' => true]);
    });
    

    Route::get('/daily-report/history', [DailyReportController::class, 'getReportHistory']);

});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/attendance/analytics', [AttendanceAnalyticsController::class, 'index'])
        ->name('attendance.analytics');

    Route::get('/attendance/analytics/fetch', [AttendanceAnalyticsController::class, 'fetch'])
        ->name('attendance.analytics.fetch');
});
// Attendance Crudes 


//Error - Problem CRUD Ticket System

Route::group(['middleware' => 'auth'], function () {
    Route::get('/problem_view', [ProblemController::class, 'index'])->name('problem.view');
    Route::get('/problem_destroy/{id}', [ProblemController::class, 'destroy'])->name('problem.destroy');
    Route::get('/problem_edit/{id}', [ProblemController::class, 'edit'])->name('problem.edit');
    Route::get('/problem_create', [ProblemController::class, 'create'])->name('problem.create');
    Route::post('/problem_save', [ProblemController::class, 'store'])->name('problem.store');
    Route::put('/problem_update/{id}', [ProblemController::class, 'update'])->name('problem.update');
    Route::post('/problem_photo', [ProblemController::class, 'image'])->name('problem.save_photo');
    Route::get('/problem_open/{id}', [ProblemController::class, 'open'])->name('problem.open');
    Route::get('/problem_close/{id}', [ProblemController::class, 'close'])->name('problem.close');
    Route::get('/problem_progress/{id}', [ProblemController::class, 'progress'])->name('problem.progress');
    Route::post('/problem_close_save', [ProblemController::class, 'closeSave'])->name('problem.close.save');
    Route::get('/problem_photos/{id}', [ProblemController::class, 'photos'])->name('problem.photos');
    Route::get('/get/ticket/customer/product/{customer_id}', [ProblemController::class, 'getProduct'])->name('problem.get.customer.product');
    Route::get('/get/ticket/customer/{customer_id}', [ProblemController::class, 'getCustomer'])->name('problem.get.customer');
    Route::get('/get/ticket/get/customer', [ProblemController::class, 'getAllCustomer'])->name('problem.all.customer');
    Route::get('get/ticket/responsible', [ProblemController::class, 'getResponsible'])->name('problem.get.responsible');
    Route::post('check/old/tickets', [ProblemController::class, 'checkTicket'])->name('problem.check.ticket');
    Route::post('check/ticket/products', [ProblemController::class, 'getProducts'])->name('problem.check.ticket.product');
    Route::get('problem/profile/{ticket_id}', [ProblemController::class, 'profile'])->name('problem.profile');
    Route::get('/tickets/fetch', [ProblemController::class, 'fetch'])->name('tickets.fetch');
    Route::get('/tickets/kanban', [ProblemController::class, 'kanban'])->name('tickets.kanban');
    Route::post('/tickets/{ticket_id}/status', [ProblemController::class, 'updateStage'])->name('tickets.status');
    Route::get('get/ticket/kanban', [ProblemController::class, 'getTicket'])->name('problem.get.ticket');
    Route::get('/ticket/kanban/get', [ProblemController::class, 'getKanban'])->name('ticket.kanban.get');
    Route::get('/ticket/kanban/search', [ProblemController::class, 'searchKanban'])->name('ticket.kanban.search');
    Route::get('/ticket/kanban/update/{ticket_id}/{stage}', [ProblemController::class, 'updateStage'])->name('ticket.kanban.update');
    Route::get('/ticket/{id}/progress', [ProblemController::class, 'getProgress']);
    Route::post('/ticket/{id}/update-type', [ProblemController::class, 'updateType'])->name('ticket.updateType');
    Route::post('ticket/upload/file', [TicketFileController::class, 'store'])->name('ticket.upload');
    Route::get('fetch/ticket/files/{id}', [TicketFileController::class, 'index'])->name('ticket.files.index');
    Route::delete('/ticket/file/{id}', [TicketFileController::class, 'destroy'])->name('ticket.file.delete');
    Route::put('/ticket/file/{id}', [TicketFileController::class, 'update'])->name('ticket.file.update');
    Route::post('/customer/tickets/load', [ProblemController::class, 'loadTickets']);
    Route::post('/ticket/assign/{id}', [ProblemController::class, 'assignTicket'])->name('ticket.assign');
    Route::post('/ticket/{problem}/update-status', [ProblemController::class, 'updateStatus'])->name('ticket.updateStatus');
    Route::put('/ticket-reports/{report}', [TicketReportController::class, 'update'])->name('ticket-reports.update');
    Route::delete('/ticket-reports/{report}', [TicketReportController::class, 'destroy'])->name('ticket-reports.destroy');
    Route::get('/tickets/lead-stage-context', [ProblemController::class, 'ticketLeadStageContext'])
        ->name('tickets.lead-stage-context');
    Route::get('/tickets/appointment-availability', [ProblemController::class, 'checkTicketAppointmentAvailability'])
        ->name('tickets.appointment-availability');

    Route::get('/tickets/lead-stage-context', [ProblemController::class, 'ticketLeadStageContext'])
        ->name('tickets.lead-stage-context');

});


// Problem Tasks - Ticket Tasks 

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Ticket Tasks
    |--------------------------------------------------------------------------
    */
    Route::prefix('ticket-tasks')->name('ticketTasks.')->group(function () {
        Route::get('/load/{problem_id}', [TicketTaskController::class, 'loadTasks'])
            ->name('load')
            ->whereNumber('problem_id');

        Route::get('/timeline/{ticket_id}', [TicketTaskController::class, 'timeline'])
            ->name('timeline')
            ->whereNumber('ticket_id');

        Route::post('/', [TicketTaskController::class, 'store'])
            ->name('store');

        Route::post('/{task}/toggle-done', [TicketTaskController::class, 'toggleDone'])
            ->name('toggleDone')
            ->whereNumber('task');

        Route::post('/{task}/update-status', [TicketTaskController::class, 'updateStatus'])
            ->name('updateStatus')
            ->whereNumber('task');

        Route::put('/{task}', [TicketTaskController::class, 'update'])
            ->name('update')
            ->whereNumber('task');

        Route::delete('/{task}', [TicketTaskController::class, 'destroy'])
            ->name('destroy')
            ->whereNumber('task');

        Route::get('/{task}', [TicketTaskController::class, 'show'])
            ->name('show')
            ->whereNumber('task');
    });

    Route::post('/ticket-reports/store', [TicketReportController::class, 'store'])->name('ticket-reports.store');
    Route::put('/ticket-reports/{report}', [TicketReportController::class, 'update'])->name('ticket-reports.update');
    Route::delete('/ticket-reports/{report}', [TicketReportController::class, 'destroy'])->name('ticket-reports.destroy');
    Route::post('/ticket-reports/{report}/like', [TicketReportController::class, 'like'])->name('ticket-reports.like');
    Route::post('/ticket-reports/comments/store', [TicketReportController::class, 'storeComment'])->name('ticket-reports.comments.store');

    Route::post('/ticket/comments', [ProblemCommentController::class, 'store'])->name('comments.store');
    Route::get('/ticket/comments/{ticket_id}', [ProblemCommentController::class, 'fetch'])->name('comments.fetch');
    Route::put('/ticket/comments/{comment}', [ProblemCommentController::class, 'update'])->name('comments.update');
    Route::delete('/ticket/comments/{comment}', [ProblemCommentController::class, 'destroy'])->name('comments.destroy');

    Route::prefix('ticket-image')->group(function () {
        Route::post('/upload', [TicketImageController::class, 'upload'])->name('ticket.image.upload');
        Route::get('/list/{ticket_id}', [TicketImageController::class, 'list'])->name('ticket.image.list');
        Route::delete('/delete/{id}', [TicketImageController::class, 'destroy'])->name('ticket.image.delete');
        Route::post('/rename/{id}', [TicketImageController::class, 'rename'])->name('ticket.image.rename');
    });


    /*
    |--------------------------------------------------------------------------
    | Ticket employee/team routes
    |--------------------------------------------------------------------------
    | Add these inside your auth middleware group.
    */

        Route::get('/tickets/{problem}/employees', [TicketEmployeeController::class, 'index'])
            ->name('ticket.employees.index')
            ->whereNumber('problem');

        Route::post('/tickets/{problem}/employees/sync', [TicketEmployeeController::class, 'sync'])
            ->name('ticket.employees.sync')
            ->whereNumber('problem');

        Route::get('/ticket-employees/search', [TicketEmployeeController::class, 'search'])
            ->name('ticket.employees.search');


    /*
    |--------------------------------------------------------------------------
    | Ticket Reports
    |--------------------------------------------------------------------------
    */
    Route::prefix('ticket-reports')->name('ticket-reports.')->group(function () {
        Route::post('/store', [TicketReportController::class, 'store'])
            ->name('store');

        Route::put('/{report}', [TicketReportController::class, 'update'])
            ->name('update')
            ->whereNumber('report');

        Route::delete('/{report}', [TicketReportController::class, 'destroy'])
            ->name('destroy')
            ->whereNumber('report');

        Route::post('/{report}/like', [TicketReportController::class, 'like'])
            ->name('like')
            ->whereNumber('report');

        Route::post('/comments/store', [TicketReportController::class, 'storeComment'])
            ->name('comments.store');
    });

    /*
  |--------------------------------------------------------------------------
  | Ticket Appointments - real MainAppointment CRUD linked by problem_id
  |--------------------------------------------------------------------------
  */

    Route::prefix('tickets/{problem}/appointments')->name('ticket.appointments.')->group(function () {
        Route::get('/', [TicketAppointmentController::class, 'index'])->name('index')->whereNumber('problem');
        Route::post('/', [TicketAppointmentController::class, 'store'])->name('store')->whereNumber('problem');
        Route::post('/check-availability', [TicketAppointmentController::class, 'checkAvailability'])->name('check')->whereNumber('problem');
        Route::put('/{appointment}', [TicketAppointmentController::class, 'update'])->name('update')->whereNumber('problem')->whereNumber('appointment');
        Route::delete('/{appointment}', [TicketAppointmentController::class, 'destroy'])->name('destroy')->whereNumber('problem')->whereNumber('appointment');
    });

    Route::get('/ticket-appointment-employees/search', [TicketAppointmentController::class, 'employees'])
        ->name('ticket.appointments.employees.search');
    /*
    |--------------------------------------------------------------------------
    | Backward-compatible old route names
    |--------------------------------------------------------------------------
    | Keep these if old Blade/JS still calls them.
    */
    Route::post('/ticket-report/{report}/like', [TicketReportController::class, 'like'])
        ->name('ticket-report.like')
        ->whereNumber('report');

    Route::post('/ticket-report/comment/store', [TicketReportController::class, 'storeComment'])
        ->name('ticket-report-comments.store');

    /*
    |--------------------------------------------------------------------------
    | Error Info
    |--------------------------------------------------------------------------
    */
    Route::get('/error', [ErrorController::class, 'index'])->name('error.info');
    Route::post('/error_save', [ErrorController::class, 'store'])->name('errors.store');
    Route::post('/error/update', [ErrorController::class, 'update'])->name('error.update');
    Route::post('/error/status', [ErrorController::class, 'updateStatus'])->name('error.status');
    Route::delete('/error/delete/{id}', [ErrorController::class, 'destroy'])->name('error.destroy');
    Route::post('/add-new-error', [ErrorController::class, 'addNewError'])->name('addNewError');
    Route::get('/get-error-codes', [ErrorController::class, 'getErrorCodes']);

    /*
    |--------------------------------------------------------------------------
    | Ticket Images
    |--------------------------------------------------------------------------
    */
    Route::prefix('ticket-image')->group(function () {
        Route::post('/upload', [TicketImageController::class, 'upload'])->name('ticket.image.upload');
        Route::get('/list/{ticket_id}', [TicketImageController::class, 'list'])->name('ticket.image.list');
        Route::delete('/delete/{id}', [TicketImageController::class, 'destroy'])->name('ticket.image.delete');
        Route::post('/rename/{id}', [TicketImageController::class, 'rename'])->name('ticket.image.rename');
    });

    /*
    |--------------------------------------------------------------------------
    | Ticket Comments
    |--------------------------------------------------------------------------
    */
    Route::post('/ticket/comments', [ProblemCommentController::class, 'store'])->name('comments.store');
    Route::put('/ticket/comments/{comment}', [ProblemCommentController::class, 'update'])->name('comments.update');
    Route::delete('/ticket/comments/{comment}', [ProblemCommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/ticket/comments/{ticket_id}', [ProblemCommentController::class, 'fetch'])->name('comments.fetch');

    Route::post('/problem-comments/store', [ProblemCommentController::class, 'storeComment']);
});
Route::prefix('admin/maintenance/contracts')
    ->name('admin.maintenance.contracts.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/customer-search', [CustomerMaintenanceContractController::class, 'customerSearch'])->name('customer_search');
        Route::get('/branch-search', [CustomerMaintenanceContractController::class, 'branchSearch'])->name('branch_search');
        Route::get('/technicians', [CustomerMaintenanceContractController::class, 'technicians'])->name('technicians');

        Route::get('/incoming', [CustomerMaintenanceContractController::class, 'incoming'])->name('incoming');
        Route::get('/calendar-feed', [CustomerMaintenanceContractController::class, 'calendarFeed'])->name('calendar_feed');
        Route::get('/kanban-feed', [CustomerMaintenanceContractController::class, 'kanbanFeed'])->name('kanban_feed');

        Route::get('/checklists', [CustomerMaintenanceContractController::class, 'ajaxIndex'])->name('checklists.ajax_index');
        Route::get('/checklists/{checklist}', [CustomerMaintenanceContractController::class, 'ajaxShow'])->name('checklists.ajax_show');

        Route::get('/', [CustomerMaintenanceContractController::class, 'index'])->name('index');
        Route::get('/create', [CustomerMaintenanceContractController::class, 'create'])->name('create');
        Route::post('/', [CustomerMaintenanceContractController::class, 'store'])->name('store');

        Route::post('/bulk-status', [CustomerMaintenanceContractController::class, 'bulkStatus'])->name('bulk-status');
        Route::post('/bulk-delete', [CustomerMaintenanceContractController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/kanban-update', [CustomerMaintenanceContractController::class, 'kanbanUpdate'])->name('kanban-update');

        Route::get('/{contract}', [CustomerMaintenanceContractController::class, 'show'])->whereNumber('contract')->name('show');
        Route::get('/{contract}/edit', [CustomerMaintenanceContractController::class, 'edit'])->whereNumber('contract')->name('edit');
        Route::put('/{contract}', [CustomerMaintenanceContractController::class, 'update'])->whereNumber('contract')->name('update');
    });


Route::prefix('admin/maintenance/checklists')
->name('admin.maintenance_checklists.')
->middleware(['web', 'auth'])
->group(function () {
    Route::get('/', [MaintenanceChecklistController::class, 'index'])->name('index');
    Route::post('/', [MaintenanceChecklistController::class, 'store'])->name('store');
    Route::put('/{maintenance_checklist}', [MaintenanceChecklistController::class, 'update'])->name('update');
    Route::patch('/{maintenance_checklist}/archive',   [MaintenanceChecklistController::class, 'archive'])->name('archive');
    Route::patch('/{maintenance_checklist}/unarchive', [MaintenanceChecklistController::class, 'unarchive'])->name('unarchive');
    Route::delete('/{maintenance_checklist}', [MaintenanceChecklistController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/restore', [MaintenanceChecklistController::class, 'restore'])->name('restore');
    Route::get('/{maintenance_checklist}/edit-json', [MaintenanceChecklistController::class, 'editJson'])->name('edit_json');
    Route::post('/bulk', [MaintenanceChecklistController::class, 'bulk'])->name('bulk');
});
 

//  User management
Route::middleware(['web', 'auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Legacy user routes
    |--------------------------------------------------------------------------
    */
    Route::get('/user', [UserController::class, 'index'])->name('user.view');
    Route::get('/user_destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::post('/user_save', [UserController::class, 'store'])->name('user.store');
    Route::post('/user_edit', [UserController::class, 'edit'])->name('user.edit');

    Route::post('/change_password', [UserController::class, 'change_password'])->name('user.change.pass');

    Route::get('/admin_user', [UserController::class, 'admin_user'])->name('user.admin');
    Route::get('/admin_destroy/{id}', [UserController::class, 'admin_destroy'])->name('user.admin.destroy');

    Route::get('/make_limit/{id}', [UserController::class, 'make_limit'])->name('user.make.limit');
    Route::get('/make_admin/{id}', [UserController::class, 'make_admin'])->name('user.make.admin');

    Route::get('/deactive/{id}', [UserController::class, 'deactive'])->name('user.deactive');
    Route::get('/active/{id}', [UserController::class, 'active'])->name('user.active');

    Route::get('/limit_user', [UserController::class, 'limit_user'])->name('user.limit');
    Route::get('/limit_destroy/{id}', [UserController::class, 'limit_destroy'])->name('user.limit.destroy');
    Route::post('/limit_edit', [UserController::class, 'limit_edit'])->name('user.limit.edit');
    Route::post('/limit_save', [UserController::class, 'limit_store'])->name('user.limit.store');

    Route::get('/photo_create', [UserController::class, 'photo_create'])->name('user.photo');
    Route::post('/save_user_photo', [UserController::class, 'save_photo'])->name('user.photo.save');

    Route::post('/logoff/{user}', [UserController::class, 'logOffUser'])->name('user.logoff');

    Route::get('/has_permission/{user_id}/{roll}', [UserController::class, 'hasPremission'])->name('user.has_permission');

    Route::post('/users/{user}/password', [UserController::class, 'updatePassword'])->name('user.password.update');

    /*
    |--------------------------------------------------------------------------
    | New AJAX admin users module
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/users', [UserController::class, 'adminUsersPage'])->name('admin.users.page');
    Route::get('/admin/users/fetch', [UserController::class, 'adminUsersFetch'])->name('admin.users.fetch');

    Route::post('/admin/users', [UserController::class, 'adminUsersStore'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'adminUsersUpdate'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'adminUsersDestroy'])->name('admin.users.destroy');

    Route::post('/admin/users/{user}/toggle-active', [UserController::class, 'adminUsersToggleActive'])->name('admin.users.toggleActive');
    Route::post('/admin/users/{user}/toggle-admin', [UserController::class, 'adminUsersToggleAdmin'])->name('admin.users.toggleAdmin');

    Route::post('/admin/users/{user}/password', [UserController::class, 'adminUsersPassword'])->name('admin.users.password');
});
//  User Roll CRUD
 Route::middleware(['auth'])->prefix('user-rolls')->name('user-rolls.')->group(function () {
    Route::get('/', [UserRollController::class, 'index'])->middleware('permission:Users,read')->name('index'); // FIX P0-13: index analog zu ajax/store absichern
    Route::get('/ajax', [UserRollController::class, 'ajaxIndex'])->middleware('permission:Users,read')->name('ajax');
    Route::post('/', [UserRollController::class, 'store'])->middleware('permission:Users,add')->name('store');
    Route::put('/{userRoll}', [UserRollController::class, 'update'])->middleware('permission:Users,update')->name('update');
    Route::delete('/{userRoll}', [UserRollController::class, 'destroy'])->middleware('permission:Users,delete')->name('destroy');
});


// Position Type CRUD
Route::group(['middleware' => ['web','auth']], function () {
    Route::get('/position', [PositionController::class, 'index'])->name('position.index');

    // Positions list (AJAX)
    Route::get('/position/list', [PositionController::class, 'list'])->name('position.list');

    // Positions CRUD (AJAX)
    Route::post('/position', [PositionController::class, 'storeAjax'])->name('position.store.ajax');
    Route::put('/position/{position}', [PositionController::class, 'updateAjax'])->name('position.update.ajax');
    Route::delete('/position/{position}', [PositionController::class, 'destroyAjax'])->name('position.destroy.ajax');
    Route::patch('/position/{position}/toggle', [PositionController::class, 'toggle'])->name('position.toggle');

    // Description (AJAX)
    Route::patch('/position/{position}/description', [PositionController::class, 'updateDescriptionAjax'])->name('position.description.ajax');

    // Qualifications (AJAX)
    Route::get('/position/qualifications', [PositionController::class, 'qualificationsBoard'])->name('position.qual.board');
    Route::post('/position/qualifications', [PositionController::class, 'qualificationStore'])->name('position.qual.store');
    Route::put('/position/qualifications/{qid}', [PositionController::class, 'qualificationUpdate'])->name('position.qual.update');
    Route::delete('/position/qualifications/{qid}', [PositionController::class, 'qualificationDestroy'])->name('position.qual.destroy');
    Route::post('/position/qualifications/reorder', [PositionController::class, 'qualificationReorder'])->name('position.qual.reorder');
    // Qualification hierarchy / replacement matrix
    Route::get('/position/hierarchy', [PositionController::class, 'hierarchyBoard'])->name('position.hierarchy.board');

    Route::post('/position/hierarchy/save', [PositionController::class, 'hierarchySave'])->name('position.hierarchy.save'); 
    Route::post('/position/hierarchy/auto-generate', [PositionController::class, 'hierarchyAutoGenerate'])->name('position.hierarchy.auto'); 
    Route::post('/position/hierarchy/check', [PositionController::class, 'hierarchyCheck'])->name('position.hierarchy.check'); 
    Route::post('/positions/store-json', [PositionController::class, 'storeJson'])->name('position.store.json');

    // Drag-drop assignment (position -> qualification)
    Route::post('/position/assign-qualification', [PositionController::class, 'assignQualification'])->name('position.assign.qual');
});

// Product CRUD
 
Route::group(['middleware' => 'web', 'is_Admin'], function () {
    Route::get('/product', [ProductController::class, 'index'])->name('product.info');
    Route::get('/product_details/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/product_destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::post('/product_save', [ProductController::class, 'store'])->name('product.store'); 
    Route::get('/product/export/no-images', [ProductController::class, 'exportNoImageProductsCsv'])->name('products.export.no-images');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product/final/summary/{id}', [ProductController::class, 'finalSummary']);
    Route::get('/product_publish/{id}', [ProductController::class, 'publish'])->name('product.publish');
    Route::get('/product_unpublish/{id}', [ProductController::class, 'unpublish'])->name('product.unpublish');
    Route::get('/product_create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/product_create_get_brand', [ProductController::class, 'getBrand'])->name('product.create.get.brand');
    Route::post('/product/store-brand', [ProductController::class, 'storeBrand'])->name('product.store.brand');
    Route::get('/product/get_distributor', [ProductController::class, 'getDistributor'])->name('product.get.distributor');
    Route::post('/product/store-distributor', [ProductController::class, 'storeDistributor'])->name('product.store.distributor');
    Route::get('/product/all_distributor', [ProductController::class, 'allDistributor'])->name('product.all.distributor'); 
    // AJAX list endpoint
    Route::get('/product/list', [ProductController::class, 'ajaxList'])->name('products.list');  
    Route::get('/product/list', [ProductController::class, 'ajaxList'])->name('products.list'); 
    Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk'); 
    Route::get('/product/{id}/distributor-prices', [ProductController::class, 'getDistributorPrices'])->name('product.distributor.prices');
    Route::get('/product/{product}/history', [ProductController::class, 'history'])->name('product.history');
    Route::get('/product/{id}/distributor', [ProductController::class, 'getProductDistributor'])->name('product.distributor');
    Route::post('/product/distributor/save', [ProductController::class, 'saveDistributorData'])->name('product.distributor.save');
    // AJAX for distributor loading
    Route::get('/ajax/distributors', [DistributorController::class, 'index'])->name('ajax.distributors');
      Route::get('/ajax/distributors/by-brand', [DistributorController::class, 'byBrand'])->name('ajax.distributors.by-brand');
    Route::post('/products/{product}/distributor-prices',[DistributorPriceController::class, 'storeSingle'])->name('products.distributor-prices.store');
    // Optional: Save distributor modal
    Route::post('/ajax/distributor/save', [DistributorController::class, 'distributorStore'])->name('ajax.distributor.save');
    // Product Description
    Route::get('/product_create_description/{id}', [ProductDescriptionController::class, 'index'])->name('product.create.description');
    Route::post('/product/description/store', [ProductDescriptionController::class, 'storeAjax'])->name('product.description.store.ajax');
    Route::post('/product_description_store', [ProductDescriptionController::class, 'store'])->name('product.description.store');
    Route::get('/product_description_destroy/{id}', [ProductDescriptionController::class, 'destroy'])->name('product.discription.destroy');
    Route::post('/product_description_update', [ProductDescriptionController::class, 'update'])->name('product.description.update');
    // Setup the KW and Temp 
    Route::get('/product_wp/{id}', [ProductWPController::class, 'index'])->name('product_wp');
    Route::get('/product_analytic/{id}', [ProductWPController::class, 'analytic'])->name('product_wp_analytic');
    Route::get('/get_product_wp/{id}', [ProductWPController::class, 'get'])->name('product_wp.get');
    Route::post('/product_wp/{id}/update', [ProductWPController::class, 'update'])->name('product_wp.update');
    Route::delete('/product_wp/{id}/delete', [ProductWPController::class, 'destroy'])->name('product_wp.delete'); 
    Route::post('/product/description/store', [ProductDescriptionController::class, 'storeAjax'])->name('product.description.store.ajax');
    Route::get('/product/description/get/{product_id}', [ProductDescriptionController::class, 'getDescriptions']);
    Route::delete('/product/description/delete/{id}', [ProductDescriptionController::class, 'deleteDescription']);
    Route::post('/product/description/update/{id}', [ProductDescriptionController::class, 'updateAjax']); 
    //Product Image CRUDE
    Route::get('/product_create_image/{id}', [ProductImageController::class, 'create'])->name('product.create.image');
    Route::post('/product_image_save', [ProductImageController::class, 'store'])->name('product.image.save');
    Route::get('/product_image_destroy/{id}', [ProductImageController::class, 'destroy'])->name('product.image.destroy');
    Route::post('/product_image_update', [ProductImageController::class, 'update'])->name('product.image.update');
    Route::post('/product_images/upload', [ProductImageController::class, 'upload'])->name('product_images.upload');
    Route::get('/product_images/list/{product}', [ProductImageController::class, 'list']);
    Route::post('/product_images/update-name/{id}', [ProductImageController::class, 'updateName']);
    Route::delete('/product_images/delete/{id}', [ProductImageController::class, 'delete']); 
    Route::post('/productsList/{product}/image', [ProductImageController::class, 'updateMain'])->name('products.image.update'); 
    Route::get('/productsList/{product}/images', [ProductImageController::class, 'index'])  ->name('products.images.index'); 
    Route::delete('/productsList/images/{image}', [ProductImageController::class, 'destroyImage']); 
    //Product Document CRUDE
    Route::get('/product_create_document/{id}', [ProductDocumentsController::class, 'create'])->name('product.create.document');
    Route::post('/product_document_save', [ProductDocumentsController::class, 'store'])->name('product.document.save');
    Route::get('/product_document_destroy/{id}', [ProductDocumentsController::class, 'destroy'])->name('product.document.destroy');
    Route::post('/product_document_update', [ProductDocumentsController::class, 'update'])->name('product.document.update');
    //Product Image CRUDE
    Route::get('/product_installation/{id}', [ProductInstallationCaseController::class, 'index'])->name('product.installation');
    Route::get('/product_installation_destroy/{id}', [ProductInstallationCaseController::class, 'destroy'])->name('product.installation.destroy');
    Route::post('/product_installation_save', [ProductInstallationCaseController::class, 'store'])->name('product.installation.save');
    Route::post('/product_installation_update', [ProductInstallationCaseController::class, 'update'])->name('product.installation.update');
    //Product Type
    Route::get('/product_type/{id}', [ProductTypeController::class, 'index'])->name('product.type');
    Route::get('/product_create_get_sub_article', [ProductController::class, 'getSubArticle'])->name('product.get.sub.article');
    Route::post('/product_type_save', [ProductTypeController::class, 'store'])->name('product.type.save');
    Route::post('/product_type_image', [ProductTypeController::class, 'save_image'])->name('product.type.save.image');
    Route::post('/product_type_update', [ProductTypeController::class, 'update'])->name('product.type.update');
    //Product PV CRUD
    Route::get('/product_pv/{id}/{article}', [ProductPVController::class, 'index'])->name('product.pv.create');
    Route::post('/product_pv_save', [ProductPVController::class, 'store'])->name('product.pv.store');
    Route::get('/product_pv_load', [ProductPVController::class, 'load'])->name('product.pv.load');
    //Battery CRUD
    Route::post('/batteries', [BatteryController::class, 'store'])->name('product.battery.store');
    Route::get('/battery_load', [BatteryController::class, 'load'])->name('product.battery.load');
    Route::post('/battery_system_save', [BatterySystemController::class, 'store'])->name('battery.system.save');
    Route::get('/battery_system_load', [BatterySystemController::class, 'load'])->name('battery.system.load');
    Route::get('/battery_inverter_load', [BatteryInverterController::class, 'load'])->name('battery.inverter.load');
    Route::post('/battery_inverters', [BatteryInverterController::class, 'store'])->name('battery.inverter.store');
    Route::post('/electric-vehicles', [ElectricVehicleController::class, 'store'])->name('product.electric.car.store');
    Route::get('/electric_vehicle_load', [ElectricVehicleController::class, 'load'])->name('product.electric.car.load');
    Route::get('/power_optimizer_load', [PowerOptimizerController::class, 'load'])->name('power.optimizer.load');
    Route::post('/power-optimizers', [PowerOptimizerController::class, 'store'])->name('power.optimizer.store');
    Route::get('/inverter_load', [InverterController::class, 'load'])->name('inverter.load');
    Route::post('/inverters', [InverterController::class, 'store'])->name('inverter.store');
    Route::post('/backup-generators', [BackupGeneratorController::class, 'store'])->name('backup.generator');
    Route::get('/backup_generator_load', [BackupGeneratorController::class, 'store'])->name('backup.generator.load');
    //Inveter CRUD
    Route::get('/product_inveter/{id}', [RadiatorController::class, 'index'])->name('product.inveter.create');
    Route::post('/product_inveter_save', [RadiatorController::class, 'store'])->name('product.inveter.store');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/products/difference', [ProductDifferenceController::class, 'index'])->name('admin.products.difference');
        Route::post('/products/difference/compare', [ProductDifferenceController::class, 'compare'])->name('admin.products.difference.compare');
    });
// Normaußentemperaturate
Route::group(['middleware' => 'auth'], function () {
    Route::get('/temp_view', [TemperatureController::class, 'index'])->name('temp.view');
    Route::post('/temp_save', [TemperatureController::class, 'store'])->name('temp.store');
    Route::post('/temp_update', [TemperatureController::class, 'update'])->name('temp.update');
    Route::get('/temp_destroy/{id}', [TemperatureController::class, 'destroy'])->name('temp.delete');
    Route::get('/temp_duplicate/{id}', [TemperatureController::class, 'duplicate'])->name('temp.duplicate');
});


Route::group(['prefix' => 'products', 'middleware' => ['auth']], function () {
    // Single product duplicate
    Route::post('{product}/duplicate', [ProductController::class, 'duplicateSingle'])->name('products.duplicate.single');
    // Bulk duplicate (from checkbox selection)
    Route::post('duplicate/bulk', [ProductController::class, 'bulkDuplicate'])->name('products.duplicate.bulk');
});

Route::group(['prefix' => 'products', 'middleware' => ['auth']], function () {
    // Create multiple descriptions for a product
    Route::post('{product}/descriptions/bulk-store', [ProductDescriptionController::class, 'bulkStore'])->name('products.descriptions.bulkStore');
    // Update single description
    Route::put('descriptions/{description}', [ProductDescriptionController::class, 'updateDescription'])->name('products.descriptions.update');
    // Delete single description
    Route::delete('descriptions/{description}', [ProductDescriptionController::class, 'destroyDescription'])->name('products.descriptions.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/products/import', [ProductImportController::class, 'index'])->name('products.import.index');
    Route::post('/products/import/preview', [ProductImportController::class, 'preview'])->name('products.import.preview');
    Route::post('/products/import/store', [ProductImportController::class, 'store'])->name('products.import.store');
});
Route::prefix('product_documents')->group(function () {
    Route::post('upload', [ProductDocumentsController::class, 'upload'])->name('product_documents.upload');
    Route::get('list/{product_id}', [ProductDocumentsController::class, 'list'])->name('product_documents.list');
    Route::post('delete/{id}', [ProductDocumentsController::class, 'delete'])->name('product_documents.delete');
    Route::post('update-name/{id}', [ProductDocumentsController::class, 'updateName']);

});
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Product favorite lists UI
    Route::get('/products/favorite-lists', [ProductFavoriteListController::class, 'index'])->name('products.favorite-lists');
    // AJAX: lists + stats
    Route::get('/ajax/products/favorite-lists', [ProductFavoriteListController::class, 'ajaxLists'])->name('ajax.products.favorite-lists');
    // AJAX: create/update/delete list
    Route::post('/ajax/products/favorite-lists', [ProductFavoriteListController::class, 'store'])->name('ajax.products.favorite-lists.store');
    Route::put('/ajax/products/favorite-lists/{list}', [ProductFavoriteListController::class, 'update'])->name('ajax.products.favorite-lists.update');
    Route::delete('/ajax/products/favorite-lists/{list}', [ProductFavoriteListController::class, 'destroy'])->name('ajax.products.favorite-lists.destroy');
    // Products inside a folder
    Route::get('/ajax/products/favorite-lists/{list}/products', [ProductFavoriteListController::class, 'ajaxProducts'])->name('ajax.products.favorite-lists.products');
    Route::post('/ajax/products/favorite-lists/{list}/products', [ProductFavoriteListController::class, 'storeItem'])->name('ajax.products.favorite-lists.products.store');
    Route::delete('/ajax/products/favorite-lists/{list}/products/{item}', [ProductFavoriteListController::class, 'destroyItem'])->name('ajax.products.favorite-lists.products.destroy');
    // Global product search for "add to list"
    Route::get('/ajax/products/search', [ProductController::class, 'ajaxSearch'])->name('ajax.products.search');
    // Optional alias to entry UI
    Route::get('/products/favorites', [ProductFavoriteListController::class, 'index'])->name('product.favorites.index');
    // --- stamp routes (as you already have) ---
    Route::get('/stamp-articles/lists', [StampArticleListController::class, 'index'])->name('stamp.lists.index');
    Route::get('/ajax/stamp-articles/lists', [StampArticleListController::class, 'ajaxLists'])->name('ajax.stamp.lists');
    Route::post('/ajax/stamp-articles/lists', [StampArticleListController::class, 'store'])->name('ajax.stamp.lists.store');
    Route::put('/ajax/stamp-articles/lists/{list}', [StampArticleListController::class, 'update'])->name('ajax.stamp.lists.update');
    Route::delete('/ajax/stamp-articles/lists/{list}', [StampArticleListController::class, 'destroy'])->name('ajax.stamp.lists.destroy');
    Route::get('/ajax/stamp-articles/lists/{list}/items', [StampArticleListController::class, 'listStampArticles'])->name('ajax.stamp.lists.items');
    Route::post('/ajax/stamp-articles/lists/{list}/attach', [StampArticleListController::class, 'attachStampArticle'])->name('ajax.stamp.lists.attach');
    Route::delete('/ajax/stamp-articles/lists/{list}/detach/{stampArticle}', [StampArticleListController::class, 'detachStampArticle'])->name('ajax.stamp.lists.detach');
    Route::get('/ajax/stamp-articles/search', [StampArticleListController::class, 'ajaxSearch'])->name('ajax.stamp.articles.search');
});
Route::prefix('products')->name('products.')->group(function () {
    Route::get('{product}/suppliers/data', [DistributorController::class, 'data'])->name('suppliers.data');  
    Route::post('{product}/distributor-prices', [DistributorController::class, 'save'])->name('distributor-prices.store'); // POST: save new price (JSON)
    Route::delete('distributor-prices/{price}', [DistributorController::class, 'delete'])->name('distributor-prices.destroy'); // DELETE: remove price (JSON, optional)
    Route::put('{product}/distributor-prices/{price}', [DistributorController::class, 'updatePrice'])->name('distributor-prices.update');
});
// RadiatorInstallation CRUDE (Heizkörper)
Route::middleware('auth')->group(function () {
    Route::get('/radiator_config_view', [RadiatorInstallationController::class, 'index'])->name('radiator.config.view');
    Route::get('/radiator_config_customers', [RadiatorInstallationController::class, 'ajaxCustomers'])->name('radiator.config.customers');
    Route::get('/radiator_config_objects/{customer}', [RadiatorInstallationController::class, 'ajaxObjects'])->name('radiator.config.objects');
    Route::get('/radiator_config_list', [RadiatorInstallationController::class, 'ajaxList'])->name('radiator.config.list');
    Route::post('/radiator_config_save', [RadiatorInstallationController::class, 'store'])->name('radiator.config.store');
    Route::post('/radiator_config_update', [RadiatorInstallationController::class, 'update'])->name('radiator.config.update');
    Route::delete('/radiator_config_delete/{id}', [RadiatorInstallationController::class, 'destroy'])->name('radiator.config.delete');
});

// IDS CRUD 
// Product TIles CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/tiles_view', [TilesController::class, 'index'])->name('tiles.view');
    Route::post('/tiles_save', [TilesController::class, 'store'])->name('tiles.save');
    Route::get('/tiles_destroy/{id}', [TilesController::class, 'destroy'])->name('tiles.delete');
    Route::post('/tiles_update', [TilesController::class, 'update'])->name('tiles.update');
});

//Measure CRUD
Route::group(['middleware' => 'web', 'is_Admin'], function () {
    Route::get('/measure', [MeasureController::class, 'index'])->name('measure.info');
    Route::get('/measure_destroy/{id}', [MeasureController::class, 'destroy'])->name('measure.destroy');
    Route::post('/measure_save', [MeasureController::class, 'store'])->name('measure.store');
    Route::post('/measure_update', [MeasureController::class, 'update'])->name('measure.update');

});

// Brand CRUD

Route::middleware(['web', 'auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Brand / Hersteller
    |--------------------------------------------------------------------------
    */
    Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
    Route::post('/brand/save', [BrandController::class, 'store'])->name('brand.store');
    Route::post('/brand/update', [BrandController::class, 'update'])->name('brand.update');
    Route::get('/brand/destroy/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
    Route::get('/brand/publish/{id}', [BrandController::class, 'publish'])->name('brand.publish');
    Route::get('/brand/unpublish/{id}', [BrandController::class, 'unpublish'])->name('brand.unpublish');

    /*
    |--------------------------------------------------------------------------
    | Brand / Hersteller filters
    |--------------------------------------------------------------------------
    */
    Route::get('/brand/contractor', [BrandController::class, 'contractor'])->name('brand.contractor');
    Route::get('/brand/sub-contractor', [BrandController::class, 'sub_contractor'])->name('brand.sub.contractor');
    Route::get('/brand/architect', [BrandController::class, 'architect'])->name('brand.architect');
    Route::get('/brand/bank', [BrandController::class, 'bank'])->name('brand.bank');
    Route::get('/brand/insurance', [BrandController::class, 'insurance'])->name('brand.insurance');
    Route::get('/brand/other', [BrandController::class, 'other'])->name('brand.other');

    /*
    |--------------------------------------------------------------------------
    | Brand Department / Kontakte
    |--------------------------------------------------------------------------
    */
    Route::get('/brand/{brandId}/departments', [BrandDepartmentController::class, 'index'])->name('brand.department.index');
    Route::post('/brand/departments/store', [BrandDepartmentController::class, 'store'])->name('brand.department.store');
    Route::post('/brand/departments/{id}/update', [BrandDepartmentController::class, 'update'])->name('brand.department.update');
    Route::get('/brand/departments/{id}/delete', [BrandDepartmentController::class, 'destroy'])->name('brand.department.delete');
});
Route::group(['middleware' => 'web'], function () {
    Route::get('/external_personal', [ExternalPersonalController::class, 'index'])->name('external.info');
    Route::get('/external_destroy/{id}', [ExternalPersonalController::class, 'destroy'])->name('external.destroy');
    Route::post('/external_save', [ExternalPersonalController::class, 'store'])->name('external.store');
    Route::post('/external_update', [ExternalPersonalController::class, 'update'])->name('external.update');
    Route::get('/external_publish/{id}', [ExternalPersonalController::class, 'publish'])->name('external.publish');
    Route::get('/external_unpublish/{id}', [ExternalPersonalController::class, 'unpublish'])->name('external.unpublish');
    Route::get('/external_department/{id}', [ExternalDepartmentsController::class, 'index'])->name('external.department');
    Route::post('/external_department_save', [ExternalDepartmentsController::class, 'store'])->name('external.department.save');
    Route::get('/external_department_delete/{id}', [ExternalDepartmentsController::class, 'destroy'])->name('external.department.delete');
    Route::post('/external_department_update/{id}', [ExternalDepartmentsController::class, 'update'])->name('external.department.update');
});

Route::middleware(['web','auth'])->group(function () {
    Route::prefix('distributors')->name('distributors.')->group(function () {
        Route::get('/', [DistributorController::class, 'index'])->name('index');
        Route::post('/', [DistributorController::class, 'store'])->name('store');
        Route::put('/{distributor}', [DistributorController::class, 'update'])->name('update');
        Route::delete('/{distributor}', [DistributorController::class, 'destroy'])->name('destroy');

        Route::post('/{distributor}/publish', [DistributorController::class, 'publish'])->name('publish');
        Route::post('/{distributor}/unpublish', [DistributorController::class, 'unpublish'])->name('unpublish');
        Route::post('/import-csv', [DistributorController::class, 'importCsv'])->name('importCsv');

        // NEW: distributor products modal
        Route::get('/{distributor}/products', [DistributorController::class, 'products'])
            ->name('products');

        // NEW: price difference from inside distributor product modal
        Route::get('/{distributor}/products/{product}/price-difference', [DistributorController::class, 'productPriceDifference'])
            ->name('products.price-difference');

        Route::prefix('{distributor}/departments')->name('departments.')->group(function () {
            Route::get('/', [DistributorDepartmentController::class, 'index'])->name('index');
            Route::post('/', [DistributorDepartmentController::class, 'store'])->name('store');
            Route::put('/{department}', [DistributorDepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [DistributorDepartmentController::class, 'destroy'])->name('destroy');
        });
    });

    Route::get('/distributor_department/{id}', [DistributorDepartmentController::class, 'index'])->name('distributor_department.legacy');
    Route::get('/distributor_destroy/{id}', [DistributorController::class, 'destroyLegacy']);
    Route::get('/distributor_publish/{id}', [DistributorController::class, 'publishLegacy']);
    Route::get('/distributor_unpublish/{id}', [DistributorController::class, 'unpublishLegacy']);
});
// Product Inventory
Route::prefix('inventory')->middleware('auth')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/analytics', [InventoryController::class, 'analytics'])->name('inventory.analytics');
    Route::get('/list-ajax', [InventoryController::class, 'listAjax'])->name('inventory.list.ajax');
    Route::post('/store-ajax', [InventoryController::class, 'storeAjax'])->name('inventory.store.ajax');
    Route::post('/update-ajax/{id}', [InventoryController::class, 'updateAjax'])->name('inventory.update.ajax');
    Route::delete('/delete-ajax/{id}', [InventoryController::class, 'destroyAjax'])->name('inventory.delete.ajax');
    Route::get('/product-data/{product_id}', [InventoryController::class, 'fetchProductData'])->name('inventory.product.data');
    Route::get('/product-data/{product_id}', [InventoryController::class, 'fetchProductData'])->name('inventory.product.data');
    Route::get('/history-ajax', [InventoryController::class, 'historyAjax'])->name('inventory.history.ajax');
    Route::post('/use-product-ajax/{id}', [InventoryController::class, 'useProductAjax'])->name('inventory.use.ajax');
    Route::get('/find-by-product/{product}', [InventoryController::class, 'findByProductAjax'])->name('find.by.product');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/ajax/inventory/store', [InventoryController::class, 'store'])->name('ajax.inventory.store');
});
// Assets Inventory
Route::middleware(['auth'])
->prefix('admin')
->group(function () {
    Route::get('lager/vermoegensbestand', [AssetController::class, 'index'])->name('handover.details.asset');
    Route::get('lager/vermoegensbestand/assets/fetch', [AssetController::class, 'assetsFetch'])->name('handover.assets.fetch');
    Route::post('lager/vermoegensbestand/assets', [AssetController::class, 'assetsStore'])->name('handover.assets.store');
    Route::put('lager/vermoegensbestand/assets/{asset}', [AssetController::class, 'assetsUpdate'])->name('handover.assets.update');
    Route::delete('lager/vermoegensbestand/assets/{asset}', [AssetController::class, 'assetsDestroy'])->name('handover.assets.destroy');
    Route::get('lager/vermoegensbestand/handovers/fetch', [AssetController::class, 'handoversFetch'])->name('handover.handovers.fetch');
    Route::post('lager/vermoegensbestand/handovers', [AssetController::class, 'handoversStore'])->name('handover.handovers.store');
    Route::put('lager/vermoegensbestand/handovers/{handover}', [AssetController::class, 'handoversUpdate'])->name('handover.handovers.update');
    Route::delete('lager/vermoegensbestand/handovers/{handover}', [AssetController::class, 'handoversDestroy'])->name('handover.handovers.destroy');
    Route::get('handover/assets/available', [AssetController::class, 'assetsAvailable'])->name('handover.assets.available');
});


Route::middleware(['auth'])->group(function () {
    // One-page AJAX machine inventory
    Route::get('/machine_view', [MachineController::class, 'index'])->name('machine.inventory');
    Route::get('/machine_ajax', [MachineController::class, 'data'])->name('machine.inventory.data');
    Route::get('/machine_analytics', [MachineController::class, 'analytics'])->name('machine.inventory.analytics');
    Route::get('/machine_show/{machine}', [MachineController::class, 'show'])->name('machine.inventory.show');
    Route::post('/machine_store', [MachineController::class, 'store'])->name('machine.inventory.store');
    Route::post('/machine_update/{machine?}', [MachineController::class, 'update'])->name('machine.inventory.update');
    Route::delete('/machine_destroy/{machine}', [MachineController::class, 'destroy'])->name('machine.inventory.destroy');

    // Legacy links kept working
    Route::get('/machine_create', [MachineController::class, 'index'])->name('machine.inventory.create');
    Route::get('/machine_edit/{id}', fn($id) => redirect()->route('machine.inventory', ['machine_id' => $id]))->name('machine.inventory.edit');
    Route::get('/machine_destroy/{id}', [MachineController::class, 'destroyLegacy'])->name('machine.inventory.destroy.legacy');

    // AJAX machine service endpoints
    Route::get('/machine_service_ajax/{machine}', [MachineServiceController::class, 'data'])->name('machine.service.data');
    Route::get('/machine_service_show/{service}', [MachineServiceController::class, 'show'])->name('machine.service.show');
    Route::post('/machine_service_store', [MachineServiceController::class, 'store'])->name('machine.service.store');
    Route::post('/machine_service_update/{service?}', [MachineServiceController::class, 'update'])->name('machine.service.update');
    Route::delete('/machine_service_destroy/{service}', [MachineServiceController::class, 'destroy'])->name('machine.service.destroy');

    // Legacy service links kept working, but they open the new one-page UI.
    Route::get('/machine_service_details/{machine_id}', [MachineServiceController::class, 'index'])->name('machine.service');
    Route::get('/machine_service_create/{machine_id}', [MachineServiceController::class, 'create'])->name('machine.service.create');
    Route::get('/machine_service_edit/{id}', fn($id) => redirect()->route('machine.inventory'))->name('machine.service.edit');
    Route::get('/machine_service_destroy/{id}', [MachineServiceController::class, 'destroyLegacy'])->name('machine.service.destroy.legacy');

    // AJAX installment plan endpoints inside the same machine page
    Route::get('/machine_installment_ajax/{machine}', [MachineInstallmentController::class, 'index'])->name('machine.installment.data');
    Route::get('/machine_installment_show/{installment}', [MachineInstallmentController::class, 'show'])->name('machine.installment.show');
    Route::post('/machine_installment_store', [MachineInstallmentController::class, 'store'])->name('machine.installment.store');
    Route::post('/machine_installment_update/{installment?}', [MachineInstallmentController::class, 'update'])->name('machine.installment.update');
    Route::delete('/machine_installment_destroy/{installment}', [MachineInstallmentController::class, 'destroy'])->name('machine.installment.destroy');
    Route::post('/machine_installment_contract/{installment}', [MachineInstallmentController::class, 'uploadContract'])->name('machine.installment.contract');

    // AJAX installment payment endpoints
    Route::get('/machine_installment_payment_ajax/{installment}', [MachineInstallmentController::class, 'payments'])->name('machine.installment.payment.data');
    Route::post('/machine_installment_payment_store', [MachineInstallmentController::class, 'storePayment'])->name('machine.installment.payment.store');
    Route::post('/machine_installment_payment_update/{payment?}', [MachineInstallmentController::class, 'updatePayment'])->name('machine.installment.payment.update');
    Route::delete('/machine_installment_payment_destroy/{payment}', [MachineInstallmentController::class, 'destroyPayment'])->name('machine.installment.payment.destroy');

    // Legacy installment links now open the one-page UI and the user can manage installments in the drawer.
    Route::get('/asset_installment/{machine}/machine/{branch?}', fn($machine, $branch = null) => redirect()->route('machine.inventory', ['machine_id' => $machine]))->name('machine.installment.legacy.create');
    Route::get('/installment_payment/{installment}', fn($installment) => redirect()->route('machine.inventory'))->name('machine.installment.legacy.payment');
});

Route::middleware(['auth'])
->group(function () {
    //Assets Installments
    Route::get('asset_installment_show', [AssetInstallmentController::class, 'show'])->name('assets.installment.show');
    Route::get('asset_installment/{asset}/{type}/{branch}', [AssetInstallmentController::class, 'index'])->name('assets.installment');
    Route::post('asset_installment_save', [AssetInstallmentController::class, 'store'])->name('assets.installment.store');
    Route::get('asset_installment_append/{id}', [AssetInstallmentController::class, 'append'])->name('assets.installment.append');
    Route::get('asset_installment_edit/{id}/{asset}/{type}', [AssetInstallmentController::class, 'edit'])->name('assets.installment.edit');
    Route::post('asset_installment_update', [AssetInstallmentController::class, 'update'])->name('assets.installment.update');
    Route::get('asset_installment_destroy/{id}', [AssetInstallmentController::class, 'destroy'])->name('assets.installment.destroy');
    Route::post('asset_installment_pdf', [AssetInstallmentController::class, 'save_pdf'])->name('assets.installment.pdf');
    //Instalment Payment Payment
    Route::get('installment_payment/{id}', [InstallmentPaymentController::class, 'create'])->name('installment.pay');
    Route::post('installment_payment_save', [InstallmentPaymentController::class, 'store'])->name('installment.store');
    //QR generators
    Route::post('/qrcode_generator', [App\Http\Controllers\QrCodeController::class, 'store'])->name('qr.code');
    Route::get('/qrcode_details', [App\Http\Controllers\QrCodeController::class, 'index'])->name('qr.details');
    Route::get('/qr_print', [App\Http\Controllers\QrCodeController::class, 'print'])->name('qr.print');
    Route::get('/qr_destroy/{id}', [App\Http\Controllers\QrCodeController::class, 'destroy'])->name('qr.destroy');
});

//Delivery Note Crud
Route::middleware(['auth'])->prefix('admin/delivery-notes')->name('delivery-notes.')->group(function () {
    Route::get('/', [DeliveryNoteController::class, 'index'])->name('index');
    Route::get('/list', [DeliveryNoteController::class, 'list'])->name('list');
    Route::get('/analytics', [DeliveryNoteController::class, 'analytics'])->name('analytics');
    Route::post('/', [DeliveryNoteController::class, 'store'])->name('store');
    Route::get('/customers/search', [DeliveryNoteController::class, 'searchCustomers'])->name('customers.search');
    Route::get('/customers/{customer}/related-data', [DeliveryNoteController::class, 'customerRelatedData'])->name('customers.related-data');
    Route::get('/deals/find', [DeliveryNoteController::class, 'findDeal'])->name('deals.find');
    Route::get('/{deliveryNote}/profile', [DeliveryNoteController::class, 'profile'])->name('profile');
    Route::get('/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('show');
    Route::post('/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('update');
    Route::delete('/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('destroy');
    Route::post('/{deliveryNote}/progress', [DeliveryNoteController::class, 'updateProgress'])->name('progress');
    Route::post('/{deliveryNote}/pdf', [DeliveryNoteController::class, 'uploadPdf'])->name('pdf');
    Route::post('/{deliveryNote}/toggle-status', [DeliveryNoteController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{deliveryNote}/linked', [DeliveryNoteController::class, 'linked'])->name('linked');
    Route::get('/{deliveryNote}/images', [DeliveryNoteImageController::class, 'index'])->name('images.index');
    Route::post('/{deliveryNote}/images', [DeliveryNoteImageController::class, 'store'])->name('images.store');
    Route::post('/images/{image}', [DeliveryNoteImageController::class, 'update'])->name('images.update');
    Route::delete('/images/{image}', [DeliveryNoteImageController::class, 'destroy'])->name('images.destroy');
   
});
Route::middleware(['auth'])
->group(function () {
    Route::get('/delivery-notes/deal/{deal}/create', [DeliveryNoteController::class, 'createFromDeal'])->name('delivery-notes.create-from-deal');
    Route::get('/delivery-notes/deal/{deal}', [DeliveryNoteController::class, 'byDeal'])->name('delivery-notes.by-deal');
});

//Handover Asset
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/handover', [App\Http\Controllers\HandoverController::class, 'handover'])->name('handover.item');
    Route::post('/handover_store', [App\Http\Controllers\HandoverController::class, 'store'])->name('handover.store');
    Route::get('/handover_next/{id}', [App\Http\Controllers\HandoverController::class, 'next'])->name('handover.next');
    Route::get('/handover_details', [App\Http\Controllers\HandoverController::class, 'index'])->name('handover.details');
    Route::get('/handover_multiple', [App\Http\Controllers\HandoverController::class, 'multiple'])->name('handover.multiple');
    Route::get('/handover_destroy/{id}', [App\Http\Controllers\HandoverController::class, 'destroy'])->name('handover.delete');
    Route::get('/handover_print/{id}', [App\Http\Controllers\HandoverController::class, 'print'])->name('handover.print');
    Route::post('/handover_update', [App\Http\Controllers\HandoverController::class, 'update'])->name('handover.update');
    Route::post('/handover_to_save', [App\Http\Controllers\HandoverToController::class, 'store'])->name('handover.to.store');
    //Request Out CRUD
    Route::get('/request_out_create', [InventoryRequestOutController::class, 'index'])->name('request.out.create');
    Route::get('/request_out_details', [InventoryRequestOutController::class, 'index'])->name('request.out.details');
    Route::get('/request_out/products', [InventoryRequestOutController::class, 'products'])->name('request.out.products');
    Route::get('/request_out/requests', [InventoryRequestOutController::class, 'requests'])->name('request.out.requests');
    Route::get('/request_out/analytics', [InventoryRequestOutController::class, 'analytics'])->name('request.out.analytics');
    Route::post('/request_out_save', [InventoryRequestOutController::class, 'store'])->name('request.out.store');
    Route::post('/request_out_update', [InventoryRequestOutController::class, 'update'])->name('request.out.update');
    Route::delete('/request_out_delete/{id}', [InventoryRequestOutController::class, 'destroy'])->name('request.out.delete');
});
Route::group(['middleware' => ['web', 'auth']], function () {
    //Purchase Request CRUD
    Route::get('/purchase_request', [PurchaseRequestController::class, 'index'])->name('purchase.request');
    Route::get('/purchase_request_create', [PurchaseRequestController::class, 'index'])->name('purchase.request.create');
    Route::get('/purchase_request/list', [PurchaseRequestController::class, 'list'])->name('purchase.request.list');
    Route::get('/purchase_request/analytics', [PurchaseRequestController::class, 'analytics'])->name('purchase.request.analytics');
    Route::get('/purchase_request_show/{id}', [PurchaseRequestController::class, 'show'])->name('purchase.request.show');
    Route::post('/purchase_request_save', [PurchaseRequestController::class, 'store'])->name('purchase.request.save');
    Route::delete('/purchase_request_delete/{id}', [PurchaseRequestController::class, 'destroy'])->name('purchase.request.delete');
});
// Department  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/department_view', [DepartmentController::class, 'index'])->name('department.info');
    Route::delete('/department_destroy/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
    Route::post('/department_create', [DepartmentController::class, 'store'])->name('department.create');
    Route::post('/department_update', [DepartmentController::class, 'update'])->name('department.update');
    Route::get('/department_publish/{id}', [DepartmentController::class, 'publish'])->name('department.publish');
    Route::get('/department_unpublish/{id}', [DepartmentController::class, 'unpublish'])->name('department.unpublish');
    Route::get('/department_organization', [DepartmentController::class, 'organize'])->name('department.organize');
    Route::post('/department/description', [DepartmentController::class, 'description_update'])->name('department.description.update');
    Route::post('/department/position/junction', [DepartmentController::class, 'assignPosition'])->name('assign.department.position');
    Route::get('/department/get/employee', [DepartmentController::class, 'getEmployee'])->name('orgnaization.get.employee');
    Route::post('/department/store/employee/head', [DepartmentController::class, 'storeHead'])->name('orgnaization.store.head.employee');
    Route::get('/department/employee/head/get/{id}', [DepartmentController::class, 'getHead']);
    Route::get('/department/employees/{id}', [DepartmentController::class, 'Employees']);
    Route::post('/department/employees/change/', [DepartmentController::class, 'employeeChange']);
    Route::post('/departments/update-order', [DepartmentController::class, 'updateOrder'])->name('departments.update.order');
    Route::get('department/leader/{department_id}', [DepartmentController::class, 'delete_leader'])->name('department.delete.leader');
    Route::post('/orgnaization/store/representative', [DepartmentController::class, 'storeRepresentative'])->name('orgnaization.store.representative.employee'); 
    Route::get('/department/employee/representative/get/{id}', [DepartmentController::class, 'getRepresentative']); 
    Route::get('/department/delete/representative/{id}', [DepartmentController::class, 'delete_representative'])->name('department.delete.representative');    
    Route::get('/departments/{department}/employees', [DepartmentController::class, 'employeesWithPositions'])->name('departments.employees'); 
    Route::post('/department/positions/update', [DepartmentController::class, 'departmentUpdate'])->name('department.positions.update');
    Route::get('/department/positions/employee/{employee}',[DepartmentController::class, 'employeeAllocations'])->name('department.positions.employee'); 
    Route::post('/department/positions/employee/{employee}/update',[DepartmentController::class, 'updateEmployeeAllocations'])->name('department.positions.employee.update');
    Route::get('/departments/filter-by-branch', [DepartmentController::class, 'filterByBranch'])->name('departments.filterByBranch');
    Route::post('/department/positions/assign', [DepartmentController::class, 'assignPosition'])->name('department.positions.assign');
    Route::post('/department/positions/unassign', [DepartmentController::class, 'unassignPosition'])->name('department.positions.unassign'); 
    Route::get('/department/positions/remaining', [DepartmentController::class, 'remainingPercent'])->name('department.positions.remaining');
    Route::get('chart/branches', [BranchController::class, 'chartIndex'])->name('branches.chart'); 
    Route::post('branches', [BranchController::class, 'branchStore'])->name('branches.store'); 
    Route::delete('delete/branches/{branch}', [BranchController::class, 'branchDestroy'])->name('branches.destroy');
    Route::get('/department/profile/{id}', [DepartmentController::class, 'profile'])->name('department.profile');
    Route::get('get_department_calendar/{department_id}', [DepartmentController::class, 'calendar'])->name('department.calendar');
    Route::get('department/profile/{id}/json', [DepartmentController::class, 'profileJson'])->name('department.profile.json');
    Route::get('/departments', [DepartmentChartController::class, 'index']);
    Route::post('/departments', [DepartmentChartController::class, 'store']);
    Route::delete('/departments/{id}', [DepartmentChartController::class, 'destroy']);
    Route::get('chart/branches', [DepartmentChartController::class, 'getBranches']);
    Route::delete('delete/branches/{id}', [DepartmentChartController::class, 'destroy_branch']); 
    Route::post('/update/node', [DepartmentChartController::class, 'update_node'])->name('department.update.node');
    Route::get('/get/department/ticket/{departmentId}', [DepartmentController::class, 'departmentTickets']); 
    Route::get('/department/{id}/tasks/json', [DepartmentController::class, 'tasksApi']);
    Route::get('/department/{id}/expense/json', [DepartmentController::class, 'expensesApi']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/lead/kanban/customers/search', [LeadOverviewController::class, 'kanbanCustomerSearch'])
        ->name('kanban.customers.search');

    Route::get('/lead/kanban/branch-addresses', [LeadOverviewController::class, 'kanbanBranchAddresses'])
        ->name('kanban.branch-addresses.index');

    Route::get('/lead/kanban/filter-settings', [LeadOverviewController::class, 'kanbanFilterSettingsIndex'])
        ->name('kanban.filter-settings.index');

    Route::post('/lead/kanban/filter-settings', [LeadOverviewController::class, 'kanbanFilterSettingsStore'])
        ->name('kanban.filter-settings.store');

    Route::post('/lead/kanban/filter-settings/{setting}', [LeadOverviewController::class, 'kanbanFilterSettingsUpdate'])
        ->name('kanban.filter-settings.update');

    Route::post('/lead/kanban/filter-settings/{setting}/default', [LeadOverviewController::class, 'kanbanFilterSettingsMakeDefault'])
        ->name('kanban.filter-settings.default');

    Route::delete('/lead/kanban/filter-settings/{setting}', [LeadOverviewController::class, 'kanbanFilterSettingsDestroy'])
        ->name('kanban.filter-settings.destroy');

    Route::get('/kanban-stage-workflow/config', [LeadOverviewController::class, 'stageWorkflowConfig'])
        ->name('kanban-stage-workflow.config');

    Route::post('/kanban-stage-workflow/move/{leadProduct}', [LeadOverviewController::class, 'moveStageWorkflow'])
        ->name('kanban-stage-workflow.move');

    Route::post('/kanban-stage-workflow/move-next/{leadProduct}', [LeadOverviewController::class, 'moveToNextProductStage'])
        ->name('kanban-stage-workflow.move-next');
});

// Qualification  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::post('/emp_qualification', [QualificationController::class, 'emp_qualification'])->name('emp.qualification');
    Route::post('/emp_qualification_update', [QualificationController::class, 'update'])->name('emp.qualification.update');
    Route::delete('/qualification_delete/{id}', [QualificationController::class, 'destroy'])->name('emp.qualification.delete');

});
// Further Education  CRUD
Route::group(['middleware' => 'web'], function () {
    Route::post('/f_education', [FurtherEducationController::class, 'store'])->name('f.education.store');
    Route::post('/f_education_update', [FurtherEducationController::class, 'update'])->name('f.education.update');
    Route::delete('f_education_delete/{id}', [FurtherEducationController::class, 'destroy'])->name('f_education.delete');
});
// Language Type CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/language', [LanguagesController::class, 'index'])->name('language.info');
    Route::get('/language_destroy/{id}', [LanguagesController::class, 'destroy'])->name('language.destroy');
    Route::post('/language_save', [LanguagesController::class, 'store'])->name('language.store');
    Route::post('/language_update', [LanguagesController::class, 'update'])->name('language.update');
    Route::post('/ajax_save', [LanguagesController::class, 'save'])->name('save.language');
    Route::get('/language_view', [LanguagesController::class, 'view'])->name('load.languages');
});
// Country Type CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('/country', [CountryController::class, 'index'])->name('country.info');
    Route::get('/country_destroy/{id}', [CountryController::class, 'destroy'])->name('country.destroy');
    Route::post('/country_save', [CountryController::class, 'store'])->name('country.store');
    Route::post('/country_update', [CountryController::class, 'update'])->name('country.update');
});
//Tax CRUD
Route::group(['middleware' => 'web', 'is_Admin'], function () {
    Route::get('/tax', [TaxController::class, 'index'])->name('tax.info');
    Route::get('/tax_destroy/{id}', [TaxController::class, 'destroy'])->name('tax.destroy');
    Route::post('/tax_save', [TaxController::class, 'store'])->name('tax.store');
    Route::post('/tax_update', [TaxController::class, 'update'])->name('tax.update');
});

//Discount Group CRUD
Route::group(['middleware' => 'web', 'is_Admin'], function () {
    Route::get('/discount_group', [DiscountGroupController::class, 'index'])->name('discount_group.info');
    Route::get('/discount_group_destroy/{id}', [DiscountGroupController::class, 'destroy'])->name('discount_group.destroy');
    Route::post('/discount_group_save', [DiscountGroupController::class, 'store'])->name('discount_group.store');
    Route::post('/discount_group_update', [DiscountGroupController::class, 'update'])->name('discount_group.update');
});

//Article Group CRUD
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/article_group', [ArticleGroupController::class, 'index'])->name('article_group.index');
    Route::post('/article_group', [ArticleGroupController::class, 'store'])->name('article_group.store');
    Route::post('/article_group/update', [ArticleGroupController::class, 'update'])->name('article_group.update');
    Route::delete('/article_group/{id}', [ArticleGroupController::class, 'destroy'])->name('article_group.destroy');
    Route::post('/sub_article_group', [ArticleGroupController::class, 'storeSubArticleGroup'])->name('sub_article_group.store');
    Route::post('/sub_article_group/update', [ArticleGroupController::class, 'updateSubArticleGroup'])->name('sub_article_group.update');
    Route::delete('/sub_article_group/{id}', [ArticleGroupController::class, 'destroySubArticleGroup'])->name('sub_article_group.destroy');
});

//Formual chechelist
Route::group(['middleware' => 'web', 'is_Admin'], function () {
    Route::get('/product-formula', [ProductFormulaController::class, 'index'])->name('product.formula.index');  
    Route::get('/admin/formula/create/{id}', [ProductFormulaController::class, 'create'])->name('admin.formula.create');
    Route::post('/product-formula/store', [ProductFormulaController::class, 'store'])->name('product.formula.store');
    Route::get('/product-formula/{id}/{product_id}/edit', [ProductFormulaController::class, 'edit'])->name('product.formula.edit');
    Route::get('/product-formula/show/{product_id}', [ProductFormulaController::class, 'show'])->name('product.formula.show');
    Route::post('/product-formula/update/{id}', [ProductFormulaController::class, 'update'])->name('product.formula.updates');
    Route::delete('/product-formula/delete/{id}', [ProductFormulaController::class, 'destroy'])->name('product.formula.destroy');
    Route::get('edit/product-formula/{id}/{product_id}', [ProductFormulaController::class, 'editFormula'])->name('edit.product.formula');
    Route::post('update/product-formula', [ProductFormulaController::class, 'updateFormula'])->name('product.formula.update');
    Route::post('/product-formula/save', [ProductFormulaController::class, 'save'])->name('product-formula.save');
    Route::get('/product-formula/{id}/test', [ProductFormulaController::class, 'test'])->name('product.formula.test');
    Route::post('/product-formula/test-submit', [ProductFormulaController::class, 'testSubmit'])->name('product.formula.test.submit');
    Route::get('/product-formula/checklist/{product_id}', [ProductFormulaController::class, 'loadChecklist']);
    Route::post('/lead-product-checklist/init', [LeadProductChecklistValueController::class, 'initChecklistRender']);
    Route::post('/lead-product-checklist/save', [LeadProductChecklistValueController::class, 'saveChecklist'])->name('lead-product-checklist.save');
}); 
 


Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/new-leads/{customer}/alternatives/{alternative}/products/{product}/invoices-panel',[NewLeadsController::class, 'panel'])->name('new_leads.invoices.panel');
});

 

//Task Phase CRUD

Route::group(['middleware' => ['web', 'auth']], function () {

    /*
    |--------------------------------------------------------------------------
    | Task Phase Manager / Product-Leistung Overview
    |--------------------------------------------------------------------------
    */

    Route::get('/task_phase', [TaskPhaseController::class, 'index'])
        ->name('task_phase.index');

    Route::get('/task-phase', [TaskPhaseController::class, 'index'])
        ->name('task.phase.index');

    Route::get('/task_phase_details/{product}', [TaskPhaseController::class, 'details'])
        ->name('task.phase.details');

    Route::get('/task_phase_order/{id}', [TaskPhaseController::class, 'order'])
        ->name('task.phase.order');

    Route::post('/task_phase_order/update', [TaskPhaseController::class, 'updateOrder'])
        ->name('task.phase.updateOrder');

    Route::get('/task_phase_create', [TaskPhaseController::class, 'create'])
        ->name('task.phase.create');

    Route::get('/task_phase_destroy/{id}', [TaskPhaseController::class, 'destroy'])
        ->name('task.phase.destroy');

    Route::post('/task_phase_clone', [TaskPhaseController::class, 'clone'])
        ->name('task.phase.clone');

    Route::post('/task_phase_store', [TaskPhaseController::class, 'store'])
        ->name('task.phase.store');

    Route::post('/task_phase_store_new', [TaskPhaseController::class, 'storeNewPhase'])
        ->name('task.phase.store.new');

    Route::post('/task_phase_update', [TaskPhaseController::class, 'update'])
        ->name('task.phase.update');

    Route::post('/task-phases/{id}/update', [TaskPhaseController::class, 'update'])
        ->name('task.phases.update.ajax');

    Route::post('/phase/{id}/toggle-status', [TaskPhaseController::class, 'toggleStatus'])
        ->name('task.phases.toggle-status');


    /*
    |--------------------------------------------------------------------------
    | Article Groups / Product Create
    |--------------------------------------------------------------------------
    */

    Route::post('/article-groups/store', [ArticleGroupController::class, 'save'])
        ->name('article-groups.save');


    /*
    |--------------------------------------------------------------------------
    | Phase Sections / Leistungen
    |--------------------------------------------------------------------------
    */

    Route::post('/task_section_create', [PhaseSectionController::class, 'store'])
        ->name('phase.section.store');

    Route::post('/task_section_update', [PhaseSectionController::class, 'update'])
        ->name('phase.section.update');

    Route::get('/task_section_delete/{id}', [PhaseSectionController::class, 'destroy'])
        ->name('phase.section.delete');

    Route::get('/task_section_restore/{id}', [PhaseSectionController::class, 'restore'])
        ->name('phase.section.restore');

    Route::get('/phase-sections/by-product/{product}', [PhaseSectionController::class, 'byProduct'])
        ->name('phase-sections.by-product');

    Route::post('/phase-sections/{section}/ajax-update', [PhaseSectionController::class, 'ajaxUpdate'])
        ->name('phase-sections.ajax-update');

    Route::delete('/phase-sections/{section}/ajax-delete', [PhaseSectionController::class, 'ajaxDelete'])
        ->name('phase-sections.ajax-delete');

    Route::delete('/phase-sections/duplicates/{product}', [PhaseSectionController::class, 'cleanupDuplicates'])
        ->name('phase-sections.duplicates.cleanup');

    Route::get('/task-phase/manager/phase-sections/{product}', [TaskPhaseController::class, 'managerSectionsByProduct'])
        ->name('task.phase.manager.sections');

    Route::post('/task-phase/manager/phase-sections/{product}/cleanup-duplicates', [TaskPhaseController::class, 'cleanupDuplicatePhaseSections'])
        ->name('task.phase.manager.sections.cleanup-duplicates');


    /*
    |--------------------------------------------------------------------------
    | New LeadStage AJAX Task/Phase Management Page
    |--------------------------------------------------------------------------
    */

    Route::get('/phase_management/{product}/{section_id}', [LeadTaskPhaseManagementController::class, 'manage'])
        ->name('phase.management');

    Route::prefix('task-phase/ajax')
        ->name('task.phase.ajax.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Board
            |--------------------------------------------------------------------------
            */

            Route::get('/board/{product}/{section}', [LeadTaskPhaseManagementController::class, 'board'])
                ->name('board');

            /*
            |--------------------------------------------------------------------------
            | LeadStage Helpers
            |--------------------------------------------------------------------------
            */

            Route::get('/lead-stages', [LeadTaskPhaseManagementController::class, 'leadStages'])
                ->name('lead-stages');

            Route::get('/lead-stages/{leadStage}/sub-stages', [LeadTaskPhaseManagementController::class, 'leadSubStages'])
                ->name('lead-sub-stages');

            /*
            |--------------------------------------------------------------------------
            | Tasks / Phases
            |--------------------------------------------------------------------------
            */

            Route::post('/tasks', [LeadTaskPhaseManagementController::class, 'storeTask'])
                ->name('tasks.store');

            Route::post('/tasks/reorder', [LeadTaskPhaseManagementController::class, 'reorderTasks'])
                ->name('tasks.reorder');

            Route::post('/tasks/move', [LeadTaskPhaseManagementController::class, 'moveTask'])
                ->name('tasks.move');

            Route::get('/tasks/{task}', [LeadTaskPhaseManagementController::class, 'showTask'])
                ->name('tasks.show');

            Route::post('/tasks/{task}/update', [LeadTaskPhaseManagementController::class, 'updateTask'])
                ->name('tasks.update');

            Route::delete('/tasks/{task}', [LeadTaskPhaseManagementController::class, 'deleteTask'])
                ->name('tasks.delete');

            Route::post('/tasks/{task}/clone', [LeadTaskPhaseManagementController::class, 'cloneTask'])
                ->name('tasks.clone');

            Route::post('/tasks/{task}/move', [LeadTaskPhaseManagementController::class, 'moveTask'])
                ->name('tasks.move.single');

            /*
            |--------------------------------------------------------------------------
            | Activities
            |--------------------------------------------------------------------------
            */

            Route::post('/activities', [LeadTaskPhaseManagementController::class, 'storeActivity'])
                ->name('activities.store');

            Route::post('/activities/reorder', [LeadTaskPhaseManagementController::class, 'reorderActivities'])
                ->name('activities.reorder');

            Route::post('/activities/move', [LeadTaskPhaseManagementController::class, 'moveActivity'])
                ->name('activities.move');

            Route::get('/activities/{activity}', [LeadTaskPhaseManagementController::class, 'showActivity'])
                ->name('activities.show');

            Route::post('/activities/{activity}/update', [LeadTaskPhaseManagementController::class, 'updateActivity'])
                ->name('activities.update');

            Route::delete('/activities/{activity}', [LeadTaskPhaseManagementController::class, 'deleteActivity'])
                ->name('activities.delete');

            Route::post('/activities/{activity}/clone', [LeadTaskPhaseManagementController::class, 'cloneActivity'])
                ->name('activities.clone');

            Route::post('/activities/{activity}/move', [LeadTaskPhaseManagementController::class, 'moveActivity'])
                ->name('activities.move.single');
        });


    Route::middleware('auth')
        ->prefix('task-phase/ajax/stage-admin')
        ->name('task.phase.ajax.stage-admin.')
        ->group(function () {
            Route::get('/stages', [LeadStageAdminController::class, 'index'])
                ->name('stages.index');

            Route::post('/stages', [LeadStageAdminController::class, 'storeStage'])
                ->name('stages.store');

            Route::post('/stages/reorder', [LeadStageAdminController::class, 'reorderStages'])
                ->name('stages.reorder');

            Route::get('/stages/{leadStage}', [LeadStageAdminController::class, 'showStage'])
                ->name('stages.show');

            Route::match(['post', 'put', 'patch'], '/stages/{leadStage}', [LeadStageAdminController::class, 'updateStage'])
                ->name('stages.update');

            Route::match(['post', 'put', 'patch'], '/stages/{leadStage}/update', [LeadStageAdminController::class, 'updateStage'])
                ->name('stages.update.legacy');

            Route::delete('/stages/{leadStage}', [LeadStageAdminController::class, 'deleteStage'])
                ->name('stages.delete');

            Route::post('/stages/{leadStage}/sub-stages', [LeadStageAdminController::class, 'storeSubStage'])
                ->name('sub-stages.store');

            Route::post('/stages/{leadStage}/sub-stages/reorder', [LeadStageAdminController::class, 'reorderSubStages'])
                ->name('sub-stages.reorder');

            Route::get('/sub-stages/{subStage}', [LeadStageAdminController::class, 'showSubStage'])
                ->name('sub-stages.show');

            Route::match(['post', 'put', 'patch'], '/sub-stages/{subStage}', [LeadStageAdminController::class, 'updateSubStage'])
                ->name('sub-stages.update');

            Route::match(['post', 'put', 'patch'], '/sub-stages/{subStage}/update', [LeadStageAdminController::class, 'updateSubStage'])
                ->name('sub-stages.update.legacy');

            Route::delete('/sub-stages/{subStage}', [LeadStageAdminController::class, 'deleteSubStage'])
                ->name('sub-stages.delete');
        });
    
    /*
    |--------------------------------------------------------------------------
    | Old Helper Routes Kept For Existing Screens
    |--------------------------------------------------------------------------
    */

    Route::get('/api/get-task-phases-activities', [TaskPhaseController::class, 'getPhasesWithActivities'])
        ->name('api.task-phases.activities');

    Route::get('/department/phase/position/{department_id}', [TaskPhaseController::class, 'getPosition'])
        ->name('task.phase.get.position');

    Route::get('/phase-sections/{product_id}', [TaskPhaseController::class, 'getSectionsByProduct'])
        ->name('phases.sections');

    Route::get('/task-phases/{section_id}', [TaskPhaseController::class, 'getPhasesBySection'])
        ->name('phases.tasks');

    Route::get('/search-phases', [TaskPhaseController::class, 'searchPhases'])
        ->name('task.phases.search');

    Route::post('/create-phase', [TaskPhaseController::class, 'createNewPhase'])
        ->name('task.phases.createFromCopy');

    Route::post('/task-phase/transfer', [TaskPhaseController::class, 'transferPhase'])
        ->name('task.phase.transfer');

    Route::post('/task-activity/transfer', [TaskPhaseController::class, 'transferActivity'])
        ->name('task.activity.transfer');

    Route::get('/get-phases-by-version', [TaskPhaseController::class, 'getPhasesByVersion'])
        ->name('task.phases.byVersion');

    Route::get('/get-stages-by-version', [TaskPhaseController::class, 'getStagesByVersion'])
        ->name('task.stages.byVersion');

    Route::get('/get-stage-versions', [TaskPhaseController::class, 'getStageVersions'])
        ->name('task.stage.versions');

    Route::get('/get/stage/version', [TaskPhaseController::class, 'getStageVersion'])
        ->name('task.stage.byVersion');

    Route::get('/get-activities-by-stage', [TaskPhaseController::class, 'getActivitiesByStage'])
        ->name('task.activities.byStage');

    Route::get('/get-stage-details/{id}', [TaskPhaseController::class, 'getStageDetails'])
        ->name('task.stage.details');

    Route::get('/get-phases-by-stage', [TaskPhaseController::class, 'getPhasesByStage'])
        ->name('task.phases.byStage');

    Route::get('/get/phase/stage', [TaskPhaseController::class, 'getPhaseStage'])
        ->name('task.phase.stage');
});

 

Route::middleware('auth')->prefix('admin/stages')->name('stages.')->group(function () {
    Route::get('/', [StageController::class, 'index'])->name('index');
    // UI data
    Route::get('fetch', [StageController::class, 'fetch'])->name('fetch');
    Route::get('sections', [StageController::class, 'getSections'])->name('get_sections');
    Route::get('get-versions', [StageController::class, 'getVersions'])->name('get_versions');
    Route::get('section-stats', [StageController::class, 'sectionStats'])->name('section_stats');
    Route::post('store', [StageController::class, 'store'])->name('store');
    Route::get('edit/{id}', [StageController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [StageController::class, 'update'])->name('update');
    Route::delete('destroy/{id}', [StageController::class, 'destroy'])->name('destroy');
    // Sorting
    Route::post('reorder', [StageController::class, 'reorder'])->name('reorder');
    // Copy helpers
    Route::post('duplicate-section', [StageController::class, 'duplicateSection'])->name('duplicate_section');
    Route::post('bulk-transfer-multi-targets', [StageController::class, 'bulkTransferMultiTargets'])->name('bulk_transfer_multi_targets');
    Route::post('copy-bucket', [StageController::class, 'copyBucket'])->name('copy_bucket');
});


Route::prefix('copy')->group(function () {
    // Load the phase and its activities for the copy modal
    Route::get('/load/{phaseId}', [PhaseCopyController::class, 'loadPhaseCopyData'])->name('copy.load');
    // Get target phases based on selected product and section
    Route::get('/get-phases/{productId}/{sectionId}', [PhaseCopyController::class, 'getPhasesByProductAndSection'])->name('copy.getPhases');
    // Perform the actual copy
    Route::post('/do', [PhaseCopyController::class, 'copyPhaseAndActivities'])->name('copy.do');
});


//Task Phase Activieis CRUD
Route::group(['middleware' => 'web'], function () {
    Route::get('get/phase/all/activity/{id}', [PhaseActivitiesController::class, 'allActivity'])->name('get.all.activities');
    Route::get('get/phase/activity/{id}', [PhaseActivitiesController::class, 'index'])->name('activities');
    Route::post('/phase/activity/status/{id}', [PhaseActivitiesController::class, 'status'])->name('activities.status');
    Route::post('/phase-activities', [PhaseActivitiesController::class, 'store'])->name('activities.store');
    Route::post('/phase-activities-new', [PhaseActivitiesController::class, 'storeNewActivity'])->name('activities.store.new');
    Route::post('/phase-activities/{id}/update', [PhaseActivitiesController::class, 'update'])->name('activities.update');
    Route::get('/activities_details/{id}/{product}', [PhaseActivitiesController::class, 'ajax'])->name('activities.details');
    Route::get('/activities_create', [PhaseActivitiesController::class, 'create'])->name('activities.create');
    Route::get('/activities_destroy/{id}', [PhaseActivitiesController::class, 'destroy'])->name('activities.destroy');
    Route::post('phase/activity/order', [PhaseActivitiesController::class, 'orderTask'])->name('phase.task.activity.order');
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/sub_task/{task_id}/{phase_id}/{product}', [TaskSubTaskController::class, 'index'])->name('sub.tasks.view'); 
    Route::post('/sub_task_save', [TaskSubTaskController::class, 'store'])->name('sub.tasks.store'); 
    Route::post('/sub_task_update', [TaskSubTaskController::class, 'update'])->name('sub.tasks.update'); 
    Route::get('/sub_task_delete/{id}', [TaskSubTaskController::class, 'destroy'])->name('sub.tasks.delete'); 
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/email_view', [LeadsController::class, 'index'])->name('email.view');
    Route::get('/email_refresh', [LeadsController::class, 'fetchAndDisplayEmails'])->name('email.refresh');
    Route::get('/email_configuration', [EmailConfigurationController::class, 'index'])->name('email.configuration');
    Route::post('/email_configuration_save', [EmailConfigurationController::class, 'store'])->name('email.configuration.save');
    Route::post('/email_configuration_update', [EmailConfigurationController::class, 'update'])->name('email.configuration.update');
    Route::get('/email_configuration_destroy/{id}', [EmailConfigurationController::class, 'destroy'])->name('email.configuration.destroy');
    Route::get('/email_config_publish/{id}', [EmailConfigurationController::class, 'publish'])->name('email.configuration.publish');
    Route::get('/email_config_unpublish/{id}', [EmailConfigurationController::class, 'unpublish'])->name('email.configuration.unpublish');
    Route::get('/email_config_test/{id}', [EmailConfigurationController::class, 'test'])->name('email.configuration.test');
    Route::get('/customer_email_add/{id}/{name}/{email}', [LeadsController::class, 'customer'])->name('customer.add.email');
    Route::post('/lead_send_email_view', [EmailConfigurationController::class, 'send'])->name('lead.email.send');
});

//Creating Angebot | Offer
// FIX P0-04: interner Angebots-Wizard inkl. Kalkulation/Netto-Preise hinter auth.
// Kein oeffentlicher (tokenisierter) Kunden-Angebotslink vorhanden -> ganze Gruppe auth.
Route::middleware('auth')->prefix('offers')->name('offers.')->group(function () {
    Route::get('/wizard', [OfferWizardController::class, 'index'])
        ->name('wizard');
    Route::get('/wizard-smart', [OfferWizardController::class, 'smart'])
        ->name('wizard.smart');
    Route::get('/wizard/customers', [OfferWizardController::class, 'searchCustomers'])
        ->name('wizard.customers');
    Route::get('/wizard/customers/{lead}', [OfferWizardController::class, 'customerShow'])
        ->name('wizard.customer.show');
    Route::get('/wizard/customers/{lead}/objects', [OfferWizardController::class, 'customerObjects'])
        ->name('wizard.customer.objects');
    Route::post('/wizard/create', [OfferWizardController::class, 'createOffer'])
        ->name('wizard.create');
    Route::get('/wizard/group-sets', [OfferWizardController::class, 'groupSetsCatalog'])
        ->name('wizard.group-sets');
    Route::get('/wizard/products-list', [OfferWizardController::class, 'productsList'])
        ->name('wizard.products-list');
    Route::get('/wizard/products', [OfferWizardController::class, 'searchProducts'])
        ->name('wizard.products');
    Route::get('/wizard/templates', [OfferTemplateController::class, 'wizardSearch'])
        ->name('wizard.templates');
    Route::get('/wizard/templates/{template}', [OfferTemplateController::class, 'wizardShow'])
        ->name('wizard.templates.show');
    Route::get('/master-set-groups/{group}', [OfferWizardController::class, 'groupSetShow'])
        ->name('wizard.master-set-groups.show');
    Route::get('/products/{product}', [OfferWizardController::class, 'productShow'])
        ->name('wizard.products.show');
    Route::get('/master-sets/{masterSet}', [OfferWizardController::class, 'showJson'])
        ->name('wizard.master-sets.show');
    Route::get('/{offer}', [OfferController::class, 'show'])
        ->whereNumber('offer')
        ->name('show');
});


Route::middleware(['auth'])
    ->prefix('offers/roof-layout')
    ->name('offers.roof-layout.')
    ->group(function () {
        Route::get('/', [OfferRoofLayoutConfigurationController::class, 'show'])->name('show');
        Route::post('/', [OfferRoofLayoutConfigurationController::class, 'store'])->name('store');
        Route::post('/image', [OfferRoofLayoutConfigurationController::class, 'uploadImage'])->name('image');
    });

Route::middleware(['auth'])
    ->prefix('admin/offers/folders/{folder}/supplier')
    ->name('admin.offers.folders.supplier.')
    ->group(function () {
        Route::get('/connections', [OfferSupplierSearchController::class, 'connections'])
            ->name('connections');

        Route::get('/{supplierConnection}/forward', [OfferSupplierSearchController::class, 'forward'])
            ->name('forward');

        Route::match(['GET', 'POST'], '/{supplierConnection}/return', [OfferSupplierSearchController::class, 'handleReturn'])
            ->name('return');

        Route::get('/{supplierConnection}/logs/{log}/review', [OfferSupplierSearchController::class, 'reviewReturn'])
            ->name('logs.review');

        Route::post('/{supplierConnection}/logs/{log}/import-to-offer', [OfferSupplierSearchController::class, 'importReviewedToOffer'])
            ->name('logs.import-to-offer');
    });
Route::middleware(['auth'])
    ->get('/admin/offer-template-supplier', [OfferTemplateSupplierController::class, 'connections'])
    ->name('admin.offer-template-supplier');

Route::middleware(['auth'])
    ->prefix('admin/offer-template-supplier')
    ->name('admin.offer-template-supplier.')
    ->group(function () {
        Route::get('/connections', [OfferTemplateSupplierController::class, 'connections'])
            ->name('connections');

        Route::get('/{supplierConnection}/forward', [OfferTemplateSupplierController::class, 'forward'])
            ->name('forward');

        Route::match(['GET', 'POST'], '/{supplierConnection}/return', [OfferTemplateSupplierController::class, 'handleReturn'])
            ->name('return');

        Route::get('/{supplierConnection}/logs/{log}/review', [OfferTemplateSupplierController::class, 'reviewReturn'])
            ->name('logs.review');

        Route::post('/{supplierConnection}/logs/{log}/import-to-template', [OfferTemplateSupplierController::class, 'importReviewedToTemplate'])
            ->name('logs.import-to-template');
    });

Route::middleware(['auth'])->group(function () {

    Route::get('/offer-templates', [OfferTemplatePickerController::class, 'index'])
        ->name('offer-templates.index');

    Route::get('/offer-templates/search/customers', [OfferTemplatePickerController::class, 'searchCustomers'])
        ->name('offer-templates.search.customers');

    Route::get('/offer-templates/search/objects', [OfferTemplatePickerController::class, 'searchObjects'])
        ->name('offer-templates.search.objects');

    Route::get('/offer-templates/search/products', [OfferTemplatePickerController::class, 'searchProducts'])
        ->name('offer-templates.search.products');

    Route::get('/offer-templates/{template}/check', [OfferTemplatePickerController::class, 'check'])
        ->name('offer-templates.check');
    Route::post('/offer-templates/{template}/use', [OfferTemplatePickerController::class, 'useTemplate'])
        ->name('offer-templates.use');
    Route::get('/offer-templates/search/article-groups', [OfferTemplatePickerController::class, 'searchArticleGroups'])
        ->name('offer-templates.search.article-groups');
    Route::get('/offer-templates/search/suggestions', [OfferTemplatePickerController::class, 'searchSuggestions'])
        ->name('offer-templates.search.suggestions');
    Route::get('/offer-templates/search/employees', [OfferTemplatePickerController::class, 'searchEmployees'])
        ->name('offer-templates.search.employees');

});

Route::prefix('clipboard')->middleware('auth')->group(function () {
    Route::get('/', [ClipboardController::class, 'index']);
    Route::post('/copy', [ClipboardController::class, 'copy']);
    Route::delete('/clear', [ClipboardController::class, 'clear']);
});

Route::post('customer_offer_save', [OffersController::class, 'store'])->name('offer.save');
Route::prefix('admin/offers')
    ->name('admin.offers.')
    ->middleware('auth')
    ->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Offer main routes
    |--------------------------------------------------------------------------
    */
    Route::get('/', [OfferController::class, 'index'])->name('index');
    Route::get('/data', [OfferController::class, 'data'])->name('data');

    Route::get('/search-customer-objects', [OfferController::class, 'searchCustomerObjects'])
        ->name('search-customer-objects');

    Route::get('/get-products', [OfferController::class, 'getProducts'])
        ->name('get-products');

    Route::post('/', [OfferController::class, 'store'])->name('store');
    Route::put('/{offer}', [OfferController::class, 'update'])->name('update');
    Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('destroy');
    Route::patch('/{offer}/status', [OfferController::class, 'updateStatus'])->name('status');
    Route::get('/employees/search', [OfferController::class, 'employeeSearch'])->name('employees.search');
    Route::get('/{offer}/team', [OfferController::class, 'team'])
        ->whereNumber('offer')
        ->name('team');
    Route::post('/{offer}/team', [OfferController::class, 'syncTeam'])
            ->whereNumber('offer')
            ->name('team.sync');

    Route::get('/kanban-stages', [OfferKanbanStageController::class, 'index'])
        ->name('kanban-stages.index');

    Route::post('/kanban-stages', [OfferKanbanStageController::class, 'store'])
        ->name('kanban-stages.store');

    Route::patch('/kanban-stages/{stage}', [OfferKanbanStageController::class, 'update'])
        ->whereNumber('stage')
        ->name('kanban-stages.update');

    Route::delete('/kanban-stages/{stage}', [OfferKanbanStageController::class, 'destroy'])
        ->whereNumber('stage')
        ->name('kanban-stages.destroy');

    Route::post('/kanban-stages/reorder', [OfferKanbanStageController::class, 'reorder'])
        ->name('kanban-stages.reorder');

    /*
    |--------------------------------------------------------------------------
    | Offer folders
    |--------------------------------------------------------------------------
    */
    Route::post('/{offer}/folders', [OfferController::class, 'storeFolder'])->name('folders.store'); 
    Route::get('/folders/{folder}', [OfferFolderController::class, 'show'])->name('folders.show'); 
    Route::get('/folders/{folder}/data', [OfferFolderController::class, 'data'])->name('folders.data'); 
    Route::post('/folders/{folder}/labor-qualification-options', [OfferFolderController::class, 'laborQualificationOptions'])->name('folders.labor-qualification-options');
    Route::put('/folders/{folder}', [OfferController::class, 'updateFolder'])->name('folders.update'); 
    Route::delete('/folders/{folder}', [OfferController::class, 'destroyFolder'])->name('folders.destroy'); 
    Route::patch('/folders/{folder}/kanban/move', [OfferFolderController::class, 'moveKanbanItem'])->name('folders.kanban.move'); 
    Route::post('folders/{folder}/material-order-status', [OfferFolderController::class, 'changeMaterialOrderStatus'])->name('folders.material-order-status');
    Route::post('/folders/{folder}/material-final-status',[OfferFolderController::class, 'confirmMaterialFinalStatus'])->name('folders.material-final-status');
    Route::patch('/folders/{folder}/document-status',[OfferFolderController::class, 'changeDocumentStatus'])->name('folders.document-status');
    Route::post('/folders/{folder}/clone', [OfferFolderController::class, 'clone']);   

    /*
    |--------------------------------------------------------------------------
    | Folder AGB
    |--------------------------------------------------------------------------
    */
    Route::post('/folders/{folder}/agb', [OfferFolderController::class, 'saveAgb'])
        ->name('folders.agb.save');

    /*
    |--------------------------------------------------------------------------
    | Folder material actions
    |--------------------------------------------------------------------------
    */
    Route::post('/folders/{folder}/material-comparison', [OfferFolderController::class, 'materialComparison'])
        ->name('folders.material-comparison');

    Route::post('/folders/{folder}/material-change', [OfferFolderController::class, 'changeMaterialDistributor'])
        ->name('folders.material-change');
    Route::post('/folders/{folder}/material-change', [OfferFolderController::class, 'changeMaterialDistributor'])->name('folders.material-change');

    /*
    |--------------------------------------------------------------------------
    | Folder attachments
    |--------------------------------------------------------------------------
    */
    Route::post('/folders/{folder}/attachments/upload', [OfferFolderController::class, 'uploadAttachments'])
        ->name('folders.attachments.upload');

    Route::delete('/folders/{folder}/attachments/{attachment}', [OfferFolderController::class, 'deleteAttachment'])
        ->name('folders.attachments.delete');

    Route::post('/folders/{folder}/attachments/sort', [OfferFolderController::class, 'sortAttachments'])
        ->name('folders.attachments.sort');
    Route::get('/folders/{folder}/attachments', [OfferFolderController::class, 'getAttachments'])->name('folders.attachments.index');
});


Route::middleware(['auth'])
    ->prefix('admin/offers/folders/{folder}/page-library')
    ->name('admin.offers.folders.page-library.')
    ->group(function () {
        Route::get('/context', [OfferPageLibraryController::class, 'context'])->name('context');
        Route::get('/article-groups', [OfferPageLibraryController::class, 'articleGroups'])->name('article-groups');
        Route::get('/products', [OfferPageLibraryController::class, 'products'])->name('products');

        Route::post('/items', [OfferPageLibraryController::class, 'storeLibraryItem'])->name('items.store');
        Route::patch('/items/{item}', [OfferPageLibraryController::class, 'updateLibraryItem'])->name('items.update');
        Route::post('/items/{item}/attach', [OfferPageLibraryController::class, 'attach'])->name('items.attach');

        Route::get('/pages', [OfferPageLibraryController::class, 'pages'])->name('pages.index');
        Route::post('/pages/reorder', [OfferPageLibraryController::class, 'reorder'])->name('pages.reorder');
        Route::patch('/pages/{page}', [OfferPageLibraryController::class, 'updatePage'])->name('pages.update');
        Route::delete('/pages/{page}', [OfferPageLibraryController::class, 'destroyPage'])->name('pages.destroy');
    });




Route::post('/offers/generate-pdf', [OfferController::class, 'generatePdf'])
    ->name('offers.generate-pdf')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::post('/offers/save-document', [OfferController::class, 'saveDocument'])->name('offers.save-document');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/deal/material-list/{offerDetail}', [DealMaterialListController::class, 'show'])->name('deal.material.list');
    Route::post('/deal/material-list/{offerDetail}/update', [DealMaterialListController::class, 'updateMaterialStatus'])->name('deal.material.update');
    Route::post('/deal/material-list/{offerDetail}/move-allocation', [DealMaterialListController::class, 'moveMaterialAllocation'])->name('deal.material.move-allocation');
    Route::post('/deal/material-list/{offerDetail}/apply-feinaufmass', [DealMaterialListController::class, 'applyFeinaufmassToOfferDetail'])->name('deal.material.apply-feinaufmass');
    Route::post('/deal/material-list/{offerDetail}/order-details',[DealMaterialListController::class, 'updateOrderDetails'])->name('deal.material.order-details');
});
Route::prefix('offers/document')->middleware('auth')->group(function () {
    Route::get('/load', [OfferDocumentController::class, 'load']);
    Route::get('/biography', [OfferDocumentController::class, 'biography']);
    Route::get('/presence/status', [OfferDocumentController::class, 'presenceStatus']);
    Route::post('/presence/ping', [OfferDocumentController::class, 'presencePing']);
    Route::post('/presence/leave', [OfferDocumentController::class, 'presenceLeave']);
    Route::get('/presence/users', [OfferDocumentController::class, 'onlineUsers']);
});
Route::patch('offers/{offer}/folders/{folder}/status', [OfferFolderController::class, 'updateStatus'])->name('admin.offers.folders.status');
Route::patch('/offers/{offer}/folders/{folder}/details', [OfferDetailsController::class, 'update'])->name('offer.details.update');
Route::get('/offers/{offer}/folders/{folder}/products', [OfferDetailsController::class, 'loadProducts'])->name('offer.details.products');
Route::get('/offers/{offer}/folders/{folder}/products', [OfferDetailsController::class, 'loadProducts']);
    Route::get('/master-sets/{id}/details', [OfferDetailsController::class, 'masterSetDetails'])->name('mastersets.details');
    // Load all employees saved for this offer+folder
    Route::get('/offers/{offer}/folders/{folder}/employees', [OfferDetailsController::class, 'loadEmployeeList'])->name('offer.employees.load');
    // Save employees for this offer+folder
    Route::patch('/offers/{offer}/folders/{folder}/employees', [OfferDetailsController::class, 'saveEmployeeList'])->name('offer.employees.save');
    // Optional: list all available positions for dropdown
    Route::get('/positions/list', [PositionController::class, 'loadPosition'])->name('positions.list');
    // Optional: create new position from Select2 tag
    Route::post('/positions', [PositionController::class, 'save'])->name('positions.save');
    Route::patch('/offers/{offer}/folders/{folder}/employees', [OfferDetailsController::class, 'updateEmployees'])->name('offer.employees.update');
    Route::get('/offers/{offer}/folders/{folder}/assets', [OfferDetailsController::class, 'loadAssets'])->name('offers.assets.index');
    Route::patch('/offers/{offer}/folders/{folder}/assets', [OfferDetailsController::class, 'updateAssets'])->name('offers.assets.update');
    Route::get('/assets/list', [AssetController::class, 'list'])->name('assets.list');
    Route::get('/offers/{offer}/folders/{folder}/products-list', [OfferDetailsController::class, 'loadProductList'])->name('offer.products.list');
    Route::patch('/offers/{offer}/folders/{folder}/products/{productList}', [OfferDetailsController::class, 'updateProduct'])->name('offer.products.update');
    Route::get('/products/gallery', [ProductController::class, 'getImage'])->name('products.gallery');
    Route::get('/ajax/product/info/{product}', [ProductController::class, 'ajaxInfo'])->name('ajax.product.info'); 
    Route::get('/products/images', function () {
        $images = \App\Models\ProductImage::with('product:id,product,model')
            ->get()
            ->map(function ($pi) {
                return [
                    'image'   => $pi->image,
                    'url'     => asset('images/products/' . $pi->image),
                    'product' => optional($pi->product)->product,
                    'model'   => optional($pi->product)->model,
                ];
            });

        return response()->json($images);
});
 
// Offer Settings 
Route::get('/user/preferences', [UserPreferenceController::class, 'load']);
Route::post('/user/preferences', [UserPreferenceController::class, 'save']);

Route::middleware('auth')->prefix('offers')->group(function () {
    Route::get('/templates', [OfferTemplateController::class, 'index'])->name('offers.templates.index');
    Route::get('/templates/options', [OfferTemplateController::class, 'getOptions'])->name('offers.templates.options');
    Route::get('/templates/{template}/load', [OfferTemplateController::class, 'load'])->name('offers.templates.load');
    Route::get('/templates/{template}', [OfferTemplateController::class, 'show'])->name('offers.templates.show');
    Route::delete('/templates/{template}', [OfferTemplateController::class, 'destroy'])->name('offers.templates.destroy');
    Route::patch('/templates/{template}/favorite', [OfferTemplateController::class, 'toggleFavorite'])->name('offers.templates.favorite');
    Route::patch('/templates/{template}/stamp', [OfferTemplateController::class, 'toggleStamp'])->name('offers.templates.stamp');
    Route::post('/templates/{template}/mark-used', [OfferTemplateController::class, 'markUsed'])->name('offers.templates.markUsed');
    Route::get('/templates/{template}/usage-history', [OfferTemplateController::class, 'usageHistory'])->name('offers.templates.usageHistory');
    Route::put('/templates/{template}', [OfferTemplateController::class, 'update'])->name('offers.templates.update');
});

Route::prefix('admin')->middleware(['auth'])->group(function () { 
    // list costing sets for dropdown
Route::get('costing-sets/options', [CostingSetController::class, 'options'])->name('admin.costing_sets.options'); 
Route::get('/costing-sets/{costingSet}', [CostingSetController::class, 'show']);
    // save selected costing set + modes onto master set
    Route::post('master-sets/{masterSet}/costing', [MasterSetController::class, 'saveCostingSettings'])->name('admin.master_sets.costing.save'); 
    // compute payload for modal/table
    Route::get('master-sets/{masterSet}/task-costing', [MasterSetController::class, 'taskCostingPayload'])->name('admin.master_sets.costing.payload');
    Route::post('/master-sets/{masterSet}/hydrate-components', [MasterSetController::class, 'hydrateComponents']);
    Route::post('/master-sets/groups/{articleGroup}/hydrate-components', [MasterSetController::class, 'hydrateGroupComponents']);
});


// JSON CRUD for Werkzeuge inside a master set 
Route::middleware('auth')->group(function () {
    // list + totals, and search for add-modal
    Route::get   ('/asset_sets/{masterId}',            [AssetSetController::class, 'index'])->name('asset-sets.index');
    Route::get   ('/asset_sets/{masterId}/search',     [AssetSetController::class, 'search'])->name('asset-sets.search');

    // create row for this master
    Route::post  ('/asset_sets/{masterId}',            [AssetSetController::class, 'store'])->name('asset-sets.store');

    // update / delete a specific row (rowId), sanity-checking it belongs to masterId
    Route::put   ('/asset_sets/{masterId}/{rowId}',    [AssetSetController::class, 'update'])->name('asset-sets.update');
    Route::delete('/asset_sets/{masterId}/{rowId}',    [AssetSetController::class, 'destroy'])->name('asset-sets.destroy');
});

Route::get('solar', function () {
    return view('admin.solar.configuration.configure');
});

Route::group(['middleware' => 'auth'], function () { 
    Route::get('/tools_view', [ToolsController::class, 'index'])->name('tools.view');
    Route::post('/tools_save', [ToolsController::class, 'store'])->name('tools.post'); 
});

// Getting Data from PVGIS APi

Route::group(['middleware' => 'auth'], function () {
    Route::post('/pvgis/fetch', [ToolsController::class, 'fetchPvgis'])->name('profitability.pvgis.fetch');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('weather/{id}', [PVToolsController::class, 'getPVData'])->name('tools.weather');
    Route::get('get_weather_date', [ToolsController::class, 'fetchWeatherData'])->name('weather.data');
    Route::get('get_weather/{id}', [ToolsController::class, 'weatherman'])->name('weather.man');
    Route::get('/admin/pvgis', [PVToolsController::class, 'index'])->name('admin.pvgis.index');
    Route::get('/admin/pvgis/fetch', [PVToolsController::class, 'fetchByPostcode'])->name('admin.pvgis.fetch');

});


 

Route::get('/test', function () {
    return view('admin.layouts.test');
})->name('appointments.test');

 // Personal Task Managment        

Route::group(['middleware'  =>  'auth'], function(){
    Route::get('/personal/task/{user}', [PersonalTaskController::class, 'index'])->name('personal.task.index');
    Route::get('/personal/task/search', [PersonalTaskController::class, 'index'])->name('personal.task.search'); 
    Route::get('/personal_task_details/{id}', [PersonalTaskController::class, 'details'])->name('personal.task.details'); 
    Route::post('/personal_task_store', [PersonalTaskController::class, 'store'])->name('personal.task.store'); 
    Route::post('/personal_task_update', [PersonalTaskController::class, 'update'])->name('personal.task.update');
    Route::get('/personal_task_delete/{id}', [PersonalTaskController::class, 'destroy'])->name('personal.task.destroy');
    Route::get('/personal_task_restore/{id}', [PersonalTaskController::class, 'restore'])->name('personal.task.restore');
    Route::delete('calendar/personal_task_delete/{id}', [PersonalTaskController::class, 'calendar_destroy'])->name('calendar.personal.task.destroy');
    Route::get('/personal_task_key_delete/{id}', [PersonalTaskController::class, 'delete_task'])->name('personal.task.key.destroy');
    Route::get('/personal_task_sub_delete/{id}', [PersonalTaskController::class, 'delete_sub_task'])->name('personal.task.sub.destroy');
    //Personal Task Attachment
    Route::post('/upload-files', [PersonalTaskAttachmentController::class, 'uploadFiles'])->name('upload.files');
    Route::post('/update-file-attachment', [PersonalTaskAttachmentController::class, 'update'])->name('update.files.name');
    Route::get('/personal_task_attachment/{task_id}', [PersonalTaskAttachmentController::class, 'index'])->name('personal.get.files');
    Route::delete('/personal_task_attachment/delete/{id}', [PersonalTaskAttachmentController::class, 'destroy'])->name('personal.delete.file');
  
    Route::post('/personal_task_accept', [PersonalTaskController::class, 'accept_request'])->name('personal.task.accept');
    Route::post('/personal_task_add_employee', [PersonalTaskController::class, 'add_employee'])->name('personal.task.add.employee');
    Route::post('personal_task_main_done', [PersonalTaskController::class, 'main_task'])->name('personal.task.done');
    Route::post('personal_task_main_undone', [PersonalTaskController::class, 'main_task_uncheck'])->name('personal.task.undo');
    Route::post('personal_task_status', [PersonalTaskController::class, 'project_status'])->name('personal.task.project.status');
    Route::get('search_duplicate_task/{emp_id}/{start_date}/{end_date}', [PersonalTaskController::class, 'availability'])->name('personal.task.check.availability');
    Route::get('/notifications/task/{task_id}', [PersonalTaskController::class, 'getTaskNotifications'])->name('notifications.task');
    Route::get('getEmployees', [EmployeeController::class, 'getEmployees'])->name('get.employees');
    Route::get('getAllEmployees', [EmployeeController::class, 'getAllEmployee'])->name('get.employees.all');
    Route::get('employee/list/status', [EmployeeController::class, 'EmployeeListStatus'])->name('get.employees.leave.status');
    Route::get('employee/list/status/inactive', [EmployeeController::class, 'EmployeeListStatusInactive'])->name('get.employees.leave.status.inactive');
    // Repeat functions 
    Route::post('personal_task_no_reminder', [PersonalTaskController::class, 'no_reminder'])->name('personal.task.project.no.reminder');
    Route::post('personal_task_no_repeat', [PersonalTaskController::class, 'no_repeat'])->name('personal.task.project.no.repeat');
    Route::get('task_repeat_lists', [PersonalTaskController::class, 'repeat_list'])->name('personal.task.repeat.list');
 
    Route::post('/tasks/process-repeats', [PersonalTaskController::class, 'processRepeatingTasks'])->name('tasks.process.repeats');
    Route::post('/tasks/stop-repeat', [PersonalTaskController::class, 'stopRepeatingTasks'])->name('tasks.stopRepeatingForAll');
    Route::post('/tasks/duration/update', [PersonalTaskController::class, 'taskDuration'])->name('tasks.duration.update');

    Route::post('/task/due/date/update', [PersonalTaskController::class, 'dueDateUpdate'])->name('due.date.update'); 
    Route::post('/get/info/{id}/{type}', [PersonalTaskController::class, 'getInfo'])->name('tasks.get.info');
    Route::get('personal_task_report/{task}', [PersonalTaskController::class, 'report'])->name('personal.task.report');
    //Personal Task Edit
    Route::get('/personal_task/{id}/edit', [PersonalTaskController::class, 'edit'])->name('personal.task.edit');
    Route::get('/personal_task_key_delete/{id}', [PersonalTaskController::class, 'delete_task'])->name('personal.task.key.destroy');
    Route::get('/personal_task_sub_delete/{id}', [PersonalTaskController::class, 'delete_sub_task'])->name('personal.task.sub.destroy');
    Route::get('personal_employees_personal_tasks/{id}', [PersonalTaskController::class, 'getEmployeeTask'])->name('personal.task.get.employee.task');
    Route::post('personal_task_new_employee', [PersonalTaskController::class, 'AddNewEmployee'])->name('personal.task.add.employee.details');
    Route::post('personal/task/customer/store', [PersonalTaskController::class, 'storeAjax'])->name('personal.task.customer.store');
    Route::delete('personal_task_delete_employee', [PersonalTaskController::class, 'deleteEmployee'])->name('personal.task.delete.employee.details');
    Route::post('personal_task_update_employee', [PersonalTaskController::class, 'updateEmployee'])->name('personal.task.update.employee.details');
    Route::post('/personal_task/update_status/{id}', [PersonalTaskController::class, 'updateStatus']);
    //Personal Task Comment
    Route::get('/personal_task_comment/{task_id}', [PersonalTaskCommentController::class, 'index'])->name('personal.task.comment.view');
    Route::post('/personal_task_comment_store', [PersonalTaskCommentController::class, 'store'])->name('personal.task.comment.store');
    Route::post('/personal_task_comment_reply', [PersonalTaskCommentController::class, 'reply'])->name('personal.task.comment.reply');

    // Customer Profile Comment Task 
    Route::post('/ajax/task_note/store', [PersonalTaskCommentController::class, 'storeComment'])->name('task_note.store');
    Route::get('/ajax/task_note/list/{task}', [PersonalTaskCommentController::class, 'list'])->name('task_note.list');
    Route::delete('/ajax/task_note/delete/{id}', [PersonalTaskCommentController::class, 'delete'])->name('task_note.delete');
    Route::post('/ajax/task_note/edit/{id}', [PersonalTaskCommentController::class, 'edit'])->name('task_note.edit');
    
    // Personal Task Calendar 
    Route::get('/tasks/calendar/personal', [PersonalTaskController::class, 'myCalender'])->name('personal.tasks.calendar'); 
    Route::get('/calendar/products/{product}/services', [PersonalTaskController::class, 'productServices'])->name('calendar.products.services');
    Route::get('/calendar/products/{product}/services/{service}/employees', [PersonalTaskController::class, 'serviceEmployees'])->name('calendar.products.service.employees');
    Route::post('/api/wizard-lead/store', [PersonalTaskController::class, 'leadStore'])->name('wizard.lead.store');
    Route::post('personal/task/change/appointment',[PersonalTaskController::class, 'change_appointment'])->name('personal.task.change.appointment');
    Route::get('/get_personal_task_calendar', [PersonalTaskController::class, 'calendar'])->name('personal.get.calendar');
    Route::get('/dashboard/calendar/fetch', [PersonalTaskController::class, 'fetchEvents'])->name('dashboard.personal.get.calendar');
    Route::get('/calendar/search/suggest', [PersonalTaskController::class, 'searchSuggest'])->name('calendar.search.suggest');
    Route::get('/get_personal_task_calendar_mini', [PersonalTaskController::class, 'MiniCalendar'])->name('personal.get.calendar.mini');
    Route::get('/get_my_personal_task_calendar', [PersonalTaskController::class, 'myCalender'])->name('personal.get.my.calendar');
    Route::get('/mobile/calendar', [PersonalTaskController::class, 'mobile'])->name('get.employee.calendar.mobile');
    Route::get('/get-appointments', [PersonalTaskController::class, 'getAppointments'])->name('appointments.filtered'); 
    Route::middleware(['auth'])->group(function () {
        // Picker APIs (Employees / Teams)
        Route::get('/picker/employees', [PersonaltaskController::class, 'pickerEmployees'])->name('picker.employees');
        Route::get('/picker/teams', [PersonaltaskController::class, 'pickerTeams'])->name('picker.teams');
        Route::get('/picker/teams/{team}', [PersonaltaskController::class, 'pickerTeam'])->name('picker.teams.show');
    });
    //Contact List: 
    Route::get('/get/contact/list', [PersonalTaskController::class, 'contactList'])->name('get.contact.list');
    Route::get('/ajax/lead-product-list', [PersonalTaskController::class, 'ajaxList'])->name('lead.product.list.ajax');
    Route::get('/api/products-by-customer', [PersonalTaskController::class, 'getProductsByCustomer'])->name('get.products.by.customer');
    Route::post('appointments/store/mobile', [MainAppointmentController::class, 'mobileStore'])->name('appointments.store.mobile');
    Route::get('/get-appointments-monthly', [PersonalTaskController::class, 'getMonthlyAppointments']);


    Route::middleware(['auth'])
        ->prefix('admin/todo/personal')
        ->name('personal-tasks.')
        ->group(function () {
            Route::get('/', [PersonalTaskBoardController::class, 'index'])->name('index');

            Route::get('/ajax/tasks', [PersonalTaskBoardController::class, 'ajaxTasks'])->name('ajax.tasks');
            Route::get('/ajax/stats', [PersonalTaskBoardController::class, 'ajaxStats'])->name('ajax.stats');

            Route::get('/customers/search', [PersonalTaskBoardController::class, 'searchCustomers'])
                ->name('customers.search');

            // IMPORTANT: you were missing this store route
            Route::post('/personal_task_store', [PersonalTaskBoardController::class, 'store'])
                ->name('personal.task.store');

            Route::get('/personal_task/{id}/edit', [PersonalTaskBoardController::class, 'edit'])
                ->name('personal.task.edit');

            Route::post('/personal_task_update', [PersonalTaskBoardController::class, 'update'])
                ->name('personal.task.update');

            // IMPORTANT: keep these inside this group
            Route::get('/lead-stage-context', [PersonalTaskBoardController::class, 'leadStageContext'])
                ->name('lead-stage-context');

            Route::post('/{task}/lead-stage', [PersonalTaskBoardController::class, 'updateLeadStage'])
                ->name('lead-stage.update');

            Route::post('{task}/status', [PersonalTaskBoardController::class, 'updateStatus'])->name('status');

            Route::post('{task}/accept', [PersonalTaskBoardController::class, 'accept'])->name('accept');
            Route::post('{task}/reject', [PersonalTaskBoardController::class, 'reject'])->name('reject');

            Route::post('{task}/pause', [PersonalTaskBoardController::class, 'pause'])->name('pause');
            Route::post('{task}/resume', [PersonalTaskBoardController::class, 'resume'])->name('resume');
            Route::post('{task}/cancel', [PersonalTaskBoardController::class, 'cancel'])->name('cancel');

            Route::post('{task}/color', [PersonalTaskBoardController::class, 'updateColor'])->name('color');
            Route::post('{task}/visibility', [PersonalTaskBoardController::class, 'updateVisibility'])->name('visibility');
            Route::post('{task}/archive', [PersonalTaskBoardController::class, 'archive'])->name('archive');

            Route::post('{task}/employees', [PersonalTaskBoardController::class, 'syncEmployees'])->name('employees.sync');
            Route::delete('{task}/employees/{employee}', [PersonalTaskBoardController::class, 'detachEmployee'])->name('employees.detach');

            Route::delete('/personal-tasks/{task}', [PersonalTaskBoardController::class, 'destroy'])->name('destroy');
            Route::post('/personal-tasks/{task}/restore', [PersonalTaskBoardController::class, 'restore'])->name('restore');
        });
 

    Route::middleware(['auth'])->group(function () {
        Route::get('/general-tasks', [GeneralTaskController::class, 'index'])->name('general-tasks.index');
        Route::post('/general-tasks', [GeneralTaskController::class, 'store'])->name('general-tasks.store');
        Route::put('/general-tasks/{generalTask}', [GeneralTaskController::class, 'update'])->name('general-tasks.update');
        Route::patch('/general-tasks/{generalTask}', [GeneralTaskController::class, 'update']);

        Route::post('/general-tasks/move', [GeneralTaskController::class, 'move'])->name('general-tasks.move');
        Route::post('/general-tasks/{generalTask}/claim', [GeneralTaskController::class, 'claim'])->name('general-tasks.claim');
        Route::post('/general-tasks/{generalTask}/archive', [GeneralTaskController::class, 'archive'])->name('general-tasks.archive');
        Route::post('/general-tasks/{id}/restore', [GeneralTaskController::class, 'restore'])->name('general-tasks.restore');

        Route::get('/general-tasks/{generalTask}/reports', [GeneralTaskController::class, 'reports'])->name('general-tasks.reports');
        Route::post('/general-tasks/{generalTask}/reports', [GeneralTaskController::class, 'storeReport'])->name('general-tasks.reports.store');

        Route::post('/general-tasks/{generalTask}/dependencies', [GeneralTaskController::class, 'storeDependency'])->name('general-tasks.dependencies.store');
        Route::delete('/general-tasks/{generalTask}/dependencies/{dependencyTask}', [GeneralTaskController::class, 'destroyDependency'])->name('general-tasks.dependencies.destroy');

        Route::post('/general-tasks/{generalTask}/steps/{step}/toggle', [GeneralTaskStepController::class, 'toggle'])->name('general-tasks.steps.toggle');
        Route::post('/general-tasks/reorder', [GeneralTaskController::class, 'reorder'])
            ->name('general-tasks.reorder');
        Route::delete('/general-tasks/{generalTask}', [GeneralTaskController::class, 'destroy'])
            ->name('general-tasks.destroy');
        Route::get('/general-tasks/{generalTask}/card', [GeneralTaskController::class, 'card'])
            ->name('general-tasks.card');
    });

    Route::middleware(['auth'])->group(function () {

        // Detail / profile page
        Route::get('/personal-tasks/{task}/profile', [PersonalTaskBoardController::class, 'show'])
            ->name('personal-tasks.profile');

        // Toggle a key: complete / undo
        Route::post('/personal-task-keys/{key}/toggle', [PersonalTaskBoardController::class, 'toggleKey'])
            ->name('personal-task-keys.toggle');

        // Comments (reports)
        Route::post('/personal-tasks/{task}/comments', [PersonalTaskBoardController::class, 'storeComment'])
            ->name('personal-tasks.comments.store');

        Route::post('/personal-tasks/comments/{comment}/reply', [PersonalTaskBoardController::class, 'storeReply'])
            ->name('personal-tasks.comments.reply');


        // TEAM: controllers (global for task)
        Route::post('/personal-tasks/{task}/team/controllers', [PersonalTaskBoardController::class, 'updateControllers'])
            ->name('personal-tasks.team.controllers');

        // TEAM: employees for whole task
        Route::post('/personal-tasks/{task}/team/employees', [PersonalTaskBoardController::class, 'addTaskEmployees'])
            ->name('personal-tasks.team.employees');

        // TEAM: employees for specific keys of this task
        Route::post('/personal-tasks/{task}/team/employees-keys', [PersonalTaskBoardController::class, 'addKeyEmployeesForTask'])
            ->name('personal-tasks.team.employees-keys');

        Route::post('/personal-tasks/{task}/attachments', [PersonalTaskBoardController::class, 'storeAttachment'])
            ->name('personal-tasks.attachments.store');

        Route::delete('/personal-tasks/attachments/{attachment}', [PersonalTaskBoardController::class, 'destroyAttachment'])
            ->name('personal-tasks.attachments.destroy');

            Route::get(  '/personal-tasks/news-feed/items', [PersonalTaskBoardController::class, 'items'])->name('personal-tasks.feed.items');
        Route::delete('/personal-tasks/{task}/team/controllers/{employee}', [PersonalTaskBoardController::class, 'removeController'])
            ->name('personal-tasks.team.controllers.destroy');

        Route::delete('/personal-tasks/{task}/team/keys/{key}/employees/{employee}', [PersonalTaskBoardController::class, 'removeKeyEmployeeFromTask'])
            ->name('personal-tasks.team.keys.employees.destroy');
 
    });

    Route::prefix('personal-tasks')->middleware(['auth'])->group(function () {
        Route::get('/{task}/steps',                  [PersonalTaskStepController::class, 'personalTaskStepsIndex'])->name('personal_task_steps.index');
        Route::post('/{task}/steps',                 [PersonalTaskStepController::class, 'personalTaskStepsStore'])->name('personal_task_steps.store');
        Route::put('/steps/{step}',                  [PersonalTaskStepController::class, 'personalTaskStepsUpdate'])->name('personal_task_steps.update');
        Route::delete('/steps/{step}',               [PersonalTaskStepController::class, 'personalTaskStepsDestroy'])->name('personal_task_steps.destroy');
        Route::post('/steps/{step}/employees/sync',  [PersonalTaskStepController::class, 'personalTaskStepsSyncEmployees'])->name('personal_task_steps.employees.sync');
    });


// Appointment CRUD 

    Route::group(['middleware' => 'web'], function () {
        Route::get('/appointments', [MainAppointmentController::class, 'index'])->name('main.appointment');
        Route::get('/appointments/search', [MainAppointmentController::class, 'index'])->name('main.appointment.search');
        Route::get('/calendar/datasets', [MainAppointmentController::class, 'datasets']);

 
        Route::post('/calendar/appointments/restore/{id}', [MainAppointmentController::class, 'restoreDeletedAppointment'])
            ->name('calendar.appointments.restore');

        Route::delete('/calendar/appointments/force-delete/{id}', [MainAppointmentController::class, 'forceDeleteAppointment'])
            ->name('calendar.appointments.force-delete');
    

        Route::get('/appointments/fetch', [MainAppointmentController::class, 'fetchAppointments']);
        Route::post('/appointments/store', [MainAppointmentController::class, 'store'])->name('main.appointments.store');
        Route::get('/appointments/edit/{id}', [MainAppointmentController::class, 'edit']);
        Route::get('/appointments/edit/calendar/{id}', [MainAppointmentController::class, 'editCalendar']);
        Route::post('/appointment/update', [MainAppointmentController::class, 'update'])->name('appointment.update');

        Route::post('/appointment/new/contact', [MainAppointmentController::class, 'newContact'])->name('new.contact'); 
        Route::get('/appointments/destroy/{id}', [MainAppointmentController::class, 'destroy'])->name('appointment.destroy');
        Route::delete('calendar/appointments/destroy/{id}', [MainAppointmentController::class, 'calendar_destroy'])->name('appointment.destroy.calendar');
        Route::get('/appointments/restore/{id}', [MainAppointmentController::class, 'restore'])->name('appointment.restore');
        Route::post('/appointment/add_employee', [MainAppointmentController::class, 'add_employee'])->name('appointment.add.employee');
        Route::post('appointment_status', [MainAppointmentController::class, 'status'])->name('appointment.status');
        Route::get('/appointment/{id}/edit', [MainAppointmentController::class, 'edit'])->name('appointment.edit');
        Route::get('appointment_details/{id}', [MainAppointmentController::class, 'details'])->name('appointment.details'); 
        Route::post('/appointment/accept', [MainAppointmentController::class, 'accept_request'])->name('appointment.accept');
        Route::get('/get/map/{id}', [MainAppointmentController::class, 'getMap'])->name('appointment.show.map');
        Route::post('/appointment/duplicate', [MainAppointmentController::class, 'duplicate'])->name('appointment.duplicate');

        Route::get('/main-appointments/{id}/fetch', [MainAppointmentController::class, 'fetch'])->name('main.appointments.fetch');
        Route::put('/main-appointments/{id}', [MainAppointmentController::class, 'updateAjax']);
        Route::get('/main-appointments/lead-stage-context', [MainAppointmentController::class, 'leadStageContext'])
            ->name('main-appointments.lead-stage-context');

        //Reporting CRUD
        Route::post('/appointments/toggle-report/{id}', [MainAppointmentController::class, 'toggleReport']);
        Route::post('/appointments/save-report/{id}', [MainAppointmentController::class, 'saveReport']);
        Route::get('/appointments/load-report/{id}', [MainAppointmentController::class, 'loadReport']);
        Route::delete('/appointments/delete-report/{id}', [MainAppointmentController::class, 'deleteReport']);
        //Appointment Comment

        // Route::get('/appointment_comment/{id}', [AppointmentCommentController::class, 'index'])->name('appointment.comment.view');
        // Route::post('/appointment_comment_store', [AppointmentCommentController::class, 'store'])->name('appointment.comment.store');
        // Route::post('/appointment_comment_reply', [AppointmentCommentController::class, 'reply'])->name('appointment.comment.reply');

        Route::get('/notifications/appointment/{appointment}', [MainAppointmentController::class, 'getAppointmentNotifications'])->name('appointment.task');

        //Appointment Attachment
        // Route::post('appointment/upload-files', [AppointmentAttachmentController::class, 'uploadFiles'])->name('appointment.upload.files');
        // Route::post('appointment/update-file-attachment', [AppointmentAttachmentController::class, 'update'])->name('appointment.update.files.name');
        // Route::get('appointment/attachment/{appointment_id}', [AppointmentAttachmentController::class, 'index'])->name('appointment.get.files');
        // Route::delete('appointment/attachment/delete/{id}', [AppointmentAttachmentController::class, 'destroy'])->name('appointment.delete.file');

            // Repeat functions 
        Route::post('appointment_no_reminder', [MainAppointmentController::class, 'no_reminder'])->name('appointment.no.reminder');
        Route::post('appointment_no_repeat', [MainAppointmentController::class, 'no_repeat'])->name('appointment.no.repeat');

        Route::get('/ticket/customer/search', [MainAppointmentController::class, 'customers'])->name('ticket.customer.search');
        Route::get('/ticket/problems/by-customer', [MainAppointmentController::class, 'problemsByCustomer'])->name('ticket.problems.by.customer');
        Route::get('/ticket/tasks/by-problem', [MainAppointmentController::class, 'tasksByProblem'])->name('ticket.tasks.by.problem');
    });

    Route::prefix('appointments/{appointment}')->group(function () {
        Route::get('reports', [AppointmentReportController::class, 'index'])->name('appointments.reports.index');
        Route::post('reports', [AppointmentReportController::class, 'store'])->name('appointments.reports.store');
        Route::put('reports/{report}', [AppointmentReportController::class, 'update'])->name('appointments.reports.update');
        Route::delete('reports/{report}', [AppointmentReportController::class, 'destroy'])->name('appointments.reports.destroy');
    });
    // reactions & comments
    Route::post('appointment-reports/{report}/react', [AppointmentReportController::class, 'react'])->name('appointmentReports.react');
    Route::post('appointment-reports/{report}/comments', [AppointmentReportController::class, 'comment'])->name('appointmentReports.comment');

});

Route::group(['middleware'  =>  'auth'], function(){
    Route::get('customer_profit/{customer_id}/{alternative_id}/{product}/{section_id}', [ProfitabilityCalculationController::class, 'index'])->name('customer.profit.view');
    Route::post('customer_profit_save', [ProfitabilityCalculationController::class, 'store'])->name('customer.profit.save');
    Route::post('customer_profit_edit', [ProfitabilityCalculationController::class, 'update'])->name('customer.profit.edit');
    Route::get('customer_profit_delete/{id}', [ProfitabilityCalculationController::class, 'destroy'])->name('customer.profit.delete');
    Route::get('customer_profit_report/{id}/{product_id}', [ProfitabilityCalculationController::class, 'showReport']);
    Route::get('get-profitability-data/{p_id}', [ProfitabilityCalculationController::class, 'getProfitabilityData']);
        Route::post('/lead-requests-solar', [ProfitabilityCalculationController::class, 'solarStore'])
    ->name('lead-requests.store.solar');  
    // 1. The List view (Shows table of all calculations)
    Route::get('/profitability/list/{customer_id}/{alternative_id}/{product_id}/{service_id?}', [ProfitabilityCalculationController::class, 'list'])
     ->name('profitability-calculations.list');

    // 2. Creating a new blank calculation
    Route::post('/profitability/store', [ProfitabilityCalculationController::class, 'store'])
        ->name('profitability-calculations.store');

    // 3. Editing a specific calculation (Loads your React/JS blade)
    Route::get('/profitability/edit/{id}', [ProfitabilityCalculationController::class, 'edit'])
        ->name('profitability-calculations.edit'); 

    Route::delete('/profitability/{id}', [ProfitabilityCalculationController::class, 'destroy'])
    ->name('profitability-calculations.destroy');

     Route::post( '/profitability-calculations/save-report', [ProfitabilityCalculationController::class, 'saveCalculationReport'])->name('profitability-calculations.save-report'); 
    Route::get('/profitability-calculations/{id}/report-data',[ProfitabilityCalculationController::class, 'getCalculationReport'])->name('profitability-calculations.report-data');

});


Route::group(['middleware'  =>  'auth'], function(){
    Route::post('/profitability/save', [ProfitabilityDataController::class, 'save'])->name('profitability.save'); 
    Route::post('/profitability/pvgis/save', [ProfitabilityDataController::class, 'savePvgisData'])->name('profitability.pvgis.save');

});
 
Route::get('/route-cache', function () {
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('view:cache');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    return 'Routes cache cleared and optimized!';
})->middleware('auth');
Auth::routes(['register' => false]);

Route::get('/timeline', [AdminController::class, 'timeline'])->name('timeline');
 
Route::post('/keep-session-alive', function () {
    Session::put('last_activity_time', time());
    return response()->json(['status' => 'session refreshed']);
});



// Feedback System 

Route::middleware(['auth'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('system.feedback.index');

    Route::get('/feedback/ajax/list', [FeedbackController::class, 'list'])->name('system.feedback.ajax.list');
    Route::post('/feedback/ajax/store', [FeedbackController::class, 'store'])->name('system.feedback.ajax.store');
    Route::post('/feedback/ajax/answer', [FeedbackController::class, 'update'])->name('system.feedback.ajax.answer');
    Route::post('/feedback/ajax/status/{id}', [FeedbackController::class, 'changeStatus'])->name('system.feedback.ajax.status');
    Route::post('/feedback/ajax/upload', [FeedbackController::class, 'upload'])->name('system.feedback.ajax.upload');
    Route::delete('/feedback/ajax/delete/{id}', [FeedbackController::class, 'destroy'])->name('system.feedback.ajax.delete');
});

// Inquiry Managment CRUD
Route::group(['middleware'  =>  'auth'], function(){
    Route::get('inquiry_view', [InquiryController::class, 'index'])->name('inquiry.view');
    Route::get('inquiry_customer', [InquiryController::class, 'customer'])->name('inquiry.customer');
    Route::get('inquiry_create', [InquiryController::class, 'create'])->name('inquiry.create');
    Route::post('inquiry_save', [InquiryController::class, 'store'])->name('inquiry.save'); 
    Route::get('inquiry_show/{id}', [InquiryController::class, 'show'])->name('inquiry.show.profile'); 
    Route::get('inquiry_edit/{id}', [InquiryController::class, 'edit'])->name('inquiry.edit');
    Route::post('inquiry_update', [InquiryController::class, 'update'])->name('inquiry.update');
    Route::get('/inquiry_delete/{id}', [InquiryController::class, 'destroy'])->name('inquiry.delete');
    Route::get('/inquiry_publish/{id}', [InquiryController::class, 'publish'])->name('inquiry.publish');
    Route::get('/inquiry_verify/{id}', [InquiryController::class, 'verify'])->name('inquiry.verify');
    Route::match(['get', 'post'], '/inquiry_junk/{id}', [InquiryController::class, 'junk'])->name('inquiry.junk');
    Route::match(['get', 'post'], '/inquiry_unjunk/{id}', [InquiryController::class, 'unjunk'])->name('inquiry.unjunk');
    Route::get('/inquiry_junklist', [InquiryController::class, 'junk_list'])->name('inquiry.junk.list');
    Route::get('/inquiry_deleted_list', [InquiryController::class, 'deleted_list'])->name('inquiry.deleted.list');
    Route::get('/inquiry_restore/{id}', [InquiryController::class, 'restore'])->name('inquiry.restore');
    Route::get('/my_inquiries', [InquiryController::class, 'my_inquiry'])->name('my.inquiry.view');
    Route::post('inquiry/department/employees', [InquiryController::class, 'getEmployee'])->name('inquiry.department.employees');
    Route::post('calender/department/employees', [InquiryController::class, 'departmentEmployees'])->name('calender.department.employees');
    Route::post( '/inquiry/{id}/status',  [InquiryController::class, 'updateStatus'])->name('inquiry.status');
    Route::post('/inquiry/product-list/store', [InquiryController::class, 'saveProduct'])->name('inquiry.products.save');
    Route::get('/inquiries/appointment-slots', [InquiryController::class, 'appointmentSlots'])->name('inquiries.appointment-slots');
    Route::get('/inquiries/calendar/availability', [InquiryController::class, 'availability'])->name('inquiries.calendar.availability');

    Route::delete('/inquiry/product-list/delete', [InquiryController::class, 'deleteProduct'])->name('inquiry.products.delete');
    Route::get('/inquiry/get/products/{id}', [InquiryController::class, 'getCustomerProduct'])->name('inquiry.get.products');
    Route::get('/inquiry/get/notification/{id}', [InquiryController::class, 'getNotification'])->name('inquiry.get.notification'); 
    Route::post('inquiries/bulk-verify', [InquiryController::class, 'bulkVerify'])
        ->name('inquiries.bulk.verify');

    Route::post('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete'])->name('inquiries.bulk.delete');
    Route::post('inquiries/bulk/junk', [InquiryController::class, 'bulkJunk'])->name('inquiries.bulk.junk');
    Route::get('/inquiries/calendar/availability',[InquiryController::class, 'availability'])->name('inquiries.calendar.availability');
    // Fetch Comments for an Inquiry
    Route::get('/inquiry/{inquiry_id}/comments', [InquiryCommentController::class, 'fetchComments'])->name('inquiry.comments.fetch'); 
    Route::post('/inquiry/{inquiry_id}/comments', [InquiryCommentController::class, 'postComment'])->name('inquiry.comments.store'); 
    Route::post('/comments/{comment_id}/like', [InquiryCommentController::class, 'likeComment'])->name('inquiry.comments.like'); 
    Route::post('/comments/{comment_id}/dislike', [InquiryCommentController::class, 'dislikeComment'])->name('inquiry.comments.dislike');
    Route::delete('/comments/{comment_id}/delete', [InquiryCommentController::class, 'deleteComment'])->name('comments.delete');
    Route::put('/comments/{comment_id}/edit', [InquiryCommentController::class, 'editComment'])->name('comments.edit');
    Route::post('/comments/{comment_id}/reply', [InquiryCommentController::class, 'replyComment'])->name('comments.reply');
    Route::post('/inquiry/{id}/verify', [InquiryController::class, 'verify'])->name('inquiry.verify1');

    Route::post('/admin/inquiries/ai-save', [InquiryController::class, 'storeFromAI']);
    Route::post('/inquiry-products/{id}/update-employee', [InquiryController::class, 'updateEmployee'])
    ->name('inquiry.update.employee');
    // api for modal
        Route::get('/api/appointments/customers', [CustomerMainAppointmentController::class, 'customers'])
        ->name('api.appointments.customers'); 
        Route::get('/api/appointments/customers/{lead}/alternatives', [CustomerMainAppointmentController::class, 'alternatives']); 
        Route::get('/api/appointments/customers/{lead}/products', [CustomerMainAppointmentController::class, 'leadProducts']);

        // store appointment from modal
        Route::post('/main-appointments/modal-store', [CustomerMainAppointmentController::class, 'storeFromModal'])
        ->name('main_appointments.store_modal');
        Route::post('/main-appointments/customer-modal', [CustomerMainAppointmentController::class, 'CustomerCalendarStore'])->name('main_appointments.customer-modal');
        
    Route::get('/ajax/calendar-events', [CustomerMainAppointmentController::class, 'getEvents'])->name('ajax.calendar.events');

     Route::get('/inquiries/new', [InquiryController::class, 'startDraft'])->name('inquiry.startDraft');

    Route::post('/inquiries/{inquiry}/autosave', [InquiryController::class,'autosave'])
        ->where('inquiry', '[1-9][0-9]*');

    Route::post('/inquiries/{inquiry}/autosave-products', [InquiryController::class,'autosaveProducts'])
        ->where('inquiry', '[1-9][0-9]*');
    Route::post('/inquiries/start-draft', [InquiryController::class, 'startDraft'])
    ->name('inquiries.start_draft');

    // Discard draft
    Route::delete('/inquiries/{inquiry}/discard', [InquiryController::class, 'discardDraft'])->name('inquiry.discardDraft');

    // Finalize draft (validate required fields, set is_draft=false, status=Unpublished)
    Route::post('/inquiries/{inquiry}/finalize', [InquiryController::class, 'finalizeDraft'])->name('inquiry.finalizeDraft');


    // Notification System 
    Route::get('/notifications/inquiry/{user}', [InquiryController::class, 'getTaskNotifications'])->name('notifications.inquiry');
    Route::post('/notifications/{id}/mark-read', [InquiryController::class, 'markAsRead']);
    Route::get('/inquiries/published', [InquiryController::class, 'published'])
    ->name('inquiry.published.list');

    // Re-verify a single Published inquiry (AJAX)
    Route::post('/inquiry/{id}/reverify', [InquiryController::class, 'reverify'])
        ->name('inquiry.reverify');

    Route::get('/inquiry/{inquiry}/reports', [InquiryReportController::class, 'index'])
        ->name('inquiry.reports.index');

    Route::post('/inquiry/{inquiry}/reports', [InquiryReportController::class, 'store'])
        ->name('inquiry.reports.store');

    Route::put('/inquiry/reports/{report}', [InquiryReportController::class, 'update'])
        ->name('inquiry.reports.update');

    Route::delete('/inquiry/reports/{report}', [InquiryReportController::class, 'destroy'])
        ->name('inquiry.reports.destroy');

});

Route::prefix('inquiries')->middleware(['auth'])->group(function () {
    Route::get('{inquiry}/verification/status',  [InquiryVerificationController::class, 'status'])
        ->name('inquiries.verification.status');

    Route::post('{inquiry}/verification/confirm', [InquiryVerificationController::class, 'confirm'])
        ->name('inquiries.verification.confirm');
});

// Inquiry Type CRUD

Route::group(['middleware' => 'web'], function () {
    Route::get('/inquiry_type', [InquiryTypeController::class, 'index'])->name('inquiry.type.info');
    Route::get('/getType', [InquiryTypeController::class, 'getType'])->name('inquiry.type.get'); 
    Route::get('/inquiry_type_destroy/{id}', [InquiryTypeController::class, 'destroy'])->name('inquiry.type.destroy');
    Route::post('/inquiry_type_save', [InquiryTypeController::class, 'store'])->name('inquiry.type.store');
    Route::post('/inquiry_type_save_form', [InquiryTypeController::class, 'save'])->name('inquiry.type.save');
    Route::post('/inquiry_type_update', [InquiryTypeController::class, 'update'])->name('inquiry.type.update');

});

 
// Bitrix System 
Route::group(['middleware'  =>  'auth'], function(){
    Route::get('contact_list', [BitrixController::class, 'contact_list']); 

});


// Planing Stage 

Route::group(['middleware' =>   'auth'], function(){
    Route::get('plan_details', [PlaningController::class, 'index'])->name('plan.details');
    Route::post('customer_plan_save', [PlaningController::class, 'store'])->name('planing.save');
    Route::post('customer_jump', [PlaningController::class, 'jump'])->name('planing.jump');
    Route::get('plan/delete/{id}', [PlaningController::class, 'destroy'])->name('planing.delete');
    Route::get('plan/restore/{id}', [PlaningController::class, 'restore'])->name('planing.restore');
});

// Deal Stage 
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Lists
    |--------------------------------------------------------------------------
    */

    Route::get('deal_details', [DealController::class, 'index'])
        ->name('deal.details');

    Route::get('deal_all_list', [DealController::class, 'all'])
        ->name('deal.all.list');

    Route::get('deal_junk_list', [DealController::class, 'junk_list'])
        ->name('deal.junk.list');

    Route::get('deal_delete_list', [DealController::class, 'delete_list'])
        ->name('deal.delete.list');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Create, Store, Info, Price
    |--------------------------------------------------------------------------
    */

    Route::post('customer_deal_save', [DealController::class, 'store'])
        ->name('deal.save');

    Route::post('customer/deal/store', [DealController::class, 'dealStore'])
        ->name('deal.store');

    Route::post('customer_deal_info', [DealController::class, 'info'])
        ->name('deal.info');

    Route::post('customer_deal_price', [DealController::class, 'price'])
        ->name('deal.price');

    Route::get('/get-deal-id', [DealController::class, 'getDealId'])
        ->name('deal.get-id');

    Route::get('/deal/{deal}/history', [DealController::class, 'history'])
    ->name('deal.history');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/{deal}/profile', [DealController::class, 'profile'])
        ->name('deal.profile');

    Route::post('/deal/{deal}/profile/status', [DealController::class, 'profileUpdateStatus'])
        ->name('deal.profile.status.update');

    Route::post('/deal/{deal}/profile/notes', [DealController::class, 'profileStoreNote'])
        ->name('deal.profile.notes.store');

    Route::post('/deal/{deal}/profile/documents/upload', [DealController::class, 'profileUploadDocument'])
        ->name('deal.profile.documents.upload');

    Route::delete('/deal/{deal}/profile/documents/{source}/{id}', [DealController::class, 'profileDeleteDocument'])
        ->name('deal.profile.documents.destroy');


    Route::delete('/deal/{deal}/profile/documents/images/{id}', function (\App\Models\Deal $deal, $id) {
        return app(DealController::class)->profileDeleteDocument(request(), $deal, 'image', $id);
    })->name('deal.profile.documents.images.destroy');

    Route::delete('/deal/{deal}/profile/documents/attachments/{id}', function (\App\Models\Deal $deal, $id) {
        return app(DealController::class)->profileDeleteDocument(request(), $deal, 'attachment', $id);
    })->name('deal.profile.documents.attachments.destroy');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Status, Jump, Bulk
    |--------------------------------------------------------------------------
    */

    Route::post('/deal/update-status', [DealController::class, 'updateStatus'])
        ->name('deal.status.update');

    Route::post('customer_dealljump', [DealController::class, 'jump'])
        ->name('deal.jump');

    Route::post('/deal/bulk-action', [DealController::class, 'bulkAction'])
        ->name('deal.bulk.action');

    Route::get('deal_junk/{id}', [DealController::class, 'junk'])
        ->name('deal.junk');

    Route::get('deal_unjunk/{id}', [DealController::class, 'unjunk'])
        ->name('deal.unjunk');

    Route::get('deal_delete/{id}', [DealController::class, 'destroy'])
        ->name('deal.delete');

    Route::get('deal_restore/{id}', [DealController::class, 'restore'])
        ->name('deal.restore');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Reviewers, Employees, Dates
    |--------------------------------------------------------------------------
    */

    Route::get('/get-employees', [DealController::class, 'getEmployees'])
        ->name('deal.employees');

    Route::post('/update-deal-reviewers', [DealController::class, 'updateReviewers'])
        ->name('deal.reviewers.update');

    Route::post('/update-deal-date', [DealController::class, 'updateDate'])
        ->name('deal.date.update');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Notes
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/load-customer-notes', [DealController::class, 'loadCustomerNotes'])
        ->name('deal.notes.load');

    Route::post('/deal/store-customer-note', [DealController::class, 'storeCustomerNote'])
        ->name('deal.notes.store');

    Route::post('/deal/update-customer-note', [DealController::class, 'updateCustomerNote'])
        ->name('deal.notes.update');

    Route::post('/deal/delete-customer-note', [DealController::class, 'deleteCustomerNote'])
        ->name('deal.notes.delete');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Documents, Images, Files
    |--------------------------------------------------------------------------
    */

    Route::get('customer/get/document/{customer}/{alternative}/{product}/{status}', [ImageController::class, 'getDocument'])
        ->name('customer.get.document');

    Route::post('/customer_upload', [DealController::class, 'uploadCustomerFile'])
        ->name('deal.customer-file.upload');

    Route::get('/deal/load-customer-files', [DealController::class, 'loadCustomerFiles'])
        ->name('deal.customer-files.load');

    Route::post('/deal/rename-file', [DealController::class, 'renameCustomerFile'])
        ->name('deal.customer-file.rename');

    Route::post('/deal/delete-file', [DealController::class, 'deleteCustomerFile'])
        ->name('deal.customer-file.delete');

    Route::get('/deal/file/{source}/preview/{id}', [DealController::class, 'previewCustomerFile'])
        ->name('deal.file.preview.source');

    Route::get('/deal/file/{source}/download/{id}', [DealController::class, 'downloadCustomerFile'])
        ->name('deal.file.download.source');

    Route::get('/deal/file/preview/{id}', [DealController::class, 'previewCustomerFile'])
        ->name('deal.file.preview');

    Route::get('/deal/file/download/{id}', [DealController::class, 'downloadCustomerFile'])
        ->name('deal.file.download');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Kanban
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/load-kanban-column/{status}', [DealController::class, 'loadKanbanColumn'])
        ->name('deal.kanban.column');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Invoices
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/invoices', [DealInvoiceController::class, 'index'])
        ->name('deal.invoice');

    Route::post('/deal/invoices/store', [DealInvoiceController::class, 'store'])
        ->name('deal.invoice.store');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Delivery Notes
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/{deal}/delivery-notes', [DealController::class, 'loadDeliveryNotes'])
        ->name('deal.delivery-notes');


    /*
    |--------------------------------------------------------------------------
    | Deal / Auftrag Planning
    |--------------------------------------------------------------------------
    */

    Route::get('/deal/{deal}/planning/preview', [DealController::class, 'planningPreview'])
        ->name('deal.planning.preview');

    Route::post('/deal/{deal}/planning/check', [DealController::class, 'planningCheck'])
        ->name('deal.planning.check');

    Route::post('/deal/{deal}/planning/store', [DealController::class, 'planningStore'])
        ->name('deal.planning.store');


    /*
    |--------------------------------------------------------------------------
    | Deal Measurement / Feinaufmaß
    |--------------------------------------------------------------------------
    */

    Route::get('/deal-measurements', [DealMeasurementController::class, 'index'])
        ->name('deal.measurements.index');
    Route::post('/deal-measurements/{measurement}/assign-work', [DealMeasurementController::class, 'assignWork'])
        ->name('deal.measurements.assign-work');

    /*
     * Official route used by Auftrag profile.
     */
    Route::post('/deal/{deal}/measurements/create', [DealMeasurementController::class, 'storeFromDeal'])
        ->name('deal.measurements.store-from-deal');

    /*
     * Old URL kept for compatibility.
     * IMPORTANT: different route name, so it is not duplicated.
     */
    Route::post('/deal/{deal}/measurement/send', [DealMeasurementController::class, 'storeFromDeal'])
        ->name('deal.measurements.send');

    Route::get('/deal-measurements/{measurement}', [DealMeasurementController::class, 'show'])
        ->name('deal.measurements.show');
    Route::get('/deal-measurements/{measurement}/quick-view', [DealMeasurementController::class, 'quickView'])
        ->name('deal.measurements.quick-view');


    Route::post('/deal-measurement-items/{item}/update', [DealMeasurementController::class, 'updateItem'])
        ->name('deal.measurement-items.update');

    Route::post('/deal-measurements/{measurement}/complete', [DealMeasurementController::class, 'complete'])
        ->name('deal.measurements.complete');
    Route::post('/deal-measurements/{measurement}/notes', [DealMeasurementController::class, 'storeNote'])
    ->name('deal-measurements.notes.store');

    Route::post('/deal-measurements/{measurement}/unlock', [DealMeasurementController::class, 'unlock'])
        ->name('deal.measurements.unlock');

    Route::delete('/deal-measurements/{measurement}', [DealMeasurementController::class, 'destroy'])
        ->name('deal.measurements.destroy');

   
    Route::get('/deal-measurements-kanban', [DealMeasurementController::class, 'kanban'])
        ->name('deal.measurements.kanban');

    Route::post('/deal-measurements/{measurement}/kanban-status', [DealMeasurementController::class, 'updateKanbanStatus'])
        ->name('deal.measurements.kanban.update-status');

    Route::post('/deal-measurements/{measurement}/notes', [DealMeasurementController::class, 'storeNote'])
        ->name('deal-measurements.notes.store');
 



    /*
    |--------------------------------------------------------------------------
    | Deal Measurement / Material Picker
    |--------------------------------------------------------------------------
    */

    Route::get('/measurement-material/products/search', [DealMeasurementController::class, 'search'])
        ->name('measurement-material.products.search');


    /*
    |--------------------------------------------------------------------------
    | Deal Measurement / Save Details, Materials, History
    |--------------------------------------------------------------------------
    */

    Route::post('/deal-measurements/{measurement}/details/save', [DealMeasurementController::class, 'saveDetail'])
        ->name('deal-measurements.details.save');

    Route::post('/deal-measurements/{measurement}/materials/save', [DealMeasurementMaterialController::class, 'saveMaterials'])
        ->name('deal-measurements.materials.save');

    Route::get('/deal-measurements/{measurement}/history', [DealMeasurementMaterialController::class, 'history'])
        ->name('deal-measurements.history');


    /*
    |--------------------------------------------------------------------------
    | Deal Measurement / Images
    |--------------------------------------------------------------------------
    */

    Route::get('/deal-measurements/{measurement}/images', [DealMeasurementImageController::class, 'index'])
        ->name('deal-measurements.images.index');

    Route::post('/deal-measurements/{measurement}/images/upload', [DealMeasurementImageController::class, 'upload'])
        ->name('deal-measurements.images.upload');

    Route::delete('/deal-measurements/images/{image}', [DealMeasurementImageController::class, 'destroy'])
        ->name('deal-measurements.images.destroy');
});
// Invoice CRUD

Route::prefix('admin')->name('admin.')->middleware(['auth', 'InvoiceMiddleware'])->group(function () { // FIX P0-07: Invoice-Rolle erzwingen
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/list', [InvoiceController::class, 'list'])->name('invoices.list');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

    // Select2 endpoints must come before /invoices/{invoice}
    Route::get('/invoices/select/customers', [InvoiceController::class, 'selectCustomers'])->name('invoices.select.customers');
    Route::get('/invoices/select/objects', [InvoiceController::class, 'selectObjects'])->name('invoices.select.objects');
    Route::get('/invoices/select/products', [InvoiceController::class, 'selectProducts'])->name('invoices.select.products');
    Route::get('/invoices/select/deals', [InvoiceController::class, 'selectDeals'])->name('invoices.select.deals');

    // Deal material positions for invoice drawer/canvas
    Route::get('/invoices/deals/{deal}/items', [InvoiceController::class, 'dealItems'])->name('invoices.deals.items');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');

    Route::post('/invoices/{invoice}/files', [InvoiceController::class, 'uploadFiles'])->name('invoices.files.upload');
    Route::delete('/invoice-files/{file}', [InvoiceController::class, 'deleteFile'])->name('invoices.files.delete');
    Route::get('/invoice-files/{file}/download', [InvoiceController::class, 'downloadFile'])->name('invoices.files.download');
    Route::get('/invoice-files/{file}/view', [InvoiceController::class, 'viewFile'])->name('invoices.files.view');
});

Route::middleware(['auth', 'InvoiceMiddleware'])->group(function () { // FIX P0-07: Canvas = Rechnungsbearbeitung
    Route::prefix('invoices/canvas')->name('invoices.canvas.')->group(function () {
        Route::get('/offer-detail/{offerDetail}', [InvoiceCanvasController::class, 'createFromOfferDetail'])
            ->name('offer-detail.create');

        Route::post('/offer-detail/{offerDetail}/draft', [InvoiceCanvasController::class, 'storeDraftFromOfferDetail'])
            ->name('offer-detail.draft');

        Route::get('/{invoice}', [InvoiceCanvasController::class, 'edit'])
            ->name('edit');

        Route::post('/{invoice}/save', [InvoiceCanvasController::class, 'save'])
            ->name('save');
        Route::post('/{invoice}/sync-auftrag', [InvoiceCanvasController::class, 'syncFromAuftrag'])
            ->name('sync-auftrag');
    });
});


// Deal Measurement - Finausmass 

Route::middleware(['auth'])->group(function () {
    Route::post('/deal-measurements/{measurement}/images/upload', [DealMeasurementImageController::class, 'upload'])->name('deal-measurements.images.upload');
    Route::get('/deal-measurements/{measurement}/images', [DealMeasurementImageController::class, 'index'])->name('deal-measurements.images.index');
    Route::delete('/deal-measurements/images/{image}', [DealMeasurementImageController::class, 'destroy'])->name('deal-measurements.images.destroy');
});
 
 
// Offer Stage 
 
Route::group(['middleware'=> 'auth'], function(){
    Route::get('chats/{user}',[MessageController::class, 'index'])->name('chats.view');
});
Route::get('/dispatch-chat-jobs/{startId}/{endId}/{chunkSize?}', [MessageController::class, 'dispatchChatProcessingJobs'])->middleware('auth');
Route::get('/chat-jobs/{startId}/{endId}/{chunkSize?}', [MessageController::class, 'dispatchChatJobs'])->middleware('auth');



Route::get('/run-backfill-phase-sections', function () {
    try {
        Artisan::call('backfill:phase-sections');
        return response()->json(['message' => 'Backfill phase-sections executed successfully.']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth');

Route::get('weather_station', [WeatherStationController::class, 'index']);
Route::post('weather_station_upload', [WeatherStationController::class, 'upload'])->name('weather_stations.upload');

 


Route::group(['middleware'=>'web'], function(){
    Route::get('knowlege', [KnowledgeCategoryController::class, 'index'])->name('knowledge.base'); 
    Route::get('question/{id}', [KnowledgeCategoryController::class, 'question'])->name('knowledge.question'); 
    Route::resource('knowledge', KnowledgeCategoryController::class);
    Route::get('question/create/{id}', [KnowledgeQuestionController::class, 'create'])->name('question.create'); 
    Route::post('question/store', [KnowledgeQuestionController::class, 'store'])->name('question.store'); 
    Route::post('question/update', [KnowledgeQuestionController::class, 'update'])->name('question.update'); 
    Route::get('getQuestion/{question_id}', [KnowledgeQuestionController::class, 'getQuestion'])->name('question.get'); 
    Route::delete('deleteQuestion/{question_id}', [KnowledgeQuestionController::class, 'destroy'])->name('question.destroy'); 
    Route::get('editQuestion/{question_id}', [KnowledgeQuestionController::class, 'edit'])->name('question.edit'); 
    Route::get('search_question', [KnowledgeCategoryController::class, 'search'])->name('search.question'); 


});


// Personal Note CRUD 


Route::middleware('auth')->group(function () {
    Route::get('/notes_view', [PersonalNoteController::class, 'index'])->name('notes');
    Route::get('/notes_lists', [PersonalNoteController::class, 'list_index'])->name('notes.lists');
   Route::get('/notes/{id}', [PersonalNoteController::class, 'show'])->name('notes.show');

    Route::get('/note_view_filter', [PersonalNoteController::class, 'filter'])->name('notes.filter');
    Route::post('/notes_store', [PersonalNoteController::class, 'store'])->name('notes.store');
    Route::put('/notes_update/{id}', [PersonalNoteController::class, 'update'])->name('notes.update');
    Route::put('/notes_done/{id}', [PersonalNoteController::class, 'done'])->name('notes.done');
    Route::put('/notes_update_name/{id}', [PersonalNoteController::class, 'name'])->name('notes.update.name');
    Route::put('/notes_update_note/{id}', [PersonalNoteController::class, 'note'])->name('notes.update.note');
    Route::delete('/notes_delete/{id}', [PersonalNoteController::class, 'destroy'])->name('notes.destroy');
    Route::put('/notes_no_repeat/{id}', [PersonalNoteController::class, 'no_repeat'])->name('notes.no.repeat');
    Route::put('/notes_no_reminder/{id}', [PersonalNoteController::class, 'no_reminder'])->name('notes.no.reminder');
    Route::get('/fetch_note_category', [PersonalNoteController::class, 'getCategory'])->name('notes.fetch.category');
    Route::put('/fetch_note_category/{id}/{category_id}', [PersonalNoteController::class, 'updateCategory'])->name('notes.update.category');
    Route::put('/note_change_color/{id}', [PersonalNoteController::class, 'changeColor'])->name('notes.update.color');
    Route::put('/note_change_date/{id}', [PersonalNoteController::class, 'changeDate'])->name('notes.update.date');
    Route::put('/note_change_time/{id}', [PersonalNoteController::class, 'changeTime'])->name('notes.update.time');
    Route::post('/notes/update-order', [PersonalNoteController::class, 'updateOrder'])->name('notes.updateOrder');
    Route::get('/note_trash', [PersonalNoteController::class, 'trash'])->name('notes.trash');
    Route::delete('/notes_permanent_delete/{id}', [PersonalNoteController::class, 'permanentDelete'])->name('notes.permanentDelete');
    Route::put('/notes_recover/{id}', [PersonalNoteController::class, 'recover'])->name('notes.recover');
    Route::put('/notes_update_settings/{id}', [PersonalNoteController::class, 'updateSettings'])->name('notes.update.settings');
    Route::get('/note_search', [PersonalNoteController::class, 'search'])->name('notes.search');
    Route::get('/note_search_category', [PersonalNoteController::class, 'category_search'])->name('notes.search.category'); 
    Route::post('/process-repeating-notes', [PersonalNoteController::class, 'processRepeatingNotes'])->name('notes.processRepeatingNotes');
    Route::put('/notes/stop-repeating-all', [PersonalNoteController::class, 'stopRepeatingForAll'])->name('notes.stopRepeatingForAll'); 
    Route::get('/notes_details', [PersonalNoteController::class, 'details'])->name('notes.details');
   


});

Route::middleware(['auth'])->group(function () {
    // Unique names to prevent conflict
    Route::get('/personal-notes/fetch', [PersonalNoteController::class, 'fetchNotes'])->name('personal_notes.fetch');
    Route::get('/personal-notes/categories', [PersonalNoteController::class, 'getCategories'])->name('personal_notes.categories');
    Route::post('/personal-notes/store', [PersonalNoteController::class, 'saves'])->name('personal_notes.store');
    Route::post('/personal-notes/reorder', [PersonalNoteController::class, 'reorder'])->name('personal_notes.reorder');
    Route::put('/personal-notes/update/{id}', [PersonalNoteController::class, 'update'])->name('personal_notes.update');
    Route::put('/personal-notes/done/{id}', [PersonalNoteController::class, 'markDone'])->name('personal_notes.done');
    Route::delete('/personal-notes/delete/{id}', [PersonalNoteController::class, 'destroy'])->name('personal_notes.delete');
    Route::get('/note_category_view', [NoteCategoryController::class, 'index'])->name('note.category.view');
    Route::post('/note_category_store', [NoteCategoryController::class, 'store'])->name('note.category.store');
    Route::post('/note_category_update', [NoteCategoryController::class, 'update'])->name('note.category.update');
    Route::get('/note_category_destroy/{id}', [NoteCategoryController::class, 'destroy'])->name('note.category.delete');
    Route::get('/note_category_get', [NoteCategoryController::class, 'getCategory'])->name('note.category.get');
    Route::post('/note_category_auto_save', [NoteCategoryController::class, 'auto_save'])->name('note.category.auto.save');
    Route::get('/due-personal-notes', [PersonalNoteReminderController::class, 'getDueReminders']);
    Route::post('/reminder/{id}/status', [PersonalNoteReminderController::class, 'updateReminderStatus']);

}); 

  
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/all-contacts', [AllContactController::class, 'index'])->name('all.contacts');
    Route::get('/all-contacts/export', [AllContactController::class, 'export'])->name('all.contacts.export');
    Route::get('/global-search', [AllContactController::class, 'globalSearch'])->name('contacts.global.search'); 
    // routes/web.php
    Route::post('/global-restore/customer',   [AllContactController::class, 'customer'])->name('global.restore.customer');
    Route::post('/global-restore/brand',      [AllContactController::class, 'brand'])->name('global.restore.brand');
    Route::post('/global-restore/distributor',[AllContactController::class, 'distributor'])->name('global.restore.distributor');

});

//Data Norm Controlelr 
Route::get('/datanorm-upload', [\App\Http\Controllers\DatanormController::class, 'showForm'])->name('datanorm.form');
Route::post('/datanorm-parse', [\App\Http\Controllers\DatanormController::class, 'parseFile'])->name('datanorm.parse');

Route::get('/testnav', function(){
    return view('admin.roof_config.config');
});
Route::get('/testnav2', function(){
    return view('admin.roof_config.config2');
});

Route::get('roofs', function(){
    return view('admin.roof_config.roofs');
});

Route::get('roof', function(){
    return view('admin.roof_config.roof');
});

Route::get('/nibe/auth', [ApiLinkController::class, 'redirectToAuth']);
Route::get('/get/nibe/data', [ApiLinkController::class, 'handleCallback']);
Route::get('/nibe/devices', [ApiLinkController::class, 'showDevices']);
Route::get('/nibe/devices/{deviceId}', [ApiLinkController::class, 'showDevice']);
Route::get('/nibe/refresh', [ApiLinkController::class, 'refreshToken']);

        Route::get('test_dashboard', function(){
            return view('admin.dashboard.test');
        });


        Route::get('/test-notification', function () {
            broadcast(new TestNotificationEvent('🔥 Hello from Laravel Reverb!'));
            return 'Notification sent';
        });


        // Chating Reverb 

Route::middleware('auth')->group(function () {
    Route::get('admin/chat', [ChatController::class, 'index'])->name('chat.index'); 
    Route::get('/chat/fetch/{userId}', [ChatController::class, 'fetch']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::get('/chat/messages/{id}', [ChatController::class, 'messages']);
    Route::get('/chat/employee', [ChatController::class, 'getEmployeeUsers'])->middleware('auth');
    Route::get('/chat/employees', [ChatController::class, 'getEmployeesAndGroups'])->middleware('auth');
    Route::post('/chat/share', [ChatController::class, 'share'])->name('chat.share');
    Route::get('/chat/unread-counts', [ChatController::class, 'unreadCounts']);
    Route::post('/chat/mark-read/{userId}', [ChatController::class, 'markAsRead']);
    Route::delete('/chat/delete/{id}', [ChatController::class, 'destroy']);

    Route::post('/chat/group/creates', [ChatController::class, 'createGroup']);
    Route::post('/chat/group/leave', [ChatController::class, 'leaveGroup']);
    Route::get('/chat/group/fetch/{id}', [ChatController::class, 'fetchGroupMessages'])->middleware('auth');

    Route::post('/chat/group/mark-read/{id}', [ChatController::class, 'markGroupRead']);
    Route::get('/chat/group/users/{groupId}', [ChatController::class, 'getGroupUsers']);
    Route::get('/chat/customers/search', [ChatController::class, 'searchCustomers'])->middleware('auth');
    Route::post('/chat/group/invite/{group}/accept', [ChatGroupController::class, 'acceptInvite']);
    Route::post('/chat/group/invite/{group}/decline', [ChatGroupController::class, 'declineInvite']);

    Route::prefix('chat/group')->group(function () {
        Route::post('/create', [ChatController::class, 'createGroup']);
        Route::get('/users/{id}', [ChatGroupController::class, 'getUsers']);
        Route::post('/upload-avatar', [ChatGroupController::class, 'uploadAvatar']); 

    });
    Route::get('/chat/mentions/unread', [ChatController::class, 'unreadMentions'])
        ->name('chat.mentions.unread');

    Route::post('/chat/mentions/{mention}/read', [ChatController::class, 'markMentionRead'])
        ->name('chat.mentions.read');
    Route::get('/chat/readers/{chat}', [ChatController::class, 'readers'])->middleware('auth'); 
    Route::put('/chat/group/update/{id}', [ChatGroupController::class, 'update']);
    Route::delete('/chat/group/delete/{id}', [ChatGroupController::class, 'destroy']);
    Route::post('/chat/group/leave/{id}', [ChatGroupController::class, 'leave']);
    Route::get('/chat/contexts/search', [ChatController::class, 'searchCustomerContexts'])->middleware('auth');
    Route::delete('/chat/group/remove-member/{group}/{user}', [ChatGroupController::class, 'removeMember'])->name('chat.group.remove-member');

    Route::post('/chat/group/leave/{group}', [ChatGroupController::class, 'leave']);
        Route::delete('/chat/group/{group}', [ChatGroupController::class, 'destroy']);

        Route::post('/chat/group/create-from-private', [ChatGroupController::class, 'createFromPrivate']);
        Route::post('/chat/group/add-members/{id}', [ChatGroupController::class, 'addMembers']);

        Route::get('/chat/attachment/{id}', [ChatAttachmentController::class,'show'])
        ->middleware('auth')
        ->name('chat.attachment.show');

    Route::post('/chat/pin/toggle', [PinnedPrivateChatController::class, 'toggle'])
    ->name('chat.pin.toggle')
    ->middleware('auth');

});

// web.php
Route::get('/chat/unread-count', function () {
    return ['count' => \App\Models\Chat::where('to_user_id', auth()->id())->where('is_read', false)->count()];
});

Route::post('/chat/mark-as-read', function () {
    \App\Models\Chat::where('to_user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
    return ['success' => true];
});
Route::get('/keep-alive', function () {
    return response()->json(['status' => 'alive']);
});


Route::middleware(['auth']) // add your admin middleware here
    ->prefix('admin/chat/learnings')
    ->name('admin.chat.learnings.')
    ->group(function () {
    Route::get('/', [LearningTopicController::class, 'index'])->name('index');
    // AJAX endpoints
    Route::get('/list', [LearningTopicController::class, 'list'])->name('list');
    Route::get('/{topic}', [LearningTopicController::class, 'show'])->name('show');
    Route::post('/', [LearningTopicController::class, 'store'])->name('store');
    Route::delete('/{topic}', [LearningTopicController::class, 'destroy'])->name('destroy');
    Route::post('/{topic}/media', [LearningTopicController::class, 'uploadMedia'])->name('media.upload');
    Route::delete('/media/{media}', [LearningTopicController::class, 'deleteMedia'])->name('media.destroy');
});
  
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/news/sync', [NewsFeedController::class, 'syncSolarNews'])->name('chat.news.sync');
    Route::get('/chat/news', [NewsFeedController::class, 'index']);
});

Route::prefix('chat/tutorials')->middleware(['auth'])->group(function () {
    Route::get('/', [ChatController::class, 'learning'])->name('chat.tutorials.index');
    Route::get('/{topic}', [ChatController::class, 'learningShow'])->name('chat.tutorials.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/ai/chats', [ChatPageController::class, 'index'])->name('ai.chats');
    Route::get('/ai/chats/{chat}', [ChatPageController::class, 'show'])->name('ai.chats.show');
    Route::post('/ai/chats', [AiMessageController::class, 'createChat'])->name('ai.chats.store');
    Route::post('/ai/chats/{chat}/message', [AiMessageController::class, 'ask'])->name('ai.chats.ask')->middleware(['auth'])->withoutMiddleware([\App\Http\Middleware\TrimStrings::class]);  
    Route::delete('/ai/chats/{chat}', [AiMessageController::class, 'destroy'])->name('ai.chats.destroy');
    Route::get('/api/ai/chats/by-customer', [ChatPageController::class, 'byCustomerIds'])->name('ai.chats.byCustomer');
    Route::get('/api/ai/chats', [ChatPageController::class, 'indexApi'])->name('ai.chats.indexApi');
    Route::get('/customers/search', [ChatPageController::class, 'search'])->name('customers.search');
});

Route::post('/ai/chats/{chat}/reset-memory', function(\App\Models\AiChat $chat){
    Gate::authorize('update', $chat);   
    $chat->update([
        'memory_summary'   => null,
        'memory_updated_at'=> null,
    ]);
    return back()->with('ok', 'Memory cleared for this chat.');
})->name('ai.chats.reset_memory')->middleware('auth');
// Public share
Route::get('/ai/s/{token}', [ShareController::class, 'publicView'])->name('ai.share.public');

Route::get('request/token', function(){
    return view('ai.token');
});
 
  Route::middleware(['auth'])
    ->prefix('customer/appointments')
    ->name('customer.appointments.')
    ->group(function () {

        // Page
        Route::get('/', [CustomerMainAppointmentController::class, 'index'])
            ->name('index');

        // Data for Kanban + List (AJAX)
        Route::get('/data', [CustomerMainAppointmentController::class, 'data'])
            ->name('data');

        // CRUD
        Route::post('/', [CustomerMainAppointmentController::class, 'store'])
            ->name('store');

        Route::put('/{appointment}', [CustomerMainAppointmentController::class, 'update'])
            ->name('update');

        Route::delete('/{appointment}', [CustomerMainAppointmentController::class, 'destroy'])
            ->name('destroy');

        // Restore soft deleted
        Route::post('/{id}/restore', [CustomerMainAppointmentController::class, 'restore'])
            ->name('restore');

        // Force delete
        Route::delete('/{id}/force', [CustomerMainAppointmentController::class, 'forceDelete'])
            ->name('forceDelete');

        // Status change from Kanban
        Route::post('/{appointment}/status', [CustomerMainAppointmentController::class, 'updateStatus'])
            ->name('updateStatus');

        // Archive / Junk shortcuts
        Route::post('/{appointment}/archive', [CustomerMainAppointmentController::class, 'archive'])->name('archive');
        Route::post('/{appointment}/junk', [CustomerMainAppointmentController::class, 'junk'])->name('junk');
        // Employees
        Route::post('/{appointment}/employees', [CustomerMainAppointmentController::class, 'addEmployee'])->name('addEmployee');
        Route::delete('/{appointment}/employees/{employee}', [CustomerMainAppointmentController::class, 'removeEmployee'])->name('removeEmployee');
        // Global notifications list (ticker) – CHANGED NAME
        Route::get('/notifications-ticker', [CustomerMainAppointmentController::class, 'notifications'])->name('notificationsTicker');
        // Notify all due today
        Route::post('/notify-due-today', [CustomerMainAppointmentController::class, 'notifyDueToday'])->name('notify_due_today');
        // PROFILE PAGE
        Route::get('/{appointment}', [CustomerMainAppointmentController::class, 'show'])->name('show');
        // Reports for a single appointment
        Route::post('/{appointment}/reports', [CustomerMainAppointmentController::class, 'storeReport'])->name('reports.store');
        // NEW: like / dislike reaction on a report
        Route::post('/{appointment}/reports/{report}/react', [CustomerMainAppointmentController::class, 'reactReport'])->name('reports.react');
        // NEW: comments on a single report (JSON comment_items)
        Route::post('/{appointment}/reports/{report}/comments', [CustomerMainAppointmentController::class, 'addReportCommentItem'])->name('reports.comments.store');
        // Appointment-wide comments
        Route::post('/{appointment}/comments', [CustomerMainAppointmentController::class, 'storeComment'])->name('comments.store');
        // Notifications for a single appointment (used in the profile JS)
        Route::get('/{appointment}/notifications', [CustomerMainAppointmentController::class, 'appointmentNotifications'])->name('notifications');
        Route::get('{appointment}/reports', [AppointmentReportController::class, 'list'])->name('reports.list');  
      
    });

    Route::get('customer/appointments/{appointment}/reports',  [CustomerMainAppointmentController::class, 'reports'])->middleware('auth');


 

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/breaking-news', [BreakingNewsController::class, 'index'])
        ->name('breaking-news.index');

    Route::post('/admin/breaking-news', [BreakingNewsController::class, 'store'])
        ->name('breaking-news.store');

    Route::put('/admin/breaking-news/{breakingNews}', [BreakingNewsController::class, 'update'])
        ->name('breaking-news.update');

    Route::delete('/admin/breaking-news/{breakingNews}', [BreakingNewsController::class, 'destroy'])
        ->name('breaking-news.destroy');

    Route::post('/admin/breaking-news/{breakingNews}/toggle', [BreakingNewsController::class, 'toggleStatus'])
        ->name('breaking-news.toggle');
    Route::get('/dashboard/breaking-news/active', [BreakingNewsController::class, 'active'])
    ->name('breaking-news.active');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/widgets/holiday-birthday', [HolidayBirthdayWidgetController::class, 'index'])
        ->name('dashboard.widgets.holiday-birthday');
});

Route::group(['prefix' => 'admin/personal-notes', 'middleware' => ['auth']], function () {
    Route::get('/fetch', [AdminPersonalNoteController::class, 'fetchNotes'])->name('admin.notes.fetch');
    Route::post('/store', [AdminPersonalNoteController::class, 'store'])->name('admin.notes.store');
    Route::post('/update', [AdminPersonalNoteController::class, 'update'])->name('admin.notes.update');
    Route::post('/delete', [AdminPersonalNoteController::class, 'destroy'])->name('admin.notes.delete');
    Route::post('/reorder', [AdminPersonalNoteController::class, 'reorder'])->name('admin.notes.reorder');
    
    // Category Routes
    Route::post('/category/store', [AdminPersonalNoteController::class, 'storeCategory'])->name('admin.notes.category.store');
    Route::get('/categories', [AdminPersonalNoteController::class, 'getCategories'])->name('admin.notes.categories');
     Route::patch('/employee-notes/{note}/toggle-done', [AdminPersonalNoteController::class, 'toggleDone'])->name('employee-notes.toggle-done');
    Route::delete('/employee-notes/{note}', [AdminPersonalNoteController::class, 'delete'])->name('employee-notes.destroy');
});

Route::post('/employees/{employee}/notes', [AdminPersonalNoteController::class, 'empStore'])
    ->name('employee-notes.store');

 
Route::middleware(['auth'])
    ->prefix('admin/master-sets')
    ->name('admin.master_sets.')
    ->group(function () {

        // ============================================================
        // A) VIEWS
        // ============================================================
        Route::get('/', [MasterSetController::class, 'index'])->name('index');
        Route::get('{id}/editor', [MasterSetController::class, 'editor'])
            ->whereNumber('id')
            ->name('editor');
        
        Route::get('/{masterSet}/duplicate-options', [MasterSetController::class, 'duplicateOptions']);
        Route::post('/{masterSet}/duplicate', [MasterSetController::class, 'duplicate']);

         Route::get('/distributor-compare/product/{product}/distributor/{distributor}',
                [MasterSetDistributorCompareController::class, 'compare'])->name('distributor-compare');

        Route::get('/distributor-compare/product/{product}/distributor/{distributor}/chart',[MasterSetDistributorCompareController::class, 'chart'])->name('distributor-compare.chart');

        // ============================================================
        // B) API / DATA
        // ============================================================
        Route::get('groups', [MasterSetController::class, 'groups'])->name('article_groups');
        Route::get('data', [MasterSetController::class, 'data'])->name('data');
        Route::get('catalog', [MasterSetController::class, 'catalog'])->name('catalog');
        Route::get('labor/options', [MasterSetController::class, 'laborOptions'])->name('labor_options');
        Route::get('tasks/options', [MasterSetController::class, 'taskOptions'])->name('task_options');
        Route::get('distributor-price/{id}', [MasterSetController::class, 'distributorPrice'])
            ->whereNumber('id')
            ->name('distributor_price');

        // ============================================================
        // C) CRUD (MasterSet)
        // ============================================================
        Route::post('/', [MasterSetController::class, 'store'])->name('store');
        Route::get('{masterSet}', [MasterSetController::class, 'show'])
            ->whereNumber('masterSet')
            ->name('show');
        Route::put('{masterSet}', [MasterSetController::class, 'update'])
            ->whereNumber('masterSet')
            ->name('update');
        Route::delete('{masterSet}', [MasterSetController::class, 'destroy'])
            ->whereNumber('masterSet')
            ->name('destroy');

        // ============================================================
        // D) CHECKLISTS
        // ============================================================
        Route::get('checklists/options', [MasterSetController::class, 'checklistOptions'])
            ->name('checklists.options');
        Route::post('checklists/validate', [MasterSetController::class, 'validateChecklistAttach'])
            ->name('checklists.validate');
        Route::get('checklists/{checklist}/items', [MasterSetController::class, 'items'])
            ->whereNumber('checklist')
            ->name('checklists.items');

        // ============================================================
        // E) COMPONENT DESCRIPTIONS
        // ============================================================
        Route::controller(MasterSetComponentDescriptionController::class)
            ->prefix('components')
            ->group(function () {
                Route::get('{component}/descriptions', 'index')->whereNumber('component')
                    ->name('component_descriptions.index');
                Route::post('{component}/descriptions', 'store')->whereNumber('component')
                    ->name('component_descriptions.store');
                Route::post('{component}/descriptions/reorder', 'reorder')->whereNumber('component')
                    ->name('component_descriptions.reorder');

                Route::put('descriptions/{desc}', 'update')->whereNumber('desc')
                    ->name('component_descriptions.update');
                Route::delete('descriptions/{desc}', 'destroy')->whereNumber('desc')
                    ->name('component_descriptions.destroy');
            });

        // ============================================================
        // F) GROUPS (Folders)
        // ============================================================
        Route::controller(MasterSetGroupController::class)
            ->prefix('groups')
            ->name('groups.')
            ->group(function () {
                // static first
                Route::get('list', 'list')->name('list');
                Route::get('sets', 'sets')->name('sets');
                Route::post('/', 'store')->name('store');

                // dynamic
                Route::prefix('{group}')->whereNumber('group')->group(function () {
                    Route::put('/', 'update')->name('update');
                    Route::delete('/', 'destroy')->name('destroy');
                    Route::get('stats', 'stats')->name('stats');
                    Route::get('master-sets', 'groupMasterSets')->name('master_sets');
                });
            });

        // ============================================================
        // G) GROUP-SETS (Legacy)
        // ============================================================
        Route::controller(MasterSetGroupController::class)
            ->prefix('group-sets')
            ->name('group_sets.')
            ->group(function () {
                Route::get('/', 'groupSetsIndex')->name('index');
                Route::post('/', 'groupSetsStore')->name('store');

                Route::prefix('{groupSet}')->whereNumber('groupSet')->group(function () {
                    Route::get('/', 'groupSetsShow')->name('show');
                    Route::put('/', 'groupSetsUpdate')->name('update');
                    Route::delete('/', 'groupSetsDestroy')->name('destroy');
                });
            });
});

Route::middleware('auth')
    ->prefix('admin/master-set-carts')
    ->name('admin.master-set-carts.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Cart main
        |--------------------------------------------------------------------------
        */
        Route::get('/', [MasterSetCartController::class, 'index'])->name('index');
        Route::get('/create', [MasterSetCartController::class, 'builder'])->name('builder');

        Route::get('/article-group-master-sets', [MasterSetCartController::class, 'articleGroupMasterSets'])
            ->name('article-group-master-sets');

        Route::get('/products/search', [MasterSetCartController::class, 'searchProducts'])
            ->name('products.search');

        Route::post('/', [MasterSetCartController::class, 'store'])->name('store');
        Route::get('/{cart}', [MasterSetCartController::class, 'show'])->name('show');
        Route::put('/{cart}', [MasterSetCartController::class, 'update'])->name('update');
        Route::delete('/{cart}', [MasterSetCartController::class, 'destroy'])->name('destroy');

        /*
        |--------------------------------------------------------------------------
        | Convert cart to master set
        |--------------------------------------------------------------------------
        */
        Route::post('/{cart}/convert', [MasterSetCartController::class, 'convert'])->name('convert');

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */
        Route::post('/{cart}/sections', [MasterSetCartController::class, 'storeSection'])->name('sections.store');
        Route::put('/sections/{section}', [MasterSetCartController::class, 'updateSection'])->name('sections.update');
        Route::delete('/sections/{section}', [MasterSetCartController::class, 'destroySection'])->name('sections.destroy');

        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */
        Route::post('/{cart}/items', [MasterSetCartController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{item}', [MasterSetCartController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [MasterSetCartController::class, 'destroyItem'])->name('items.destroy');
        Route::post('/items/{item}/move', [MasterSetCartController::class, 'moveItem'])->name('items.move');

        /*
        |--------------------------------------------------------------------------
        | Sort / hierarchy sync
        |--------------------------------------------------------------------------
        */
        Route::post('/{cart}/sync-order', [MasterSetCartController::class, 'syncOrder'])->name('sync-order');


    });


Route::middleware(['auth'])
    ->prefix('mobile')
    ->as('mobile.')
    ->group(function () {

        // View (unique URI + unique name)
        Route::get('mobile-calendar', [MobileCalendarController::class, 'index'])
            ->name('mobile_calendar.index');

        // API Data (unique URIs + unique names)
        Route::get('mobile-calendar/employees', [MobileCalendarController::class, 'getEmployees'])
            ->name('mobile_calendar.employees');

        Route::get('mobile-calendar/events', [MobileCalendarController::class, 'getEvents'])
            ->name('mobile_calendar.events');

        // Actions (unique URI + unique name)
        Route::post('mobile-calendar/appointments', [MobileCalendarController::class, 'store'])
            ->name('mobile_calendar.appointments.store');
    });

Route::get('/mobile/attendance', function(){
    return view('admin.daily_report.prototype.test1');
});

Route::get('/mobile/calendar', function(){
    return view('admin.daily_report.prototype.blackgrading');
});

 
Route::get('visual/plan', function(){ 
    return view('admin.planner.visual');
});


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('goods-receipts/relation-options', [GoodsReceiptController::class, 'relationOptions'])
        ->name('goods-receipts.relation-options');

    Route::get('goods-receipts', [GoodsReceiptController::class, 'index'])
        ->name('goods-receipts.index');

    Route::get('goods-receipts/data', [GoodsReceiptController::class, 'data'])
        ->name('goods-receipts.data');

    Route::post('goods-receipts', [GoodsReceiptController::class, 'store'])
        ->name('goods-receipts.store');

    Route::get('goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'show'])
        ->name('goods-receipts.show');

    Route::put('goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'update'])
        ->name('goods-receipts.update');

    Route::delete('goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'destroy'])
        ->name('goods-receipts.destroy');

    Route::post('goods-receipts/{goodsReceipt}/issue', [GoodsReceiptController::class, 'issue'])
        ->name('goods-receipts.issue');

    Route::patch('goods-receipts/{goodsReceipt}/quick-status', [GoodsReceiptController::class, 'quickStatus'])
        ->name('goods-receipts.quick-status');
});



Route::prefix('planner')
    ->middleware(['web', 'auth'])
    ->as('planner.')
    ->group(function () {
        Route::pattern('project', '[0-9]+');
        Route::pattern('plan', '[0-9]+');
        Route::pattern('item', '[0-9]+');
        Route::pattern('step', '[0-9]+');
        Route::pattern('material', '[0-9]+');
        Route::pattern('customerId', '[0-9]+');
        Route::pattern('planId', '[0-9]+');
        Route::pattern('itemId', '[0-9]+');

        /*
        |--------------------------------------------------------------------------
        | Old /planner fallback
        |--------------------------------------------------------------------------
        */
        Route::get('/', function () {
            return redirect()->route('planner.projects');
        })->name('index');

        /*
        |--------------------------------------------------------------------------
        | Project list / Projektplanung
        |--------------------------------------------------------------------------
        */
        Route::get('/projects', [PlannerPlanController::class, 'projectCockpit'])
            ->name('projects');

        Route::get('/projects/data', [PlannerPlanController::class, 'projectCockpitData'])
            ->name('projects.data');

        Route::get('/projects/kanban', [PlannerPlanController::class, 'projectKanbanData'])
            ->name('projects.kanban');

        Route::get('/projects/candidates', [PlannerPlanController::class, 'projectCandidates'])
            ->name('projects.candidates');

        Route::post('/projects/store', [PlannerPlanController::class, 'storeProjectFromLeadProduct'])
            ->name('projects.store');

        Route::post('/projects/{project}/ensure-plan', [PlannerPlanController::class, 'ensureProjectPlan'])
            ->name('projects.ensure_plan');

        Route::post('/projects/{project}/move', [PlannerPlanController::class, 'moveProjectKanban'])
            ->name('projects.move');

        Route::get('/projects/{project}/history', [PlannerPlanController::class, 'projectHistory'])
            ->name('projects.history');

        Route::get('/projects/{project}/profile', [PlannerPlanController::class, 'projectProfile'])
            ->name('projects.profile');

        Route::get('/projects/{project}/profile/data', [PlannerPlanController::class, 'projectProfileData'])
            ->name('projects.profile.data');

        Route::post('/projects/{project}/team', [PlannerPlanController::class, 'saveProjectTeam'])
            ->name('projects.team.save');

        /*
        |--------------------------------------------------------------------------
        | Selected project cockpit / Montage Planung
        |--------------------------------------------------------------------------
        */
        Route::get('/cockpit', [PlannerPlanController::class, 'index'])
            ->name('cockpit');

        /*
        |--------------------------------------------------------------------------
        | Cockpit AJAX endpoints
        |--------------------------------------------------------------------------
        */
        Route::get('/projects/{project}/montage-work', [PlannerPlanController::class, 'montageWorkPayload'])
            ->name('projects.montage_work');

        Route::post('/projects/{project}/team-member', [PlannerPlanController::class, 'saveProjectTeamMember'])
            ->name('projects.team.member');

        Route::post('/projects/{project}/work-items', [PlannerPlanController::class, 'storeProjectWorkItem'])
            ->name('projects.work_items.store');


        Route::post('/plans/{plan}/items/{item}/comments', [PlannerPlanController::class, 'storeItemComment'])
            ->name('plans.items.comments.store');

        Route::delete('/plans/{plan}/items/{item}/comments/{comment}', [PlannerPlanController::class, 'destroyItemComment'])
            ->name('plans.items.comments.destroy');

        Route::post('/plans/{plan}/items/{item}/gallery', [PlannerPlanController::class, 'storeItemGallery'])
            ->name('plans.items.gallery.store');

        Route::delete('/plans/{plan}/items/{item}/gallery/{image}', [PlannerPlanController::class, 'destroyItemGallery'])
            ->name('plans.items.gallery.destroy');

        /*
        |--------------------------------------------------------------------------
        | Drag & Drop
        |--------------------------------------------------------------------------
        */
        Route::post('/dnd/add', [PlannerPlanController::class, 'add'])
            ->name('dnd.add');

        Route::post('/dnd/move', [PlannerPlanController::class, 'move'])
            ->name('dnd.move');

        Route::post('/dnd/order', [PlannerPlanController::class, 'order'])
            ->name('dnd.order');

        /*
        |--------------------------------------------------------------------------
        | Planner resources
        |--------------------------------------------------------------------------
        */
        Route::get('/employees/active', [PlannerPlanController::class, 'employeesActive'])
            ->name('employees.active');

        Route::get('/customers', [PlannerPlanController::class, 'customersIndex'])
            ->name('customers.index');

        Route::get('/customers/{customerId}/lead-products', [PlannerPlanController::class, 'customerLeadProducts'])
            ->name('customers.lead_products');

        Route::get('/phases', [PlannerPlanController::class, 'phasesAndActivities'])
            ->name('phases.activities');

        Route::get('/object-data', [PlannerPlanController::class, 'getObjectData'])
            ->name('object.data');

        /*
        |--------------------------------------------------------------------------
        | Plan sync / payload
        |--------------------------------------------------------------------------
        | Supports GET and POST because older/newer JS may call it differently.
        |--------------------------------------------------------------------------
        */
        Route::match(['GET', 'POST'], '/plans/sync', [PlannerPlanController::class, 'syncAndLoad'])
            ->name('plans.sync');

        Route::get('/plans/{plan}', [PlannerPlanController::class, 'show'])
            ->name('plans.show');

        /*
        |--------------------------------------------------------------------------
        | Planner dependencies
        |--------------------------------------------------------------------------
        | Final names:
        | planner.plans.dependencies.store
        | planner.plans.dependencies.destroy
        |--------------------------------------------------------------------------
        */
        Route::post('/plans/{plan}/dependencies', [PlannerPlanController::class, 'storeDependency'])
            ->name('plans.dependencies.store');

        Route::delete('/plans/{plan}/dependencies', [PlannerPlanController::class, 'destroyDependency'])
            ->name('plans.dependencies.destroy');

        /*
        |--------------------------------------------------------------------------
        | Planner item update / delete
        |--------------------------------------------------------------------------
        | Final names:
        | planner.planItems.update
        | planner.planItems.destroy
        |--------------------------------------------------------------------------
        */
        Route::patch('/plans/{plan}/items/{item}', [PlannerPlanController::class, 'updateItem'])
            ->name('planItems.update');

        Route::delete('/plans/{plan}/items/{item}', [PlannerPlanController::class, 'destroyItem'])
            ->name('planItems.destroy');

        /*
        |--------------------------------------------------------------------------
        | Planner item status timer
        |--------------------------------------------------------------------------
        */
        Route::get('/plans/{planId}/items/status', [PlannerItemStateController::class, 'statusesByPlan'])
            ->name('plans.items.status'); // timer/list status endpoint
    
        Route::post('/plans/{planId}/items/{itemId}/play', [PlannerItemStateController::class, 'play'])
            ->name('plans.items.play');

        Route::post('/plans/{planId}/items/{itemId}/pause', [PlannerItemStateController::class, 'pause'])
            ->name('plans.items.pause');

        Route::post('/plans/{planId}/items/{itemId}/stop', [PlannerItemStateController::class, 'stop'])
            ->name('plans.items.stop');

        /*
        |--------------------------------------------------------------------------
        | Planner item steps
        |--------------------------------------------------------------------------
        | Final names:
        | planner.plans.items.steps.store
        | planner.plans.items.steps.update
        | planner.plans.items.steps.destroy
        |--------------------------------------------------------------------------
        */
        Route::post('/plans/{plan}/items/{item}/steps', [PlannerPlanController::class, 'storeItemStep'])
            ->name('plans.items.steps.store');

        Route::patch('/plans/{plan}/items/{item}/steps/{step}', [PlannerPlanController::class, 'updateItemStep'])
            ->name('plans.items.steps.update');

        Route::delete('/plans/{plan}/items/{item}/steps/{step}', [PlannerPlanController::class, 'destroyItemStep'])
            ->name('plans.items.steps.destroy');

        /*
        |--------------------------------------------------------------------------
        | Planner item materials
        |--------------------------------------------------------------------------
        | Final names:
        | planner.plans.items.materials.sources
        | planner.plans.items.materials.importDeal
        | planner.plans.items.materials.products
        | planner.plans.items.materials.store
        | planner.plans.items.materials.update
        | planner.plans.items.materials.destroy
        |--------------------------------------------------------------------------
        */
        Route::get('/plans/{plan}/items/{item}/materials/sources', [PlannerPlanController::class, 'materialSources'])
            ->name('plans.items.materials.sources');

        Route::post('/plans/{plan}/items/{item}/materials/import-deal', [PlannerPlanController::class, 'importDealMaterials'])
            ->name('plans.items.materials.importDeal');

        Route::get('/plans/{plan}/items/{item}/materials/products', [PlannerPlanController::class, 'searchPlannerMaterialProducts'])
            ->name('plans.items.materials.products');

        Route::post('/plans/{plan}/items/{item}/materials', [PlannerPlanController::class, 'storeItemMaterial'])
            ->name('plans.items.materials.store');

        Route::patch('/plans/{plan}/items/{item}/materials/{material}', [PlannerPlanController::class, 'updateItemMaterial'])
            ->name('plans.items.materials.update');

        Route::delete('/plans/{plan}/items/{item}/materials/{material}', [PlannerPlanController::class, 'destroyItemMaterial'])
            ->name('plans.items.materials.destroy');
        Route::post('/plans/{plan}/group-materials', [PlannerPlanController::class, 'storePlanGroupMaterial'])
            ->name('plans.group_materials.store');

        Route::get('/plans/{plan}/attendance/day', [PlannerAttendanceController::class, 'day'])->name('plans.attendance.day');
        Route::get('/plans/{plan}/attendance/report', [PlannerAttendanceController::class, 'report'])->name('plans.attendance.report');

        Route::post('/plans/{plan}/attendance/check-in', [PlannerAttendanceController::class, 'checkIn'])->name('plans.attendance.check_in');
        Route::post('/plans/{plan}/attendance/check-out', [PlannerAttendanceController::class, 'checkOut'])->name('plans.attendance.check_out');
        Route::post('/plans/{plan}/attendance/travel-start', [PlannerAttendanceController::class, 'travelStart'])->name('plans.attendance.travel_start');
        Route::post('/plans/{plan}/attendance/location', [PlannerAttendanceController::class, 'location'])->name('plans.attendance.location');
        Route::post('/plans/{plan}/attendance/arrived', [PlannerAttendanceController::class, 'arrived'])->name('plans.attendance.arrived');
        Route::post('/plans/{plan}/attendance/work-start', [PlannerAttendanceController::class, 'workStart'])->name('plans.attendance.work_start');
        Route::post('/plans/{plan}/attendance/work-end', [PlannerAttendanceController::class, 'workEnd'])->name('plans.attendance.work_end');
        Route::post('/plans/{plan}/attendance/pause-start', [PlannerAttendanceController::class, 'pauseStart'])->name('plans.attendance.pause_start');
        Route::post('/plans/{plan}/attendance/pause-end', [PlannerAttendanceController::class, 'pauseEnd'])->name('plans.attendance.pause_end');
        Route::patch('/plans/{plan}/items/{item}/status', [PlannerPlanController::class, 'updateItemStatus'])
            ->name('plans.items.status.update');

        Route::patch('/plans/{plan}/items/{item}/material-requests/{materialRequest}/status', [PlannerPlanController::class, 'updateItemMaterialRequestStatus'])
            ->whereNumber('plan')
            ->whereNumber('item')
            ->whereNumber('materialRequest')
            ->name('plans.items.material_requests.status');

    });


 

Route::middleware(['auth'])
        ->prefix('admin/task-wizard')
        ->name('taskWizard.')
        ->group(function () {

            Route::post('/apply', [TaskWizardController::class, 'apply'])
                ->name('apply'); 
            // Optional helpers for async select/search (if you want AJAX search inputs):
            Route::post('/wizard/activity/at', [TaskWizardController::class, 'storeActivityAt'])->name('wizard.activity.at');
            Route::get('/lookup/products', [TaskWizardController::class, 'lookupProducts'])->name('lookup.products');
            Route::get('/lookup/stages', [TaskWizardController::class, 'lookupStages'])->name('lookup.stages');
            Route::get('/lookup/sections', [TaskWizardController::class, 'lookupSections'])->name('lookup.sections');
        });


    


  
    