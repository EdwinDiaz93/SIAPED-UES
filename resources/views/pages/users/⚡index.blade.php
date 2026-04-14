<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $selectedRol = null;

    #[Computed]
    public function users()
    {
        $inactivo = Role::where('id', $this->selectedRol)->first();
        return User::role($inactivo->name)
            ->where('id', '!=', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);
    }
    #[Computed]
    public function roles()
    {
        return Role::all();
    }

    public function mount()
    {
        $inactivo = Role::where('name', 'inactivo')->first();
        $this->selectedRol = $inactivo->id;
    }

    public function reviewInfo($id)
    {
        $this->redirectRoute('users.info', ['id' => $id]);
    }

    public function updatedSelectedRol()
    {
        $this->resetPage();
    }
};
?>

<div>
    <div class="relative flex w-full max-w-xs flex-col gap-1 text-on-surface dark:text-on-surface-dark">
        <label for="os" class="w-fit pl-0.5 text-sm">Filtrar Por Rol</label>

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
            class="absolute pointer-events-none right-4 top-8 size-5">
            <path fill-rule="evenodd"
                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                clip-rule="evenodd" />
        </svg>
        <select id="rol" name="rol" wire:model.live='selectedRol'
            class="w-full appearance-none rounded-radius border border-outline bg-surface-alt px-4 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:cursor-not-allowed disabled:opacity-75 dark:border-outline-dark dark:bg-surface-dark-alt/50 dark:focus-visible:outline-primary-dark">
            @forelse ($this->roles as $rol)
                <option {{ $rol->id === $this->selectedRol ? 'selected' : '' }} value={{ $rol->id }}>
                    {{ $rol->name }}</option>

            @empty
            @endforelse

        </select>
    </div>

    <div
        class="overflow-hidden w-full mt-5 overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead
                class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
                <tr>
                    <th scope="col" class="p-4 text-center">Nombres</th>
                    <th scope="col" class="p-4 text-center">Apellidos</th>
                    <th scope="col" class="p-4 text-center">Email</th>
                    <th scope="col" class="p-4 text-center">Telefono</th>

                    <th scope="col" class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->users as $user)
                    <tr>
                        <td class="p-4 text-center">{{ $user->name ?? '-' }}</td>
                        <td class="p-4 text-center">{{ $user->apellidos ?? '-' }}</td>
                        <td class="p-4 text-center">{{ $user->email ?? '-' }}</td>
                        <td class="p-4 text-center">{{ $user->telefono ?? '-' }}</td>
                        <td class="p-4 text-center">
                            {{-- @switch($user->getRoleNames()[0]) --}}
                                {{-- @case('inactivo') --}}
                                    <div class="relative w-fit">
                                        <button type="button" wire:click="reviewInfo('{{$user->id}}')"
                                            class="peer rounded-radius bg-cyan-500 cursor-pointer p-2 font-medium tracking-wide text-white  focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary dark:bg-surface-dark-alt dark:border-surface-dark-alt dark:text-on-surface-dark dark:focus-visible:outline-primary-dark"
                                            aria-describedby="tooltipExample">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltipExample"
                                            class="absolute -top-9 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap rounded-sm bg-surface-dark px-2 py-1 text-center text-sm text-on-surface-dark-strong opacity-0 transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100 dark:bg-surface dark:text-on-surface-strong"
                                            role="tooltip">Revisar Informacion</div>
                                    </div>
                                {{-- @break

                                @default
                            @endswitch --}}
                        </td>

                    </tr>
                    @empty
                        <tr class="p-4">
                            Records Not found
                        </tr>
                    @endforelse

                    {{ $this->users->links() }}
                </tbody>
            </table>
        </div>

    </div>
