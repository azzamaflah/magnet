<?php
// app/View/Components/MainLayout.php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MainLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        // Ubah ini agar menunjuk ke file layout baru kita
        return view('layouts.main'); 
    }
}