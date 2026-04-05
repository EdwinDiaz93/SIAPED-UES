<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
new class extends Component {
    #[Computed]
    public function users()
    {
        return User::role('inactivo')
            ->where('id', '!=', auth()->id()) // Excluye al usuario autenticado
            ->orderBy('id', 'desc')
            ->get();
    }
};
?>

<div>
    <div class="overflow-hidden w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead
                class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
                <tr>
                    <th scope="col" class="p-4 text-center">Nombres</th>
                    <th scope="col" class="p-4 text-center">Apellidos</th>
                    <th scope="col" class="p-4 text-center">Email</th>
                    <th scope="col" class="p-4 text-center">Telefono</th>
                    <th scope="col" class="p-4 text-center"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->users as $user)
                    <tr>
                        <td class="p-4 text-center">{{ $user->name ?? '-'}}</td>
                        <td class="p-4 text-center">{{ $user->apellidos ?? '-'}}</td>
                        <td class="p-4 text-center">{{ $user->email ?? '-'}}</td>
                        <td class="p-4 text-center">{{ $user->telefono ?? '-'}}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr class="p-4">
                        Records Not found
                    </tr>
                @endforelse


            </tbody>
        </table>
    </div>

</div>
