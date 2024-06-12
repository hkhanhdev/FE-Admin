<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

new #[Layout("layouts.app")]
#[Title("Orders Management")]
class extends Component {
    //
    use Toast;
    protected $access_token;
    protected $ords_endpoint = "http://127.0.0.1:8000/api/v1/admin/get_all_orders?orderBy=DESC&perPage=2";
    protected $upt_endpoint = "http://127.0.0.1:8000/api/v1/admin/update_status_order";

    public $select_ordID;
    public $select_status;
    public $editDrawer = false;
    public $status = [
        [
            'id' => 1,
            'name' => 'Pending'
        ],
        [
            'id' => 2,
            'name' => 'Delivering'
        ],
        [
            'id' => 3,
            'name' => 'Delivered'
        ],
        [
            'id' => 4,
            'name' => 'Completed'
        ],
        [
            'id' => 5,
            'name' => 'Canceled'
        ]
    ];

    #[On("toNextPage")]
    public function nextPage($endpoint)
    {
        $this->ords_endpoint = $endpoint.'&perPage=2';
//        dd($this->ords_endpoint);
    }

    #[On("toPrevPage")]
    public function prevPage($endpoint)
    {
        $this->ords_endpoint = $endpoint.'&perPage=2';
    }

    public function updateOrd()
    {
        $res = \Illuminate\Support\Facades\Http::post($this->upt_endpoint,['token'=>session()->get("access_token"),'id'=>$this->select_ordID,'status'=>$this->select_status])->json();
        if ($res['status_code'] == 200) {
            $this->success("Update status successfully!");
            $this->editDrawer = false;
        }else {
            $this->error("Please try again!");
            $this->editDrawer = false;
        }
    }

    public function openEditDrawer($id,$status)
    {
        $this->select_ordID = $id;
        if ($status == "Pending") {
            $this->select_status = 1;
        }elseif ($status == "Delivering") {
            $this->select_status = 2;
        }elseif ($status == "Delivered") {
            $this->select_status = 3;
        }elseif ($status == "Success") {
            $this->select_status = 4;
        }else {
            $this->select_status = 5;
        }
        $this->editDrawer = true;
    }
    protected function getOrders()
    {
        $this->access_token = session()->get("access_token");
        $res = \Illuminate\Support\Facades\Http::get($this->ords_endpoint,['token'=>$this->access_token])->json();
        return $res['data'];
    }
    public function with():array
    {
        $data = $this->getOrders();
//        dd($data);
        return [
            'orders' => $data
        ];
    }
}; ?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Orders Management - Stay on top of your orders from start to finish.') }}
    </h2>
</x-slot>

<div class="py-12">
    <x-ui-drawer
        wire:model="editDrawer"
        title="Edit panel"
        subtitle="Update your order information"
        separator
        with-close-button
        class="w-11/12 lg:w-1/3"
    >
        <div class="flex flex-col gap-2">
            <label class="input input-bordered flex items-center gap-2">
                ID
                <input type="text" class="grow" disabled wire:model="select_ordID"/>
            </label>

            <x-ui-select label="Order status" :options="$status" wire:model="select_status" placeholder="Update order status"/>

        </div>

        <x-slot:actions>
            <x-ui-button label="Cancel" @click="$wire.editDrawer = false" />
            <x-ui-button label="Update" class="btn-primary" icon="o-check" wire:click="updateOrd()" spinner/>
        </x-slot:actions>
    </x-ui-drawer>
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-y-hidden">
                <table class="table table-md">
                    <thead class="font-bold text-lg">
                    <tr>
                        <th>Order ID</th>
                        <th>Product image</th>
                        <th>Product name</th>
                        <th>Price</th>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>
{{--                            <x-ui-button label="Add" icon="o-plus" responsive class="btn-info" spinner wire:click="openAddDrawer()"/>--}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders['pagination_data'] as $order)
                        <tr>
                            <th>{{$order['id']}}</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$order['user_name']}}</td>
                            <td>{{$order['address']}}</td>
                            <td>{{$order['phone_number']}}</td>
                            <td>{{$order['total_bill']}}</td>
                            <td>
                                @if($order['status_order'] == "Pending")
                                    <x-ui-badge value="Pending" class="badge-warning" />
                                @elseif($order['status_order'] == "Canceled")
                                    <x-ui-badge value="Canceled" class="badge-error" />
                                @elseif($order['status_order'] == "Delivering")
                                    <x-ui-badge value="Delivering" class="badge-success" />
                                @elseif($order['status_order'] == "Delivered")
                                    <x-ui-badge value="Delivered" class="badge-success" />
                                @elseif($order['status_order'] == "Success")
                                    <x-ui-badge value="Done" class="badge-success" />
                                @else
                                    <x-ui-badge value="User" class="badge-primary" />
                                @endif
                            </td>
                            <td>
                                <x-ui-button label="Edit" icon="o-pencil-square" responsive class="btn-warning" spinner wire:click="openEditDrawer({{$order['id']}},'{{$order['status_order']}}')"/>
                            </td>
                        </tr>
                        @foreach(json_decode($order['products_cart'],true) as $item)
                            <tr>
                                <td></td>
                                <td>
                                    <img src="{{$item['image']}}" alt="Logo" class="h-20">
                                </td>
                                <td>{{$item['name']}}</td>
                                <td>{{$item['price']}}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td>Empty</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <livewire:pagination :data="$orders"/>
    </div>
</div>
