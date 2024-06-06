<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

new #[Layout("layouts.app")]
#[Title("Manufacturers management")]
class extends Component {
    //
    use Toast;

    protected $manu_endpoint = "http://127.0.0.1:8000/api/v1/manufacturers?perPage=5";
    protected $detail_endpoint = "http://127.0.0.1:8000/api/v1/manufacturers/";
    protected $update_endpoint = "http://127.0.0.1:8000/api/v1/manufacturers/"; //PUT
    protected $add_endpoint = "http://127.0.0.1:8000/api/v1/manufacturers"; //POST
    public $manu = [];
    public $editDrawer = false;
    public $addDrawer = false;

    #[On("toNextPage")]
    public function nextPage($endpoint)
    {
        $this->manu_endpoint = $endpoint.'&perPage=5';
//        dd($endpoint);
    }
    #[On("toPrevPage")]
    public function prevPage($endpoint)
    {
        $this->manu_endpoint = $endpoint.'&perPage=5';
    }
    public function addManu()
    {
        $res = \Illuminate\Support\Facades\Http::post($this->add_endpoint,['name'=>$this->manu['name'],'web_image'=>$this->manu['image']])->json();
        if ($res['status_code'] == 200) {
            $this->success("Added!");
        }else {
            $this->error("Please try again!");
        }
    }
    public function updateManu()
    {
        $manu_id = $this->manu['id'];
        $res = \Illuminate\Support\Facades\Http::put($this->update_endpoint.$manu_id,['name'=>$this->manu['name'],'web_image'=>$this->manu['image']])->json();
        if ($res['status'] == 202) {
           $this->success("Updated!");
        }else {
           $this->error("Please try again!",position: 'toast-top toast-end');
        }
        $this->editDrawer = false;
    }

    public function openEditDrawer($id)
    {
        $res = \Illuminate\Support\Facades\Http::get($this->detail_endpoint.$id)->json();
        $this->manu = $res['data'];
//        dd($this->manu);
        $this->editDrawer = true;
    }

    public function openAddDrawer()
    {
        $this->addDrawer = true;
    }
    protected function getManus()
    {
        $res = \Illuminate\Support\Facades\Http::get($this->manu_endpoint)->json();
        return $res['data'];
    }
    public function with():array
    {
        $data = $this->getManus();
        $this->dispatch("pagi",data:$data);
        return [
            'manus' => $data
        ];
    }
}; ?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Manufacturers Management') }}
    </h2>
</x-slot>

<div class="py-12">
    <x-ui-drawer
        wire:model="addDrawer"
        title="Add new manufacturer"
        subtitle="Add new manufacturer associated with your products"
        separator
        right
        with-close-button
        class="w-11/12 lg:w-1/3"
    >
        <div class="flex flex-col gap-2">
            <label class="input input-bordered flex items-center gap-2">
                Manufacturer Name:
                <input type="text" class="grow" wire:model="manu.name"/>
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Image URL:
                <input type="text" class="grow" wire:model="manu.image"/>
            </label>
        </div>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.addDrawer = false" />
            <x-ui-button label="Confirm" class="btn-primary" icon="o-check" wire:click="addManu" spinner/>

        </x-slot:actions>
    </x-ui-drawer>

    <x-ui-drawer
        wire:model="editDrawer"
        title="Edit panel"
        subtitle="Update your manufacturer information"
        separator
        with-close-button
        class="w-11/12 lg:w-1/3"
    >
        <div class="flex flex-col gap-2">
            <label class="input input-bordered flex items-center gap-2">
                ID
                <input type="text" class="grow" disabled wire:model="manu.id"/>
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Manufacturer:
                <input type="text" class="grow" wire:model="manu.name" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Image URL:
                <input type="text" class="grow" wire:model="manu.image"/>
            </label>
        </div>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.editDrawer = false" />
            <x-ui-button label="Update" class="btn-primary" icon="o-check" wire:click="updateManu()" spinner/>
        </x-slot:actions>
    </x-ui-drawer>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-y-hidden">
                <table class="table table-md">
                    <thead class="font-bold text-lg">
                    <tr>
                        <th>Manu ID</th>
                        <th>Manufacturer Image</th>
                        <th>Manufacturer Name</th>
                        <th>Num of associated products</th>
                        <th>
                            <x-ui-button label="Add" icon="o-plus" responsive class="btn-info" spinner wire:click="openAddDrawer()"/>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($manus['pagination_data'] as $manu)
                            <tr>
                                <th>{{$manu['id']}}</th>
                                <td>
                                    <img src="{{$manu['image']}}" alt="Image" class="h-20">
                                </td>
                                <td>{{$manu['name']}}</td>
                                <td>{{$manu['count_products']}}</td>
                                <td>
                                    <x-ui-button label="Edit" icon="o-pencil-square" responsive class="btn-warning" spinner wire:click="openEditDrawer({{$manu['id']}})"/>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>Empty</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>

        </div>
            <livewire:pagination :data="$manus"/>
    </div>
</div>

