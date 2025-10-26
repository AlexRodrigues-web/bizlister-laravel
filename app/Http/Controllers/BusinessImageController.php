<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BusinessImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // pode reforçar com Gates/Policies se desejar
    }

    /**
     * Lista as imagens (JSON simples — útil para um gerenciador via AJAX).
     */
    public function index(Business $business)
    {
        $images = $business->images()->get()->map(function ($img) {
            return [
                'id'         => $img->id,
                'url'        => Storage::disk('public')->url($img->path),
                'title'      => $img->title,
                'sort_order' => $img->sort_order,
                'is_primary' => (bool)$img->is_primary,
            ];
        });

        return response()->json(['ok' => true, 'images' => $images]);
    }

    /**
     * Upload de uma imagem.
     */
    public function store(Request $request, Business $business)
    {
        $data = $request->validate([
            'image' => ['required','file','image','mimes:jpeg,jpg,png,webp','max:3072'], // 3MB
            'title' => ['nullable','string','max:255'],
        ]);

        $dir = 'galleries/'.$business->biz_id;
        $ext = $request->file('image')->getClientOriginalExtension() ?: 'jpg';
        $name = Str::uuid()->toString().'.'.$ext;

        $path = $request->file('image')->storeAs($dir, $name, 'public');

        $img = BusinessImage::create([
            'business_id' => $business->biz_id,
            'path'        => $path,
            'title'       => $data['title'] ?? null,
            'sort_order'  => 0,
            'is_primary'  => false,
        ]);

        return back()->with('status', 'Imagem enviada com sucesso!');
        // Ou: return response()->json(['ok'=>true,'id'=>$img->id,'url'=>Storage::disk('public')->url($img->path)]);
    }

    /**
     * Define/Remove capa.
     */
    public function primary(Request $request, Business $business, BusinessImage $image)
    {
        abort_unless($image->business_id === (int)$business->biz_id, 404);

        $makePrimary = (bool)$request->boolean('is_primary');

        if ($makePrimary) {
            BusinessImage::where('business_id', $business->biz_id)->update(['is_primary' => false]);
        }

        $image->update(['is_primary' => $makePrimary]);

        return back()->with('status', 'Atualizado!');
    }

    /**
     * Reordenar (recebe array de ids na ordem desejada).
     */
    public function reorder(Request $request, Business $business)
    {
        $ids = $request->input('ids', []); // [3,10,5,...]
        if (is_array($ids)) {
            foreach ($ids as $i => $id) {
                BusinessImage::where('id', (int)$id)
                    ->where('business_id', $business->biz_id)
                    ->update(['sort_order' => $i]);
            }
        }
        return back()->with('status', 'Ordem atualizada!');
    }

    /**
     * Remover.
     */
    public function destroy(Business $business, BusinessImage $image)
    {
        abort_unless($image->business_id === (int)$business->biz_id, 404);

        // apaga do disco
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return back()->with('status', 'Imagem removida!');
    }
}
