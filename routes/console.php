<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Business;

Artisan::command('backfill:thumbs {--id=} {--limit=0} {--force} {--dry}', function () {
    $this->info('Backfill de imagens iniciado...');

    // Config
    $disk        = 'public';
    $placeholder = 'placeholders/no-image.png';
    $limit       = (int) $this->option('limit');
    $onlyId      = $this->option('id');
    $force       = (bool) $this->option('force');
    $dry         = (bool) $this->option('dry');

    $isVerbose     = $this->output->isVerbose();      // -v
    $isVeryVerbose = $this->output->isVeryVerbose();  // -vv
    $isDebug       = $this->output->isDebug();        // -vvv

    if (!Storage::disk($disk)->exists($placeholder)) {
        $this->error("Placeholder ausente em '{$placeholder}' no disco '{$disk}'.");
        return 1;
    }

    // Descobre a melhor origem possível para um registro legado
    $pickSourcePath = function (Business $b) use ($disk) {
        if (empty($b->image)) {
            return null;
        }
        $candidates = [
            $b->image,
            "businesses/{$b->image}",
            "businesses/large/{$b->image}",
            "businesses/thumbs/{$b->image}",
        ];
        foreach ($candidates as $p) {
            if (Storage::disk($disk)->exists($p)) {
                return $p;
            }
        }
        return null;
    };

    // Query base
    $q = Business::query()->orderBy('biz_id');

    if ($onlyId) {
        $q->where('biz_id', (int) $onlyId);
    } elseif (!$force) {
        $q->where(function ($qq) {
            $qq->whereNull('image_sm')->orWhereNull('image_lg');
        });
    }

    $remaining = $limit > 0 ? $limit : PHP_INT_MAX;

    $updated = 0;
    $skipped = 0;
    $failed  = 0;

    $q->chunkById(100, function ($rows) use (
        $disk, $placeholder, $pickSourcePath, $force, $dry,
        $isVerbose, $isVeryVerbose, $isDebug,
        &$updated, &$skipped, &$failed, &$remaining
    ) {
        foreach ($rows as $b) {
            if ($remaining <= 0) {
                return false; // parar o chunking
            }

            if (!$force && !empty($b->image_sm) && !empty($b->image_lg)) {
                $skipped++;
                if ($isVerbose) {
                    $this->line("SKIP biz_id={$b->biz_id} (já possui sm & lg)");
                }
                $remaining--;
                continue;
            }

            // Origem: caminho válido no disco ou placeholder
            $src = $pickSourcePath($b) ?: $placeholder;

            // Caminho absoluto no disco (usa API do Storage p/ evitar problemas de path no Windows)
            $full = Storage::disk($disk)->path($src);
            if (!is_file($full)) {
                $full = Storage::disk($disk)->path($placeholder);
            }

            $dir = "businesses/{$b->biz_id}";
            if (!Storage::disk($disk)->exists($dir)) {
                Storage::disk($disk)->makeDirectory($dir);
            }

            try {
                // Tenta decodificar a imagem; se falhar, usa um canvas cinza como fallback
                try {
                    $img = Image::make($full)->orientate();
                } catch (\Throwable $decodeErr) {
                    // canvas ~800x600 (cinza claro) para garantir continuidade
                    $img = Image::canvas(800, 600, '#e5e7eb');
                    if ($isVeryVerbose) {
                        $this->line("Fallback canvas para biz_id={$b->biz_id}: ".$decodeErr->getMessage());
                    }
                }

                // Gera variações
                $orig = (clone $img)->encode('webp', 90);
                $lg   = (clone $img)
                    ->resize(800, 600, function ($c) { $c->aspectRatio(); $c->upsize(); })
                    ->encode('webp', 85);
                $sm   = (clone $img)
                    ->resize(360, 240, function ($c) { $c->aspectRatio(); $c->upsize(); })
                    ->encode('webp', 80);

                if ($dry) {
                    $this->line("DRY biz_id={$b->biz_id} → {$dir}/(orig|lg|sm).webp");
                } else {
                    Storage::disk($disk)->put("{$dir}/orig.webp", $orig);
                    Storage::disk($disk)->put("{$dir}/lg.webp",   $lg);
                    Storage::disk($disk)->put("{$dir}/sm.webp",   $sm);

                    $b->image    = "{$dir}/orig.webp";
                    $b->image_lg = "{$dir}/lg.webp";
                    $b->image_sm = "{$dir}/sm.webp";
                    $b->save();

                    if ($isVerbose) {
                        $this->line("OK biz_id={$b->biz_id}");
                    }
                }

                $updated++;
            } catch (\Throwable $e) {
                $failed++;
                $this->error("FAIL biz_id={$b->biz_id}: ".$e->getMessage());
                if ($isDebug) {
                    $this->line($e->getTraceAsString());
                }
            }

            $remaining--;
        }
        return true; // continuar chunking
    }, 'biz_id');

    $this->newLine();
    $this->info("Concluído. Atualizados: {$updated} | Ignorados: {$skipped} | Falhas: {$failed}");
    if ((int) $this->option('limit') > 0 && $remaining <= 0) {
        $this->line("Obs.: limite (--limit) atingido.");
    }

    return $failed > 0 ? 2 : 0;
});
