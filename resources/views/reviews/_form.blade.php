@auth
<section aria-labelledby="write-review" class="mt-10">
  <h2 id="write-review" class="text-lg font-semibold text-slate-800 mb-2">Escrever uma avaliação</h2>

  <form method="POST" action="{{ route("reviews.store", ["id" => $business->biz_id]) }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Nota</label>
      <div class="flex items-center gap-3">
        <select name="rating" class="w-28 rounded-lg border border-slate-300 p-2 text-slate-800" required>
          @for($i=5;$i>=1;$i--)
            <option value="{{ $i }}">{{ $i }}</option>
          @endfor
        </select>
        <x-stars :value="(int) old('rating', 5)" size="md" />
      </div>
      @error("rating")<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
      <input type="text" name="title" value="{{ old('title') }}"
             class="w-full rounded-lg border border-slate-300 p-2 text-slate-800 focus:outline-none focus:ring focus:ring-slate-200"
             maxlength="150" placeholder="Resumo curto opcional" />
      @error("title")<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Comentário</label>
      <textarea name="body" rows="4"
                class="w-full rounded-lg border border-slate-300 p-2 text-slate-800 focus:outline-none focus:ring focus:ring-slate-200"
                maxlength="5000" placeholder="Conte mais sobre sua experiência"></textarea>
      @error("body")<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
      <p class="mt-1 text-xs text-slate-500">Sua avaliação ficará visível após aprovação.</p>
    </div>

    <button class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-white hover:bg-slate-800">
      Enviar avaliação
    </button>
  </form>
</section>
@else
  <div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
    <a class="text-slate-900 underline" href="{{ route('login') }}">Entre</a> para avaliar.
  </div>
@endauth