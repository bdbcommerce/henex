<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generates the `slug` automatically — admins never type it.
 *
 * Models define:
 *   protected function slugSource(): string     text the slug is made from (SKU, name, title…)
 *   protected array $slugSourceFields = [...];  columns that trigger regeneration when changed
 *   protected bool  $slugFollowsSource = true;  public URLs: regenerate on change + 301 from the old slug
 *                                               false: slug is fixed after creation (internal references)
 */
trait HasAutoSlug
{
    protected static function bootHasAutoSlug(): void
    {
        static::saving(function (Model $model) {
            $sourceChanged = $model->exists && $model->slugFollowsSource()
                && collect($model->slugSourceFields())->contains(fn ($f) => $model->isDirty($f));

            if (blank($model->slug) || $sourceChanged) {
                $model->slug = $model->uniqueSlug($model->makeSlugBase());
            }
        });

        static::saved(function (Model $model) {
            if (! $model->slugFollowsSource() || ! $model->wasChanged('slug')) {
                return;
            }
            $old = $model->getOriginal('slug');
            $table = DB::table('slug_redirects');

            // The current slug must never be shadowed by a redirect.
            $table->where('model_type', $model->getMorphClass())->where('old_slug', $model->slug)->delete();

            if (filled($old) && $old !== $model->slug) {
                $table->updateOrInsert(
                    ['model_type' => $model->getMorphClass(), 'old_slug' => $old],
                    ['model_id' => $model->getKey(), 'updated_at' => now(), 'created_at' => now()],
                );
            }
        });

        static::deleted(function (Model $model) {
            DB::table('slug_redirects')->where('model_type', $model->getMorphClass())->where('model_id', $model->getKey())->delete();
        });
    }

    /** Route-model binding: unknown slug → 301 to the current URL if it is an old slug, else 404. */
    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->where($field, $value)->first() ?? $this->redirectFromOldSlug($field === 'slug' ? $value : null);
    }

    /** For routes that take a plain slug string (e.g. /category/{slug}). */
    public static function findBySlugOrRedirect(string $slug): static
    {
        $model = new static;

        return static::where('slug', $slug)->first() ?? $model->redirectFromOldSlug($slug);
    }

    protected function redirectFromOldSlug(?string $slug): never
    {
        $id = $slug ? DB::table('slug_redirects')->where('model_type', $this->getMorphClass())->where('old_slug', $slug)->value('model_id') : null;
        $current = $id ? static::whereKey($id)->value('slug') : null;

        if ($current) {
            $url = preg_replace('#/' . preg_quote(rawurlencode($slug), '#') . '(?=$|[/?])#', '/' . $current, request()->fullUrl(), 1);
            throw new HttpResponseException(redirect($url, 301));
        }

        abort(404);
    }

    protected function makeSlugBase(): string
    {
        // Russian transliteration rules (ш→sh, ж→zh, ч→ch); Latin text (en/uz) passes through unchanged.
        $base = Str::slug((string) $this->slugSource(), '-', 'ru');

        return Str::limit($base, 150, '') ?: Str::lower(class_basename($this)) . '-' . Str::lower(Str::random(6));
    }

    /** base, base-2, base-3 … unique within the table (and not an old slug of another record). */
    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        for ($i = 2; $this->slugTaken($slug); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    protected function slugTaken(string $slug): bool
    {
        $taken = static::where('slug', $slug)->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))->exists();

        return $taken || ($this->slugFollowsSource() && DB::table('slug_redirects')
            ->where('model_type', $this->getMorphClass())->where('old_slug', $slug)
            ->where('model_id', '!=', $this->getKey() ?? 0)->exists());
    }

    protected function slugSourceFields(): array
    {
        return property_exists($this, 'slugSourceFields') ? $this->slugSourceFields : ['name'];
    }

    protected function slugFollowsSource(): bool
    {
        return property_exists($this, 'slugFollowsSource') ? $this->slugFollowsSource : false;
    }

    /** First non-empty translation, preferring Latin-script locales for readable URLs. */
    protected function firstTranslation(string $field, array $locales = ['en', 'uz', 'ru']): string
    {
        $t = $this->getTranslations($field);
        foreach ($locales as $l) {
            if (filled($t[$l] ?? null)) {
                return $t[$l];
            }
        }

        return '';
    }
}
