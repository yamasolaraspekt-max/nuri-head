<div class="pp-card-top">
    <div>
        <div class="pp-card-title" x-text="project.customer.name"></div>
        <div class="pp-card-sub">
            <span x-text="project.customer.no || ''"></span>
            <span x-show="project.customer.no"> · </span>
            <span x-text="project.object.name"></span>
        </div>
        <div class="pp-card-sub" x-text="project.object.address"></div>
    </div>

    <span class="pp-badge" :style="`background:${project.stage.color}22;color:${project.stage.color}`">
        <span x-text="project.stage.name"></span>
    </span>
</div>

<div class="pp-product-row">
    <template x-if="project.product.image">
        <img class="pp-product-img" :src="project.product.image">
    </template>

    <template x-if="!project.product.image">
        <div class="pp-product-img"></div>
    </template>

    <div>
        <div style="font-weight:900;" x-text="project.product.name"></div>
        <div class="pp-card-sub" x-text="project.product.service"></div>
    </div>
</div>

<div style="margin-top:10px;">
    <span class="pp-badge pp-badge-green" x-text="project.stage.sub_stage_name || 'Keine Unterphase'"></span>
</div>

<div class="pp-progress">
    <div class="pp-progress-info">
        <span>Fortschritt</span>
        <span x-text="project.progress + '%'"></span>
    </div>
    <div class="pp-progress-track">
        <div class="pp-progress-bar" :style="`width:${project.progress}%`"></div>
    </div>
</div>

<div class="pp-counts">
    <div class="pp-count">
        <strong x-text="project.counts.appointments"></strong>
        <span>Termine</span>
    </div>
    <div class="pp-count">
        <strong x-text="project.counts.personal_tasks"></strong>
        <span>Tasks</span>
    </div>
    <div class="pp-count">
        <strong x-text="project.counts.tickets"></strong>
        <span>Tickets</span>
    </div>
    <div class="pp-count">
        <strong x-text="project.counts.kanban_tasks"></strong>
        <span>Kanban</span>
    </div>
    <div class="pp-count">
        <strong x-text="project.counts.planner_items"></strong>
        <span>Plan</span>
    </div>
</div>

<div class="pp-team">
    <template x-for="emp in project.team.slice(0,6)" :key="emp.id">
        <template x-if="emp.image">
            <img class="pp-avatar" :src="emp.image" :title="emp.name">
        </template>
    </template>

    <template x-for="emp in project.team.filter(e => !e.image).slice(0,3)" :key="'t'+emp.id">
        <div class="pp-avatar-text" :title="emp.name" x-text="emp.name.substring(0,1)"></div>
    </template>

    <template x-if="project.team.length === 0">
        <span class="text-muted small">Kein Team zugewiesen</span>
    </template>
</div>

<div class="pp-actions">
    <button class="pp-btn" type="button">
        <i class="fa fa-eye"></i>
        Details
    </button>

    <button class="pp-btn pp-btn-primary" type="button" @click="openPlan(project)">
        <i class="fa fa-calendar-check"></i>
        Plan öffnen
    </button>
</div>