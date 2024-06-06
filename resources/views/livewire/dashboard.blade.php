<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout("layouts.app")]
class extends Component {
    //
    protected $db_endpoint = 'http://127.0.0.1:8000/api/v1/admin/dash_board';
    protected $rev_endpoint = "http://127.0.0.1:8000/api/v1/admin/get_all_revenue?month=";
    protected $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3YxL2xvZ2luIiwiaWF0IjoxNzE3MzE2NTI4LCJleHAiOjE3MTkxMTY1MjgsIm5iZiI6MTcxNzMxNjUyOCwianRpIjoibnJSajJSMHNLVHpIV0VHVSIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0._o7chwAuJfqqD2P7bFc9ZomefJqqfjirGHaOTimOf2g";
    public array $myChart = [
        'type' => 'bar',
        'data' => [
            'labels' => ['Jan', 'Feb', 'Mar','Apr','May','June','July','Aug','Sep','Oct','Nov','Dec'],
            'datasets' => [
                [
                    'label' => 'Revenue by month',
                    'data' => [],
                ]
            ]
        ]
    ];

    protected function dashboard()
    {
        $res = \Illuminate\Support\Facades\Http::get($this->db_endpoint,['token'=>$this->token])->json();
//        dd($res);
        return $res['data'];
    }
    protected function makeChart()
    {
        $i = 1;
        while($i <= 12) {
            $res = \Illuminate\Support\Facades\Http::get($this->rev_endpoint.$i,['token'=>$this->token])->json();
            if (empty($res['data'])) {
                $rand_rev = fake()->randomNumber(4);
                $this->myChart['data']['datasets'][0]['data'][] = $rand_rev;
            }else {
                $this->myChart['data']['datasets'][0]['data'][] = $res['data'];
            }
            $i++;
        }
    }
    public function with(): array
    {
        $data = $this->dashboard();
        $this->makeChart();
        return [
            "users" => $data['users'],
            "revenue" => "1.000.000",
            "orders" => $data['orders'],
            'products' => $data['products']
        ];
    }
}; ?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden">
            <div class="flex gap-4">
                <x-ui-stat
                    title="Revenue"
                    description="This month"
                    value="{{$revenue}}"
                    icon="o-currency-dollar"
                    />

                <x-ui-stat
                    title="Users"
                    description="This month"
                    value="{{$users}}"
                    icon="o-user-group"
                />

                <x-ui-stat
                    title="Products"
                    description="This month"
                    value="{{$products}}"
                    icon="o-shopping-bag"
                />

                <x-ui-stat
                    title="Orders"
                    description="This month"
                    value="{{$orders}}"
                    icon="o-truck"
                />
            </div>
            <x-ui-chart wire:model="myChart" class="mt-5"/>

        </div>
    </div>
</div>
