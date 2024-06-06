<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

new #[Layout("layouts.app")]
class extends Component {
    use Toast;
    public $addDrawer = false;
    public $editDrawerr = false;
    public $usr = [];
    public $role = [
        [
            'id' => 0,
            'name' => 'User'
        ],
        [
            'id' => 1,
            'name' => 'Admin'
        ]
    ];
    protected $access_token;
    protected $update_endpoint = "http://127.0.0.1:8000/api/v1/admin/user/"; //PUT
    protected $usr_endpoint = "http://127.0.0.1:8000/api/v1/admin/get_all_users";
    protected $detail_endpoint = "http://127.0.0.1:8000/api/v1/admin/get_detail_user"; //POST

    #[On("toNextPage")]
    public function nextPage($endpoint)
    {
//        dd($endpoint);
        $this->usr_endpoint = $endpoint;
//        $this->with();
//        dd($this->usr_endpoint);
    }

    #[On("toPrevPage")]
    public function prevPage($endpoint)
    {
        $this->usr_endpoint = $endpoint.'&perPage=10';
    }

    public function updateRole()
    {
        $res = \Illuminate\Support\Facades\Http::put($this->update_endpoint.$this->usr['id'],['token'=>session()->get('access_token'),'role'=>$this->usr['roles']])->json();
        if ($res['status_code'] == 200) {
            $this->success("Updated!");
        }else {
            $this->error("Please try again!");
        }
        $this->reset();
    }

    protected function getUsers()
    {
        $this->access_token = session()->get('access_token');
        $res = \Illuminate\Support\Facades\Http::get($this->usr_endpoint,['token'=>$this->access_token])->json();
//        dd($res);
        return $res['data'];
    }
    public function openEditDrawer($id)
    {
//        dd(session()->get('access_token'));
        $res = \Illuminate\Support\Facades\Http::post($this->detail_endpoint,['token'=>session()->get('access_token'),'id'=>$id])->json();
        $this->usr = $res['data'];
//        dd($this->usr);
        $this->editDrawerr = true;
    }

    public function with(): array
    {
        $data = $this->getUsers();
        $this->dispatch("pagi",data:$data);
//        dd($data);
        return [
            'users' => $data
        ];
    }
}; ?>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management - Manage users with ease.') }}
        </h2>
    </div>
</x-slot>

<div class="py-12">
    <x-ui-drawer
        wire:model="editDrawerr"
        title="Update user information"
        subtitle="Livewire"
        separator
        with-close-button
        close-on-escape
        class="w-11/12 lg:w-1/3"
    >
        <label class="input input-bordered flex items-center gap-2">
            ID
            <input type="text" class="grow" disabled wire:model="usr.id"/>
        </label>

        <x-ui-select label="Privilege" :options="$role" wire:model="usr.roles"/>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.editDrawer = false" />
            <x-ui-button label="Confirm" class="btn-primary" icon="o-check" spinner wire:click="updateRole"/>
        </x-slot:actions>
    </x-ui-drawer>
    <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-y-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-y-hidden">
                <table class="table table-md">
                    <thead class="font-bold text-lg">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th><x-ui-button label="Open Left" wire:click="$toggle('showDrawer1')" /></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users['pagination_data'] as $user)
                        <tr>
{{--                            {{dd($user)}}--}}
                            <th>{{$user['id']}}</th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="{{$user['avatar']}}" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div>{{$user['name']}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{$user['email']}}</td>
                            <td>{{$user['phone_number']}}</td>
                            <td>{{$user['address']}}</td>
                            <td>
                                @if($user['roles'] == 1)
                                    <x-ui-badge value="Admin" class="badge-warning" />
                                @else
                                    <x-ui-badge value="User" class="badge-primary" />
                                @endif
                            </td>
                            <td>
                                <x-ui-button label="Edit" icon="o-pencil-square" responsive class="btn-warning" spinner wire:click="openEditDrawer({{$user['id']}})"/>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>No users found!</td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>
        <livewire:pagination :data="$users"/>
    </div>
</div>
