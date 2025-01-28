<?php

namespace Modules\Recruitment\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Recruitment';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('Recruitment Dashboard'),
            'icon' => '',
            'name' => 'recruitment-dashboard',
            'parent' => 'dashboard',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'recruitment.dashboard',
            'module' => $module,
            'permission' => 'recruitment dashboard manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Recruitment'),
            'icon' => 'user-plus',
            'name' => 'recruitment',
            'parent' => '',
            'order' => 453,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'recruitment manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies'),
            'icon' => '',
            'name' => 'vacancies',
            'parent' => 'recruitment',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job.index',
            'module' => $module,
            'permission' => 'job manage'
        ]);/*
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies Create'),
            'icon' => '',
            'name' => 'vacancies-create',
            'parent' => 'recruitment',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job.create',
            'module' => $module,
            'permission' => 'job create'
        ]);*/
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies Application'),
            'icon' => '',
            'name' => 'job-application',
            'parent' => 'recruitment',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job-application.index',
            'module' => $module,
            'permission' => 'jobapplication manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies Archived'),
            'icon' => '',
            'name' => 'job-archived',
            'parent' => 'recruitment',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job.application.archived',
            'module' => $module,
            'permission' => 'jobapplication archived manage'
        ]);/*
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies Candidate'),
            'icon' => '',
            'name' => 'job-candidate',
            'parent' => 'recruitment',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job-candidates.index',
            'module' => $module,
            'permission' => 'jobapplication candidate manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancies On-boarding'),
            'icon' => '',
            'name' => 'job-on-boarding',
            'parent' => 'recruitment',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job.on.board',
            'module' => $module,
            'permission' => 'jobonboard manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Custom Question'),
            'icon' => '',
            'name' => 'custom-question',
            'parent' => 'recruitment',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'custom-question.index',
            'module' => $module,
            'permission' => 'custom question manage'
        ]);*/
        $menu->add([
            'category' => 'HR',
            'title' => __('Interview Schedule'),
            'icon' => '',
            'name' => 'interview-schedule',
            'parent' => 'recruitment',
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'interview-schedule.index',
            'module' => $module,
            'permission' => 'interview schedule manage'
        ]);        
        $menu->add([
            'category' => 'HR',
            'title' => __('Vacancy Announcement'),
            'icon' => '',
            'name' => 'Vacancy Announcement',
            'parent' => 'recruitment',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'career',
            'module' => $module,
            'permission' => 'career manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Job Configuration'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'recruitment',
            'order' => 55,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'job-category.index',
            'module' => $module,
            'permission' => 'branch manage'
        ]);
    }
}
