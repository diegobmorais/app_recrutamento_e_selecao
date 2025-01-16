@extends('recruitment::layouts.master')
@section('page-title')
    {{ $job->title }}
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('assets/fonts/movies/movie-player.min.css') }}">
@endpush
@section('content')
    <div class="job-wrapper">
        <div class="job-content">
            <nav class="navbar">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <img src="{{ !empty(get_file(company_setting('logo_light', $job->created_by, $job->workspace))) ? get_file('uploads/logo/logo_light.png') : 'WorkDo' }}"
                            alt="logo" style="width: 90px">
                    </a>
                    <li class="dropdown dash-h-item drp-language">
                        <div class="dropdown global-icon" data-toggle="tooltip"
                            data-original-titla="{{ __('Choose Language') }}">
                            <a class="nav-link px-0  btn bg-white px-3 py-2" role="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false" data-offset="0,10">
                                <span class="d-none d-lg-inline-block">{{ \Str::upper($currantLang) }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                style="min-width: auto">
                                @foreach ($languages as $key => $language)
                                    <a class="dropdown-item @if ($key == $currantLang) text-danger @endif"
                                        href="{{ route('job.apply', [$job->code, $key]) }}">{{ $language }}</a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                </div>
            </nav>
            <section class="job-banner">
                <div class="job-banner-bg">
                    <img src="{{ asset('Modules/Recruitment/Resources/assets/image/banner.png') }}" alt="">
                </div>
                <div class="container">
                    <div class="job-banner-content text-center text-white">
                        <h1 class="text-white mb-3">
                            {{ __(' We help') }} <br> {{ __('businesses grow') }}
                        </h1>
                        <p>{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>
                        </p>
                    </div>
                </div>
            </section>
            <section class="apply-job-section">
                <div class="container">
                    <div class="apply-job-wrapper bg-light">
                        <div class="section-title text-center">
                            <h2 class="h1 mb-3"> {{ $job->title }}</h2>
                            <div class="d-flex flex-wrap justify-content-center gap-1 mb-4">
                                @foreach (explode(',', $job->skill) as $skill)
                                    <span class="badge rounded p-2 bg-primary">{{ $skill }}</span>
                                @endforeach
                            </div>
                            @if (!empty($job->branches) ? $job->branches->name : $job->location)
                                <p> <i class="ti ti-map-pin ms-1"></i>
                                    {{ !empty($job->branches) ? $job->branches->name : $job->location }}</p>
                            @endif
                        </div>
                        <div class="apply-job-form">
                            <h2 class="mb-4">{{ __('Apply for this job') }}</h2>
                            {{ Form::open(['route' => ['job.apply.data', $job->code], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('name', null, ['class' => 'form-control name', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('email', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('phone', __('Phone'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('phone', null, ['class' => 'form-control', 'required' => 'required']) }}
                                        <div class=" text-xs text-danger">
                                            {{ __('Please add mobile number with country code. (ex. +91)') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if (!empty($job->applicant) && in_array('dob', explode(',', $job->applicant)))
                                        <div class="form-group">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                            {!! Form::date('dob', old('dob'), ['class' => 'form-control datepicker w-100', 'required' => 'required']) !!}
                                        </div>
                                    @endif
                                </div>
                                @if (!empty($job->applicant) && in_array('gender', explode(',', $job->applicant)))
                                    <div class="form-group col-md-6 ">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        <div class="d-flex radio-check">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="g_male" value="Male" name="gender"
                                                    class="custom-control-input" required>
                                                <label class="custom-control-label"
                                                    for="g_male">{{ __('Male') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="g_female" value="Female" name="gender"
                                                    class="custom-control-input" required>
                                                <label class="custom-control-label"
                                                    for="g_female">{{ __('Female') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($job->applicant) && in_array('country', explode(',', $job->applicant)))
                                    <div class="form-group col-md-6 ">
                                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                    <div class="form-group col-md-6 country">
                                        {{ Form::label('state', __('State'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                    <div class="form-group col-md-6 country">
                                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('profile', explode(',', $job->visibility)))
                                    <div class="form-group col-md-6 ">
                                        {{ Form::label('profile', __('Profile'), ['class' => 'col-form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        <input type="file" class="form-control" name="profile" id="profile"
                                            data-filename="profile_create"
                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah" src="" class="mt-3" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="profile_create"></p>
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('resume', explode(',', $job->visibility)))
                                    <div class="form-group col-md-6 ">
                                        {{ Form::label('resume', __('CV / Resume'), ['class' => 'col-form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        <input type="file" class="form-control" name="resume" id="resume"
                                            data-filename="resume_create"
                                            onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah1" class="mt-3" src="" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="resume_create"></p>
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('letter', explode(',', $job->visibility)))
                                    <div class="form-group col-md-12 ">
                                        {{ Form::label('cover_letter', __('Cover Letter'), ['class' => 'form-label']) }}<span
                                            class="text-danger pl-1">*</span>
                                        {{ Form::textarea('cover_letter', null, ['class' => 'form-control', 'rows' => '3', 'required' => 'required']) }}
                                    </div>
                                @endif

                                @foreach ($questions as $question)
                                    <div class="form-group col-md-12  question question_{{ $question->id }}">
                                        {{ Form::label($question->question, $question->question, ['class' => 'form-label']) }}
                                        <input type="text" class="form-control"
                                            name="question[{{ $question->question }}]"
                                            {{ $question->is_required == 'yes' ? 'required' : '' }}>
                                    </div>
                                @endforeach

                                @if (!empty($job->visibility) && in_array('terms', explode(',', $job->visibility)))
                                    <div class="form-group col-md-12 ">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="termsCheckbox"
                                                name="terms_condition_check" required>
                                            <label class="form-check-label" for="termsCheckbox">{{ __('I Accept the') }}
                                                <a href="{{ route('job.terms.and.conditions', [$job->code, $currantLang]) }}"
                                                    target="_blank">{{ __('terms and conditions') }}</a></label>
                                        </div>

                                    </div>
                                @endif
                                <input type="hidden" id="qualify_lead" value="{{ $job->qualify_lead }}">
                                <input type="hidden" id="job_movies" value="{{ $job->movies }}">
                                <div class="col-12">
                                    <div class="text-center mt-4">
                                        <button type="submit" id="submitApplication"
                                            class="btn btn-primary">{{ __('Submit your application') }}</button>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </section>
            <!--Modao com cursos-->
            <section>
                <div class="modal fade" id="movieModal" tabindex="-1" aria-labelledby="movieModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lx modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <div id="movieCarousel" class="carousel slide" data-bs-ride="carousel"
                                    data-bs-interval="false">
                                    <div class="carousel-inner" id="carouselInner">
                                        <!-- vídeos inseridos dinamicamente -->
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#movieCarousel"
                                        data-bs-slide="prev" id="prevBtn" disabled>
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Voltar</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#movieCarousel"
                                        data-bs-slide="next" id="nextBtn" disabled>
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Próximo</span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" id="confirmApplication" disabled>Finalizar
                                    Cursos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/site.core.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/autosize.min.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/site.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/demo.js') }} "></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/daterangepicker.js') }}"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
           
            const qualifyLead = document.getElementById('qualify_lead').value;
            const jobMovies = JSON.parse(document.getElementById('job_movies').value || '[]');
            const movieModal = new bootstrap.Modal(document.getElementById('movieModal'));
            const carouselInner = document.getElementById('carouselInner');
            const confirmButton = document.getElementById('confirmApplication');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentIndex = 0;
            let moviesWatched = 0;
            let playerInstances = [];
            // Exibe o modal com os vídeos obrigatórios
            const showMovies = () => {
                carouselInner.innerHTML = '';
                moviesWatched = 0;

                jobMovies.forEach((movie, index) => {
                    const isActive = index === 0 ? 'active' : '';
                    const videoId = movie.path.match(/v=([^&]+)/)[1];
                    const embedUrl = `https://www.youtube.com/embed/${videoId}`;
                    const videoElement = `
                        <div class="carousel-item ${isActive}">
                            <div id="player-${index}" class="youtube-player"></div>
                        </div>`;
                    carouselInner.insertAdjacentHTML('beforeend', videoElement);

                    playerInstances.push(
                        new YT.Player(`player-${index}`, {
                            height: "390",
                            width: "640",
                            videoId: videoId,
                            events: {
                                onStateChange: (event) => onPlayerStateChange(event, index),
                            },
                        })
                    );
                });
                updateControls();
                movieModal.show();
            };
            const onPlayerStateChange = (event, index) => {
                if (event.data === YT.PlayerState.ENDED) {                    
                    if (index < jobMovies.length - 1) {
                        nextBtn.removeAttribute("disabled");
                    } else {
                        confirmButton.removeAttribute("disabled");
                    }
                }
            }
            updateControls = () => {
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = true;
            }

            // Volta para o vídeo anterior
            prevBtn.addEventListener('click', () => {
                currentIndex--;
                $('#movieCarousel').carousel('prev');
                updateControls();
            });

            // Monitora quando o vídeo termina
            carouselInner.addEventListener('timeupdate', (e) => {
                const iframe = e.target;
                if (iframe.dataset.index == currentIndex && iframe.currentTime >= iframe.duration - 2) {
                    nextBtn.removeAttribute(
                        'disabled');
                }
            });

            // Finaliza o processo e envia o formulário
            confirmButton.addEventListener('click', () => {
                movieModal.hide();
                document.querySelector('form').submit();
            });

            // Exibe o modal se necessário ao clicar no botão de envio
            document.getElementById('submitApplication').addEventListener('click', (e) => {
                if (qualifyLead == 1 && jobMovies.length > 0) {
                    e.preventDefault();
                    showMovies();
                } else {
                    document.querySelector('form').submit();
                }
            });
        });
    </script>
@endpush
