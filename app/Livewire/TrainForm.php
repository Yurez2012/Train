<?php

namespace App\Livewire;

use Livewire\Component;

class TrainForm extends Component
{
    public $trains = [
        ['station' => '', 'date' => '', 'time' => '', 'trainID' => '']
    ];

    public function addTrain()
    {
        $this->trains[] = ['station' => '', 'date' => '', 'time' => '', 'trainID' => ''];
    }

    public function render()
    {
        if(request()->has('trains')) {
            $this->trains = request()->get('trains', []);
        }

        return view('livewire.train-form');
    }
}
