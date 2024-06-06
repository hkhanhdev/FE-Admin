<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Pagination extends Component
{
    public $pagination_data = [];
    public $current_page;
    public $last_page;

    public function mount($data)
    {
        $this->pagination_data = $data;
//        dd($this->pagination_data);
        $this->current_page = $this->pagination_data['current_page'];
        $this->last_page = $this->pagination_data['last_page'];
    }

    #[On("pagi")]
    public function pagi($data)
    {
        $this->pagination_data = $data;
        $this->current_page = $data['current_page'];
    }

    public function nextPage()
    {
        $this->dispatch("toNextPage",endpoint:$this->pagination_data['next_page_url']);
    }
    public function prevPage()
    {
        $this->dispatch("toPrevPage",endpoint:$this->pagination_data['prev_page_url']);
    }

    public function render()
    {
        return view('livewire.pagination');
    }
}
