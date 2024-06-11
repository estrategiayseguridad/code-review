<?php

use App\Models\Parameter;
use Livewire\Volt\Component;

new class extends Component
{
    public $parameters;
    public $params = [];
    public $dirtyParams = [];

    public function mount(): void
    {
        $this->parameters = Parameter::all();
        foreach($this->parameters as $param) {
            $this->params[$param->id] = $param->value;
        }
    }

    public function updated($propertyName): void
    {
        if (!in_array($propertyName, $this->dirtyParams)) {
            $this->dirtyParams[] = $propertyName;
        }
    }

    public function save(): void
    {
        if (count($this->dirtyParams)) {
            foreach ($this->dirtyParams as $dirtyParam) {
                $paramId = explode('.', $dirtyParam)[1];
                $parameter = Parameter::findOrFail($paramId);
                $parameter->value = empty($this->params[$paramId]) ? null : $this->params[$paramId];
                $parameter->save();
                $this->editEnv($parameter->key, $parameter->value);
            }
            $this->dirtyParams = [];
            $this->dispatch('params-updated', ['message' => 'Saved.']);
        } else {
            $this->dispatch('params-updated', ['message' => 'No changes.']);
        }
    }

    public function editEnv($key, $value): void
    {
        $path = base_path('.env');
        if (is_bool(env($key))) {
            $old = env($key)? 'true' : 'false';
        } elseif (env($key) === null) {
            $old = 'null';
        } else {
            $old = env($key);
        }
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                "$key=$old", "$key=$value", file_get_contents($path)
            ));
        }
    }

}; ?>

@section('header')
    <livewire:layout.header-config />
@endsection

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <x-card>
            <x-slot:heading>{{ __('Parameters') }}</x-slot>
            <x-slot:body>
                <form wire:submit="save" class="d-grid gap-4">
                    @forelse($parameters->where('key', '<>', 'PROMPT') as $param)
                    <div>
                        <x-input-label class="form-label text-body-tertiary small" :value="$param->key" />
                        <x-text-input type="text" wire:model.blur="params.{{ $param->id }}" value="{{ $param->value }}" />
                        @if ($param->description)
                            <div class="form-text text-body-tertiary">{{ $param->description }}</div>
                        @endif
                    </div>
                    @empty
                    <div>{{ __('No Content') }}</div>
                    @endforelse
                    @if ($p = $parameters->where('key', 'PROMPT')->first())
                    <div>
                        <x-input-label class="form-label text-body-tertiary small" :value="$p->key" />
                        <x-textarea wire:model.blur="params.{{ $p->id }}" :rows="10">{!! $p->value !!}</x-textarea>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <x-action-message on="params-updated">{{ session('params-updated.message') }}</x-action-message>
                    </div>
                </form>
            </x-slot>
        </x-card>
    </div>
</div>
