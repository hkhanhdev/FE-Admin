<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

new #[Layout("layouts.app")]
class extends Component {
    use Toast;
    public $addDrawer = false;
    public $editDrawer = false;
    public $prd = [];
    public $manus = [];

    protected $prd_endpoint = "http://127.0.0.1:8000/api/v1/products?perPage=10";
    protected $addPrd_endpoint = "http://127.0.0.1:8000/api/v1/products"; //POST
    protected $update_endpoint = "http://127.0.0.1:8000/api/v1/products/"; //PUT

    #[On("toNextPage")]
    public function nextPage($endpoint)
    {
        $this->prd_endpoint = $endpoint.'&perPage=10';
    }

    #[On("toPrevPage")]
    public function prevPage($endpoint)
    {
        $this->prd_endpoint = $endpoint.'&perPage=10';
    }

    public function addPrd()
    {
//        dd($this->prd);
        $res = \Illuminate\Support\Facades\Http::post($this->addPrd_endpoint,[
            'name' => $this->prd['name'],
            'id_manufacturer' => $this->prd['manufacturer']['id'],
            'image' => $this->prd['image'],
            'os' => $this->prd['os'],
            'ram' => $this->prd['ram'],
            'storage' => $this->prd['storage'],
            'processor' => $this->prd['processor'],
            'selling_price' => $this->prd['selling_price'],
            'display' => $this->prd['display'],
            'original_price' => $this->prd['original_price']
        ])->json();
        if ($res['status_code'] == 200) {
            $this->success("Product Added!");
            $this->reset();
        }else {
            $this->error("Please try again!");
            $this->reset();
        }
    }

    public function updatePrd()
    {
//        dd($this->prd);
        $res = \Illuminate\Support\Facades\Http::put($this->update_endpoint.$this->prd['id'],[
            'name' => $this->prd['name'],
            'manufacturer_id' => (int)$this->prd['manufacturer']['id'],
            'image' => $this->prd['image'],
            'os' => $this->prd['os'],
            'ram' => $this->prd['ram'],
            'storage' => $this->prd['storage'],
            'processor' => $this->prd['processor'],
            'selling_price' => $this->prd['selling_price'],
            'display' => $this->prd['display'],
            'original_price' => $this->prd['original_price']
        ])->json();
        if ($res['status_code'] == 200) {
            $this->success("Updated!");
            $this->reset();
        }else {
            $this->error("Please try again!");
            $this->reset();
        }
    }
    protected function getPrd()
    {
        $res = \Illuminate\Support\Facades\Http::get($this->prd_endpoint)->json();
//        dd($res);
        return $res['data'];
    }
    public function openEditDrawer($id)
    {
//        dd($id);
        $res = \Illuminate\Support\Facades\Http::get("http://127.0.0.1:8000/api/v1/products/".$id)->json();
        $this->prd = $res['data'];
        $manu_res = \Illuminate\Support\Facades\Http::get("http://127.0.0.1:8000/api/v1/manufacturers?perPage=100")->json();
        $this->manus = $manu_res['data']['pagination_data'];
//        dd($this->prd);
        $this->editDrawer = true;
    }
    public function openAddDrawer()
    {
        $this->addDrawer = true;
    }
    public function with(): array
    {
        $data = $this->getPrd();
        $this->dispatch("pagi",data:$data);
        return [
            'products' => $data
        ];
    }
}; ?>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products Management - Keep track of your products effortlessly.') }}
        </h2>

    </div>
</x-slot>

