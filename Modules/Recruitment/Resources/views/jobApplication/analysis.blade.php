@push('css')
    <link href="{{ asset('Modules/Recruitment/Resources/assets/css/analysis.css') }}" rel="stylesheet" />
@endpush

<div class="modal-body">
    <ul class="nav nav-tabs" id="testTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link-an active" id="pre-selection-tab" data-bs-toggle="tab" data-bs-target="#pre-selection"
                type="button" role="tab">
                <i class="fas fa-chart-pie"></i> Teste de Pré-Seleção
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link-an" id="behavioral-test-tab" data-bs-toggle="tab" data-bs-target="#behavioral-test"
                type="button" role="tab">
                <i class="fas fa-user-check"></i> Teste Comportamental
            </button>
        </li>
    </ul>

    <div class="tab-content" id="testTabContent">
        <!-- Resultado de Pré-Seleção -->
        <div class="tab-pane fade show active" id="pre-selection" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="result-container">
                        <h5 class="result-title">Resultado do Teste de Pré-Seleção</h5>
                        <p>{{ $jobApplication->final_summary ?? 'Nenhum resumo disponível.' }}</p>
                        <div class="result-score-circle">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle" stroke-dasharray="{{ $jobApplication->final_score ?? 0 }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <text x="18" y="20.35"
                                    class="percentage">{{ $jobApplication->final_score ?? 'N/A' }}%</text>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chat-container">
                        <h5 class="chat-title">Histórico de Conversa (Pré-Seleção)</h5>
                        @forelse ($preSelectionHistory as $message)
                            <div
                                class="chat-message {{ $message->sender === 'candidato' ? 'candidato' : 'assistente' }}">
                                <div class="message-content">
                                    <span
                                        class="sender-name">{{ $message->sender === 'candidato' ? 'Candidato' : 'Assistente' }}</span>
                                    <p class="message-text">{{ $message->content }}</p>
                                    <span class="message-time">{{ $message->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        @empty
                            <p>{{ __('Nenhum histórico de conversa disponível para o teste de pré-seleção.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultado de Teste Comportamental -->
        <div class="tab-pane fade" id="behavioral-test" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="result-container">
                        <h5 class="result-title">Resultado do Teste Comportamental</h5>
                        <p>{{ $jobApplication->behavioral_test_summary ?? 'Nenhum resumo disponível.' }}</p>
                        <div class="result-score-circle">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle"
                                    stroke-dasharray="{{ $jobApplication->behavioral_test_score ?? 0 }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <text x="18" y="20.35"
                                    class="percentage">{{ $jobApplication->behavioral_test_score ?? 'N/A' }}%</text>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chat-container">
                        <h5 class="chat-title">Histórico de Conversa (Teste Comportamental)</h5>
                        @forelse ($behavioralTestHistory as $message)
                            <div
                                class="chat-message {{ $message->sender === 'candidato' ? 'candidato' : 'assistente' }}">
                                <div class="message-content">
                                    <span
                                        class="sender-name">{{ $message->sender === 'candidato' ? 'Candidato' : 'Assistente' }}</span>
                                    <p class="message-text">{{ $message->content }}</p>
                                    <span class="message-time">{{ $message->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        @empty
                            <p>{{ __('Nenhum histórico de conversa disponível para o teste comportamental.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--
<div class="modal-footer">
    <button type="button" class="btn btn-primary">{{ __('Exportar para PDF') }}</button>
</div>
--}}
