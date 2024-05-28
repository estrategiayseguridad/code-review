<?php

use App\Models\Extension;
use App\Models\File;
use App\Models\Language;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File as FileRule;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $description = '';
    public $zipfile = '';

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): void
    {
        DB::beginTransaction();
        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['string'],
                'zipfile' => ['required', FileRule::types(['zip'])->max('500mb')]
            ]);

            $filename = Str::random(40) . '.zip';
            $validated['directory'] = $this->zipfile->storeAs(path: 'projects', name: $filename);
            $validated['created_by'] = auth()->user()->id;
            $validated['description'] = empty($validated['description']) ? null : $validated['description'];

            $project = Project::create($validated);

            if ($project) {
                $zip = new \ZipArchive();
                if ($zip->open(storage_path('app/' . $validated['directory'])) === true) {
                    $zip->extractTo(storage_path('app/projects/'));
                    $languageIds = array();
                    for ($i = 0; $i < $zip->count(); $i++) {
                        $filename = $zip->getNameIndex($i);
                        // Not a directory - Get the extension of the file
                        if (substr($filename, -1) != '/') {
                            $last_dot_position = strrpos($filename, '.');
                            $file_ext = $last_dot_position !== false ? substr($filename, $last_dot_position) : '';
                            if ($file_ext) {
                                // Check extension exists in DB
                                $extension = Extension::where('suffix', $file_ext)->first();
                                if ($extension) {
                                    // Store file for analysis
                                    File::create([
                                        'project_id'   => $project->id,
                                        'path'         => $filename,
                                        'extension_id' => $extension->id
                                    ]);
                                    array_push($languageIds, $extension->language_id);
                                }
                            }
                        }
                    }
                    $zip->close();
                    $project->languages()->sync($languageIds);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            dd($e);
        }
        Storage::deleteDirectory('livewire-tmp');
        $this->redirectRoute('projects.index');
    }

}; ?>

<section>
    <div class="container">
        <h2>Información del Proyecto</h2>
        <p class="mt-1 mb-4 text-body-tertiary">Ingresar siguiente información para crear un nuevo proyecto.</p>

        <div class="card">
            <div class="card-body m-4">
                <form wire:submit="store" enctype="multipart/form-data">
                    <div
                        class="d-grid gap-3"
                        x-data="{ uploading: false, progress: 0 }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input wire:model="name" id="name" name="name" type="text" class="border border-secondary rounded shadow-sm w-100" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea wire:model="description" id="description" name="description" class="w-100" />
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="zipfile" :value="__('Zip')" />
                            <x-text-input wire:model="zipfile" id="zipfile" class="border border-secondary rounded shadow-sm w-100" type="file" name="zipfile" accept=".zip" required autofocus />
                            <x-input-error :messages="$errors->get('zipfile')" class="mt-2" />
                        </div>

                        <div x-show="uploading">
                            <progress max="100" x-bind:value="progress" class="w-100"></progress>
                        </div>

                        <div class="d-flex">
                            <x-primary-button type="submit" wire:loading.attr="disabled" class="mt-4">{{ __('Save') }}</x-primary-button>
                            <div wire:loading wire:target="store"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
