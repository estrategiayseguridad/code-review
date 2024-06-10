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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File as FileRule;

new class extends Component
{
    use WithFileUploads;

    public $name;
    public $description;
    public $zipfile;

    public function save(): void
    {
        $validated = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'zipfile'     => ['required', FileRule::types(['zip'])->max('256mb')]
        ]);

        DB::beginTransaction();
        $tmpPath = 'livewire-tmp/' . $validated['zipfile']->getFilename();
        $zipPath = '';
        $unzipPath = '';

        try {
            $filename = Str::random(40) . '.zip';
            $zipPath = $this->zipfile->storeAs(path: 'projects', name: $filename);
            $validated['directory'] = $zipPath;
            $validated['created_by'] = auth()->user()->id;
            $validated['description'] = empty($validated['description']) ? null : $validated['description'];

            $project = Project::create($validated);

            if ($project) {
                $zip = new \ZipArchive();
                $zipRealPath = storage_path('app/' . $validated['directory']);

                if ($zip->open($zipRealPath) === true) {

                    // Extract files
                    $languageIds = [];
                    $extractedPath = storage_path('app/projects/');
                    $zip->extractTo($extractedPath);

                    // Update project directory with the extracted path
                    if ($zip->count()) {
                        $unzipPath = "projects/" . trim($zip->getNameIndex(0), '/');
                        $project->update(['directory' => $unzipPath]);
                    }

                    // Analyze the extracted contents.
                    for ($i = 0; $i < $zip->count(); $i++) {
                        $filename = $zip->getNameIndex($i);

                        // Not a directory
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

                    $project->languages()->sync(array_unique($languageIds));
                } else {
                    throw new \Exception("Failed to open zip file.");
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            if ($unzipPath && Storage::exists($unzipPath)) {
                Storage::deleteDirectory($unzipPath);
            }
            Log::error($e->getMessage());
        } finally {
            if ($tmpPath && Storage::exists($tmpPath)) {
                Storage::delete($tmpPath);
            }
            if ($zipPath && Storage::exists($zipPath)) {
                Storage::delete($zipPath);
            }
        }

        $this->redirect('/', navigate: true);
    }

}; ?>

@section('header')
    <livewire:layout.header-projects />
@endsection

<div class="row justify-content-center">
    <div class="col-11 col-md-8">
        <h2>Información del Proyecto</h2>
        <p class="mb-4 text-body-tertiary">Ingresar siguiente información para crear un nuevo proyecto.</p>

        <div class="card">
            <div class="card-body m-4">
                <form wire:submit="save" enctype="multipart/form-data">
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
                            <x-input-error :for="'name'" />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea wire:model="description" id="description" name="description" class="w-100" />
                            <x-input-error :for="'description'" />
                        </div>

                        <div>
                            <x-input-label for="zipfile" :value="__('File')" />
                            <x-text-input wire:model="zipfile" id="zipfile" class="border border-secondary rounded shadow-sm w-100" type="file" name="zipfile" accept=".zip" required autofocus />
                            <div class="form-text text-body-tertiary">Tamaño máximo: 256MB</div>
                            <x-input-error :for="'zipfile'" />
                        </div>

                        <div x-show="uploading">
                            <progress max="100" x-bind:value="progress" class="w-100"></progress>
                        </div>

                        <div class="d-flex mt-3">
                            <x-primary-button type="submit" x-show="!uploading">{{ __('Save') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
