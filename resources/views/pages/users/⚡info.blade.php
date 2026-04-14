<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use App\Models\User;
use App\Models\Document;
use Livewire\WithPagination;
use App\Mail\ApproveMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

new class extends Component {
    use WithPagination;

    #[Url]
    #[Validate('required|integer|exists:users,id')]
    public $id;

    public $usuario = null;

    public function mount()
    {
        // 1. Validamos manualmente para poder controlar el fallo
        $validador = validator(
            ['id' => $this->id],
            [
                'id' => 'required|integer|exists:users,id',
            ],
        );

        // 2. Ahora sí existe la variable $validador
        if ($validador->fails()) {
            $this->dispatch('notify', type: 'error', message: 'Usuario no existe');
        } else {
            $this->usuario = User::find($this->id);
        }
    }

    public function approveUser()
    {
        $rolDocente = Role::where('name', 'docente')->firstOrFail();
        $rolInactivo = Role::where('name', 'inactivo')->firstOrFail();

        // Quitar rol inactivo y asignar docente
        $this->usuario->removeRole($rolInactivo);
        $this->usuario->assignRole($rolDocente);

        Mail::to($this->usuario->email)->send(new ApproveMail($this->usuario));

        return $this->redirectRoute('manage.users');
    }

    public function returnBack()
    {
        return $this->redirectRoute('manage.users');
    }
};
?>

<div>
    <div class="w-full flex justify-between">
        <h2><button class="p-2 bg-ues flex text-white rounded-lg cursor-pointer" wire:click="returnBack">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>

                <span class="ml-3"> Administrar Usuairos</span>
            </button></h2>

        <div class="flex gap-2">
            @if ($this->usuario->hasRole('docente'))
                <a href="{{ route('credenciales', ['docenteId' => $this->usuario->id]) }}" wire:navigate
                    class="p-2 bg-zinc-600 flex text-white rounded-lg cursor-pointer hover:opacity-90">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    <span class="ml-2">Revisar Credenciales</span>
                </a>
            @endif

            @if ($this->usuario->hasRole('inactivo'))
                <button class="p-2 bg-ues flex text-white rounded-lg cursor-pointer" wire:click="approveUser">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <span class="ml-3">Aprobar Usuario</span>
                </button>
            @endif


        </div>
    </div>


    <h3 class="text-4xl flex  items-center mt-4"> <svg xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
        </svg>
        <span class="font-bold ml-3">Informacion </span>
    </h3>

    <div class="grid grid-cols-3 gap-4 shadow-xl p-4 rounded-xl ">
        <div class="grid grid-cols-1">
            <label class="font-bold">Nombres:</label>
            <p>{{ $this->usuario->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Apellidos:</label>
            <p>{{ $this->usuario->apellidos }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Sexo:</label>
            <p>{{ $this->usuario->selectedSex->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Fecha De Nacimiento:</label>
            <p>{{ $this->usuario->fecha_nacimiento }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Nacionalidad:</label>
            <p>{{ $this->usuario->selectedNacionalidad->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Estado Civil:</label>
            <p>{{ $this->usuario->estadoCivil->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Conyugue:</label>
            <p>{{ $this->usuario->conyugue }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Direccion:</label>
            <p>{{ strlen($this->usuario->direccion) > 0 ? $this->usuario->direccion : '-' }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Telefono:</label>
            <p>{{ $this->usuario->telefono }}</p>
        </div>
    </div>


    <h3 class="text-4xl flex  items-center mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>

        <span class="font-bold ml-3">Documentos </span>
    </h3>

    <div class="grid grid-cols-1   shadow-xl p-4 rounded-xl ">
        <div
            class="overflow-hidden w-full mt-5 overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
            <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
                <thead
                    class="border-b bg-ues text-white text-sm  dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
                    <tr>
                        <th scope="col" class="p-4">Tipo Document</th>
                        <th scope="col" class="p-4">Numero Documento</th>
                        <th scope="col" class="p-4">Fecha Expedicion</th>
                        <th scope="col" class="p-4">Lugar Expedicion</th>
                        <th scope="col" class="p-4">Fecha Expiracion</th>
                        <th scope="col" class="p-4">Institucion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->usuario->documents as $document)
                        <tr>
                            <td class="p-4">{{ $document->documentType->name }}</td>
                            <td class="p-4">{{ $document->value }}</td>
                            <td class="p-4">{{ $document->fecha_expedicion ?? '-' }}</td>
                            <td class="p-4">{{ $document->lugar_expedicion ?? '-' }}</td>
                            <td class="p-4">{{ $document->fecha_expiracion ?? '-' }}</td>
                            <td class="p-4">{{ $document->institucion ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4">Records not found</td>

                        </tr>
                    @endforelse


                </tbody>
            </table>
            {{-- {{ $this->userDocuments->links() }} --}}
        </div>
    </div>

    <h3 class="text-4xl flex  items-center mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
        </svg>


        <span class="font-bold ml-3">Datos Academicos </span>
    </h3>

    <div class="grid grid-cols-3 gap-4 shadow-xl p-4 rounded-xl ">
        <div class="grid grid-cols-1">
            <label class="font-bold">Grado Academico:</label>
            <p>{{ $this->usuario->institution->grado->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Institucion:</label>
            <p>{{ $this->usuario->institution->institucion->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Fecha De Graduacion:</label>
            <p>{{ $this->usuario->institution->fecha_graduacion }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Escuela o Unidad:</label>
            <p>{{ $this->usuario->institution->escuela->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Categoria Escalafonaria:</label>
            <p>{{ $this->usuario->institution->categoria->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Area de desempeño:</label>
            <p>{{ $this->usuario->institution->area->name }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Fecha de ingreso a la UES:</label>
            <p>{{ $this->usuario->institution->fecha_ingreso ?? '-' }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Tipo de nombramiento:</label>
            <p>{{ $this->usuario->institution->tipoNombramiento->name ?? '-' }}</p>
        </div>
        <div class="grid grid-cols-1">
            <label class="font-bold">Puntaje Tiempo de Servicio:</label>
            <p>{{ $this->usuario->institution->puntaje_tiempo_servicio ?? '0' }} pts</p>
        </div>

    </div>

</div>
