<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\CatalogType;
use App\Models\CatalogValue;
use Illuminate\Validation\Rule;

new class extends Component {
    use WithPagination;

    #[Url]
    public $catalogTypeId = null;

    // --- Estado del modal ---
    public bool $showModal = false;
    public ?int $editingId = null;

    // --- Campos del formulario ---
    public $name  = '';
    public $value = '';

    public function mount()
    {
        // Nunca debe entrarse a esta pantalla sin un catálogo preseleccionado.
        if (! $this->catalogTypeId || ! CatalogType::find($this->catalogTypeId)) {
            $this->catalogTypeId = optional(CatalogType::orderBy('name')->first())->id;
        }
    }

    protected function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('catalog_values', 'value')
                    ->where('catalog_type_id', $this->catalogTypeId)
                    ->ignore($this->editingId),
            ],
        ];
    }

    protected $messages = [
        'name.required'  => 'El nombre es requerido.',
        'value.required' => 'El valor es requerido.',
        'value.unique'   => 'Ya existe un elemento con ese valor en este catálogo.',
    ];

    #[Computed]
    public function catalogTypes()
    {
        return CatalogType::orderBy('name')->get();
    }

    #[Computed]
    public function selectedCatalogType()
    {
        return CatalogType::find($this->catalogTypeId);
    }

    #[Computed]
    public function catalogValues()
    {
        if (! $this->catalogTypeId) {
            return CatalogValue::whereRaw('1 = 0')->paginate(10);
        }

        return CatalogValue::where('catalog_type_id', $this->catalogTypeId)
            ->orderBy('name')
            ->paginate(10);
    }

    public function updatedCatalogTypeId()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $catalogValue = CatalogValue::where('catalog_type_id', $this->catalogTypeId)->findOrFail($id);
        $this->editingId = $catalogValue->id;
        $this->name      = $catalogValue->name;
        $this->value     = $catalogValue->value;
        $this->showModal = true;
    }

    public function save()
    {
        if (! $this->catalogTypeId) {
            $this->addError('catalogTypeId', 'Debe seleccionar un catálogo.');
            return;
        }

        $this->validate();

        $data = [
            'name'            => $this->name,
            'value'           => $this->value,
            'catalog_type_id' => $this->catalogTypeId,
        ];

        if ($this->editingId) {
            CatalogValue::where('catalog_type_id', $this->catalogTypeId)->findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Elemento actualizado correctamente.');
        } else {
            CatalogValue::create($data);
            $this->dispatch('notify', type: 'success', message: 'Elemento creado correctamente.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        CatalogValue::where('catalog_type_id', $this->catalogTypeId)->findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Elemento eliminado.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name      = '';
        $this->value     = '';
        $this->resetValidation();
    }
};
?>

<div class="p-4">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Catálogos</h1>
        <button wire:click="openCreate" @if (! $catalogTypeId) disabled @endif
            class="flex items-center gap-2 px-4 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo Elemento
        </button>
    </div>

    {{-- Selector de catálogo --}}
    <div class="mb-4 flex flex-col gap-1 max-w-xs">
        <label class="font-semibold text-sm">Catálogo</label>
        <select wire:model.live="catalogTypeId"
            class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
            @foreach ($this->catalogTypes as $catalogType)
                <option value="{{ $catalogType->id }}">{{ $catalogType->name }}</option>
            @endforeach
        </select>
        @error('catalogTypeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead class="border-b border-outline bg-ues text-white dark:border-outline-dark">
                <tr>
                    <th class="p-4">Nombre</th>
                    <th class="p-4">Valor</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->catalogValues as $catalogValue)
                    <tr class="hover:bg-surface-alt dark:hover:bg-surface-dark-alt">
                        <td class="p-4 font-medium">{{ $catalogValue->name }}</td>
                        <td class="p-4">{{ $catalogValue->value }}</td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Editar --}}
                                <button wire:click="openEdit({{ $catalogValue->id }})" title="Editar"
                                    class="p-1 rounded text-blue-600 hover:bg-blue-50 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                {{-- Eliminar --}}
                                <button wire:click="delete({{ $catalogValue->id }})"
                                    wire:confirm="¿Confirma que desea eliminar este elemento?"
                                    title="Eliminar"
                                    class="p-1 rounded text-gray-500 hover:bg-gray-100 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-400">No hay elementos registrados en este catálogo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->catalogValues->links() }}
    </div>

    {{-- Modal crear / editar --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg p-6">

                <h2 class="text-xl font-bold mb-4">
                    {{ $editingId ? 'Editar Elemento' : 'Nuevo Elemento' }} — {{ $this->selectedCatalogType?->name }}
                </h2>

                <form wire:submit.prevent="save" class="flex flex-col gap-4">

                    <div>
                        <label class="font-semibold text-sm">Nombre</label>
                        <input type="text" wire:model="name"
                            class="w-full mt-1 p-2 border rounded-lg border-ues">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-semibold text-sm">Valor</label>
                        <input type="text" wire:model="value"
                            class="w-full mt-1 p-2 border rounded-lg border-ues">
                        @error('value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
                            {{ $editingId ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
