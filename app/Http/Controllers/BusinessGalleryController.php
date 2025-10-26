<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessImage; // já existe no seu projeto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BusinessGalleryController extends Controller
{
    /**
     * Lista a galeria de um negócio + formulário de upload.
     */
    public function index(int $biz)
    {
        $business = Business::findOrFail($biz);

        // Carrega imagens só se a tabela existir
        $images = collect();
        if (Schema::hasTable('business_images')) {
            $images = BusinessImage::query()
                ->where('business_id', $business->biz_id)
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        // Use uma view simples (veja exemplo no final) ou reaproveite a tela de show
        return view('business.gallery', compact('business', 'images'));
    }

    /**
     * Recebe uploads (múltiplos) e salva em storage público.
     * Se a tabela business_images existir, persiste o registro também.
     */
    public function store(Request $request, int $biz)
    {
        $business = Business::findOrFail($biz);

        $request->validate([
            'images'   => ['required', 'array', 'max:12'],
            'images.*' => ['file', 'image', 'max:4096'], // 4MB cada
        ]);

        $disk = 'public';
        $basePath = "businesses/{$business->biz_id}/gallery";

        $created = 0;
        foreach ($request->file('images', []) as $upload) {
            $path = $upload->store($basePath, $disk); // ex.: storage/app/public/businesses/{biz}/gallery/xxx.jpg
            $created++;

            if (Schema::hasTable('business_images')) {
                $img = new BusinessImage();
                $img->business_id = $business->biz_id;
                $img->path        = $path;              // seu model deve ter 'path' (ou ajuste para 'image'/'url')
                $img->is_primary  = 0;
                $img->sort_order  = 0;
                $img->save();

                // Se não houver nenhuma primária, marca a primeira enviada
                if (!BusinessImage::where('business_id', $business->biz_id)->where('is_primary', 1)->exists()) {
                    $img->is_primary = 1;
                    $img->save();
                }
            }
        }

        return back()->with('status', "Upload concluído: {$created} arquivo(s).");
    }

    /**
     * Define uma imagem como principal (se tabela existir).
     */
    public function setPrimary(int $biz, int $image)
    {
        if (!Schema::hasTable('business_images')) {
            abort(404);
        }

        $business = Business::findOrFail($biz);
        $img = BusinessImage::where('business_id', $business->biz_id)->findOrFail($image);

        // zera todas e ativa a escolhida
        BusinessImage::where('business_id', $business->biz_id)->update(['is_primary' => 0]);
        $img->is_primary = 1;
        $img->save();

        return back()->with('status', 'Imagem definida como principal.');
    }

    /**
     * Reordena imagens (payload: order[id] = sort_order).
     */
    public function reorder(Request $request, int $biz)
    {
        if (!Schema::hasTable('business_images')) {
            abort(404);
        }

        $business = Business::findOrFail($biz);
        $data = $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($data['order'] as $id => $sort) {
            BusinessImage::where('business_id', $business->biz_id)
                ->where('id', $id)
                ->update(['sort_order' => (int) $sort]);
        }

        return back()->with('status', 'Ordem atualizada.');
    }

    /**
     * Exclui uma imagem (apaga arquivo do storage e o registro, se existir).
     */
    public function destroy(int $biz, int $image)
    {
        $business = Business::findOrFail($biz);

        if (Schema::hasTable('business_images')) {
            $img = BusinessImage::where('business_id', $business->biz_id)->findOrFail($image);

            if ($img->path && Storage::disk('public')->exists($img->path)) {
                Storage::disk('public')->delete($img->path);
            }

            $img->delete();
            return back()->with('status', 'Imagem removida.');
        }

        // Sem tabela, não sabemos qual arquivo apagar com segurança.
        abort(404, 'Recurso indisponível sem a tabela business_images.');
    }
}
