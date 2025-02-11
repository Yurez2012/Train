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
        return view('livewire.train-form');
    }
}
