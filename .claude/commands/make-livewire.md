Create a new Livewire component. The argument should be the component class path.

```bash
php artisan make:livewire $ARGUMENTS
```

Examples:
- `/make-livewire Frontend/HeroSlider` → creates app/Livewire/Frontend/HeroSlider.php + view
- `/make-livewire Admin/Products/ProductForm` → creates app/Livewire/Admin/Products/ProductForm.php + view

After creating, remind the user to:
1. Register the component in routes/web.php or routes/admin.php
2. Add the correct Tailwind classes following the admin UI conventions (bg-gray-50 page, bg-white cards, shadow-sm rounded-xl)
3. For admin components, ensure the layout extends `layouts.admin`
4. For frontend components, ensure the layout extends `layouts.app`
