<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view dashboard', only: ['index']),
            new Middleware('permission:view my overview', only: ['myOverviewData']),
            new Middleware('permission:view project overview', only: ['projectOverviewData']),
            new Middleware('permission:view task overview', only: ['taskOverviewData']),
            new Middleware('permission:view organization overview', only: ['organizationOverviewData']),
        ];
    }

    public function index()
    {
        return view('dashboard');
    }

    public function myOverviewData()
    {
        $userId = auth()->id();

        // own task
        $taskStatusRows = Task::getPersonalStatusCounts($userId);
        $taskMonthRows = Task::getPersonalMonthlyCounts($userId);
        $taskCategoryRows = Task::getPersonalCategoryCounts($userId);
        $taskOverdueCount = Task::getPersonalOverdueCount($userId);
        $taskOnTimeCount = Task::getPersonalOnTimeCount($userId);
        $taskTotals = Task::getPersonalTotals($userId);

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $row = $taskMonthRows->firstWhere('month', $month);
            $data[] = $row ? (int) $row->total : 0;
        }

        $statusLabels = [];
        $statusData = [];
        $statusColors = [
            'pending' => '#ffc107',
            'in_progress' => '#0d6efd',
            'completed' => '#198754',
        ];
        foreach ($taskStatusRows as $row) {
            $statusLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $statusData[] = (int) $row->total;
        }

        $categoryLabels = [];
        $categoryData = [];
        foreach ($taskCategoryRows as $row) {
            $categoryLabels[] = $row->name;
            $categoryData[] = (int) $row->total;
        }

        // own project ticket
        $projStatusRows = Project::getPersonalStatusCounts($userId);
        $projMonthRows = Project::getPersonalMonthlyCounts($userId);
        $projTotals = Project::getPersonalTotals($userId);

        $projStatusLabels = [];
        $projStatusData = [];
        foreach ($projStatusRows as $row) {
            $projStatusLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $projStatusData[] = (int) $row->total;
        }

        $projLabels = [];
        $projData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $projLabels[] = now()->subMonths($i)->format('M Y');
            $row = $projMonthRows->firstWhere('month', $month);
            $projData[] = $row ? (int) $row->total : 0;
        }

        // own user tickets
        $ticketStatusRows = Ticket::getPersonalStatusCounts($userId);
        $ticketMonthRows = Ticket::getPersonalMonthlyCounts($userId);
        $ticketPriorityRows = Ticket::getPersonalPriorityCounts($userId);
        $ticketTotals = Ticket::getPersonalTotals($userId);

        $ticketStatusLabels = [];
        $ticketStatusData = [];
        foreach ($ticketStatusRows as $row) {
            $ticketStatusLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $ticketStatusData[] = (int) $row->total;
        }

        $ticketLabels = [];
        $ticketData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $ticketLabels[] = now()->subMonths($i)->format('M Y');
            $row = $ticketMonthRows->firstWhere('month', $month);
            $ticketData[] = $row ? (int) $row->total : 0;
        }

        $ticketPriorityLabels = [];
        $ticketPriorityData = [];
        foreach ($ticketPriorityRows as $row) {
            $ticketPriorityLabels[] = ucfirst($row->priority);
            $ticketPriorityData[] = (int) $row->total;
        }

        return response()->json([
            'tasks' => [
                'status' => [
                    'labels' => $statusLabels,
                    'datasets' => [[
                        'data' => $statusData,
                        'backgroundColor' => array_map(fn ($label) => $statusColors[strtolower(str_replace(' ', '_', $label))] ?? '#6c757d', $statusLabels),
                    ]],
                ],
                'monthly' => [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => 'Tasks Created',
                        'data' => $data,
                        'backgroundColor' => '#0d6efd',
                    ]],
                ],
                'category' => [
                    'labels' => $categoryLabels,
                    'datasets' => [[
                        'data' => $categoryData,
                        'backgroundColor' => '#6c757d',
                    ]],
                ],
                'overdue' => [
                    'labels' => ['Overdue', 'On Time'],
                    'datasets' => [[
                        'data' => [(int) $taskOverdueCount, (int) $taskOnTimeCount],
                        'backgroundColor' => ['#dc3545', '#198754'],
                    ]],
                ],
                'totals' => $taskTotals,
            ],
            'projects' => [
                'status' => [
                    'labels' => $projStatusLabels,
                    'datasets' => [[
                        'data' => $projStatusData,
                        'backgroundColor' => '#0d6efd',
                    ]],
                ],
                'monthly' => [
                    'labels' => $projLabels,
                    'datasets' => [[
                        'label' => 'Projects Created',
                        'data' => $projData,
                        'backgroundColor' => '#0d6efd',
                    ]],
                ],
                'totals' => $projTotals,
            ],
            'tickets' => [
                'status' => [
                    'labels' => $ticketStatusLabels,
                    'datasets' => [[
                        'data' => $ticketStatusData,
                        'backgroundColor' => '#6c757d',
                    ]],
                ],
                'monthly' => [
                    'labels' => $ticketLabels,
                    'datasets' => [[
                        'label' => 'Tickets Created',
                        'data' => $ticketData,
                        'backgroundColor' => '#dc3545',
                    ]],
                ],
                'priority' => [
                    'labels' => $ticketPriorityLabels,
                    'datasets' => [[
                        'data' => $ticketPriorityData,
                        'backgroundColor' => '#ffc107',
                    ]],
                ],
                'totals' => $ticketTotals,
            ],
            'totals' => $taskTotals,
        ]);
    }

    public function projectOverviewData()
    {
        $statusRows = Project::getStatusCounts();
        $monthRows = Project::getMonthlyCounts();

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $row = $monthRows->firstWhere('month', $month);
            $data[] = $row ? (int) $row->total : 0;
        }

        $statusLabels = [];
        $statusData = [];
        foreach ($statusRows as $row) {
            $statusLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $statusData[] = (int) $row->total;
        }

        // Projects by owner
        $ownerRows = Project::getOwnerCounts();

        $ownerLabels = [];
        $ownerData = [];
        foreach ($ownerRows as $row) {
            $ownerLabels[] = trim($row->first_name . ' ' . $row->last_name);
            $ownerData[] = (int) $row->total;
        }

        $durationRows = Project::getAvgDurationByStatus();

        $durationLabels = [];
        $durationData = [];
        foreach ($durationRows as $row) {
            $durationLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $durationData[] = round((float) $row->avg_days, 1);
        }

        return response()->json([
            'status' => [
                'labels' => $statusLabels,
                'datasets' => [[
                    'data' => $statusData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'monthly' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Projects Created',
                    'data' => $data,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'owner' => [
                'labels' => $ownerLabels,
                'datasets' => [[
                    'data' => $ownerData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'duration' => [
                'labels' => $durationLabels,
                'datasets' => [[
                    'data' => $durationData,
                    'backgroundColor' => '#6c757d',
                ]],
            ],
            'totals' => Project::getTotals(),
        ]);
    }

    public function taskOverviewData()
    {
        $statusRows = Task::getStatusCounts();
        $monthRows = Task::getMonthlyCounts();

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $row = $monthRows->firstWhere('month', $month);
            $data[] = $row ? (int) $row->total : 0;
        }

        $categoryRows = Task::getCategoryCounts();

        $statusLabels = [];
        $statusData = [];
        foreach ($statusRows as $row) {
            $statusLabels[] = ucfirst(str_replace('_', ' ', $row->status));
            $statusData[] = (int) $row->total;
        }

        $categoryLabels = [];
        $categoryData = [];
        foreach ($categoryRows as $row) {
            $categoryLabels[] = $row->name;
            $categoryData[] = (int) $row->total;
        }

        // Tasks by assignee (top 10)
        $assigneeRows = Task::getAssigneeCounts();

        $assigneeLabels = [];
        $assigneeData = [];
        foreach ($assigneeRows as $row) {
            $assigneeLabels[] = trim($row->first_name . ' ' . $row->last_name);
            $assigneeData[] = (int) $row->total;
        }

        $overdueMonthRows = Task::getOverdueTrend();

        $overdueLabels = [];
        $overdueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $overdueLabels[] = now()->subMonths($i)->format('M Y');
            $row = $overdueMonthRows->firstWhere('month', $month);
            $overdueData[] = $row ? (int) $row->total : 0;
        }

        return response()->json([
            'status' => [
                'labels' => $statusLabels,
                'datasets' => [[
                    'data' => $statusData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'monthly' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Tasks Created',
                    'data' => $data,
                    'borderColor' => '#0d6efd',
                    'fill' => false,
                    'tension' => 0.4,
                ]],
            ],
            'category' => [
                'labels' => $categoryLabels,
                'datasets' => [[
                    'data' => $categoryData,
                    'backgroundColor' => '#6c757d',
                ]],
            ],
            'assignee' => [
                'labels' => $assigneeLabels,
                'datasets' => [[
                    'data' => $assigneeData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'overdue_trend' => [
                'labels' => $overdueLabels,
                'datasets' => [[
                    'label' => 'Overdue Tasks',
                    'data' => $overdueData,
                    'borderColor' => '#dc3545',
                    'fill' => false,
                    'tension' => 0.4,
                ]],
            ],
            'totals' => Task::getTotals(),
        ]);
    }

    public function organizationOverviewData()
    {
        $companyRows = Company::getUserCounts();

        $companyLabels = [];
        $companyData = [];
        foreach ($companyRows as $row) {
            $companyLabels[] = $row->company_name;
            $companyData[] = (int) $row->total;
        }

        $deptRows = Department::getDeptCounts();

        $deptLabels = [];
        $deptData = [];
        foreach ($deptRows as $row) {
            $deptLabels[] = $row->company_name . ' - ' . $row->department_name;
            $deptData[] = (int) $row->total;
        }

        $groupRows = Group::getGroupUserCounts();

        $groupLabels = [];
        $groupData = [];
        foreach ($groupRows as $row) {
            $groupLabels[] = $row->company_name . ' / ' . $row->department_name . ' / ' . $row->group_name;
            $groupData[] = (int) $row->total;
        }

        // Users per department
        $usersDeptRows = User::getUsersPerDept();

        $usersDeptLabels = [];
        $usersDeptData = [];
        foreach ($usersDeptRows as $row) {
            $usersDeptLabels[] = $row->department_name;
            $usersDeptData[] = (int) $row->total;
        }

        $activeUsers = User::getActiveCount();
        $inactiveUsers = User::getInactiveCount();

        return response()->json([
            'company' => [
                'labels' => $companyLabels,
                'datasets' => [[
                    'data' => $companyData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'department' => [
                'labels' => $deptLabels,
                'datasets' => [[
                    'data' => $deptData,
                    'backgroundColor' => '#6c757d',
                ]],
            ],
            'group' => [
                'labels' => $groupLabels,
                'datasets' => [[
                    'data' => $groupData,
                    'backgroundColor' => '#198754',
                ]],
            ],
            'users_dept' => [
                'labels' => $usersDeptLabels,
                'datasets' => [[
                    'data' => $usersDeptData,
                    'backgroundColor' => '#0d6efd',
                ]],
            ],
            'user_status' => [
                'labels' => ['Active', 'Inactive'],
                'datasets' => [[
                    'data' => [(int) $activeUsers, (int) $inactiveUsers],
                    'backgroundColor' => ['#198754', '#dc3545'],
                ]],
            ],
            'totals' => [
                'companies' => Company::getCount(),
                'departments' => Department::getCount(),
                'groups' => Group::getCount(),
                'users' => User::getCount(),
            ],
        ]);
    }
}