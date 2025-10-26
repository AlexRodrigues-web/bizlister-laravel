<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementAdminController extends Controller
{
    public function edit()
    {
        // tabela legado costuma ter 1 linha
        $ad = Advertisement::query()->first() ?: Advertisement::create(['id' => 1, 'ad1' => '', 'ad2' => '', 'ad3' => '']);
        return view('admin.advertisements.edit', compact('ad'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ad1' => ['nullable','string'],
            'ad2' => ['nullable','string'],
            'ad3' => ['nullable','string'],
        ]);

        $ad = Advertisement::query()->firstOrCreate(['id' => 1], ['ad1' => '', 'ad2' => '', 'ad3' => '']);
        $ad->fill($data)->save();

        return redirect()->route('admin.advertisements.edit')->with('status', 'Anúncios salvos!');
    }
}
