@push('css')
<link href="{{ asset('Modules/Recruitment/Resources/assets/css/analysis.css') }}" rel="stylesheet" />
@endpush

<div class="modal-body">
    <ul class="nav nav-tabs" id="testTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="result-tab" data-bs-toggle="tab" data-bs-target="#result" type="button"
                role="tab">Resultado Final</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button"
                role="tab">Histórico de Conversa</button>
        </li>
    </ul>
    <div class="tab-content" id="testTabContent">
        <div class="tab-pane fade show active" id="result" role="tabpanel">
            <div class="result-container d-flex align-items-center">
                <div class="result-summary">
                    <h5 class="result-title">Avaliação do Candidato</h5>
                    <p>{{ $jobApplication->final_summary ?? 'Nenhum resumo disponível.' }}</p>
                </div>
                <div class="result-score-circle">                  
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg"
                            d="M18 2.0845
                               a 15.9155 15.9155 0 0 1 0 31.831
                               a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle"
                            stroke-dasharray="{{ $jobApplication->final_score ?? 0 }}, 100"
                            d="M18 2.0845
                               a 15.9155 15.9155 0 0 1 0 31.831
                               a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <text x="18" y="20.35" class="percentage">
                            {{ $jobApplication->final_score ?? 'N/A' }}%
                        </text>
                    </svg>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="chat-container">
                @forelse ($interviewHistory as $message)
                    <div class="chat-message {{ $message->sender === 'candidato' ? 'candidato' : 'assistente' }}">                       
                        <div class="message-content">
                            <span class="sender-name">{{ $message->sender === 'candidato' ? 'Candidato' : 'Assistente' }}</span>
                            <p class="message-text">{{ $message->content }}</p>
                            <span class="message-time">{{ $message->created_at->format('h:i A') }}</span>
                        </div>
                    </div>
                @empty
                    <p>{{ __('No conversation history available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary">{{ __('Export to PDF') }}</button>
</div>
