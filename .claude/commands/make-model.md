Create a new Eloquent model with migration. The argument is the model name.

```bash
php artisan make:model $ARGUMENTS -m
```

After creating, remind the user to:
1. Add `use HasTranslations;` and `public array $translatable = [...]` if the model has translatable fields
2. Add `use HasMedia; implements HasMedia` and `registerMediaCollections()` if the model needs images
3. Add `use HasRoles;` if it's the User model
4. Define relationships according to the schema in CLAUDE.md
