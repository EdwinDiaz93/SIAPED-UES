<?php

use Livewire\Component;
use App\Livewire\Forms\UserDataForm;
use App\Livewire\Forms\DocumentDataForm;
use App\Models\CatalogValue;
use App\Models\User;
use App\Models\Document;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public UserDataForm $userForm;
    public DocumentDataForm $documentForm;

    public $mask = '';
    public $document_type = 'dui';

    public $sexOptions = [];
    public $nacionalidades = [];
    public $estadosCiviles = [];
    public $documents = [];

    public function saveUser()
    {
        // actualzar usuario
        $user = User::find(auth()->user()->id);
        $this->userForm->validate();
        $user->name = $this->userForm->nombres;
        $user->apellidos = $this->userForm->apellidos;
        $user->fecha_nacimiento = $this->userForm->fecha_nacimiento;
        $user->estado_civil = $this->userForm->estado_civil;
        $user->sexo = $this->userForm->sexo;
        $user->nacionalidad = $this->userForm->nacionalidad;
        $user->conyugue = $this->userForm->conyugue;
        $user->direccion = $this->userForm->direccion;
        $user->telefono = $this->userForm->telefono;
        $user->save();
        $this->dispatch('notify', type: 'success', message: 'Datos de usuario guardados correctamente');
    }

    public function saveDocuments()
    {
        $this->documentForm->validate();

        Document::create([
            'user_id' => auth()->user()->id,
            'document_type_id' => $this->documentForm->document_type,
            'value' => $this->documentForm->value,
            'fecha_expedicion' => $this->documentForm->fecha_expedicion,
            'lugar_expedicion' => $this->documentForm->lugar_expedicion,
            'fecha_expiracion' => $this->documentForm->fecha_expiracion,
            'institucion' => $this->documentForm->institucion,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Documentos de usuario guardados correctamente');
        $this->documentForm->reset();
    }

    public function updatedDocumentFormDocumentType()
    {
        $document = CatalogValue::find($this->documentForm->document_type);
        $this->document_type = $document->value;
    }

    #[Computed]
    public function userDocuments()
    {
        return Document::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(5);
    }

    public function mount()
    {
        $this->userForm = new UserDataForm($this, []);
        $this->documentForm = new DocumentDataForm($this, []);

        $user = User::find(auth()->user()->id);
        if ($user != null) {
            $this->userForm->nombres = $user->name;
            $this->userForm->apellidos = $user->apellidos;
            $this->userForm->sexo = $user->sexo;
            $this->userForm->fecha_nacimiento = $user->fecha_nacimiento;
            $this->userForm->nacionalidad = $user->nacionalidad;
            $this->userForm->estado_civil = $user->estado_civil;
            $this->userForm->conyugue = $user->conyugue;
            $this->userForm->direccion = $user->direccion;
            $this->userForm->telefono = $user->telefono;
        }

        $this->sexOptions = CatalogValue::where('catalog_type_id', 1)->get();
        $this->nacionalidades = CatalogValue::where('catalog_type_id', 2)->get();
        $this->estadosCiviles = CatalogValue::where('catalog_type_id', 3)->get();
        $this->documents = CatalogValue::where('catalog_type_id', 4)->get();

        $sexOption = CatalogValue::where(['catalog_type_id' => 1, 'value' => 'M'])->get();
        $this->userForm->sexo = $sexOption[0]->id;

        $nacionalidad = CatalogValue::where(['catalog_type_id' => 2, 'value' => 'SV'])->get();
        $this->userForm->nacionalidad = $nacionalidad[0]->id;

        $estadoCivil = CatalogValue::where(['catalog_type_id' => 3, 'value' => 'S'])->get();
        $this->userForm->estado_civil = $estadoCivil[0]->id;

        $documento = CatalogValue::where(['catalog_type_id' => 4, 'value' => 'dui'])->get();
        $this->documentForm->document_type = $documento[0]->id;
    }
};
?>

<div class="p-2">
    <div x-data="{ selectedTab: 'personales' }" class="w-full">
        <div x-on:keydown.right.prevent="$focus.wrap().next()" x-on:keydown.left.prevent="$focus.wrap().previous()"
            class="flex gap-2 overflow-x-auto border-b border-outline dark:border-outline-dark" role="tablist"
            aria-label="tab options">
            <button x-on:click="selectedTab = 'personales'" x-bind:aria-selected="selectedTab === 'personales'"
                x-bind:tabindex="selectedTab === 'personales' ? '0' : '-1'"
                x-bind:class="selectedTab === 'personales' ?
                    'font-bold  bg-ues text-white  border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelGroups">Datos
                Personales</button>
            <button x-on:click="selectedTab = 'documentos'" x-bind:aria-selected="selectedTab === 'documentos'"
                x-bind:tabindex="selectedTab === 'documentos' ? '0' : '-1'"
                x-bind:class="selectedTab === 'documentos' ?
                    'font-bold  bg-ues text-white  border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="h-min px-4 py-2 text-sm" type="button" role="tab"
                aria-controls="tabpanelLikes">Documentos</button>
            <button x-on:click="selectedTab = 'institucionales'"
                x-bind:aria-selected="selectedTab === 'institucionales'"
                x-bind:tabindex="selectedTab === 'institucionales' ? '0' : '-1'"
                x-bind:class="selectedTab === 'institucionales' ?
                    'font-bold  bg-ues text-white  border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelComments">Datos
                Institucionales</button>

        </div>
        <div class="px-2 py-4 text-on-surface dark:text-on-surface-dark">
            <div x-cloak x-show="selectedTab === 'personales'" id="tabpanelGroups" role="tabpanel"
                aria-label="personales">
                <form id="dataForm" wire:submit.prevent="saveUser" class="flex flex-col w-full">

                    <div class="flex flex-row w-full">
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Nombres:</label>
                            <input wire:model="userForm.nombres" type="text"
                                class=" p-2 border rounded-lg border-ues w-full">
                            @error('userForm.nombres')
                                <span class="error">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Apellidos:</label>
                            <input type="text" wire:model="userForm.apellidos"
                                class=" p-2 border rounded-lg border-ues w-full">
                            @error('userForm.apellidos')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="flex flex-col w-120 mx-1">
                            <label class="font-bold">Sexo:</label>

                            <select wire:model='userForm.sexo' class="p-[0.55rem] border rounded-lg border-ues w-full">
                                @forelse ($this->sexOptions as $option)
                                    <option value={{ $option->id }}>{{ $option->name }}</option>
                                @empty
                                    <option value={{ null }}>--No Option--</option>
                                @endforelse
                            </select>
                            @error('userForm.sexo')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>



                    </div>

                    <div class="flex flex-row w-full mt-5">

                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Fecha Nacimiento:</label>
                            <input type="date" wire:model='userForm.fecha_nacimiento'
                                class=" p-2 border rounded-lg border-ues w-full">
                            @error('userForm.fecha_nacimiento')
                                <span class="error">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Nacionalidad:</label>

                            <select wire:model='userForm.nacionalidad'
                                class="p-[0.55rem] border rounded-lg border-ues w-full">
                                @forelse ($this->nacionalidades as $nacionalidad)
                                    <option value={{ $nacionalidad->id }}>{{ $nacionalidad->name }}</option>
                                @empty
                                    <option value={{ null }}>--No Option--</option>
                                @endforelse
                            </select>
                            @error('userForm.nacionalidad')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Estado Civil:</label>

                            <select wire:model='userForm.estado_civil'
                                class="p-[0.55rem] border rounded-lg border-ues w-full">
                                @forelse ($this->estadosCiviles as $estadoCivil)
                                    <option value={{ $estadoCivil->id }}>{{ $estadoCivil->name }}</option>
                                @empty
                                    <option value={{ null }}>--No Option--</option>
                                @endforelse
                            </select>
                            @error('userForm.estado_civil')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="flex flex-row w-full mt-5">

                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Conyugue:</label>
                            <input wire:model='userForm.conyugue' type="text"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Direccion:</label>
                            <input wire:model='userForm.direccion' type="text"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Telefono:</label>
                            <input x-mask="9999-9999" wire:model='userForm.telefono' type="text"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>

                    </div>

                    <div class="w-full mt-10">

                        <button form="dataForm"
                            class="p-2 w-60 bg-ues text-white border-white  cursor-pointer  font-bold border  rounded-lg  "
                            type="submit">Guardar</button>
                    </div>
                </form>
            </div>
            <div x-cloak x-show="selectedTab === 'documentos'" id="tabpanelLikes" role="tabpanel"
                aria-label="documentos">
                <form id="documentForm" wire:submit.prevent="saveDocuments" class="flex flex-col w-full">

                    <div class="flex flex-row w-full">
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Tipo Documento:</label>


                            <select wire:model.live='documentForm.document_type'
                                class="p-[0.55rem] border rounded-lg border-ues w-full">
                                @forelse ($this->documents as $document)
                                    <option value={{ $document->id }}>{{ $document->name }}</option>
                                @empty
                                    <option value={{ null }}>--No Option--</option>
                                @endforelse
                            </select>
                            @error('documentForm.document_type')
                                <span class="error">{{ $message }}</span>
                            @enderror

                        </div>
                        <div x-data="{ document_type: @entangle('document_type').live }" class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Numero Documento :</label>
                            <input
                                x-mask:dynamic="
                document_type === 'dui' ? '99999999-9' :
                (document_type === 'nit' ? '9999-999999-999-9' : '')
            "
                                type="text" wire:model="documentForm.value"
                                class=" p-2 border rounded-lg border-ues w-full">
                            @error('documentForm.value')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col w-120 mx-1">
                            <label class="font-bold">Fecha De Expedicion:</label>
                            <input type="date" wire:model="documentForm.fecha_expedicion"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>

                        <div class="flex flex-col w-120 mx-1">
                            <label class="font-bold">Lugar De Expedicion:</label>
                            <input type="text" wire:model="documentForm.lugar_expedicion"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>

                    </div>

                    <div class="flex flex-row w-full mt-6">
                        <div class="flex flex-col w-120 mx-1">
                            <label class="font-bold">Fecha De Expiracion:</label>
                            <input type="date" wire:model="documentForm.fecha_expiracion"
                                class=" p-2 border rounded-lg border-ues w-full">
                        </div>



                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Institucion:</label>

                            <input type="text" wire:model='documentForm.institucion'
                                class=" p-2 border rounded-lg border-ues w-full">

                        </div>
                        <div class="flex flex-col w-120 mx-1 ">

                            <button form="documentForm"
                                class="p-2 w-60 bg-ues text-white border-white  cursor-pointer  font-bold border  rounded-lg  mt-6"
                                type="submit">Guardar</button>
                        </div>

                    </div>



                </form>
                <div
                    class="overflow-hidden w-full mt-5 overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
                    <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
                        <thead
                            class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
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
                            @forelse ($this->userDocuments as $document)
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
                    {{ $this->userDocuments->links() }}
                </div>

            </div>
            <div x-cloak x-show="selectedTab === 'institucionales'" id="tabpanelComments" role="tabpanel"
                aria-label="institucionales">
                institucionales
            </div>

        </div>
    </div>

</div>
