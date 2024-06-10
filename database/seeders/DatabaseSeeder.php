<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Parameter;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'username' => 'admin',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'is_admin' => true
        ]);

        Parameter::create([
            'key' => 'PAGINATION_ROWS_PER_PAGE',
            'value' => '15',
            'description' => "Cantidad de filas a mostrar por página en las listas paginadas."
        ]);

        Parameter::create([
            'key' => 'GEMINI_API',
            'value' => 'true',
            'description' => "Habilitar o deshabilitar el uso de la API de Gemini para los análisis."
        ]);

        Parameter::create([
            'key' => 'GEMINI_API_KEY'
        ]);

        // Parameter::create([
        //     'key' => 'GEMINI_BASE_URL',
        //     'value' => 'https://generativelanguage.googleapis.com/v1beta/'
        // ]);

        Parameter::create([
            'key' => 'OPENAI_API',
            'value' => 'true',
            'description' => "Habilitar o deshabilitar el uso de la API de OpenAI para los análisis."
        ]);

        Parameter::create([
            'key' => 'OPENAI_API_KEY'
        ]);

        Parameter::create([
            'key' => 'OPENAI_ORGANIZATION'
        ]);

        Parameter::create([
            'key' => 'OPENAI_MODEL',
            'value' => 'gpt-3.5-turbo'
        ]);

        Parameter::create([
            'key' => 'JOBS_FOR_TESTING',
            'description' => "Cantidad de arhivos a enviar por análisis para reducir el tiempo de ejecución de pruebas. Dejar en blanco para desactivar el modo de prueba."
        ]);

        Parameter::create([
            'key' => 'JOBS_INTERVAL',
            'value' => '40',
            'description' => "Intervalo de tiempo en segundos entre envíos de prompts."
        ]);

        Parameter::create([
            'key' => 'PROMPT',
            'value' => "Identificar vulnerabilidades del OWASP Top 10-2021 en el siguiente código:\n:code\nResponder en español. No dar explicaciones ni comentarios. Proporcionar los datos de cada vulnerabilidad detectada en formato JSON con la siguiente estructura:\n[{\n\t'Nombre de vulnerabilidad':'',\n\t'Descripción':'',\n\t'Línea de código':'',\n\t'Severidad':'',\n\t'Impacto':'',\n\t'CWE': '',\n\t'CVE': '',\n\t'Solución': '',\n\t'Mitigación': ''\n}]\nEn descripción, no superar 500 caracteres.\nEn severidad, los valores solo pueden ser: Crítica, Alta, Media o Baja.\nEn mitigación, proporcionar código ejemplo.\nDevolver JSON."
        ]);

        // Languages
        $languages = [
            'AsciiDoc',
            'ASP.NET',
            'Assembly',
            'AWK',
            'Ballerina',
            'Bash',
            'Batch',
            'BibTeX',
            'C',
            'C Shell',
            'C#',
            'C++',
            'Cap\'n Proto',
            'Clojure',
            'ClojureScript',
            'CMake',
            'Common Lisp',
            'CSS',
            'CSV',
            'D',
            'Dart',
            'Elixir',
            'Elm',
            'Erlang',
            'Fortran',
            'Go',
            'Groovy',
            'Haskell',
            'HTML',
            'INI',
            'Java',
            'JavaScript',
            'JSON',
            'JSP',
            'Julia',
            'Korn Shell',
            'Kotlin',
            'LaTeX',
            'Log',
            'Lua',
            'Make',
            'Markdown',
            'MATLAB',
            'Nim',
            'OCaml',
            'Pascal',
            'Perl',
            'PHP',
            'PowerShell',
            'Prolog',
            'Protocol Buffer',
            'Python',
            'R',
            'reStructuredText',
            'Ruby',
            'Rust',
            'SAS',
            'Scala',
            'Scheme',
            'Sed',
            'SQL',
            'Stata',
            'Svelte',
            'Swift',
            'TeX',
            'Text',
            'TOML',
            'Twig',
            'TypeScript',
            'V',
            'Visual Basic',
            'Vue',
            'XML',
            'YAML'
        ];

        $data = array_map(function ($language) {
            return ['name' => $language];
        }, $languages);

        DB::table('languages')->insert($data);

        // Extensions
        $extensions = [
            ['extension' => '.adoc', 'language' => 'AsciiDoc'],
            ['extension' => '.asmx', 'language' => 'ASP.NET'],
            ['extension' => '.asp', 'language' => 'ASP.NET'],
            ['extension' => '.asm', 'language' => 'Assembly'],
            ['extension' => '.awk', 'language' => 'AWK'],
            ['extension' => '.bal', 'language' => 'Ballerina'],
            ['extension' => '.sh', 'language' => 'Bash'],
            ['extension' => '.bash', 'language' => 'Bash'],
            ['extension' => '.bat', 'language' => 'Batch'],
            ['extension' => '.bibtex', 'language' => 'BibTeX'],
            ['extension' => '.c', 'language' => 'C'],
            ['extension' => '.h', 'language' => 'C'],
            ['extension' => '.csh', 'language' => 'C Shell'],
            ['extension' => '.cs', 'language' => 'C#'],
            ['extension' => '.cpp', 'language' => 'C++'],
            ['extension' => '.hpp', 'language' => 'C++'],
            ['extension' => '.capnp', 'language' => 'Cap\'n Proto'],
            ['extension' => '.clj', 'language' => 'Clojure'],
            ['extension' => '.cljs', 'language' => 'ClojureScript'],
            ['extension' => '.cmake', 'language' => 'CMake'],
            ['extension' => '.lisp', 'language' => 'Common Lisp'],
            ['extension' => '.css', 'language' => 'CSS'],
            ['extension' => '.csv', 'language' => 'CSV'],
            ['extension' => '.d', 'language' => 'D'],
            ['extension' => '.dart', 'language' => 'Dart'],
            ['extension' => '.ex', 'language' => 'Elixir'],
            ['extension' => '.exs', 'language' => 'Elixir'],
            ['extension' => '.elm', 'language' => 'Elm'],
            ['extension' => '.erl', 'language' => 'Erlang'],
            ['extension' => '.hrl', 'language' => 'Erlang'],
            ['extension' => '.epp', 'language' => 'Erlang'],
            ['extension' => '.f', 'language' => 'Fortran'],
            ['extension' => '.f90', 'language' => 'Fortran'],
            ['extension' => '.go', 'language' => 'Go'],
            ['extension' => '.groovy', 'language' => 'Groovy'],
            ['extension' => '.hs', 'language' => 'Haskell'],
            ['extension' => '.html', 'language' => 'HTML'],
            ['extension' => '.ini', 'language' => 'INI'],
            ['extension' => '.java', 'language' => 'Java'],
            ['extension' => '.js', 'language' => 'JavaScript'],
            ['extension' => '.json', 'language' => 'JSON'],
            ['extension' => '.jsp', 'language' => 'JSP'],
            ['extension' => '.julia', 'language' => 'Julia'],
            ['extension' => '.ksh', 'language' => 'Korn Shell'],
            ['extension' => '.kt', 'language' => 'Kotlin'],
            ['extension' => '.latex', 'language' => 'LaTeX'],
            ['extension' => '.log', 'language' => 'Log'],
            ['extension' => '.lua', 'language' => 'Lua'],
            ['extension' => '.make', 'language' => 'Make'],
            ['extension' => '.md', 'language' => 'Markdown'],
            ['extension' => '.m', 'language' => 'MATLAB'],
            ['extension' => '.nim', 'language' => 'Nim'],
            ['extension' => '.ml', 'language' => 'OCaml'],
            ['extension' => '.pas', 'language' => 'Pascal'],
            ['extension' => '.perl', 'language' => 'Perl'],
            ['extension' => '.php', 'language' => 'PHP'],
            ['extension' => '.php3', 'language' => 'PHP'],
            ['extension' => '.php4', 'language' => 'PHP'],
            ['extension' => '.php5', 'language' => 'PHP'],
            ['extension' => '.phtml', 'language' => 'PHP'],
            ['extension' => '.ps1', 'language' => 'PowerShell'],
            ['extension' => '.pl', 'language' => 'Prolog'],
            ['extension' => '.proto', 'language' => 'Protocol Buffer'],
            ['extension' => '.py', 'language' => 'Python'],
            ['extension' => '.r', 'language' => 'R'],
            ['extension' => '.rst', 'language' => 'reStructuredText'],
            ['extension' => '.rb', 'language' => 'Ruby'],
            ['extension' => '.rs', 'language' => 'Rust'],
            ['extension' => '.sas', 'language' => 'SAS'],
            ['extension' => '.stp', 'language' => 'SAS'],
            ['extension' => '.scala', 'language' => 'Scala'],
            ['extension' => '.ss', 'language' => 'Scheme'],
            ['extension' => '.scm', 'language' => 'Scheme'],
            ['extension' => '.sed', 'language' => 'Sed'],
            ['extension' => '.sql', 'language' => 'SQL'],
            ['extension' => '.do', 'language' => 'Stata'],
            ['extension' => '.ado', 'language' => 'Stata'],
            ['extension' => '.svelte', 'language' => 'Svelte'],
            ['extension' => '.swift', 'language' => 'Swift'],
            ['extension' => '.tex', 'language' => 'TeX'],
            ['extension' => '.txt', 'language' => 'Text'],
            ['extension' => '.toml', 'language' => 'TOML'],
            ['extension' => '.twig', 'language' => 'Twig'],
            ['extension' => '.ts', 'language' => 'TypeScript'],
            ['extension' => '.v', 'language' => 'V'],
            ['extension' => '.vb', 'language' => 'Visual Basic'],
            ['extension' => '.vbs', 'language' => 'Visual Basic'],
            ['extension' => '.vue', 'language' => 'Vue'],
            ['extension' => '.xml', 'language' => 'XML'],
            ['extension' => '.yaml', 'language' => 'YAML'],
            ['extension' => '.yml', 'language' => 'YAML']
        ];

        $data = [];

        foreach ($extensions as $extension) {
            $languageId = DB::table('languages')
                ->where('name', $extension['language'])
                ->value('id');

            if ($languageId) {
                $data[] = [
                    'suffix' => $extension['extension'],
                    'language_id' => $languageId,
                ];
            } else {
                // Handle missing language (optional logging/error)
                \Log::warning("Language not found: " . $extension['language']);
            }
        }

        DB::table('extensions')->insert($data);
    }
}
