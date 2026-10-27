<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dashboard" />
    </x-slot>

    @php
        $activeTab = request('tab', 'my-overview');
        $tabs = [
            'my-overview' => [
                'label' => 'My Overview',
                'permission' => 'view my overview',
            ],
            'project-overview' => [
                'label' => 'Project Overview',
                'permission' => 'view project overview',
            ],
            'task-overview' => [
                'label' => 'Task Overview',
                'permission' => 'view task overview',
            ],
            'organization-overview' => [
                'label' => 'Organization Overview',
                'permission' => 'view organization overview',
            ],
        ];

        foreach ($tabs as $key => $tab) {
            if (auth()->user()->can($tab['permission'])) {
                if ($activeTab === $key || ! auth()->user()->can($tabs[$activeTab]['permission'] ?? '')) {
                    $activeTab = $key;
                    break;
                }
            }
        }
    @endphp

    <div class="container py-4">
        <div id="successMessage" class="alert alert-success d-none"></div>

        <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
            @foreach ($tabs as $key => $tab)
                @can($tab['permission'])
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === $key ? 'active' : '' }} fw-semibold" id="tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#{{ $key }}" type="button" role="tab" aria-controls="{{ $key }}" aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}">
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endcan
            @endforeach
        </ul>

        <div class="tab-content" id="dashboardTabContent">
                            @can('view my overview')
                                <div class="tab-pane fade {{ $activeTab === 'my-overview' ? 'show active' : '' }}" id="my-overview" role="tabpanel" aria-labelledby="tab-my-overview">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="card text-white bg-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Total Tasks</h6>
                                                    <h3 class="mb-0" id="stat-my-tasks">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Pending</h6>
                                                    <h3 class="mb-0" id="stat-my-pending">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">In Progress</h6>
                                                    <h3 class="mb-0" id="stat-my-inprogress">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Completed</h6>
                                                    <h3 class="mb-0" id="stat-my-completed">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks by Status
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks Created per Month (Last 6 Months)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-monthly" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks by Category
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-category" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Overdue vs On Time
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-overdue" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    My Projects by Status
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-proj-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    My Projects Created per Month
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-proj-monthly" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    My Tickets by Status
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-ticket-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    My Tickets Created per Month
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-ticket-monthly" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    My Tickets by Priority
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-my-ticket-priority" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan

                            @can('view project overview')
                                <div class="tab-pane fade {{ $activeTab === 'project-overview' ? 'show active' : '' }}" id="project-overview" role="tabpanel" aria-labelledby="tab-project-overview">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="card text-white bg-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Total Projects</h6>
                                                    <h3 class="mb-0" id="stat-proj-total">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Active</h6>
                                                    <h3 class="mb-0" id="stat-proj-active">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Completed</h6>
                                                    <h3 class="mb-0" id="stat-proj-completed">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-secondary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Inactive</h6>
                                                    <h3 class="mb-0" id="stat-proj-inactive">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Projects by Status
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-proj-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Projects Created per Month (Last 6 Months)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-proj-monthly" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Projects by Owner (Top 10)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-proj-owner" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Avg Project Duration by Status (Days)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-proj-duration" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan

                            @can('view task overview')
                                <div class="tab-pane fade {{ $activeTab === 'task-overview' ? 'show active' : '' }}" id="task-overview" role="tabpanel" aria-labelledby="tab-task-overview">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="card text-white bg-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Total Tasks</h6>
                                                    <h3 class="mb-0" id="stat-task-total">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Pending</h6>
                                                    <h3 class="mb-0" id="stat-task-pending">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">In Progress</h6>
                                                    <h3 class="mb-0" id="stat-task-inprogress">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Completed</h6>
                                                    <h3 class="mb-0" id="stat-task-completed">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks by Status
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-task-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks Created per Month (Last 6 Months)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-task-monthly" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks by Category
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-task-category" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Tasks by Assignee (Top 10)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-task-assignee" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Overdue Tasks Trend (Last 6 Months)
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-task-overdue-trend" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan

                            @can('view organization overview')
                                <div class="tab-pane fade {{ $activeTab === 'organization-overview' ? 'show active' : '' }}" id="organization-overview" role="tabpanel" aria-labelledby="tab-organization-overview">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="card text-white bg-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Companies</h6>
                                                    <h3 class="mb-0" id="stat-org-companies">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Departments</h6>
                                                    <h3 class="mb-0" id="stat-org-departments">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Groups</h6>
                                                    <h3 class="mb-0" id="stat-org-groups">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-dark">
                                                <div class="card-body">
                                                    <h6 class="card-title text-opacity-75">Users</h6>
                                                    <h3 class="mb-0" id="stat-org-users">0</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Users per Company
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-org-company" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Departments per Company
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-org-department" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Users per Group
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-org-group" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Users per Department
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-org-users-dept" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-header fw-semibold bg-light">
                                                    Active vs Inactive Users
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chart-org-user-status" height="300"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @if (collect($tabs)->filter(fn ($t) => auth()->user()->can($t['permission']))->isEmpty())
                            <div class="alert alert-warning">
                                You do not have permission to view any dashboard overviews. Please contact your administrator.
                            </div>
                        @endif

                    </div>
                </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartInstances = {};
            const loadedTabs = new Set();

            const tabDataUrls = {
                'my-overview': '{{ route("dashboard.data.my-overview") }}',
                'project-overview': '{{ route("dashboard.data.project-overview") }}',
                'task-overview': '{{ route("dashboard.data.task-overview") }}',
                'organization-overview': '{{ route("dashboard.data.organization-overview") }}',
            };

            const tabStatIds = {
                'my-overview': ['stat-my-tasks', 'stat-my-pending', 'stat-my-inprogress', 'stat-my-completed'],
                'project-overview': ['stat-proj-total', 'stat-proj-active', 'stat-proj-completed', 'stat-proj-inactive'],
                'task-overview': ['stat-task-total', 'stat-task-pending', 'stat-task-inprogress', 'stat-task-completed'],
                'organization-overview': ['stat-org-companies', 'stat-org-departments', 'stat-org-groups', 'stat-org-users'],
            };

            function updateStats(tabName, totals) {
                const ids = tabStatIds[tabName];
                if (!ids || !totals) return;

                const keys = Object.keys(totals);
                keys.forEach(function (key, index) {
                    const el = document.getElementById(ids[index]);
                    if (el) {
                        el.textContent = totals[key];
                    }
                });
            }

            function destroyChart(tabName) {
                if (chartInstances[tabName]) {
                    chartInstances[tabName].destroy();
                    chartInstances[tabName] = null;
                }
            }

            function createChart(tabName, ctx, options) {
                destroyChart(tabName);
                chartInstances[tabName] = new Chart(ctx, options);
            }

            function renderCharts(tabName, data) {
                switch (tabName) {
                    case 'my-overview':
                        createChart(tabName, document.getElementById('chart-my-status').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.tasks.status.labels,
                                datasets: data.tasks.status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tasks by Status' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        createChart(tabName + '-monthly', document.getElementById('chart-my-monthly').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.tasks.monthly.labels,
                                datasets: data.tasks.monthly.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tasks Created per Month' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-category', document.getElementById('chart-my-category').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.tasks.category.labels,
                                datasets: data.tasks.category.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tasks by Category' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-overdue', document.getElementById('chart-my-overdue').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.tasks.overdue.labels,
                                datasets: data.tasks.overdue.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Overdue vs On Time' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        // Projects
                        createChart(tabName + '-proj-status', document.getElementById('chart-my-proj-status').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.projects.status.labels,
                                datasets: data.projects.status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Projects by Status' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        createChart(tabName + '-proj-monthly', document.getElementById('chart-my-proj-monthly').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.projects.monthly.labels,
                                datasets: data.projects.monthly.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Projects Created per Month' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        // Tickets
                        createChart(tabName + '-ticket-status', document.getElementById('chart-my-ticket-status').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.tickets.status.labels,
                                datasets: data.tickets.status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tickets by Status' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        createChart(tabName + '-ticket-monthly', document.getElementById('chart-my-ticket-monthly').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.tickets.monthly.labels,
                                datasets: data.tickets.monthly.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tickets Created per Month' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-ticket-priority', document.getElementById('chart-my-ticket-priority').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.tickets.priority.labels,
                                datasets: data.tickets.priority.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'My Tickets by Priority' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        break;

                    case 'project-overview':
                        createChart(tabName, document.getElementById('chart-proj-status').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.status.labels,
                                datasets: data.status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Projects by Status' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-monthly', document.getElementById('chart-proj-monthly').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.monthly.labels,
                                datasets: data.monthly.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Projects Created per Month' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-owner', document.getElementById('chart-proj-owner').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.owner.labels,
                                datasets: data.owner.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Projects by Owner (Top 10)' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-duration', document.getElementById('chart-proj-duration').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.duration.labels,
                                datasets: data.duration.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Avg Project Duration by Status (Days)' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true },
                                },
                            },
                        });
                        break;

                    case 'task-overview':
                        createChart(tabName, document.getElementById('chart-task-status').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.status.labels,
                                datasets: data.status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Tasks by Status' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-monthly', document.getElementById('chart-task-monthly').getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: data.monthly.labels,
                                datasets: data.monthly.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Tasks Created per Month' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-category', document.getElementById('chart-task-category').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.category.labels,
                                datasets: data.category.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Tasks by Category' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-assignee', document.getElementById('chart-task-assignee').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.assignee.labels,
                                datasets: data.assignee.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Tasks by Assignee (Top 10)' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-overdue-trend', document.getElementById('chart-task-overdue-trend').getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: data.overdue_trend.labels,
                                datasets: data.overdue_trend.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Overdue Tasks Trend' },
                                    legend: { display: false },
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        break;

                    case 'organization-overview':
                        createChart(tabName, document.getElementById('chart-org-company').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.company.labels,
                                datasets: data.company.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Users per Company' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-department', document.getElementById('chart-org-department').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.department.labels,
                                datasets: data.department.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Departments per Company' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-group', document.getElementById('chart-org-group').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.group.labels,
                                datasets: data.group.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Users per Group' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-users-dept', document.getElementById('chart-org-users-dept').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.users_dept.labels,
                                datasets: data.users_dept.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    title: { display: true, text: 'Users per Department' },
                                    legend: { display: false },
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                                },
                            },
                        });
                        createChart(tabName + '-user-status', document.getElementById('chart-org-user-status').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.user_status.labels,
                                datasets: data.user_status.datasets,
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: { display: true, text: 'Active vs Inactive Users' },
                                    legend: { position: 'bottom' },
                                },
                            },
                        });
                        break;

                }
            }

            function loadTab(tabName) {
                if (loadedTabs.has(tabName)) return;
                const url = tabDataUrls[tabName];
                if (!url) return;

                fetch(url)
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Unable to load data (HTTP ' + response.status + ')');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        loadedTabs.add(tabName);
                        renderCharts(tabName, data);
                        updateStats(tabName, data.totals);
                    })
                    .catch(function (error) {
                        console.error('Dashboard data error:', error);
                        alert('Unable to load dashboard data. You may not have permission to view this section.');
                    });
            }

            const tabList = document.getElementById('dashboardTabs');
            if (tabList) {
                tabList.addEventListener('shown.bs.tab', function (event) {
                    const target = event.target.getAttribute('data-bs-target').replace('#', '');
                    loadTab(target);
                });

                const activeTrigger = tabList.querySelector('.nav-link.active');
                if (activeTrigger) {
                    const target = activeTrigger.getAttribute('data-bs-target').replace('#', '');
                    loadTab(target);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>