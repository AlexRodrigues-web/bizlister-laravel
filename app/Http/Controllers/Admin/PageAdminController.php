<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PageAdminController extends Controller
{
    public function index(Request $request)
    {
        // Busca simples opcional por título/slug
        $q = Page::query()->orderByDesc('id');

        if ($search = trim((string) $request->get('q', ''))) {
            $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $q->paginate(20)->appends($request->only('q'));
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $page = new Page([
            'is_active'    => 1,
            'published_at' => null,
        ]);

        return view('admin.pages.create', compact('page'));
    }

    public function store(PageRequest $request)
    {
        $data = $this->sanitize($request->validated());

        // Se não vier slug, gera do título
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? '');
        }

        // Publicar agora? (opcional)
        if ($request->boolean('publish_now')) {
            $data['is_active']    = 1;
            $data['published_at'] = now();
        }

        // Garante unicidade do slug
        $data['slug'] = $this->uniqueSlug($data['slug']);

        $page = Page::create($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Página criada!');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(PageRequest $request, Page $page)
    {
        $data = $this->sanitize($request->validated());

        // Se slug vier vazio, regen do título
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? '');
        }

        // Publicar agora? (opcional)
        if ($request->boolean('publish_now')) {
            $data['is_active']    = 1;
            $data['published_at'] = now();
        }

        // Se slug mudou, precisa garantir unicidade
        if (isset($data['slug']) && $data['slug'] !== $page->slug) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $page->id);
        }

        $page->update($data);

        return back()->with('status', 'Página atualizada!');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Página removida.');
    }

    /**
     * Limpa/normaliza payload.
     */
    protected function sanitize(array $data): array
    {
        // Trim em strings
        foreach (['title','slug','content'] as $key) {
            if (array_key_exists($key, $data) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        // Checkbox -> 0/1
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        // published_at: aceita vazio (null) ou string parseável
        if (array_key_exists('published_at', $data)) {
            if ($data['published_at'] === '' || $data['published_at'] === null) {
                $data['published_at'] = null;
            } else {
                try {
                    $ts = \Carbon\Carbon::parse($data['published_at']);
                    $data['published_at'] = $ts;
                } catch (\Throwable $e) {
                    // Se quiser falhar, descomente:
                    // throw ValidationException::withMessages(['published_at' => 'Data/hora inválida.']);
                    // Caso contrário, zera:
                    $data['published_at'] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Gera um slug único. Se já existir, adiciona sufixo -2, -3, ...
     *
     * @param  string   $base
     * @param  int|null $ignoreId  (id a ignorar na checagem, para update)
     */
    protected function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'pagina';

        $exists = function ($candidate) use ($ignoreId) {
            $q = Page::where('slug', $candidate);
            if ($ignoreId) {
                $q->where('id', '<>', $ignoreId);
            }
            return $q->exists();
        };

        if (!$exists($slug)) {
            return $slug;
        }

        // Tenta com sufixos
        $i = 2;
        while (true) {
            $candidate = "{$slug}-{$i}";
            if (!$exists($candidate)) {
                return $candidate;
            }
            $i++;
            if ($i > 200) {
                // Segurança: evita loop infinito
                throw ValidationException::withMessages([
                    'slug' => 'Não foi possível gerar um slug único. Tente outro título/slug.',
                ]);
            }
        }
    }
}
