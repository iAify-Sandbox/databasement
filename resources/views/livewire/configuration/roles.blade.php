<div>
    <x-header :title="__('Configuration')" separator>
        <x-slot:subtitle>
            {{ __('Define what each role can do. Changes apply immediately, no redeploy needed.') }}
        </x-slot:subtitle>
    </x-header>

    @include('livewire.configuration._tabs', ['active' => 'roles'])

    @can('create', \Silber\Bouncer\Database\Role::class)
        <div class="flex justify-end mb-4">
            <x-button :label="__('New role')" icon="o-plus" wire:click="openCreate" class="btn-primary btn-sm" />
        </div>
    @endcan

    <x-card shadow>
        <table class="table table-default">
            <thead>
                <tr>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Abilities') }}</th>
                    <th class="text-center w-28">{{ __('Members') }}</th>
                    <th class="text-right w-28">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td>
                            <div class="font-medium">{{ $role->title ?: $role->name }}</div>
                            <div class="text-sm text-base-content/60 flex items-center gap-1">
                                <code>{{ $role->name }}</code>
                                @if($role->built_in)
                                    <x-badge :value="__('Built-in')" class="badge-ghost badge-xs" />
                                @endif
                            </div>
                        </td>
                        <td>
                            <x-ability-badges :abilities="$role->abilities->pluck('name')->all()" />
                        </td>
                        <td class="text-center">{{ $memberCounts[$role->id] ?? 0 }}</td>
                        <td>
                            <div class="flex gap-2 justify-end">
                                @can('update', $role)
                                    <x-button icon="o-pencil" wire:click="openEdit({{ $role->id }})" :tooltip="__('Edit')" class="btn-ghost btn-sm" />
                                @endcan
                                @can('delete', $role)
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $role->id }})" :tooltip="__('Delete')" class="btn-ghost btn-sm text-error" />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-base-content/50 py-8">{{ __('No roles yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <!-- CREATE / EDIT MODAL -->
    <x-modal wire:model="showFormModal" :title="$editingId ? __('Edit role') : __('New role')" box-class="max-w-2xl" class="backdrop-blur">
        <div class="space-y-4">
            <x-input :label="__('Name')" wire:model="title" :placeholder="__('e.g. Backup Operator')" />

            <div>
                <div class="font-medium mb-2">{{ __('Abilities') }}</div>
                <x-ability-toggles :groups="$abilityGroups" model="abilities" />
            </div>
        </div>

        <x-slot:actions>
            <x-button :label="__('Cancel')" @click="$wire.showFormModal = false" />
            <x-button :label="__('Save')" class="btn-primary" wire:click="save" spinner="save" />
        </x-slot:actions>
    </x-modal>

    <!-- DELETE MODAL -->
    <x-modal wire:model="showDeleteModal" :title="__('Delete role')" class="backdrop-blur">
        <p>{{ __('Are you sure you want to delete this role? Users who have only this role will lose its abilities.') }}</p>
        <x-slot:actions>
            <x-button :label="__('Cancel')" @click="$wire.showDeleteModal = false" />
            <x-button :label="__('Delete')" class="btn-error" wire:click="delete" spinner="delete" />
        </x-slot:actions>
    </x-modal>
</div>
