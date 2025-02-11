{{ Form::model($interviewSchedule, ['route' => ['interview-schedule.update', $interviewSchedule->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'interview-schedule','module'=>'Recruitment'])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('candidate', __('Interviewer'), ['class' => 'col-form-label']) }}
            {{ Form::select('candidate', $candidates, null, ['class' => 'form-control ', 'required' => 'required']) }}
        </div>
        @if (module_is_active('Hrm'))
            <div class="form-group col-md-6">
                {{ Form::label('employee', __('Assign Employee'), ['class' => 'col-form-label']) }}
                {{ Form::select('employee', $employees, null, ['class' => 'form-control ', 'required' => 'required']) }}
            </div>
        @endif
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Interview Date'), ['class' => 'col-form-label']) }}
            {{ Form::date('date', null, ['class' => 'form-control d_week']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('time', __('Interview Time'), ['class' => 'col-form-label']) }}
            {{ Form::time('time', null, ['class' => 'form-control timepicker']) }}
        </div>
        <div class="form-group">
            {{ Form::label('comment', __('Comment'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('comment', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}
