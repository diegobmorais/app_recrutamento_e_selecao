@extends('layouts.main')
@section('page-title')
    {{ __('Job Details') }}
@endsection
@section('page-action')
    <div>
        @permission('job edit')
            <a href="{{ route('job.edit', $job->id) }}" data-size="md" data-title="{{ __('Edit Job') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-info" data-bs-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('page-breadcrumb')
    {{ __('Manage Job') }},
    {{ __('Job Details') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table ">
                            <tbody>
                                <tr>
                                    <td>{{ __('Job Title') }}</td>
                                    <td class="">{{ $job->title }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Location') }}</td>
                                    <td class="">{{ !empty($job->location) ? $job->location : $job->location }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Job Category') }}</td>
                                    <td class="">{{ !empty($job->categories) ? $job->categories->title : '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Positions') }}</td>
                                    <td class="">{{ $job->position }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Status') }}</td>
                                    <td class="">
                                        @if ($job->status == 'active')
                                            <span
                                                class="p-2 px-3 rounded badge bg-success">{{ \Modules\Recruitment\Entities\Job::$status[$job->status] }}</span>
                                        @else
                                            <span
                                                class="p-2 px-3 rounded badge bg-danger">{{ \Modules\Recruitment\Entities\Job::$status[$job->status] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>                      
                                    <td>{{ __('Remuneração') }}</td>
                                    <td class="">{{ currency_format_with_sym($job->remuneration) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Beneficios') }}</td>
                                    <td class="">{{ $job->benefits}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Nota mínimina de aprovação') }}</td>
                                    <td class="">{{ $job->average}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Created Date') }}</td>
                                    <td class="">{{ company_date_formate($job->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Start Date') }}</td>
                                    <td class="">{{ company_date_formate($job->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('End Date') }}</td>
                                    <td class="">{{ company_date_formate($job->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Skill') }}</td>
                                    <td class="">
                                        @foreach ($job->skill as $skill)
                                            <span class="p-2 px-3 rounded badge bg-primary">{{ $skill }}</span>
                                        @endforeach
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-fluid">
                <div class="card-body">
                    <div class="col-12">
                        <div class="row">

                            @if ($job->applicant)
                                <div class="col-6">
                                    <h6>{{ __('Need to ask ?') }}</h6>
                                    <ul class="">
                                        @foreach ($job->applicant as $applicant)
                                            <li>{{ ucfirst($applicant) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!empty($job->visibility))
                                <div class="col-6">
                                    <h6>{{ __('Need to show option ?') }}</h6>
                                    <ul class="">
                                        @foreach ($job->visibility as $visibility)
                                            <li>{{ ucfirst($visibility) }}</li>
                                        @endforeach
                                    </ul>

                                </div>
                            @endif

                            @if (count($job->questions()) > 0)
                                <div class="col-12">
                                    <h6>{{ __('Custom Question') }}</h6>
                                    <ul class="">
                                        @foreach ($job->questions() as $question)
                                            <li>{{ $question->question }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="row ">
                            <div class="col-12 mt-3">
                                <h6>{{ __('Job Description') }}</h6>
                                {!! $job->description !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-3">
                                <h6>{{ __('Job Requirement') }}</h6>
                                {!! $job->requirement !!}
                            </div>
                        </div>
                        @if (!empty($job->terms_and_conditions))
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <h6>{{ __('Terms And Conditions') }}</h6>
                                    {!! $job->terms_and_conditions !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
