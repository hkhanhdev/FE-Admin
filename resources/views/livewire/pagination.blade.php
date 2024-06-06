<div class="flex justify-center w-full">
    <div class="join grid grid-cols-3 mt-5">
        @if ($pagination_data['prev_page_url'])
            <x-ui-button icon="m-arrow-small-left" class="join-item btn btn-outline" wire:click="prevPage('{{$pagination_data['prev_page_url']}}')" spinner></x-ui-button>
        @else
            <x-ui-button icon="m-arrow-small-left" class="join-item btn btn-outline btn-disabled"></x-ui-button>
        @endif
        <button class="join-item btn btn-outline">Page {{$current_page}}/{{$last_page}}</button>
        @if ($pagination_data['next_page_url'])
            <x-ui-button icon="m-arrow-small-right" class="join-item btn btn-outline" wire:click="nextPage('{{$pagination_data['next_page_url']}}')" spinner></x-ui-button>
        @else
            <x-ui-button icon="m-arrow-small-right" class="join-item btn btn-outline btn-disabled"></x-ui-button>
        @endif
    </div>
</div>
