@extends('layouts.main')
@section('page-title')
    {{ __('Edit Job') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/Recruitment/Resources/assets/css/custom.css') }}">
    <link href="{{ asset('Modules/Recruitment/Resources/assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@section('page-action')
    <div class="">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'job',
                'module' => 'Recruitment',
            ])
        @endif
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.12.1/basic/ckeditor.js"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/editorplaceholder.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>

    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary"
            })
        });
    </script>
@endpush

@section('page-breadcrumb')
    {{ __('Manage Job') }},
    {{ __('Edit Job') }}
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">

        {{ Form::model($job, ['route' => ['job.update', $job->id], 'method' => 'PUT', 'id' => 'formMain']) }}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 ">
                    <div class="card card-fluid jobs-card">
                        <div class="card-body ">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('title', __('Job Title'), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                                {{-- ----------------------------------
                                <div class="form-group col-md-6">
                                    {{ Form::label('recruitment_type', __('Recruitment Type'), ['class' => 'col-form-label']) }}
                                    {{ Form::select('recruitment_type', $recruitment_type, null, ['class' => 'form-control select', 'id' => 'recruitment_type']) }}
                                </div>
                                ------------------------------------ --}}
                                @if (module_is_active('Hrm'))
                                    <div class="form-group col-md-6" id="branch" style="display: none;">
                                        {!! Form::label(
                                            'branch',
                                            !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'),
                                            ['class' => 'form-label'],
                                        ) !!}
                                        {{ Form::select('branch', $branches, null, ['class' => 'form-control ', 'placeholder' => 'Select Branch']) }}
                                    </div>
                                @endif

                                <div class="form-group col-md-6 mt-2">
                                    {!! Form::label('location', __('Location'), ['class' => 'form-label']) !!}
                                    {!! Form::text('location', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! Form::label('category', __('Tipo da Vaga'), ['class' => 'form-label']) !!}
                                    {{ Form::select('category', $categories, null, ['class' => 'form-control ', 'placeholder' => 'Categoria', 'required' => 'required']) }}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! Form::label('status', __('Status'), ['class' => 'form-label']) !!}
                                    {{ Form::select('status', $status, null, ['class' => 'form-control ', 'placeholder' => 'Selecione o Status', 'required' => 'required']) }}
                                </div>

                                <div class="form-group col-md-6" id="users" style="display: none;">
                                    {{ Form::label('user_id', __('Client'), ['class' => 'form-label']) }}
                                    {{ Form::select('user_id', $users, null, ['class' => 'form-control select2']) }}
                                    @if (empty($users->count()))
                                        <div class="text-muted text-xs">
                                            {{ __('Please create new client') }} <a
                                                href="{{ route('users.index') }}">{{ __('here') }}</a>.
                                        </div>
                                    @endif
                                </div>
                                {{-- ----------------------------------
                                <div class="form-group col-md-6">
                                    {{ Form::label('job_type', __('Tipo de Vaga'), ['class' => 'form-label']) }}
                                    {{ Form::select('job_type', $job_type, null, ['class' => 'form-control select']) }}
                                </div>
                                 ------------------------------------ --}}
                                <div class="form-group col-md-6">
                                    {!! Form::label('remuneration', __('Remuneração'), ['class' => 'form-label']) !!}
                                    {!! Form::number('remuneration', old('remuneration'), [
                                        'class' => 'form-control',
                                        'required' => 'required',
                                        'step' => '1',
                                        'placeholder' => 'Entre com o valor',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! Form::label('position', __('No. of Positions'), ['class' => 'form-label']) !!}
                                    {!! Form::text('position', null, [
                                        'class' => 'form-control',
                                        'required' => 'required',
                                        'placeholder' => 'Enter Position',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('start_date', __('Start Date'), ['class' => 'form-label']) !!}
                                    {!! Form::date('start_date', null, ['class' => 'form-control ', 'autocomplete' => 'off']) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('end_date', __('End Date'), ['class' => 'form-label']) !!}
                                    {!! Form::date('end_date', null, ['class' => 'form-control ', 'autocomplete' => 'off']) !!}
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-form-label" for="benefits">{{ __('Benefícios') }}</label>
                                    <input type="text" class="form-control benefits_data" value="{{ $job->benefits }}"
                                        data-toggle="tags" name="benefits" placeholder="Benefícios" />
                                </div>
                                <p class="text-danger d-none" id="benefits_validation">
                                    {{ __('benefits filed is required.') }}
                                <div class="form-group col-md-12">
                                    <label class="col-form-label" for="skill">{{ __('Skill Box') }}</label>
                                    <input type="text" class="form-control" value="{{ $job->skill }}"
                                        data-toggle="tags" name="skill" placeholder="Habilidades" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-form-label"
                                        for="average">{{ __('Nota media de aprovação') }}</label>
                                    <input type="number" class="form-control" value="{{ $job->average }}" name="average"
                                        placeholder="average" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="card card-fluid jobs-card">
                        <div class="card-body ">
                            <div class="row">
                                {{--
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h6>{{ __('Need to Ask ?') }}</h6>
                                        <div class="my-4">
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="applicant[]"
                                                    value="gender" id="check-gender"
                                                    {{ in_array('gender', $job->applicant) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="check-gender">{{ __('Gender') }}
                                                </label>
                                            </div>
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="applicant[]"
                                                    value="dob" id="check-dob"
                                                    {{ in_array('dob', $job->applicant) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-dob">{{ __('Date Of Birth') }}</label>
                                            </div>
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="applicant[]"
                                                    value="country" id="check-country"
                                                    {{ in_array('country', $job->applicant) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-country">{{ __('Country') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h6>{{ __('Need to show Option ?') }}</h6>
                                        <div class="my-4">
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="visibility[]"
                                                    value="profile" id="check-profile"
                                                    {{ in_array('profile', $job->visibility) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-profile">{{ __('Profile Image') }} </label>
                                            </div>
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="visibility[]"
                                                    value="resume" id="check-resume"
                                                    {{ in_array('resume', $job->visibility) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-resume">{{ __('Resume') }}</label>
                                            </div>
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="visibility[]"
                                                    value="letter" id="check-letter"
                                                    {{ in_array('letter', $job->visibility) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-letter">{{ __('Cover Letter') }}</label>
                                            </div>
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" name="visibility[]"
                                                    value="terms" id="check-terms"
                                                    {{ in_array('terms', $job->visibility) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="check-terms">{{ __('Terms And Conditions') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                --}}
                                <div class="form-group col-md-12" id="vaga-opcoes">
                                    <h6>{{ __('Opções da vaga') }}</h6>
                                    <div class="my-4">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" name="qualify_lead"
                                                value="qualify-lead" id="check-qualify-lead"
                                                {{ $job->qualify_lead === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="check-qualify-lead">{{ __('Qualificar lead através dos cursos da vaga') }}</label>
                                        </div>
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" name="notification"
                                                value="notification" id="check-notification"
                                                {{ $job->receive_notification === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="check-notification">{{ __('Receber notificações a cada candidato recebido.') }}</label>
                                        </div>
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" name="activate_pre_selection"
                                                value="activate-pre-selection" id="check-activate-pre-selection"
                                                {{ $job->activate_pre_selection === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="check-activate-pre-selection">{{ __('Realizar pré-seleção automático a cada candidato.') }}</label>
                                        </div>
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" name="activate_behavioral_test"
                                                value="1" id="check-activate-behavioral_test"
                                                {{ $job->activate_behavioral_test === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="check-activate-behavioral_test">Realizar
                                                teste comportamental automático se aprovado.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <h6>{{ __('Custom Questions') }}</h6>
                                    {{--
                                    <div>
                                        @permission('custom question create')
                                            <button type="button" id="addCustomQuestionBtn" class="btn btn-sm btn-primary"
                                                data-bs-toggle="tooltip"
                                                title="{{ __('Adicionar Pergunta Personalizada') }}">
                                                <i class="ti ti-plus"></i> {{ __('Adicionar Pergunta') }}
                                            </button>
                                        @endpermission
                                    </div>   --}}
                                    <div class="my-4" id="customQuestionsContainer">
                                        @foreach ($customQuestion as $question)
                                            <div class="form-check custom-checkbox existing-question mb-3"
                                                style="display: flex; align-items: center; gap: 10px; font-family: Arial, sans-serif; font-size: 12px;">
                                                <i class="far fa-question-circle"></i>
                                                <label class="form-check-label"
                                                    for="custom_question_{{ $question->id }}">
                                                    {{ $question->question }}
                                                    @if ($question->is_required == 'yes')
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                                {{--
                                <!-- Modal para adicionar pergunta -->
                                <div class="modal fade" id="addCustomQuestionModal" tabindex="-1"
                                    aria-labelledby="addCustomQuestionModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addCustomQuestionModalLabel">
                                                    {{ __('Adicionar Pergunta Personalizada') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="newCustomQuestion">{{ __('Question') }}</label>
                                                    <input type="text" id="newCustomQuestion" class="form-control">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="button" id="saveCustomQuestionBtn"
                                                    class="btn btn-primary">{{ __('Add') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="form-group col-md-12">
                                    <div class="header">
                                        <h5 class="title mb-2" id="addVideoModalLabel">Aplicar Cursos para Vaga</h5>
                                    </div>
                                    <div>
                                        <input type="text" id="movie_name" class="form-control mb-3"
                                            placeholder="Nome">
                                        <div class="input-group mb-3">
                                            <input type="text" id="movie_path" class="form-control"
                                                placeholder="Adicione o link do Curso">
                                            <button type="button" class="btn btn-success"
                                                onclick="addMovie()">Adicionar</button>
                                        </div>
                                        <div id="alertContainer"></div>
                                        <ul id="movie_list" class="list-group mt-3">
                                            @foreach ($job->movies as $movie)
                                                <li class="list-group-item d-flex justify-content-between align-items-center"
                                                    data-id="{{ $movie->id }}">
                                                    <span>{{ $movie->name }} - <a href="{{ $movie->path }}"
                                                            target="_blank">Ver Curso</a></span>
                                                    <i class="ti ti-trash text-danger cursor-pointer"
                                                        onclick="removeMovieList({{ $movie->id }}, this)"
                                                        title="Remover"></i>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <input type="hidden" name="movies" id="movies_input">
                                    <input type="hidden" name="removed_movies" id="removed_movies">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-fluid jobs-summernote-card">
                        <div class="card-body ">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('description', __('Job Description'), ['class' => 'form-label']) !!}
                                    <textarea name="description"
                                        class="form-control summernote  {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}" required
                                        id="description">{{ $job->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-fluid jobs-summernote-card">
                        <div class="card-body ">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('requirement', __('Job Requirement'), ['class' => 'form-label']) !!}
                                    <textarea name="requirement"
                                        class="form-control summernote  {{ !empty($errors->first('requirement')) ? 'is-invalid' : '' }}" required
                                        id="requirement">{{ $job->requirement }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="termsandcondition">
                    <div class="card card-fluid jobs-summernote-card">
                        <div class="card-body ">
                            <div class="row">
                                <div class="form-group terms_val col-md-12">
                                    {!! Form::label('terms_and_conditions', __('Terms And Conditions'), ['class' => 'col-form-label']) !!}
                                    <textarea name="terms_and_conditions"
                                        class="form-control summernote  {{ !empty($errors->first('terms_and_conditions')) ? 'is-invalid' : '' }}"
                                        id="terms_and_conditions">{{ $job->terms_and_conditions }}</textarea>
                                    <p class="text-danger d-none" id="terms_val">{{ __('This filed is required.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <div class="form-group">
                        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
                    </div>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                var qualifyLeadCheckbox = document.getElementById("check-qualify-lead");
                var applyCoursesBlock = document.getElementById("addVideoModalLabel").closest(".form-group");

                applyCoursesBlock.style.display = qualifyLeadCheckbox.checked ? "block" : "none";

                qualifyLeadCheckbox.addEventListener("change", function() {
                    applyCoursesBlock.style.display = this.checked ? "block" : "none";
                });

                var preSelectionCheckbox = document.getElementById("check-activate-pre-selection");
                var behavioralTestCheckbox = document.getElementById("check-activate-behavioral_test");

                behavioralTestCheckbox.addEventListener("change", function() {
                    if (!preSelectionCheckbox.checked) {
                        Swal.fire({
                            icon: "warning",
                            title: "Ação não permitida!",
                            text: "Para ativar este teste, primeiro ative a opção 'Realizar pré-seleção automático a cada candidato.'",
                            confirmButtonText: "Entendido",
                            confirmButtonColor: "#d33",
                        });
                        this.checked = false;
                    }
                });

                preSelectionCheckbox.addEventListener("change", function() {
                    if (!this.checked) {
                        behavioralTestCheckbox.checked = false;
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                var checkbox = $('#check-terms');
                var termsDiv = $('#termsandcondition');
                var textarea = $('#terms_and_conditions');
                var validationMessage = $('#terms_val');

                checkbox.change(function() {
                    if (checkbox.is(':checked')) {
                        termsDiv.show();
                    } else {
                        termsDiv.hide();
                    }
                });

                if (!checkbox.is(':checked')) {
                    termsDiv.hide();
                }

                $('form').submit(function(event) {

                    if (checkbox.is(':checked') && textarea.val().trim() === '') {
                        validationMessage.removeClass('d-none');
                        event.preventDefault();
                    }
                    $("#vaga-opcoes input[type='checkbox']").each(function() {
                        if (!this.checked) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = this.name;
                            hiddenInput.value = '0';
                            $(this).closest('form').append(hiddenInput);
                        } else {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = this.name;
                            hiddenInput.value = '1';
                            $(this).closest('form').append(hiddenInput);
                        }
                    });
                });
                textarea.on('input', function() {
                    if (textarea.val().trim() !== '') {
                        validationMessage.addClass('d-none');
                    }
                });
            });
        </script>
        <!--
                    <script>
                        $(document).ready(function() {
                            function toggleFormGroups() {
                                var selectedType = $('#recruitment_type').val();
                                if (selectedType === 'internal') {
                                    $('#branch').show();
                                    $('#users').hide();
                                    $('#branch').prop('required', true);
                                    $('#users').prop('required', false);
                                } else if (selectedType === 'client') {
                                    $('#branch').hide();
                                    $('#users').show();
                                    $('#users').prop('required', true);
                                    $('#branch').prop('required', false);
                                } else {
                                    $('#branch').hide();
                                    $('#users').hide();
                                    $('#users').prop('required', false);
                                    $('#branch').prop('required', false);
                                }
                            }

                            toggleFormGroups();

                            $('#recruitment_type').change(toggleFormGroups);
                        });
                    </script>
                    -->
        <script>
            let movieData = [];
            let removedMovies = [];

            function addMovie() {
                const name = $('#movie_name').val().trim();
                const path = $('#movie_path').val().trim();

                if (!name || !path) {
                    $("#alertContainer").html('<div class="alert alert-danger">Preencha todos os campos.</div>');
                    setTimeout(() => $("#alertContainer").html(""), 3000);
                    return;
                }

                const movie = {
                    name,
                    path
                };
                movieData.push(movie);

                const movieListItem = `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${name} - <a href="${path}" target="_blank">Ver Curso</a></span>
                        <i class="ti ti-trash text-danger cursor-pointer" onclick="removeNewMovie(${movieData.length - 1}, this)" title="Remover"></i>
                    </li>
                `;

                $('#movie_list').append(movieListItem);

                $('#movie_name').val('');
                $('#movie_path').val('');
            }

            function removeMovieList(movieId, element) {
                removedMovies.push(movieId);
                $(element).closest('li').remove();
            }

            function removeNewMovie(index, element) {
                movieData.splice(index, 1);
                $(element).closest('li').remove();
            }

            function beforeFormSubmit() {
                const moviesInput = document.querySelector('#movies_input');
                const removedMoviesInput = document.querySelector('#removed_movies');
                moviesInput.value = JSON.stringify(movieData);
                removedMoviesInput.value = JSON.stringify(removedMovies);
            }

            document.querySelector('#formMain').addEventListener('submit', beforeFormSubmit);
        </script>
    @endpush