<div class="py-12">
    <x-ui-drawer
        wire:model="addDrawer"
        title="Add new product"
        subtitle="Add new product to your storage"
        separator
        right
        with-close-button
        class="w-11/12 lg:w-1/3"
    >
        <div class="flex flex-col gap-2">
            <label class="input input-bordered flex items-center gap-2">
                Product Name:
                <input type="text" class="grow" wire:model="prd.name"/>
            </label>

            <x-ui-select label="Manufacturers" :options="$manus" wire:model="prd.manufacturer.id" placeholder="List of manufacturers"/>

            <label class="input input-bordered flex items-center gap-2">
                Image URL:
                <input type="text" class="grow" wire:model="prd.image"/>
            </label>

            <label class="input input-bordered flex items-center gap-2">
                OS:
                <input type="text" class="grow" wire:model="prd.os" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                RAM:
                <input type="text" class="grow" wire:model="prd.ram" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Storage:
                <input type="text" class="grow" wire:model="prd.storage" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Display:
                <input type="text" class="grow" wire:model="prd.display" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Processor:
                <input type="text" class="grow" wire:model="prd.processor" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Selling Price:
                <input type="text" class="grow" wire:model="prd.selling_price" />
            </label>
            <label class="input input-bordered flex items-center gap-2">
                Original Price:
                <input type="text" class="grow" wire:model="prd.original_price" />
            </label>
        </div>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.addDrawer = false" />
            <x-ui-button label="Confirm" class="btn-primary" icon="o-check" wire:click="addPrd" spinner/>

        </x-slot:actions>
    </x-ui-drawer>

    <x-ui-drawer
        wire:model="editDrawer"
        title="Edit panel"
        subtitle="Update your product information"
        separator
        with-close-button
        class="w-11/12 lg:w-1/3"
    >
        <div class="flex flex-col gap-2">
            <label class="input input-bordered flex items-center gap-2">
                ID
                <input type="text" class="grow" disabled wire:model="prd.id"/>
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Product Name:
                <input type="text" class="grow" wire:model="prd.name"/>
            </label>
            <x-ui-select label="Manufacturers" :options="$manus" wire:model="prd.manufacturer.id" placeholder="List of manufacturers"/>

            <label class="input input-bordered flex items-center gap-2">
                Image URL:
                <input type="text" class="grow" wire:model="prd.image"/>
            </label>

            <label class="input input-bordered flex items-center gap-2">
                OS:
                <input type="text" class="grow" wire:model="prd.os" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                RAM:
                <input type="text" class="grow" wire:model="prd.ram" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Storage:
                <input type="text" class="grow" wire:model="prd.storage" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Display:
                <input type="text" class="grow" wire:model="prd.display" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Processor:
                <input type="text" class="grow" wire:model="prd.processor" />
            </label>

            <label class="input input-bordered flex items-center gap-2">
                Price:
                <input type="text" class="grow" wire:model="prd.selling_price" />
            </label>
        </div>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.editDrawer = false" />
            <x-ui-button label="Update" class="btn-primary" icon="o-check" wire:click="updatePrd()"/>
        </x-slot:actions>
    </x-ui-drawer>
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-y-hidden">
                <table class="table table-md">
                    <thead class="font-bold text-lg">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Image</th>
                        <th>Manufacturer</th>
                        <th>OS</th>
                        <th>RAM</th>
                        <th>Storage</th>
                        <th>Display</th>
                        <th>Processor</th>
                        <th>Price</th>
                        <th>
                            <x-ui-button label="Add" icon="o-plus" responsive class="btn-info" spinner wire:click="openAddDrawer()"/>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products['pagination_data'] as $product)
                        <tr>
                            <th>{{$product['id']}}</th>
                            <td>{{$product['name']}}</td>
                            <td>
                                <img src="{{$product['image']}}" alt="Image" class="h-20">
                            </td>
                            <td>{{$product['manufacturer']['name']}}</td>
                            <td>{{$product['os']}}</td>
                            <td>{{$product['ram']}}</td>
                            <td>{{$product['storage']}}</td>
                            <td>{{$product['display']}}</td>
                            <td>{{$product['processor']}}</td>
                            <td>{{$product['selling_price']}}</td>
                            <td>
                                <x-ui-button label="Edit" icon="o-pencil-square" responsive class="btn-warning" spinner wire:click="openEditDrawer({{$product['id']}})"/>
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
        <livewire:pagination :data="$products"/>
    </div>
</div>
