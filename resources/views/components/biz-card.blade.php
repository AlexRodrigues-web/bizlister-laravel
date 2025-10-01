@props(["biz"])
@php($slug = \Illuminate\Support\Str::slug($biz->business_name))
<div class="card h-100 shadow-sm">
  <div class="card-body">
    <h5 class="card-title mb-1">
      <a href="{{ route('business.show', [$biz->biz_id, $slug]) }}">{{ $biz->business_name }}</a>
    </h5>
    <p class="text-muted small mb-2">{{ $biz->city ?? "—" }}</p>
    <p class="card-text">{{ \Illuminate\Support\Str::limit($biz->description ?? "", 140) }}</p>
  </div>
  <div class="card-footer bg-white">
    <a class="btn btn-sm btn-primary" href="{{ route('business.show', [$biz->biz_id, $slug]) }}">Ver detalhes</a>
  </div>
</div>