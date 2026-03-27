<?php

use Livewire\Component;
use App\Livewire\Forms\UserDataForm;
use App\Models\CatalogValue;
use Livewire\Attributes\Validate;

new class extends Component {
    //
    public UserDataForm $userForm;
    public $sexOptions = [];

    public function saveUser()
    {
        $this->userForm->validate();
    }

    public function mount()
    {
        $this->userForm = new UserDataForm($this, []);
        $this->sexOptions = CatalogValue::where('catalog_type_id', 1)->get();
        $sexOption = $this->sexOptions[array_key_first($this->sexOptions->toArray())];
        $this->userForm->sexo = $sexOption->id;
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
                            <input type="date" wire:model='userForm.birth_date' class=" p-2 border rounded-lg border-ues w-full">
                            @error('userForm.birth_date')
                                <span class="error">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Nacionalidad:</label>
                            <input type="text" class=" p-2 border rounded-lg border-ues w-full">
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Estado Civil:</label>
                            <input type="text" class=" p-2 border rounded-lg border-ues w-full">
                        </div>

                    </div>

                    <div class="flex flex-row w-full mt-5">
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Conyugue:</label>
                            <input type="text" class=" p-2 border rounded-lg border-ues w-full">
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Direccion:</label>
                            <input type="text" class=" p-2 border rounded-lg border-ues w-full">
                        </div>
                        <div class="flex flex-col w-120 mx-1 ">
                            <label class="font-bold">Telefono:</label>
                            <input type="text" class=" p-2 border rounded-lg border-ues w-full">
                        </div>

                    </div>

                    <div class="w-full mt-10">

                        <button form="dataForm" class="p-2 w-60 font-bold border border-ues rounded-lg  "
                            type="submit">Guardar</button>
                    </div>
                </form>
            </div>
            <div x-cloak x-show="selectedTab === 'documentos'" id="tabpanelLikes" role="tabpanel"
                aria-label="documentos">
                documentos
            </div>
            <div x-cloak x-show="selectedTab === 'institucionales'" id="tabpanelComments" role="tabpanel"
                aria-label="institucionales">
                institucionales
            </div>

        </div>
    </div>

</div>
