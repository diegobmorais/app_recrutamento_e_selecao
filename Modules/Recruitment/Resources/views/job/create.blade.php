@extends('layouts.main')
@section('page-title')
    {{ __('Create Job') }}
@endsection
@push('css')
    <link href="{{ asset('Modules/Recruitment/Resources/assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Modules/Recruitment/Resources/assets/css/custom.css') }}">
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
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/editorplaceholder.js') }}"></script>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary",
            })
        });

        $("#submit").click(function() {
            var allChecked = true;
            var customQuestionsContainer = $('#customQuestionsContainer');
            var errorMessageContainer = $('#customQuestionsError');

            if (errorMessageContainer.length === 0) {
                errorMessageContainer = $('<div id="customQuestionsError" class="text-danger mt-2 d-none"></div>');
                errorMessageContainer.text('Por favor, insira todas as perguntas para o teste de pré-selção.');
                customQuestionsContainer.append(errorMessageContainer);
            }

            var totalQuestions = customQuestionsContainer.find('.form-check').length;

            if (totalQuestions === 0) {
                event.preventDefault();
                errorMessageContainer.removeClass('d-none');
                $('html, body').animate({
                    scrollTop: customQuestionsContainer.offset().top - 50
                }, 500);
                return false;
            } else {
                errorMessageContainer.addClass('d-none');
            }

            $("#vaga-opcoes input[type='checkbox']").each(function() {
                if (!this.checked) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = this.name;
                    hiddenInput.value = '0';
                    $(this).closest('form').append(hiddenInput);
                }
            });

            var skill = $('.skill_data').val();
            if (skill == '') {
                $('#skill_validation').removeClass('d-none')
                return false;
            } else {
                $('#skill_validation').addClass('d-none')
            }

            var average = $('.average_data').val();
            if (average == '') {
                $('#average_validation').removeClass('d-none')
                return false;
            } else {
                $('#average_validation').addClass('d-none')
            }

            var description = $('textarea[name="description"]').val();
            if (!isNaN(description)) {
                $('#description_val').removeClass('d-none')
                return false;
            } else {
                $('#description_val').addClass('d-none')
            }

            var requirement = $('textarea[name="requirement"]').val();
            if (!isNaN(requirement)) {
                $('#req_val').removeClass('d-none')
                return false;
            } else {
                $('#req_val').addClass('d-none')
            }

            var checkbox = $('#check-terms');
            var termsDiv = $('#termsandcondition');
            var TermsAndCondition = $('textarea[name="terms_and_conditions"]').val();
            if (checkbox.is(':checked')) {
                if (!isNaN(TermsAndCondition)) {
                    $('#terms_val').removeClass('d-none')
                    return false;
                } else {
                    $('#terms_val').addClass('d-none')
                }
            }

            return true;
        });
    </script>

    <script>
        $(document).ready(function() {
            var checkbox = $('#check-terms');
            var termsDiv = $('#termsandcondition');

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
        });
    </script>
    <!--<script>
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
    </script>-->
    <script>
        const movieData = []

        function addMovie() {
            const name = $('#movie_name').val().trim();
            const path = $('#movie_path').val().trim();

            if (name === '' && path === '') {
                $("#alertContainer").html('<div class="alert alert-danger">Preencha todos os campos.</div>')
                setTimeout(() => ("#alertContainer").html(""), 3000)
            }
            const movie = {
                name: name,
                path: path,
            }
            movieData.push(movie)

            const movie_list = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${name} - <a href="${path}" target="_blank">Ver Curso</a></span>
                    <i class="ti ti-trash text-danger cursor-pointer" onclick="removeMovieList(${movieData.length - 1}, this)" title="Remover"></i>
                </li>
            `;

            $('#movie_list').append(movie_list)

            $('#movie_name').val('')
            $('#movie_path').val('')
        }

        function removeMovieList(index, element) {
            movieData.splice(index, 1)
            $(element).closest('li').remove()
        }

        function beforeFormSubmit() {
            const moviesInput = document.querySelector('#movies_input');
            moviesInput.value = JSON.stringify(movieData);
        }
        document.querySelector('#formMain').addEventListener('click', beforeFormSubmit);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxQuestions = 3;
            const addCustomQuestionBtn = document.getElementById('addCustomQuestionBtn');
            const saveCustomQuestionBtn = document.getElementById('saveCustomQuestionBtn');
            const customQuestionsContainer = document.getElementById('customQuestionsContainer');
            const newCustomQuestionInput = document.getElementById('newCustomQuestion');

            function updateAddButtonState() {
                const currentQuestions = customQuestionsContainer.querySelectorAll('.form-check').length;
                addCustomQuestionBtn.disabled = currentQuestions >= maxQuestions;
            }

            function addDeleteEvent(deleteButton, questionElement) {
                deleteButton.addEventListener('click', () => {
                    questionElement.remove();
                    updateAddButtonState();
                });
            }

            saveCustomQuestionBtn.addEventListener('click', () => {
                const questionText = newCustomQuestionInput.value.trim();

                if (!questionText) {
                    alert('{{ __('Please enter a question.') }}');
                    return;
                }

                const currentQuestions = customQuestionsContainer.querySelectorAll('.form-check').length;
                if (currentQuestions >= maxQuestions) {
                    alert('{{ __('You can only add up to 3 questions.') }}');
                    return;
                }

                const questionId = `custom_question_${Date.now()}`;
                const newQuestion = document.createElement('div');
                newQuestion.classList.add('form-check', 'custom-checkbox');
                newQuestion.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" class="form-check-input" name="custom_question[]"
                            value="${questionText}" id="${questionId}">
                        <label class="form-check-label" for="${questionId}" style="flex: 1;">
                            ${questionText}
                        </label>
                        <button type="button" class="btn btn-sm btn-danger delete-question-btn" style="padding: 5px 8px;">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;

                customQuestionsContainer.appendChild(newQuestion);


                const deleteButton = newQuestion.querySelector('.delete-question-btn');
                addDeleteEvent(deleteButton, newQuestion);

                newCustomQuestionInput.value = '';
                const modal = bootstrap.Modal.getInstance(document.getElementById(
                    'addCustomQuestionModal'));
                modal.hide();

                updateAddButtonState();
            });

            customQuestionsContainer.querySelectorAll('.delete-question-btn').forEach(button => {
                const questionElement = button.closest('.form-check');
                addDeleteEvent(button, questionElement);
            });

            updateAddButtonState();

            addCustomQuestionBtn.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('addCustomQuestionModal'));
                modal.show();
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var qualifyLeadCheckbox = document.getElementById("check-qualify-lead");
            var applyCoursesBlock = document.getElementById("addVideoModalLabel").closest(".form-group");

            applyCoursesBlock.style.display = "none";

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
@endpush

@section('page-breadcrumb')
    {{ __('Manage Job') }},
    {{ __('Create Job') }}
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">
        {{ Form::open(['url' => 'job', 'method' => 'post', 'id' => 'formMain']) }}
        <div class="row mt-3">
            <div class="col-md-6 ">
                <div class="card card-fluid jobs-card">
                    <div class="card-body ">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('title', __('Job Title'), ['class' => 'col-form-label']) !!}
                                {!! Form::text('title', old('title'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Titulo da vaga',
                                ]) !!}
                            </div>
                            {{--
                                <div class="form-group col-md-6">
                                    {{ Form::label('recruitment_type', __('Recruitment Type'), ['class' => 'col-form-label']) }}
                                    {{ Form::select('recruitment_type', $recruitment_type, null, ['class' => 'form-control select', 'id' => 'recruitment_type']) }}
                                </div>
                                            --}}
                            @if (module_is_active('Hrm'))
                                <div class="form-group col-md-6" id="branch" style="display: none;">
                                    {!! Form::label(
                                        'branch',
                                        !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'),
                                        ['class' => 'col-form-label'],
                                    ) !!}
                                    {{ Form::select('branch', $branches, null, ['class' => 'form-control ', 'placeholder' => 'Select Branch']) }}
                                </div>
                            @endif

                            <div class="form-group col-md-6">
                                {!! Form::label('location', __('Location'), ['class' => 'col-form-label']) !!}
                                {!! Form::text('location', old('location'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Localização da vaga',
                                ]) !!}
                            </div>

                            <div class="form-group col-md-6">
                                {!! Form::label('category', __('Tipo de Vaga'), ['class' => 'col-form-label']) !!}
                                {{ Form::select('category', $categories, null, ['class' => 'form-control ', 'placeholder' => 'Categoria', 'required' => 'required']) }}
                                @if (empty($categories->count()))
                                    <div class=" text-xs">
                                        {{ __('Por favor, adicione uma categoria. ') }}<a
                                            href="{{ route('job-category.index') }}"><b>{{ __('Adicionar Categoria') }}</b></a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-6 form-group" id="users" style="display: none;">
                                {{ Form::label('user_id', __('Client'), ['class' => 'col-form-label']) }}
                                {{ Form::select('user_id', $users, null, ['class' => 'form-control select2']) }}
                                @if (empty($users->count()))
                                    <div class="text-muted text-xs">
                                        {{ __('Please create new client') }} <a
                                            href="{{ route('users.index') }}">{{ __('here') }}</a>.
                                    </div>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                {!! Form::label('status', __('Status'), ['class' => 'col-form-label']) !!}
                                {{ Form::select('status', $status, null, ['class' => 'form-control ', 'placeholder' => 'Selecione o Status', 'required' => 'required']) }}
                            </div>
                            {{--
                                            <div class="form-group col-md-6">
                                                {{ Form::label('job_type', __('Modelo de Trabalho'), ['class' => 'col-form-label']) }}
                                                {{ Form::select('job_type', ['' => __('Modelo')] + $job_type, null, ['class' => 'form-control select']) }}
                                            </div>
                                            --}}
                            <div class="form-group col-md-6">
                                {!! Form::label('remuneration', __('Remuneração'), ['class' => 'col-form-label']) !!}
                                {!! Form::number('remuneration', old('remuneration'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'step' => '1',
                                    'placeholder' => 'R$',
                                ]) !!}
                            </div>

                            <div class="form-group col-md-6">
                                {!! Form::label('position', __('No. of Positions'), ['class' => 'col-form-label']) !!}
                                {!! Form::number('position', old('positions'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'step' => '1',
                                    'placeholder' => 'Quantidade',
                                ]) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) !!}
                                {!! Form::date('start_date', old('start_date'), [
                                    'class' => 'form-control ',
                                    'autocomplete' => 'off',
                                    'required' => 'required',
                                ]) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) !!}
                                {!! Form::date('end_date', old('end_date'), [
                                    'class' => 'form-control ',
                                    'autocomplete' => 'off',
                                    'required' => 'required',
                                ]) !!}
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="average ">{{ __('Nota de aprovação') }}</label>
                                <input type="number" class="form-control average_data" value="" name="average"
                                    placeholder="Nota minima" />
                            </div>
                            <p class="text-danger d-none" id="average_validation">{{ __('average filed is required.') }}
                            </p>
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="benefits">{{ __('Benefícios') }}</label>
                                <input type="text" class="form-control benefits_data" value="" data-toggle="tags"
                                    name="benefits" placeholder="Benefícios" />
                            </div>
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="skill">{{ __('Skill Box') }}</label>
                                <input type="text" class="form-control skill_data" value="" data-toggle="tags"
                                    name="skill" placeholder="Habilidades" />
                            </div>
                            <p class="text-danger d-none" id="skill_validation">{{ __('Skill filed is required.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ">
                <div class="card card-fluid jobs-card">
                    <div class="card-body ">
                        <div class="row">
                            {{-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <h6>{{ __('Need to Ask ?') }}</h6>
                                                    <div class="my-4">
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="applicant[]"
                                                                value="gender" id="check-gender">
                                                            <label class="form-check-label" for="check-gender">{{ __('Gênero') }} </label>
                                                        </div>
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="applicant[]"
                                                                value="dob" id="check-dob">
                                                            <label class="form-check-label"
                                                                for="check-dob">{{ __('Data de Nascimento') }}</label>
                                                        </div>
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="applicant[]"
                                                                value="country" id="check-country">
                                                            <label class="form-check-label" for="check-country">{{ __('País') }}</label>
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
                                                                value="profile" id="check-profile">
                                                            <label class="form-check-label" for="check-profile">{{ __('Perfil') }}
                                                            </label>
                                                        </div>
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="visibility[]"
                                                                value="resume" id="check-resume">
                                                            <label class="form-check-label"
                                                                for="check-resume">{{ __('Resumo') }}</label>
                                                        </div>
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="visibility[]"
                                                                value="letter" id="check-letter">
                                                            <label class="form-check-label"
                                                                for="check-letter">{{ __('Carta de Apresentação') }}</label>
                                                        </div>
                                                        <div class="form-check custom-checkbox">
                                                            <input type="checkbox" class="form-check-input" name="visibility[]"
                                                                value="terms" id="check-terms">
                                                            <label class="form-check-label"
                                                                for="check-terms">{{ __('Termos e Condições') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                            <div class="form-group col-md-12" id="vaga-opcoes">
                                <h6>{{ __('Opções da vaga') }}</h6>
                                <div class="my-4">
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="qualify_lead" value="1"
                                            id="check-qualify-lead">
                                        <label class="form-check-label"
                                            for="check-qualify-lead">{{ __('Qualificar lead através dos cursos da vaga.') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="notification" value="1"
                                            id="check-notification">
                                        <label class="form-check-label"
                                            for="check-notification">{{ __('Receber notificações a cada candidato recebido.') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="activate_pre_selection"
                                            value="1" id="check-activate-pre-selection">
                                        <label class="form-check-label"
                                            for="check-activate-pre-selection">{{ __('Realizar pré-seleção automático a cada candidato.') }}</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" name="activate_behavioral_test"
                                            value="1" id="check-activate-behavioral_test">
                                        <label class="form-check-label" for="check-activate-behavioral_test">Realizar teste
                                            comportamental automático se aprovado.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <h6>{{ __('Custom Questions') }}</h6>
                                <div>
                                    @permission('custom question create')
                                        <button type="button" id="addCustomQuestionBtn" class="btn btn-sm btn-primary"
                                            data-bs-toggle="tooltip" title="{{ __('Adicionar Pergunta Personalizada') }}">
                                            <i class="ti ti-plus"></i> {{ __('Adicionar Pergunta') }}
                                        </button>
                                    @endpermission
                                </div>
                                <div class="my-4" id="customQuestionsContainer">
                                    @foreach ($customQuestion as $question)
                                        <div class="form-check custom-checkbox existing-question">
                                            <input type="checkbox" class="form-check-input" name="custom_question[]"
                                                value="{{ $question->id }}"
                                                @if ($question->is_required == 'yes') required @endif
                                                id="custom_question_{{ $question->id }}">
                                            <label class="form-check-label"
                                                for="custom_question_{{ $question->id }}">{{ $question->question }}
                                                @if ($question->is_required == 'yes')
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
                            </div>
                            <div class="form-group col-md-12">
                                <div class="header">
                                    <h5 class="title mb-2" id="addVideoModalLabel">Aplicar Cursos para Vaga</h5>
                                </div>
                                <div>
                                    <input type="text" id="movie_name" class="form-control mb-3" placeholder="Nome">
                                    <div class="input-group mb-3">
                                        <input type="text" id="movie_path" class="form-control"
                                            placeholder="Adicione o link do Curso">
                                        <button type="button" class="btn btn-success"
                                            onclick="addMovie()">Adicionar</button>
                                    </div>
                                    <input type="hidden" name="movies" id="movies_input">
                                    <div id="alertContainer"></div>
                                    <ul id="movie_list" class="list-group mt-3"></ul>
                                </div>
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
                                {!! Form::label('description', __('Job Description'), ['class' => 'col-form-label']) !!}
                                <textarea name="description"
                                    class="form-control dec_data summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}" required
                                    id="description"></textarea>

                                <p class="text-danger d-none" id="description_val">
                                    {{ __('This filed is required.') }}
                                </p>
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
                                {!! Form::label('requirement', __('Job Requirement'), ['class' => 'col-form-label']) !!}
                                <textarea name="requirement"
                                    class="form-control req_data summernote  {{ !empty($errors->first('requirement')) ? 'is-invalid' : '' }}" required
                                    id="requirement"></textarea>
                                <p class="text-danger d-none" id="req_val">{{ __('This filed is required.') }}</p>
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
                                    id="terms_and_conditions"></textarea>
                                <p class="text-danger d-none" id="terms_val">{{ __('This filed is required.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-end">
                <div class="form-group">
                    <input type="submit" id="submit" value="{{ __('Create') }}" class="btn btn-primary">
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
